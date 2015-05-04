<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 27.04.15
 * Time: 7:21
 */

namespace Application\Controller;


use Application\Entity\Role;
use Application\Service\AdminService;
use Application\Service\UserService;
use Application\Util\StringUtil;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Parameters;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController {

    const AUTHORIZE_CLASS = 'controller/Application\Controller\Admin:';
    const NAV_KEY_ROLE = 'role';
    const NAV_KEY_USER = 'user';
    /**
     * @var AdminService
     */
    protected $adminService;
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param AdminService $userService
     */
    public function setAdminService(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * @param UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function roleAction(){
        $view = new ViewModel();
        $userData = $this->getUserData();
        $roles = $this->adminService->getRolesByCompany($userData->company);
        $view->roles = $this->getPaginatedResult($roles, $this->params('page'));
        $view->navKey = self::NAV_KEY_ROLE;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->company = $userData->company;
        return $view;
    }

    public function addRoleAction(){
        $userData = $this->getUserData();
        $controllers = $this->adminService->getControllers();
        $form = $this->getServiceLocator()->get('Application\Form\Role')->setCompany($userData->company)->init();
        $view = new ViewModel();
        $view->setTemplate('application/admin/role/add-role');
        $translator = $this->getTranslator();
        if($this->request->isPost()){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $role = $this->adminService->addRoleAndActions($this->request->getPost(), $userData->company);
                $this->flashMessenger()->addMessage($translator->translate('Admin.role.add.successMessage'));
                return $this->redirect()->toRoute('edit-role', ['id' => $role->getId()], true);
            }
        }
        $view->navKey = self::NAV_KEY_ROLE;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->controllers = $controllers;
        $view->form = $form;
        return $view;
    }

    public function editRoleAction(){
        $role = $this->adminService->getRoleById($this->params('id'));
        $userData = $this->getUserData();
        if(!$role || $role->getCompany() !== $userData->company){
            return $this->notFoundAction();
        }

        $controllers = $this->adminService->getControllers();
        $form = $this->getServiceLocator()->get('Application\Form\Role')->setRole($role)->setCompany($userData->company)->init();
        $form->setFormValues($role);
        $view = new ViewModel();
        $view->setTemplate('application/admin/role/edit-role');
        $translator = $this->getTranslator();
        if($this->request->isPost()){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $this->adminService->editRoleAndActions($this->request->getPost(), $role);
                $this->flashMessenger()->addMessage($translator->translate('Admin.role.edit.successMessage'));
                return $this->redirect()->toRoute('edit-role', ['id' => $role->getId()], true);
            }
        }
        $view->navKey = self::NAV_KEY_ROLE;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->controllers = $controllers;
        $view->form = $form;
        $view->role = $role;
        return $view;
    }

    public function userAction(){
        $userData = $this->getUserData();
        $users = $this->userService->getCompanyUsers($userData->company);
        $view = new ViewModel();
        $view->navKey = self::NAV_KEY_USER;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->users = $this->getPaginatedResult($users, $this->params('page'));
        $view->userStatusArray = $this->adminService->getUserStatusSelect();
        return $view;
    }

    public function addUserAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        $form = $this->getServiceLocator()->get('Application\Form\Register'); /* @var $form \Application\Form\RegisterForm */
        $form->setConfigOptions($this->adminService->getCompanyUserFormConfiguration());
        $form->setCompany($userData->company);
        $form->init();
        $translator = $this->getTranslator();
        if($this->request->isPost()){
            $params = $this->request->getPost();
            $form->setData($params);{
                if($form->isValid()){
                    $params->company = $userData->company;
                    $user = $this->userService->saveUser(null, $params);
                    $this->flashMessenger()->addMessage($translator->translate('Admin.user.add.successMessage'));
                    return $this->redirect()->toRoute('company-user', [], true);
                }
            }
        }
        $view->setTemplate('application/admin/user/add-user');
        $view->navKey = self::NAV_KEY_USER;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->form = $form;
        return $view;
    }

    public function editUserAction(){
        $userData = $this->getUserData();
        $user = $this->userService->getUserById($this->params('id'));
        if(!$user || $user->getCompany() !== $userData->company || ($user->isMaster() && !$userData->user->isMaster())){
            return $this->notFoundAction();
        }
        $view = new ViewModel();
        $form = $this->getServiceLocator()->get('Application\Form\Register'); /* @var $form \Application\Form\RegisterForm */
        $form->setUser($user);
        $form->setConfigOptions($this->adminService->getCompanyUserFormConfiguration());
        $form->setCompany($userData->company);
        $form->init();
        $form->populateWithValues();
        $translator = $this->getTranslator();
        if($this->request->isPost()){
            $params = $this->request->getPost();
            if(isset($params->isP) && $params->isP == 0){
                $form->removePasswordValidation();
            }
            $form->setData($params);{
                if($form->isValid()){
                    $this->userService->saveUser($user, $params);
                    $this->flashMessenger()->addMessage($translator->translate('Admin.user.edit.successMessage'));
                    return $this->redirect()->toRoute('company-user', [], true);
                }
            }
        }
        $view->setTemplate('application/admin/user/edit-user');
        $view->navKey = self::NAV_KEY_USER;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->form = $form;
        $view->user = $user;
        return $view;
    }


    public function ifRoleExistsAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $userData = $this->getUserData();
            $roleId = $userData->company->getId() . '-' . StringUtil::urlify($this->request->getQuery()->name);
            $exclude = $this->request->getQuery()->excude;
            $role = $this->adminService->getRoleByRoleId($roleId);
            $result = (!$role || ($role && $role->getId() == $exclude)) ? array(1) : array(0);

            return new JsonModel($result);
        }

        return $this->response;
    }


    private function getUserData(){
        $data = new Parameters();
        $user = $this->currentdata()->getCurrentUser();
        $company = $user->getCompany();
        $data->company = $company;
        $data->user = $user;
        return $data;
    }

    private function getPaginatedResult(array $collection, $currentPageNumber, $pageRange = 10, $cntPerPage = 10){
        $paginated = new Paginator(new ArrayAdapter($collection));
        $paginated->setCurrentPageNumber($currentPageNumber);
        $paginated->setPageRange($pageRange);
        $paginated->setItemCountPerPage($cntPerPage);

        return $paginated;
    }

    private function getTranslator(){
        return $this->serviceLocator->get('MvcTranslator');
    }
} 