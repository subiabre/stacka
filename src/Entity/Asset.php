<?php

namespace App\Entity;

use App\Accounting\AbstractAccount;
use App\Accounting\Rounding\RoundingInterface;
use App\Repository\AssetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An `Asset` represents a transacteable item
 */
#[ORM\Entity(repositoryClass: AssetRepository::class)]
#[UniqueEntity(fields: ['name'], message: self::MESSAGE_ERROR_EXISTING)]
class Asset
{
    public const MESSAGE_ERROR_MISSING = "The asset '%s' does not exist.";
    public const MESSAGE_ERROR_EXISTING = "The asset '%s' already exists.";

    public const MESSAGE_SUCCESS_CREATED = "The asset '%s' was created successfully.";
    public const MESSAGE_SUCCESS_UPDATED = "The asset '%s' was updated successfully.";
    public const MESSAGE_SUCCESS_REMOVED = "The asset '%s' was removed successfully.";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\Length(max: 24, min: 4)]
    #[Assert\NotBlank()]
    #[ORM\Column(type: 'string', length: 24)]
    private $name;

    #[ORM\Column(type: 'object')]
    private $account;

    #[ORM\OneToMany(mappedBy: 'asset', targetEntity: Transaction::class, orphanRemoval: true)]
    private $transactions;

    #[Assert\Locale()]
    #[Assert\NotBlank()]
    #[ORM\Column(type: 'string', length: 255)]
    private $dateFormat;

    #[Assert\Locale()]
    #[Assert\NotBlank()]
    #[ORM\Column(type: 'string', length: 255)]
    private $moneyFormat;

    #[Assert\Currency()]
    #[Assert\NotBlank()]
    #[ORM\Column(type: 'string', length: 255)]
    private $moneyCurrency;

    #[Assert\PositiveOrZero()]
    #[Assert\NotBlank()]
    #[ORM\Column(type: 'integer')]
    private $moneyScale;

    #[ORM\Column(type: 'object')]
    private $moneyRounding;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAccount(): ?AbstractAccount
    {
        return $this->account;
    }

    public function setAccount(AbstractAccount $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setAsset($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getAsset() === $this) {
                $transaction->setAsset(null);
            }
        }

        return $this;
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    public function setDateFormat(string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    public function getMoneyFormat(): ?string
    {
        return $this->moneyFormat;
    }

    public function setMoneyFormat(string $moneyFormat): self
    {
        $this->moneyFormat = $moneyFormat;

        return $this;
    }

    public function getMoneyCurrency(): ?string
    {
        return $this->moneyCurrency;
    }

    public function setMoneyCurrency(string $moneyCurrency): self
    {
        $this->moneyCurrency = $moneyCurrency;

        return $this;
    }

    public function getMoneyScale(): ?int
    {
        return $this->moneyScale;
    }

    public function setMoneyScale(int $moneyScale): self
    {
        $this->moneyScale = $moneyScale;

        return $this;
    }

    public function getMoneyRounding(): ?RoundingInterface
    {
        return $this->moneyRounding;
    }

    public function setMoneyRounding(RoundingInterface $moneyRounding): self
    {
        $this->moneyRounding = $moneyRounding;

        return $this;
    }
}
