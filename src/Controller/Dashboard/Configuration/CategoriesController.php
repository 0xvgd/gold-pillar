<?php

namespace App\Controller\Dashboard\Configuration;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Helper\TableKey as Entity;
use App\Form\Dashboard\SearchTableKeyType;
use App\Form\Dashboard\TableKeyType;
use App\Service\TableKeyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/dashboard/config/categories",
 *  name="dashboard_config_categories_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class CategoriesController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        $form = $this->createFormBuilder()
            ->create('', SearchTableKeyType::class)
            ->getForm();

        return $this->render('dashboard/configuration/categories/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        $params = [];

        if (!is_array($search)) {
            $search = [
                'basic' => [],
            ];
        }
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder()
            ->select('e')
            ->from(Entity::class, 'e');

        if (isset($search['basic'])) {
            parse_str($search['basic'], $basic);

            $description = $basic['description'];

            if (!empty($description)) {
                $qb->andWhere('LOWER(e.description) LIKE :arg');
                $params['arg'] = "%{$description}%";
            }
        }

        $query = $qb
            ->setParameters($params)
            ->getQuery();

        return $this->dataTable($request, $query, false);
    }

    /**
     * @Route("/edit", name="new", methods={"GET", "POST"})
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     */
    public function edit(
        Request $request,
        TableKeyService $tableKeyService,
        Entity $entity = null
    ) {
        $errors = null;

        if (!$entity) {
            $entity = new Entity();
        }

        $form = $this->createForm(TableKeyType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $tableKeyService->save($entity);

                $this->addFlash('success', 'Item saved successfully.');

                return $this->redirectToRoute('dashboard_config_categories_index');
            } catch (Throwable $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('dashboard/configuration/categories/form.html.twig', [
            'entity' => $entity,
            'errors' => $errors,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function remove(Request $request, Entity $entity)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($entity);
        $em->flush();

        $this->addFlash('success', 'Item deleted successfully.');

        return $this->redirectToRoute('dashboard_config_categories_index');
    }
}
