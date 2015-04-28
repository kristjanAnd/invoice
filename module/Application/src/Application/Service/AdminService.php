<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 27.04.15
 * Time: 7:44
 */

namespace Application\Service;


use Application\Entity\Controller;
use Application\Entity\Role;
use Application\Entity\Subject\Company;
use BitWeb\Stdlib\StringUtil;
use Zend\Stdlib\Parameters;

class AdminService extends AbstractService {

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getRolesByCompany(Company $company){
        return $this->entityManager->getRepository(Role::getClass())->findBy(array('company' => $company));
    }


    public function updateControllers(){
        $controllers = include 'controllerArray/controllers.php';
        foreach($controllers as $controller){
            $controllerEntity = $this->getControllerByCode($controller['code']) ? $this->getControllerByCode($controller['code']) : $this->createController($controller['name'], $controller['code'], $controller['key']);
            foreach($controller['actions'] as $action){
                if(!$this->getControllerActionByNameAndController($action['name'], $controllerEntity)){
                    $this->createControllerAction($action['name'], $action['key'], $controllerEntity);
                }
            }
        }
    }

    public function addRoleAndActions(Parameters $data, Company $company){
        $isAllowedArray = $data->isAllowed;
        $role = $this->createCompanyRole($company, $data->name);
        foreach($this->getControllerActions() as $action){ /* @var $action \Application\Entity\Controller\Action */
            $isAllowed = isset($isAllowedArray[$action->getId()]);
            $this->createRoleAction($role, $action, $isAllowed);
        }
    }

    public function createRoleAction(Role $role, Controller\Action $action, $isAllowed = false){
        $roleAction = new Role\RoleAction();
        $roleAction->setControllerAction($action);
        $roleAction->setRole($role);
        $roleAction->setIsEnabled($isAllowed);

        $this->entityManager->persist($roleAction);
        $this->entityManager->flush($roleAction);

        return $role;
    }

    public function createCompanyRole(Company $company, $roleName){
        $role = new Role();
        $role->setCompany($company);
        $role->setParent($this->getGuestRole());
        $role->setRoleId($company->getId() . '-' . StringUtil::urlify($roleName));

        $this->entityManager->persist($role);
        $this->entityManager->flush($role);

        return $role;
    }

    public function getGuestRole(){
        return $this->entityManager->getRepository(Role::getClass())->findOneBy(array('roleId' => Role::ROLE_GUEST));
    }

    public function getControllerByCode($code){
        return $this->entityManager->getRepository(Controller::getClass())->findOneBy(array('code' => $code));
    }

    public function getControllers(){
        return $this->entityManager->getRepository(Controller::getClass())->findBy(array());
    }

    public function getControllerActions(){
        return $this->entityManager->getRepository(Controller\Action::getClass())->findBy(array());
    }

    public function getControllerActionByNameAndController($name, Controller $controller){
        return $this->entityManager->getRepository(Controller\Action::getClass())->findOneBy(array('name' => $name, 'controller' => $controller));
    }

    /**
     * @param Role $role
     * @param Controller\Action $action
     * @return null|Role\RoleAction
     */
    public function getRoleActionByRoleAndAction(Role $role, Controller\Action $action){
        return $this->entityManager->getRepository(Role\RoleAction::getClass())->findOneBy(array('role' => $role, 'controllerAction' => $action));
    }

    public function createController($name, $code, $translationKey){
        $controller = new Controller();
        $controller->setName($name);
        $controller->setCode($code);
        $controller->setTranslationKey($translationKey);

        $this->entityManager->persist($controller);
        $this->entityManager->flush($controller);

        return $controller;
    }

    public function createControllerAction($name, $translationKey, Controller $controller){
        $action = new Controller\Action();
        $action->setName($name);
        $action->setTranslationKey($translationKey);
        $action->setController($controller);

        $this->entityManager->persist($action);
        $this->entityManager->flush($action);

        return $action;
    }

} 