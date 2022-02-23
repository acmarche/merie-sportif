<?php

namespace AcMarche\MeriteSportif\Repository;

use AcMarche\MeriteSportif\Doctrine\OrmCrudTrait;
use AcMarche\MeriteSportif\Entity\Categorie;
use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vote[]    findAll()
 * @method Vote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    public function getAll()
    {
        return $this->createQueryBuilder('vote')
            ->leftJoin('vote.candidat', 'candidat', 'WITH')
            ->leftJoin('vote.club', 'club', 'WITH')
            ->leftJoin('vote.categorie', 'categorie', 'WITH')
            ->addSelect('candidat', 'club', 'categorie')
            ->orderBy('vote.categorie', 'ASC')
            ->orderBy('vote.candidat', 'ASC')
            ->orderBy('vote.point', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Vote[]
     */
    public function getByClub(Club $club)
    {
        return $this->createQueryBuilder('vote')
            ->andWhere('vote.club = :club')
            ->setParameter('club', $club)
            ->orderBy('vote.categorie', 'ASC')
            ->orderBy('vote.candidat', 'ASC')
            ->orderBy('vote.point', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Vote[]
     */
    public function getByClubAndCategorie(Club $club, Categorie $categorie)
    {
        return $this->createQueryBuilder('vote')
            ->andWhere('vote.club = :club')
            ->setParameter('club', $club)
            ->andWhere('vote.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Vote[]
     */
    public function getByCategorie(Categorie $categorie)
    {
        return $this->createQueryBuilder('vote')
            ->andWhere('vote.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->getQuery()
            ->getResult();
    }
}
