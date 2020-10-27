<?php

namespace App\Controller\Dashboard;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Negotiation\Offer;
use App\Entity\Negotiation\Offer as Entity;
use App\Entity\Resource\Accommodation;
use App\Enum\OfferStatus;
use App\Form\Dashboard\CounterOfferType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route(
 *  "/{_locale}/dashboard/offers",
 *  name="dashboard_offers_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class OffersController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index()
    {
        return $this->render('dashboard/offers/index.html.twig', []);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(
        Request $request,
        EntityManagerInterface $em
    ) {
        $search = $request->get('search');
        $params = [];

        if (!is_array($search)) {
            $search = [
                'basic' => [],
            ];
        }

        $qb = $em->createQueryBuilder()
        ->select('
        o, r, t, ut')
        ->from(Entity::class, 'o')
        ->join('o.resource', 'r')
        ->join(Accommodation::class, 'a', 'WITH', 'o.resource = a')
        ->join('o.agent', 'ag')
        ->join('ag.user', 'ua')
        ->join('o.tenant', 't')
        ->join('t.user', 'ut')
        ->orderBy('o.createdAt', 'DESC')
        ->andWhere('o.removedAt is null');

        if (!$this->isGranted('ROLE_ADMIN')) {
            $qb
                ->andWhere('ua = :user');
            $params['user'] = $this->getUser();
        }

        if (isset($search['basic'])) {
            parse_str($search['basic'], $basic);

            $email = $basic['email'];

            if (!empty($email)) {
                $qb->andWhere('LOWER(u.email) LIKE :arg');
                $params['arg'] = "%{$email}%";
            }
        }

        $query = $qb
            ->setParameters($params)
            ->getQuery()
            ;

        $dateCallback = function ($innerObject) {
            return $innerObject instanceof \DateTime ? $innerObject->format('d/m/Y H:i:s') : '';
        };

        $context = [
                AbstractNormalizer::CALLBACKS => [
                    'createdAt' => $dateCallback,
                ],
            ];

        return $this->dataTable($request, $query, true, $context);
    }

    /**
     * @Route("/edit", name="new", methods={"GET", "POST"})
     * @Route("/edit/{offer_id}", name="edit", methods={"GET", "POST"})
     * @ParamConverter("entity", options={"mapping": {"offer_id": "id" , null = "removedAt"}})
     */
    public function edit(Request $request, Entity $entity)
    {
        $formCounterOffer = $this->createForm(CounterOfferType::class);

        return $this->render('dashboard/offers/form.html.twig', [
            'entity' => $entity,
            'formCounterOffer' => $formCounterOffer->createView(),
        ]);
    }

    /**
     * @Route("/counter-offer", name="counter_offer", methods={"GET", "POST"})
     */
    public function counterOffer(Request $request, EntityManagerInterface $em)
    {
        $offerId = $request->get('offerId');
        $counterOfferValue = $request->get('counterOfferValue');

        $offerRepository = $em->getRepository(Offer::class);
        /** @var Offer */
        $offer = $offerRepository->findOneBy([
            'id' => $offerId,
        ]);

        $offer->setCounterOfferValue($counterOfferValue);

        $em->merge($offer);

        $em->flush();

        $this->addFlash('success', 'Item saved successfully.');

        return $this->redirectToRoute('dashboard_offers_edit', [
            'offer_id' => $offer->getId(),
        ]);
    }

    /**
     * @Route("/accept", name="accept", methods={"GET", "POST"})
     */
    public function accept(Request $request, EntityManagerInterface $em)
    {
        $offerId = $request->get('offerId');

        $offerRepository = $em->getRepository(Offer::class);
        /** @var Offer */
        $offer = $offerRepository->findOneBy([
            'id' => $offerId,
        ]);

        $offer->setStatus(OfferStatus::OFFER_ACCEPTED());
        $offer->setOfferAcceptedAt(new DateTime());
        $offer->setAgreedValue($offer->getOfferValue());

        $em->merge($offer);

        $em->flush();

        $this->addFlash('success', 'Item saved successfully.');

        return $this->redirectToRoute('dashboard_offers_edit', [
            'offer_id' => $offer->getId(),
        ]);
    }

    /**
     * @Route("/decline", name="decline", methods={"GET", "POST"})
     */
    public function decline(Request $request, EntityManagerInterface $em)
    {
        $offerId = $request->get('offerId');

        $offerRepository = $em->getRepository(Offer::class);
        /** @var Offer */
        $offer = $offerRepository->findOneBy([
            'id' => $offerId,
        ]);

        $offer->setStatus(OfferStatus::OFFER_DECLINED());
        $offer->setOfferDeclinedAt(new DateTime());

        $em->merge($offer);
        $em->flush();

        $this->addFlash('success', 'Item saved successfully.');

        return $this->redirectToRoute('dashboard_offers_edit', [
            'offer_id' => $offer->getId(),
        ]);
    }
}
