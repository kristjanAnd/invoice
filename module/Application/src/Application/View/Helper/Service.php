<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 23.04.15
 * Time: 18:26
 */

namespace Application\View\Helper;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

class Service extends AbstractHelper implements ServiceLocatorAwareInterface {

    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $pluginManager) { /* @var $pluginManager \Zend\ServiceManager\AbstractPluginManager */
        $this->serviceLocator = $pluginManager->getServiceLocator();
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function __invoke($name) {
        if ($this->serviceLocator == null) {
            $this->serviceLocator = $this->helperPluginManager->getServiceLocator();
        }

        return $this->serviceLocator->get($name);
    }
} 