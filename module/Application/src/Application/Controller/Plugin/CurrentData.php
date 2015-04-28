<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 21.04.15
 * Time: 18:09
 */

namespace Application\Controller\Plugin;


use Application\Service\UserService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class CurrentData extends AbstractPlugin{

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getCurrentUser(){
        return $this->userService->getCurrentUser();
    }
} 