<?php

namespace AcMarche\MeriteSportif\Repository;

use AcMarche\MeriteSportif\Doctrine\OrmCrudTrait;
use AcMarche\MeriteSportif\Entity\Candidat;
use AcMarche\MeriteSportif\Entity\Categorie;
use AcMarche\MeriteSportif\Entity\Club;
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

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Candidat::class);
    }

    /**
     * @return Candidat[]
     */
    public function getAll(): array
    {
        return $this
            ->createQueryBuilder('c')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Candidat[]
     */
    public function getByCategorie(Categorie $categorie): array
    {
        return $this
            ->createQueryBuilder('candidat')
            ->andWhere('candidat.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->orderBy('RAND()')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Candidat[]
     */
    public function getByClub(Club $club): array
    {
        $email = $club->getEmail();

        return $this
            ->createQueryBuilder('candidat')
            ->andWhere('candidat.add_by = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();
    }

    public function isAlreadyProposed(Club $club, Categorie $categorie): ?Candidat
    {
        return $this
            ->createQueryBuilder('candidat')
            ->andWhere('candidat.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->andWhere('candidat.add_by = :user')
            ->setParameter('user', $club->getEmail())
            ->orderBy('RAND()')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<int,string>
     */
    public function getAllSports(): array
    {
        $sports = [];

        foreach ($this->getAll() as $candidat) {
            $sports[$candidat->getSport()] = $candidat->getSport();
        }
ksort($sports);
        return $sports;
    }

    /**
     * @param string|null $nom
     * @param string|null $sport
     * @param Categorie|null $categorie
     * @return Candidat[]
     */
    public function search(?string $nom, ?string $sport, ?Categorie $categorie): array
    {
        $queryBuilder = $this->createQueryBuilder('candidat');

        if ($nom) {
            $queryBuilder
                ->andWhere('candidat.nom LIKE :nom OR candidat.prenom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        if ($sport) {
            $queryBuilder
                ->andWhere('candidat.sport LIKE :sport')
                ->setParameter('sport', '%'.$sport.'%');
        }

        if ($categorie instanceof Categorie) {
            $queryBuilder
                ->andWhere('candidat.categorie = :categorie')
                ->setParameter('categorie', $categorie);
        }

        return $queryBuilder
            ->orderBy('candidat.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
