<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 29.04.15
 * Time: 12:23
 */

namespace Application\Service;
use BjyAuthorize\Guard\Controller;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ControllerGuardServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Guard\Controller
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $adminService = $serviceLocator->get('Application\Service\Admin');
        $config = $serviceLocator->get('Config');
        $userAuthorizedActions = $adminService->getUserAuthorizedActions();
        $bjyAuthorizeActions = $config['bjyauthorize']['guards']['BjyAuthorize\Guard\Controller'];
        $mergedActions = array_merge($bjyAuthorizeActions, $userAuthorizedActions);
        return new Controller($mergedActions, $serviceLocator);
    }
}