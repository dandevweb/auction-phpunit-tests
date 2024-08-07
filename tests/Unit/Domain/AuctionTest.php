<?php

namespace Dandevweb\Auction\Tests\Domain;

use Dandevweb\Auction\Model\Offer;
use Dandevweb\Auction\Model\Auction;
use Dandevweb\Auction\Model\User;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AuctionTest extends TestCase
{
    public function testThrowsExceptionWhenBiddingInClosedAuction()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Este leilão já está finalizado');

        $auction = new Auction('Fiat 147 0KM');
        $auction->finish();

        $auction->processOffer(new Offer(new User(''), 1000));
    }

    #[DataProvider('createOfferData')]
    public function testProposeBidsInAuctionShouldWork(int $expectedCount, array $offers)
    {
        $auction = new Auction('Fiat 147 0KM');
        foreach ($offers as $lance) {
            $auction->processOffer($lance);
        }

        static::assertCount($expectedCount, $auction->getOffers());
    }

    public function testSameUserCannotProposeTwoBidsInARow()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Usuário já deu o último lance');
        $user = new User('Ganancioso');

        $auction = new Auction('Objeto inútil');

        $auction->processOffer(new Offer($user, 1000));
        $auction->processOffer(new Offer($user, 1100));
    }

    public static function createOfferData()
    {
        $user1 = new User('Usuário 1');
        $user2 = new User('Usuário 2');
        return [
            [1, [new Offer($user1, 1000)]],
            [2, [new Offer($user1, 1000), new Offer($user2, 2000)]],
        ];
    }
}
