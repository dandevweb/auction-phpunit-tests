<?php

declare(strict_types=1);

namespace Dandevweb\Auction\Tests\Integration\Dao;

use PHPUnit\Framework\TestCase;
use Dandevweb\Auction\Model\Auction;
use PHPUnit\Framework\Attributes\DataProvider;
use Dandevweb\Auction\Dao\Auction as AuctionDao;

class AuctionDaoTest extends TestCase
{
    private static \PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new \PDO('sqlite::memory:');
        self::$pdo->exec('CREATE TABLE leiloes
            (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                descricao TEXT,
                finalizado INTEGER,
                dataInicio TEXT
            )'
        );
    }

    public function setUp(): void
    {
        self::$pdo->beginTransaction();
    }

    #[DataProvider('auctions')]
    public function testShouldBeAbleToGetUnfinishedAuctions(array $auctions)
    {
        $auctionDao = new AuctionDao(self::$pdo);

        foreach ($auctions as $auction) {
            $auctionDao->save($auction);
        }

        $auctions = $auctionDao->getUnfinishedAuctions();
        static::assertCount(1, $auctions);
        static::assertContainsOnlyInstancesOf(Auction::class, $auctions);
        static::assertSame('Fiat 147 0KM', $auctions[0]->getDescription());
        static::assertFalse($auctions[0]->isFinished());
    }

    #[DataProvider('auctions')]
    public function testShouldBeAbleToGetFinishedAuctions(array $auctions)
    {
        $auctionDao = new AuctionDao(self::$pdo);

        foreach ($auctions as $auction) {
            $auctionDao->save($auction);
        }

        $auctions = $auctionDao->getCompletedAuctions();
        static::assertCount(1, $auctions);
        static::assertContainsOnlyInstancesOf(Auction::class, $auctions);
        static::assertSame('Variant', $auctions[0]->getDescription());
        static::assertTrue($auctions[0]->isFinished());
    }

    public function testShouldBeAbleToUpdateAuctions()
    {
        $auction = new Auction('Brasília Amarela');
        $auctionDao = new AuctionDao(self::$pdo);
        $auction = $auctionDao->save($auction);
        $auction->finish();

        $unfinishedAuctions = $auctionDao->getUnfinishedAuctions();
        self::assertCount(1, $unfinishedAuctions);
        self::assertSame('Brasília Amarela', $unfinishedAuctions[0]->getDescription());
        self::assertFalse($unfinishedAuctions[0]->isFinished());

        $auctionDao->update($auction);

        $finishedAuctions = $auctionDao->getCompletedAuctions();
        self::assertCount(1, $finishedAuctions);
        self::assertSame('Brasília Amarela', $finishedAuctions[0]->getDescription());
        self::assertTrue($finishedAuctions[0]->isFinished());
    }

    public function tearDown(): void
    {
        self::$pdo->rollBack();
    }

    public static function auctions(): array
    {
        $unfinishedAuction = new Auction('Fiat 147 0KM');
        $finished = new Auction('Variant');
        $finished->finish();

        return [
            [[$unfinishedAuction, $finished]]
        ];
    }
}
