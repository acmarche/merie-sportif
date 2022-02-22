<?php


namespace AcMarche\MeriteSportif\Service;


use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Repository\CandidatRepository;
use AcMarche\MeriteSportif\Repository\CategorieRepository;

class PropositionService
{
    public function __construct(private CategorieRepository $categorieRepository, private CandidatRepository $candidatRepository)
    {
    }

    public function isComplete(Club $club): bool
    {
        $count = 0;
        $categories = $this->categorieRepository->findAll();

        foreach ($categories as $categorie) {
            $candidat = $this->candidatRepository->isAlreadyProposed($club, $categorie);
            if ($candidat !== null) {
                $count++;
            }
        }
        return $count === count($categories);
    }


}