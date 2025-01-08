<?php

namespace AcMarche\MeriteSportif\Repository;

use AcMarche\MeriteSportif\Doctrine\OrmCrudTrait;
use AcMarche\MeriteSportif\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Setting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Setting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Setting[]    findAll()
 * @method Setting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Setting::class);
    }

    public function findMode(): string
    {
        $setting = $this->find(1);

        return $setting->mode;
    }

    /**
     * @return array<int,string>
     */
    public function findEmails(): array
    {
        $setting = $this->find(1);

        return $setting->emails;
    }

    public function findOne(): Setting
    {
        return $this->find(1);
    }

}
