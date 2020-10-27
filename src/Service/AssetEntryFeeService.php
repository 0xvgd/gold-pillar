<?php

namespace App\Service;

use App\Entity\AssetEntryFee;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class AssetEntryFeeService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    public function save(AssetEntryFee $assetEntryFee)
    {
        $this->em->beginTransaction();
        try {
            $this->em->persist($assetEntryFee);

            $this->em->commit();

            $this->em->flush();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    /**
     * @return AssetEntryFee
     */
    public function getFeeByInvestment($investment)
    {
        $assetEntryFeeRepository = $this->em->getRepository(AssetEntryFee::class);
        $assetEntryFee = $assetEntryFeeRepository->findByInvestment($investment);

        return $assetEntryFee;
    }
}
