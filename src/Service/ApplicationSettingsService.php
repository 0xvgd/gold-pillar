<?php

namespace App\Service;

use App\Entity\ApplicationSettings;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * ApplicationSettingsService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class ApplicationSettingsService
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
    public function save(ApplicationSettings $appSettings)
    {
        $this->em->beginTransaction();
        try {
            $this->em->persist($appSettings);
            $this->em->commit();

            $this->em->flush();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
