<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 25.02.15
 * Time: 18:00
 */

namespace Application\Controller;



use Application\Common\SocialStorage;
use ScnSocialAuthDoctrineORM\Options\ModuleOptions;
use Zend\Session\Container;
use Zend\Stdlib\Parameters;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use BitWeb\IdServices\Authentication\Exception\AuthenticationException;
use BitWeb\IdServices\Authentication\IdCard\Authentication;
use BitWeb\IdServices\Authentication\Exception\AuthenticationException as BitWebAuthenticationException;

class UserController extends \ZfcUser\Controller\UserController {

    protected $socialOptions;

    const MOBILE_ID_SESSION_COOKIE = 'mobileIdAuthSession';

    public function indexAction()
    {
        return parent::indexAction();
    }

    /**
     * @return array|object|\Application\Service\UserService
     */
    public function getUserService(){
        return $this->getServiceLocator()->get('Application/Service/User');
    }

    public function loginAction()
    {
        $this->layout('layout/empty');
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $request = $this->getRequest();
        $form    = $this->getLoginForm();

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        $config = $this->getServiceLocator()->get('Config');
        $idLogin = isset($config['id-services']['enable-id-login']) ? $config['id-services']['enable-id-login'] : false;
        $mobileIdLogin = isset($config['id-services']['enable-mobile-id-login']) ? $config['id-services']['enable-mobile-id-login'] : false;
        $view = new ViewModel();
        $userService = $this->getServiceLocator()->get('Application\Service\User'); /* @var $userService \Application\Service\UserService */

        $view->idLogin = $idLogin;
        $view->mobileIdLogin = $mobileIdLogin;
        $view->loginForm = $form;
        $view->redirect = $redirect;
        $view->enableRegistration = $this->getOptions()->getEnableRegistration();
        $view->providers = $userService->getEnabledProviders();

        if (!$request->isPost()) {
            return $view;
        }

        $form->setData($request->getPost());

        if (!$form->isValid()) {
            $this->flashMessenger()->setNamespace('zfcuser-login-form')->addMessage($this->failedLoginMessage);
            return $this->redirect()->toUrl($this->url()->fromRoute(static::ROUTE_LOGIN).($redirect ? '?redirect='. rawurlencode($redirect) : ''));
        }

        // clear adapters
        $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
        $this->zfcUserAuthentication()->getAuthService()->clearIdentity();

        return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate', 'language' => $this->params('language')));
    }


    public function logoutAction()
    {
        $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
        $this->zfcUserAuthentication()->getAuthAdapter()->logoutAdapters();
        $this->zfcUserAuthentication()->getAuthService()->clearIdentity();

        SocialStorage::resetSocialStorage();

        $redirect = $this->params()->fromPost('redirect', $this->params()->fromQuery('redirect', false));

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        return $this->redirect()->toRoute($this->getOptions()->getLogoutRedirectRoute(), [], true);
    }

