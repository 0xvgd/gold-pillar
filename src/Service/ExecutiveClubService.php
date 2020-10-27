<?php

namespace App\Service;

use App\Entity\ExecutiveClub;
use App\Utils\FileUploadManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ExecutiveClubService
{
    /**
     * @var FileUploadManager
     */
    private $uploader;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        EntityManagerInterface $em,
        FileUploadManager $uploader
    ) {
        $this->em = $em;
        $this->uploader = $uploader;
    }

    /**
     * @param array $mainPhoto
     *
     * @throws Exception
     */
    public function save(ExecutiveClub $executiveClub)
    {
        $this->em->beginTransaction();
        try {
            $this->em->persist($executiveClub);
            $this->em->commit();

            $this->em->flush();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
