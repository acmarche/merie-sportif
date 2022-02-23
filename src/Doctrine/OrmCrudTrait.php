<?php

namespace AcMarche\MeriteSportif\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

trait OrmCrudTrait
{
    /** @var EntityManagerInterface */
    protected $_em;

    public function insert(object $object): void
    {
        $this->persist($object);
        $this->flush();
    }

    public function persist(object $object): void
    {
        $this->_em->persist($object);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function reset(): void
    {
        $cmd = $this->_em->getClassMetadata($this->getClassName());
        $connection = $this->_em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
        $connection->executeStatement($q);
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
    }

    public function remove(object $object): void
    {
        $this->_em->remove($object);
    }

    public function getOriginalEntityData(object $object)
    {
        return $this->_em->getUnitOfWork()->getOriginalEntityData($object);
    }
}
