<?php

namespace App\Controller\Dashboard;

use App\Entity\Finance\Commission;
use App\Entity\Finance\CommissionPayment;
use App\Entity\Finance\Dividend;
use App\Entity\Finance\ExpensePayment;
use App\Entity\Finance\IncomePayment;
use App\Entity\Finance\Investment;
use App\Entity\Finance\Recurring;
use App\Entity\Finance\RecurringIncome;
use App\Entity\Finance\Transaction;
use App\Entity\Resource\Resource;
use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(
 *  "/{_locale}/dashboard/user/finance",
 *  name="dashboard_user_finance_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class UserFinanceController extends AbstractController
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

    private function getEntitiesByUser(Request $request, EntityManagerInterface $em, User $user, string $entityClass)
    {
        return $this->getEntitiesBy($request, $em, $entityClass, [
            'resource' => [
                'e.user = :user' => $user,
            ],
        ]);
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
    public function investments(Request $request, EntityManagerInterface $em, User $user)
    {
        return $this->getEntitiesByUser($request, $em, $user, Investment::class);
    }

    /**
     * @Route("/{id}/commission/payments", methods={"GET"})
     */
    public function commissions(Request $request, EntityManagerInterface $em, User $user)
    {
        return $this->getEntitiesByUser($request, $em, $user, CommissionPayment::class);
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
    public function dividends(Request $request, EntityManagerInterface $em, User $user)
    {
        return $this->getEntitiesByUser($request, $em, $user, Dividend::class);
    }

    /**
     * @Route("/{id}/recurring-income", methods={"GET"})
     */
    public function getRecurringIncomes(Request $request, EntityManagerInterface $em, User $user)
    {
        return $this->getEntitiesByUser($request, $em, $user, RecurringIncome::class);
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
     * @Route("/{id}/income", methods={"GET"})
     */
    public function getIncomes(Request $request, EntityManagerInterface $em, User $user)
    {
        return $this->getEntitiesByUser($request, $em, $user, IncomePayment::class);
    }

    /**
     * @Route("/{id}/expense", methods={"GET"})
     */
    public function getExpenses(Request $request, EntityManagerInterface $em, User $user)
    {
        return $this->getEntitiesByUser($request, $em, $user, ExpensePayment::class);
    }
}
