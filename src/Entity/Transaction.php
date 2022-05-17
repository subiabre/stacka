<?php

namespace App\Entity;

use App\Accounting\Inventory\Balance;
use App\Accounting\Transaction\TransactionType;
use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * A `Transaction` holds a `Balance` that's been transacted in `Buy` or `Sale` types
 */
#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $date;

    #[ORM\Column(type: 'object')]
    private $type;

    #[ORM\Column(type: 'object')]
    private $balance;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?TransactionType
    {
        return $this->type;
    }

    public function setType(TransactionType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBalance(): ?Balance
    {
        return $this->balance;
    }

    public function setBalance(Balance $balance): self
    {
        $this->balance = $balance;

        return $this;
    }
}
