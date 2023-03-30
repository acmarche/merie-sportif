<?php

namespace AcMarche\MeriteSportif\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use AcMarche\MeriteSportif\Entity\Candidat;
use AcMarche\MeriteSportif\Form\CandidatType;
use AcMarche\MeriteSportif\Form\SearchCandidatType;
use AcMarche\MeriteSportif\Repository\CandidatRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/candidat')]
#[IsGranted('ROLE_MERITE_ADMIN')]
class CandidatController extends AbstractController
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }

    #[Route(path: '/', name: 'candidat_index', methods: ['GET', 'POST'])]
    public function index(Request $request, CandidatRepository $candidatRepository): Response
    {
        $form = $this->createForm(SearchCandidatType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $candidats = $candidatRepository->search($data['nom'], $data['sport'], $data['categorie']);
        } else {
            $candidats = $candidatRepository->getAll();
        }

        return $this->render('@AcMarcheMeriteSportif/candidat/index.html.twig',
            [
                'candidats' => $candidats,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/new', name: 'candidat_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $candidat = new Candidat();
        $form = $this->createForm(CandidatType::class, $candidat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->persist($candidat);
            $entityManager->flush();

            $this->addFlash('success', 'Candidat ajouté');

            return $this->redirectToRoute('candidat_index');
        }

        return $this->render('@AcMarcheMeriteSportif/candidat/new.html.twig',
            [
                'candidat' => $candidat,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'candidat_show', methods: ['GET'])]
    public function show(Candidat $candidat): Response
    {
        return $this->render('@AcMarcheMeriteSportif/candidat/show.html.twig',
            [
                'candidat' => $candidat,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'candidat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidat $candidat): Response
    {
        $form = $this->createForm(CandidatType::class, $candidat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->managerRegistry->getManager()->flush();

            $this->addFlash('success', 'Candidat modifié');

            return $this->redirectToRoute('candidat_show', ['id' => $candidat->getId()]);
        }

        return $this->render('@AcMarcheMeriteSportif/candidat/edit.html.twig',
            [
                'candidat' => $candidat,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'candidat_delete', methods: ['POST'])]
    public function delete(Request $request, Candidat $candidat): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$candidat->getId(), $request->request->get('_token'))) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->remove($candidat);
            $entityManager->flush();
            $this->addFlash('success', 'Candidat supprimé');
        }

        return $this->redirectToRoute('candidat_index');
    }
}
