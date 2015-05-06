<?php

$controllerGuard = array(
    ['controller' => 'ScnSocialAuth-HybridAuth', 'roles' => []],
    ['controller' => 'ScnSocialAuth-User', 'roles' => []],
    ['controller' => 'zfcuser', 'roles' => []],
    ['controller' => 'Application\Controller\Admin', 'action' => 'if-role-exists', 'roles' => ['guest']],
    ['controller' => 'Application\Controller\Article', 'action' => 'get-article-select', 'roles' => ['guest']],
    ['controller' => 'Application\Controller\Invoice', 'action' => 'add-article', 'roles' => ['guest']],
    ['controller' => 'Application\Controller\Cron', 'roles' => ['admin']],
    ['controller' => 'Application\Controller\Index', 'action' => 'dashboard', 'roles' => ['guest']],
    ['controller' => 'Application\Controller\Index', 'action' => 'index', 'roles' => ['guest']],
    ['controller' => 'Application\Controller\Index', 'action' => 'email-exists', 'roles' => ['guest']],
    ['controller' => 'Application\Controller\Index', 'action' => 'isPasswordValid', 'roles' => ['guest']],
    ['controller' => 'Application\Controller\Index', 'action' => 'forgotPassword', 'roles' => ['guest']],
);

$adminControllerGuard = include 'controllers/admin.config.php';
$articleControllerGuard = include 'controllers/article.config.php';
$companyControllerGuard = include 'controllers/company.config.php';

//$controllerGuard = array_merge(
//    $controllerGuard,
//    $adminControllerGuard,
//    $articleControllerGuard,
//    $companyControllerGuard
//);

return array(
    // Using the authentication identity provider, which basically reads the roles from the auth service's identity
    'identity_provider' => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',

    'role_providers'        => array(
//        'BjyAuthorize\Provider\Role\Config' => array(
//            'guest' => array( 'children' => array(
//                    'user'  => array('children' => array(
//                        'admin' => array(),
//                    )),
//                )
//            ),
//
//        ),
        // using an object repository (entity repository) to load all roles into our ACL
        'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
            'object_manager'    => 'doctrine.entity_manager.orm_default',
            'role_entity_class' => 'Application\Entity\Role',
        ),
    ),
    'guards' => array(
        'BjyAuthorize\Guard\Controller' => $controllerGuard
    ),
    'unauthorized_strategy' => 'BjyAuthorize\View\RedirectionStrategy',
);