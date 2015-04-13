<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4/24/14
 * Time: 3:33 PM
 */

namespace Application\Service;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Log\LoggerInterface;
use Zend\Config\Config;
use Doctrine\ORM\EntityManager;

abstract class AbstractService implements ServiceLocatorAwareInterface, ObjectManagerAwareInterface {

    /**
     * Enity Manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Config
     * @var ServiceManager
     */

    protected $locator;

    /**
     * @var \Zend\Log\LoggerInterface
     */
    protected $logger;

    private $config;

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * @return \Zend\Log\LoggerInterface
     */
    public function getLogger() {
        return $this->logger;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->locator = $serviceLocator;
        return $this;
    }

    public function getServiceLocator() {
        return $this->locator;
    }

    public function isEntityManagerConnected() {
        return $this->entityManager->getConnection()->isConnected();
    }

    public static function startTransaction($entityManager) {
        if (!$entityManager->getConnection()->isTransactionActive()) {
            $entityManager->beginTransaction();
        }
    }

    public static function commit(EntityManager $entityManager) {
        if ($entityManager->getConnection()->isTransactionActive()) { //This place tried to commit without an active transaction
            $entityManager->commit();
            $entityManager->flush();
        }
    }

    public static function rollback(EntityManager $entityManager) {
        if ($entityManager->getConnection()->isTransactionActive()) { //This place tried to commit without an active transaction
            $entityManager->rollback();
            $entityManager->beginTransaction();
        }
    }

    public function setObjectManager(ObjectManager $objectManager) {
        $this->entityManager = $objectManager;
    }

    public function getObjectManager() {
        return $this->entityManager;
    }

    /**
     * @deprecated Use getObjectManager instead
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->entityManager;
    }

    /**
     * Create new entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function newEntityManager() {
        $newEntityManager = $this->locator->create('doctrine.entitymanager.orm_default');
        $this->entityManager = $newEntityManager;
        foreach ($this->locator->getCanonicalNames() as $alias => $canonicalName) {
            if (strstr($alias, '\Service\\')) {
                $service = $this->locator->get($alias);
                if ($service instanceof AbstractService) {
                    $service->setObjectManager($newEntityManager);
                }
            }
        }

        return $newEntityManager;
    }

    /**
     * @return \Zend\Config\Config
     */
    public function getConfig() {
        if ($this->config == null) {
            $this->config = new Config($this->locator->get('Config'));
        }

        return $this->config;
    }

    public function refreshTransaction() {
        $this->entityManager->flush();
        $this->entityManager->commit();
        $this->entityManager->beginTransaction();
    }

    public function refreshConnection() {
        $this->entityManager->commit();
        $this->entityManager->beginTransaction();
    }

    /**
     * @param AbstractService $service
     * @return AbstractService
     */
    protected function getService(AbstractService $service) {
        $service->setEntityManager($this->em);
        $service->setConfig($this->config);

        return $service;
    }

    public static function getClass() {
        return get_called_class();
    }

    public function getOneObjectByField($objectClass, $field, $value) {
        if ($field !== null && $value !== null) {
            return $this->entityManager->getRepository($objectClass)->findOneBy(array($field => $value));
        }
    }

    public function getOneObjectByArray($objectClass, array $fieldValues) {
        if (count($fieldValues) > 0) {
            return $this->entityManager->getRepository($objectClass)->findOneBy($fieldValues);
        }
    }

    public function getObjectByField($objectClass, array $fieldValues) {
        if (count($fieldValues) > 0) {
            return $this->entityManager->getRepository($objectClass)->findBy($fieldValues);
        }
    }

    public function deleteObject($object) {
        if (is_object($object)) {
            $this->entityManager->remove($object);
            $this->entityManager->flush();
        }
    }

    public function saveObject($object) {
        if (is_object($object)) {
            $this->entityManager->persist($object);
            $this->entityManager->flush($object);
            return $object;
        }
    }

    public function findAllObjects($objectClass) {
        return $this->entityManager->getRepository($objectClass)->findAll();
    }
}