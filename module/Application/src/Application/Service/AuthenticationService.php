<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 12.02.15
 * Time: 12:35
 */

namespace Application\Service;


use Zend\Soap\Client;

class AuthenticationService extends \BitWeb\IdServices\Authentication\MobileID\AuthenticationService{

    /**
     * @var Client
     */
    protected $soap;

    public function getSoapClient(){
        return $this->soap;
    }
} 