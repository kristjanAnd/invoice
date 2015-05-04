<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 15:39
 */

namespace Application\Entity;

use Application\Entity\Subject\Company;
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Parameters;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"invoice" = "Application\Entity\Document\Invoice"})
 */
abstract class Document extends AbstractEntity {


    const DATE_FORMAT_POINT_dmY = 'd.m.Y';
    const DATE_FORMAT_SLASH_dmY = 'd/m/Y';
    const DATE_FORMAT_SLASH_mdY = 'm/d/Y';

    public static $dateFormats = array(
        self::DATE_FORMAT_POINT_dmY => '25.01.',
        self::DATE_FORMAT_SLASH_dmY => '25/01/',
        self::DATE_FORMAT_SLASH_mdY => '01/25/'
    );

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;

    /**
     * @ORM\Column(name="document_date", type="datetime")
     */
    protected $documentDate;

    /**
     * @var \Application\Entity\Subject\Company
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Subject\Company")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    protected $company;

    /**
     * @var \Application\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    protected $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="document_number", type="integer")
     */
    protected $documentNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="prefix", type="string")
     */
    protected $prefix;

    /**
     * @var string
     *
     * @ORM\Column(name="suffix", type="string")
     */
    protected $suffix;

    /**
     * @var \Application\Entity\Subject
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Subject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="subject_id", referencedColumnName="id")
     * })
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_name", type="string")
     */
    protected $subjectName;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_email", type="string")
     */
    protected $subjectEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_address", type="string")
     */
    protected $subjectAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_reg_no", type="string")
     */
    protected $subjectRegNo;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_vat_no", type="string")
     */
    protected $subjectVatNo;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal")
     */
    protected $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="vat_amount", type="decimal")
     */
    protected $vatAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_vat", type="decimal")
     */
    protected $amountVat;

    /**
     * @var string
     * @ORM\Column(name="language_code", type="string")
     */
    protected $languageCode;

    /**
     * @var string
     * @ORM\Column(name="date_format", type="string")
     */
    protected $dateFormat;

    public function __construct(Parameters $data = null){
        if(isset($data->user)){
            $this->user = $data->user;
        }
        if(isset($data->company)){
            $this->company = $data->company;
        }
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getAmountVat()
    {
        return $this->amountVat;
    }

    /**
     * @param float $amountVat
     */
    public function setAmountVat($amountVat)
    {
        $this->amountVat = $amountVat;
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
    public function setCompany(Subject\Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDocumentDate()
    {
        return $this->documentDate;
    }

    /**
     * @param \DateTime $documentDate
     */
    public function setDocumentDate(\DateTime $documentDate)
    {
        $this->documentDate = $documentDate;
    }

    /**
     * @return int
     */
    public function getDocumentNumber()
    {
        return $this->documentNumber;
    }

    /**
     * @param int $documentNumber
     */
    public function setDocumentNumber($documentNumber)
    {
        $this->documentNumber = $documentNumber;
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
     * @return Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param Subject $subject
     */
    public function setSubject(Subject $subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubjectAddress()
    {
        return $this->subjectAddress;
    }

    /**
     * @param string $subjectAddress
     */
    public function setSubjectAddress($subjectAddress)
    {
        $this->subjectAddress = $subjectAddress;
    }

    /**
     * @return string
     */
    public function getSubjectEmail()
    {
        return $this->subjectEmail;
    }

    /**
     * @param string $subjectEmail
     */
    public function setSubjectEmail($subjectEmail)
    {
        $this->subjectEmail = $subjectEmail;
    }

    /**
     * @return string
     */
    public function getSubjectName()
    {
        return $this->subjectName;
    }

    /**
     * @param string $subjectName
     */
    public function setSubjectName($subjectName)
    {
        $this->subjectName = $subjectName;
    }

    /**
     * @return string
     */
    public function getSubjectRegNo()
    {
        return $this->subjectRegNo;
    }

    /**
     * @param string $subjectRegNo
     */
    public function setSubjectRegNo($subjectRegNo)
    {
        $this->subjectRegNo = $subjectRegNo;
    }

    /**
     * @return string
     */
    public function getSubjectVatNo()
    {
        return $this->subjectVatNo;
    }

    /**
     * @param string $subjectVatNo
     */
    public function setSubjectVatNo($subjectVatNo)
    {
        $this->subjectVatNo = $subjectVatNo;
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
     * @return float
     */
    public function getVatAmount()
    {
        return $this->vatAmount;
    }

    /**
     * @param float $vatAmount
     */
    public function setVatAmount($vatAmount)
    {
        $this->vatAmount = $vatAmount;
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @param string $dateFormat
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * @param string $languageCode
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;
    }


} 