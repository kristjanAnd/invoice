<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 13.04.15
 * Time: 17:10
 */

namespace Application\Entity\Article;


use Application\Entity\Article;
use Application\Entity\Subject\Company;
use Application\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Parameters;

/**
 * Service
 *
 * @ORM\Table(name="service")
 * @ORM\Entity(repositoryClass="Application\Repository\ServiceRepository")
 */
class Service extends Article {
    /**
     * @var \Application\Entity\Subject\Company $company
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Subject\Company", inversedBy="services")
     *      @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     */
    protected $company;

    /**
     * @var \Application\Entity\User $user
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\User", inversedBy="services")
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
     * @return \Application\Entity\Subject\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param \Application\Entity\Subject\Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return \Application\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Application\Entity\User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }


} 