<?php

namespace App\Entity\Finance;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="finance_accounts")
 * @ORM\Entity()
 */
class Account
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"list","read"})
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     * @Groups({"list","read"})
     */
    private $balance;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="account")
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $transactions;

    /**
     * @ORM\OneToOne(targetEntity=Transaction::class, cascade={"persist", "remove"})
     * @Groups({"read"})
     */
    private $lastTransaction;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"list","read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"list","read"})
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=1, name="chr")
     * @Groups({"list","read"})
     */
    private $char;

    public function __construct(string $char)
    {
        $this->balance = 0;
        $this->createdAt = new DateTime();
        $this->transactions = new ArrayCollection();
        $this->char = $char;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getLastTransaction(): ?Transaction
    {
        return $this->lastTransaction;
    }

    public function setLastTransaction(?Transaction $lastTransaction): self
    {
        $this->lastTransaction = $lastTransaction;

        return $this;
    }

    /**
     * @return Transaction[]|Collection
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getChar()
    {
        return $this->char;
    }

    public function getFormattedNumber(): string
    {
        return (string) sprintf('%s%s', $this->char, str_pad($this->number, 5, '0', STR_PAD_LEFT));
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setAccount($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getAccount() === $this) {
                $transaction->setAccount(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getFormattedNumber();
    }
}
