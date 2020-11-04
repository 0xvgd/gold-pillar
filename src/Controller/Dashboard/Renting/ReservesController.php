<?php

namespace App\Controller\Dashboard\Renting;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Reserve\Reserve as Entity;
use App\Enum\AccommodationStatus;
use App\Form\Dashboard\RemoveReserveFormType;
use App\Repository\Reserve\ReserveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/renting/reserves",
 *  name="dashboard_renting_reserves_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class ReservesController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        return $this->render('dashboard/renting/reserves/index.html.twig', []);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, ReserveRepository $repository, EntityManagerInterface $em)
    {
        $columns = [];

        $user = $this->getUser();
        $search = $this->searchValues($request);
        $qb = $repository
            ->createQueryBuilder('e')
            ->join('e.accommodation', 'r')
            ->join('e.tenant', 't')
            ->join('t.user', 'tu')
            ->join('r.agent', 'a')
            ->join('a.user', 'au');

        if (!$this->isGranted('ROLE_ADMIN')) {
            $qb
                ->where('tu = :user OR au = :user')
                ->setParameter('user', $user);
        }

        $this->addOrderBy($request, $qb, $columns);

        $query = $qb->getQuery();

        return $this->dataTable($request, $query, false, [
            'groups' => 'list',
        ]);
    }

    /**
     * @Route("/{id}", name="view", methods={"GET"})
     */
    public function view(Request $request, Entity $entity)
    {
        $removeReserveForm = null;

        $emailBody = "Hi,\n\n Can you please verify the tenants below? \n\n";
        foreach ($entity->getPeople() as $person) {
            $emailBody .= "{$person->getName()}, {$person->getEmail()}, {$person->getPhone()}\n";
        }
        $emailBody .= "\nThanks.\n\nYours sincerely,\nGold Pillar";

        if ($entity->getId()) {
            $removeReserveForm = $this
                ->createForm(RemoveReserveFormType::class, null, [
                    'method' => 'post',
                    'action' => $this->generateUrl('dashboard_renting_reserves_register_payment', [
                        'id' => $entity->getId(),
                    ]),
                ]);
        }

        return $this->render('dashboard/renting/reserves/view.html.twig', [
            'entity' => $entity,
            'removeReserveForm' => $removeReserveForm->createView(),
            'emailBody' => rawurlencode($emailBody),
        ]);
    }

    /**
     * @Route("/{id}", name="remove", methods={"DELETE"})
     */
    public function remove(EntityManagerInterface $em, Entity $entity)
    {
        $em->remove($entity);
        $em->flush();

        $this->addFlash('success', 'Success!');

        return $this->redirectToRoute('dashboard_renting_reserves_index');
    }

    /**
     * @Route("/{id}", name="register_payment", methods={"POST"})
     */
    public function registerPayment(Request $request, EntityManagerInterface $em, Entity $entity)
    {
        $form = $this
            ->createForm(RemoveReserveFormType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entity->getAccommodation()->setAccommodationStatus(AccommodationStatus::RESERVED());
            $em->merge($entity);
            $em->flush();

            $this->addFlash('success', 'Success!');
        }

        return $this->redirectToRoute('dashboard_renting_reserves_view', [
            'id' => $entity->getId(),
        ]);
    }
}
