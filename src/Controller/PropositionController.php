<?php

namespace AcMarche\MeriteSportif\Controller;

use Doctrine\Persistence\ManagerRegistry;
use AcMarche\MeriteSportif\Entity\Candidat;
use AcMarche\MeriteSportif\Entity\Categorie;
use AcMarche\MeriteSportif\Form\CandidatType;
use AcMarche\MeriteSportif\Form\PropositionType;
use AcMarche\MeriteSportif\Repository\CandidatRepository;
use AcMarche\MeriteSportif\Repository\CategorieRepository;
use AcMarche\MeriteSportif\Service\Mailer;
use AcMarche\MeriteSportif\Service\PropositionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/proposition')]
#[IsGranted('ROLE_MERITE_CLUB')]
class PropositionController extends AbstractController
{
    public function __construct(private CategorieRepository $categorieRepository, private CandidatRepository $candidatRepository, private Mailer $mailer, private PropositionService $propositionService, private ParameterBagInterface $parameterBag, private ManagerRegistry $managerRegistry)
    {
    }
    #[Route(path: '/', name: 'proposition_index', methods: ['GET'])]
    public function index(CandidatRepository $candidatRepository) : Response
    {
        $user = $this->getUser();
        $club = $user->getClub();
        $categories = $this->categorieRepository->findAll();
        foreach ($categories as $categorie) {
            $candidat = $this->candidatRepository->isAlreadyProposed($club, $categorie);
            if ($candidat !== null) {
                $categorie->setComplete(true);
                $categorie->setProposition($candidat->getId());
            }
        }
        $complete = $this->propositionService->isComplete($club);
        return $this->render('@AcMarcheMeriteSportif/proposition/index.html.twig',
            [
                'categories' => $categories,
                'complete' => $complete
            ]
        );
    }
    #[Route(path: '/new/{id}', name: 'proposition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Categorie $categorie) : Response
    {
        if ($this->parameterBag->get('merite.proposition_activate') == false) {
            $this->addFlash('warning', 'Les propositions sont clôturées');

            return $this->redirectToRoute('proposition_index');
        }
        $user = $this->getUser();
        $club = $user->getClub();
        if ($this->candidatRepository->isAlreadyProposed($club, $categorie) !== null) {
            $this->addFlash('warning', 'Vous avez déjà proposé un candidat pour cette catégorie');

            return $this->redirectToRoute('proposition_index');
        }
        $candidat = new Candidat();
        $candidat->setValidate(false);
        $candidat->setAddBy($club->getEmail());
        $candidat->setCategorie($categorie);
        $form = $this->createForm(PropositionType::class, $candidat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->persist($candidat);
            $entityManager->flush();

            $this->addFlash('success', 'Le candidat a bien été proposé');

            try {
                $this->mailer->newPropositionMessage($candidat, $club);
            } catch (TransportExceptionInterface) {

            }

            if ($this->propositionService->isComplete($club)) {
                try {
                    $this->mailer->propositionFinish($club);
                } catch (TransportExceptionInterface) {
                    $this->addFlash('danger', 'Le mail de résumé n\'a pas été envoyé');
                }
            }

            return $this->redirectToRoute('proposition_index');
        }
        return $this->render('@AcMarcheMeriteSportif/proposition/new.html.twig',
            [
                'categorie' => $categorie,
                'candidat' => $candidat,
                'form' => $form->createView(),
            ]
        );
    }
    /**
     * @Security("is_granted('CANDIDAT_EDIT', candidat)")
     */
    #[Route(path: '/{id}', name: 'proposition_show', methods: ['GET'])]
    public function show(Candidat $candidat) : Response
    {
        return $this->render('@AcMarcheMeriteSportif/proposition/show.html.twig',
            [
                'candidat' => $candidat,
            ]
        );
    }
    /**
     * @Security("is_granted('CANDIDAT_EDIT', candidat)")
     */
    #[Route(path: '/{id}/edit', name: 'proposition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidat $candidat) : Response
    {
        if ($this->parameterBag->get('merite.proposition_activate') == false) {
            $this->addFlash('warning', 'Les propositions sont clôturées');

            return $this->redirectToRoute('proposition_index');
        }
        $form = $this->createForm(PropositionType::class, $candidat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->managerRegistry->getManager()->flush();

            $this->addFlash('success', 'Le candidat a bien été modifié');

            return $this->redirectToRoute('proposition_index');
        }
        return $this->render('@AcMarcheMeriteSportif/proposition/edit.html.twig',
            [
                'candidat' => $candidat,
                'form' => $form->createView(),
            ]
        );
    }
}
