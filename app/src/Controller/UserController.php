<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    private $repository;

    /**
     * Constractor of UserController.
     *
     */
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->repository = $entityManager->getRepository(User::class);
    }

    /**
     * This function will show the list of user.
     *
     * @return Symfony\Component\HttpFoundation\Response $response.
     */
    #[Route('/user', name: 'list_user', methods: ['GET'])]
    public function index()
    {
        $users = $this->repository->findAll();

        return $this->render('user.html.twig', [
            'obj' => 'get',
            'users' => $users
        ]);
    }

    /**
     * This function will create the user.
     *
     * @param Request $request data of user parameter
     *
     * @return Symfony\Component\HttpFoundation\RedirectResponse $redirectResponse.
     */
    #[Route('/user', name: 'create_user', methods: ['POST'])]
    public function store(Request $request)
    {
        $user = new User();
        $user->setId(time());
        $user->setData(
            $request->get("firstname")
                . ' - ' . $request->get("lastname")
                . ' - ' . $request->get("address")
        );
        $this->entityManager->persist($user);

        // Flush changes to the database
        $this->entityManager->flush();

        return $this->redirectToRoute('list_user');
    }

    /**
     * This function will delete the user.
     *
     * @param string $id Id of user parameter
     *
     * @return Symfony\Component\HttpFoundation\RedirectResponse $redirectResponse.
     */
    #[Route('/delete-user/{id}', name: 'delete_user', methods: ['GET'])]
    public function delete(string $id)
    {
        $user = $this->repository->find($id);

        if (!$user instanceof User) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('list_user');
    }
}
