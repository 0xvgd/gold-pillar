<?php

namespace App\Controller\Dashboard;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Person\Engineer as Entity;
use App\Form\Dashboard\EngineerType;
use App\Form\Dashboard\SearchEngineerType;
use App\Service\EngineerService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/engineers",
 *  name="dashboard_engineers_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class EngineersController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $form = $this->createFormBuilder()
            ->create('', SearchEngineerType::class)
            ->getForm();

        return $this->render('dashboard/engineers/index.html.twig', [
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
            ->from(Entity::class, 'e')
            ->join('e.user', 'u');

        if (isset($search['basic'])) {
            parse_str($search['basic'], $basic);

            $email = $basic['email'];

            if (!empty($email)) {
                $qb->andWhere('LOWER(u.email) LIKE :arg');
                $params['arg'] = "%{$email}%";
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
        EngineerService $engineerService,
        Entity $entity = null
    ) {
        $errors = null;

        if (!$entity) {
            $entity = new Entity();
        }

        $form = $this->createForm(EngineerType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $engineerService->save($entity);

                $this->addFlash('success', 'Item saved successfully.');

                return $this->redirectToRoute('dashboard_engineers_index');
            } catch (Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('dashboard/engineers/form.html.twig', [
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

        return $this->redirectToRoute('dashboard_engineers_index');
    }
}
