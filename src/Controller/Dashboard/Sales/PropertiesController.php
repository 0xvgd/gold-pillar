<?php

namespace App\Controller\Dashboard\Sales;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Person\Agent;
use App\Entity\Resource\Property as Entity;
use App\Enum\PropertyStatus;
use App\Enum\RemovalReason;
use App\Form\Dashboard\RemoveResourceFormType;
use App\Form\Dashboard\Sales\PropertyType;
use App\Form\Dashboard\SearchPropertyType;
use App\Repository\Sales\PropertyRepository;
use App\Service\Finance\AgentPaymentsService;
use App\Service\Property\PropertyService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/sales/properties",
 *  name="dashboard_sales_properties_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class PropertiesController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        $form = $this->createFormBuilder()
            ->create('', SearchPropertyType::class)
            ->getForm();

        return $this->render('dashboard/sales/properties/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, PropertyRepository $repository)
    {
        $columns = ['e.name', 'e.price', 'e.hits', 'e.owner', 'e.postStatus'];

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
                    ->andWhere('e.propertyStatus IN (:status)')
                    ->setParameter('status', $status);
            }
        }

        if (!$this->isGranted('ROLE_ADMIN') && $user) {
            $qb->andWhere('e.owner = :user')->setParameter('user', $user);
        }

        $query = $qb->getQuery();

        return $this->dataTable($request, $query, false, [
            'groups' => 'list',
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function add(
        Request $request,
        PropertyService $service,
        EntityManagerInterface $em
    ) {
        $user = $this->getUser();
        $entity = new Entity();
        $entity->setOwner($user);

        //comision_rate
        $entity->setCommissionRate(0.0491);

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
    public function edit(
        Request $request,
        PropertyService $service,
        Entity $entity
    ) {
        return $this->form($request, $service, $entity);
    }

    private function form(
        Request $request,
        PropertyService $service,
        Entity $entity
    ) {
        $removeForm = null;
        $errors = null;

        if ($entity->getId()) {
            $removeForm = $this->createForm(
                RemoveResourceFormType::class,
                null,
                [
                    'method' => 'DELETE',
                    'action' => $this->generateUrl(
                        'dashboard_sales_properties_remove',
                        [
                            'id' => $entity->getId(),
                        ]
                    ),
                ]
            );
        }

        $form = $this->createForm(PropertyType::class, $entity)->handleRequest(
            $request
        );

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->save($entity);

                $this->addFlash('success', 'Success!');

                return $this->redirectToRoute(
                    'dashboard_sales_properties_edit',
                    [
                        'id' => $entity->getId(),
                    ]
                );
            } catch (Throwable $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('dashboard/sales/properties/form.html.twig', [
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
        return $this->render('dashboard/sales/properties/finance.html.twig', [
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/{id}", name="remove", methods={"DELETE"})
     */
    public function remove(
        Request $request,
        AgentPaymentsService $agentPaymentsService,
        Entity $entity
    ) {
        $form = $this->createForm(RemoveResourceFormType::class, $entity, [
            'method' => 'DELETE',
        ])->handleRequest($request);

        $removalReason = $request->request->get('remove_resource_form')[
            'removalReason'
        ];
        $removalDetails = $request->request->get('remove_resource_form')[
            'removalDetails'
        ];
        if (!$removalDetails) {
            $this->addFlash('error', 'could not delete record!');

            return $this->redirectToRoute('dashboard_sales_properties_edit', [
                'id' => $entity->getId(),
            ]);
        } else {
            $em = $this->getDoctrine()->getManager();
            $propertyStatus = null;
            $entity->setRemovedAt(new DateTime('now'));

            if ($entity
                    ->getRemovalReason()
                    ->equals(RemovalReason::MADE_THE_SALE())
            ) {
                $propertyStatus = PropertyStatus::SOLD();

                $agentPaymentsService->commissionGenerate($entity);
            } elseif ($entity
                    ->getRemovalReason()
                    ->equals(RemovalReason::WONT_SELL_ANYMORE())
            ) {
                $propertyStatus = PropertyStatus::REMOVED();
            }

            $entity->setPropertyStatus($propertyStatus);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);

            $em->flush();

            $this->addFlash('success', 'Success!');

            return $this->redirectToRoute('dashboard_sales_properties_index');
        }
    }
}
