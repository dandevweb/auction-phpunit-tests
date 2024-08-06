<?php

use Dandevweb\Auction\Model\Offer;
use Dandevweb\Auction\Model\Auction;
use Dandevweb\Auction\Model\User;
use Dandevweb\Auction\Service\Auctioneer;

require 'vendor/autoload.php';

$auction = new Auction('Fiat 147 0km');

$maria = new User('Maria');
$joao = new User('João');

$auction->processOffer(new Offer($maria, 2000));
$auction->processOffer(new Offer($joao, 2500));

$auctioneer = new Auctioneer();
$auctioneer->evaluate($auction);

$highestValue = $auctioneer->getHighestOffer();



echo $highestValue;
