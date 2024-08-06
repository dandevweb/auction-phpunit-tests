<?php

namespace Dandevweb\Auction\Model;

class Auction
{
    private $offers;
    private $description;
    private $isFinished;
    private $startDate;
    private $id;

    public function __construct(string $description, \DateTimeImmutable $startDate = null, int $id = null)
    {
        $this->description = $description;
        $this->isFinished = false;
        $this->offers = [];
        $this->startDate = $startDate ?? new \DateTimeImmutable();
        $this->id = $id;
    }

    public function processOffer(Offer $lance)
    {
        if ($this->isFinished) {
            throw new \DomainException('Este leilão já está finalizado');
        }

        $ultimoLance = empty($this->offers)
            ? null
            : $this->offers[count($this->offers) - 1];
        if (!empty($this->offers) && $ultimoLance->getUser() == $lance->getUser()) {
            throw new \DomainException('Usuário já deu o último lance');
        }

        $this->offers[] = $lance;
    }

    public function finish()
    {
        $this->isFinished = true;
    }

    public function getOffers(): array
    {
        return $this->offers;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isFinished(): bool
    {
        return $this->isFinished;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function hasExceededOneWeek(): bool
    {
        $dtToday = new \DateTime();
        $duration = $this->startDate->diff($dtToday);

        return $duration->days > 7;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
