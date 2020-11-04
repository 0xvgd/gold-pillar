<?php

namespace App\Controller\Dashboard;

use App\Entity\Finance\Commission;
use App\Entity\Finance\CommissionPayment;
use App\Entity\Finance\Dividend;
use App\Entity\Finance\ExpensePayment;
use App\Entity\Finance\IncomePayment;
use App\Entity\Finance\Investment;
use App\Entity\Finance\Payment;
use App\Entity\Finance\Recurring;
use App\Entity\Finance\RecurringExpense;
use App\Entity\Finance\RecurringIncome;
use App\Entity\Finance\Transaction;
use App\Entity\Resource\Resource;
use App\Service\Finance\PaymentsService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/dashboard/resources/finance",
 *  name="dashboard_resource_finance_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class ResourceFinanceController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/{id}", name="index", methods={"GET"})
     */
    public function index($id)
    {
        return $this->redirectToRoute('dashboard_index');
    }

    /**
     * @Route("/{id}/distribute-dividend", methods={"POST"})
     */
    public function distributeDividend(Request $request, PaymentsService $service, Resource $resource)
    {
        try {
            $service->distributeDividend($this->getUser(), $resource, new DateTime());

            $this->addFlash('success', 'Dividend paid successfuly');
        } catch (Throwable $ex) {
            $this->addFlash('error', $ex->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/{id}/distribute-profit", methods={"POST"})
     */
    public function distributeProfit(Request $request, PaymentsService $service, Resource $resource)
    {
        try {
            $service->distributeProfit($this->getUser(), $resource, new DateTime());

            $this->addFlash('success', 'Dividend paid successfuly');
        } catch (Throwable $ex) {
            $this->addFlash('error', $ex->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/{id}/distribute-commission", methods={"POST"})
     */
    public function distributeCommission(Request $request, PaymentsService $service, Resource $resource)
    {
        try {
            $service->distributeCommission($this->getUser(), $resource, new DateTime());

            $this->addFlash('success', 'Commission paid successfuly');
        } catch (Throwable $ex) {
            $this->addFlash('error', $ex->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    private function getEntitiesBy(Request $request, EntityManagerInterface $em, string $entityClass, array $criteria)
    {
        $maxResults = 15;
        $page = (int) $request->get('page');
        $page = max(1, $page);
        $offset = $maxResults * ($page - 1);

        $qb = $em
            ->createQueryBuilder()
            ->select('e')
            ->from($entityClass, 'e')
            ->setMaxResults($maxResults)
            ->setFirstResult($offset)
            ->addOrderBy('e.createdAt', 'DESC');

        foreach ($criteria as $name => $pair) {
            foreach ($pair as $stmt => $value) {
                $qb
                    ->andWhere($stmt)
                    ->setParameter($name, $value);
            }
        }

        $entities = $qb
            ->getQuery()
            ->getResult();

        return $this->json($entities, 200, [], [
            'groups' => ['read'],
        ]);
    }

    private function getEntitiesByResource(Request $request, EntityManagerInterface $em, Resource $resource, string $entityClass)
    {
        return $this->getEntitiesBy($request, $em, $entityClass, [
            'resource' => [
                'e.resource = :resource' => $resource,
            ],
        ]);
    }

    public function updateEntity(Request $request, Resource $resource, $entity, callable $fn = null)
    {
        $body = $request->getContent();
        $entity = $this->serializer->deserialize($body, get_class($entity), 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $entity,
            'groups' => ['write'],
        ]);
        $entity->setResource($resource);
        $errors = $this->validator->validate($entity);

        if (0 === count($errors)) {
            $status = $entity->getId() ? 200 : 201;

            if (is_callable($fn)) {
                $fn($entity);
            }

            return $this->json($entity, $status, [], [
                'groups' => ['read'],
            ]);
        }

        return $this->json($errors, 400);
    }

    /**
     * @Route("/{id}/account", methods={"GET"})
     */
    public function account(Request $request, EntityManagerInterface $em, Resource $resource)
    {
        return $this->json($resource->getAccount(), 200, [], [
            'groups' => ['read'],
        ]);
    }

    /**
     * @Route("/{id}/investments", methods={"GET"})
     */
    public function investments(Request $request, EntityManagerInterface $em, Resource $resource)
    {
        return $this->getEntitiesByResource($request, $em, $resource, Investment::class);
    }

    /**
     * @Route("/{id}/commission/payments", methods={"GET"})
     */
    public function commissions(Request $request, EntityManagerInterface $em, Resource $resource)
    {
        return $this->getEntitiesByResource($request, $em, $resource, CommissionPayment::class);
    }

    /**
     * @Route("/{id}/transactions", methods={"GET"})
     */
    public function transactions(Request $request, EntityManagerInterface $em, Resource $resource)
    {
        return $this->getEntitiesBy($request, $em, Transaction::class, [
            'account' => [
                'e.account = :account' => $resource->getAccount(),
            ],
        ]);
    }

    /**
     * @Route("/{id}/dividends", methods={"GET"})
     */
    public function dividends(Request $request, EntityManagerInterface $em, Resource $resource)
    {
        return $this->getEntitiesByResource($request, $em, $resource, Dividend::class);
    }

    /**
     * @Route("/{id}/recurring-income", methods={"GET"})
     */
    public function getRecurringIncomes(Request $request, EntityManagerInterface $em, Resource $resource)
    {
        return $this->getEntitiesByResource($request, $em, $resource, RecurringIncome::class);
    }

    /**
     * @Route("/{resource_id}/recurring-income", methods={"POST"})
     * @Route("/{resource_id}/recurring-income/{recurring_id}", methods={"POST"})
     * @ParamConverter("resource", options={"id" = "resource_id"})
     * @ParamConverter("entity", options={"id" = "recurring_id"})
     */
    public function saveRecurringIncome(Request $request, PaymentsService $service, Resource $resource, RecurringIncome $entity = null)
    {
        if (!$entity) {
            $entity = new RecurringIncome($resource);
        }

        return $this->updateEntity($request, $resource, $entity, function (Recurring $entity) use ($resource, $service) {
            $service->saveRecurringIncome($resource, $entity);
        });
    }

    /**
     * @Route("/{resource_id}/recurring-income/{recurring_id}", methods={"GET"})
     * @ParamConverter("resource", options={"id" = "resource_id"})
     * @ParamConverter("entity", options={"id" = "recurring_id"})
     */
    public function getRecurringIncome(Request $request, Resource $resource, RecurringIncome $entity)
    {
        return $this->json($entity, 200, [], [
            'groups' => ['read'],
        ]);
    }

    /**
     * @Route("/{resource_id}/recurring-income/{recurring_id}", methods={"DELETE"})
     * @ParamConverter("resource", options={"id" = "resource_id"})
     * @ParamConverter("entity", options={"id" = "recurring_id"})
     */
    public function deleteRecurringIncome(Request $request, PaymentsService $service, Resource $resource, RecurringIncome $entity)
    {
        $service->removeRecurringIncome($resource, $entity);

        return $this->json($entity);
    }

    /**
     * @Route("/{id}/recurring-expense", methods={"GET"})
     */
    public function getRecurringExpenses(Request $request, EntityManagerInterface $em, Resource $resource)
    {
        return $this->getEntitiesByResource($request, $em, $resource, RecurringExpense::class);
    }

    /**
     * @Route("/{resource_id}/recurring-expense", methods={"POST"})
     * @Route("/{resource_id}/recurring-expense/{recurring_id}", methods={"POST"})
     * @ParamConverter("resource", options={"id" = "resource_id"})
     * @ParamConverter("entity", options={"id" = "recurring_id"})
     */
    public function saveRecurringExpense(Request $request, PaymentsService $service, Resource $resource, RecurringExpense $entity = null)
    {
        if (!$entity) {
            $entity = new RecurringExpense($resource);
        }

        return $this->updateEntity($request, $resource, $entity, function (Recurring $entity) use ($resource, $service) {
            $service->saveRecurringExpense($resource, $entity);
        });
    }

    /**
     * @Route("/{resource_id}/recurring-expense/{recurring_id}", methods={"GET"})
     * @ParamConverter("resource", options={"id" = "resource_id"})
     * @ParamConverter("entity", options={"id" = "recurring_id"})
     */
    public function getRecurringExpense(Request $request, Resource $resource, RecurringExpense $entity)
    {
        return $this->json($entity, 200, [], [
            'groups' => ['read'],
        ]);
    }

    /**
     * @Route("/{resource_id}/recurring-expense/{recurring_id}", methods={"DELETE"})
     * @ParamConverter("resource", options={"id" = "resource_id"})
     * @ParamConverter("entity", options={"id" = "recurring_id"})
     */
    public function deleteRecurringExpense(Request $request, PaymentsService $service, Resource $resource, RecurringExpense $entity)
    {
        $service->removeRecurringExpense($resource, $entity);

        return $this->json($entity);
    }

    /**
     * @Route("/{id}/income", methods={"GET"})
     */
    public function getIncomes(Request $request, EntityManagerInterface $em, Resource $resource)
    {
        return $this->getEntitiesByResource($request, $em, $resource, IncomePayment::class);
    }

    /**
     * @Route("/{id}/income", methods={"POST"})
     */
    public function newIncome(Request $request, PaymentsService $paymentService, Resource $resource)
    {
        return $this->updateEntity($request, $resource, new IncomePayment($resource), function (Payment $entity) use ($paymentService) {
            $user = $this->getUser();
            $paymentService->addIncome($user, $entity, true);
        });
    }

    /**
     * @Route("/{id}/expense", methods={"GET"})
     */
    public function getExpenses(Request $request, EntityManagerInterface $em, Resource $resource)
    {
        return $this->getEntitiesByResource($request, $em, $resource, ExpensePayment::class);
    }

    /**
     * @Route("/{id}/expense", methods={"POST"})
     */
    public function newExpense(Request $request, PaymentsService $paymentService, Resource $resource)
    {
        return $this->updateEntity($request, $resource, new ExpensePayment($resource), function (Payment $entity) use ($paymentService) {
            $user = $this->getUser();
            $paymentService->addExpense($user, $entity, true);
        });
    }
}
