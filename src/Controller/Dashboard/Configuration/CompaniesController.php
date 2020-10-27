<?php

namespace App\Controller\Dashboard\Configuration;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Company\Company as Entity;
use App\Form\Dashboard\Company\CompanyType;
use App\Form\Dashboard\Company\SearchCompanyType;
use App\Repository\CompanyRepository;
use App\Service\Company\CompanyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/dashboard/config/companies",
 *  name="dashboard_config_companies_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class CompaniesController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchCompanyType::class)
            ->getForm();

        return $this->render('dashboard/configuration/companies/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, CompanyRepository $repository)
    {
        $columns = [
            'e.name',
        ];

        $search = $this->searchValues($request);
        $qb = $repository->createQueryBuilder('e');

        $this->addOrderBy($request, $qb, $columns);

        if (isset($search['name'])) {
            $name = $search['name'];
            if (!empty($name)) {
                $qb
                    ->andWhere('LOWER(e.name) LIKE LOWER(:name)')
                    ->setParameter('name', "%{$name}%");
            }
        }

        $query = $qb->getQuery();

        return $this->dataTable($request, $query, false, [
            'groups' => 'list',
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function add(Request $request, CompanyService $service)
    {
        $entity = new Entity();

        return $this->form($request, $service, $entity);
    }

    /**
     * @Route("/{id}", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CompanyService $service, Entity $entity)
    {
        return $this->form($request, $service, $entity);
    }

    private function form(Request $request, CompanyService $service, Entity $entity)
    {
        $removeForm = null;
        $errors = null;

        $tab = $request->get('tab');

        $form = $this
            ->createForm(CompanyType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->save($entity);

                $this->addFlash('success', 'Success!');

                return $this->redirectToRoute('dashboard_config_companies_edit', [
                    'id' => $entity->getId(),
                ]);
            } catch (Throwable $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('dashboard/configuration/companies/form.html.twig', [
            'entity' => $entity,
            'errors' => $errors,
            'form' => $form->createView(),
            'removeForm' => $removeForm ? $removeForm->createView() : null,
            'tab' => $tab,
        ]);
    }
}
