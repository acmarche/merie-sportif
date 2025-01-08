<?php

namespace AcMarche\MeriteSportif\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use AcMarche\MeriteSportif\Entity\User;
use AcMarche\MeriteSportif\Form\UserEditType;
use AcMarche\MeriteSportif\Form\UserType;
use AcMarche\MeriteSportif\Repository\UserRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/user')]
#[IsGranted('ROLE_MERITE_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    #[Route(path: '/', name: 'merite_user_index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();
        return $this->render('@AcMarcheMeriteSportif/user/index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    #[Route(path: '/new', name: 'merite_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $this->userPasswordHasher->hashPassword($user, $user->getPassword())
            );
            $this->userRepository->persist($user);
            $this->userRepository->flush();

            return $this->redirectToRoute('merite_user_show', ['id' => $user->getId()]);
        }

        return $this->render('@AcMarcheMeriteSportif/user/new.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'merite_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('@AcMarcheMeriteSportif/user/show.html.twig',
            [
                'user' => $user,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'merite_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->flush();

            return $this->redirectToRoute(
                'merite_user_show',
                ['id' => $user->getId()]
            );
        }

        return $this->render('@AcMarcheMeriteSportif/user/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'merite_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->userRepository->remove($user);
            $this->userRepository->flush();
        }

        return $this->redirectToRoute('merite_user_index');
    }
}
