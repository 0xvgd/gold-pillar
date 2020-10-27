<?php

namespace App\Controller\Frontend;

use App\Entity\Resource\Project;
use App\Enum\PostStatus;
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
 *  "/{_locale}/projects",
 *  name="app_projects_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class ProjectsController extends AbstractController
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

        $page = $repository->findOneBy(['name' => 'projects']);

        return $this->render('frontend/projects/index.html.twig', [
            'page' => $page,
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search")
     */
    public function searchAction(Request $request, EntityManagerInterface $em)
    {
        $searchForm = $request->get('form');

        $form = $this->createFormBuilder()
            ->create('', SearchProductType::class)
            ->getForm();

        parse_str($searchForm, $data);

        $form->submit($data);

        $params = [];
        try {
            $data = null;

            $params['approved'] = PostStatus::APPROVED();

            $qb = $em
                ->createQueryBuilder()
                ->select('p')
                ->from(Project::class, 'p')
                ->andWhere('p.postStatus = :approved');

            $propertyType = $form->get('propertyType')->getData();

            if ($propertyType) {
                $qb->andWhere('p.projectType = :propertyType');
                $params['projectType'] = $propertyType;
            }

            $location = $form->get('location')->getData();

            if (!empty($location)) {
                $qb->andWhere('p.address.postcode like :location or p.address.city like :location');
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
    public function view($slug = null)
    {
        $projectRepository = $this->getDoctrine()->getManager()->getRepository(Project::class);
        $project = $projectRepository->findOneBy([
            'slug' => $slug,
        ]);

        return $this->render('frontend/projects/view.html.twig', [
            'entity' => $project,
        ]);
    }
}
