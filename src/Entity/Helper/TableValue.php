<?php

namespace App\Entity\Helper;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Helper\TableValueRepository")
 */
class TableValue implements \JsonSerializable
{
    /*
     * TRANSACTION_TYPE [2]
     */
    const TRANSACTION_TYPE_AGENT_MONTHLY_FEE = 5;
    const TRANSACTION_TYPE_AGENT_COMISSION = 14;
    const TRANSACTION_TYPE_PARENT_AGENT_BONUS = 15;
    const TRANSACTION_TYPE_GRANDPARENT_AGENT_BONUS = 53;
    const TRANSACTION_TYPE_COMPANY_COMISSION = 54;
    const PROPERTY_TRANSACTION_TYPE_UNDISTRIBUTED_AMOUNT = 55;
    const TRANSACTION_TYPE_TOTAL_COMISSION = 16;
    const TRANSACTION_TYPE_TRANSFER = 45;

    /*
    * PROJECT_TRANSACTION_TYPE [7]
    */
    const PROJECT_TRANSACTION_TYPE_INVESTMENT = 17;
    const PROJECT_TRANSACTION_TYPE_INVESTOR_PROFIT = 24;
    const PROJECT_TRANSACTION_TYPE_ENTRY_FEE = 25;
    const PROJECT_TRANSACTION_TYPE_ENGINEERS_EARNINGS = 26;
    const PROJECT_TRANSACTION_TYPE_CONTRACTOR_EARNINGS = 27;
    const PROJECT_TRANSACTION_TYPE_EXIT_FEE = 28;
    const PROJECT_TRANSACTION_TYPE_PROJECT_PROFIT = 29;
    const PROJECT_TRANSACTION_TYPE_COMPANY_PROFIT = 30;
    const PROJECT_TRANSACTION_TYPE_PURCHASE_PRICE = 31;
    const PROJECT_TRANSACTION_TYPE_SALES_PRICE = 32;
    const PROJECT_TRANSACTION_TYPE_INVESTMENT_RETURN = 33;
    const PROJECT_TRANSACTION_TYPE_ADVERTISING_RESERVE = 50;
    const PROJECT_TRANSACTION_TYPE_BROKER_EARNINGS = 51;
    const PROJECT_TRANSACTION_TYPE_AGENT_EARNINGS = 52;

    /*
     * ASSET_TRANSACTION_TYPE [9]
     */
    const ASSET_TRANSACTION_TYPE_INVESTMENT = 34;
    const ASSET_TRANSACTION_TYPE_ENTRY_FEE = 35;
    const ASSET_TRANSACTION_TYPE_DIVIDEND_FEE = 36;
    const ASSET_TRANSACTION_TYPE_EXIT_FEE = 37;
    const ASSET_TRANSACTION_TYPE_INVESTOR_MONTHLY_DIVIDEND = 38;
    const ASSET_TRANSACTION_TYPE_RENTAL_INCOME = 43;
    const ASSET_TRANSACTION_TYPE_UNDISTRIBUTED_AMOUNT = 44;
    const ASSET_TRANSACTION_TYPE_CONUNCIL_TAX = 45;
    const ASSET_TRANSACTION_TYPE_ELETRICITY = 46;
    const ASSET_TRANSACTION_TYPE_GAS = 47;
    const ASSET_TRANSACTION_TYPE_TV_LICENCE = 48;
    const ASSET_TRANSACTION_TYPE_LETTING_FEES = 49;

    /*
     * BALANCE_TRANSACTION_TYPE [10]
     */
    const BALANCE_TRANSACTION_TYPE_TRANSFER = 40;
    const BALANCE_TRANSACTION_TYPE_CREDIT_NOTE = 41;
    const BALANCE_TRANSACTION_TYPE_DEBIT_NOTE = 42;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TableKey::class, inversedBy="tableValues")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $tableKey;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getTableKey(): ?TableKey
    {
        return $this->tableKey;
    }

    public function setTableKey(?TableKey $tableKey): self
    {
        $this->tableKey = $tableKey;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function __toString()
    {
        return $this->getDescription();
    }

    public function unserialize($serialized)
    {
        return list(
            $this->id,
            $this->description
        ) = unserialize($serialized);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
        ];
    }
}
