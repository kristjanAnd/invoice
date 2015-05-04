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
        'item' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/item[/:page]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Article',
                    'action'     => 'item',
                    'language' => 'us'
                ),
            ),
        ),
        'add-item' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/add-item',
                'defaults' => array(
                    'controller' => 'Application\Controller\Article',
                    'action'     => 'add-item',
                    'language' => 'us'
                ),
            ),
        ),
        'edit-item' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/edit-item[/:id]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Article',
                    'action'     => 'edit-item',
                    'language' => 'us'
                ),
            ),
        ),
        'service' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/service[/:page]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Article',
                    'action'     => 'service',
                    'language' => 'us'
                ),
            ),
        ),
        'add-service' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/add-service',
                'defaults' => array(
                    'controller' => 'Application\Controller\Article',
                    'action'     => 'add-service',
                    'language' => 'us'
                ),
            ),
        ),
        'edit-service' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/edit-service[/:id]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Article',
                    'action'     => 'edit-service',
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
        'client' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/client[/:page]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Client',
                    'action'     => 'client',
                    'language' => 'us'
                ),
            ),
        ),
        'add-client' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/add-client',
                'defaults' => array(
                    'controller' => 'Application\Controller\Client',
                    'action'     => 'add-client',
                    'language' => 'us'
                ),
            ),
        ),
        'edit-client' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/edit-client[/:id]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Client',
                    'action'     => 'edit-client',
                    'language' => 'us'
                ),
            ),
        ),
        'invoice' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/invoice[/:page]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Invoice',
                    'action'     => 'invoice',
                    'language' => 'us'
                ),
            ),
        ),
        'add-invoice' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/add-invoice',
                'defaults' => array(
                    'controller' => 'Application\Controller\Invoice',
                    'action'     => 'add-invoice',
                    'language' => 'us'
                ),
            ),
        ),
        'edit-invoice' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/edit-invoice[/:id]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Invoice',
                    'action'     => 'edit-invoice',
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
        'company-user' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/company-user[/:page]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Admin',
                    'action'     => 'user',
                    'language' => 'us'
                ),
            ),
        ),
        'add-user' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/add-user',
                'defaults' => array(
                    'controller' => 'Application\Controller\Admin',
                    'action'     => 'add-user',
                    'language' => 'us'
                ),
            ),
        ),
        'edit-user' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:language]/edit-user[/:id]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Admin',
                    'action'     => 'edit-user',
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
        'profile' => array (
            'type' => 'Segment',
            'options' => array (
                'route' => '[/:language]/profile',
                'defaults' => array (
                    'controller' => 'zfcuser',
                    'action'     => 'profile',
                    'language' => 'us'
                )
            )
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