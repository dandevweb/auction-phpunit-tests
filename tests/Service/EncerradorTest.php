<?php

declare(strict_types=1);

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviadorEmail;
use \Alura\Leilao\Dao\Leilao as LeilaoDao;

class EncerradorTest extends TestCase
{
    private $encerrador;
    private $enviadorEmail;
    private $leilaoFiat147;
    private $leilaoVariant;
    
    protected function setUp(): void
    {
        $this->leilaoFiat147 = new Leilao('Fiat 147 0KM', new \DateTimeImmutable('8 days ago'));
        $this->leilaoVariant = new Leilao('Variant 1972 0KM', new \DateTimeImmutable('10 days ago'));
        
        $leilaoDao = $this->createMock(LeilaoDao::class);
        $leilaoDao->method('recuperarNaoFinalizados')->willReturn([$this->leilaoFiat147, $this->leilaoVariant]);
        $leilaoDao->method('recuperarFinalizados')->willReturn([$this->leilaoFiat147, $this->leilaoVariant]);
        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza');

        $this->enviadorEmail = $this->createMock(EnviadorEmail::class);

        $this->encerrador = new Encerrador($leilaoDao, $this->enviadorEmail);
    }

    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
       
        $this->encerrador->encerra();

        $leiloes = [$this->leilaoFiat147, $this->leilaoVariant];
        self::assertCount(2, $leiloes);
        self::assertTrue($leiloes[0]->estaFinalizado());
        self::assertTrue($leiloes[1]->estaFinalizado());
        
    }

    public function testDeveContinuarOProcessamentoAoEncontrarErroAoEnviarEmail()
    {
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willThrowException(new \DomainException('Erro ao enviar e-mail'));

        $this->encerrador->encerra();
        
    }

    public function testSoDeveEnviarLeilaoPorEmailAposFinalizado()
    {
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willReturnCallback(function (Leilao $leilao) {
                self::assertTrue($leilao->estaFinalizado());
            });

        $this->encerrador->encerra();
    }
}
