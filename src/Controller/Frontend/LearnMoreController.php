<?php

namespace App\Controller\Frontend;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/learn-more")
 */

/**
 * @Route(
 *  "/{_locale}/learn-more",
 *  name="frontend_learn_more_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class LearnMoreController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PageRepository $repository)
    {
        $page = $repository->findOneBy(['name' => 'learn-more']);

        return $this->render('frontend/learn_more/index.html.twig', [
            'page' => $page,
        ]);
    }
}
