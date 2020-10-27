<?php

namespace App\Service;

use App\Entity\AssetInvestmentClass;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class AssetInvestmentClassService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    public function save(AssetInvestmentClass $assetInvestmentClass)
    {
        $this->em->beginTransaction();
        try {
            $this->em->persist($assetInvestmentClass);

            $this->em->commit();

            $this->em->flush();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    /**
     * @return AssetInvestmentClass
     */
    public function getByInvestment($investment)
    {
        $assetInvestmentClassRepo = $this->em->getRepository(AssetInvestmentClass::class);
        $assetEntryFee = $assetInvestmentClassRepo->findByInvestment($investment);

        return $assetEntryFee;
    }
}
