<?php

namespace App\Entity\Finance;

use App\Entity\Resource\Resource;
use App\Enum\PeriodUnit;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="finance_recurring")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "income"=RecurringIncome::class,
 *     "expense"=RecurringExpense::class
 * })
 */
abstract class Recurring
{
    public const WEEK = [
        'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',
        'Saturday', 'Sunday',
    ];
    public const MONTHS = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];

    /**
     * @var UuidInterface|null
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"list", "read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"list", "read"})
     */
    private $createdAt;

    /**
     * @var float|null
     *
     * @ORM\Column(type="decimal", precision=20, scale=2)
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     * @Groups({"list", "read", "write"})
     */
    private $amount;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150)
     * @Groups({"list", "read", "write"})
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     */
    private $description;

    /**
     * @var PeriodUnit|null
     *
     * @ORM\Column(type=PeriodUnit::class, name="intervl")
     * @Groups({"list", "read", "write"})
     * @Assert\NotBlank()
     */
    private $interval;

    /**
     * Day of week when interval is Weekly
     * Day of month when interval is monthly
     * Month of year when yearly.
     *
     * @var int|null
     *
     * @ORM\Column(type="integer")
     * @Groups({"list", "read", "write"})
     * @Assert\NotBlank()
     */
    private $dayOrMonth;

    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(type="time")
     * @Groups({"list", "read", "write"})
     * @Assert\NotBlank()
     */
    private $time;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Groups({"list", "read"})
     */
    private $yearlyProjection;

    public function __construct(Resource $resource)
    {
        $this->createdAt = new DateTime();
        $this->setResource($resource);
    }

    abstract public function getResource(): ?Resource;

    abstract public function setResource(?Resource $resource);

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    public function getInterval(): ?PeriodUnit
    {
        return $this->interval;
    }

    public function setInterval(?PeriodUnit $interval)
    {
        $this->interval = $interval;

        return $this;
    }

    public function getDayOrMonth(): ?int
    {
        return $this->dayOrMonth;
    }

    public function setDayOrMonth(?int $dayOrMonth)
    {
        $this->dayOrMonth = $dayOrMonth;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?\DateTimeInterface $time)
    {
        $this->time = $time;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @Groups({"list", "read"})
     */
    public function getFormattedLabel(): string
    {
        $str = (string) $this->dayOrMonth;
        if ($this->interval) {
            switch ($this->interval->getValue()) {
                case PeriodUnit::DAY():
                    $str = 'Daily';
                    break;
                case PeriodUnit::WEEK():
                    $str = 'Weely, all '.self::WEEK[$this->dayOrMonth - 1];
                    break;
                case PeriodUnit::MONTH():
                    $day = $this->dayOrMonth;
                    if ($this->dayOrMonth > 3) {
                        $day .= 'th';
                    } elseif (1 == $this->dayOrMonth) {
                        $day .= 'st';
                    } elseif (2 == $this->dayOrMonth) {
                        $day .= 'nd';
                    } elseif (3 == $this->dayOrMonth) {
                        $day .= 'rd';
                    }
                    $str = 'Monthly, on the '.$day;
                    break;
                case PeriodUnit::YEAR():
                    $str = 'Yearly, all '.self::MONTHS[$this->dayOrMonth - 1];
                    break;
            }
        }

        return $str;
    }

    public function __toString()
    {
        return $this->getFormattedLabel();
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

    public function getYearlyProjection(): ?string
    {
        return $this->yearlyProjection;
    }

    public function setYearlyProjection(string $yearlyProjection): self
    {
        $this->yearlyProjection = $yearlyProjection;

        return $this;
    }
}
