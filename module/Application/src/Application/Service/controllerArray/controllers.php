<?php

return [
    [
        'name' => 'Application\Controller\Admin',
        'code' => 'admin',
        'key' => 'Controller.admin',
        'order_no' => 2,
        'actions' => [
            [
                'name' => 'role',
                'key' => 'Controller.admin.action.role',
                'is_navigation' => true,
                'order_no' => 1,
            ],
            [
                'name' => 'add-role',
                'key' => 'Controller.admin.action.add-role',
                'is_navigation' => false,
                'order_no' => 2,
            ],
            [
                'name' => 'edit-role',
                'key' => 'Controller.admin.action.edit-role',
                'is_navigation' => false,
                'order_no' => 3,
            ],
            [
                'name' => 'user',
                'key' => 'Controller.admin.action.user',
                'is_navigation' => true,
                'order_no' => 4,
            ],
            [
                'name' => 'add-user',
                'key' => 'Controller.admin.action.add-user',
                'is_navigation' => false,
                'order_no' => 5,
            ],
            [
                'name' => 'edit-user',
                'key' => 'Controller.admin.action.edit-user',
                'is_navigation' => false,
                'order_no' => 6,
            ],
        ]
    ],
    [
        'name' => 'Application\Controller\Article',
        'code' => 'article',
        'key' => 'Controller.article',
        'order_no' => 1,
        'actions' => [
            [
                'name' => 'unit',
                'key' => 'Controller.article.action.unit',
                'is_navigation' => true,
                'order_no' => 7,
            ],
            [
                'name' => 'add-unit',
                'key' => 'Controller.article.action.add-unit',
                'is_navigation' => false,
                'order_no' => 8,
            ],
            [
                'name' => 'edit-unit',
                'key' => 'Controller.article.action.edit-unit',
                'is_navigation' => false,
                'order_no' => 9,
            ],
            [
                'name' => 'category',
                'key' => 'Controller.article.action.category',
                'is_navigation' => true,
                'order_no' => 10,
            ],
            [
                'name' => 'add-category',
                'key' => 'Controller.article.action.add-category',
                'is_navigation' => false,
                'order_no' => 11,
            ],
            [
                'name' => 'edit-category',
                'key' => 'Controller.article.action.edit-category',
                'is_navigation' => false,
                'order_no' => 12,
            ],
            [
                'name' => 'brand',
                'key' => 'Controller.article.action.brand',
                'is_navigation' => true,
                'order_no' => 13,
            ],
            [
                'name' => 'add-brand',
                'key' => 'Controller.article.action.add-brand',
                'is_navigation' => false,
                'order_no' => 14,
            ],
            [
                'name' => 'edit-brand',
                'key' => 'Controller.article.action.edit-brand',
                'is_navigation' => false,
                'order_no' => 15,
            ],
            [
                'name' => 'item',
                'key' => 'Controller.article.action.item',
                'is_navigation' => true,
                'order_no' => 1,
            ],
            [
                'name' => 'add-item',
                'key' => 'Controller.article.action.add-item',
                'is_navigation' => false,
                'order_no' => 2,
            ],
            [
                'name' => 'edit-item',
                'key' => 'Controller.article.action.edit-item',
                'is_navigation' => false,
                'order_no' => 3,
            ],
            [
                'name' => 'service',
                'key' => 'Controller.article.action.service',
                'is_navigation' => true,
                'order_no' => 4,
            ],
            [
                'name' => 'add-service',
                'key' => 'Controller.article.action.add-service',
                'is_navigation' => false,
                'order_no' => 5,
            ],
            [
                'name' => 'edit-service',
                'key' => 'Controller.article.action.edit-service',
                'is_navigation' => false,
                'order_no' => 6,
            ],
        ]
    ],
    [
        'name' => 'Application\Controller\Company',
        'code' => 'company',
        'key' => 'Controller.company',
        'order_no' => 3,
        'actions' => [
            [
                'name' => 'index',
                'key' => 'Controller.company.action.index',
                'is_navigation' => true,
                'order_no' => 1,
            ]
        ]
    ],
    [
        'name' => 'Application\Controller\User',
        'code' => 'user',
        'key' => 'Controller.user',
        'order_no' => 4,
        'actions' => [
            [
                'name' => 'profile',
                'key' => 'Controller.user.action.profile',
                'is_navigation' => true,
                'order_no' => 1,
            ]
        ]
    ],
    [
        'name' => 'Application\Controller\Client',
        'code' => 'client',
        'key' => 'Controller.client',
        'order_no' => 5,
        'actions' => [
            [
                'name' => 'client',
                'key' => 'Controller.client.action.client',
                'is_navigation' => true,
                'order_no' => 1,
            ],
            [
                'name' => 'add-client',
                'key' => 'Controller.client.action.add-client',
                'is_navigation' => false,
                'order_no' => 2,
            ],
            [
                'name' => 'edit-client',
                'key' => 'Controller.client.action.edit-client',
                'is_navigation' => false,
                'order_no' => 3,
            ]
        ]
    ],
    [
        'name' => 'Application\Controller\Invoice',
        'code' => 'invoice',
        'key' => 'Controller.invoice',
        'order_no' => 6,
        'actions' => [
            [
                'name' => 'invoice',
                'key' => 'Controller.invoice.action.invoice',
                'is_navigation' => true,
                'order_no' => 1,
            ],
            [
                'name' => 'add-invoice',
                'key' => 'Controller.invoice.action.add-invoice',
                'is_navigation' => false,
                'order_no' => 2,
            ],
            [
                'name' => 'edit-invoice',
                'key' => 'Controller.invoice.action.edit-invoice',
                'is_navigation' => false,
                'order_no' => 3,
            ],
            [
                'name' => 'invoice-setting',
                'key' => 'Controller.invoice.action.invoice-setting',
                'is_navigation' => true,
                'order_no' => 4,
            ],
            [
                'name' => 'edit-invoice-setting',
                'key' => 'Controller.invoice.action.edit-invoice-setting',
                'is_navigation' => false,
                'order_no' => 5,
            ]
        ]
    ],
];