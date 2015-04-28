<?php

return [
    [
        'name' => 'Application\Controller\Admin',
        'code' => 'admin',
        'key' => 'Controller.admin',
        'actions' => [
            [
                'name' => 'role',
                'key' => 'Controller.admin.action.role'
            ],
            [
                'name' => 'add-role',
                'key' => 'Controller.admin.action.add-role'
            ],
            [
                'name' => 'edit-role',
                'key' => 'Controller.admin.action.edit-role'
            ],
        ]
    ],
    [
        'name' => 'Application\Controller\Article',
        'code' => 'article',
        'key' => 'Controller.article',
        'actions' => [
            [
                'name' => 'unit',
                'key' => 'Controller.article.action.unit'
            ],
            [
                'name' => 'add-unit',
                'key' => 'Controller.article.action.add-unit'
            ],
            [
                'name' => 'edit-unit',
                'key' => 'Controller.article.action.edit-unit'
            ],
            [
                'name' => 'category',
                'key' => 'Controller.article.action.category'
            ],
            [
                'name' => 'add-category',
                'key' => 'Controller.article.action.add-category'
            ],
            [
                'name' => 'edit-category',
                'key' => 'Controller.article.action.edit-category'
            ],
            [
                'name' => 'brand',
                'key' => 'Controller.article.action.brand'
            ],
            [
                'name' => 'add-brand',
                'key' => 'Controller.article.action.add-brand'
            ],
            [
                'name' => 'edit-brand',
                'key' => 'Controller.article.action.edit-brand'
            ],
        ]
    ],
    [
        'name' => 'Application\Controller\Company',
        'code' => 'company',
        'key' => 'Controller.company',
        'actions' => [
            [
                'name' => 'index',
                'key' => 'Controller.company.action.index'
            ]
        ]
    ],
    [
        'name' => 'Application\Controller\Index',
        'code' => 'index',
        'key' => 'Controller.index',
        'actions' => [
            [
                'name' => 'dashboard',
                'key' => 'Controller.index.action.dashboard'
            ]
        ]
    ],
];