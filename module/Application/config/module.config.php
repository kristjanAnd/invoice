<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

use Application\Controller\IndexController;
use Application\Controller\UserController;
use Application\Form\ForgotPassword;
use Application\Form\NewPassword;
use Application\Form\RegisterForm;
use Application\Service\AuthenticationService;
use Application\Service\MailService;
use Application\Service\UserService;
use Application\View\Helper\CurrentRoute;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;
use Zend\View\HelperPluginManager;

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/[:language]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                        'language' => 'us'
                    ),
                ),
            ),
            'zfcuser' => array(
                'type' => 'Segment',
                'priority' => 1000,
                'options' => array(
                    'route' => '[/:language]/user',
                    'defaults' => array(
                        'controller' => 'zfcuser',
                        'action'     => 'index',
                        'language' => 'us'
                    ),
                ),
                'child_routes' => array(
                    'forgot-password' => array (
                        'type' => 'Segment',
                        'options' => array (
                            'route' => '/forgot-password',
                            'defaults' => array (
                                'controller' => 'zfcuser',
                                'action' => 'forgotPassword',
                            )
                        )
                    ),
                    'new-password' => array (
                        'type' => 'Segment',
                        'options' => array (
                            'route' => '/new-password/:hash',
                            'defaults' => array (
                                'controller' => 'zfcuser',
                                'action' => 'newPassword',
                            )
                        )
                    ),
                    'mobile-id-authenticate' => array (
                        'type' => 'Literal',
                        'options' => array (
                            'route' => '/mobile-id-authenticate',
                            'defaults' => array (
                                'controller' => 'zfcuser',
                                'action' => 'mobile-id-authenticate',
                            )
                        )
                    ),
                    'mobile-id-authenticate-status' => array (
                        'type' => 'Literal',
                        'options' => array (
                            'route' => '/mobile-id-authenticate-status',
                            'defaults' => array (
                                'controller' => 'zfcuser',
                                'action' => 'mobile-id-authenticate-status',
                            )
                        )
                    ),
                    'id-card-login' => array (
                        'type' => 'Literal',
                        'options' => array (
                            'route' => '/id-card-login',
                            'defaults' => array (
                                'controller' => 'zfcuser',
                                'action' => 'id-card-login',
                            )
                        )
                    ),
                ),
            ),

            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array (
                'type' => 'Segment',
                'options' => array (
                    'route' => '[/:language]/application[/:controller[/:action]]',
                    'constraints' => array (
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => 'us'
                    ),
                    'defaults' => array (
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array (
                    'default' => array (
                        'type' => 'wildcard'
                    )
                )
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => array(
            'ScnSocialAuth\Authentication\Adapter\HybridAuth' => 'Application\Service\HybridAuthAdapterFactory',
            'Zend\Session\SessionManager' => function (ServiceManager $sm) {
                $sessionManager = new \Zend\Session\SessionManager();
                $configuration = $sm->get('Config');
                if (isset($configuration['sessionConfiguration'])) {
                    $sessionConfig = new \Zend\Session\Config\SessionConfig();
                    if (isset($configuration['sessionConfiguration']['rememberMeSeconds'])) {
                        $sessionConfig->setRememberMeSeconds($configuration['sessionConfiguration']['rememberMeSeconds']);
                    }
                    if (isset($configuration['sessionConfiguration']['savePath'])) {
                        $target = $configuration['sessionConfiguration']['savePath'];
                        if ($target === true) {
                            $target = realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../data/session';
                        }
                        if (!file_exists($target)) {
                            mkdir($target);
                        }
                        $sessionConfig->setSavePath($target);

                    }
                    if (isset($configuration['sessionConfiguration']['options'])) {
                        $sessionConfig->setOptions($configuration['sessionConfiguration']['options']);
                    }
                    $sessionManager->setConfig($sessionConfig);
                }

                return $sessionManager;
            },
            'Application\Form\ForgotPassword' => function (ServiceManager $sm) {
                $form = new ForgotPassword();
                $form->setTranslator($sm->get('Translator'));
                return $form;
            },
            'Application\Form\NewPassword' => function (ServiceManager $sm) {
                $form = new NewPassword();
                $form->setTranslator($sm->get('Translator'));
                return $form;
            },
            'Application\Form\Register' => function (ServiceManager $sm) {
                $configOptions = $sm->get('Config')['registerOptions'];
                $form = new RegisterForm($configOptions);
                $form->setTranslator($sm->get('Translator'));
                return $form;
            },
            'Application\Service\Authentication' => function (ServiceManager $sm) {
                $service = new AuthenticationService();
                return $service;
            },
            'Application\Service\Mail' => function (ServiceManager $sm) {
                $service = new MailService();
                return $service;
            },
            'Application\Service\User' => function (ServiceManager $sm) {
                $service = new UserService();
                return $service;
            },
        ),
        'initializers' => array (
            function ($service, $sm) {
                if ($service instanceof TranslatorAwareInterface) {
                    $service->setTranslator($sm->get('MvcTranslator'));
                }
                if ($service instanceof ObjectManagerAwareInterface) {
                    $service->setObjectManager($sm->get('doctrine.entitymanager.orm_default'));
                }
            }
        ),
    ),
    'view_helpers' => array (
        'invokables' => array (
            'SocialLogin' => 'Application\View\Helper\SocialLogin'
        ),
        'factories' => array (
            'currentRoute' => function (HelperPluginManager $pm) {
                $helper = new CurrentRoute();
                $application = $pm->getServiceLocator()->get('Application');
                $helper->setRouteMatch($application->getMvcEvent()->getRouteMatch());

                return $helper;
            }
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'Application_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Application/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Application\Entity' =>  'Application_driver'
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(

        ),
        'factories' => [
            'zfcuser' => function(ControllerManager $cm) {
                /* @var ControllerManager $cm*/
                $serviceManager = $cm->getServiceLocator();
                /* @var RedirectCallback $redirectCallback */
                $redirectCallback = $serviceManager->get('zfcuser_redirect_callback');
                /* @var UserController $controller */
                $controller = new UserController($redirectCallback);

                return $controller;
            },
            'Application\Controller\Index'  => function(ControllerManager $cm) {
                /* @var ControllerManager $cm*/
                $serviceManager = $cm->getServiceLocator();
                $controller = new IndexController();
                $controller->setUserService($serviceManager->get('Application\Service\User'));
                return $controller;
            },
        ],
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array (
            'ViewJsonStrategy'
        )
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    'zfcuser' => include 'user/zfcuser.config.php',
    'bjyauthorize' => include 'user/bjyauthorize.config.php',
);
