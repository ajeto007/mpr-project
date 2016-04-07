<?php

namespace App\Model\Repository;

use App\Model\Entity\Category;
use Kdyby\Doctrine\EntityManager;
use Doctrine\ORM\EntityRepository;

class CategoryRepository extends AbstractRepository
{
    /**
     * CategoryRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->entityRepository = $this->entityManager->getRepository(Category::getClassName());
    }

    /**
     * @return Category[]
     */
    public function getAll()
    {
        return parent::getAll();
    }

    /**
     * @param $id
     * @return Category|null
     */
    public function getById($id)
    {
        return parent::getById($id);
    }

    /**
     * @param $parameters
     * @return Category[]
     */
    public function getByParameters($parameters)
    {
        return parent::getByParameters($parameters);
    }

    /**
     * @param $parameters
     * @return Category|null
     */
    public function getOneByParameters($parameters)
    {
        return parent::getOneByParameters($parameters);
    }
}