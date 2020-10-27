<?php

namespace App\Controller\Dashboard;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Negotiation\Offer;
use App\Entity\Resource\Accommodation;
use App\Enum\OfferStatus;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route(
 *  "/{_locale}/dashboard/tenant",
 *  name="dashboard_tenant_offers_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class TenantOffersController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/offers", name="index")
     */
    public function index()
    {
        return $this->render('dashboard/tenant_offers/index.html.twig', [
            'controller_name' => 'TenantOffersController',
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(
        EntityManagerInterface $em
    ) {
        $params = [];
        $params['user'] = $this->getUser();

        $qb = $em->createQueryBuilder()
            ->select('o.id, 
            o.createdAt, 
            o.originalPrice.amount as originalPrice,
            o.status,
            o.peopleCount,
            o.offerValue,
            o.counterOfferValue,
            o.counterOfferAcceptedAt,
            ua.avatar as agentAvatar,
            ua.name as agentName,
            ua.email as agentEmail,
            ua.phone as agentPhone,
            r.mainPhoto as propertyPhoto, 
            r.tag, 
            r.name,
            r.slug as resourceSlug, 
            a.propertyType, 
            a.status as acommodationStatus')
            ->from(Offer::class, 'o')
            ->join('o.resource', 'r')
            ->join('o.agent', 'ag')
            ->join('ag.user', 'ua')
            ->join(Accommodation::class, 'a', 'WITH', 'o.resource = a')
            ->join('o.tenant', 't')
            ->join('t.user', 'ut')
            ->andWhere('ut = :user')
            ->andWhere('o.removedAt is null');

        $result = $qb
            ->setParameters($params)
            ->getQuery()
            ->getResult();

        $dateCallback = function ($innerObject) {
            return $innerObject instanceof \DateTime ? $innerObject->format('Y-m-d H:i:s') : '';
        };

        $context = [
            AbstractNormalizer::CALLBACKS => [
                'createdAt' => $dateCallback,
            ],
        ];

        $content = [
            'data' => $result,
        ];

        return $this->json($content, 200, [], $context);
    }

    /**
     * @Route("/offers/delete/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Offer $offer, EntityManagerInterface $em)
    {
        $offer->setRemovedAt(new DateTime());

        $em->merge($offer);
        $em->flush();

        $this->addFlash('success', 'Item deleted successfully.');

        return $this->redirectToRoute('dashboard_tenant_offers_index');
    }

    /**
     * @Route("/offers/accept/{id}", name="accept")
     */
    public function accept(Offer $offer, EntityManagerInterface $em)
    {
        $offer->setStatus(OfferStatus::COUNTER_OFFER_ACCEPTED());
        $offer->setCounterOfferAcceptedAt(new DateTime());
        $offer->setAgreedValue($offer->getCounterOfferValue());

        $em->merge($offer);
        $em->flush();

        $this->addFlash('success', 'Counter offer accepted successfully.');

        return $this->redirectToRoute('dashboard_tenant_offers_index');
    }

    /**
     * @Route("/offers/decline/{id}", name="decline")
     */
    public function decline(Offer $offer, EntityManagerInterface $em)
    {
        $offer->setStatus(OfferStatus::COUNTER_OFFER_DECLINED());
        $offer->setCounterOfferDeclinedAt(new DateTime());

        $em->merge($offer);
        $em->flush();

        $this->addFlash('success', 'Item declined successfully.');

        return $this->redirectToRoute('dashboard_tenant_offers_index');
    }
}
