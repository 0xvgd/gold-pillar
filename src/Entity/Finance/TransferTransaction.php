<?php

namespace App\Entity\Finance;

use App\Entity\Security\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class TransferTransaction extends Transaction
{
    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity=Account::class)
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $origin;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity=Account::class)
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $destiny;

    /**
     * @var TransferTransaction
     *
     * @ORM\OneToOne(targetEntity=TransferTransaction::class)
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $relatedTransaction;

    public function __construct(
        User $user,
        Account $account,
        Account $origin,
        Account $destiny,
        float $amount,
        ?Transaction $previous = null
    ) {
        $this->origin = $origin;
        $this->destiny = $destiny;
        parent::__construct($user, $account, $amount, $previous);
    }

    public function isCredit(): bool
    {
        if ($this->destiny && $this->account) {
            return $this->destiny->getId() === $this->account->getId();
        }

        return false;
    }

    public function getOrigin(): ?Account
    {
        return $this->origin;
    }

    public function getDestiny(): ?Account
    {
        return $this->destiny;
    }

    public function getRelatedTransaction(): ?self
    {
        return $this->relatedTransaction;
    }

    public function setRelatedTransaction(self $relatedTransaction): self
    {
        $this->relatedTransaction = $relatedTransaction;

        return $this;
    }

    public function setOrigin(?Account $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function setDestiny(?Account $destiny): self
    {
        $this->destiny = $destiny;

        return $this;
    }
}
