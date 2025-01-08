<?php

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Entity\Token;
use AcMarche\MeriteSportif\Repository\SettingRepository;
use AcMarche\MeriteSportif\Setting\SettingEnum;
use AcMarche\MeriteSportif\Token\TokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/token')]
class TokenController extends AbstractController
{
    public function __construct(
        private readonly TokenManager $tokenManager,
        private readonly SettingRepository $settingRepository,
    ) {}

    #[Route(path: '/', name: 'merite_token_create')]
    #[IsGranted('ROLE_MERITE_ADMIN')]
    public function index(): RedirectResponse
    {
        if ($this->getUser()->getId() === 1) {
            $this->tokenManager->createForAllUsers();
            $this->addFlash('success', 'Les tokens ont bien été générés');
        } else {
            $this->addFlash('danger', 'Seul jf peut faire ça ;-)');
        }

        return $this->redirectToRoute('merite_user_index');
    }

    #[Route(path: '/{value}', name: 'merite_token_show')]
    public function show(Request $request, Token $token): RedirectResponse
    {
        $setting = $this->settingRepository->findOne();
        if ($this->tokenManager->isExpired($token)) {
            $this->addFlash('danger', "Cette url a expirée");

            return $this->redirectToRoute('merite_home');
        }

        $user = $token->getUser();
        $this->tokenManager->loginUser($request, $user, 'main');

        if ($setting->mode === SettingEnum::MODE_VOTE) {
            return $this->redirectToRoute('vote_intro');
        }

        return $this->redirectToRoute('proposition_index');
    }
}
