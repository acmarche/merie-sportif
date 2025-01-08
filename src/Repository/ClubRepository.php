<?php

namespace AcMarche\MeriteSportif\Repository;

use AcMarche\MeriteSportif\Doctrine\OrmCrudTrait;
use AcMarche\MeriteSportif\Entity\Club;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Club|null find($id, $lockMode = null, $lockVersion = null)
 * @method Club|null findOneBy(array $criteria, array $orderBy = null)
 * @method Club[]    findAll()
 * @method Club[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Club::class);
    }

    /**
     * @return Club[]
     */
    public function getAll(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByEmail(string $email): ?Club
    {
        return $this->createQueryBuilder('club')
            ->andWhere('club.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
