<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 10.03.15
 * Time: 9:19
 */

namespace Application\Service;

use Application\Authentication\Adapter\HybridAuth as HybridAuthAdapter;
use Zend\ServiceManager\ServiceLocatorInterface;

class HybridAuthAdapterFactory extends \ScnSocialAuth\Service\HybridAuthAdapterFactory{

    public function createService(ServiceLocatorInterface $services)
    {
        $moduleOptions = $services->get('ScnSocialAuth-ModuleOptions');
        $zfcUserOptions = $services->get('zfcuser_module_options');

        $mapper = $services->get('ScnSocialAuth-UserProviderMapper');
        $zfcUserMapper = $services->get('zfcuser_user_mapper');

        $adapter = new HybridAuthAdapter();
        $adapter->setOptions($moduleOptions);
        $adapter->setZfcUserOptions($zfcUserOptions);
        $adapter->setMapper($mapper);
        $adapter->setZfcUserMapper($zfcUserMapper);

        return $adapter;
    }
} 