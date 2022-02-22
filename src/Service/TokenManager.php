<?php

namespace AcMarche\MeriteSportif\Service;

use Exception;
use DateTime;
use AcMarche\MeriteSportif\Entity\Token;
use AcMarche\MeriteSportif\Entity\User;
use AcMarche\MeriteSportif\Repository\TokenRepository;
use AcMarche\MeriteSportif\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class TokenManager
{
    public $guardAuthenticatorHandler;
    public $appAuthenticator;


    public function __construct(

        private TokenRepository $tokenRepository,
        private UserRepository $userRepository
    ) {
    //    $this->guardAuthenticatorHandler = $guardAuthenticatorHandler;
    //    $this->appAuthenticator = $appAuthenticator;
    }

    public function getInstance(User $user): Token
    {
        if (($token = $this->tokenRepository->findOneBy(['user' => $user])) === null) {
            $token = new Token();
            $token->setUser($user);
            $this->tokenRepository->persist($token);
        }

        return $token;
    }

    public function generate(User $user)
    {
        $token = $this->getInstance($user);
        try {
            $token->setValue(bin2hex(random_bytes(20)));
        } catch (Exception) {
        }

        $expireTime = new DateTime('+90 day');
        $token->setExpireAt($expireTime);

        $this->tokenRepository->save();

        return $token;
    }

    public function isExpired(Token $token): bool
    {
        $today = new DateTime('today');

        return $today > $token->getExpireAt();
    }

    public function createForAllUsers(): void
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->generate($user);
        }
    }

    public function loginUser(Request $request, User $user, $firewallName): void
    {
        $this->guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $this->appAuthenticator,
            $firewallName
        );
    }
}