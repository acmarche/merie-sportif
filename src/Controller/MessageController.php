<?php
/**
 * This file is part of meritesportif application
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 8/10/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Form\MessageType;
use AcMarche\MeriteSportif\Service\Mailer;
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
    public function __construct(private readonly Mailer $mailer) {}

    #[Route(path: '/', name: 'merite_message_index', methods: ['GET', 'POST'])]
    public function index(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(MessageType::class, ['from' => 'csl@marche.be']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->mailer->handle($form->getData());
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'Le mail n\'a pas été envoyé. '.$e->getMessage());
            }

            $this->addFlash('success', 'Message envoyé');

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