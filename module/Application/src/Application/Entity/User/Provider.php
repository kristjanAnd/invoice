<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 9.03.15
 * Time: 11:59
 */

namespace Application\Entity\User;


use Application\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_provider")
 *
 */
class Provider extends AbstractEntity{

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", name="user_id")
     */
    protected  $userId;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", name="provider_id")
     */
    protected $providerId;

    /**
     * @var string
     * @ORM\Column(type="string", name="provider")
     */
    protected $provider;

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return int
     */
    public function getProviderId()
    {
        return $this->providerId;
    }

    /**
     * @param int $providerId
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;
        return $this;
    }

} 