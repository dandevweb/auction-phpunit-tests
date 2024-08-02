<?php

declare(strict_types=1);

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use PHPUnit\Framework\TestCase;
use Alura\Leilao\Service\Avaliador;
use PHPUnit\Framework\Attributes\DataProvider;

class AvaliadorTest extends TestCase
{
    private $leiloeiro;
    
    protected function setUp(): void
    {
        $this->leiloeiro = new Avaliador();
    }
    
    #[DataProvider('leilaoEmOrdemCrescente')]
    #[DataProvider('leilaoEmOrdemDecrescente')]
    #[DataProvider('leilaoEmOrdemAleatoria')]
    public function testAvaliadorDeveEncontrarOMaiorValorDeLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maiorValor = $this->leiloeiro->getMaiorValor();

        $valorEsperado = 2500;

        self::assertEquals($valorEsperado, $maiorValor);
    }

    #[DataProvider('leilaoEmOrdemCrescente')]
    #[DataProvider('leilaoEmOrdemDecrescente')]
    #[DataProvider('leilaoEmOrdemAleatoria')]
    public function testAvaliadorDeveEncontrarOMenorValorDeLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $menorValor = $this->leiloeiro->getMenorValor();

        $valorEsperado = 500;

        self::assertEquals($valorEsperado, $menorValor);
    }

    #[DataProvider('leilaoEmOrdemCrescente')]
    #[DataProvider('leilaoEmOrdemDecrescente')]
    #[DataProvider('leilaoEmOrdemAleatoria')]
    public function testAvaliadorDeveBuscarOsTresMaioresLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maioresLances = $this->leiloeiro->getMaioresLances();

        self::assertCount(3, $maioresLances);
        self::assertEquals(2500, $maioresLances[0]->getValor());
        self::assertEquals(2000, $maioresLances[1]->getValor());
        self::assertEquals(1700, $maioresLances[2]->getValor());
    }

    public static function leilaoEmOrdemCrescente(): array
    {
        $leilao = new Leilao('Fiat 147 0km');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $nara = new Usuario('Nara');
        $flora = new Usuario('Flora');

        $leilao->recebeLance(new Lance($flora, 500));
        $leilao->recebeLance(new Lance($nara, 1700));
        $leilao->recebeLance(new Lance($maria, 2000));
        $leilao->recebeLance(new Lance($joao, 2500));

        return [
            'ordem-crescente' => [$leilao]
        ];
    }

    public static function leilaoEmOrdemDecrescente(): array
    {
        $leilao = new Leilao('Fiat 147 0km');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $nara = new Usuario('Nara');
        $flora = new Usuario('Flora');

        $leilao->recebeLance(new Lance($joao, 2500));
        $leilao->recebeLance(new Lance($maria, 2000));
        $leilao->recebeLance(new Lance($nara, 1700));
        $leilao->recebeLance(new Lance($flora, 500));

        return [
           'ordem-decrescente' => [$leilao]
        ];
    }

    public static function leilaoEmOrdemAleatoria(): array
    {
        $leilao = new Leilao('Fiat 147 0km');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $nara = new Usuario('Nara');
        $flora = new Usuario('Flora');

        $leilao->recebeLance(new Lance($nara, 1700));
        $leilao->recebeLance(new Lance($flora, 500));
        $leilao->recebeLance(new Lance($joao, 2500));
        $leilao->recebeLance(new Lance($maria, 2000));

        return [
            'orderm-aleatoria' => [$leilao]
        ];
    }
}