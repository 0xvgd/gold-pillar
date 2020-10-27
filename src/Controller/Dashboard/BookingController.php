<?php

namespace App\Controller\Dashboard;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Person\Agent;
use App\Entity\Resource\View;
use App\Form\Dashboard\Booking\SearchType;
use App\Repository\Renting\ViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/booking",
 *  name="dashboard_booking_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class BookingController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index()
    {
        $form = $this->createForm(SearchType::class, null);

        return $this->render('dashboard/booking/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, ViewRepository $repository)
    {
        $columns = [
            'v.id',
            'e.date',
            'e.startTime',
            'e.endTime',
            'r.name',
            'au.name',
            'tu.name',
        ];

        $user = $this->getUser();
        $search = $this->searchValues($request);
        $qb = $repository
            ->createQueryBuilder('v')
            ->join('v.event', 'e')
            ->join('v.resource', 'r')
            ->join('v.agent', 'a')
            ->where('v.customer = :user OR a.user = :user')
            ->setParameter('user', $user);

        $this->addOrderBy($request, $qb, $columns);

        $form = $this
            ->createForm(SearchType::class, null)
            ->submit($search);

        if ($form->isSubmitted() && $form->isValid()) {
            $startDate = $form->get('startDate')->getData();
            $endDate = $form->get('endDate')->getData();

            if ($startDate) {
                $qb
                    ->andWhere('e.date >= :startDate')
                    ->setParameter('startDate', $startDate->format('Y-m-d'));
            }

            if ($endDate) {
                $qb
                    ->andWhere('e.date <= :endDate')
                    ->setParameter('endDate', $endDate->format('Y-m-d'));
            }
        }

        $query = $qb->getQuery();

        return $this->dataTable($request, $query, false, [
            'groups' => 'list',
        ]);
    }

    /**
     * @Route("/{id}", name="view", methods={"GET"})
     */
    public function view(View $view, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $isAgent = $this->isGranted('ROLE_AGENT');
        $denied = true;

        if ($isAgent) {
            $agent = $em
                ->getRepository(Agent::class)
                ->findOneBy(['user' => $user]);
            // view booked to another agent
            $denied = !$agent || ($view->getAgent()->getId() !== $agent->getId());
        }
        if ($denied) {
            // view booked to another customer
            $denied = ($view->getCustomer()->getId() !== $user->getId());
        }

        if ($denied) {
            return $this->redirectToRoute('dashboard_index');
        }

        return $this->render('dashboard/booking/view.html.twig', [
            'view' => $view,
        ]);
    }
}
