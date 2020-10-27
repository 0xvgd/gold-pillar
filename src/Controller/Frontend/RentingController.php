<?php

namespace App\Controller\Frontend;

use App\Controller\Payment\Traits\PaymentTrait;
use App\Entity\Person\Tenant;
use App\Entity\Reserve\Person;
use App\Entity\Reserve\Reserve;
use App\Entity\Resource\Accommodation;
use App\Enum\AccommodationStatus;
use App\Enum\PostStatus;
use App\Form\Frontend\ReserveType;
use App\Form\SearchProductType;
use App\Repository\Negotiation\OfferRepository;
use App\Repository\PageRepository;
use App\Repository\Person\TenantRepository;
use App\Repository\Renting\AccommodationRepository;
use App\Repository\Reserve\ReserveRepository;
use App\Service\Finance\TransactionService;
use App\Service\Renting\ReserveService;
use App\Utils\TakePayments\Helpers\PaymentData;
use App\Utils\TakePayments\Helpers\PayzoneHelper;
use App\Utils\TakePayments\PayzoneGateway;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/renting",
 *  name="app_renting_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class RentingController extends AbstractController
{
    use PaymentTrait;

    private $payzoneGateway;
    private $payzoneHelper;

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->payzoneHelper = new PayzoneHelper();
    }

    /**
     * @Route("/", name="index")
     */
    public function index(PageRepository $repository)
    {
        $form = $this->createFormBuilder()
            ->create('', SearchProductType::class)
            ->getForm();

        $page = $repository->findOneBy(['name' => 'renting']);

        return $this->render('frontend/renting/index.html.twig', [
            'page' => $page,
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search")
     */
    public function search(
        Request $request,
        AccommodationRepository $repository
    ) {
        $data = null;
        $searchForm = $request->get('form');

        $form = $this->createFormBuilder()
            ->create('', SearchProductType::class)
            ->getForm();

        parse_str($searchForm, $data);

        $form->submit($data);

        $params = [];
        try {
            $data = null;

            $params['approved'] = PostStatus::APPROVED();
            $params['to_rent'] = AccommodationStatus::TO_RENT();
            $params['reserved'] = AccommodationStatus::RESERVED();
            $params['rented'] = AccommodationStatus::RENTED();

            $qb = $repository
                ->createQueryBuilder('p')
                ->select('p')
                ->andWhere('p.postStatus = :approved')
                ->andWhere('p.status = :to_rent or
                            p.status = :reserved or
                            p.status = :rented');

            $location = $form->get('location')->getData();
            if (!empty($location)) {
                $qb
                    ->andWhere(
                        'p.address.postcode like :location or p.address.city like :location'
                    )
                    ->setParameter('location', "{$location}%");
            }

            $propertyType = $form->get('propertyType')->getData();
            if ($propertyType) {
                $qb
                    ->andWhere('p.propertyType = :propertyType')
                    ->setParameter('propertyType', "{$propertyType}%");
            }

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

            $query = $qb->select('p')->getQuery();
            $paginator = new Paginator($query, true);
            $total = sizeof($paginator);

            $content = [
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $result,
            ];
        } catch (Throwable $ex) {
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
        Accommodation $entity,
        OfferRepository $offerRepository,
        ReserveRepository $reserveRepository,
        TenantRepository $tenantRepository
    ) {
        $user = $this->getUser();
        $isTenant = $this->isGranted('ROLE_TENANT');
        $tenant = null;
        $reserve = null;
        $offer = null;

        if ($isTenant) {
            $tenant = $tenantRepository->findOneBy(['user' => $user]);
            $reserve = $reserveRepository->findOneBy([
                'accommodation' => $entity,
                'tenant' => $tenant,
            ]);

            $offer = $offerRepository->getOfferAcceptedByResource($entity);
        }

        return $this->render('frontend/renting/view.html.twig', [
            'entity' => $entity,
            'reserve' => $reserve,
            'offer' => $offer,
            'tenant' => $tenant,
        ]);
    }

    /**
     * @Route("/{slug}/reserve", name="reserve", methods={"GET","POST"})
     */
    public function reserveAdd(
        Request $request,
        EntityManagerInterface $em,
        Accommodation $accommodation,
        ReserveService $reserveService,
        OfferRepository $offerRepository,
        string $certDir
    ) {
        $errors = null;
        $user = $this->getUser();
        $action = '';
        $peopleCount = $accommodation->getLetAvailableFor();

        $paymentData = new PaymentData();

        $tenant = $em->getRepository(Tenant::class)->findOneBy([
            'user' => $user,
        ]);

        if (!$tenant) {
            return $this->redirectToRoute('app_renting_view', [
                'slug' => $accommodation->getSlug(),
            ]);
        }

        $offer = $offerRepository->getOfferAcceptedByResourceTenant(
            $accommodation,
            $tenant
        );

        $fee = clone $accommodation->getRent();
        $tax = 0.2;

        if ($offer) {
            $peopleCount = $offer->getPeopleCount();
            $fee->setAmount($offer->getAgreedValue());
        }

        $fee->setAmount($fee->getAmount() * $tax);

        $referenceCode = $reserveService->generateReferenceCode();

        $reserve = new Reserve();

        $reserve
            ->setFee($fee)
            ->setReferenceCode($referenceCode)
            ->setTenant($tenant)
            ->setAccommodation($accommodation)
            ->addPerson(new Person());

        $form = $this->createForm(ReserveType::class, $reserve, [
            'peopleCount' => $peopleCount,
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reserve);
            $em->flush();

            $paymentData->setOrderId($reserve->getReferenceCode());
            $paymentData->setFullAmount($reserve->getFee()->getAmount());
            $paymentData->setCountry(55);
            $paymentData->setCustomerName(
                $reserve
                    ->getTenant()
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

            $paymentData->setTransactionDateTime(date('Y-m-d H:i:s P'));
            $config = $this->getConfiguration();
            $this->payzoneGateway = new PayzoneGateway(
                $paymentData,
                $em,
                $config
            );

            $successUrl = $this->generateUrl('app_renting_reserve_view', [
                'slug' => $accommodation->getSlug(),
                'id' => $reserve->getId(),
            ]);

            $errorUrl = $this->generateUrl('app_renting_error', [
                'slug' => $accommodation->getSlug(),
            ]);

            $paymentResponse = $this->process(
                $request,
                $em,
                $paymentData,
                $successUrl,
                $errorUrl,
                $certDir
            );

            if (3 != $paymentResponse['StatusCode']) {
                $em->remove($reserve);
                $em->flush();
            }

            return $this->json(json_encode($paymentResponse));
        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('frontend/renting/reserve_add.html.twig', [
            'accommodation' => $accommodation,
            'reserve' => $reserve,
            'form' => $form->createView(),
            'errors' => $errors,
            'slug' => $accommodation->getSlug(),
            'action' => $action,
        ]);
    }

    /**
     * @Route("/{slug}/reserve/process", name="reserve_process", methods={"GET","POST"})
     */
    public function reserveProcess(
        Request $request,
        EntityManagerInterface $em,
        Accommodation $accommodation
    ) {
        $errors = null;
        $user = $this->getUser();

        $tenant = $em->getRepository(Tenant::class)->findOneBy([
            'user' => $user,
        ]);

        if (!$tenant) {
            return $this->redirectToRoute('app_renting_view', [
                'slug' => $accommodation->getSlug(),
            ]);
        }

        if ($this->session->has('paymentProcess')) {
            $paymentProcess = $this->session->get('paymentProcess');

            return $this->redirectToRoute('app_renting_reserve_view', [
                'slug' => $accommodation->getSlug(),
                'id' => $paymentProcess['reserveDatails']['reserveId'],
            ]);
        }
    }

    /**
     * @Route("/{slug}/reserve/{id}", name="reserve_view", methods={"GET", "POST"})
     * @ParamConverter("accommodation", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("reserve", options={"id" = "id"})
     */
    public function reserveView(
        Request $request,
        Accommodation $accommodation,
        Reserve $reserve,
        EntityManagerInterface $em,
        TransactionService $transactionService
    ) {
        $paymentData = new PaymentData();
        $config = $this->getConfiguration();
        $this->payzoneGateway = new PayzoneGateway($paymentData, $em, $config);
        $validate = $this->callback($request, $transactionService, $em);

        return $this->render('frontend/renting/reserve_view.html.twig', [
            'accommodation' => $accommodation,
            'reserve' => $reserve,
            'validate' => $validate,
            'payzoneGateway' => $this->payzoneGateway,
        ]);
    }

    /**
     * @Route("/{slug}/error", name="error", methods={"GET", "POST"})
     * @ParamConverter("accommodation", options={"mapping": {"slug": "slug"}})
     */
    public function reserveError(
        Request $request,
        Accommodation $accommodation,
        EntityManagerInterface $em,
        TransactionService $transactionService
    ) {
        $paymentData = new PaymentData();
        $config = $this->getConfiguration();
        $this->payzoneGateway = new PayzoneGateway($paymentData, $em, $config);
        $validate = $this->callback($request, $transactionService, $em);

        return $this->render('frontend/renting/error.html.twig', [
            'accommodation' => $accommodation,
            'validate' => $validate,
            'payzoneGateway' => $this->payzoneGateway,
        ]);
    }
}
