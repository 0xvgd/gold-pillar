<?php

namespace App\Controller\Dashboard;

use App\Controller\Traits\FormErrorsTrait;
use App\Entity\Calendar\Event;
use App\Entity\Resource\View;
use App\Enum\CalendarEventType;
use App\Form\Dashboard\Calendar\EventType;
use App\Repository\Calendar\EventRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/dashboard/calendar",
 *  name="dashboard_calendar_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class CalendarController extends AbstractController
{
    use FormErrorsTrait;

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index()
    {
        $form = $this->createForm(EventType::class, null, [
            'action' => $this->generateUrl('dashboard_calendar_add'),
        ]);

        return $this->render('dashboard/calendar/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $event = new Event();
        $event->setOwner($user)->addParticipant($user);

        $form = $this->createForm(EventType::class, $event)->handleRequest(
            $request
        );

        if ($form->isSubmitted()) {
            try {
                if (!$form->isValid()) {
                    throw new Exception('Form invalid');
                }

                if ($event->isAllDay()) {
                    $startTime = new DateTime();
                    $startTime->setTime(0, 0, 0);
                    $endTime = new DateTime();
                    $endTime->setTime(23, 59, 59);
                    $event->setStartTime($startTime)->setEndTime($endTime);
                }

                $count = (int) $em
                    ->createQueryBuilder()
                    ->select('COUNT(e)')
                    ->from(Event::class, 'e')
                    ->where('e.owner = :user')
                    ->andWhere('e.date = :date')
                    ->andWhere(
                        implode(' OR ', [
                            '(e.startTime <= :startTime AND e.endTime >= :startTime)',
                            '(e.startTime <= :endTime AND e.endTime >= :endTime)',
                        ])
                    )
                    ->setParameters([
                        'user' => $user,
                        'date' => $event->getDate()->format('Y-m-d'),
                        'startTime' => $event->getStartTime()->format('H:i:s'),
                        'endTime' => $event->getEndTime()->format('H:i:s'),
                    ])
                    ->getQuery()
                    ->getSingleScalarResult();

                if ($count > 0) {
                    throw new Exception('You already have an event scheduled at that time');
                }

                $em->persist($event);
                $em->flush();

                $message =
                    'Booked a view successfuly. Go to dashboard to see all your calendar events.';

                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'success' => true,
                        'message' => $message,
                    ]);
                }

                $this->addFlash('success', $message);
            } catch (Throwable $ex) {
                if ($request->isXmlHttpRequest()) {
                    return $this->json(
                        [
                            'success' => false,
                            'message' => $ex->getMessage(),
                            'errors' => $this->getErrorMessages($form),
                        ],
                        400
                    );
                }

                $this->addFlash('error', $ex->getMessage());
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/events", name="events", methods={"GET"})
     */
    public function events(Request $request, EventRepository $repository)
    {
        $startParam = $request->get('start');
        $endParam = $request->get('end');
        $dateFormat = 'Y-m-d\TH:i:sP';
        $now = new DateTime();

        $user = $this->getUser();
        $startDate = DateTime::createFromFormat($dateFormat, $startParam);
        $endDate = DateTime::createFromFormat($dateFormat, $endParam);

        if (!$startDate) {
            $startDate = $now;
        }

        if (!$endDate) {
            $endDate = $now;
        }

        $events = $repository->findByUser($user, $startDate, $endDate);

        return $this->json(
            $events,
            200,
            [],
            [
                'groups' => 'list',
            ]
        );
    }

    /**
     * @Route("/events/{id}", name="event", methods={"GET"})
     */
    public function event(EntityManagerInterface $em, Event $event)
    {
        $data = [
            'event' => $event,
        ];

        if ($event->getType()->equals(CalendarEventType::VIEW())) {
            $view = $em->getRepository(View::class)->findOneBy([
                'event' => $event,
            ]);
            $data['view'] = $view;
        }

        return $this->json($data, 200, [], ['groups' => 'list']);
    }

    /**
     * @Route("/events/{id}", name="delete", methods={"DELETE"})
     */
    public function remove(EntityManagerInterface $em, Event $event)
    {
        $em->remove($event);

        $em->flush();

        return $this->json([]);
    }
}
