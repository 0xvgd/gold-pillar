<?php

namespace App\Controller\Frontend;

use App\Entity\Address;
use App\Entity\Helper\PricePaidIndex;
use App\Entity\Money;
use App\Entity\Person\Agent;
use App\Entity\Resource\Property;
use App\Entity\Resource\Resource;
use App\Enum\PropertyType;
use App\Form\Frontend\SellingForm;
use App\Repository\PageRepository;
use App\Service\AgentService;
use App\Service\BookingService;
use App\Service\HousePriceIndex\ClientInterface;
use App\Service\Property\PropertyService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/selling",
 *  name="app_selling_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class SellingController extends AbstractController
{
    use AgentJsonRouteTrait;

    /**
     * @Route("/", name="index")
     */
    public function index() {
        $countrys = Countries::getNames();
        return $this->render('frontend/selling/index_new.html.twig',['countrys' => $countrys]);
    }

    /**
     * @Route("/check-postcode", name="postcode")
     */
    public function checkAvailablePostCode(Request $request,ClientInterface $client,PropertyService $propertyService){
        $location = (string) $request->get('q');
        try {
            $result = $client->getAverageSoldPrice($location);
            if (!$result)
                return $this->json(['status' => false,'result' => 'No one result found']);
            $addresses = $propertyService->getAddressByPostCode($location);
            return $this->json(['status' => true,'result' => $result,'addresses' => $addresses]);
        } catch (Throwable $ex) {
            return $this->json(['status' => false,'result' => $ex->getMessage()]);
        }

    }

    /**
     * @Route("/index_back", name="index_back")
     */
    public function index_back(
        Request $request,
        PageRepository $repository,
        ClientInterface $client,
        EntityManagerInterface $em,
        PropertyService $propertyService,
        BookingService $bookingService
    ) {
        $location = (string) $request->get('q');
        $page = $repository->findOneBy(['name' => 'selling']);
        $result = null;

        if ($location) {
            try {
                $result = $client->getAverageSoldPrice($location);
                if (!$result) {
                    throw new Exception('No one result found.');
                }
            } catch (Throwable $ex) {
                $result = [
                    'error' => $ex->getMessage(),
                ];
            }
        }

        $data = [];
        if ($result instanceof PricePaidIndex) {
            $data['postcode'] = $result->getPostcode() ?? '';
            $data['address'] = $result->getAddress() ?? '';
            $data['town'] = $result->getTown() ?? '';
            $data['county'] = $result->getCounty() ?? '';
            $data['price'] = $result->getPrice() ?? '';
        }
        $form = $this
            ->createForm(SellingForm::class, $data)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user = $this->getUser();
                if (!$user) {
                    throw new Exception('Please sign in');
                }

                $em->transactional(function () use ($form, $propertyService, $bookingService, $user) {
                    $postcode = $form->get('postcode')->getData();
                    $town = $form->get('town')->getData();
                    $street = $form->get('street')->getData();
                    $county = $form->get('county')->getData();
                    $agent = $form->get('agent')->getData();
                    $amount = $form->get('price')->getData();
                    $bookingDate = $form->get('view')->get('date')->getData();
                    $bookingTime = $form->get('view')->get('time')->getData();

                    $address = new Address();
                    $address
                        ->setPostcode($postcode)
                        ->setAddressLine1($street)
                        ->setAddressLine2($county)
                        ->setCountry('UK')
                        ->setCity($town);

                    $property = new Property();
                    $property
                        ->setPropertyType(PropertyType::RESIDENTIAL())
                        ->setPrice(new Money($amount))
                        ->setName($address->__toString())
                        ->setOwner($user)
                        ->setDescription('')
                        ->setAgent($agent)
                        ->setAddress($address);
                    $propertyService->save($property);
                    $bookingService->add($user, $property, $bookingDate, $bookingTime);
                });

                $this->addFlash('success', 'Property registered successfuly. A view request was booked. Please wait to agent contact you.');

                return $this->redirectToRoute('app_selling_index');
            } catch (Throwable $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/selling/index.html.twig', [
            'page' => $page,
            'result' => $result,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/save", name="save", methods={"POST"})
     */
    public function bookingSave(Request $request, UserService $userService,AgentService $agentService,PropertyService $propertyService,BookingService $bookingService, UserPasswordEncoderInterface $passwordEncoder){
        $field = $request->get('field');

        if($field == 'login'){
            $email = $request->get('email');
            $pass = $request->get('password');
            $userRepo = $userService->getRepository();
            $user = $userRepo->findOneBy(['email' => $email]);
            if (!$user != null)
                return $this->json(['result' => false]);
            $success = $passwordEncoder->isPasswordValid($user, $pass);
            if (!$success)
                return $this->json(['result' => false,'msg' => 'Not found account']);

        } else if($field == 'register'){
            $result = $userService->create($request,$passwordEncoder);
            if($result['result'] === false)
                return $this->json(['result' => false,'msg' => $result['msg'] ]);
            $user = $result['msg'];
        } else
            $user = $this->getUser();

        try {
            $em->transactional(function () use ($request,$propertyService,$agentService, $bookingService, $user) {
                $bookingDate = $request->get('date');
                $bookingTime = $request->get('hour');
                $agentId = $request->get('agent');
                $agent = $agentService->getById($agentId);

                $address = new Address();
                $address
                    ->setPostcode($request->get('code'))
                    ->setAddressLine1($request->get('addr'))
                    ->setAddressLine2($request->get('county'))
                    ->setCountry('UK')
                    ->setCity($request->get('city'));

                $property = new Property();
                $property
                    ->setPropertyType(PropertyType::RESIDENTIAL())
                    ->setPrice(new Money($request->get('price')))
                    ->setName($address->__toString())
                    ->setOwner($user)
                    ->setDescription('')
                    ->setAgent($agent)
                    ->setAddress($address);
                $propertyService->save($property);
                $bookingService->add($user, $property, $bookingDate, $bookingTime);
            });
            return $this->json(['result' => true,'msg' => 'You booking was successful.Please access you Portal for all future communications.']);

        } catch (Throwable $e) {
            return $this->json(['result' => false,'msg' => $e->getMessage()]);
        }



    }


}
