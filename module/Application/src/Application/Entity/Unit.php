<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 13.04.15
 * Time: 17:02
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Entity\Subject\Company;
use Zend\Stdlib\Parameters;

/**
 * @ORM\Entity(repositoryClass="Application\Repository\UnitRepository")
 * @ORM\Table(name="unit")
 */
class Unit extends AbstractEntity {

    const STATUS_ACTIVE = 'active';
    const STATUS_DISABLED = 'disabled';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    protected $status;

    /**
     * @var \Application\Entity\Subject\Company $company
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Subject\Company", inversedBy="units")
     *      @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     */
    protected $company;

    /**
     * @var \Application\Entity\User $user
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\User", inversedBy="units")
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    public function __construct(Parameters $data = null){
        if(isset($data->user)){
            $this->user = $data->user;
        }
        if(isset($data->company)){
            $this->company = $data->company;
        }
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return Subject\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Subject\Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}