<?php

namespace App\Service;

use App\Entity\Person\Contractor;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * ContractorService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class ContractorService
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
    public function save(Contractor $contractor)
    {
        $this->em->beginTransaction();
        try {
            $this->em->persist($contractor);
            $this->em->commit();
            $this->em->flush();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
