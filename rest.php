<?php

use Dandevweb\Auction\Dao\Auction as AuctionDao;
use Dandevweb\Auction\Model\Auction;

require_once __DIR__ . '/vendor/autoload.php';

$pdo = new \PDO('sqlite::memory:');
$pdo->exec('create table leiloes (
    id INTEGER primary key,
    descricao TEXT,
    finalizado BOOL,
    dataInicio TEXT
);');
$auctionDao = new AuctionDao($pdo);

$auction1 = new Auction('Leilão 1');
$auction2 = new Auction('Leilão 2');
$auction3 = new Auction('Leilão 3');
$auction4 = new Auction('Leilão 4');

$auctionDao->save($auction1);
$auctionDao->save($auction2);
$auctionDao->save($auction3);
$auctionDao->save($auction4);

header('Content-type: application/json');
echo json_encode(array_map(function (Auction $auction) {
    return [
        'description' => $auction->getDescription(),
        'isFinished' => $auction->isFinished(),
    ];
}, $auctionDao->getUnfinishedAuctions()));
