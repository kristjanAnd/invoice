<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 5.05.15
 * Time: 14:10
 */

namespace Application\Entity;

use Application\Entity\Subject\Company;
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Parameters;

/**
 * DocumentSetting
 *
 * @ORM\Table(name="document_setting")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"invoice" = "Application\Entity\DocumentSetting\InvoiceSetting"})
 */
abstract class DocumentSetting extends AbstractEntity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Application\Entity\Subject\Company
     *
     * @ORM\OneToOne(targetEntity="Application\Entity\Subject\Company")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    protected $company;

    /**
     * @var \Application\Entity\User
     *
     * @ORM\OneToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(name="date_pdf_format", type="string")
     */
    protected $datePdfFormat;

    /**
     * @var string
     * @ORM\Column(name="prefix", type="string")
     */
    protected $prefix;

    /**
     * @var string
     * @ORM\Column(name="suffix", type="string")
     */
    protected $suffix;

    /**
     * @var integer
     * @ORM\Column(name="next_number", type="integer")
     */
    protected $nextNumber;

    /**
     * @var string
     * @ORM\Column(name="pdf_language_code", type="string")
     */
    protected $pdfLanguageCode;

    public function __construct(Parameters $data = null){
        if(isset($data->user)){
            $this->user = $data->user;
        }
        if(isset($data->company)){
            $this->company = $data->company;
        }
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
     * @return string
     */
    public function getDatePdfFormat()
    {
        return $this->datePdfFormat;
    }

    /**
     * @param string $datePdfFormat
     */
    public function setDatePdfFormat($datePdfFormat)
    {
        $this->datePdfFormat = $datePdfFormat;
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
     * @return int
     */
    public function getNextNumber()
    {
        return $this->nextNumber;
    }

    /**
     * @param int $nextNumber
     */
    public function setNextNumber($nextNumber)
    {
        $this->nextNumber = $nextNumber;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * @return string
     */
    public function getPdfLanguageCode()
    {
        return $this->pdfLanguageCode;
    }

    /**
     * @param string $pdfLanguageCode
     */
    public function setPdfLanguageCode($pdfLanguageCode)
    {
        $this->pdfLanguageCode = $pdfLanguageCode;
    }

} 