<?php

namespace Dandevweb\Auction\Dao;

use Dandevweb\Auction\Model\Auction as AuctionModel;

class Auction
{
    private $con;

    public function __construct(\PDO $con)
    {
        $this->con = $con;
    }

    public function save(AuctionModel $auction): AuctionModel
    {
        $sql = 'INSERT INTO leiloes (descricao, finalizado, dataInicio) VALUES (?, ?, ?)';
        $stm = $this->con->prepare($sql);
        $stm->bindValue(1, $auction->getDescription(), \PDO::PARAM_STR);
        $stm->bindValue(2, $auction->isFinished(), \PDO::PARAM_BOOL);
        $stm->bindValue(3, $auction->getStartDate()->format('Y-m-d'));
        $stm->execute();

        return new AuctionModel(
            $auction->getDescription(),
            $auction->getStartDate(),
            $this->con->lastInsertId()
        );
    }

    public function getUnfinishedAuctions(): array
    {
        return $this->getAuctionsIfFinished(false);
    }

    public function getCompletedAuctions(): array
    {
        return $this->getAuctionsIfFinished(true);
    }

    private function getAuctionsIfFinished(bool $completed): array
    {
        $sql = 'SELECT * FROM leiloes WHERE finalizado = ' . ($completed ? 1 : 0);
        $stm = $this->con->query($sql, \PDO::FETCH_ASSOC);

        $auctionData = $stm->fetchAll();
        $auctions = [];
        foreach ($auctionData as $data) {
            $auction = new AuctionModel($data['descricao'], new \DateTimeImmutable($data['dataInicio']), $data['id']);
            if ($data['finalizado']) {
                $auction->finish();
            }
            $auctions[] = $auction;
        }

        return $auctions;
    }

    public function update(AuctionModel $auction)
    {
        $sql = 'UPDATE leiloes SET descricao = :descricao, dataInicio = :dataInicio, finalizado = :finalizado WHERE id = :id';
        $stm = $this->con->prepare($sql);
        $stm->bindValue(':descricao', $auction->getDescription());
        $stm->bindValue(':dataInicio', $auction->getStartDate()->format('Y-m-d'));
        $stm->bindValue(':finalizado', $auction->isFinished(), \PDO::PARAM_BOOL);
        $stm->bindValue(':id', $auction->getId(), \PDO::PARAM_INT);
        $stm->execute();
    }
    
}
