<?php

namespace App\Service\Finance;

use App\Entity\Resource\Property;
use App\Repository\Sales\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * AgentPaymentsService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class AgentPaymentsService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ParameterBagInterface */
    private $params;

    /** @var AccountService */
    private $accountService;

    public function __construct(
        EntityManagerInterface $em,
        ParameterBagInterface $params,
        AccountService $accountService
    ) {
        $this->em = $em;
        $this->params = $params;
        $this->accountService = $accountService;
    }

    public function getTotalSalesByUser($user)
    {
        /** @var PropertyRepository */
        $propertyRepository = $this->em->getRepository(Property::class);
        $totalSales = $propertyRepository->getTotalSalesByUser($user);

        return $totalSales;
    }

    public function getTotalSalesByMonth($user, $month)
    {
        /** @var PropertyRepository */
        $propertyRepository = $this->em->getRepository(Property::class);
        $totalSalesByMonth = $propertyRepository->getTotalSalesByMonth($user, $month);

        return $totalSalesByMonth;
    }
}
