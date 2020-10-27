<?php

namespace App\Controller\Dashboard;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Page as Entity;
use App\Form\Dashboard\PageType as EntityType;
use App\Form\Dashboard\SearchTableKeyType;
use App\Repository\PageRepository;
use Cocur\Slugify\Slugify;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/config/pages",
 *  name="dashboard_config_pages_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class PagesController extends AbstractController
{
    use DatatableTrait;

    public function __construct()
    {
    }

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchTableKeyType::class)
            ->getForm();

        return $this->render('dashboard/configuration/pages/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, PageRepository $repository)
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
                    ->andWhere('LOWER(e.title) LIKE :arg')
                    ->setParameter('arg', "%{$title}%");
            }
        }

        $query = $qb
            ->getQuery();

        return $this->dataTable($request, $query, false);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function add(Request $request)
    {
        $entity = new Entity();

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
        $form = $this
            ->createForm(EntityType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $slugfy = new Slugify();
                $slug = $slugfy->slugify($entity->getName());
                $entity->setName($slug);

                $em->persist($entity);
                $em->flush();

                $this->addFlash('success', 'Page saved successfully.');

                return $this->redirectToRoute('dashboard_config_pages_edit', [
                    'id' => $entity->getId(),
                ]);
            } catch (Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('dashboard/configuration/pages/form.html.twig', [
            'entity' => $entity,
            'errors' => $errors,
            'form' => $form->createView(),
        ]);
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

        return $this->redirectToRoute('dashboard_config_pages_index');
    }
}
