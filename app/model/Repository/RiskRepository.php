<?php

namespace App\Model\Repository;

use App\Model\Entity\Risk;
use Kdyby\Doctrine\EntityManager;
use Doctrine\ORM\EntityRepository;

class RiskRepository extends AbstractRepository
{
    /**
     * RiskRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->entityRepository = $this->entityManager->getRepository(Risk::getClassName());
    }

    /**
     * @return Risk[]
     */
    public function getAll()
    {
        return parent::getAll();
    }

    /**
     * @param $n
     * @return Risk[]
     */
    public function getNewestRisk($n)
    {
        return $this->getQB()->orderBy("table.created", "DESC")->setMaxResults($n);
    }

    /**
     * @param $n
     * @return Risk[]
     */
    public function getActivatedRisk($n)
    {
        return $this->getQB()->where("table.state='aktivni'")->orderBy("table.activated", "DESC")->setMaxResults($n);
    }

    /**
     * @param $id
     * @return Risk|null
     */
    public function getById($id)
    {
        return parent::getById($id);
    }

    /**
     * @param $parameters
     * @return Risk[]
     */
    public function getByParameters($parameters)
    {
        return parent::getByParameters($parameters);
    }

    /**
     * @param $parameters
     * @return Risk|null
     */
    public function getOneByParameters($parameters)
    {
        return parent::getOneByParameters($parameters);
    }
}