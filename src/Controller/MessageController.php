<?php

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Form\MessageType;
use AcMarche\MeriteSportif\Repository\ClubRepository;
use AcMarche\MeriteSportif\Service\MailerMerite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/message')]
#[IsGranted('ROLE_MERITE_ADMIN')]
class MessageController extends AbstractController
{
    public function __construct(
        private readonly MailerMerite $mailerMerite,
        private readonly ClubRepository $clubRepository,
    ) {}

    #[Route(path: '/', name: 'merite_message_index', methods: ['GET', 'POST'])]
    public function index(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(MessageType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($this->clubRepository->findAll() as $club) {
                $user = $club->getUser();
                if ($user === null) {
                    $this->addFlash('error', $club->getNom().' a pas de compte user');
                    continue;
                }

                $token = $user->getToken();
                if ($token === null) {
                    $this->addFlash('error', $club->getNom().' a pas de token');
                    continue;
                }

                $value = $token->getValue();

                $message = $this->mailerMerite->createMessage($data, $club, $value);
                try {
                    $this->mailerMerite->mailer->send($message);
                } catch (TransportExceptionInterface $e) {
                    $this->addFlash('danger', 'Le mail n\'a pas été envoyé. '.$e->getMessage());
                }
            }

            $this->addFlash('success', 'Traitement terminé');

            return $this->redirectToRoute('merite_message_index');
        }

        return $this->render(
            '@AcMarcheMeriteSportif/message/index.html.twig',
            [
                'form' => $form->createView(),
            ],
        );
    }
}