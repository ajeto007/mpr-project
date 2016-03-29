<?php

namespace App\Model\Repository;

use App\Model\Entity\Employee;
use Kdyby\Doctrine\EntityManager;
use Doctrine\ORM\EntityRepository;

class EmployeeRepository extends AbstractRepository
{
    /**
     * EmployeeRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->entityRepository = $this->entityManager->getRepository(Employee::getClassName());
    }

    /**
     * @return Employee[]
     */
    public function getAll()
    {
        return parent::getAll();
    }

    /**
     * @param $id
     * @return Employee|null
     */
    public function getById($id)
    {
        return parent::getById($id);
    }

    /**
     * @param $parameters
     * @return Employee[]
     */
    public function getByParameters($parameters)
    {
        return parent::getByParameters($parameters);
    }

    /**
     * @param $parameters
     * @return Employee|null
     */
    public function getOneByParameters($parameters)
    {
        return parent::getOneByParameters($parameters);
    }
}