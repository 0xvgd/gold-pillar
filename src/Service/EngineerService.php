<?php

namespace App\Service;

use App\Entity\Person\Engineer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * EngineerService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class EngineerService
{
    private $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    public function save(Engineer $engineer)
    {
        $this->em->beginTransaction();
        try {
            $this->em->persist($engineer);
            $this->em->commit();
            $this->em->flush();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
