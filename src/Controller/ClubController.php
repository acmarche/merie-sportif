<?php

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Entity\User;
use AcMarche\MeriteSportif\Form\ClubType;
use AcMarche\MeriteSportif\Repository\ClubRepository;
use AcMarche\MeriteSportif\Service\UserService;
use AcMarche\MeriteSportif\Service\VoteService;
use AcMarche\MeriteSportif\Token\TokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/club')]
#[IsGranted('ROLE_MERITE_ADMIN')]
class ClubController extends AbstractController
{
    public function __construct(
        private readonly ClubRepository $clubRepository,
        private readonly VoteService $voteService,
        private readonly UserService $userService,
        private readonly TokenManager $tokenManager,
    ) {}

    #[Route(path: '/', name: 'club_index', methods: ['GET'])]
    #[IsGranted('ROLE_MERITE_ADMIN')]
    public function index(): Response
    {
        $clubs = $this->clubRepository->getAll();
        $this->voteService->setIsComplete($clubs);

        return $this->render(
            '@AcMarcheMeriteSportif/club/index.html.twig',
            [
                'clubs' => $clubs,
            ],
        );
    }

    #[Route(path: '/new', name: 'club_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $club = new Club();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $club->setEmail(strtolower((string)$club->getEmail()));

            if ($this->clubRepository->findOneByEmail($club->getEmail()) instanceof Club) {
                $this->addFlash('danger', 'Un club a déjà cette adresse mail');

                return $this->redirectToRoute('club_new');
            }

            $this->clubRepository->persist($club);
            $user = $this->userService->createUser($club);
            $this->tokenManager->generate($user);
            $this->clubRepository->flush();

            $this->addFlash('success', 'Club ajouté');

            return $this->redirectToRoute('club_index');
        }

        return $this->render(
            '@AcMarcheMeriteSportif/club/new.html.twig',
            [
                'club' => $club,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}', name: 'club_show', methods: ['GET'])]
    public function show(Club $club): Response
    {
        $votes = $this->voteService->getVotesByClub($club);
        $isComplete = $this->voteService->isComplete($club);

        return $this->render(
            '@AcMarcheMeriteSportif/club/show.html.twig',
            [
                'club' => $club,
                'votes' => $votes,
                'voteIsComplete' => $isComplete,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'club_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Club $club): Response
    {
        $oldEmail = $club->getEmail();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $club->setEmail(strtolower((string)$club->getEmail()));
            $this->clubRepository->flush();
            if ($club->getEmail() !== $oldEmail) {
                $user = $club->getUser();
                if ($user instanceof User) {
                    $user->setUsername($club->getEmail());
                }
            }

            $this->addFlash('success', 'Club modifié');

            return $this->redirectToRoute('club_show', ['id' => $club->getId()]);
        }

        return $this->render(
            '@AcMarcheMeriteSportif/club/edit.html.twig',
            [
                'club' => $club,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}', name: 'club_delete', methods: ['POST'])]
    public function delete(Request $request, Club $club): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$club->getId(), $request->request->get('_token'))) {
            $this->clubRepository->remove($club);
            $this->clubRepository->flush();
        }

        $this->addFlash('success', 'Club supprimé');

        return $this->redirectToRoute('club_index');
    }
}
