<?php

declare(strict_types=1);

namespace Alura\Leilao\Test\Model;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class LeilaoTest extends TestCase
{

    #[DataProvider('geraLances')]
    public function testUmLeilaoDeveReceberLances(
        int $qtdLances,
        Leilao $leilao,
        array $valores
    )
    {
        static::assertCount($qtdLances, $leilao->getLances());
        foreach ($valores as $i => $valorEsperado) {
            static::assertEquals($valorEsperado, $leilao->getLances()[$i]->getValor());
        }
    }

    public function testLeilaoNaoDeveReceberLancesRepetidos()
    {
        $leilao = new Leilao('Variante');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana, 1000));
        $leilao->recebeLance(new Lance($ana, 1500));

        static::assertCount(1, $leilao->getLances());
        static::assertEquals(1000, $leilao->getLances()[0]->getValor());
        
    }

    public function testUsuarioNaoPodeDarMaisDeCincoLances()
    {
        $leilao = new Leilao('Variante');
        $ana = new Usuario('Ana');
        $joao = new Usuario('Joao');

        $leilao->recebeLance(new Lance($ana, 1000));
        $leilao->recebeLance(new Lance($joao, 1500));
        $leilao->recebeLance(new Lance($ana, 2000));
        $leilao->recebeLance(new Lance($joao, 2500));
        $leilao->recebeLance(new Lance($ana, 3000));
        $leilao->recebeLance(new Lance($joao, 3500));
        $leilao->recebeLance(new Lance($ana, 4000));
        $leilao->recebeLance(new Lance($joao, 4500));
        $leilao->recebeLance(new Lance($ana, 5000));
        $leilao->recebeLance(new Lance($joao, 5500));
        $leilao->recebeLance(new Lance($ana, 6000));

        static::assertCount(10, $leilao->getLances());
        static::assertEquals(5500, $leilao->getLances()[array_key_last($leilao->getLances())]->getValor());
    }

    public static function geraLances()
    {
        $joao = new Usuario('Joao');
        $maria = new Usuario('Maria');

        $leilaoCom1Lance = new Leilao('Variante');
        $leilaoCom1Lance->recebeLance(new Lance($maria, 5000));

        $leilaoCom2Lance = new Leilao('Fusca');
        $leilaoCom2Lance->recebeLance(new Lance($joao, 1000));
        $leilaoCom2Lance->recebeLance(new Lance($maria, 2000));

        return [
            '2-lances' => [2, $leilaoCom2Lance, [1000, 2000]],
            '1-lance' => [1, $leilaoCom1Lance, [5000]],
        ];
    }
    
}