<?php

return array(
    // telling ZfcUser to use our own class
    'user_entity_class'       => 'Application\Entity\User',
    // telling ZfcUserDoctrineORM to skip the entities it defines
    'enable_default_entities' => false,
    'enable_registration' => true,
    'login_redirect_route' => 'home',
    'logout_redirect_route' => 'home',
    'password_cost' => 14,
//    'auth_adapters' => array( 100 => 'ScnSocialAuth\Authentication\Adapter\HybridAuth' ),
);