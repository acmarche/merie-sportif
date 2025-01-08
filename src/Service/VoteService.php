<?php
/**
 * This file is part of meritesportif application
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 3/10/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace AcMarche\MeriteSportif\Service;


use AcMarche\MeriteSportif\Entity\Categorie;
use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Repository\CategorieRepository;
use AcMarche\MeriteSportif\Repository\VoteRepository;

class VoteService
{
    private array $votes = [];

    public function __construct(private readonly VoteRepository $voteRepository, private readonly CategorieRepository $categorieRepository)
    {
    }

    public function voteExist(Club $club, Categorie $categorie): bool
    {
        return (bool) $this->voteRepository->getByClubAndCategorie($club, $categorie);
    }

    public function getVotesByClub(Club $club): array
    {
        $rows = $this->voteRepository->getByClub($club);
        foreach ($rows as $row) {
            $categorie = $row->getCategorie();
            $vote = ['candidat' => $row->getCandidat(), 'point' => $row->getPoint()];
            $this->addVote($categorie, $vote);
        }

        return $this->votes;
    }

    public function addVote(Categorie $categorie, array $vote): void
    {
        $this->votes[$categorie->getId()]['categorie'] = $categorie;
        $this->votes[$categorie->getId()]['votes'][] = $vote;
    }

    public function isComplete(Club $club): bool
    {
        $points = 0;
        foreach ($this->categorieRepository->findAll() as $categorie) {
            $votes = $this->voteRepository->getByClubAndCategorie($club, $categorie);
            foreach ($votes as $vote) {
                $points += $vote->getPoint();
            }
        }

        return $points === 9;
    }

    /**
     * @param Club[] $clubs
     */
    public function setIsComplete(array $clubs): void
    {
        foreach ($clubs as $club) {
            $club->setvoteIsComplete($this->isComplete($club));
        }
    }

    public function getVotesByCategorie(Categorie $categorie): array
    {
        $candidats = [];
        $votes = $this->voteRepository->getByCategorie($categorie);
        foreach ($votes as $vote) {
            $candidat = $vote->getCandidat();
            $point = $vote->getPoint();
            if (!isset($candidats[$candidat->getId()])) {
                $candidats[$candidat->getId()]['candidat'] = $candidat;
                $candidats[$candidat->getId()]['point'] = $point;
            } else {
                $candidats[$candidat->getId()]['point'] += $point;
            }
        }

        usort(
            $candidats,
            fn($a, $b): int => (int)$b['point'] <=> (int)$a['point']
        );

        return $candidats;
    }
}