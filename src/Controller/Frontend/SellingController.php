<?php

namespace App\Controller\Frontend;

use App\Entity\Address;
use App\Entity\Helper\PricePaidIndex;
use App\Entity\Money;
use App\Entity\Resource\Property;
use App\Enum\PropertyType;
use App\Form\Frontend\SellingForm;
use App\Repository\PageRepository;
use App\Service\BookingService;
use App\Service\HousePriceIndex\ClientInterface;
use App\Service\Property\PropertyService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
    public function index(
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
}
