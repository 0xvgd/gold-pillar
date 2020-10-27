<?php

namespace App\Controller\Frontend;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/learn-more")
 */
class LearnMoreController extends AbstractController
{
    /**
     * @Route("/", name="frontend_learn_more_index")
     */
    public function index(PageRepository $repository)
    {
        $page = $repository->findOneBy(['name' => 'learn-more']);

        return $this->render('frontend/learn_more/index.html.twig', [
            'page' => $page,
        ]);
    }
}
