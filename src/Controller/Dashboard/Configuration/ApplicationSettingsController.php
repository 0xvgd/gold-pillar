<?php

namespace App\Controller\Dashboard\Configuration;

use App\Entity\ApplicationSettings;
use App\Entity\ApplicationSettings as Entity;
use App\Entity\Helper\LogChanges;
use App\Form\Dashboard\ApplicationSettingsType;
use App\Service\ApplicationSettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/dashboard/config/application",
 *  name="application_config_application_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class ApplicationSettingsController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(
        Request $request,
        ApplicationSettingsService $applicationSettingsService,
        Entity $entity = null
    ) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(ApplicationSettings::class)->findOneBy([
        ]);

        $tab = $request->get('tab');
        $errors = null;

        if (!$entity) {
            $entity = new Entity();
        }

        $form = $this
            ->createForm(ApplicationSettingsType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $applicationSettingsService->save($entity);

                $this->addFlash('success', 'Item saved successfully.');

                return $this->redirectToRoute('application_config_application_index');
            } catch (Throwable $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        $logChanges = $em->getRepository(LogChanges::class)->findBy(
            ['className' => Entity::class],
            ['createdAt' => 'desc']
        );

        return $this->render('dashboard/configuration/application/form.html.twig', [
            'entity' => $entity,
            'errors' => $errors,
            'logChanges' => $logChanges,
            'form' => $form->createView(),
            'tab' => $tab,
        ]);
    }
}
