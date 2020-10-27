<?php

namespace App\Service;

use App\Entity\Plan;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PlanService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    public function save(Plan $pĺan)
    {
        $this->em->beginTransaction();
        try {
            $this->em->persist($pĺan);

            $this->em->commit();

            $this->em->flush();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
