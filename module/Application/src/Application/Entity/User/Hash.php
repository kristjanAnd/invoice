<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 9/18/14
 * Time: 8:17 AM
 */

namespace Application\Entity\User;


use Application\Entity\AbstractEntity;
use Application\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_hash", indexes={@ORM\Index(name="fk_user_hash_user", columns={"user_id"})})
 */
class Hash extends AbstractEntity
{

    /**
     *
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var User $user
     *
     *      @ORM\OneToOne(targetEntity="Application\Entity\User", inversedBy="hash")
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     *
     * @var $hash
     * @ORM\Column(name="hash", type="string", nullable=false)
     */
    private $hash;

    /**
     *
     * @var \DateTime $dateCreated
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    private $dateCreated;

    public function __construct(User $user = null, $hash = null)
    {
        $this->user = $user;
        $this->hash = $hash;
        $this->dateCreated = new \DateTime();
    }

    /**
     * @param DateTime $dateCreated
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     *
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return the $hash
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     *
     * @param string $hash
     * @return \Application\Entity\User\Hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @param User $user
     * @return Hash
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     *
     * @return \DateTime $dateCreated
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
}
