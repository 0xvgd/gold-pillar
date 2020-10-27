<?php

namespace App\Controller\Dashboard\Profile;

use App\Entity\Security\User;
use App\Utils\FileUploadManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/profile/account",
 *  name="dashboard_profile_account_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('dashboard/profile/account/index.html.twig', [
        ]);
    }

    /**
     * @Route("/avatar", name="avatar", methods={"POST"})
     */
    public function avatarUpload(
        Request $request,
        FileUploadManager $uploader,
        EntityManagerInterface $em
    ) {
        /** @var User */
        $user = $this->getUser();
        $url = null;
        /** @var UploadedFile */
        $file = $request->files->get('file');
        $resp = [];

        if ($file instanceof UploadedFile) {
            $resp = $uploader->send($file->getRealPath(), $file->getClientOriginalName());
        }

        if (200 == $resp['status']) {
            $file = $resp['response']['files'][0];
            $url = $file['url'];
            $user->setAvatar($url);
            $em->merge($user);
            $em->flush();
        } else {
            throw new Exception($resp['error'], $resp['code']);
        }

        return $this->json([
            'status' => $resp['status'],
            'url' => $url,
        ]);
    }
}
