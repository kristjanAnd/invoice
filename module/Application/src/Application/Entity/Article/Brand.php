<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 24.04.15
 * Time: 9:31
 */

namespace Application\Entity\Article;


use Application\Entity\AbstractEntity;
use Application\Entity\Article;
use Application\Entity\Subject\Company;
use Application\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Parameters;

/**
 * Brand
 *
 * @ORM\Table(name="article_brand")
 * @ORM\Entity(repositoryClass="Application\Repository\ArticleBrandRepository")
 */
class Brand extends AbstractEntity {

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
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    protected $status;

    /**
     * @var \Application\Entity\Subject\Company $company
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Subject\Company", inversedBy="articleBrands")
     *      @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     */
    protected $company;

    /**
     * @var \Application\Entity\User $user
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\User", inversedBy="articleBrands")
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Article", mappedBy="brand", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $articles;

    public function __construct(Parameters $data = null){
        $this->status = self::STATUS_DISABLED;
        if(isset($data->user)){
            $this->user = $data->user;
        }
        if(isset($data->company)){
            $this->company = $data->company;
        }
        $this->articles = new ArrayCollection();
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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

    /**
     * @return ArrayCollection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @param Article $article
     */
    public function addArticle(Article $article)
    {
        $this->articles[] = $article;
    }


} 