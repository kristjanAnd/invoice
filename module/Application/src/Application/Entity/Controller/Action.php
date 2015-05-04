<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 27.04.15
 * Time: 8:44
 */

namespace Application\Entity\Controller;


use Application\Entity\AbstractEntity;
use Application\Entity\Controller;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="controller_action")
 */
class Action extends AbstractEntity {

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
     * @var integer
     *
     * @ORM\Column(name="order_no", type="integer")
     */
    protected $orderNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="translation_key", type="string")
     */
    protected $translationKey;

    /**
     * @var \Application\Entity\Controller $controller
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Controller", inversedBy="actions")
     *      @ORM\JoinColumn(name="controller_id", referencedColumnName="id", nullable=false)
     */
    protected $controller;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_navigation", type="boolean")
     */
    protected $isNavigation;

    /**
     * @return \Application\Entity\Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param \Application\Entity\Controller $controller
     */
    public function setController(Controller $controller)
    {
        $this->controller = $controller;
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
     * @return string
     */
    public function getTranslationKey()
    {
        return $this->translationKey;
    }

    /**
     * @param string $translationKey
     */
    public function setTranslationKey($translationKey)
    {
        $this->translationKey = $translationKey;
    }

    /**
     * @return boolean
     */
    public function isNavigation()
    {
        return $this->isNavigation;
    }

    /**
     * @param boolean $isNavigation
     */
    public function setIsNavigation($isNavigation)
    {
        $this->isNavigation = $isNavigation;
    }

    /**
     * @return int
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param int $orderNumber
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

} 