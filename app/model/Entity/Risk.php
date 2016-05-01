<?php

namespace App\Model\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Risk extends AbstractEntity
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;
    
    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="risks")
     */
    protected $category;

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
     * @ORM\Column(type="enum", columnDefinition="enum('nepatrna', 'mala', 'stredni', 'velka', 'mimoradna') NOT NULL")
     */
    protected $probability;
    public static $probabilityEnum = array('nepatrna' => 'Nepatrná', 'mala' => 'Malá', 'stredni' => 'Střední', 'velka' => 'Velká', 'mimoradna' => 'Mimořádně velká');

    /**
     * @ORM\Column(type="enum", columnDefinition="enum('nepatrne', 'male', 'citelne', 'kriticke', 'katastroficke') NOT NULL")
     */
    protected $impacts;
    public static $impactsEnum = array('nepatrne' => 'Nepatrný', 'male' => 'Malý', 'citelne' => 'Citelný', 'kriticke' => 'Kritický', 'katastroficke' => 'Katastrofický');

    /**
     * @ORM\Column(type="enum", columnDefinition="enum('neaktivni', 'aktivni') NOT NULL")
     */
    protected $state;
    public static $stateEnum = array('neaktivni' => 'Neaktivní', 'aktivni' => 'Aktivní');

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

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="risks")
     */
    protected $project;

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
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getThreat()
    {
        return $this->threat;
    }

    /**
     * @param string $threat
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
}
