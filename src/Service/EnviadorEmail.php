<?php

declare(strict_types=1);

namespace Alura\Leilao\Service;

use Alura\Leilao\Model\Leilao;

class EnviadorEmail
{
    public function notificarTerminoLeilao(Leilao $leilao): void
    {
        $sucesso = mail(
            'user@email.com',
            'Leilão finalizado',
            'O leilão para ' . $leilao->recuperarDescricao() . ' foi finalizado'
        );

        if (!$sucesso) {
            throw new \DomainException('Erro ao enviar e-mail');
        }
    }
}
