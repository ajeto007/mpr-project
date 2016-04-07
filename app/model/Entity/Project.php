<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class Project extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $description;
    
    /**
     * @ORM\ManyToMany(targetEntity="Employee", inversedBy="projects")
     */
    protected $employees;
    
    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="projects")
     */
    protected $client;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $fromDate;
    
   /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $toDate;
    
    /**
     * @ORM\OneToMany(targetEntity="ProjectRisk", mappedBy="project", cascade={"persist", "remove"})
     */    
    protected $risks;
    
    public function __construct() 
    {
        parent::__construct();
        $this->employees = new ArrayCollection();
        $this->risks = new ArrayCollection();
    }       

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $name
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    /**
     * @return Employee[]
     */
    public function getEmployees()
    {
        return $this->employees;
    }

   /**
     * @return \DateTime
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * @param \DateTime $fromDate
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }
    
   /**
     * @return \DateTime
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * @param \DateTime $toDate
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return Risk[]
     */
    public function getRisks()
    {
        return $this->risks;
    }
}