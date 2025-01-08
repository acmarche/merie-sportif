<?php

namespace AcMarche\MeriteSportif\Doctrine;

use Doctrine\DBAL\Exception;

trait OrmCrudTrait
{
    public function insert(object $object): void
    {
        $this->persist($object);
        $this->flush();
    }

    public function persist(object $object): void
    {
        $this->getEntityManager()->persist($object);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(object $object): void
    {
        $this->getEntityManager()->remove($object);
    }

    public function getOriginalEntityData(object $object)
    {
        return $this->getEntityManager()->getUnitOfWork()->getOriginalEntityData($object);
    }

    /**
     * @deprecated
     * @throws Exception
     */
    public function reset(): void
    {
        $cmd = $this->getEntityManager()->getClassMetadata($this->getClassName());
        $connection = $this->getEntityManager()->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
        $connection->executeStatement($q);
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
    }

}
