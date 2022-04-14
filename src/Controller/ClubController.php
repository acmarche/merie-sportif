<?php

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Form\ClubType;
use AcMarche\MeriteSportif\Repository\ClubRepository;
use AcMarche\MeriteSportif\Service\UserService;
use AcMarche\MeriteSportif\Service\VoteService;
use AcMarche\MeriteSportif\Token\TokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/club')]
#[IsGranted('ROLE_MERITE_ADMIN')]
class ClubController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClubRepository $clubRepository,
        private VoteService $voteService,
        private UserService $userService,
        private TokenManager $tokenManager
    ) {
    }

    #[Route(path: '/', name: 'club_index', methods: ['GET'])]
    #[IsGranted('ROLE_MERITE_ADMIN')]
    public function index(): Response
    {
        $clubs = $this->clubRepository->getAll();
        $this->voteService->setIsComplete($clubs);

        return $this->render('@AcMarcheMeriteSportif/club/index.html.twig',
            [
                'clubs' => $clubs,
            ]
        );
    }

    #[Route(path: '/new', name: 'club_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $club = new Club();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($club);
            $user = $this->userService->createUser($club);
            $this->tokenManager->generate($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Club ajouté');

            return $this->redirectToRoute('club_index');
        }

        return $this->render('@AcMarcheMeriteSportif/club/new.html.twig',
            [
                'club' => $club,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'club_show', methods: ['GET'])]
    public function show(Club $club): Response
    {
        $votes = $this->voteService->getVotesByClub($club);
        $isComplete = $this->voteService->isComplete($club);

        return $this->render('@AcMarcheMeriteSportif/club/show.html.twig',
            [
                'club' => $club,
                'votes' => $votes,
                'voteIsComplete' => $isComplete,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'club_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Club $club): Response
    {
        $oldEmail = $club->getEmail();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();
            if ($club->getEmail() !== $oldEmail) {
                $user = $club->getUser();
                if ($user !== null) {
                    $user->setUsername($club->getEmail());
                }
            }

            $this->addFlash('success', 'Club modifié');

            //    return $this->redirectToRoute('club_index');
        }

        return $this->render('@AcMarcheMeriteSportif/club/edit.html.twig',
            [
                'club' => $club,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'club_delete', methods: ['DELETE'])]
    public function delete(Request $request, Club $club): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$club->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($club);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('club_index');
    }
}
