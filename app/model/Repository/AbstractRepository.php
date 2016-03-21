<?php

namespace App\Model\Repository;

use App\Model\Entity\AbstractEntity;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette\Object;

abstract class AbstractRepository extends Object
{
    /** @var EntityManager */
    protected $entityManager;
    /** @var EntityRepository */
    protected $entityRepository;

    /**
     * AbstractRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param AbstractEntity|AbstractEntity[] $entity
     */
    public function insert($entity)
    {
        if (is_array($entity)) {
            $entities = $entity;
        } else {
            $entities = array($entity);
        }

        foreach ($entities as $entity) {
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }

    /**
     * @param AbstractEntity|AbstractEntity[] $entity
     */
    public function update($entity)
    {
        if (is_array($entity)) {
            $entities = $entity;
        } else {
            $entities = array($entity);
        }

        foreach ($entities as $entity) {
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }

    /**
     * @param $values
     * @param array $where
     */
    public function updateWhere($values, $where = array())
    {
        foreach ($this->getByParameters($where) as $entity) {
            $entity->populateData($values);
        }
        $this->entityManager->flush();
    }

    /**
     * @param AbstractEntity|AbstractEntity[] $entity
     */
    public function delete($entity)
    {
        if (is_array($entity)) {
            $entities = $entity;
        } else {
            $entities = array($entity);
        }

        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }
        $this->entityManager->flush();
    }

    /**
     * @param array $where
     */
    public function deleteWhere($where = array())
    {
        foreach ($this->getByParameters($where) as $entity) {
            $this->entityManager->remove($entity);
        }
        $this->entityManager->flush();
    }

    /**
     * @return AbstractEntity[]
     */
    public function getAll()
    {
        return $this->entityRepository->findAll();
    }

    /**
     * @param $id
     * @return null|AbstractEntity
     */
    public function getById($id)
    {
        return $this->entityRepository->find($id);
    }

    /**
     * @param $parameters
     * @return AbstractEntity[]
     */
    public function getByParameters($parameters)
    {
        return $this->entityRepository->findBy($parameters);
    }

    /**
     * @param $parameters
     * @return AbstractEntity
     */
    public function getOneByParameters($parameters)
    {
        return $this->entityRepository->findOneBy($parameters);
    }

    /**
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function getQB()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('table')->from($this->entityRepository->getClassName(), 'table');

        return $qb;
    }

    /**
     * @param AbstractEntity[] $entities
     * @return array
     */
    public static function getIdIndexedArrayOfNames($entities)
    {
        $result = array();
        foreach($entities as $entity) {
            $result[$entity->getId()] = $entity->getName();
        }
        return $result;
    }
}