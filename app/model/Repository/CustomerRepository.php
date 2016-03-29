<?php

namespace App\Model\Repository;

use App\Model\Entity\Customer;
use Kdyby\Doctrine\EntityManager;
use Doctrine\ORM\EntityRepository;

class CustomerRepository extends AbstractRepository
{
    /**
     * CustomerRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->entityRepository = $this->entityManager->getRepository(Customer::getClassName());
    }

    /**
     * @return Customer[]
     */
    public function getAll()
    {
        return parent::getAll();
    }

    /**
     * @param $id
     * @return Customer|null
     */
    public function getById($id)
    {
        return parent::getById($id);
    }

    /**
     * @param $parameters
     * @return Customer[]
     */
    public function getByParameters($parameters)
    {
        return parent::getByParameters($parameters);
    }

    /**
     * @param $parameters
     * @return Customer|null
     */
    public function getOneByParameters($parameters)
    {
        return parent::getOneByParameters($parameters);
    }
}