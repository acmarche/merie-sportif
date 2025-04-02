<?php

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Entity\Candidat;
use AcMarche\MeriteSportif\Entity\Categorie;
use AcMarche\MeriteSportif\Form\PropositionType;
use AcMarche\MeriteSportif\Repository\CandidatRepository;
use AcMarche\MeriteSportif\Repository\CategorieRepository;
use AcMarche\MeriteSportif\Repository\SettingRepository;
use AcMarche\MeriteSportif\Service\MailerMerite;
use AcMarche\MeriteSportif\Service\PropositionService;
use AcMarche\MeriteSportif\Setting\SettingEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/proposition')]
#[IsGranted('ROLE_MERITE_CLUB')]
class PropositionController extends AbstractController
{
    public function __construct(
        private readonly CategorieRepository $categorieRepository,
        private readonly CandidatRepository $candidatRepository,
        private readonly MailerMerite $mailer,
        private readonly PropositionService $propositionService,
        private readonly SettingRepository $settingRepository,
    ) {}

    #[Route(path: '/', name: 'proposition_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $club = $user->getClub();
        $categories = $this->categorieRepository->findAll();
        foreach ($categories as $category) {
            $candidat = $this->candidatRepository->isAlreadyProposed($club, $category);
            if ($candidat instanceof Candidat) {
                $category->setComplete(true);
                $category->setProposition($candidat->getId());
            }
        }

        $complete = $this->propositionService->isComplete($club);
        $count = $this->propositionService->countPropo($club);

        return $this->render(
            '@AcMarcheMeriteSportif/proposition/index.html.twig',
            [
                'categories' => $categories,
                'complete' => $complete,
                'count' => $count,
            ],
        );
    }

    #[Route(path: '/new/{id}', name: 'proposition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Categorie $categorie): Response
    {
        $setting = $this->settingRepository->findOne();
        if ($setting->mode === SettingEnum::MODE_VOTE->value) {
            $this->addFlash('warning', 'Les propositions sont clôturées');

            return $this->redirectToRoute('proposition_index');
        }

        $user = $this->getUser();
        $club = $user->getClub();
        if ($this->candidatRepository->isAlreadyProposed($club, $categorie) instanceof Candidat) {
            $this->addFlash('warning', 'Vous avez déjà proposé un candidat pour cette catégorie');

            return $this->redirectToRoute('proposition_index');
        }

        $candidat = new Candidat();
        $candidat->setUuid($candidat->generateUuid());
        $candidat->setValidate(false);
        $candidat->setAddBy($club->getEmail());
        $candidat->setCategorie($categorie);

        $form = $this->createForm(PropositionType::class, $candidat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->candidatRepository->persist($candidat);
                $this->candidatRepository->flush();

                $this->addFlash('success', 'Le candidat a bien été proposé');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur a eu lieu.'.$e->getMessage());

                return $this->redirectToRoute('proposition_index');
            }

            try {
                $this->mailer->newPropositionMessage($candidat, $club);
            } catch (TransportExceptionInterface|\Exception $e) {
                $this->addFlash('danger', "Erreur envoie du mail ".$e->getMessage());
            }

            if ($this->propositionService->isComplete($club)) {
                try {
                    $this->mailer->propositionFinish($club);
                } catch (TransportExceptionInterface|\Exception $e) {
                    $this->addFlash('danger', 'Le mail de résumé n\'a pas été envoyé. '.$e->getMessage());
                }
            }

            return $this->redirectToRoute('proposition_index');
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheMeriteSportif/proposition/new.html.twig',
            [
                'categorie' => $categorie,
                'candidat' => $candidat,
                'form' => $form,
            ],
            $response,
        );
    }

    #[IsGranted('CANDIDAT_EDIT', subject: 'candidat')]
    #[Route(path: '/{id}', name: 'proposition_show', methods: ['GET'])]
    public function show(Candidat $candidat): Response
    {
        return $this->render(
            '@AcMarcheMeriteSportif/proposition/show.html.twig',
            [
                'candidat' => $candidat,
            ],
        );
    }

    #[IsGranted('CANDIDAT_EDIT', subject: 'candidat')]
    #[Route(path: '/{id}/edit', name: 'proposition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidat $candidat): Response
    {
        $setting = $this->settingRepository->findOne();
        if ($setting->mode === SettingEnum::MODE_VOTE->value) {
            $this->addFlash('warning', 'Les propositions sont clôturées');

            return $this->redirectToRoute('proposition_index');
        }

        $form = $this->createForm(PropositionType::class, $candidat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $this->candidatRepository->persist($candidat);
                $this->candidatRepository->flush();

                $this->addFlash('success', 'Le candidat a bien été modifié');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur a eu lieu.'.$e->getMessage());

                return $this->redirectToRoute('proposition_index');
            }

            return $this->redirectToRoute('proposition_index');
        }

        return $this->render(
            '@AcMarcheMeriteSportif/proposition/edit.html.twig',
            [
                'candidat' => $candidat,
                'form' => $form,
            ],
        );
    }
}
