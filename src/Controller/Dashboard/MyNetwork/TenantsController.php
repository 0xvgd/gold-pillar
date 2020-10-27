<?php

namespace App\Controller\Dashboard\MyNetwork;

use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/my/network/tenants",
 *  name="dashboard_my_network_tenants_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class TenantsController extends AbstractController
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

        $tenants = (array_filter($user->getChildren()->toArray(), function ($child) {
            return true === $child->isTenant();
        }));

        if (count($tenants) > 0) {
            $serializer = $this->get('serializer');
            $userJson = $serializer->serialize($user, 'json', ['groups' => 'tree']);
        }

        return $this->render('dashboard/my_network/tenants/index.html.twig', [
            'userJson' => $userJson,
        ]);
    }
}
