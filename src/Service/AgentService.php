<?php

namespace App\Service;

use App\Entity\Person\Agent;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class AgentService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    public function save(Agent $agent)
    {
        $this->em->beginTransaction();
        try {
            $this->em->persist($agent);
            $this->em->flush();
            $this->em->commit();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
