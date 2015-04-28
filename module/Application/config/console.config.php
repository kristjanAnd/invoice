<?php

return array(
    'router' => array(
        'routes' => array(
            'update-controllers' => array(
                'type' => 'Simple',
                'options' => array(
                    'route' => 'application update-controllers',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Cron',
                        'action' => 'updateControllers'
                    )
                )
            )
        )
    )
);