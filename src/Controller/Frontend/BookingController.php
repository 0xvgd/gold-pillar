<?php

namespace App\Controller\Frontend;

use App\Controller\Traits\FormErrorsTrait;
use App\Entity\Address;
use App\Entity\Person\Agent;
use App\Entity\Person\Tenant;
use App\Entity\Resource\Resource;
use App\Entity\Resource\View;
use App\Entity\Schedule\Day;
use App\Entity\Security\User;
use App\Form\Frontend\ViewType;
use App\Form\Subscribe\RegistrationType;
use App\Service\BookingService;
use App\Service\ScheduleService;
use App\Service\UserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
        /*$days = $service->getNext30AvailableDaysFromNow($agent);
        $days = $service->getUniqueDaysAsString($days);*/
        $current = date('Y-m-d');
        $days = $service->get35DayPeriod($current);
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
            $hours = $service->getAvailableTimesFixed($schedule, $dt);
            // $hours = $service->getUniqueTimesAsString($hours);
        }
        $availDays = $service->get35DayPeriod($day);

        return $this->json(array('times' => $hours, 'moves' => $availDays));

    }

    /**
     * @Route("/agents/{id}/days/{day}", name="available_hours2", methods={"GET"})
     */
    public function hoursFromAgent(Request $request, ScheduleService $service, Agent $agent, string $day)
    {
        $dt = DateTime::createFromFormat('Y-m-d', $day);
        $schedule = $service->getSchedule($agent);
        if($schedule == null)
            return $this->json([]);
        $hours = $service->getAvailableTimesFixed($schedule, $dt);
       // $hours = $service->getUniqueTimesAsString($hours);

        return $this->json($hours);
    }


    /**
     * //todo old one should remove
     * @Route("/{id}", name="add", methods={"POST"})
     */
    public function add(Request $request, BookingService $service, Resource $resource)
    {
        $user = $this->getUser();
        $livingInfo = (bool)$request->get('livingInfo');

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

    /**
     * @param Resource $resource
     * @param bool $livingInfo
     * @return \Symfony\Component\HttpFoundation\Response
     * //todo old one should remove
     */
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

    /**
     * @Route("/assign/{id}/{type}",defaults={"type"=1}, name="assign", methods={"GET"})
     */
    public function booking($id,$type, ScheduleService $service)
    {
        $current = date('Y-m-d');
        $availDays = $service->get35DayPeriod($current);
        $countrys = Countries::getNames();
        return $this->render('frontend/booking/view.html.twig', ['id' => $id,'type' => $type, 'items' => $availDays, 'countrys' => $countrys]);
    }

    /**
     * @Route("/auth/{id}", name="auth", methods={"POST"})
     */
    public function bookingWithAuth(Request $request, BookingService $bookingService, Resource $resource){

        $user = $this->getUser();
        $date = $request->get('bookingDate');
        $time = $request->get('bookingTime');

        try {
            $res = $bookingService->add($user, $resource, $date, $time);
        } catch (Exception $e) {
            return $this->json(['result' => $e->getMessage()]);
        }

        return $this->json(['result' => true]);
    }
    /**
     * @Route("/assign/{id}/login", name="login", methods={"POST"})
     */
    public function bookingWithLogin(Request $request, Resource $resource, BookingService $bookingService, UserService $userService, UserPasswordEncoderInterface $passwordEncoder)
    {

        $userRepo = $userService->getRepository();
        $user = $userRepo->findOneBy(['email' => $request->get('email')]);
        if (!$user != null)
            return $this->json(['result' => false]);
        $success = $passwordEncoder->isPasswordValid($user, $request->get('password'));
        if (!$success)
            return $this->json(['result' => false]);
        $date = $request->get('bookingDate');
        $time = $request->get('bookingTime');
        try {
            $res = $bookingService->add($user, $resource, $date, $time);
        } catch (Exception $e) {
            return $this->json(['result' => $e->getMessage()]);
        }

        return $this->json(['result' => true]);
    }

    /**
     * @Route("/assign/{id}/sign", name="sign", methods={"POST"})
     */
    public function bookingWithSign(Request $request, Resource $resource, BookingService $bookingService, UserService $userService, UserPasswordEncoderInterface $passwordEncoder,EntityManagerInterface $em)
    {
        $userRepo = $userService->getRepository();
        $user = new User();
        //user register todo need to use user service create method
        try {

            $email = $request->get('email');
            $phone = $request->get('phone');
            $plainPass = $request->get('password');
            $encodedPass =$passwordEncoder->encodePassword($user, $plainPass);
            $name = $request->get('name');
            $address1 = $request->get('address1');
            $address2 = $request->get('address2');
            $postcode = $request->get('postcode');
            $city = $request->get('city');
            $country = $request->get('country');
            $address = new Address();
            $address
                ->setPostcode($postcode)
                ->setAddressLine1($address1)
                ->setAddressLine2($address2)
                ->setCountry($country)
                ->setCity($city);
            $user
                ->setName($name)
                ->setEmail($email)
                ->setPhone($phone)
                ->setPassword($encodedPass)
                ->setAddress($address)
                ->setRoles(['ROLE_TENANT']);

            $tenant = new Tenant();
            $tenant->setUser($user);

            $em->persist($tenant);
            $em->flush();

            $userService->sendConfirmationMail($user);


        } catch (Exception $e){
            return $this->json(['result' => $e->getMessage()]);
        }

        $date = $request->get('bookingDate');
        $time = $request->get('bookingTime');

        try {
            $res = $bookingService->add($user, $resource, $date, $time);
        } catch (Exception $e) {
            return $this->json(['result' => $e->getMessage()]);
        }

        return $this->json(['result' => true]);
    }

    /**
     * @Route("/resources/check/{email}",defaults={"email"=1}, name="check", methods={"GET"})
     */
    public function bookingCheckEmail(Request $request, UserService $userService, string $email)
    {
        $userRepo = $userService->getRepository();
        $user = $userRepo->findOneBy(['email' => $email]);
        if ($user != null)
            return $this->json(['result' => true]);
        return $this->json(['result' => false]);
    }
}
