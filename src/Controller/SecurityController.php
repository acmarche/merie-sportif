<?php

namespace AcMarche\MeriteSportif\Controller;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class SecurityController extends AbstractController
{
    public function __construct(
        private CsrfTokenManagerInterface $tokenManager
    ) {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request): Response
    {
        /** @var $session Session */
        $session = $request->getSession();
        $authErrorKey = SecurityRequestAttributes::AUTHENTICATION_ERROR;
        $lastUsernameKey = SecurityRequestAttributes::LAST_USERNAME;
        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }
        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);
        $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;

        return $this->renderLogin(
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'csrf_token' => $csrfToken,
            ]
        );
    }

    #[Route(path: '/login_check', name: 'app_login_check')]
    public function check(): void
    {
        throw new RuntimeException(
            'You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.'
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     */
    protected function renderLogin(array $data): Response
    {
        return $this->render('@AcMarcheMeriteSportif/security/login.html.twig', $data);
    }
}
