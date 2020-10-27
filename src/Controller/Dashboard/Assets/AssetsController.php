<?php

namespace App\Controller\Dashboard\Assets;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Person\Agent;
use App\Entity\Resource\Asset as Entity;
use App\Form\Dashboard\Asset\AssetType;
use App\Form\Dashboard\Asset\SearchAssetType;
use App\Form\Dashboard\RemoveResourceFormType;
use App\Repository\Asset\AssetRepository;
use App\Service\Asset\AssetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/dashboard/assets/assets",
 *  name="dashboard_assets_assets_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class AssetsController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchAssetType::class)
            ->getForm();

        return $this->render('dashboard/assets/assets/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, AssetRepository $repository)
    {
        $columns = [
            'e.name',
            'e.price',
            'e.lastEquity',
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
                    ->andWhere('e.assetStatus IN (:status)')
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
    public function add(Request $request, AssetService $service, EntityManagerInterface $em)
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
     * @Route("/{id}", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AssetService $service, Entity $entity)
    {
        return $this->form($request, $service, $entity);
    }

    private function form(Request $request, AssetService $service, Entity $entity)
    {
        $removeForm = null;
        $errors = null;

        $form = $this
            ->createForm(AssetType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->save($entity);

                $this->addFlash('success', 'Success!');

                return $this->redirectToRoute('dashboard_assets_assets_edit', [
                    'id' => $entity->getId(),
                ]);
            } catch (Throwable $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        if ($entity->getId()) {
            $removeForm = $this->createForm(RemoveResourceFormType::class, null, [
                'method' => 'DELETE',
                'action' => $this->generateUrl('dashboard_assets_assets_remove', [
                    'id' => $entity->getId(),
                ]),
            ]);
        }

        return $this->render('dashboard/assets/assets/form.html.twig', [
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
        return $this->render('dashboard/assets/assets/finance.html.twig', [
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

            return $this->redirectToRoute('dashboard_assets_assets_index');
        }
    }
}
