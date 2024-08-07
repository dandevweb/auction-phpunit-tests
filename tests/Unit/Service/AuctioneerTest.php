<?php

declare(strict_types=1);

namespace Dandevweb\Auction\Tests\Service;

use Dandevweb\Auction\Model\Offer;
use Dandevweb\Auction\Model\Auction;
use Dandevweb\Auction\Model\User;
use PHPUnit\Framework\TestCase;
use Dandevweb\Auction\Service\Auctioneer;
use PHPUnit\Framework\Attributes\DataProvider;

class AuctioneerTest extends TestCase
{
    private Auctioneer $auctioneer;
    
    protected function setUp(): void
    {
        $this->auctioneer = new Auctioneer();
    }
    
    #[DataProvider('leilaoEmOrdemCrescente')]
    #[DataProvider('leilaoEmOrdemDecrescente')]
    #[DataProvider('leilaoEmOrdemAleatoria')]
    public function testAvaliadorDeveEncontrarOMaiorValorDeLances(Auction $auction)
    {
        $this->auctioneer->evaluate($auction);

        $maiorValor = $this->auctioneer->getHighestOffer();

        $valorEsperado = 2500;

        self::assertEquals($valorEsperado, $maiorValor);
    }

    #[DataProvider('leilaoEmOrdemCrescente')]
    #[DataProvider('leilaoEmOrdemDecrescente')]
    #[DataProvider('leilaoEmOrdemAleatoria')]
    public function testAvaliadorDeveEncontrarOMenorValorDeLances(Auction $auction)
    {
        $this->auctioneer->evaluate($auction);

        $menorValor = $this->auctioneer->getLowestValue();

        $valorEsperado = 500;

        self::assertEquals($valorEsperado, $menorValor);
    }

    #[DataProvider('leilaoEmOrdemCrescente')]
    #[DataProvider('leilaoEmOrdemDecrescente')]
    #[DataProvider('leilaoEmOrdemAleatoria')]
    public function testAvaliadorDeveBuscarOsTresMaioresLances(Auction $auction)
    {
        $this->auctioneer->evaluate($auction);

        $maioresLances = $this->auctioneer->getHighestOffers();

        self::assertCount(3, $maioresLances);
        self::assertEquals(2500, $maioresLances[0]->getValue());
        self::assertEquals(2000, $maioresLances[1]->getValue());
        self::assertEquals(1700, $maioresLances[2]->getValue());
    }

    public function testLeilaoVazioNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Não é possível avaliar um leilão vazio');
        
        $auction = new Auction('Fusca Azul');
        $this->auctioneer->evaluate($auction);
    }

    public function testLeilaoFinalizadoNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Leilão já finalizado');
        
        $auction = new Auction('Fusca Azul');
        $auction->finish();
        $this->auctioneer->evaluate($auction);
    }

    public static function leilaoEmOrdemCrescente(): array
    {
        $auction = new Auction('Fiat 147 0km');

        $maria = new User('Maria');
        $joao = new User('João');
        $nara = new User('Nara');
        $flora = new User('Flora');

        $auction->processOffer(new Offer($flora, 500));
        $auction->processOffer(new Offer($nara, 1700));
        $auction->processOffer(new Offer($maria, 2000));
        $auction->processOffer(new Offer($joao, 2500));

        return [
            'ordem-crescente' => [$auction]
        ];
    }

    public static function leilaoEmOrdemDecrescente(): array
    {
        $auction = new Auction('Fiat 147 0km');

        $maria = new User('Maria');
        $joao = new User('João');
        $nara = new User('Nara');
        $flora = new User('Flora');

        $auction->processOffer(new Offer($joao, 2500));
        $auction->processOffer(new Offer($maria, 2000));
        $auction->processOffer(new Offer($nara, 1700));
        $auction->processOffer(new Offer($flora, 500));

        return [
           'ordem-decrescente' => [$auction]
        ];
    }

    public static function leilaoEmOrdemAleatoria(): array
    {
        $auction = new Auction('Fiat 147 0km');

        $maria = new User('Maria');
        $joao = new User('João');
        $nara = new User('Nara');
        $flora = new User('Flora');

        $auction->processOffer(new Offer($nara, 1700));
        $auction->processOffer(new Offer($flora, 500));
        $auction->processOffer(new Offer($joao, 2500));
        $auction->processOffer(new Offer($maria, 2000));

        return [
            'orderm-aleatoria' => [$auction]
        ];
    }
}
