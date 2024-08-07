<?php

declare(strict_types=1);

namespace Dandevweb\Auction\Tests\Integration\Dao;

use Dandevweb\Auction\Infra\ConnectionCreator;
use PHPUnit\Framework\TestCase;
use Dandevweb\Auction\Dao\Auction as AuctionDao;
use Dandevweb\Auction\Model\Auction;

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

    public function testShouldBeAbleToSaveAndGetAuctions()
    {
        
        $auctionName = 'Fiat 147 0KM';
        $auction = new Auction($auctionName);
        $auctionDao = new AuctionDao(self::$pdo);

        $auctionDao->save($auction);

        $auctions = $auctionDao->getUnfinishedAuctions();
        static::assertCount(1, $auctions);
        static::assertContainsOnlyInstancesOf(Auction::class, $auctions);
        static::assertSame($auctionName, $auctions[0]->getDescription());
    }

    public function tearDown(): void
    {
        self::$pdo->rollBack();
    }
}
