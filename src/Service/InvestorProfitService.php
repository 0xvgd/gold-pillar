<?php

namespace App\Service;

use App\Entity\InvestorProfit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class InvestorProfitService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    public function save(InvestorProfit $investorProfit)
    {
        $this->em->beginTransaction();
        try {
            $this->em->persist($investorProfit);

            $this->em->commit();

            $this->em->flush();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
