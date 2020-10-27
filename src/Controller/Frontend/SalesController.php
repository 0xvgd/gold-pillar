<?php

namespace App\Controller\Frontend;

use App\Entity\Resource\Property;
use App\Enum\PostStatus;
use App\Enum\PropertyStatus;
use App\Form\SearchProductType;
use App\Repository\PageRepository;
use App\Repository\Sales\PropertyRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/sales",
 *  name="app_sales_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class SalesController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PageRepository $repository)
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchProductType::class)
            ->getForm();

        $page = $repository->findOneBy(['name' => 'sales']);

        return $this->render('frontend/sales/index.html.twig', [
            'page' => $page,
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search")
     */
    public function searchAction(Request $request)
    {
        $data = null;
        $searchForm = $request->get('form');

        $form = $this
            ->createFormBuilder()
            ->create('', SearchProductType::class)
            ->getForm();

        parse_str($searchForm, $data);

        $form->submit($data);

        $em = $this->getDoctrine()->getManager();
        $params = [];
        try {
            $data = null;

            $params['approved'] = PostStatus::APPROVED();
            $params['forSale'] = PropertyStatus::FOR_SALE();
            $params['sold'] = PropertyStatus::SOLD();

            $qb = $em
                ->createQueryBuilder()
                ->select('p')
                ->from(Property::class, 'p')
                ->andWhere('p.postStatus = :approved')
                ->andWhere('p.propertyStatus = :forSale or p.propertyStatus = :sold');

            $location = $form->get('location')->getData();

            if (!empty($location)) {
                $qb->andWhere('p.address.postcode like :location or p.address.city like :location');
                $params['location'] = "{$location}%";
            }

            $propertyType = $form->get('propertyType')->getData();

            if ($propertyType) {
                $qb->andWhere('p.propertyType = :propertyType');
                $params['propertyType'] = $propertyType;
            }

            $maxResults = (int) $request->get('length');
            if ($maxResults <= 0 || $maxResults > 1000) {
                $maxResults = 10;
            }

            $offset = (int) $request->get('start');
            if ($offset < 0) {
                $offset = 0;
            }

            $result = $qb
                ->setParameters($params)
                ->getQuery()
                ->setFirstResult($offset)
                ->setMaxResults($maxResults)
                ->getResult();

            $query = $qb->select('p')->getQuery();
            $paginator = new Paginator($query, true);
            $total = sizeof($paginator);

            $content = [
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $result,
            ];
        } catch (Throwable $ex) {
            $content = [
                'error' => $ex->getMessage(),
            ];
        }

        return $this->json($content, 200, [], ['groups' => 'list']);
    }

    /**
     * @Route("/{slug}/view", name="view")
     */
    public function view(PropertyRepository $repository, $slug)
    {
        $property = $repository->findOneBy([
            'slug' => $slug,
        ]);

        return $this->render('frontend/sales/view.html.twig', [
            'entity' => $property,
        ]);
    }
}
