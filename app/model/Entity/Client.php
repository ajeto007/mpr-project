<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class Client extends AbstractEntity
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
    protected $companyName;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contactName;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;
    
    /**
     * @ORM\OneToOne(targetEntity="Address", cascade={"persist"})
     */
    protected $address;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $ico;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $dic; 
    
    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="client")
     */
    protected $projects;  
    
    public function __construct() 
    {
        parent::__construct();
        $this->projects = new ArrayCollection();
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }
    
    /**
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * @param string $contactName
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;
    }
    
    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $name
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }
    
   /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
    
   /**
     * @return string
     */
    public function getIco()
    {
        return $this->ico;
    }

    /**
     * @param string $ico
     */
    public function setIco($ico)
    {
        $this->ico = $ico;
    }
    
   /**
     * @return string
     */
    public function getDic()
    {
        return $this->dic;
    }

    /**
     * @param string $ico
     */
    public function setDic($dic)
    {
        $this->dic = $dic;
    }
    
    /**
     * @return Project[]
     */
    public function getProjects()
    {
        return $this->projects;
    }
}