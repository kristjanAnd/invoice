<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 15:56
 */

namespace Application\Entity\Document;


use Application\Entity\Document;
use Application\Entity\Subject\Client;
use Application\Entity\Subject\Company;
use Application\Entity\User;
use Application\Entity\Vat;
use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 *
 * @ORM\Table(name="invoice")
 * @ORM\Entity(repositoryClass="Application\Repository\InvoiceRepository")
 */
class Invoice extends Document {

    const STATUS_PENDING = 'pending';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_CONFIRMED = 'confirmed';

    /**
     * @var \Application\Entity\User $user
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\User", inversedBy="invoices")
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @var \Application\Entity\Subject\Company $company
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Subject\Company", inversedBy="invoices")
     *      @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     */
    protected $company;

    /**
     * @var \Application\Entity\Subject\Client $client
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Subject\Client", inversedBy="invoices")
     *      @ORM\JoinColumn(name="subject_id", referencedColumnName="id", nullable=false)
     */
    protected $client;

    /**
     * @var string
     *
     * @ORM\Column(name="client_reference_no", type="string")
     */
    protected $referenceNumber;

    /**
     * @var float
     *
     * @ORM\Column(name="client_delay_percent", type="decimal")
     */
    protected $delayPercent;

    /**
     * @var integer
     *
     * @ORM\Column(name="client_deadline_days", type="integer")
     */
    protected $deadlineDays;

    /**
     * @var \DateTime
     * @ORM\Column(name="client_deadline_date", type="datetime")
     */
    protected $deadlineDate;

    /**
     * @var \Application\Entity\Vat
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Vat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vat_id", referencedColumnName="id")
     * })
     */
    protected $vat;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_sent", type="datetime")
     */
    protected $dateSent;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    protected $status;

    /**
     * @return \Application\Entity\Subject\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \Application\Entity\Subject\Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
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
     * @return \DateTime
     */
    public function getDeadlineDate()
    {
        return $this->deadlineDate;
    }

    /**
     * @param \DateTime $deadlineDate
     */
    public function setDeadlineDate(\DateTime $deadlineDate)
    {
        $this->deadlineDate = $deadlineDate;
    }

    /**
     * @return int
     */
    public function getDeadlineDays()
    {
        return $this->deadlineDays;
    }

    /**
     * @param int $deadlineDays
     */
    public function setDeadlineDays($deadlineDays)
    {
        $this->deadlineDays = $deadlineDays;
    }

    /**
     * @return float
     */
    public function getDelayPercent()
    {
        return $this->delayPercent;
    }

    /**
     * @param float $delayPercent
     */
    public function setDelayPercent($delayPercent)
    {
        $this->delayPercent = $delayPercent;
    }

    /**
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * @param string $referenceNumber
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
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
     * @return \Application\Entity\Vat
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param \Application\Entity\Vat $vat
     */
    public function setVat(Vat $vat)
    {
        $this->vat = $vat;
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
     * @return \DateTime
     */
    public function getDateSent()
    {
        return $this->dateSent;
    }

    /**
     * @param \DateTime $dateSent
     */
    public function setDateSent($dateSent)
    {
        $this->dateSent = $dateSent;
    }


} 