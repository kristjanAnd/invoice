<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 10.03.15
 * Time: 9:09
 */

namespace Application\Authentication\Adapter;

use ScnSocialAuth\Authentication\Adapter\Exception\RuntimeException;
use Zend\Session\Container;
use ZfcUser\Authentication\Adapter\AdapterChainEvent as AuthEvent;
use Zend\Authentication\Result;

class HybridAuth extends \ScnSocialAuth\Authentication\Adapter\HybridAuth {

    public function authenticate(AuthEvent $authEvent)
    {
        if ($this->isSatisfied()) {
            $storage = $this->getStorage()->read();
            $authEvent->setIdentity($storage['identity'])
                ->setCode(Result::SUCCESS)
                ->setMessages(array('Authentication successful.'));

            return;
        }

        $enabledProviders = $this->getOptions()->getEnabledProviders();
        $provider = $authEvent->getRequest()->getMetadata('provider');

        if (empty($provider) || !in_array($provider, $enabledProviders)) {
            $authEvent->setCode(Result::FAILURE)
                ->setMessages(array('Invalid provider'));
            $this->setSatisfied(false);

            return false;
        }

        try {
            $hybridAuth = $this->getHybridAuth();
            $adapter = $hybridAuth->authenticate($provider);
            $userProfile = $adapter->getUserProfile();
        } catch (\Exception $ex) {
            $authEvent->setCode(Result::FAILURE)
                ->setMessages(array('Invalid provider'));
            $this->setSatisfied(false);

            return false;
        }


        if (!$userProfile) {
            $authEvent->setCode(Result::FAILURE_IDENTITY_NOT_FOUND)
                ->setMessages(array('A record with the supplied identity could not be found.'));
            $this->setSatisfied(false);

            return false;
        }

        $localUserProvider = $this->getMapper()->findUserByProviderId($userProfile->identifier, $provider);

        if (false == $localUserProvider) {
            if (!$this->getOptions()->getEnableSocialRegistration()) {
                $authEvent->setCode(Result::FAILURE_IDENTITY_NOT_FOUND)
                    ->setMessages(array('A record with the supplied identity could not be found.'));
                $this->setSatisfied(false);

                return false;
            }
            $method = $provider.'ToLocalUser';
            if (method_exists($this, $method)) {
                try {
                    $localUser = $this->$method($userProfile);
                } catch (RuntimeException $ex) {
                    $authEvent->setCode($ex->getCode())
                        ->setMessages(array($ex->getMessage()))
                        ->stopPropagation();
                    $this->setSatisfied(false);

                    return false;
                }
            } else {
                $localUser = $this->instantiateLocalUser();
                $localUser->setDisplayName($userProfile->displayName)
                    ->setPassword($provider);
                if (isset($userProfile->emailVerified) && !empty($userProfile->emailVerified)) {
                    $localUser->setEmail($userProfile->emailVerified);
                }
                $result = $this->insert($localUser, $provider, $userProfile);
            }
            $localUserProvider = clone($this->getMapper()->getEntityPrototype());
            $localUserProvider->setUserId($localUser->getId())
                ->setProviderId($userProfile->identifier)
                ->setProvider($provider);
            $this->getMapper()->insert($localUserProvider);

            // Trigger register.post event
            $this->getEventManager()->trigger('register.post', $this, array('user' => $localUser, 'userProvider' => $localUserProvider));
        }

        $zfcUserOptions = $this->getZfcUserOptions();

        if ($zfcUserOptions->getEnableUserState()) {
            // Don't allow user to login if state is not in allowed list
            $mapper = $this->getZfcUserMapper();
            $user = $mapper->findById($localUserProvider->getUserId());
            if (!in_array($user->getState(), $zfcUserOptions->getAllowedLoginStates())) {
                $authEvent->setCode(Result::FAILURE_UNCATEGORIZED)
                    ->setMessages(array('A record with the supplied identity is not active.'));
                $this->setSatisfied(false);

                return false;
            }
        }

        $authEvent->setIdentity($localUserProvider->getUserId());

        $this->setSatisfied(true);
        $storage = $this->getStorage()->read();
        $storage['identity'] = $authEvent->getIdentity();
        $this->getStorage()->write($storage);
        $authEvent->setCode(Result::SUCCESS)
            ->setMessages(array('Authentication successful.'));
    }

    protected function googleToLocalUser($userProfile)
    {
        if (!isset($userProfile->emailVerified) || empty($userProfile->emailVerified)) {
            throw new RuntimeException(
                'Please verify your email with Google before attempting login',
                Result::FAILURE_CREDENTIAL_INVALID
            );
        }
        $mapper = $this->getZfcUserMapper();
        if (false != ($localUser = $mapper->findByEmail($userProfile->emailVerified))) {
            return $localUser;
        }
        $localUser = $this->instantiateLocalUser();
        $localUser->setEmail($userProfile->emailVerified)
            ->setDisplayName($userProfile->displayName)
            ->setFirstName($userProfile->firstName)
            ->setLastName($userProfile->lastName)
            ->setPassword(__FUNCTION__);
        $result = $this->insert($localUser, 'google', $userProfile);

        return $localUser;
    }

    protected function facebookToLocalUser($userProfile)
    {
        if (!isset($userProfile->emailVerified) || empty($userProfile->emailVerified)) {
            throw new RuntimeException(
                'Please verify your email with Facebook before attempting login',
                Result::FAILURE_CREDENTIAL_INVALID
            );
        }
        $mapper = $this->getZfcUserMapper();
        if (false != ($localUser = $mapper->findByEmail($userProfile->emailVerified))) {
            return $localUser;
        }
        $localUser = $this->instantiateLocalUser();
        $localUser->setEmail($userProfile->emailVerified)
            ->setDisplayName($userProfile->displayName)
            ->setFirstName($userProfile->firstName)
            ->setLastName($userProfile->lastName)
            ->setPassword(__FUNCTION__);
        $result = $this->insert($localUser, 'facebook', $userProfile);

        return $localUser;
    }
} 