<?php
use Application\Controller\AdminController;
use Application\Controller\ArticleController;
use Application\Controller\CompanyController;
use Application\Controller\CronController;
use Application\Controller\IndexController;
use Application\Controller\InvoiceController;
use Application\Controller\UserController;
use Zend\Mvc\Controller\ControllerManager;

return array(
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
        'Application\Controller\Admin'  => function(ControllerManager $cm) {
            /* @var ControllerManager $cm*/
            $serviceManager = $cm->getServiceLocator();
            $controller = new AdminController();
            $controller->setAdminService($serviceManager->get('Application\Service\Admin'));
            $controller->setUserService($serviceManager->get('Application\Service\User'));
            return $controller;
        },
        'Application\Controller\Article'  => function(ControllerManager $cm) {
            /* @var ControllerManager $cm*/
            $serviceManager = $cm->getServiceLocator();
            $controller = new ArticleController();
            $controller->setArticleService($serviceManager->get('Application\Service\Article'));
            $controller->setUnitService($serviceManager->get('Application\Service\Unit'));
            return $controller;
        },
        'Application\Controller\Client'  => function(ControllerManager $cm) {
            /* @var ControllerManager $cm*/
            $serviceManager = $cm->getServiceLocator();
            $controller = new \Application\Controller\ClientController();
            $controller->setClientService($serviceManager->get('Application\Service\Client'));
            return $controller;
        },
        'Application\Controller\Company'  => function(ControllerManager $cm) {
            /* @var ControllerManager $cm*/
            $serviceManager = $cm->getServiceLocator();
            $controller = new CompanyController();
            $controller->setCompanyService($serviceManager->get('Application\Service\Company'));
            return $controller;
        },
        'Application\Controller\Cron'  => function(ControllerManager $cm) {
            /* @var ControllerManager $cm*/
            $serviceManager = $cm->getServiceLocator();
            $controller = new CronController();
            $controller->setAdminService($serviceManager->get('Application\Service\Admin'));
            return $controller;
        },
        'Application\Controller\Index'  => function(ControllerManager $cm) {
            /* @var ControllerManager $cm*/
            $serviceManager = $cm->getServiceLocator();
            $controller = new IndexController();
            $controller->setUserService($serviceManager->get('Application\Service\User'));
            return $controller;
        },
        'Application\Controller\Invoice'  => function(ControllerManager $cm) {
            /* @var ControllerManager $cm*/
            $serviceManager = $cm->getServiceLocator();
            $controller = new InvoiceController();
            $controller->setInvoiceService($serviceManager->get('Application\Service\Invoice'));
            return $controller;
        },
    ],
);