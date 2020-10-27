<?php

namespace App\Controller\Dashboard;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Person\Contractor as Entity;
use App\Form\Dashboard\ContractorType;
use App\Form\Dashboard\SearchContractorType;
use App\Repository\Person\ContractorRepository;
use App\Service\ContractorService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/contractors",
 *  name="dashboard_contractors_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class ContractorsController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchContractorType::class)
            ->getForm();

        return $this->render('dashboard/contractors/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, ContractorRepository $repository)
    {
        $columns = [
            'e.id',
            'u.name',
            'u.email',
        ];

        $search = $this->searchValues($request);
        $qb = $repository
            ->createQueryBuilder('e')
            ->join('e.user', 'u');

        $this->addOrderBy($request, $qb, $columns);

        if (isset($search['email'])) {
            $email = $search['email'];
            if (!empty($email)) {
                $qb
                    ->andWhere('LOWER(u.name) LIKE LOWER(:email)')
                    ->setParameter('email', $email);
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
    public function add(Request $request, ContractorService $service)
    {
        $entity = new Entity();

        return $this->form($request, $service, $entity);
    }

    /**
     * @Route("/{id}", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, ContractorService $service, Entity $entity)
    {
        return $this->form($request, $service, $entity);
    }

    private function form(Request $request, ContractorService $service, Entity $entity)
    {
        $errors = null;

        $form = $this
            ->createForm(ContractorType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->save($entity);

                $this->addFlash('success', 'Item saved successfully.');

                return $this->redirectToRoute('dashboard_contractors_edit', [
                    'id' => $entity->getId(),
                ]);
            } catch (Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('dashboard/contractors/form.html.twig', [
            'entity' => $entity,
            'errors' => $errors,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function remove(Request $request, Entity $entity)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($entity);
        $em->flush();

        $this->addFlash('success', 'Item deleted successfully.');

        return $this->redirectToRoute('dashboard_contractors_index');
    }
}
