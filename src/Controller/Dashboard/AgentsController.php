<?php

namespace App\Controller\Dashboard;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Person\Agent as Entity;
use App\Form\Dashboard\AgentType;
use App\Form\Dashboard\SearchContractorType;
use App\Repository\Person\AgentRepository;
use App\Service\AgentService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/dashboard/agents",
 *  name="dashboard_agents_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class AgentsController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchContractorType::class)
            ->getForm();

        return $this->render('dashboard/agents/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, AgentRepository $repository)
    {
        $columns = [
            'e.id',
            'u.name',
            'u.email',
        ];

        $search = $this->searchValues($request);
        $qb = $repository
            ->createQueryBuilder('e')
            ->join('e.user', 'u');

        $this->addOrderBy($request, $qb, $columns);

        if (isset($search['email'])) {
            $email = $search['email'];
            if (!empty($email)) {
                $qb
                    ->andWhere('LOWER(u.name) LIKE LOWER(:email)')
                    ->setParameter('email', $email);
            }
        }

        $query = $qb->getQuery();

        return $this->dataTable($request, $query, false, [
            'groups' => 'list',
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function add(Request $request, AgentService $service)
    {
        $entity = new Entity();

        return $this->form($request, $service, $entity);
    }

    /**
     * @Route("/{id}", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, AgentService $service, Entity $entity)
    {
        return $this->form($request, $service, $entity);
    }

    private function form(Request $request, AgentService $service, Entity $entity)
    {
        $errors = null;

        $form = $this
            ->createForm(AgentType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $entity->getUser()->addRole('ROLE_AGENT');
                $service->save($entity);

                $this->addFlash('success', 'Item saved successfully.');

                return $this->redirectToRoute('dashboard_agents_edit', [
                    'id' => $entity->getId(),
                ]);
            } catch (Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('dashboard/agents/form.html.twig', [
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
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'Item deleted successfully.');

            return $this->redirectToRoute('dashboard_agents_index');
        } catch (Throwable $ex) {
            $this->addFlash('error', 'This agent cannot be removed.');

            return $this->redirectToRoute('dashboard_agents_edit', [
                'id' => $entity->getId(),
            ]);
        }
    }
}
