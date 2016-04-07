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
     * @return ClientRepository[]
     */
    public function getAll()
    {
        return parent::getAll();
    }

    /**
     * @param $id
     * @return ClientRepository|null
     */
    public function getById($id)
    {
        return parent::getById($id);
    }

    /**
     * @param $parameters
     * @return ClientRepository[]
     */
    public function getByParameters($parameters)
    {
        return parent::getByParameters($parameters);
    }

    /**
     * @param $parameters
     * @return ClientRepository|null
     */
    public function getOneByParameters($parameters)
    {
        return parent::getOneByParameters($parameters);
    }
}