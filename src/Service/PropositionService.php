<?php


namespace AcMarche\MeriteSportif\Service;


use AcMarche\MeriteSportif\Entity\Candidat;
use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Repository\CandidatRepository;
use AcMarche\MeriteSportif\Repository\CategorieRepository;

class PropositionService
{
    public function __construct(
        private readonly CategorieRepository $categorieRepository,
        private readonly CandidatRepository $candidatRepository,
    ) {}

    public function isComplete(Club $club): bool
    {
        $count = 0;
        $categories = $this->categorieRepository->findAll();

        foreach ($categories as $category) {
            $candidat = $this->candidatRepository->isAlreadyProposed($club, $category);
            if ($candidat instanceof Candidat) {
                ++$count;
            }
        }

        return $count === count($categories);
    }

    public function countPropo(Club $club): string
    {
        $count = 0;
        $categories = $this->categorieRepository->findAll();

        foreach ($categories as $category) {
            $candidat = $this->candidatRepository->isAlreadyProposed($club, $category);
            if ($candidat instanceof Candidat) {
                ++$count;
            }
        }

        return $count."/".count($categories);
    }
}