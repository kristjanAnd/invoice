<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 13.04.15
 * Time: 17:08
 */

namespace Application\Entity\Subject;


use Application\Entity\Article\Brand;
use Application\Entity\Article\Category;
use Application\Entity\Article\Item;
use Application\Entity\Article\Service;
use Application\Entity\Role;
use Application\Entity\Subject;
use Application\Entity\Unit;
use Application\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="company")
 * @ORM\Entity
 */
class Company extends Subject{

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Unit", mappedBy="company", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $units;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Article\Service", mappedBy="company", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $services;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Article\Item", mappedBy="company", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $items;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Article\Brand", mappedBy="company", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $articleBrands;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Article\Category", mappedBy="company", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $articleCategories;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\User", mappedBy="company", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $users;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Role", mappedBy="company", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $roles;

    public function __construct()
    {
        $this->units = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->articleBrands = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->articleCategories = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @param ArrayCollection $units
     */
    public function addUnit(Unit $unit)
    {
        $this->units[] = $unit;
        $unit->setCompany($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ArrayCollection $items
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;
        $item->setCompany($this);
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
        $service->setCompany($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;
        $user->setCompany($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getArticleBrands()
    {
        return $this->articleBrands;
    }

    /**
     * @param Brand $brand
     */
    public function addArticleBrand(Brand $brand)
    {
        $this->articleBrands[] = $brand;
        $brand->setCompany($this);
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

    /**
     * @return ArrayCollection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param Role $role
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;
    }




} 