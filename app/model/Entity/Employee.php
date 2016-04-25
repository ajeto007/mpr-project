<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class Employee extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;
    
    /**
     * @ORM\OneToOne(targetEntity="Address", cascade={"persist"})
     */
    protected $address;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phone;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $birthday;
    
    /**
     * @ORM\Column(type="string")
    */
    protected $position;

    /**
     * @ORM\Column(type="enum", columnDefinition="enum('vedouci', 'zamestnanec', 'admin', 'bezprihlasovani') NOT NULL")
    */
    protected $role;
    public static $roles = array('vedouci' => 'Vedoucí projektu', 'zamestnanec' => 'Zaměstnanec', 'admin' => 'Admin', 'bezprihlasovani' => 'Zaměstnanec bez přihlašování');
    
    /**
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="employees")
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
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
    
    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
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
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }
    
   /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
    
   /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return ArrayCollection|Project[]
     */    
    public function getProjects()
    {
        return $this->projects;
    }
}
