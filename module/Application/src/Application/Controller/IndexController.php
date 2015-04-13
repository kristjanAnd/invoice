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
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
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
        return new ViewModel();
    }

    public function emailExistsAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $userService = $this->serviceLocator->get('Application\Service\User'); /* @var $userService \Application\Service\UserService */
            $user = $userService->getUserByEmail($this->request->getQuery()->email);
            $result = ($user) ? array(1) : array(0);

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
}
