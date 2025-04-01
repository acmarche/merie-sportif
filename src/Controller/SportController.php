<?php

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Entity\Sport;
use AcMarche\MeriteSportif\Form\SportType;
use AcMarche\MeriteSportif\Repository\SportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/sport')]
#[IsGranted('ROLE_MERITE_ADMIN')]
class SportController extends AbstractController
{
    public function __construct(private readonly SportRepository $sportRepository) {}

    #[Route(path: '/', name: 'sport_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMeriteSportif/sport/index.html.twig',
            [
                'sports' => $this->sportRepository->getAll(),
            ],
        );
    }

    #[Route(path: '/new', name: 'sport_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $sport = new Sport();
        $form = $this->createForm(SportType::class, $sport);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->sportRepository->persist($sport);
            $this->sportRepository->flush();
            $this->addFlash('success', 'Le sport a bien été ajouté');

            return $this->redirectToRoute('sport_index');
        }

        return $this->render(
            '@AcMarcheMeriteSportif/sport/new.html.twig',
            [
                'sport' => $sport,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}', name: 'sport_show', methods: ['GET'])]
    public function show(Sport $sport): Response
    {
        return $this->render(
            '@AcMarcheMeriteSportif/sport/show.html.twig',
            [
                'sport' => $sport,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'sport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sport $sport): Response
    {
        $form = $this->createForm(SportType::class, $sport);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->sportRepository->flush();

            $this->addFlash('success', 'Le sport a bien été modifié');
            return $this->redirectToRoute('sport_index');
        }

        return $this->render(
            '@AcMarcheMeriteSportif/sport/edit.html.twig',
            [
                'sport' => $sport,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}', name: 'sport_delete', methods: ['POST'])]
    public function delete(Request $request, Sport $sport): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$sport->getId(), $request->request->get('_token'))) {
            $this->sportRepository->remove($sport);
            $this->sportRepository->flush();
            $this->addFlash('success', 'Le sport a bien été supprimé');
        }

        return $this->redirectToRoute('sport_index');
    }
}
