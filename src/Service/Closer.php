<?php

namespace Dandevweb\Auction\Service;

use Dandevweb\Auction\Service\EmailSender;
use Dandevweb\Auction\Dao\Auction as AuctionDao;

class Closer
{
    public function __construct(
        private AuctionDao $dao,
        private EmailSender $emailSender
    ) {}

    public function finish()
    {
        $auctions = $this->dao->getUnfinishedAuctions();

        foreach ($auctions as $auction) {
            if ($auction->hasExceededOneWeek()) {
                try {
                    $auction->finish();
                    $this->dao->update($auction);
                    $this->emailSender->notifyAuctionEnd($auction);
                } catch (\DomainException $e) {
                    error_log($e->getMessage());
                }
            }
        }
    }
}
