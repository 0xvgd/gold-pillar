<?php

namespace App\Controller\Frontend;

use App\Entity\Address;
use App\Entity\Helper\PricePaidIndex;
use App\Entity\Money;
use App\Entity\Resource\Accommodation;
use App\Enum\PropertyType;
use App\Form\Frontend\LettingForm;
use App\Repository\PageRepository;
use App\Service\BookingService;
use App\Service\HousePriceIndex\ClientInterface;
use App\Service\Renting\RentingService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/letting",
 *  name="app_letting_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class LettingController extends AbstractController
{
    use AgentJsonRouteTrait;

    /**
     * @Route("/", name="index")
     */
    public function index(
        Request $request,
        PageRepository $repository,
        ClientInterface $client,
        EntityManagerInterface $em,
        RentingService $rentingService,
        BookingService $bookingService
    ) {
        $location = (string) $request->get('q');
        $page = $repository->findOneBy(['name' => 'letting']);
        $result = null;

        if ($location) {
            try {
                $result = $client->getAverageRentPrice($location);
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
            ->createForm(LettingForm::class, $data)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user = $this->getUser();
                if (!$user) {
                    throw new Exception('Please sign in');
                }

                $em->transactional(function () use ($form, $rentingService, $bookingService, $user) {
                    $postcode = $form->get('postcode')->getData();
                    $town = $form->get('town')->getData();
                    $street = $form->get('street')->getData();
                    $county = $form->get('county')->getData();
                    $agent = $form->get('agent')->getData();
                    $amount = $form->get('price')->getData();
                    $plan = $form->get('plan')->getData();
                    $bookingDate = $form->get('view')->get('date')->getData();
                    $bookingTime = $form->get('view')->get('time')->getData();

                    $address = new Address();
                    $address
                        ->setPostcode($postcode)
                        ->setAddressLine1($street)
                        ->setAddressLine2($county)
                        ->setCountry('UK')
                        ->setCity($town);

                    $property = new Accommodation();
                    $property
                        ->setDeposit(new Money(0))
                        ->setPropertyType(PropertyType::RESIDENTIAL())
                        ->setPlan($plan)
                        ->setRent(new Money($amount))
                        ->setName($address->__toString())
                        ->setOwner($user)
                        ->setDescription('')
                        ->setAgent($agent)
                        ->setAddress($address);
                    $rentingService->save($property);
                    $bookingService->add($user, $property, $bookingDate, $bookingTime);
                });

                $this->addFlash('success', 'Property registered successfuly. A view request was booked. Please wait to agent contact you.');

                return $this->redirectToRoute('app_letting_index');
            } catch (Throwable $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/letting/index.html.twig', [
            'page' => $page,
            'result' => $result,
            'form' => $form->createView(),
        ]);
    }
}
