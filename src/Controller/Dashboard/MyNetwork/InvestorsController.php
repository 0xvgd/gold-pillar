<?php

namespace App\Controller\Dashboard\MyNetwork;

use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/my/network/investors",
 *  name="dashboard_my_network_investors_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class InvestorsController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(EntityManagerInterface $em)
    {
        $userJson = [];

        $user = $em->getRepository(User::class)->findOneBy([
            'id' => $this->getUser()->getId(),
        ]);

        $investors = (array_filter($user->getChildren()->toArray(), function ($child) {
            return true === $child->isInvestor();
        }));

        if (count($investors) > 0) {
            $serializer = $this->get('serializer');
            $userJson = $serializer->serialize($user, 'json', ['groups' => 'tree']);
        }

        return $this->render('dashboard/my_network/investors/index.html.twig', [
            'userJson' => $userJson,
        ]);
    }
}
