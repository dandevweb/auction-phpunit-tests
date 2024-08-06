<?php

namespace Dandevweb\Auction\Service;
use Dandevweb\Auction\Model\Offer;
use Dandevweb\Auction\Model\Auction;

class Auctioneer
{
    private $maxValue = -INF;
    private $minValue = INF;
    private $highestOffers;
    
    public function evaluate(Auction $auction): void
    {
        if ($auction->isFinished()) {
            throw new \DomainException('Leilão já finalizado');
        }

        if (empty($auction->getOffers())) {
            throw new \DomainException('Não é possível avaliar um leilão vazio');
        }
        
        foreach ($auction->getOffers() as $lance) {
            if ($lance->getValue() > $this->maxValue) {
                $this->maxValue = $lance->getValue();
            }
            
            if ($lance->getValue() < $this->minValue) {
                $this->minValue = $lance->getValue();
            }

            $offers = $auction->getOffers();
            usort($offers, function(Offer $offer1, Offer $offer2) {
                return $offer2->getValue() - $offer1->getValue();
            });
            
            $this->highestOffers = array_slice($offers, 0, 3);
        }

    }

    public function getHighestOffer(): float
    {
        return $this->maxValue;
    }

    public function getLowestValue(): float
    {
        return $this->minValue;
    }

    public function getHighestOffers(): array
    {
        return $this->highestOffers;
    }

}
