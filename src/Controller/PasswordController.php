<?php

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Entity\User;
use AcMarche\MeriteSportif\Form\UserPasswordType;
use AcMarche\MeriteSportif\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/password')]
#[IsGranted('ROLE_MERITE_ADMIN')]
class PasswordController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {}

    #[Route(path: '/{id}', name: 'merite_user_password')]
    public function edit(Request $request, User $user): RedirectResponse|Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $password = $data->getPassword();
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
            $this->userRepository->flush();

            return $this->redirectToRoute(
                'merite_user_show',
                ['id' => $user->getId()],
            );
        }

        return $this->render(
            '@AcMarcheMeriteSportif/user/edit_password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ],
        );
    }
}
