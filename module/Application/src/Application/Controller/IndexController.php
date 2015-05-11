<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Service\UserService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    const NAV_KEY_DASHBOARD = 'dashboard';
    const DEFAULT_REDIRECT_ROUTE = 'dashboard';
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

    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login', [], true);
        }
        $user = $this->currentdata()->getCurrentUser();
        $redirectRoute = $user->getRoles()->last()->getRedirectRoute() !== null ? $user->getRoles()->last()->getRedirectRoute() : self::DEFAULT_REDIRECT_ROUTE;
        return $this->redirect()->toRoute($redirectRoute, ['language' => $user->getLanguageCode()]);
    }

    public function dashboardAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->notFoundAction();
        }
        $view = new ViewModel();
        $view->navKey = self::NAV_KEY_DASHBOARD;
        return $view;
    }

    public function emailExistsAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $userService = $this->serviceLocator->get('Application\Service\User'); /* @var $userService \Application\Service\UserService */
            $user = $userService->getUserByEmail($this->request->getQuery()->email);
            $exclude = (isset($this->request->getQuery()->exclude) && $user && $user->getId() == $this->request->getQuery()->exclude) ? true : false;
            $result = ($user && !$exclude) ? array(1) : array(0);

            return new JsonModel($result);
        }

        return $this->response;
    }

    public function isPasswordValidAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $email = $this->request->getQuery()->email;
            $password = $this->request->getQuery()->password;
            $isValid = $this->userService->isPasswordValid($email, $password);

            return new JsonModel(array($isValid ? 1 : 0));
        }

        return $this->response;
    }

    public function forgotPasswordAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $user = $this->userService->getUserByEmail($this->request->getQuery()->email);
            $language = $this->request->getQuery()->language;
            $isSent = false;
            if($user){
                $passwordLink = $this->userService->generatePasswordLink($user, $language);
                $isSent = true;
            }

            return new JsonModel(array($isSent ? 1 : 0));
        }

        return $this->response;
    }

    public function getUnitAddFormAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $form = $this->getServiceLocator()->get('Application\Form\Unit')->init();
            $this->getTranslator($this->request->getQuery()->locale);
            $view = new ViewModel();
            $view->setTemplate('form/add/unit-form');
            $view->setTerminal(true);
            $view->form = $form;

            return $view;
        }

        return $this->response;
    }

    public function getVatAddFormAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $form = $this->getServiceLocator()->get('Application\Form\Vat')->init();
            $this->getTranslator($this->request->getQuery()->locale);
            $view = new ViewModel();
            $view->setTemplate('form/add/vat-form');
            $view->setTerminal(true);
            $view->form = $form;

            return $view;
        }

        return $this->response;
    }

    public function getBrandAddFormAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $form = $this->getServiceLocator()->get('Application\Form\Brand')->init();
            $this->getTranslator($this->request->getQuery()->locale);
            $view = new ViewModel();
            $view->setTemplate('form/add/brand-form');
            $view->setTerminal(true);
            $view->form = $form;

            return $view;
        }

        return $this->response;
    }

    public function getCategoryAddFormAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $form = $this->getServiceLocator()->get('Application\Form\Category')->init();
            $this->getTranslator($this->request->getQuery()->locale);
            $view = new ViewModel();
            $view->setTemplate('form/add/category-form');
            $view->setTerminal(true);
            $view->form = $form;

            return $view;
        }

        return $this->response;
    }

    public function getClientDataAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $clientService = $this->getServiceLocator()->get('Application\Service\Client');
            $clientData = $clientService->getClientDataForAjax($this->request->getQuery()->id);

            return new JsonModel($clientData);
        }

        return $this->response;
    }

    private function getTranslator($locale = null){
        $translator =  $this->serviceLocator->get('MvcTranslator');
        if($locale){
            $translator->setLocale($locale);
        }
        return $translator;
    }

    public function getClientAddFormAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $userData = $this->getUserData();
            $form = $this->getServiceLocator()->get('Application\Form\Subject\Client')->setCompany($userData->company)->init();
            $form->setDefaultClientUser($userData->user);
            $this->getTranslator($this->request->getQuery()->locale);
            $view = new ViewModel();
            $view->setTemplate('form/add/client-form');
            $view->setTerminal(true);
            $view->form = $form;

            return $view;
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


}
