<?php

namespace App\Controller\Frontend;

use App\Controller\Traits\FormErrorsTrait;
use App\Entity\Negotiation\Offer;
use App\Entity\Person\Tenant;
use App\Entity\Resource\OfferableInterface;
use App\Entity\Resource\Resource;
use App\Entity\Security\User;
use App\Enum\OfferStatus;
use App\Form\Frontend\OfferType;
use App\Repository\Negotiation\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/offers")
 */
class OffersController extends AbstractController
{
    use FormErrorsTrait;

    /**
     * @Route("/{id}", name="app_offer", methods={"POST"})
     */
    public function add(Request $request, EntityManagerInterface $em, Resource $resource)
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_home');
        }

        if (!$resource instanceof OfferableInterface) {
            return $this->redirectToRoute('app_home');
        }

        $offer = new Offer();
        $form = $this
            ->createForm(OfferType::class, $offer, [
                'accommodation' => $resource,
            ])
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $agent = $resource->getAgent();
                $tenant = $em
                    ->getRepository(Tenant::class)
                    ->findOneBy([
                        'user' => $user,
                    ]);

                $offer
                    ->setOriginalPrice($resource->getOfferPrice())
                    ->setCreatedAt(new \DateTime())
                    ->setTenant($tenant)
                    ->setStatus(OfferStatus::STARTED())
                    ->setResource($resource)
                    ->setAgent($agent);

                $em->persist($offer);
                $em->flush();

                $message = 'Offer registered successfully. Go to dashboard to see all your offers.';

                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'success' => true,
                        'message' => $message,
                    ]);
                }

                $this->addFlash('success', $message);
            } else {
                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'success' => false,
                        'errors' => $this->getErrorMessages($form),
                    ], 400);
                }
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }

    public function modal(OfferRepository $repository, Resource $resource)
    {
        $user = $this->getUser();
        $offer = $repository
            ->createQueryBuilder('e')
            ->join('e.tenant', 't')
            ->where('e.resource = :resource')
            ->andWhere('e.status = :status')
            ->andWhere('t.user = :user')
            ->andWhere('e.removedAt is null')
            ->setParameters([
                'resource' => $resource,
                'status' => OfferStatus::STARTED(),
                'user' => $user,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $form = $this->createForm(OfferType::class, new Offer(), [
            'accommodation' => $resource,
            'action' => $this->generateUrl('app_offer', [
                'id' => $resource->getId(),
            ]),
        ]);

        return $this->render('frontend/offers/modal.html.twig', [
            'form' => $form->createView(),
            'offer' => $offer,
        ]);
    }
}
