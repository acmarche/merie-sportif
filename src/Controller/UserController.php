<?php

namespace AcMarche\MeriteSportif\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use AcMarche\MeriteSportif\Entity\User;
use AcMarche\MeriteSportif\Form\UserEditType;
use AcMarche\MeriteSportif\Form\UserType;
use AcMarche\MeriteSportif\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/user')]
#[IsGranted('ROLE_MERITE_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $utilisateurRepository,
        private UserPasswordHasherInterface $userPasswordEncoder,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route(path: '/', name: 'merite_user_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $users = $this->utilisateurRepository->findAll();

        return $this->render('@AcMarcheMeriteSportif/user/index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    #[Route(path: '/new', name: 'merite_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $utilisateur = new User();
        $form = $this->createForm(UserType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur->setPassword(
                $this->userPasswordEncoder->hashPassword($utilisateur, $utilisateur->getPassword())
            );
            $this->entityManager->persist($utilisateur);
            $this->entityManager->flush();

            return $this->redirectToRoute('merite_user_show', ['id' => $utilisateur->getId()]);
        }

        return $this->render('@AcMarcheMeriteSportif/user/new.html.twig',
            [
                'user' => $utilisateur,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'merite_user_show', methods: ['GET'])]
    public function show(User $utilisateur): Response
    {
        return $this->render('@AcMarcheMeriteSportif/user/show.html.twig',
            [
                'user' => $utilisateur,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'merite_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $utilisateur): Response
    {
        $form = $this->createForm(UserEditType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute(
                'merite_user_show',
                ['id' => $utilisateur->getId()]
            );
        }

        return $this->render('@AcMarcheMeriteSportif/user/edit.html.twig',
            [
                'user' => $utilisateur,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'merite_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $utilisateur): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($utilisateur);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('merite_user_index');
    }
}
