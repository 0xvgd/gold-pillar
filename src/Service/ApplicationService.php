<?php

namespace App\Service;

use App\Entity\Security\Application;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * ApplicationService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class ApplicationService
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
    public function save(Application $application)
    {
        $this->em->beginTransaction();
        try {
            $this->em->persist($application);

            $this->em->commit();

            $this->em->flush();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
