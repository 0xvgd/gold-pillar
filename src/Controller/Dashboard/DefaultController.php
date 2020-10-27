<?php

namespace App\Controller\Dashboard;

use App\Service\BalanceService;
use App\Service\Finance\AgentPaymentsService;
use App\Utils\FileUploadManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard",
 *  name="dashboard_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(
        AgentPaymentsService $agentPaymentsService,
        BalanceService $balanceService,
        EntityManagerInterface $em
    ) {
        $userIdHash = $this->getUser()->getId();
        $totalSales = $agentPaymentsService->getTotalSalesByUser($this->getUser());
        $referredAgentBonus = $balanceService->getReferredAgentBonusBalanceByUser($this->getUser());
        $salesBalance = $balanceService->getSalesBalanceByUser($this->getUser());
        $salesByMonth = $this->getSalesByMonth($agentPaymentsService);
        $account = $this->getUser()->getAccount();

        return $this->render('dashboard/default/index.html.twig', [
            'totalSales' => $totalSales,
            'salesBalance' => $salesBalance,
            'referredAgentBonus' => $referredAgentBonus,
            'salesByMonth' => $salesByMonth,
            'userHash' => $userIdHash,
        ]);
    }

    /**
     * @Route("/upload", name="upload", methods={"POST"})
     */
    public function upload(Request $request, FileUploadManager $uploader)
    {
        /** @var UploadedFile */
        $file = $request->files->get('file');
        $resp = [];

        if ($file instanceof UploadedFile) {
            $resp = $uploader->send($file->getRealPath(), $file->getClientOriginalName());
        }

        return $this->json($resp);
    }

    private function getSalesByMonth(AgentPaymentsService $agentPaymentsService)
    {
        $dates = null;

        for ($i = 0; $i < 6; ++$i) {
            $year = date('Y', strtotime(date('Y-m')." -$i month"));
            $month = date('m', strtotime(date('Y-m')." -$i month"));
            $dates[] = [
                'month' => $this->getMonth($month).'/'.$year,
                'sales' => $agentPaymentsService->getTotalSalesByMonth($this->getUser(), $month),
            ];
        }

        return array_reverse($dates);
    }

    private function getMonth($index)
    {
        --$index;
        $month =
            [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December',
            ];

        return $month[intval($index)];
    }
}
