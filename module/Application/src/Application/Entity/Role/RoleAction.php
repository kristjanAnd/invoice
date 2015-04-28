<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 27.04.15
 * Time: 8:50
 */

namespace Application\Entity\Role;


use Application\Entity\AbstractEntity;
use Application\Entity\Controller\Action;
use Application\Entity\Role;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="role_action")
 */
class RoleAction extends AbstractEntity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Application\Entity\Role $role
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Role", inversedBy="roleActions")
     *      @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     */
    protected $role;

    /**
     * @var \Application\Entity\Controller\Action $controller
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Controller\Action", inversedBy="roleActions")
     *      @ORM\JoinColumn(name="action_id", referencedColumnName="id", nullable=false)
     */
    protected $controllerAction;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_enabled", type="boolean")
     */
    protected $isEnabled;

    public function __construct(){
        $this->isEnabled = false;
    }

    /**
     * @return \Application\Entity\Controller\Action
     */
    public function getControllerAction()
    {
        return $this->controllerAction;
    }

    /**
     * @param \Application\Entity\Controller\Action $controllerAction
     */
    public function setControllerAction(Action $controllerAction)
    {
        $this->controllerAction = $controllerAction;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param boolean $isEnabled
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     * @return \Application\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param \Application\Entity\Role $role
     */
    public function setRole(Role $role)
    {
        $this->role = $role;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


} 