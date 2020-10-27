<?php

namespace App\Translation;

use App\Entity\Translation\Locale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class CustomLoader implements LoaderInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function load($resource, $currentLocale, $domain = 'messages')
    {
        $localeRepo = $this->em->getRepository(Locale::class);

        /** @var Locale */
        $locale = $localeRepo->findOneBy([
            'code' => $currentLocale,
        ]);

        $messages = [];

        if ($locale) {
            foreach ($locale->getTranslations() as $translation) {
                $messages[$translation->getSource()] = $translation->getTarget();
            }
        }

        $messageCatalogue = new MessageCatalogue($currentLocale);

        $messageCatalogue->add($messages, $domain);

        return $messageCatalogue;
    }
}
