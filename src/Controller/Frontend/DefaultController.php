<?php

namespace App\Controller\Frontend;

use App\Entity\Resource\Asset;
use App\Form\SearchProductType;
use App\Repository\Translation\LocaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route(
     *      "/{_locale}/",
     *      name="app_home",
     *      requirements={"_locale"="%app.supported_locales%"},
     *  )
     */
    public function index(EntityManagerInterface $em)
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchProductType::class)
            ->getForm();

        $rs = $em
            ->createQueryBuilder()
            ->select([
                'MIN(a.marketValue.amount) as minValue',
                'MAX(a.marketValue.amount) as maxValue',
            ])
            ->from(Asset::class, 'a')
            ->getQuery()
            ->getSingleResult();

        $minValue = $rs['minValue'] ?? '';
        $maxValue = $rs['maxValue'] ?? '';

        return $this->render('frontend/default/index.html.twig', [
            'searchForm' => $form->createView(),
            'minValue' => $minValue,
            'maxValue' => $maxValue,
        ]);
    }

    /**
     * @Route(
     *      "/{_locale}/locale",
     *      name="app_translations",
     *      requirements={"_locale"="%app.supported_locales%"},
     *  )
     */
    public function getTranslations(Request $request, LocaleRepository $repo)
    {
        $locale = $repo->findOneBy([
            'code' => $request->getLocale(),
        ]);

        $translations = [];

        foreach ($locale->getTranslations() as $translation) {
            $source = null;

            if ($translation->getBase()) {
                $source = $translation->getBase()->getSource();
            } else {
                $source = $translation->getSource();
            }

            $translations[] =
            [
                'source' => "$source",
                'target' => "{$translation->getTarget()}",
            ];
        }

        return $this->render('includes/translations.html.twig', [
            'translations' => $translations,
        ]);
    }
}
