<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity
 */
class ProjectRisk
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Risk")
     */
    protected $risk;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="risks")
     */
    protected $project;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $threat;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $starter;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $reaction;
    
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $probability;   
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $impacts;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $state;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;          
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $modified;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $activated;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    /**
     * @return Risk
     */
    public function getRisk()
    {
        return $this->risk;
    }

    /**
     * @param Risk $risk
     */
    public function setRisk($risk)
    {
        $this->risk = $risk;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return string
     */
    public function getThreat()
    {
        return $this->threat;
    }

    /**
     * @param string $name
     */
    public function setThreat($threat)
    {
        $this->threat = $threat;
    }

    /**
     * @return string
     */
    public function getStarter()
    {
        return $this->starter;
    }

    /**
     * @param string $starter
     */
    public function setStarter($starter)
    {
        $this->starter = $starter;
    }

    /**
     * @return string
     */
    public function getReaction()
    {
        return $this->reaction;
    }

    /**
     * @param string $reaction
     */
    public function setReaction($reaction)
    {
        $this->reaction = $reaction;
    }

    /**
     * @return float
     */
    public function getProbability()
    {
        return $this->probability;
    }

    /**
     * @param float $probability
     */
    public function setProbability($probability)
    {
        $this->probability = $probability;
    }

    /**
     * @return string
     */
    public function getImpacts()
    {
        return $this->impacts;
    }

    /**
     * @param string $impacts
     */
    public function setImpacts($impacts)
    {
        $this->impacts = $impacts;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param DateTime $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    /**
     * @return DateTime
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * @param DateTime $activated
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;
    }
}