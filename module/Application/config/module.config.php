<?php
use Application\Common\GeoZone;
use Application\Controller\Plugin\CurrentData;
use Application\View\Helper\CurrentRoute;
use Application\View\Helper\CurrentUser;
use Application\View\Helper\ErrorMessages;
use Application\View\Helper\FormErrors;
use Application\View\Helper\SuccessMessages;
use Zend\Mvc\Controller\PluginManager;
use Zend\View\HelperPluginManager;

function getDefaultLanguageByLocation(){
    $languageCode = GeoZone::LANGUAGE_CODE_US;
    $locationArray = GeoZone::getGeoLocationInfoArray();
    if(isset($locationArray['geoplugin_countryCode'])){
        if($locationArray['geoplugin_countryCode'] == GeoZone::COUNTRY_CODE_ESTONIA){
            $languageCode = GeoZone::LANGUAGE_CODE_ET;
        }
        if($locationArray['geoplugin_countryCode'] == GeoZone::COUNTRY_CODE_RUSSIA){
            $languageCode = GeoZone::LANGUAGE_CODE_RU;
        }
    }

    return $languageCode;
}

return array(
    'console' => include 'console.config.php',
    'router'  => include 'module/route.config.php',
    'navigation' => array(
        'default' => include 'navigation.config.php'
    ),
    'service_manager' => include 'module/service.config.php',
    'view_helpers' => array (
        'invokables' => array (
            'SocialLogin' => 'Application\View\Helper\SocialLogin',
            'service' => 'Application\View\Helper\Service',
        ),
        'factories' => array (
            'currentRoute' => function (HelperPluginManager $pm) {
                $helper = new CurrentRoute();
                $application = $pm->getServiceLocator()->get('Application');
                $helper->setRouteMatch($application->getMvcEvent()->getRouteMatch());

                return $helper;
            },
            'currentUser' => function (HelperPluginManager $pm) {
                $helper = new CurrentUser();
                $helper->setUserService($pm->getServiceLocator()->get('Application\Service\User'));

                return $helper;
            },
            'successMessages' => function () {
                $helper = new SuccessMessages();
                return $helper;
            },
            'errorMessages' => function () {
                $helper = new ErrorMessages();
                return $helper;
            },
            'formErrors' => function (HelperPluginManager $pm) {
                $helper = new FormErrors();
                $helper->setTranslator($pm->getServiceLocator()->get('Translator'));
                return $helper;
            },
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
    'controllers' => include 'module/controller.config.php',
    'controller_plugins' => array(
        'invokables' => array(

        ),
        'factories' => [
            'currentData' => function(PluginManager $pm) {
                $plugin = new CurrentData();
                $plugin->setUserService($pm->getServiceLocator()->get('Application\Service\User'));
                return $plugin;
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
    'zfcuser' => include 'user/zfcuser.config.php',
    'bjyauthorize' => include 'user/bjyauthorize.config.php',
);
