<?php

namespace App\Controller\Dashboard\Project;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Person\Agent;
use App\Entity\Resource\Project as Entity;
use App\Form\Dashboard\Project\ProjectType;
use App\Form\Dashboard\Project\SearchProjectType;
use App\Form\Dashboard\RemoveResourceFormType;
use App\Repository\Project\ProjectRepository;
use App\Service\Project\ProjectService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/dashboard/project/projects",
 *  name="dashboard_projects_projects_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class ProjectsController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchProjectType::class)
            ->getForm();

        return $this->render('dashboard/project/projects/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, ProjectRepository $repository)
    {
        $columns = [
            'e.name',
            'e.price',
            'e.hits',
            'e.owner',
            'e.postStatus',
        ];

        $user = $this->getUser();
        $search = $this->searchValues($request);
        $qb = $repository->createQueryBuilder('e');

        $this->addOrderBy($request, $qb, $columns);

        if (isset($search['name'])) {
            $name = $search['name'];
            if (!empty($name)) {
                $qb
                    ->andWhere('LOWER(e.name) LIKE LOWER(:name)')
                    ->setParameter('name', "%{$name}%");
            }
        }

        if (isset($search['status'])) {
            $status = $search['status'];
            if (!empty($status)) {
                $qb
                    ->andWhere('e.projectStatus IN (:status)')
                    ->setParameter('status', $status);
            }
        }

        if (!$this->isGranted('ROLE_ADMIN') && $user) {
            $qb
                ->andWhere('e.owner = :user')
                ->setParameter('user', $user);
        }

        $query = $qb->getQuery();

        return $this->dataTable($request, $query, false, [
            'groups' => 'list',
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function add(Request $request, ProjectService $service, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $entity = new Entity();
        $entity->setOwner($user);

        if ($this->isGranted('ROLE_AGENT')) {
            $agent = $em->getRepository(Agent::class)->findOneBy([
                'user' => $user,
            ]);

            $entity->setAgent($agent);
        }

        return $this->form($request, $service, $entity);
    }

    /**
     * @Route("/{id}", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, ProjectService $service, Entity $entity)
    {
        return $this->form($request, $service, $entity);
    }

    private function form(Request $request, ProjectService $service, Entity $entity)
    {
        $removeForm = null;
        $errors = null;

        if ($entity->getId()) {
            $removeForm = $this->createForm(RemoveResourceFormType::class, null, [
                'method' => 'DELETE',
                'action' => $this->generateUrl('dashboard_projects_projects_remove', [
                    'id' => $entity->getId(),
                ]),
            ]);
        }

        $form = $this
            ->createForm(ProjectType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->save($entity);

                $this->addFlash('success', 'Success!');

                return $this->redirectToRoute('dashboard_projects_projects_edit', [
                    'id' => $entity->getId(),
                ]);
            } catch (Throwable $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('dashboard/project/projects/form.html.twig', [
            'entity' => $entity,
            'errors' => $errors,
            'form' => $form->createView(),
            'removeForm' => $removeForm ? $removeForm->createView() : null,
        ]);
    }

    /**
     * @Route("/{id}/finance", name="finance", methods={"GET", "POST"})
     */
    public function finance(Request $request, Entity $entity)
    {
        return $this->render('dashboard/project/projects/finance.html.twig', [
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/{id}", name="remove", methods={"DELETE"})
     */
    public function remove(Request $request, Entity $entity)
    {
        $form = $this
            ->createForm(RemoveResourceFormType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //TODO
            $this->addFlash('success', 'Success!');

            return $this->redirectToRoute('dashboard_projects_projects_index');
        }
    }
}
