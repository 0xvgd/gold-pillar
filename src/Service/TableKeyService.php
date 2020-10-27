<?php

namespace App\Service;

use App\Entity\Helper\TableKey;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * TableKeyService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class TableKeyService
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
    public function save(TableKey $tableKey)
    {
        $this->em->beginTransaction();
        try {
            $this->em->persist($tableKey);
            $this->em->commit();
            $this->em->flush();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
