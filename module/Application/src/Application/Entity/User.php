<?php

namespace Application\Entity;

use Application\Entity\Article\Brand;
use Application\Entity\Article\Category;
use Application\Entity\Article\Item;
use Application\Entity\Article\Service;
use Application\Entity\Subject\Company;
use Application\Entity\User\Hash;
use BjyAuthorize\Provider\Role\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ZfcUser\Entity\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 *
 */
class User extends AbstractEntity implements UserInterface, ProviderInterface
{

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true,  length=255)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(type="string", name="display_name", length=50, nullable=true)
     */
    protected $displayName;

    /**
     * @var string
     * @ORM\Column(type="string", name="personal_code", length=11, nullable=true)
     */
    protected $personalCode;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(name="language_code", type="string")
     */
    protected $languageCode;

    /**
     * @ORM\OneToOne(targetEntity="Application\Entity\User\Hash", mappedBy="user")
     */
    protected $hash;

    /**
     * @var int
     */
    protected $state;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Application\Entity\Role", cascade={"all"})
     * @ORM\JoinTable(name="user_role_linker",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;

    /**
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;

    /**
     * @var string
     * @ORM\Column(type="string", name="first_name", length=255)
     */
    protected $firstName;

    /**
     * @var string
     * @ORM\Column(type="string", name="last_name", length=255)
     */
    protected $lastName;

    /**
     * @var string
     * @ORM\Column(type="string", name="phone", length=90)
     */
    protected $phone;

    /**
     * @var string
     * @ORM\Column(type="string", name="status")
     */
    protected $status;

    /**
     * @var \Application\Entity\Subject\Company $company
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Subject\Company", inversedBy="users")
     *      @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     */
    protected $company;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Article\Service", mappedBy="user", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $services;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Article\Item", mappedBy="user", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $items;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Article\Brand", mappedBy="user", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $articleBrands;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Article\Category", mappedBy="user", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $articleCategories;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Unit", mappedBy="user", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $units;

    /**
     * Initialies the roles variable.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->dateCreated = new \DateTime();
        $this->articleBrands = new ArrayCollection();
        $this->articleCategories = new ArrayCollection();
        $this->units = new ArrayCollection();
    }

    /**
     * Get the value of date_created.
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this|UserInterface
     */
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this|UserInterface
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this|UserInterface
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return int
     */
    public function getPersonalCode()
    {
        return $this->personalCode;
    }

    /**
     * @param int $personalCode
     */
    public function setPersonalCode($personalCode)
    {
        $this->personalCode = $personalCode;
    }

    /**
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return $this|UserInterface
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this|UserInterface
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     * @return $this|UserInterface
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get role.
     *
     * @return ArrayCollection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add a role to the user.
     *
     * @param Role $role
     *
     * @return void
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;
    }

    /**
     * @param Hash $hash
     */
    public function setHash(Hash $hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getFullName(){
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return \Application\Entity\Subject\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param \Application\Entity\Subject\Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * @param string $languageCode
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;
    }

    /**
     * @return ArrayCollection
     */
    public function getArticleBrands()
    {
        return $this->articleBrands;
    }

    /**
     * @param Brand $brands
     */
    public function addArticleBrand(Brand $brand)
    {
        $this->articleBrands[] = $brand;
        $brand->setUser($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param Item $items
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;
        $item->setUser($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param Service $service
     */
    public function addService(Service $service)
    {
        $this->services[] = $service;
        $service->setUser($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @param Unit $units
     */
    public function addUnit(Unit $unit)
    {
        $this->units[] = $unit;
        $unit->setUser($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getArticleCategories()
    {
        return $this->articleCategories;
    }

    /**
     * @param Category $articleCategories
     */
    public function addArticleCategory(Category $articleCategory)
    {
        $this->articleCategories[] = $articleCategory;
    }

}
