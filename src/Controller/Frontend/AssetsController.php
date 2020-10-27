<?php

namespace App\Controller\Frontend;

use App\Entity\Resource\Asset;
use App\Form\SearchProductType;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/assets",
 *  name="app_assets_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class AssetsController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(EntityManagerInterface $em, PageRepository $repository)
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchProductType::class)
            ->getForm();

        $page = $repository->findOneBy(['name' => 'assets']);

        $rs = $em
            ->createQueryBuilder()
            ->select([
                'MIN(a.marketValue.amount) as minValue',
                'MAX(a.marketValue.amount) as maxValue',
            ])
            ->from(Asset::class, 'a')
            ->getQuery()
            ->getSingleResult();

        $minValue = $rs['minValue'] ?? '';
        $maxValue = $rs['maxValue'] ?? '';

        return $this->render('frontend/assets/index.html.twig', [
            'page' => $page,
            'searchForm' => $form->createView(),
            'minValue' => $minValue,
            'maxValue' => $maxValue,
        ]);
    }

    /**
     * @Route("/search.json", name="search")
     */
    public function searchAction(Request $request)
    {
        $data = null;
        $searchForm = $request->get('form');

        $form = $this->createFormBuilder()
            ->create('', SearchProductType::class)
            ->getForm();

        parse_str($searchForm, $data);

        $form->submit($data);

        $em = $this->getDoctrine()->getManager();
        $params = [];
        try {
            $data = null;

            $qb = $em
                ->createQueryBuilder()
                ->select('a')
                ->from(Asset::class, 'a')
                ->leftJoin('a.lastEquity', 'le');

            $minValue = $form->get('minValue')->getData();

            if ($minValue) {
                $qb->andWhere('a.marketValue >= :minValue');
                $params['minValue'] = $minValue;
            }

            $maxValue = $form->get('maxValue')->getData();

            if ($maxValue) {
                $qb->andWhere('a.marketValue <= :maxValue');
                $params['maxValue'] = $maxValue;
            }

            $propertyType = $form->get('propertyType')->getData();

            if ($propertyType) {
                $qb->andWhere('a.assetType = :propertyType');
                $params['propertyType'] = $propertyType;
            }

            $location = $form->get('location')->getData();

            if (!empty($location)) {
                $qb->andWhere('a.address.postcode like :location or a.address.city like :location');
                $params['location'] = "{$location}%";
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

            $query = $qb->select('a')->getQuery();
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
    public function view($slug = null)
    {
        $assetRepository = $this->getDoctrine()->getManager()->getRepository(Asset::class);
        $asset = $assetRepository->findOneBy([
            'slug' => $slug,
        ]);

        return $this->render('frontend/assets/view.html.twig', [
            'entity' => $asset,
        ]);
    }
}