    public function authenticateAction()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute(), [], true);
        }

        $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        $redirect = $this->params()->fromPost('redirect', $this->params()->fromQuery('redirect', false));

        $result = $adapter->prepareForAuthentication($this->getRequest());

        // Return early if an adapter returned a response
        if ($result instanceof Response) {
            return $result;
        }

        $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);

        if (!$auth->isValid()) {
            $this->flashMessenger()->setNamespace('zfcuser-login-form')->addMessage($this->failedLoginMessage);
            $adapter->resetAdapters();
            return $this->redirect()->toUrl(
                $this->url()->fromRoute(static::ROUTE_LOGIN) .
                ($redirect ? '?redirect='. rawurlencode($redirect) : '')
            );
        }

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        $userService = $this->getServiceLocator()->get('Application\Service\User'); /* @var $userService \Application\Service\UserService */
        $adminRole = $userService->getAdminRoleEntity();
        $config = $this->getServiceLocator()->get('Config');
        $adminRoute = isset($config['zfcuser']['admin_login_redirect_route']) ? $config['zfcuser']['admin_login_redirect_route'] : null;
        $user = $this->zfcUserAuthentication()->getIdentity();

        $redirectRoute = ($user && $user->getRoles()->contains($adminRole) && $adminRoute) ? $adminRoute : $this->getOptions()->getLoginRedirectRoute();

        return $this->redirect()->toRoute($redirectRoute, [], true);
    }

    public function registerAction()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute(), [], true);
        }

        $this->layout('layout/empty');

        $request = $this->getRequest();
        $view = new ViewModel();
        $form = $this->getServiceLocator()->get('Application\Form\Register')->init();
        if($this->request->isPost()){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $userService = $this->getServiceLocator()->get('Application\Service\User'); /* @var $userService \Application\Service\UserService */
                $params = new Parameters($form->getData());
                $user = $userService->register($params);
                $userService->setRoleAfterRegister($user, $params);
                $data = new Parameters();
                $data->identity = $this->request->getPost()->email;
                $data->credential = $this->request->getPost()->password;
                $request->setPost($data);
                return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate', 'language' => $this->params('language')));
            }
        }
        $view->registerForm = $form;
        $view->enableRegistration = true;

        return $view;

    }

    public function changepasswordAction()
    {
        return parent::changepasswordAction();
    }

    public function changeEmailAction()
    {
        return parent::changeEmailAction();
    }

    public function forgotPasswordAction() {

        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $this->layout('layout/empty');

        $userService = $this->serviceLocator->get('Application\Service\User'); /* @var $userService \Application\Service\UserService */
        $form = $this->serviceLocator->get('Application\Form\ForgotPassword')->init();
        if ($this->request->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $user = $userService->getUserByEmail($form->get('email')->getValue());
                $translator = $this->serviceLocator->get('MvcTranslator');
                if($user){
                    $passwordLink = $userService->generatePasswordLink($user, $this->params('language'));
                    $this->flashMessenger()->addMessage($translator->translate('Application.user.message.passwordLinkSent'));
                    $this->redirect()->toRoute('home', [], true);
                }

            }
        }
        $view = new ViewModel();
        $view->form = $form;
        $view->messages = $this->flashMessenger()->getMessages();

        return $view;
    }

    public function newPasswordAction()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $this->layout('layout/empty');

        $userService = $this->serviceLocator->get('Application\Service\User'); /* @var $userService \Application\Service\UserService */

        $hash = $this->params('hash');
        $flashMessenger = $this->flashMessenger();
        $form = $this->serviceLocator->get('Application\Form\NewPassword')->init();
        $passwordHashEntity = $userService->getPasswordLinkByHash($hash);
        if ($hash === null) {
            return $this->notFoundAction();
        }

        if ($passwordHashEntity === null || ! $userService->checkPasswordLinkLife($passwordHashEntity)) {
            $flashMessenger->addErrorMessage('Application.user.message.noHash');
            return $this->notFoundAction();
        }

        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $newPassword = $form->getInputFilter()->getValue('password');
                $user = $passwordHashEntity->getUser();
                $userService->changePassword($user, $newPassword);
                $translator = $this->serviceLocator->get('MvcTranslator');
                $this->flashMessenger()->addMessage($translator->translate('Application.user.message.passwordChanged'));
                return $this->redirect()->toRoute('home', [], true);
            }
        }

        $view = new ViewModel();
        $view->form = $form;
        $view->messages = $this->flashMessenger()->getMessages();

        return $view;
    }

    public function idCardLoginAction()
    {
        $error = null;
        try {
            $idCardUser = Authentication::getLoggedInUser();
        } catch (BitWebAuthenticationException $e) {
            return $this->redirect()->toRoute('zfcuser/login', [], true);
        }
        $email = $this->params()->fromQuery('email');
        $user = $this->getUserService()->getUserByPersonalCodeAndEmail($idCardUser->getSocialSecurityNumber(), $email);
        if ($user === null) {
            return $this->redirect()->toRoute('zfcuser/login', [], true);
        }
        if (!$this->getUserService()->loginWithIdCard($user)) {
            return $this->redirect()->toRoute('zfcuser/login', [], true);
        }
        return $this->redirect()->toRoute('home');
    }

    public function mobileIdAuthenticateAction()
    {
        $error = null;
        $code = null;
        if ($this->getRequest()->isPost()) {
            try {
                $email = $this->getRequest()->getPost()->email;
                $phone = $this->getRequest()->getPost()->phone;
                $result = $this->getUserService()->mobileIdAuthenticateStart($email, $phone);
                $code = $result->getChallengeID();
                $container = new Container(static::MOBILE_ID_SESSION_COOKIE);
                $container->offsetSet('id',    $result->getSessCode());
                $container->offsetSet('email', $email);
                $container->offsetSet('phone', $phone);
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }

        } else {
            $error = 'Only POST requests allowed!';
        }

        return new JsonModel([
            'code'  => $code,
            'error' => $error,
        ]);
    }

    public function mobileIdAuthenticateStatusAction()
    {
        $translator = $this->getServiceLocator()->get('Translator');
        $error = null;
        $status = false;
        if ($this->getRequest()->isPost()) {
            $container = new Container(static::MOBILE_ID_SESSION_COOKIE);
            if (
                $container->offsetGet('email') === $this->params()->fromPost('email')
                && $container->offsetGet('phone') === ('+372' . $this->params()->fromPost('phone'))
                && $container->offsetExists('id')
            ) {
                try {
                    $result = $this->getUserService()->mobileIdAuthenticateStatus(
                        $container->offsetGet('id'),
                        $container->offsetGet('email')
                    );
                    $status = $result->getStatus();
                    if ($status === 'USER_AUTHENTICATED') {
                        $this->getUserService()->loginWithMobileId($container->offsetGet('email'));
                        $container->getManager()->getStorage()->clear(static::MOBILE_ID_SESSION_COOKIE);
                    }
                } catch (AuthenticationException $e) {
                    $error = $e->getMessage();
                }
            } else {
                $error = $translator->translate('IdAuth.errorMessage.SESSION_DATA_NOT_VALID');
            }
        } else {
            $error = 'Only POST requests allowed!';
        }
        return new JsonModel([
            'error'  => $error,
            'status' => $status
        ]);
    }

    public function mobileIdAuthenticateCancelAction()
    {
        $translator = $this->getServiceLocator()->get('Translator');
        $error = null;
        $success = false;
        if ($this->getRequest()->isPost()) {
            $container = new Container(static::MOBILE_ID_SESSION_COOKIE);
            if (
                $container->offsetGet('email') === $this->params()->fromPost('email')
                && $container->offsetGet('phone') === ('+372' . $this->params()->fromPost('phone'))
                && $container->offsetExists('id')
            ) {
                $container->getManager()->getStorage()->clear(static::MOBILE_ID_SESSION_COOKIE);
            } else {
                $error = $translator->translate('IdAuth.errorMessage.SESSION_DATA_NOT_VALID');
            }
        } else {
            $error = 'Only POST requests allowed!';
        }
        return new JsonModel([
            'error'   => $error,
            'success' => $success
        ]);
    }

    public function getSocialOptions()
    {
        return $this->getServiceLocator()->get('ScnSocialAuth-ModuleOptions');
    }


} 