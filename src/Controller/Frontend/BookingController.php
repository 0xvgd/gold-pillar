<?php

namespace App\Controller\Frontend;

use App\Controller\Traits\FormErrorsTrait;
use App\Entity\Person\Agent;
use App\Entity\Resource\Resource;
use App\Entity\Resource\View;
use App\Entity\Schedule\Day;
use App\Entity\Security\User;
use App\Form\Frontend\ViewType;
use App\Service\BookingService;
use App\Service\ScheduleService;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route("/booking", name="app_booking_")
 */
class BookingController extends AbstractController
{
    use FormErrorsTrait;

    /**
     * @Route("/resources/{id}/days", name="available_days", methods={"GET"})
     */
    public function daysFromResource(Request $request, ScheduleService $service, Resource $resource)
    {
        $agent = $resource->getAgent();
        $days = [];

        if ($agent) {
            $days = $service->getNext30AvailableDaysFromNow($agent);
            $days = $service->getUniqueDaysAsString($days);
        }

        return $this->json($days);
    }

    /**
     * @Route("/agents/{id}/days", name="available_days2", methods={"GET"})
     */
    public function daysFromAgent(Request $request, ScheduleService $service, Agent $agent)
    {
        $days = $service->getNext30AvailableDaysFromNow($agent);
        $days = $service->getUniqueDaysAsString($days);

        return $this->json($days);
    }

    /**
     * @Route("/resources/{id}/days/{day}", name="available_hours", methods={"GET"})
     */
    public function hoursFromResource(Request $request, ScheduleService $service, Resource $resource, string $day)
    {
        $dt = DateTime::createFromFormat('Y-m-d', $day);
        $agent = $resource->getAgent();
        $hours = [];

        if ($agent) {
            $schedule = $service->getSchedule($agent);
            $hours = $service->getAvailableTimes($schedule, $dt);
            $hours = $service->getUniqueTimesAsString($hours);
        }

        return $this->json($hours);
    }

    /**
     * @Route("/agents/{id}/days/{day}", name="available_hours2", methods={"GET"})
     */
    public function hoursFromAgent(Request $request, ScheduleService $service, Agent $agent, string $day)
    {
        $dt = DateTime::createFromFormat('Y-m-d', $day);
        $schedule = $service->getSchedule($agent);
        $hours = $service->getAvailableTimes($schedule, $dt);
        $hours = $service->getUniqueTimesAsString($hours);

        return $this->json($hours);
    }

    /**
     * @Route("/{id}", name="add", methods={"POST"})
     */
    public function add(Request $request, BookingService $service, Resource $resource)
    {
        $user = $this->getUser();
        $livingInfo = (bool) $request->get('livingInfo');

        if (!$user instanceof User || !$resource->getAgent()) {
            return $this->redirectToRoute('app_home');
        }

        $view = new View();
        $view->setResource($resource);

        $form = $this
            ->createForm(ViewType::class, $view, [
                'livingInfo' => $livingInfo,
                'resource' => $resource,
            ])
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            try {
                if (!$form->isValid()) {
                    throw new Exception('Form invalid');
                }

                $date = $form->get('date')->getData();
                $time = $form->get('time')->getData();

                $service->add($user, $resource, $date, $time);

                $message = 'Booked a view/meeting successfuly. Go to dashboard to see all your calendar events.';

                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'success' => true,
                        'message' => $message,
                    ]);
                }

                $this->addFlash('success', $message);
            } catch (Throwable $ex) {
                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'success' => false,
                        'message' => $ex->getMessage(),
                        'errors' => $this->getErrorMessages($form),
                    ], 400);
                }

                $this->addFlash('error', $ex->getMessage());
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }

    public function modal(Resource $resource, bool $livingInfo = true)
    {
        $view = new View();
        $view->setResource($resource);

        $form = $this->createForm(ViewType::class, $view, [
            'resource' => $resource,
            'action' => $this->generateUrl('app_booking_add', [
                'id' => $resource->getId(),
                'livingInfo' => $livingInfo,
            ]),
            'livingInfo' => $livingInfo,
        ]);

        return $this->render('frontend/booking/modal.html.twig', [
            'resource' => $resource,
            'form' => $form->createView(),
        ]);
    }
}
