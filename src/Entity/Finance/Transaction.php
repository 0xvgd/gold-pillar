<?php

namespace App\Entity\Finance;

use App\Entity\Security\User;
use App\Enum\TransactionStatus;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\Table(name="finance_transactions")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "transfer"=TransferTransaction::class,
 *     "deposit"=DepositTransaction::class,
 *     "withdrawl"=WithdrawalTransaction::class
 * })
 */
abstract class Transaction
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"read", "list"})
     */
    private $id;

    /**
     * @var TransactionStatus
     *
     * @ORM\Column(type=TransactionStatus::class)
     * @Groups({"read", "list"})
     */
    private $status;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"read"})
     */
    protected $account;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=20, scale=2)
     * @Groups({"read", "list"})
     */
    protected $amount;

    /**
     * @ORM\Column(type="float")
     */
    private $currentBalance;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read", "list"})
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "list"})
     */
    protected $processedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "list"})
     */
    private $cancelledAt;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read", "list"})
     */
    protected $monthRef;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read", "list"})
     */
    protected $yearRef;

    /**
     * @ORM\ManyToOne(targetEntity=Transaction::class)
     */
    protected $previous;

    /**
     * @ORM\Column(type="string", length=60)
     */
    protected $signature;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"read", "list"})
     */
    protected $transactionId;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"list", "read"})
     */
    protected $credit;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "list"})
     */
    private $note;

    public function __construct(
        User $user,
        Account $account,
        float $amount,
        ?self $previous = null
    ) {
        $this->user = $user;
        $this->account = $account;
        $this->amount = $amount;
        $this->previous = $previous;
        $this->createdAt = new DateTime();
        $this->status = TransactionStatus::CREATED();
        $this->currentBalance = $account->getBalance();
        $this->monthRef = (int) date('m');
        $this->yearRef = (int) date('Y');
        $this->credit = $this->isCredit();
    }

    abstract public function isCredit(): bool;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getProcessedAt(): ?\DateTimeInterface
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeInterface $processedAt): self
    {
        $this->processedAt = $processedAt;

        return $this;
    }

    public function getMonthRef(): ?int
    {
        return $this->monthRef;
    }

    public function setMonthRef(int $monthRef): self
    {
        $this->monthRef = $monthRef;

        return $this;
    }

    public function getYearRef(): ?int
    {
        return $this->yearRef;
    }

    public function setYearRef(int $yearRef): self
    {
        $this->yearRef = $yearRef;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPrevious(): ?self
    {
        return $this->previous;
    }

    public function setPrevious(?self $previous): self
    {
        $this->previous = $previous;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(string $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @Groups({"read","list"})
     */
    public function isVerified(): bool
    {
        return $this->sign() === $this->signature;
    }

    protected function serialize(): string
    {
        if ($this instanceof TransferTransaction) {
            $type = 'transfer';
        } elseif ($this instanceof DepositTransaction) {
            $type = 'deposit';
        } elseif ($this instanceof WithdrawalTransaction) {
            $type = 'withdrawl';
        } else {
            $type = 'unknown';
        }

        $dt = new DateTime();
        $dt->setTimestamp($this->createdAt->getTimestamp());
        $dt->setTimezone(new \DateTimeZone('UTC'));

        return implode(';', [
            $type,
            (string) $this->transactionId,
            (string) $this->account->getId(),
            (string) $this->user->getId(),
            number_format($this->amount, 2, '.', ''),
            $dt->format('Y-m-d\TH:i:s'),
        ]);
    }

    public function sign(): string
    {
        $str = $this->serialize();

        if ($this->previous) {
            $str = sprintf('%s{%s}', $str, $this->previous->getSignature());
        }

        return sha1($str);
    }

    public function getStatus(): TransactionStatus
    {
        return $this->status;
    }

    public function setStatus(TransactionStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCancelledAt(): ?\DateTimeInterface
    {
        return $this->cancelledAt;
    }

    public function setCancelledAt(?\DateTimeInterface $cancelledAt): self
    {
        $this->cancelledAt = $cancelledAt;

        return $this;
    }

    public function getCurrentBalance(): ?float
    {
        return $this->currentBalance;
    }

    public function setCurrentBalance(float $currentBalance): self
    {
        $this->currentBalance = $currentBalance;

        return $this;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(?string $transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
