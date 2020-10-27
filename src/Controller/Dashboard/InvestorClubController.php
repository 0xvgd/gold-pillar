<?php

namespace App\Controller\Dashboard;

use App\Entity\Person\Investor;
use App\Entity\Security\User;
use App\Form\Dashboard\InvestorClubType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/investor-club",
 *  name="dashboard_investor_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class InvestorClubController extends AbstractController
{
    /**
     * @Route("/", name="club")
     */
    public function index(Request $request, EntityManagerInterface $em)
    {
        /** @var User */
        $user = $this->getUser();
        $form = $this->createForm(InvestorClubType::class, [
            'parentInviteCode' => $user->getInviteCode(),
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parentInviteCode = $form->get('parentInviteCode')->getData();

            if (!$user->getInviteCode()) {
                $user->setInviteCode($parentInviteCode);
            }

            $user->addRole('ROLE_INVESTOR');
            $investor = new Investor();
            $investor->setUser($user);

            $em->persist($investor);
            $em->flush();

            return $this->redirectToRoute(
                'frontend_subscribe_success',
                [
                    'request' => $request,
                ],
                307
            );
        }

        return $this->render('dashboard/investor_club/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
