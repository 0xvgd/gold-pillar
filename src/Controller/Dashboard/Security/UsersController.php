<?php

namespace App\Controller\Dashboard\Security;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Security\User as Entity;
use App\Form\Security\SearchUserType;
use App\Form\Security\UserType;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Throwable;

/**
 * @Route(
 *  "/{_locale}/dashboard/security/users",
 *  name="security_security_users_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class UsersController extends AbstractController
{
    use DatatableTrait;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        $form = $this
            ->createFormBuilder()
            ->create('', SearchUserType::class)
            ->getForm();

        return $this->render('dashboard/security/users/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(Request $request, UserRepository $repository)
    {
        $search = $request->get('search');

        if (!is_array($search)) {
            $search = [
                'basic' => [],
            ];
        }

        $qb = $repository->createQueryBuilder('u');

        // filtro principal
        if (isset($search['basic'])) {
            parse_str($search['basic'], $basic);

            $name = $basic['name'];

            if (!empty($name)) {
                $qb
                    ->andWhere('LOWER(u.name) LIKE :name')
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
    public function add(Request $request, UserService $service)
    {
        $entity = new Entity();

        return $this->form($request, $service, $entity);
    }

    /**
     * @Route("/{id}", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, UserService $service, Entity $entity)
    {
        return $this->form($request, $service, $entity);
    }

    private function form(Request $request, UserService $service, Entity $entity)
    {
        $errors = null;

        $form = $this
            ->createForm(UserType::class, $entity)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $plainPass = substr(strtoupper(sha1(random_bytes(6))), 0, 6);

                $encodedPass = $this
                    ->encoder
                    ->encodePassword($entity, $plainPass);

                $entity->setPassword($encodedPass);

                $service->save($entity);

                $this->addFlash('success', 'User saved successfully.');

                return $this->redirectToRoute('security_security_users_edit', [
                    'id' => $entity->getId(),
                ]);
            } catch (Throwable $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('dashboard/security/users/form.html.twig', [
            'entity' => $entity,
            'errors' => $errors,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function remove(Request $request, Entity $entity)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($entity);
        $em->flush();

        $this->addFlash('success', 'User deleted successfully');

        return $this->redirectToRoute('security_security_users_index');
    }
}
