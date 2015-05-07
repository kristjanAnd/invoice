<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 7.05.15
 * Time: 14:50
 */

namespace Application\Entity;

use Application\Entity\Subject\Company;
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Parameters;

/**
 * ArticleSetting
 *
 * @ORM\Table(name="article_setting")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"item_setting" = "Application\Entity\ArticleSetting\ItemSetting", "service_setting" = "Application\Entity\ArticleSetting\ServiceSetting"})
 */
abstract class ArticleSetting extends AbstractEntity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Application\Entity\Subject\Company $company
     *
     *      @ORM\OneToOne(targetEntity="Application\Entity\Subject\Company")
     *      @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     */
    protected $company;

    /**
     * @var \Application\Entity\User $user
     *
     *      @ORM\OneToOne(targetEntity="Application\Entity\User")
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @var Unit
     * @ORM\OneToOne(targetEntity="Application\Entity\Unit")
     */
    protected $unit;

    /**
     * @var Vat
     * @ORM\OneToOne(targetEntity="Application\Entity\Vat")
     */
    protected $vat;

    /**
     * @var float
     *
     * @ORM\Column(name="qty", type="decimal", nullable=true)
     */
    protected $quantity;

    public function __construct(Parameters $data = null){
        if(isset($data->user)){
            $this->user = $data->user;
        }
        if(isset($data->company)){
            $this->company = $data->company;
        }
    }

    /**
     * @return Subject\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Subject\Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Vat
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param Vat $vat
     */
    public function setVat(Vat $vat)
    {
        $this->vat = $vat;
    }


} 