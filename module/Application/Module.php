<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $eventManager->getSharedManager()->attach('Zend\Mvc\Application', MvcEvent::EVENT_ROUTE, array(
            $this,
            'initializeLanguage'
        ), - 100);

        $this->initializeSessions($e);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function initializeLanguage(MvcEvent $e) {
        $locator = $e->getApplication()->getServiceManager();
        $userService = $locator->get('Application\Service\User');
        $config = $locator->get('Config');

        $defaultIdentificator = $config['languages']['defaultIdentificator'];
        $defaultLocale = $config['languages']['defaultLocale'];

        $language = $e->getRouteMatch() != null ? $e->getRouteMatch()->getParam('language') : null;

        if ($language == null) {
            $language = $defaultIdentificator;
        }

        /* @var $sessionManager \Zend\Session\SessionManager */
        $sessionManager = $locator->get('Zend\Session\SessionManager');
        $sessionStorage = $sessionManager->getStorage();

        if (!$sessionStorage->offsetExists('language') || $sessionStorage->offsetGet('language') == null) {
            $sessionStorage->offsetSet('language', $defaultIdentificator);
            $sessionStorage->offsetSet('locale', $defaultLocale);
        }
        if ($e->getRouteMatch() != null) {
            $e->getRouteMatch()->setParam('language', $language);
        }
        if ($language != $sessionStorage->offsetGet('language')) {
            $locales = $config['languages']['available'];
            $sessionStorage->offsetSet('language', $language);
            if(array_key_exists($language, $locales)){
                $sessionStorage->offsetSet('locale', $locales[$language]);
            }
        }

        $this->addLayoutParams($e);

        \Locale::setDefault($sessionStorage->offsetGet('locale'));

        $translator = $locator->get('Translator');
        $translator->setLocale($sessionStorage->offsetGet('locale'));

        AbstractValidator::setDefaultTranslator($translator);
    }

    public function initializeSessions($e) {
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 1000);
        $locator = $e->getApplication()->getServiceManager();
        /* @var $sessionManager \Zend\Session\SessionManager */
        $sessionManager = $locator->get('Zend\Session\SessionManager');
        $sessionManager->start();
    }

    public function addLayoutParams(MvcEvent $e){

        $locator = $e->getApplication()->getServiceManager();
        $userService = $locator->get('Application\Service\User');
        $config = $locator->get('Config');

        /* @var $sessionManager \Zend\Session\SessionManager */
        $sessionManager = $locator->get('Zend\Session\SessionManager');
        $sessionStorage = $sessionManager->getStorage();

        $id = $e->getRouteMatch() != null ? $e->getRouteMatch()->getParam('id') : null;
        $page = $e->getRouteMatch() != null ? $e->getRouteMatch()->getParam('page') : null;
        $hash = $e->getRouteMatch() != null ? $e->getRouteMatch()->getParam('hash') : null;
        $provider = $e->getRouteMatch() != null ? $e->getRouteMatch()->getParam('provider') : null;

        $serviceManager = $e->getApplication()->getServiceManager();
        $viewRenderer = $serviceManager->get('viewRenderer');
        $viewRenderer->layout()->config = $config;
        $viewRenderer->layout()->language = $sessionStorage->offsetGet('language');
        $viewRenderer->layout()->currentUser = $userService->getCurrentUser();
        if($id > 0){
            $viewRenderer->layout()->id = $id;
        }
        if($page > 0){
            $viewRenderer->layout()->page = $page;
        }
        if(strlen($hash) > 0){
            $viewRenderer->layout()->hash = $hash;
        }
        if(strlen($provider) > 0){
            $viewRenderer->layout()->provider = $provider;
        }
    }
}
