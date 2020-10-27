<?php

namespace App\Controller\Dashboard\Investor;

use App\Controller\Payment\Traits\PaymentTrait;
use App\Entity\Finance\EntryFee;
use App\Entity\Finance\Investment;
use App\Entity\Person\Investor;
use App\Entity\Resource\Asset;
use App\Enum\InvestmentStatus;
use App\Form\Dashboard\Investor\NewInvestmentType;
use App\Repository\Asset\AssetRepository;
use App\Service\Finance\TransactionService;
use App\Service\InvestorService;
use App\Utils\TakePayments\Helpers\PaymentData;
use App\Utils\TakePayments\Helpers\PayzoneHelper;
use App\Utils\TakePayments\PayzoneGateway;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/investor/assets",
 *  name="dashboard_investor_assets_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class AssetsController extends AbstractController
{
    use PaymentTrait;

    private $payzoneGateway;
    private $payzoneHelper;

    public function __construct()
    {
        $this->payzoneHelper = new PayzoneHelper();
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('dashboard/investors/assets/index.html.twig', []);
    }

    /**
     * @Route("/search.json", name="search")
     */
    public function searchAction(Request $request, AssetRepository $repository)
    {
        $params = [];

        try {
            $qb = $repository->createQueryBuilder('a')->select('a');

            $maxResults = (int) $request->get('length');
            if ($maxResults <= 0 || $maxResults > 1000) {
                $maxResults = 10;
            }

            $offset = (int) $request->get('start');
            if ($offset < 0) {
                $offset = 0;
            }

            $result = $qb
                ->setParameters($params)
                ->getQuery()
                ->setFirstResult($offset)
                ->setMaxResults($maxResults)
                ->getResult();

            $query = $qb->select('a')->getQuery();
            $paginator = new Paginator($query, true);
            $total = sizeof($paginator);

            $content = [
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $result,
            ];
        } catch (Exception $ex) {
            $content = [
                'error' => $ex->getMessage(),
            ];
        }

        return $this->json($content, 200, [], ['groups' => 'list']);
    }

    /**
     * @Route("/{slug}/view", name="view")
     */
    public function view(
        Request $request,
        EntityManagerInterface $em,
        InvestorService $service,
        string $slug,
        string $certDir,
        LoggerInterface $logger,
        TransactionService $transactionService
    ) {
        $validate = null;
        if ($request->request->has('StatusCode')) {
            $validate = $this->validatePaymentResponse(
                $request,
                $transactionService,
                $em
            );
        }

        $paymentData = new PaymentData();
        $user = $this->getUser();

        /** @var Investor */
        $investor = $em
            ->getRepository(Investor::class)
            ->findOneBy(['user' => $user]);

        if (!$investor) {
            return $this->redirectToRoute('dashboard_index');
        }

        /** @var Asset */
        $asset = $em->getRepository(Asset::class)->findOneBy([
            'slug' => $slug,
        ]);

        if (!$asset) {
            return $this->redirectToRoute('dashboard_investor_assets_index');
        }

        $settings = $asset->getCompany()->getSettings();
        $assetEntryFee = $settings->getAssetEntryFee();

        $min = $asset->getMinInvestmentValue();
        $max = $asset->getMaxInvestmentValue();

        $form = $this->createForm(
            NewInvestmentType::class,
            ['amount' => $min],
            [
                'min' => $min,
                'max' => $max,
            ]
        )->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $amount = (float) $form->get('amount')->getData();

                $transactions = $service->makeInvestment(
                    $investor,
                    $asset,
                    $amount
                );

                /** @var Investment */
                $investment = $transactions['investment'];
                /** @var EntryFee */
                $entryFee = $transactions['entryFee'];

                $totalPayment = number_format($investment->getAmount() + $entryFee->getAmount(), 2, '.', '');

                $paymentData->setOrderId($investment->getReferenceCode());

                $paymentData->setFullAmount($totalPayment);
                $paymentData->setCustomerName(
                    $investment
                        ->getInvestor()
                        ->getUser()
                        ->getName()
                );
                $paymentData->setCardName(
                    $form
                        ->get('orderDetail')
                        ->get('cardName')
                        ->getData()
                );

                $paymentData->setCardNumber(
                    $form
                        ->get('orderDetail')
                        ->get('cardNumber')
                        ->getData()
                );

                $paymentData->setCV2(
                    $form
                        ->get('orderDetail')
                        ->get('cv2')
                        ->getData()
                );

                $paymentData->setExpiryDateMonth(
                    $form
                        ->get('orderDetail')
                        ->get('expiryDateMonth')
                        ->getData()
                );

                $paymentData->setExpiryDateYear(
                    $form
                        ->get('orderDetail')
                        ->get('expiryDateYear')
                        ->getData()
                );

                $paymentData->setAddress1(
                    $form
                        ->get('orderDetail')
                        ->get('address')
                        ->get('addressLine1')
                        ->getData()
                );

                $paymentData->setAddress2(
                    $form
                        ->get('orderDetail')
                        ->get('address')
                        ->get('addressLine2')
                        ->getData()
                );

                $paymentData->setCity(
                    $form
                        ->get('orderDetail')
                        ->get('address')
                        ->get('city')
                        ->getData()
                );

                $paymentData->setState(
                    $form
                        ->get('orderDetail')
                        ->get('address')
                        ->get('town')
                        ->getData()
                );

                $paymentData->setPostCode(
                    $form
                        ->get('orderDetail')
                        ->get('address')
                        ->get('postcode')
                        ->getData()
                );

                $paymentData->setCountry(
                    $form
                        ->get('orderDetail')
                        ->get('address')
                        ->get('country')
                        ->getData()
                );

                $paymentData->setTransactionDateTime(date('Y-m-d H:i:s P'));
                $config = $this->getConfiguration();
                $this->payzoneGateway = new PayzoneGateway(
                    $paymentData,
                    $em,
                    $config
                );

                $successUrl = $this->generateUrl(
                    'dashboard_investor_assets_view',
                    [
                        'slug' => $asset->getSlug(),
                    ]
                );

                $errorUrl = $this->generateUrl(
                    'dashboard_investor_assets_error',
                    [
                        'slug' => $asset->getSlug(),
                    ]
                );

                $paymentResponse = $this->process(
                    $request,
                    $em,
                    $paymentData,
                    $successUrl,
                    $errorUrl,
                    $certDir
                );

                if (3 != $paymentResponse['StatusCode']) {
                    $em->remove($investment);
                    $em->remove($entryFee);

                    $em->flush();
                }

                return $this->json(json_encode($paymentResponse));

                $this->addFlash(
                    'success',
                    'Investment added successfully. Waiting deposit confirmation.'
                );

                return $this->redirectToRoute(
                    'dashboard_investor_assets_view',
                    ['slug' => $slug]
                );
            } catch (Exception $ex) {
                $logger->error($ex->getMessage());

                $this->addFlash('error', $ex->getMessage());
            }
        }

        $investments = $em->getRepository(Investment::class)->findBy(
            [
                'investor' => $investor,
                'resource' => $asset,
                'status' => InvestmentStatus::APPROVED(),
            ],
            [
                'createdAt' => 'ASC',
            ]
        );

        $entryFees = $em->getRepository(EntryFee::class)->findBy(
            [
                'investor' => $investor,
                'resource' => $asset,
                'status' => InvestmentStatus::APPROVED(),
            ],
            [
                'createdAt' => 'ASC',
            ]
        );

        return $this->render('dashboard/investors/assets/view.html.twig', [
            'asset' => $asset,
            'investments' => $investments,
            'entryFees' => $entryFees,
            'form' => $form->createView(),
            'min' => $min,
            'max' => $max,
            'validation' => $validate,
            'slug' => $slug,
            'payzoneGateway' => $this->payzoneGateway,
            'assetEntryFee' => $assetEntryFee,
        ]);
    }

    /**
     * @Route("/{slug}/payment", name="payment", methods={"GET", "POST"})
     */
    public function paymentOk(
        Request $request,
        string $slug,
        EntityManagerInterface $em,
        TransactionService $transactionService
    ) {
        $paymentData = new PaymentData();
        $config = $this->getConfiguration();
        $this->payzoneGateway = new PayzoneGateway($paymentData, $em, $config);

        $validate = $this->validatePaymentResponse(
            $request,
            $transactionService,
            $em
        );

        /** @var Asset */
        $asset = $em->getRepository(Asset::class)->findOneBy([
            'slug' => $slug,
        ]);

        // $investments = $em->getRepository(Investment::class)->findBy(
        //     [
        //         'referenceCode' => $investor,
        //         'resource' => $asset,
        //     ],
        //     [
        //         'createdAt' => 'ASC',
        //     ]
        // );

        // $entryFees = $em->getRepository(EntryFee::class)->findBy(
        //     [
        //         'investor' => $investor,
        //         'resource' => $asset,
        //     ],
        //     [
        //         'createdAt' => 'ASC',
        //     ]
        // );

        return $this->render(
            'dashboard/investors/assets/payment_ok.html.twig',
            [
                'asset' => $asset,
                'validate' => $validate,
                'payzoneGateway' => $this->payzoneGateway,
            ]
        );
    }

    /**
     * @Route("/{slug}/error", name="error", methods={"GET", "POST"})
     */
    public function paymentError(
        Request $request,
        string $slug,
        EntityManagerInterface $em,
        TransactionService $transactionService
    ) {
        $paymentData = new PaymentData();
        $config = $this->getConfiguration();
        $this->payzoneGateway = new PayzoneGateway($paymentData, $em, $config);
        $validate = $this->callback($request, $transactionService, $em);

        /** @var Asset */
        $asset = $em->getRepository(Asset::class)->findOneBy([
            'slug' => $slug,
        ]);

        return $this->render('dashboard/investors/assets/error.html.twig', [
            'asset' => $asset,
            'validate' => $validate,
            'payzoneGateway' => $this->payzoneGateway,
        ]);
    }
}
