<?php

namespace App\Controller\Frontend;

use App\Entity\AgentPreRegistration;
use App\Entity\Person\Tenant;
use App\Entity\Plan;
use App\Entity\Security\User;
use App\Form\Subscribe\AgentPreRegistrationType;
use App\Form\Subscribe\RegistrationType;
use App\Service\SubscribeService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route(
 *  "/{_locale}/subscribe",
 *  name="frontend_subscribe_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class SubscribeController extends AbstractController
{
    const FORM_MSG_ERROR = 'The submitted form is not valid. Please check the fields and send again.';

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var SubscribeService
     */
    private $service;

    public function __construct(UserPasswordEncoderInterface $encoder, SubscribeService $service)
    {
        $this->encoder = $encoder;
        $this->service = $service;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(Request $request)
    {
        return $this->redirectToRoute('frontend_subscribe_choose');
    }

    /**
     * @Route("/choose/", name="choose", methods={"GET"})
     */
    public function choose(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('dashboard_index');
        }

        return $this->render('frontend/subscribe/choose.html.twig');
    }

    /**
     * @Route("/tenant/", name="tenant", methods={"GET", "POST"})
     */
    public function tenant(Request $request, EntityManagerInterface $em)
    {
        $user = new User();
        $form = $this
            ->createForm(RegistrationType::class, $user, [
                'planCode' => null,
                'parentInviteCode' => null,
            ])
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $phone = $form->get('phone')->getData();
            $plainPass = $form->get('plainPassword')->getData();
            $encodedPass = $this
                ->encoder
                ->encodePassword($user, $plainPass);

            $user
                ->setEmail($email)
                ->setPhone($phone)
                ->setPassword($encodedPass)
                ->setRoles(['ROLE_TENANT']);

            $tenant = new Tenant();
            $tenant->setUser($user);

            $em->persist($tenant);
            $em->flush();

            $this->service->sendConfirmationMail($user);

            return $this->redirectToRoute('frontend_subscribe_success', [
                'request' => $request,
            ], 307);
        }

        return $this->render('frontend/subscribe/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/agent/", name="agent", methods={"GET", "POST"})
     * @Route("/agent/{inviteCode}", name="agent_bring", methods={"GET", "POST"})
     */
    public function agent(
        Request $request,
        EntityManagerInterface $em,
        string $inviteCode = null
    ) {
        return $this->planSubscribe(
            $request,
            $em,
            Plan::PLAN_AGENT,
            $inviteCode
        );
    }

    /**
     * @Route("/agentpreregistration", name="agent_pre_registration", methods={"GET", "POST"})
     */
    public function agentPreRegistration(
        Request $request,
        EntityManagerInterface $em
    ) {
        $preRegistration = new AgentPreRegistration();
        $form = $this
            ->createForm(AgentPreRegistrationType::class, $preRegistration)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($preRegistration);
            $em->flush();

            $this->addFlash('success', 'Item saved successfully.');

            return $this->redirectToRoute('frontend_subscribe_preregistration_success', [
                'request' => $request,
            ], 307);
        }

        return $this->render('frontend/subscribe/agent_preregistration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/preregistration-success", name="preregistration_success", methods={"POST"})
     */
    public function preregistrationSuccess(Request $request)
    {
        return $this->render('frontend/subscribe/agent_preregistration/success.html.twig');
    }

    /**
     * @Route("/investor/", name="investor", methods={"GET", "POST"})
     * @Route("/investor/{inviteCode}", name="investor_bring", methods={"GET", "POST"})
     */
    public function investor(
        Request $request,
        EntityManagerInterface $em,
        string $inviteCode = null
    ) {
        return $this->planSubscribe(
            $request,
            $em,
            Plan::PLAN_INVESTOR,
            $inviteCode
        );
    }

    /**
     * @Route("/broker/", name="broker", methods={"GET", "POST"})
     * @Route("/broker/{inviteCode}", name="broker_bring", methods={"GET", "POST"})
     */
    public function broker(
        Request $request,
        EntityManagerInterface $em,
        string $inviteCode = null
    ) {
        return $this->planSubscribe(
            $request,
            $em,
            Plan::PLAN_BROKER,
            $inviteCode
        );
    }

    private function planSubscribe(
        Request $request,
        EntityManagerInterface $em,
        string $planCode,
        string $inviteCode = null
    ) {
        $parentUser = null;

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('dashboard_index');
        }

        if ($inviteCode) {
            $parentUser = $em
                ->getRepository(User::class)
                ->findOneBy([
                    'inviteCode' => $inviteCode,
                ]);
        }

        $plan = $em
            ->getRepository(Plan::class)
            ->findOneBy([
                'code' => $planCode,
            ]);

        $user = new User();

        $form = $this
            ->createForm(RegistrationType::class, $user, [
                'planCode' => $planCode,
                'parentInviteCode' => $inviteCode,
            ])
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $phone = $form->get('phone')->getData();
            $plainPass = $form->get('plainPassword')->getData();
            $encodedPass = $this
                ->encoder
                ->encodePassword($user, $plainPass);

            $user
                ->setEmail($email)
                ->setPhone($phone)
                ->setPassword($encodedPass);

            if ($parentUser) {
                $user->setParentUser($parentUser);
            }

            $this
                ->service
                ->registerToPlan($user, $plan);

            return $this->redirectToRoute('frontend_subscribe_success', [
                'request' => $request,
            ], 307);
        }

        return $this->render('frontend/subscribe/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/success", name="success", methods={"POST"})
     */
    public function success(Request $request)
    {
        return $this->render('frontend/subscribe/success.html.twig');
    }

    /**
     * @Route("/confirmation", name="confirmation", methods={"GET"})
     */
    public function confirmation(Request $request, EntityManagerInterface $em)
    {
        $hash = $request->get('hash');
        $email = $request->get('email');

        /** @var User $entity */
        $entity = $em
            ->getRepository(User::class)
            ->findOneBy([
                'email' => $email,
            ]);

        if ($entity && !$entity->isConfirmed()) {
            $userHash = User::generateHash($entity);

            if ($userHash === $hash) {
                $entity->setConfirmedAt(new DateTime());

                $em->merge($entity);
                $em->flush();

                $this->addFlash('success', 'Your email has been successfully confirmed.');
            }
        }

        return $this->redirectToRoute('app_home');
    }
}
