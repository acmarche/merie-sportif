<?php

namespace AcMarche\MeriteSportif\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use AcMarche\MeriteSportif\Entity\Token;
use AcMarche\MeriteSportif\Service\TokenManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TokenController
 */
#[Route(path: '/token')]
class TokenController extends AbstractController
{
    public function __construct(private TokenManager $tokenManager)
    {
    }
    #[Route(path: '/', name: 'merite_token_create')]
    #[IsGranted('ROLE_MERITE_ADMIN')]
    public function index() : RedirectResponse
    {
        //  $this->tokenManager->createForAllUsers();
        $this->addFlash('success', 'Les tokens ont bien été générés');
        return $this->redirectToRoute('merite_user_index');
    }
    #[Route(path: '/{value}', name: 'app_token_show')]
    public function show(Request $request, Token $token) : RedirectResponse
    {
        if ($this->tokenManager->isExpired($token)) {
            $this->addFlash('danger', "Cette url a expirée");

            return $this->redirectToRoute('merite_home');
        }
        $user = $token->getUser();
        $this->tokenManager->loginUser($request, $user, 'main');
        return $this->redirectToRoute('vote_intro');
        //        return $this->redirectToRoute('proposition_index');
    }
}
