<?php

namespace App\Model\Repository;

use App\Model\Entity\Project;
use Kdyby\Doctrine\EntityManager;
use Doctrine\ORM\EntityRepository;

class ProjectRepository extends AbstractRepository
{
    /**
     * ProjectRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->entityRepository = $this->entityManager->getRepository(Project::getClassName());
    }

    /**
     * @return Project[]
     */
    public function getAll()
    {
        return parent::getAll();
    }

    /**
     * @param $id
     * @return Project|null
     */
    public function getById($id)
    {
        return parent::getById($id);
    }

    /**
     * @param $parameters
     * @return Project[]
     */
    public function getByParameters($parameters)
    {
        return parent::getByParameters($parameters);
    }

    /**
     * @param $parameters
     * @return Project|null
     */
    public function getOneByParameters($parameters)
    {
        return parent::getOneByParameters($parameters);
    }
}