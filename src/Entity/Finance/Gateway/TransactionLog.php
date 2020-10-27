<?php

namespace App\Entity\Finance\Gateway;

use App\Repository\Finance\Gateway\TransactionLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TransactionLogRepository::class)
 */
class TransactionLog
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
     * @ORM\Column(type="string", length=30)
     */
    private $orderId;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=0, nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $amountMajor;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $currency;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $crossReference;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $statusCode;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gatewayMessage;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $transactionDatetime;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $transactionDatetimeText;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $integrationType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $timestamp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmountMajor(): ?string
    {
        return $this->amountMajor;
    }

    public function setAmountMajor(string $amountMajor): self
    {
        $this->amountMajor = $amountMajor;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCrossReference(): ?string
    {
        return $this->crossReference;
    }

    public function setCrossReference(string $crossReference): self
    {
        $this->crossReference = $crossReference;

        return $this;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function setStatusCode(?int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getGatewayMessage(): ?string
    {
        return $this->gatewayMessage;
    }

    public function setGatewayMessage(?string $gatewayMessage): self
    {
        $this->gatewayMessage = $gatewayMessage;

        return $this;
    }

    public function getTransactionDatetime(): ?\DateTimeInterface
    {
        return $this->transactionDatetime;
    }

    public function setTransactionDatetime(?\DateTimeInterface $transactionDatetime): self
    {
        $this->transactionDatetime = $transactionDatetime;

        return $this;
    }

    public function getTransactionDatetimeText(): ?string
    {
        return $this->transactionDatetimeText;
    }

    public function setTransactionDatetimeText(?string $transactionDatetimeText): self
    {
        $this->transactionDatetimeText = $transactionDatetimeText;

        return $this;
    }

    public function getIntegrationType(): ?string
    {
        return $this->integrationType;
    }

    public function setIntegrationType(?string $integrationType): self
    {
        $this->integrationType = $integrationType;

        return $this;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(?int $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
