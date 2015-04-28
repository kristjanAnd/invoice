<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 21.04.15
 * Time: 17:59
 */

namespace Application\View\Helper;


use Application\Service\UserService;
use Zend\Form\View\Helper\AbstractHelper;

class CurrentUser extends AbstractHelper{

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

    public function __invoke()
    {
       return $this->userService->getCurrentUser();
    }


} 