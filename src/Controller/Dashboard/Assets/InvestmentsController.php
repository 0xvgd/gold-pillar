<?php

namespace App\Controller\Dashboard\Assets;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Finance\Investment;
use App\Entity\Finance\Transaction;
use App\Entity\Resource\Asset;
use App\Enum\InvestmentStatus;
use App\Form\Dashboard\Asset\SearchTransactionType;
use App\Service\Finance\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/asset/investments",
 *  name="dashboard_assets_investments_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class InvestmentsController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchTransactionType::class)
            ->getForm();

        return $this->render('dashboard/assets/investments/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, EntityManagerInterface $em)
    {
        $search = $request->get('search');

        if (!is_array($search)) {
            $search = [
                'basic' => [],
            ];
        }

        $search = $this->searchValues($request);

        $qb = $em
            ->createQueryBuilder()
            ->select([
                'e',
            ])
            ->from(Investment::class, 'e')
            ->join('e.resource', 'r')
            ->where(sprintf('r INSTANCE OF %s', Asset::class))
            ->orderBy('e.id', 'desc');

        $query = $qb
            ->getQuery();

        return $this->dataTable($request, $query, false, [
            'groups' => ['list'],
        ]);
    }

    /**
     * @Route("/{id}", name="view", methods={"GET"})
     */
    public function view(Request $request, Investment $investment)
    {
        return $this->render('dashboard/assets/investments/view.html.twig', [
            'entity' => $investment,
        ]);
    }

    /**
     * @Route("/{id}/transaction", name="transaction", methods={"POST"})
     */
    public function justToReturn(Investment $investment)
    {
        // if the form was submitted without inform _method (PUT or DELETE), just redirect to view
        return $this->redirectToRoute('dashboard_assets_investments_view', [
            'id' => $investment->getId(),
        ]);
    }

    /**
     * @Route("/{id}/transaction", methods={"PUT"})
     */
    public function confirmTransaction(TransactionService $service, Investment $investment)
    {
        $service->process(
            $investment->getTransaction(),
            function (EntityManagerInterface $em, Transaction $transaction) use ($investment) {
                $investment
                    ->setApprovedAt($transaction->getProcessedAt())
                    ->setStatus(InvestmentStatus::APPROVED());

                $em->persist($investment);
                $em->flush();
            }
        );

        return $this->redirectToRoute('dashboard_assets_investments_view', [
            'id' => $investment->getId(),
        ]);
    }

    /**
     * @Route("/{id}/transaction", methods={"DELETE"})
     */
    public function cancelTransaction(TransactionService $service, Investment $investment)
    {
        $service->cancel(
            $investment->getTransaction(),
            function (EntityManagerInterface $em, Transaction $transaction) use ($investment) {
                $investment
                    ->setApprovedAt($transaction->getCancelledAt())
                    ->setStatus(InvestmentStatus::DENIED());

                // reenable investment amount
                $resource = $investment->getResource();
                $resource
                    ->getTotalInvested()
                    ->sub($investment->getAmount());

                $em->persist($investment);
                $em->persist($resource);
                $em->flush();
            }
        );

        return $this->redirectToRoute('dashboard_assets_investments_view', [
            'id' => $investment->getId(),
        ]);
    }
}
