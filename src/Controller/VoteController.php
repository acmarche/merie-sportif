<?php

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Entity\Categorie;
use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Form\VotesType;
use AcMarche\MeriteSportif\Repository\CandidatRepository;
use AcMarche\MeriteSportif\Repository\CategorieRepository;
use AcMarche\MeriteSportif\Repository\SettingRepository;
use AcMarche\MeriteSportif\Repository\VoteRepository;
use AcMarche\MeriteSportif\Service\Mailer;
use AcMarche\MeriteSportif\Service\VoteManager;
use AcMarche\MeriteSportif\Service\VoteService;
use AcMarche\MeriteSportif\Setting\SettingEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/vote')]
#[IsGranted('ROLE_MERITE_CLUB')]
class VoteController extends AbstractController
{
    public function __construct(
        private readonly CategorieRepository $categorieRepository,
        private readonly CandidatRepository $candidatRepository,
        private readonly VoteRepository $voteRepository,
        private readonly VoteService $voteService,
        private readonly VoteManager $voteManager,
        private readonly SettingRepository $settingRepository,
        private readonly Mailer $mailer,
    ) {}

    #[Route(path: '/', name: 'vote_index', methods: ['GET'])]
    public function index(): Response
    {
        $setting = $this->settingRepository->findOne();
        if ($setting->mode === SettingEnum::MODE_PROPOSITION) {
            $this->addFlash('warning', 'Les votes ne sont pas encore ouvert');

            return $this->redirectToRoute('merite_home');
        }

        $votes = $this->voteRepository->getAll();

        return $this->render(
            '@AcMarcheMeriteSportif/vote/index.html.twig',
            [
                'votes' => $votes,
            ],
        );
    }

    #[Route(path: '/intro', name: 'vote_intro', methods: ['GET', 'POST'])]
    public function intro(): Response
    {
        $user = $this->getUser();
        $club = $user->getClub();
        $categories = $this->categorieRepository->findAll();
        foreach ($categories as $category) {
            $done = $this->voteService->voteExist($club, $category);
            $category->setComplete($done);
        }

        return $this->render(
            '@AcMarcheMeriteSportif/vote/intro.html.twig',
            [
                'club' => $club,
                'categories' => $categories,
            ],
        );
    }

    #[Route(path: '/new/{ordre}', name: 'vote_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Categorie $categorie): Response
    {
        $setting = $this->settingRepository->findOne();
        if ($setting->mode === SettingEnum::MODE_PROPOSITION) {
            $this->addFlash('warning', 'Les votes ne sont pas encore ouvert');

            return $this->redirectToRoute('merite_home');
        }

        $user = $this->getUser();
        $club = $user->getClub();
        if ($this->voteService->voteExist($club, $categorie)) {
            $this->addFlash('warning', 'Vous avez déjà voté dans cette catégorie');

            return $this->redirectToRoute('vote_intro');
        }

        $candidatures = [];
        $candidats = $this->candidatRepository->getByCategorie($categorie);
        foreach ($candidats as $candidat) {
            $candidatures[] = ['candidat' => $candidat, 'point' => 0];
        }

        $data = ['candidatures' => $candidatures];
        $form = $this->createForm(VotesType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $form->getData();

            $this->voteManager->handleVote($result, $club, $categorie);
            $this->voteRepository->flush();
            $this->addFlash('success', 'Votre vote a bien été pris en compte');

            $isComplete = $this->voteService->isComplete($club);

            if ($isComplete) {
                try {
                    $this->mailer->votesFinish($club);
                } catch (TransportExceptionInterface $e) {
                    $this->addFlash('danger', $e->getMessage());
                }

                return $this->redirectToRoute('vote_show');
            }

            return $this->redirectToRoute('vote_intro');
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheMeriteSportif/vote/new.html.twig',
            [
                'categorie' => $categorie,
                'candidats' => $categorie->getCandidats(),
                'form' => $form->createView(),
            ]
            , $response,
        );
    }

    #[Route(path: '/show', name: 'vote_show', methods: ['GET'])]
    public function show(): Response
    {
        $user = $this->getUser();
        $club = $user->getClub();
        $votes = $this->voteService->getVotesByClub($club);
        $isComplete = $this->voteService->isComplete($club);

        return $this->render(
            '@AcMarcheMeriteSportif/vote/show.html.twig',
            [
                'club' => $club,
                'votes' => $votes,
                'voteIsComplete' => $isComplete,
            ],
        );
    }

    #[Route(path: '/trier', name: 'vote_trier', methods: ['GET', 'POST'])]
    public function trier(Request $request): Response
    {
        $positions = [];
        if ($request->isXmlHttpRequest()) {
            $candidats = $request->request->get("candidats");
            if (is_array($candidats)) {
                foreach ($candidats as $candidatId) {
                    $candidat = $this->candidatRepository->find($candidatId);
                    if ($candidat !== null) {
                        $positions[] = $candidat->getId();
                    }
                }

                return new Response(implode('|', $positions));
            }
        }

        return new Response(null);
    }

    #[Route(path: '/{id}', name: 'vote_delete', methods: ['POST'])]
    #[IsGranted('ROLE_MERITE_ADMIN')]
    public function delete(Request $request, Club $club): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$club->getId(), $request->request->get('_token'))) {
            foreach ($club->getVotes() as $vote) {
                $this->voteRepository->remove($vote);
            }

            $this->voteRepository->flush();
        }

        return $this->redirectToRoute('vote_index');
    }
}
