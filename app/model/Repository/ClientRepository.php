<?php

namespace App\Model\Repository;

use App\Model\Entity\Client;
use Kdyby\Doctrine\EntityManager;
use Doctrine\ORM\EntityRepository;

class ClientRepository extends AbstractRepository
{
    /**
     * ClientRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->entityRepository = $this->entityManager->getRepository(Client::getClassName());
    }

    /**
     * @return Client[]
     */
    public function getAll()
    {
        return parent::getAll();
    }

    /**
     * @param $id
     * @return Client|null
     */
    public function getById($id)
    {
        return parent::getById($id);
    }

    /**
     * @param $parameters
     * @return Client[]
     */
    public function getByParameters($parameters)
    {
        return parent::getByParameters($parameters);
    }

    /**
     * @param $parameters
     * @return Client|null
     */
    public function getOneByParameters($parameters)
    {
        return parent::getOneByParameters($parameters);
    }

    /**
     * @param Client[] $entities
     * @return array
     */
    public static function getIdIndexedArrayOfNames($entities)
    {
        $result = array();
        foreach($entities as $entity) {
            $result[$entity->getId()] = $entity->getCompanyName();
        }
        return $result;
    }
}