<?php

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Entity\Categorie;
use AcMarche\MeriteSportif\Form\CategorieType;
use AcMarche\MeriteSportif\Repository\CategorieRepository;
use AcMarche\MeriteSportif\Service\VoteService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/categorie')]
#[IsGranted('ROLE_MERITE_ADMIN')]
class CategorieController extends AbstractController
{
    public function __construct(
        private CategorieRepository $categorieRepository,
        private VoteService $voteService,
        private ManagerRegistry $managerRegistry
    ) {
    }

    #[Route(path: '/', name: 'categorie_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMeriteSportif/categorie/index.html.twig',
            [
                'categories' => $this->categorieRepository->findAll(),
            ]
        );
    }

    #[Route(path: '/new', name: 'categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render(
            '@AcMarcheMeriteSportif/categorie/new.html.twig',
            [
                'categorie' => $categorie,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        $votes = $this->voteService->getVotesByCategorie($categorie);

        return $this->render(
            '@AcMarcheMeriteSportif/categorie/show.html.twig',
            [
                'categorie' => $categorie,
                'votes' => $votes,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->managerRegistry->getManager()->flush();

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render(
            '@AcMarcheMeriteSportif/categorie/edit.html.twig',
            [
                'categorie' => $categorie,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_index');
    }
}
