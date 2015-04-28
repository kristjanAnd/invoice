<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 28.04.15
 * Time: 17:21
 */

namespace Application\Controller;


use Application\Service\AdminService;
use Zend\Mvc\Controller\AbstractActionController;

class CronController extends AbstractActionController {
    /**
     * @var AdminService
     */
    protected $adminService;

    /**
     * @param AdminService $adminService
     */
    public function setAdminService(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function updateControllersAction(){
        $this->checkConsoleRequest();
        $this->adminService->updateControllers();
        echo 'Controllers updated';
    }


    protected function checkConsoleRequest() {
        $request = $this->getRequest();
        if (!$request instanceof \Zend\Console\Request) {
            throw new \RuntimeException('You can only use this action from a console!');
        }
    }

} 