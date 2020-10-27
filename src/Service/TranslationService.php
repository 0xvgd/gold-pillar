<?php

namespace App\Service;

use App\Entity\Translation\Translation;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class TranslationService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    public function removeTranslationsByBase(Translation $base)
    {
        $translations = $this->em->getRepository(Translation::class)->findBy([
            'base' => $base,
        ]);

        foreach ($translations as $translattion) {
            $this->em->remove($translattion);
        }

        $this->em->flush();
    }
}
