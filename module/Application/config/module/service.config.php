<?php
use Application\Form\AddArticleForm;
use Application\Form\ArticleForm;
use Application\Form\BrandForm;
use Application\Form\CategoryForm;
use Application\Form\Document\InvoiceForm;
use Application\Form\DocumentForm;
use Application\Form\DocumentRow\InvoiceRowForm;
use Application\Form\DocumentRowForm;
use Application\Form\DocumentSetting\InvoiceSettingForm;
use Application\Form\DocumentSettingForm;
use Application\Form\FilterForm;
use Application\Form\ForgotPassword;
use Application\Form\NewPassword;
use Application\Form\RegisterForm;
use Application\Form\RoleForm;
use Application\Form\SubjectForm;
use Application\Form\UnitForm;
use Application\Form\VatForm;
use Application\Service\AdminService;
use Application\Service\ArticleService;
use Application\Service\AuthenticationService;
use Application\Service\ClientService;
use Application\Service\CompanyService;
use Application\Service\DocumentService;
use Application\Service\InvoiceService;
use Application\Service\LanguageService;
use Application\Service\MailService;
use Application\Service\UnitService;
use Application\Service\UserService;
use Application\Service\VatService;
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
        'BjyAuthorize\Guard\Controller'         => 'Application\Service\ControllerGuardServiceFactory',
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
            $form->setArticleService($sm->get('Application\Service\Article'));
            return $form;
        },
        'Application\Form\AddArticle' => function (ServiceManager $sm) {
            $form = new AddArticleForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setArticleService($sm->get('Application\Service\Article'));
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
        'Application\Form\Document' => function (ServiceManager $sm) {
            $form = new DocumentForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setDocumentService($sm->get('Application\Service\Document'));
            $form->setLanguageService($sm->get('Application\Service\Language'));
            return $form;
        },
        'Application\Form\Document\Invoice' => function (ServiceManager $sm) {
            $form = new InvoiceForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setDocumentService($sm->get('Application\Service\Document'));
            $form->setLanguageService($sm->get('Application\Service\Language'));
            $form->setClientService($sm->get('Application\Service\Client'));
            $form->setVatService($sm->get('Application\Service\Vat'));
            return $form;
        },
        'Application\Form\DocumentRow' => function (ServiceManager $sm) {
            $form = new DocumentRowForm();
            $form->setUnitService($sm->get('Application\Service\Unit'));
            $form->setVatService($sm->get('Application\Service\Vat'));
            return $form;
        },
        'Application\Form\DocumentRow\InvoiceRow' => function (ServiceManager $sm) {
            $form = new InvoiceRowForm();
            $form->setUnitService($sm->get('Application\Service\Unit'));
            $form->setVatService($sm->get('Application\Service\Vat'));
            return $form;
        },
        'Application\Form\DocumentSetting' => function (ServiceManager $sm) {
            $form = new DocumentSettingForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setDocumentService($sm->get('Application\Service\Document'));
            $form->setLanguageService($sm->get('Application\Service\Language'));
            return $form;
        },
        'Application\Form\DocumentSetting\InvoiceSetting' => function (ServiceManager $sm) {
            $form = new InvoiceSettingForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setDocumentService($sm->get('Application\Service\Document'));
            $form->setLanguageService($sm->get('Application\Service\Language'));
            $form->setVatService($sm->get('Application\Service\Vat'));
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
            $form->setAdminService($sm->get('Application\Service\Admin'));
            return $form;
        },
        'Application\Form\Role' => function (ServiceManager $sm) {
            $form = new RoleForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setAdminService($sm->get('Application\Service\Admin'));
            return $form;
        },
        'Application\Form\Filter' => function (ServiceManager $sm) {
            $form = new FilterForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setUnitService($sm->get('Application\Service\Unit'));
            $form->setArticleService($sm->get('Application\Service\Article'));
            return $form;
        },
        'Application\Form\Subject' => function (ServiceManager $sm) {
            $form = new SubjectForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setClientService($sm->get('Application\Service\Client'));
            return $form;
        },
        'Application\Form\Unit' => function (ServiceManager $sm) {
            $form = new UnitForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setUnitService($sm->get('Application\Service\Unit'));
            return $form;
        },
        'Application\Form\Vat' => function (ServiceManager $sm) {
            $form = new VatForm();
            $form->setTranslator($sm->get('Translator'));
            $form->setVatService($sm->get('Application\Service\Vat'));
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
        'Application\Service\Client' => function (ServiceManager $sm) {
            $service = new ClientService();
            return $service;
        },
        'Application\Service\Company' => function (ServiceManager $sm) {
            $service = new CompanyService();
            return $service;
        },
        'Application\Service\Document' => function (ServiceManager $sm) {
            $service = new DocumentService();
            return $service;
        },
        'Application\Service\Invoice' => function (ServiceManager $sm) {
            $service = new InvoiceService();
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
        'Application\Service\Vat' => function (ServiceManager $sm) {
            $service = new VatService();
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