<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 27.04.15
 * Time: 7:21
 */

namespace Application\Controller;


use Application\Service\AdminService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController {

    const AUTHORIZE_CLASS = 'controller/Application\Controller\Admin:';
    const NAV_KEY_ROLE = 'role';
    /**
     * @var AdminService
     */
    protected $adminService;

    /**
     * @param AdminService $userService
     */
    public function setAdminService(AdminService $adminService)
    {
        $this->adminService = $adminService;
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
        $controllers = $this->adminService->getControllers();
        $view = new ViewModel();
        $view->setTemplate('application/admin/role/add-role');
        if($this->request->isPost()){
            $userData = $this->getUserData();
            $this->adminService->addRoleAndActions($this->request->getPost(), $userData->company);
            return $this->redirect()->toRoute('role', [], true);
        }
        $view->navKey = self::NAV_KEY_ROLE;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->controllers = $controllers;
        return $view;
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