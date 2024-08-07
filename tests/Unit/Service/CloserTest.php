<?php

declare(strict_types=1);

namespace Dandevweb\Auction\Tests\Service;

use Dandevweb\Auction\Model\Auction;
use PHPUnit\Framework\TestCase;
use Dandevweb\Auction\Service\Closer;
use Dandevweb\Auction\Service\EmailSender;
use \Dandevweb\Auction\Dao\Auction as LeilaoDao;

class CloserTest extends TestCase
{
    private $encerrador;
    private $enviadorEmail;
    private $leilaoFiat147;
    private $leilaoVariant;
    
    protected function setUp(): void
    {
        $this->leilaoFiat147 = new Auction('Fiat 147 0KM', new \DateTimeImmutable('8 days ago'));
        $this->leilaoVariant = new Auction('Variant 1972 0KM', new \DateTimeImmutable('10 days ago'));
        
        $leilaoDao = $this->createMock(LeilaoDao::class);
        $leilaoDao->method('getUnfinishedAuctions')->willReturn([$this->leilaoFiat147, $this->leilaoVariant]);
        $leilaoDao->method('getCompletedAuctions')->willReturn([$this->leilaoFiat147, $this->leilaoVariant]);
        $leilaoDao->expects($this->exactly(2))
            ->method('update');

        $this->enviadorEmail = $this->createMock(EmailSender::class);

        $this->encerrador = new Closer($leilaoDao, $this->enviadorEmail);
    }

    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
       
        $this->encerrador->finish();

        $auctions = [$this->leilaoFiat147, $this->leilaoVariant];
        self::assertCount(2, $auctions);
        self::assertTrue($auctions[0]->isFinished());
        self::assertTrue($auctions[1]->isFinished());
        
    }

    public function testDeveContinuarOProcessamentoAoEncontrarErroAoEnviarEmail()
    {
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notifyAuctionEnd')
            ->willThrowException(new \DomainException('Erro ao enviar e-mail'));

        $this->encerrador->finish();
        
    }

    public function testSoDeveEnviarLeilaoPorEmailAposFinalizado()
    {
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notifyAuctionEnd')
            ->willReturnCallback(function (Auction $auction) {
                self::assertTrue($auction->isFinished());
            });

        $this->encerrador->finish();
    }
}
