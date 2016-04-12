<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class Category extends AbstractEntity
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
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="descendants")
     */
    protected $parent; 
    
    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent", cascade={"remove"})
     */
    protected $descendants;   
    
    /**
     * @ORM\OneToMany(targetEntity="Risk", mappedBy="category", cascade={"remove"})
     */
    protected $risks;  

    public function __construct() 
    {
        parent::__construct();
        $this->risks = new ArrayCollection();
        $this->descendants = new ArrayCollection();
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
     * @return Category|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Category $category
     */
    public function setParent($category)
    {
        $this->parent = $category;
    }
    
    /**
     * @return ArrayCollection|Risk[]
     */
    public function getRisks()
    {
        return $this->risks;
    }

    /**
     * @return ArrayCollection|Category[]
     */
    public function getDescendants()
    {
        return $this->descendants;
    }
}