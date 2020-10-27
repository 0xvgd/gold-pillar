<?php

namespace App\Controller\Frontend;

use App\Form\ForgotPasswordType;
use App\Form\NewPasswordType;
use App\Service\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route(
 *  "/{_locale}/frontend/forgot/password",
 *  name="frontend_forgot_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class ForgotPasswordController extends AbstractController
{
    const MSG_ERROR_UNKNOWN_ENTITY = 'Register not found.';

    /**
     * @Route("/new", name="password_new")
     */
    public function new(Request $request, UserService $userService)
    {
        $form = $this->createForm(ForgotPasswordType::class, null, [
            'action' => $this->generateUrl('frontend_forgot_password_new'),
            'method' => 'post',
        ]);

        if ($request->isMethod('post')) {
            try {
                $form->handleRequest($request);
                $email = $form->get('email')->getData();

                $userService->recoveryPassword($email);

                return $this->redirectToRoute('frontend_forgot_password_sent', [
                    'request' => $request,
                ], 307);
            } catch (Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/forgot_password/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/sent", name="password_sent")
     */
    public function sent(Request $request)
    {
        $form = $this->createForm(ForgotPasswordType::class, null, [
            'action' => $this->generateUrl('frontend_forgot_password_new'),
            'method' => 'post',
        ]);

        $form->handleRequest($request);
        $email = $form->get('email')->getData();

        //if ($request->isMethod('post')) {
        return $this->render('frontend/forgot_password/sent.html.twig', [
            'email' => $email,
        ]);
        //  } else {
        //      return $this->redirectToRoute('app_home');
        // }
    }

    /**
     * @Route("/new-password/{hash}", name="password_new_password")
     */
    public function newPassword(
        Request $request,
        UserService $userService,
        UserPasswordEncoderInterface $encoder,
        $hash = null
    ) {
        $form = $this->createForm(NewPasswordType::class, null, [
            'action' => $this->generateUrl('frontend_forgot_password_new_password'),
            'method' => 'post',
            'hash' => $hash,
        ]);

        $user = $userService->getByUserHash($hash);
        $email = null;

        if ($user) {
            $email = $user->getEmail();
        }

        if ($request->isMethod('post')) {
            try {
                $form->handleRequest($request);
                $hash = $form->get('hash')->getData();

                $password = $form->get('plainPassword')->getData();

                if ($hash) {
                    $user = $userService->getByUserHash($hash);
                    if ($form->isValid() && $hash) {
                        if ($password) {
                            $user = $userService->getByUserHash($hash);
                            if ($user) {
                                $pass = $encoder->encodePassword($user, $password);
                                $userService->updatePasswordByUserHash($hash, $pass);
                                $this->addFlash('success', 'Your Password has been updated successfully.');

                                return $this->redirectToRoute('dashboard_login');
                            } else {
                                throw new Exception(self::MSG_ERROR_UNKNOWN_ENTITY);
                            }
                        }
                    }
                } else {
                    throw new Exception(self::MSG_ERROR_UNKNOWN_ENTITY);
                }
            } catch (Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/forgot_password/new_password.html.twig', [
            'form' => $form->createView(),
            'email' => $email,
        ]);
    }
}
