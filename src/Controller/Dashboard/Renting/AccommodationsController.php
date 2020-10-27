<?php

namespace App\Controller\Dashboard\Renting;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Resource\Accommodation as Entity;
use App\Enum\PropertyStatus;
use App\Enum\RemovalReason;
use App\Form\Dashboard\RemoveResourceFormType;
use App\Form\Dashboard\Renting\AccommodationType;
use App\Form\Dashboard\SearchAccommodationType;
use App\Repository\Renting\AccommodationRepository;
use App\Service\Finance\AgentPaymentsService;
use App\Service\Renting\RentingService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/dashboard/renting/accommodations",
 *  name="dashboard_renting_accommodations_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class AccommodationsController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchAccommodationType::class)
            ->getForm();

        return $this->render('dashboard/renting/accommodations/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, AccommodationRepository $repository, EntityManagerInterface $em)
    {
        $columns = [
            'e.name',
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
                    ->andWhere('e.status IN (:status)')
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
    public function add(Request $request, RentingService $service)
    {
        $user = $this->getUser();
        $entity = new Entity();
        $entity->setOwner($user);

        return $this->form($request, $service, $entity);
    }

    /**
     * @Route("/{id}", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, RentingService $service, Entity $entity)
    {
        return $this->form($request, $service, $entity);
    }

    private function form(Request $request, RentingService $service, Entity $entity)
    {
        $removeForm = null;
        $errors = null;

        if ($entity->getId()) {
            $removeForm = $this
                ->createForm(RemoveResourceFormType::class, null, [
                    'method' => 'DELETE',
                    'action' => $this->generateUrl('dashboard_sales_properties_remove', [
                        'id' => $entity->getId(),
                    ]),
                ]);
        }

        $form = $this
            ->createForm(AccommodationType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->save($entity);

                $this->addFlash('success', 'Success!');

                return $this->redirectToRoute('dashboard_renting_accommodations_edit', [
                    'id' => $entity->getId(),
                ]);
            } catch (Throwable $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('dashboard/renting/accommodations/form.html.twig', [
            'entity' => $entity,
            'errors' => $errors,
            'form' => $form->createView(),
            'removeForm' => $removeForm ? $removeForm->createView() : null,
        ]);
    }

    /**
     * @Route("/{id}", name="remove", methods={"DELETE"})
     */
    public function remove(Request $request, Entity $entity, AgentPaymentsService $agentPaymentsService)
    {
        $form = $this
            ->createForm(RemoveResourceFormType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $propertyStatus = null;
            $entity->setRemovedAt(new DateTime('now'));

            if ($entity->getRemovalReason()->equals(RemovalReason::MADE_THE_SALE())) {
                $propertyStatus = PropertyStatus::SOLD();
                $agentPaymentsService->commissionGenerate($entity);
            } elseif ($entity->getRemovalReason()->equals(RemovalReason::WONT_SELL_ANYMORE())) {
                $propertyStatus = PropertyStatus::REMOVED();
            }

            $entity->setPropertyStatus($propertyStatus);

            $em = $this->getDoctrine()->getManager();
            $em->merge($entity);
            $em->flush();

            $this->addFlash('success', 'Success!');

            return $this->redirectToRoute('dashboard_renting_accommodations_index');
        }
    }
}
