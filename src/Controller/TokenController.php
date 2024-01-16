<?php

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Entity\Token;
use AcMarche\MeriteSportif\Token\TokenManager;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TokenController
 */
#[Route(path: '/token')]
class TokenController extends AbstractController
{
    public function __construct(private TokenManager $tokenManager, private ParameterBagInterface $parameterBag)
    {
    }

    #[Route(path: '/', name: 'merite_token_create')]
    #[IsGranted('ROLE_MERITE_ADMIN')]
    public function index(): RedirectResponse
    {
        $this->tokenManager->createForAllUsers();
        $this->addFlash('success', 'Les tokens ont bien été générés');

        return $this->redirectToRoute('merite_user_index');
    }

    #[Route(path: '/{value}', name: 'merite_token_show')]
    public function show(Request $request, Token $token): RedirectResponse
    {
        if ($this->tokenManager->isExpired($token)) {
            $this->addFlash('danger', "Cette url a expirée");

            return $this->redirectToRoute('merite_home');
        }
        $user = $token->getUser();
        $this->tokenManager->loginUser($request, $user, 'main');

        if ($this->parameterBag->get('merite.vote_activate') == "true") {
            return $this->redirectToRoute('vote_intro');
        }

        return $this->redirectToRoute('proposition_index');
    }
}
