<?php
/**
 * This file is part of meritesportif application
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 4/10/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace AcMarche\MeriteSportif\Service;

use AcMarche\MeriteSportif\Entity\Categorie;
use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;

class VoteManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function handleVote(array $data, Club $club, Categorie $categorie): void
    {
        foreach ($data as $candidature) {
            foreach ($candidature as $value) {
                $candidat = $value['candidat'];
                $point = $value['point'];
                if ($point > 0) {
                    $vote = new Vote($categorie, $club, $candidat, $point);
                    $this->entityManager->persist($vote);
                }
            }
        }
    }
}