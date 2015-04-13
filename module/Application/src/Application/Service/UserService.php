<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 25.02.15
 * Time: 18:06
 */

namespace Application\Service;


use Application\Common\SocialStorage;
use Application\Entity\Role;
use Application\Entity\RoleLinker;
use Application\Entity\User;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mail\Message;
use Zend\Stdlib\Parameters;
use BitWeb\IdServices\Authentication\IdCard\Authentication;
use Zend\Soap\Client;

class UserService extends AbstractService {

    public function setRoleAfterRegister(User $user, Parameters $data) {
        if (count($user->getRoles()) == 0) {
            $role = $this->getUserRole();
            $user = $this->saveUser($user, new Parameters());
            $roleLinker = new RoleLinker();
            $roleLinker->setUser($user);
            $roleLinker->setRole($role);
            $this->saveRoleLinker($roleLinker);
            $this->saveUser($user, new Parameters());
        }
        return $user;
    }

    /**
     * @param $email
     * @return null|User
     */
    public function getUserByEmail($email){
        return $this->entityManager->getRepository(User::getClass())->findOneBy(array('email' => $email));
    }

    public function getUserById($id){
        return $this->entityManager->getRepository(User::getClass())->findOneBy(array('id' => $id));
    }

    public function getRoleByRoleId($roleId){
        return $this->entityManager->getRepository(Role::getClass())->findOneBy(array('roleId' => $roleId));
    }

    private function getUserRole(){
        $role = $this->getRoleByRoleId(Role::ROLE_USER);
        if(!$role){
            $role = new Role();
            $role->setRoleId(Role::ROLE_USER);
            $role = $this->saveRole($role);
        }

        return $role;
    }

    public function saveRoleLinker(RoleLinker $roleLinker) {
        $this->entityManager->persist($roleLinker);
        $this->entityManager->flush($roleLinker);

        return $roleLinker;
    }

    public function saveRole(Role $role) {
        $this->entityManager->persist($role);
        $this->entityManager->flush($role);

        return $role;
    }

    public function saveUser(User $user = null, Parameters $data) {
        if(!$user){
            $user = new User();
        }
        if(isset($data->firstName)){
            $user->setFirstName($data->firstName);
        }
        if(isset($data->lastName)){
            $user->setLastName($data->lastName);
        }
        if(isset($data->email)){
            $user->setEmail($data->email);
        }
        if(isset($data->phone)){
            $user->setPhone($data->phone);
        }
        if(isset($data->personalCode)){
            $user->setPersonalCode($data->personalCode);
        }
        if(isset($data->active)){
            $user->setStatus(($data->active == 1) ? User::STATUS_ACTIVE : User::STATUS_INACTIVE);
        }

        if(isset($data->password) && strlen($data->password) > 5 && $data->password == $data->passwordRepeat){
            $cost = isset($this->locator->get('Config')['zfcuser']['password_cost']) ? $this->locator->get('Config')['zfcuser']['password_cost'] : 14;
            $bcrypt = new Bcrypt();
            $bcrypt->setCost($cost);
            $pass = $bcrypt->create($data->password);
            $user->setPassword($pass);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);

        return $user;
    }

    public function generatePasswordLink(User $user, $language)
    {
        $hash = $this->generatePasswordLinkHash();
        $hashEntity = $user->getHash();

        if ($hashEntity == null) {
            $hashEntity = new User\Hash($user, $hash);
        } else {
            $hashEntity->setHash($hash)->setDateCreated(new \DateTime());
        }

        $this->entityManager->persist($hashEntity);
        $this->entityManager->flush();
        $this->entityManager->refresh($user);

        $this->sendPasswordLinkEmail($user, $language);

        return $hashEntity;
    }

