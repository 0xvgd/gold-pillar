<?php

namespace App\Controller\Dashboard;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Translation\Locale;
use App\Entity\Translation\Locale as Entity;
use App\Entity\Translation\Translation;
use App\Form\Dashboard\LocaleType as EntityType;
use App\Form\Dashboard\SearchLocaleType;
use App\Repository\Translation\LocaleRepository;
use App\Service\TranslationService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/config/locales",
 *  name="dashboard_config_locales_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class LocalesController extends AbstractController
{
    use DatatableTrait;

    private $translationService;
    private $kernel;

    public function __construct(TranslationService $translationService, KernelInterface $kernel)
    {
        $this->translationService = $translationService;
        $this->kernel = $kernel;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchLocaleType::class)
            ->getForm();

        return $this->render('dashboard/configuration/locales/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, LocaleRepository $repository)
    {
        $search = $request->get('search');

        if (!is_array($search)) {
            $search = [
                'basic' => [],
            ];
        }

        $qb = $repository->createQueryBuilder('e');

        if (isset($search['basic'])) {
            parse_str($search['basic'], $basic);
            $title = $basic['title'];
            if (!empty($title)) {
                $qb
                    ->andWhere('LOWER(e.code) LIKE :arg')
                    ->setParameter('arg', "%{$title}%");
            }
        }

        $query = $qb
            ->getQuery();

        return $this->dataTable($request, $query, false, [
                'groups' => 'list',
            ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function add(Request $request)
    {
        $entity = new Entity();

        $defaultLocale = $this->getDoctrine()
            ->getManager()
            ->getRepository(Locale::class)->findOneBy([
                'defaultLocale' => true,
            ]);

        if ($defaultLocale) {
            foreach ($defaultLocale->getTranslations() as $tans) {
                $translation = new Translation();
                $translation->setBase($tans);
                $translation->setSource($tans->getSource());
                $translation->setTarget('');
                $entity->addTranslation($translation);
            }
        }

        return $this->form($request, $entity);
    }

    /**
     * @Route("/{id}", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Entity $entity)
    {
        return $this->form($request, $entity);
    }

    private function form(Request $request, Entity $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $errors = null;

        $originalTranslations = clone $entity->getTranslations();
        $form = $this
            ->createForm(EntityType::class, $entity)
            ->handleRequest($request);
        $current = clone $entity->getTranslations();

        if ($form->isSubmitted() && $form->isValid()) {
            $removed = array_udiff(
                $originalTranslations->toArray(),
                $current->toArray(),
                function ($obj_a, $obj_b) {
                    return strcmp($obj_a->getId(), $obj_b->getId());
                }
            );

            foreach ($removed as $base) {
                $this->translationService->removeTranslationsByBase($base);
            }

            try {
                if ($entity->getDefaultLocale()) {
                    foreach ($entity->getTranslations() as $tans) {
                        if (!$tans->getId()) {
                            $translation = new Translation();
                            $translation->setBase($tans);
                            $translation->setSource($tans->getSource());
                            $translation->setTarget('');

                            $this->addToAnotherLocales($translation);
                        }
                    }
                }

                $em->persist($entity);
                $em->flush();

                $env = $this->kernel->getEnvironment();

                $process = new Process([
                    'command' => 'bin/console cache:clear',
                    '--env' => $env,
                ]);
                $process->run();

                $this->addFlash('success', 'Locale saved successfully.');

                return $this->redirectToRoute('dashboard_config_locales_edit', [
                    'id' => $entity->getId(),
                ]);
            } catch (Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('dashboard/configuration/locales/form.html.twig', [
            'entity' => $entity,
            'errors' => $errors,
            'form' => $form->createView(),
        ]);
    }

    private function doCommand($command)
    {
        $env = $this->kernel->getEnvironment();

        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => $command,
            '--env' => $env,
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);
    }

    private function addToAnotherLocales($translation)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $translation = clone $translation;
        $locales = $em->getRepository(Locale::class)->findBy([
            'defaultLocale' => false,
        ]);

        foreach ($locales as $locale) {
            $trans = clone $translation;
            $locale->addTranslation($trans);
            $em->persist($locale);
        }
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Entity $entity)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($entity);
        $em->flush();

        $this->addFlash('success', 'Item deleted successfully.');

        return $this->redirectToRoute('dashboard_config_locales_index');
    }

    /**
     * @Route("/apply/{id}", name="apply", methods={"GET", "POST"})
     */
    public function clearCache(Request $request, Entity $entity)
    {
        $this->doCommand('cache:clear');

        $this->addFlash('success', 'Changes successfully applied.');

        return $this->redirectToRoute('dashboard_config_locales_edit', [
            'id' => $entity->getId(),
        ]);
    }
}
