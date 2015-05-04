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
use Application\Entity\User;
use Application\Util\StringUtil;
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

    /**
     * @param $roleId
     * @return null|Role
     */
    public function getRoleByRoleId($roleId){
        return $this->entityManager->getRepository(Role::getClass())->findOneBy(array('roleId' => $roleId));
    }

    /**
     * @param $id
     * @return null|Role
     */
    public function getRoleById($id){
        return $this->entityManager->getRepository(Role::getClass())->findOneBy(array('id' => $id));
    }


    public function updateControllers(){
        $controllers = include 'controllerArray/controllers.php';
        foreach($controllers as $controller){
            $controllerEntity = $this->getControllerByCode($controller['code']);
            $controllerEntity = $controllerEntity ? $this->checkIfControllerDataDifferent($controllerEntity, $controller) : $this->createController($controller['name'], $controller['code'], $controller['order_no'], $controller['key']);
            foreach($controller['actions'] as $action){
                $controllerAction = $this->getControllerActionByNameAndController($action['name'], $controllerEntity);
                if(!$controllerAction){
                    $this->createControllerAction($action['name'], $action['key'], $action['is_navigation'], $action['order_no'], $controllerEntity);
                } elseif($controllerAction){
                    $this->checkIfActionDataDifferent($controllerAction, $action);
                }
            }
        }

        $this->updateControllerActions();
    }

    private function checkIfActionDataDifferent(Controller\Action $action, array $actionData){
        $isDifferent = false;
        if($action->getOrderNumber() !== $actionData['order_no']){
            $action->setOrderNumber($actionData['order_no']);
            $isDifferent = true;
        }
        if($action->isNavigation() !== $actionData['is_navigation']){
            $action->setIsNavigation($actionData['is_navigation']);
            $isDifferent = true;
        }
        if($isDifferent){
            $this->entityManager->persist($action);
            $this->entityManager->flush($action);
        }
        return $action;
    }

    private function checkIfControllerDataDifferent(Controller $controller, array $controllerData){
        $isDifferent = false;
        if($controller->getOrderNumber() !== $controllerData['order_no']){
            $controller->setOrderNumber($controllerData['order_no']);
            $isDifferent = true;
        }
        if($isDifferent){
            $this->entityManager->persist($controller);
            $this->entityManager->flush($controller);
        }
        return $controller;
    }

    public function updateControllerActions(){
        $currentActionIds = array();
        $allActionIds = array();
        $controllers = include 'controllerArray/controllers.php';
        foreach($controllers as $controller){
            foreach($controller['actions'] as $action){
                $controllerAction = $this->getControllerActionByName($action['name']);
                if($controllerAction){
                    $currentActionIds[] = $controllerAction->getId();
                }
            }
        }

        foreach($this->getControllerActions() as $action){
            $allActionIds[] = $action->getId();
        }

        $removedActionIds = array_diff($allActionIds, $currentActionIds);
        if(count($removedActionIds) > 0){
            foreach($removedActionIds as $id){
                $action = $this->getControllerActionById($id);
                if($action){
                    $this->removeControllerAction($action);
                }
            }
        }

    }

    public function removeControllerAction(Controller\Action $action){
        foreach($this->getRoleActionsByAction($action) as $roleAction){
            $this->entityManager->remove($roleAction);
            $this->entityManager->flush($roleAction);
        }

        $this->entityManager->remove($action);
        $this->entityManager->flush($action);
    }


    /**
     * @param Parameters $data
     * @param Company $company
     * @return Role
     */
    public function addRoleAndActions(Parameters $data, Company $company){
        $isAllowedArray = $data->isAllowed;
        $role = $this->createCompanyRole($company, $data->name);
        foreach($this->getControllerActions() as $action){ /* @var $action \Application\Entity\Controller\Action */
            $isAllowed = isset($isAllowedArray[$action->getId()]);
            $this->createRoleAction($role, $action, $isAllowed);
        }
        return $role;
    }

    public function editRoleAndActions(Parameters $data, Role $role){
        $role = $this->editRole($role, $data->name);
        $isAllowedArray = $data->isAllowed;
        foreach($this->getControllerActions() as $action){ /* @var $action \Application\Entity\Controller\Action */
            $isAllowed = isset($isAllowedArray[$action->getId()]);
            $roleAction = $this->getRoleActionByRoleAndAction($role, $action);
            if(!$roleAction){
                $this->createRoleAction($role, $action, $isAllowed);
            } else {
                if($roleAction->isEnabled() !== $isAllowed){
                    $this->editRoleAction($roleAction, $isAllowed);
                }
            }
        }
    }

    public function editRole(Role $role, $name){
        if($name !== $role->getName()){
            $role->setName($name);
            $role->setRoleId($role->getCompany()->getId() . '-' . StringUtil::urlify($name));
        }
        $this->entityManager->persist($role);
        $this->entityManager->flush($role);

        return $role;
    }

    public function editRoleAction(Role\RoleAction $roleAction, $isAllowed = false){
        $roleAction->setIsEnabled($isAllowed);
        $this->entityManager->persist($roleAction);
        $this->entityManager->flush($roleAction);

        return $roleAction;
    }

    public function createRoleAction(Role $role, Controller\Action $action, $isAllowed = false){
        $roleAction = new Role\RoleAction();
        $roleAction->setControllerAction($action);
        $roleAction->setRole($role);
        $roleAction->setIsEnabled($isAllowed);

        $this->entityManager->persist($roleAction);
        $this->entityManager->flush($roleAction);

        return $roleAction;
    }

    public function createCompanyRole(Company $company, $roleName){
        $role = new Role();
        $role->setCompany($company);
        $role->setParent($this->getGuestRole());
        $role->setName($roleName);
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
        return $this->entityManager->getRepository(Controller::getClass())->findBy(array(), array('orderNumber' => 'ASC' ));
    }

    public function getControllerActions(){
        return $this->entityManager->getRepository(Controller\Action::getClass())->findBy(array(), array('orderNumber' => 'ASC'));
    }

    public function getControllerActionByNameAndController($name, Controller $controller){
        return $this->entityManager->getRepository(Controller\Action::getClass())->findOneBy(array('name' => $name, 'controller' => $controller));
    }

    /**
     * @param $name
     * @return null|Controller\Action
     */
    public function getControllerActionByName($name){
        return $this->entityManager->getRepository(Controller\Action::getClass())->findOneBy(array('name' => $name));
    }

    /**
     * @param $id
     * @return null|Controller\Action
     */
    public function getControllerActionById($id){
        return $this->entityManager->getRepository(Controller\Action::getClass())->findOneBy(array('id' => $id));
    }

    /**
     * @param Role $role
     * @param Controller\Action $action
     * @return null|Role\RoleAction
     */
    public function getRoleActionByRoleAndAction(Role $role, Controller\Action $action){
        return $this->entityManager->getRepository(Role\RoleAction::getClass())->findOneBy(array('role' => $role, 'controllerAction' => $action));
    }

    public function getRoleActionsByAction(Controller\Action $action){
        return $this->entityManager->getRepository(Role\RoleAction::getClass())->findBy(array('controllerAction' => $action));
    }

    public function getRoleActionsByRole(Role $role){
        return $this->entityManager->getRepository(Role\RoleAction::getClass())->findBy(array('role' => $role));
    }

    public function createController($name, $code, $orderNo, $translationKey){
        $controller = new Controller();
        $controller->setName($name);
        $controller->setCode($code);
        $controller->setTranslationKey($translationKey);
        $controller->setOrderNumber($orderNo);

        $this->entityManager->persist($controller);
        $this->entityManager->flush($controller);

        return $controller;
    }

    public function createControllerAction($name, $translationKey, $isNavigation, $orderNo, Controller $controller){
        $action = new Controller\Action();
        $action->setName($name);
        $action->setTranslationKey($translationKey);
        $action->setController($controller);
        $action->setIsNavigation($isNavigation);
        $action->setOrderNumber($orderNo);

        $this->entityManager->persist($action);
        $this->entityManager->flush($action);

        return $action;
    }

    public function getUserAuthorizedActions(){
        $result = array();
        $user = $this->userService->getCurrentUser();
        if($user && count($user->getRoles())){
            foreach($user->getRoles() as $role){
                $roleActions = $this->getRoleActionsByRole($role);
                if(count($roleActions) > 0) {
                    foreach($roleActions as $roleAction){
                        $roleArray = array(
                            Role::ROLE_ADMIN
                        );
                        if($role->getRoleId() !== Role::ROLE_ADMIN && $roleAction->isEnabled()){
                            $roleArray[] = $role->getRoleId();
                        }
                        $controllerAction = $roleAction->getControllerAction(); /* @var $controllerAction \Application\Entity\Controller\Action */
                        $controller = $controllerAction->getController(); /* @var $controller \Application\Entity\Controller */
                        $subResult = array(
                            'controller' => $controller->getName(),
                            'action' => $controllerAction->getName(),
                            'roles' => $roleArray
                        );
                        if(!in_array($subResult, $result)){
                            $result[] = $subResult;
                        }
                    }
                } else {
                    $controllers = include 'controllerArray/controllers.php';
                    foreach($controllers as $controller){
                        foreach($controller['actions'] as $action){
                            $subResult = array(
                                'controller' => $controller['name'],
                                'action' => $action['name'],
                                'roles' => array(Role::ROLE_ADMIN)
                            );
                            if(!in_array($subResult, $result)){
                                $result[] = $subResult;
                            }
                        }
                    }
                }

            }
        }

        return $result;

    }

    public function getRoleSelectForCompany(Company $company){
        $translator = $this->locator->get('Translator');
        $adminRole = $this->userService->getAdminRoleEntity();
        $result[$adminRole->getId()] = $translator->translate('SystemRoles.admin');
        $companyRoles = $this->getRolesByCompany($company);
        foreach($companyRoles as $companyRole){
            $result[$companyRole->getId()] = $companyRole->getName();
        }

        return $result;
    }

    public function getUserStatusSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            User::STATUS_ACTIVE => $translator->translate('User.status.active'),
            User::STATUS_INACTIVE => $translator->translate('User.status.inActive')
        );
    }

    public function getCompanyUserFormConfiguration(){
        $configOptions = $this->locator->get('Config')['registerOptions'];
        $configOptions['addPhone'] = true;
        $configOptions['addRole'] = true;
        $configOptions['addStatus'] = true;

        return $configOptions;
    }

} 