    protected function sendPasswordLinkEmail(User $user, $language)
    {
        /* @var $mailService \Application\Service\MailService */
        $mailService = $this->locator->get('Application\Service\Mail');
        $config = $this->locator->get('Config');

        /* @var $urlHelper \Zend\View\Helper\Url */
        $urlHelper = $this->locator->get('Zend\View\Renderer\PhpRenderer')
            ->getHelperPluginManager()
            ->get('url');

        $locale = array_key_exists($language, $config['languages']['available']) ? $config['languages']['available'][$language] : $config['languages']['defaultLocale'];

        $translator = $this->locator->get('Translator');
        $translator->setLocale($locale);


        $message = new Message();

        $toEmail = isset($config['mail']['sendAllMailsTo']) ? $config['mail']['sendAllMailsTo'] : $user->getEmail();

        $message->setTo($toEmail, $user->getFullName());
        $message->setFrom($this->getConfig()->siteInfo->emailInfo->email, $this->getConfig()->siteInfo->emailInfo->name);
        $message->setSubject($translator->translate('mail.forgotPassword.subject'));
        $message->setBody(
            $translator->translate('mail.forgotPassword.bodyMessageBeforeLink') . ' ' . $urlHelper->__invoke('zfcuser/new-password', array(
                'hash' => $user->getHash()->getHash(),
            ), array(
                'force_canonical' => true
            ))
        );

        $mailService->send($mailService->getTransport(), $message);
    }

    private function generatePasswordLinkHash()
    {
        $config = $this->locator->get('Config');
        return sha1($config['user']['password']['globalSalt'] . $this->getRandomString(6));
    }

