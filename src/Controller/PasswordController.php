<?php

namespace AcMarche\MeriteSportif\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use AcMarche\MeriteSportif\Entity\User;
use AcMarche\MeriteSportif\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/password')]
#[IsGranted('ROLE_MERITE_ADMIN')]
class PasswordController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $userPasswordEncoder)
    {
    }
    #[Route(path: '/{id}', name: 'merite_user_password')]
    public function edit(Request $request, User $user) : RedirectResponse|Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $password = $data->getPassword();
            $user->setPassword($this->userPasswordEncoder->hashPassword($user, $password));
            $this->entityManager->flush();

            return $this->redirectToRoute(
                'merite_user_show',
                ['id' => $user->getId()]
            );
        }
        return $this->render('@AcMarcheMeriteSportif/user/edit_password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
}
