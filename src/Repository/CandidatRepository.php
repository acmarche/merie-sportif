<?php

namespace AcMarche\MeriteSportif\Repository;

use AcMarche\MeriteSportif\Doctrine\OrmCrudTrait;
use Doctrine\ORM\QueryBuilder;
use AcMarche\MeriteSportif\Entity\Candidat;
use AcMarche\MeriteSportif\Entity\Categorie;
use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Candidat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Candidat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Candidat[]    findAll()
 * @method Candidat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidatRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Candidat::class);
    }

    public function getAll()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getQueryBuilder(Categorie $categorie): QueryBuilder
    {
        return $this->createQueryBuilder('candidat')
            ->andWhere('candidat.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->orderBy('candidat.nom', 'ASC');
    }

    public function getByCategorie(Categorie $categorie)
    {
        return $this->createQueryBuilder('candidat')
            ->andWhere('candidat.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->orderBy('RAND()')
            ->getQuery()
            ->getResult();
    }

    public function getByClub(Club $club)
    {
        $email = $club->getEmail();
        return $this->createQueryBuilder('candidat')
            ->andWhere('candidat.add_by = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();
    }

    public function isAlreadyProposed(Club $club, Categorie $categorie): ?Candidat
    {
        return $this->createQueryBuilder('candidat')
            ->andWhere('candidat.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->andWhere('candidat.add_by = :user')
            ->setParameter('user', $club->getEmail())
            ->orderBy('RAND()')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllGroupByName()
    {
        /**
         * SELECT ANY_VALUE(d.id), d.nom, count(d.nom) as lignes
         * FROM defunts d GROUP BY d.nom ORDER BY d.nom ASC.
         */
        $qb = $this->createQueryBuilder('d');
        //$qb->select('ANY_VALUE(d.id) as id, d.nom, count(d.nom) as lignes');
        $qb->select('d.id, d.nom, count(d.nom) as lignes');
        $qb->groupBy('d.nom');
        $qb->orderBy('d.nom');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getAllSports(): array
    {
        $sports = [];
        $candidats = $this->createQueryBuilder('c')
            //  ->select('ANY_VALUE(c.id) as id, c.sport, count(c.sport) as lignes')
            //  ->select('c.id, c.sport, count(c.sport) as lignes')
            // ->groupBy('c.sport')
            ->orderBy('c.sport', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($candidats as $candidat) {
            $sports[$candidat->getSport()] = $candidat->getSport();
        }

        return $sports;
    }

    /**
     * @param string $nom
     * @param string $sport
     * @return Candidat[]
     */
    public function search(?string $nom, ?string $sport, ?Categorie $categorie)
    {
        $qb = $this->createQueryBuilder('candidat');

        if ($nom) {
            $qb->andWhere('candidat.nom LIKE :nom OR candidat.prenom LIKE :nom')
                ->setParameter('nom', '%' . $nom . '%');
        }

        if ($sport) {
            $qb->andWhere('candidat.sport LIKE :sport')
                ->setParameter('sport', '%' . $sport . '%');
        }

        if ($categorie !== null) {
            $qb->andWhere('candidat.categorie = :categorie')
                ->setParameter('categorie', $categorie);
        }
        return $qb->orderBy('candidat.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