    public function getRandomString($length) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' .
            'abcdefghijklmnopqrstuvwxyz0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    public function getEnabledProviders(){
        $result = array();
        $config = $this->locator->get('Config');
        foreach(SocialStorage::$providers as $provider){
            if(isset($config['scn-social-auth'][$provider . '_enabled']) && $config['scn-social-auth'][$provider . '_enabled'] == true){
                $result[] = $provider;
            }
        }
        return $result;
    }

    public function changePassword(User $user, $newPassword)
    {
        $cost = isset($this->locator->get('Config')['zfcuser']['password_cost']) ? $this->locator->get('Config')['zfcuser']['password_cost'] : 14;
        $bcrypt = new Bcrypt();
        $bcrypt->setCost($cost);
        $pass = $bcrypt->create($newPassword);
        $user->setPassword($pass);

        $this->entityManager->persist($user);
        $this->entityManager->remove($user->getHash());
        $this->entityManager->flush();

        return $user;
    }

    public function getPasswordLinkByHash($hash)
    {
        return $this->entityManager->getRepository(User\Hash::getClass())->findOneBy(array(
            'hash' => $hash,
        ));
    }

    public function checkPasswordLinkLife(User\Hash $hash)
    {
        $config = $this->locator->get('Config');
        /* @var $diff \Dateinterval */
        $diff = $hash->getDateCreated()->diff(new \DateTime(), true);
        $passwordLinkLife = $config['user']['password']['forgotPasswordLinkLife'];
        if ($diff->days > floor($passwordLinkLife / 24)) {
            return false;
        } elseif ($diff->h > $passwordLinkLife) {
            return false;
        } else {
            return true;
        }
    }

    public function register(Parameters $data) {

        $cost = isset($this->locator->get('Config')['zfcuser']['password_cost']) ? $this->locator->get('Config')['zfcuser']['password_cost'] : 14;
        $user  = new User();
        if(isset($data->email)){
            $user->setEmail($data->email);
        }

        if(isset($data->firstName)){
            $user->setFirstName($data->firstName);
        }

        if(isset($data->email)){
            $user->setLastName($data->lastName);
        }

        if(isset($data->phone)){
            $user->setPhone($data->phone);
        }

        if(isset($data->personalCode)){
            $user->setPersonalCode($data->personalCode);
        }

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($cost);
        $user->setPassword($bcrypt->create($data->password));

        return $user;
    }

    /**
     * @return User
     */
    public function getCurrentUser(){
        $auth = $this->getServiceLocator()->get('zfcuser_auth_service');
        return ($auth->hasIdentity()) ? $auth->getIdentity() : null;
    }

    public function isPasswordValid($email, $password){
        $user = $this->getUserByEmail($email);
        if($user){
            $cost = isset($this->locator->get('Config')['zfcuser']['password_cost']) ? $this->locator->get('Config')['zfcuser']['password_cost'] : 14;
            $bcrypt = new Bcrypt();
            $bcrypt->setCost($cost);
            return $bcrypt->verify($password, $user->getPassword());
        }

        return false;
    }


    public function getAdminRoleEntity(){
        $adminRole =  $this->getRoleByRoleId(Role::ROLE_ADMIN);
        return $adminRole;
    }

    public function getUserByPersonalCodeAndEmail($personalCode, $email) {
        return $this->entityManager->getRepository(User::getClass())->findOneBy(array('personalCode' => $personalCode, 'email' => $email));
    }

    public function isAdminUser(User $user = null){
        $adminRole = $this->getAdminRoleEntity();
        return ($user && $user->getRoles()->contains($adminRole)) ? true : false;
    }

    protected function getUserByEmailOrThrow($email)
    {
        $user = $this->getUserByEmail($email);
        if ($user === null) {
            $translator = $this->getServiceLocator()->get('Translator');
            throw new \Exception(sprintf($translator->translate('IdAuth.errorMessage.USER_WITH_EMAIL_NOT_FOUND'), $email));
        }

        return $user;
    }

    public function loginWithIdCard(User $user)
    {
        $translator = $this->getServiceLocator()->get('Translator');
        if (!Authentication::isUserLoggedIn()) {
            throw new \Exception($translator->translate('IdAuth.errorMessage.NO_ID_CARD_USER_FOUND'));
        }

        if (Authentication::getLoggedInUser()->getSocialSecurityNumber() !== $user->getPersonalCode()) {
            throw new \Exception($translator->translate('IdAuth.errorMessage.NO_ID_CARD_USER_NOT_MATCH'));
        }

        $auth = $this->getServiceLocator()->get('zfcuser_auth_service');

        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
        }

        $auth->getStorage()->write($user->getId());

        $success = $auth->hasIdentity();

        return $success;
    }

    public function mobileIdAuthenticateStart($email, $phone)
    {
        $user = $this->getUserByEmailOrThrow($email);

        try {
            $config = $this->locator->get('Config')['id-services'];
            return $this->getMobileAuthenticationService()->mobileAuthenticate(
                $user->getPersonalCode(), $phone, 'EST', $config['serviceName'], $config['message']
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function mobileIdAuthenticateStatus($sessionCode, $email)
    {
        $user = $this->getUserByEmailOrThrow($email);

        try {
            $response =  $this->getMobileAuthenticationService()->getMobileAuthenticateStatus($sessionCode, false);

            return $response;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function getMobileAuthenticationService()
    {
        $config = $this->locator->get('Config')['id-services'];

        $service = new AuthenticationService();
        $service->setWsdl($config['url'])->initSoap();
        $client = $service->getSoapClient();
        $this->setSoapStreamContext($client);

        if ($config['loggerEnabled']) {
            $service->enableLogging('data/logs/id-services.log');
        }

        return $service;
    }

    protected function setSoapStreamContext(Client $client){
        $config = $this->locator->get('Config');
        if(isset($config['id-services']['bindTo'])){
            $opts = array('socket' => array('bindto' => $config['id-services']['bindTo']));
            $context = stream_context_create($opts);
            $client->setStreamContext($context);
        }

        return $client;
    }

    public function loginWithMobileId($email)
    {
        $user = $this->getUserByEmailOrThrow($email);
        $auth = $this->getServiceLocator()->get('zfcuser_auth_service');

        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
        }

        $auth->getStorage()->write($user->getId());
        $success = $auth->hasIdentity();

        return $success;
    }

}