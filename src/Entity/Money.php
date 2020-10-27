<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Embeddable
 */
class Money
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3)
     * @Groups({"read", "list"})
     */
    private $currency;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=20, scale=2)
     * @Groups({"read", "list"})
     */
    private $amount;

    public function __construct(float $amount = null, string $currency = 'GBP')
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function add(float $amount): self
    {
        $this->amount += $amount;

        return $this;
    }

    public function sub(float $amount): self
    {
        $this->amount -= $amount;

        return $this;
    }

    public function getSymbol(): string
    {
        try {
            $symbol = Currencies::getSymbol($this->currency);

            return $symbol;
        } catch (MissingResourceException $ex) {
        }

        return '?';
    }
}
