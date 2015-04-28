<?php
use Application\Form\ArticleForm;
use Application\Form\BrandForm;
use Application\Form\CategoryForm;
use Application\Form\ForgotPassword;
use Application\Form\NewPassword;
use Application\Form\RegisterForm;
use Application\Form\SubjectForm;
use Application\Form\UnitForm;
use Application\Service\AdminService;
use Application\Service\ArticleService;
use Application\Service\AuthenticationService;
use Application\Service\CompanyService;
use Application\Service\LanguageService;
use Application\Service\MailService;
use Application\Service\UnitService;
use Application\Service\UserService;
use Zend\ServiceManager\ServiceManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Zend\I18n\Translator\TranslatorAwareInterface;

return array(
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
        'Application\Form\Article' => function (ServiceManager $sm) {
            $form = new ArticleForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setUnitService($sm->get('Application\Service\Unit'));
            return $form;
        },
        'Application\Form\Brand' => function (ServiceManager $sm) {
            $form = new BrandForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setArticleService($sm->get('Application\Service\Article'));
            return $form;
        },
        'Application\Form\Category' => function (ServiceManager $sm) {
            $form = new CategoryForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setArticleService($sm->get('Application\Service\Article'));
            return $form;
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
            $form->setLanguageService($sm->get('Application\Service\Language'));
            return $form;
        },
        'Application\Form\Subject' => function (ServiceManager $sm) {
            $form = new SubjectForm();
            $form->setTranslator($sm->get('Translator'));
            return $form;
        },
        'Application\Form\Unit' => function (ServiceManager $sm) {
            $form = new UnitForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setUnitService($sm->get('Application\Service\Unit'));
            return $form;
        },
        'Application\Service\Admin' => function (ServiceManager $sm) {
            $service = new AdminService();
            $service->setUserService($sm->get('Application\Service\User'));
            return $service;
        },
        'Application\Service\Article' => function (ServiceManager $sm) {
            $service = new ArticleService();
            return $service;
        },
        'Application\Service\Authentication' => function (ServiceManager $sm) {
            $service = new AuthenticationService();
            return $service;
        },
        'Application\Service\Company' => function (ServiceManager $sm) {
            $service = new CompanyService();
            return $service;
        },
        'Application\Service\Language' => function (ServiceManager $sm) {
            $service = new LanguageService();
            return $service;
        },
        'Application\Service\Mail' => function (ServiceManager $sm) {
            $service = new MailService();
            return $service;
        },
        'Application\Service\Unit' => function (ServiceManager $sm) {
            $service = new UnitService();
            return $service;
        },
        'Application\Service\User' => function (ServiceManager $sm) {
            $service = new UserService();
            $service->setCompanyService($sm->get('Application\Service\Company'));
            return $service;
        },
        'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
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
);