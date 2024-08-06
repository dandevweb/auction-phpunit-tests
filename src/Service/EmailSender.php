<?php

declare(strict_types=1);

namespace Dandevweb\Auction\Service;

use Dandevweb\Auction\Model\Auction;

class EmailSender
{
    public function notifyAuctionEnd(Auction $auction): void
    {
        $sendEmailSuccess = mail(
            'user@email.com',
            'Leilão finalizado',
            'O leilão para ' . $auction->getDescription() . ' foi finalizado'
        );

        if (!$sendEmailSuccess) {
            throw new \DomainException('Erro ao enviar e-mail');
        }
    }
}
