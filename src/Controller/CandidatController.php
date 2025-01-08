<?php

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Entity\Candidat;
use AcMarche\MeriteSportif\Form\CandidatType;
use AcMarche\MeriteSportif\Form\SearchCandidatType;
use AcMarche\MeriteSportif\Repository\CandidatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/candidat')]
#[IsGranted('ROLE_MERITE_ADMIN')]
class CandidatController extends AbstractController
{
    public function __construct(private readonly CandidatRepository $candidatRepository) {}

    #[Route(path: '/', name: 'candidat_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchCandidatType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $candidats = $this->candidatRepository->search($data['nom'], $data['sport'], $data['categorie']);
        } else {
            $candidats = $this->candidatRepository->getAll();
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheMeriteSportif/candidat/index.html.twig',
            [
                'candidats' => $candidats,
                'form' => $form->createView(),
            ]
            , $response,
        );
    }

    #[Route(path: '/new', name: 'candidat_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $candidat = new Candidat();

        $form = $this->createForm(CandidatType::class, $candidat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $candidat->setUuid($candidat->generateUuid());
            $this->candidatRepository->persist($candidat);
            $this->candidatRepository->flush();

            $this->addFlash('success', 'Candidat ajouté');

            return $this->redirectToRoute('candidat_index');
        }

        return $this->render(
            '@AcMarcheMeriteSportif/candidat/new.html.twig',
            [
                'candidat' => $candidat,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{uuid}', name: 'candidat_show', methods: ['GET'])]
    public function show(Candidat $candidat): Response
    {
        return $this->render(
            '@AcMarcheMeriteSportif/candidat/show.html.twig',
            [
                'candidat' => $candidat,
            ],
        );
    }

    #[Route(path: '/{uuid}/edit', name: 'candidat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidat $candidat): Response
    {
        $form = $this->createForm(CandidatType::class, $candidat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->candidatRepository->flush();

            $this->addFlash('success', 'Candidat modifié');

            return $this->redirectToRoute('candidat_show', ['uuid' => $candidat->getUuid()]);
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheMeriteSportif/candidat/edit.html.twig',
            [
                'candidat' => $candidat,
                'form' => $form->createView(),
            ]
            , $response,
        );
    }

    #[Route(path: '/{uuid}', name: 'candidat_delete', methods: ['POST'])]
    public function delete(Request $request, Candidat $candidat): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$candidat->getId(), $request->request->get('_token'))) {
            $this->candidatRepository->remove($candidat);
            $this->candidatRepository->flush();
            $this->addFlash('success', 'Candidat supprimé');
        }

        return $this->redirectToRoute('candidat_index');
    }
}
