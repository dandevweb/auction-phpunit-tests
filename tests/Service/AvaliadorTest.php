<?php

declare(strict_types=1);

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use PHPUnit\Framework\TestCase;
use Alura\Leilao\Service\Avaliador;

class AvaliadorTest extends TestCase
{
    public function testAvaliadorDeveEncontrarOMaiorValorDeLanceEmOrdemCrescente()
    {
        $leilao = new Leilao('Fiat 147 0km');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');

        $leilao->recebeLance(new Lance($maria, 2000));
        $leilao->recebeLance(new Lance($joao, 2500));

        $leiloeiro = new Avaliador();
        $leiloeiro->avalia($leilao);

        $maiorValor = $leiloeiro->getMaiorValor();

        $valorEsperado = 2500;

        self::assertEquals($valorEsperado, $maiorValor);
    }
    public function testAvaliadorDeveEncontrarOMaiorValorDeLanceEmOrdemDecrescente()
    {
        $leilao = new Leilao('Fiat 147 0km');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');

        $leilao->recebeLance(new Lance($joao, 2500));
        $leilao->recebeLance(new Lance($maria, 2000));

        $leiloeiro = new Avaliador();
        $leiloeiro->avalia($leilao);

        $maiorValor = $leiloeiro->getMaiorValor();

        $valorEsperado = 2500;

        self::assertEquals($valorEsperado, $maiorValor);
    }

    public function testAvaliadorDeveEncontrarOMenorValorDeLanceEmOrdemDecrescente()
    {
        $leilao = new Leilao('Fiat 147 0km');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');

        $leilao->recebeLance(new Lance($joao, 2500));
        $leilao->recebeLance(new Lance($maria, 2000));

        $leiloeiro = new Avaliador();
        $leiloeiro->avalia($leilao);

        $menorValor = $leiloeiro->getMenorValor();

        $valorEsperado = 2000;

        self::assertEquals($valorEsperado, $menorValor);
    }

    public function testAvaliadorDeveEncontrarOMenorValorDeLanceEmOrdemCrescente()
    {
        $leilao = new Leilao('Fiat 147 0km');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');

        $leilao->recebeLance(new Lance($maria, 2000));
        $leilao->recebeLance(new Lance($joao, 2500));

        $leiloeiro = new Avaliador();
        $leiloeiro->avalia($leilao);

        $menorValor = $leiloeiro->getMenorValor();

        $valorEsperado = 2000;

        self::assertEquals($valorEsperado, $menorValor);
    }

    public function testAvaliadorDeveBuscarOsTresMaioresLances()
    {
        $leilao = new Leilao('Fiat 147 0km');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');
        $jose = new Usuario('José');

        $leilao->recebeLance(new Lance($maria, 2000));
        $leilao->recebeLance(new Lance($joao, 2500));
        $leilao->recebeLance(new Lance($ana, 1700));
        $leilao->recebeLance(new Lance($jose, 2600));

        $leiloeiro = new Avaliador();
        $leiloeiro->avalia($leilao);

        $maioresLances = $leiloeiro->getMaioresLances();

        self::assertCount(3, $maioresLances);
        self::assertEquals(2600, $maioresLances[0]->getValor());
        self::assertEquals(2500, $maioresLances[1]->getValor());
        self::assertEquals(2000, $maioresLances[2]->getValor());
    }
    
}