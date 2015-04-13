<?php
return array(
'doctrine' => array(
    'connection' => array(
        'orm_default' => array(
            'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
            'params' => array(
                'host' => 'localhost',
                'port' => ini_get('mysql.default_port')
            ),
        ),
    )
));