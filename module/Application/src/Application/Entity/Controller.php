<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 27.04.15
 * Time: 8:40
 */

namespace Application\Entity;

use Application\Entity\Controller\Action;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="controller")
 */
class Controller extends AbstractEntity {

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
     * @ORM\Column(name="translation_key", type="string")
     */
    protected $translationKey;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Controller\Action", mappedBy="controller", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="controller_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $actions;

    public function __construct(){
        $this->actions = new ArrayCollection();
    }

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
     * @return ArrayCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param Action $action
     */
    public function addAction(Action $action)
    {
        $this->actions[] = $action;
        $action->setController($this);
    }


} 