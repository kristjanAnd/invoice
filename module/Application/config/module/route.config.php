<?php

return array(
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
        'dashboard' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/dashboard',
                'defaults' => array(
                    'controller' => 'Application\Controller\Index',
                    'action'     => 'dashboard',
                    'language' => 'us'
                ),
            ),
        ),
        'role' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/roles[/:page]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Admin',
                    'action'     => 'role',
                    'language' => 'us'
                ),
            ),
        ),
        'add-role' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/add-role',
                'defaults' => array(
                    'controller' => 'Application\Controller\Admin',
                    'action'     => 'add-role',
                    'language' => 'us'
                ),
            ),
        ),
        'edit-role' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/edit-role[/:id]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Admin',
                    'action'     => 'edit-role',
                    'language' => 'us'
                ),
            ),
        ),
        'unit' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/unit[/:page]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Article',
                    'action'     => 'unit',
                    'language' => 'us'
                ),
            ),
        ),
        'add-unit' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/add-unit[/:page]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Article',
                    'action'     => 'add-unit',
                    'language' => 'us'
                ),
            ),
        ),
        'category' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/category[/:page]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Article',
                    'action'     => 'category',
                    'language' => 'us'
                ),
            ),
        ),
        'add-category' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/add-category[/:page]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Article',
                    'action'     => 'add-category',
                    'language' => 'us'
                ),
            ),
        ),
        'brand' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/brand[/:page]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Article',
                    'action'     => 'brand',
                    'language' => 'us'
                ),
            ),
        ),
        'add-brand' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/add-brand[/:page]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Article',
                    'action'     => 'add-brand',
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

        'company' => array (
            'type' => 'Segment',
            'options' => array (
                'route' => '[/:language]/company[/:id]',
                'defaults' => array (
                    '__NAMESPACE__' => 'Application\Controller',
                    'controller' => 'company',
                    'action' => 'index',
                    'language' => 'us'
                )
            )
        ),

        // The following is a route to simplify getting started creating
        // new controllers and actions without needing to create a new
        // module. Simply drop new controllers in, and you can access them
        // using the path /application/:controller/:action
        'application' => array (
            'type' => 'Segment',
            'options' => array (
                'route' => '[/:language]/application[/:controller[/:action][/:page]]',
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
);