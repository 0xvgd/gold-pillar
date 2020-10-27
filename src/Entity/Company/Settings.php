<?php

namespace App\Entity\Company;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Embeddable
 */
class Settings
{
    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"read", "list"})
     */
    private $companyFee;

    /**
     * @ORM\Column(type="float")
     * @Groups({"read", "list"})
     */
    private $commissionRate;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=20, scale=2)
     * @Groups({"read", "list"})
     */
    private $monthlyFee;

    /**
     * @ORM\Column(type="float")
     * @Groups({"read", "list"})
     */
    private $projectAdvertisingReserve;

    /**
     * @ORM\Column(type="float")
     * @Groups({"read", "list"})
     */
    private $projectAgentEarnings;

    /**
     * @ORM\Column(type="float")
     * @Groups({"read", "list"})
     */
    private $projectContractorEarnings;

    /**
     * @ORM\Column(type="float")
     * @Groups({"read", "list"})
     */
    private $projectEngineerEarnings;

    /**
     * @ORM\Column(type="float")
     * @Groups({"read", "list"})
     */
    private $projectBrokerEarnings;

    /**
     * @ORM\Column(type="float")
     * @Groups({"read", "list"})
     */
    private $assetEntryFee;

    /**
     * @ORM\Column(type="float")
     * @Groups({"read", "list"})
     */
    private $assetExitFee;

    public function getCompanyFee(): ?float
    {
        return $this->companyFee;
    }

    public function setCompanyFee(?float $companyFee): self
    {
        $this->companyFee = $companyFee;

        return $this;
    }

    public function getCommissionRate(): ?float
    {
        return $this->commissionRate;
    }

    public function setCommissionRate(?float $commissionRate): self
    {
        $this->commissionRate = $commissionRate;

        return $this;
    }

    public function getMonthlyFee(): ?float
    {
        return $this->monthlyFee;
    }

    public function setMonthlyFee(?float $monthlyFee): self
    {
        $this->monthlyFee = $monthlyFee;

        return $this;
    }

    public function getProjectAdvertisingReserve(): ?float
    {
        return $this->projectAdvertisingReserve;
    }

    public function setProjectAdvertisingReserve(?float $projectAdvertisingReserve): self
    {
        $this->projectAdvertisingReserve = $projectAdvertisingReserve;

        return $this;
    }

    public function getProjectAgentEarnings(): ?float
    {
        return $this->projectAgentEarnings;
    }

    public function setProjectAgentEarnings(?float $projectAgentEarnings): self
    {
        $this->projectAgentEarnings = $projectAgentEarnings;

        return $this;
    }

    public function getProjectContractorEarnings(): ?float
    {
        return $this->projectContractorEarnings;
    }

    public function setProjectContractorEarnings(?float $projectContractorEarnings): self
    {
        $this->projectContractorEarnings = $projectContractorEarnings;

        return $this;
    }

    public function getProjectEngineerEarnings(): ?float
    {
        return $this->projectEngineerEarnings;
    }

    public function setProjectEngineerEarnings(?float $projectEngineerEarnings): self
    {
        $this->projectEngineerEarnings = $projectEngineerEarnings;

        return $this;
    }

    public function getProjectBrokerEarnings(): ?float
    {
        return $this->projectBrokerEarnings;
    }

    public function setProjectBrokerEarnings(?float $projectBrokerEarnings): self
    {
        $this->projectBrokerEarnings = $projectBrokerEarnings;

        return $this;
    }

    public function getAssetEntryFee(): ?float
    {
        return $this->assetEntryFee;
    }

    public function setAssetEntryFee(?float $assetEntryFee): self
    {
        $this->assetEntryFee = $assetEntryFee;

        return $this;
    }

    public function getAssetExitFee(): ?float
    {
        return $this->assetExitFee;
    }

    public function setAssetExitFee(?float $assetExitFee): self
    {
        $this->assetExitFee = $assetExitFee;

        return $this;
    }
}
