<?php

namespace App\Controller\Dashboard;

use App\Entity\Person\Agent;
use App\Entity\Schedule\Schedule;
use App\Form\Schedule\ScheduleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/schedule",
 *  name="dashboard_schedule_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class ScheduleController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET", "POST"})
     */
    public function index(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $agent = $em
            ->getRepository(Agent::class)
            ->findOneBy([
                'user' => $user,
            ]);

        if (!$agent) {
            return $this->redirectToRoute('dashboard_index');
        }

        /** @var Schedule $schedule */
        $schedule = $em
            ->getRepository(Schedule::class)
            ->findOneBy([
                'agent' => $agent,
            ]);

        if (!$schedule) {
            $schedule = new Schedule();
            $schedule->setAgent($agent);
        }

        $form = $this
            ->createForm(ScheduleType::class, $schedule)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // prevent insert null value
            foreach ($schedule->getShifts() as $shift) {
                $shift->setSchedule($schedule);
                foreach ($shift->getDays() as $day) {
                    $day->setShift($shift);
                }
            }

            $em->persist($schedule);
            $em->flush();

            $this->addFlash('success', 'Schedule saved successfuly');

            return $this->redirectToRoute('dashboard_schedule_index');
        }

        return $this->render('dashboard/schedule/index.html.twig', [
            'agent' => $agent,
            'form' => $form->createView(),
        ]);
    }
}
