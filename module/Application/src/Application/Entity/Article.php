<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 13.04.15
 * Time: 16:53
 */

namespace Application\Entity;

use Application\Entity\Article\Brand;
use Application\Entity\Article\Category;
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Parameters;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"item" = "Application\Entity\Article\Item", "service" = "Application\Entity\Article\Service"})
 */
abstract class Article extends AbstractEntity {

    const STATUS_ACTIVE = 'active';
    const STATUS_DISABLED = 'disabled';

    const ARTICLE_TYPE_ITEM = 'item';
    const ARTICLE_TYPE_SERVICE = 'service';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string")
     */
    protected $description;

    /**
     * @var Unit
     * @ORM\OneToOne(targetEntity="Application\Entity\Unit")
     */
    protected $unit;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    protected $status;

    /**
     * @var float
     *
     * @ORM\Column(name="sale_price", type="decimal", nullable=true)
     */
    protected $salePrice;

    /**
     * @var float
     *
     * @ORM\Column(name="qty", type="decimal", nullable=true)
     */
    protected $quantity;

    /**
     * @var \Application\Entity\Article\Brand $brand
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Article\Brand", inversedBy="articles")
     *      @ORM\JoinColumn(name="brand_id", referencedColumnName="id", nullable=false)
     */
    protected $brand;

    /**
     * @var \Application\Entity\Article\Category $category
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Article\Category", inversedBy="articles")
     *      @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    protected $category;

    /**
     * @var \Application\Entity\Subject\Company $company
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Subject\Company", inversedBy="items")
     *      @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     */
    protected $company;

    /**
     * @var \Application\Entity\User $user
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\User", inversedBy="items")
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;


    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return float
     */
    public function getSalePrice()
    {
        return $this->salePrice;
    }

    /**
     * @param float $salePrice
     */
    public function setSalePrice($salePrice)
    {
        $this->salePrice = $salePrice;
    }

    /**
     * @return Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param Unit $unit
     */
    public function setUnit(Unit $unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return Article\Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param Article\Brand $brand
     */
    public function setBrand(Brand $brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return Article\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Article\Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
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



}