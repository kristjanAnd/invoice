<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 13:15
 */

namespace Application\Entity\Subject;


use Application\Entity\Document\Invoice;
use Application\Entity\Subject;
use Application\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Parameters;

/**
 * Company
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="Application\Repository\ClientRepository")
 */
class Client extends Subject {

    /**
     * @var \Application\Entity\User $user
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\User", inversedBy="clients")
     *      @ORM\JoinColumn(name="client_user_id", referencedColumnName="id", nullable=false)
     */
    protected $clientUser;

    /**
     * @var \Application\Entity\Subject\Company $company
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Subject\Company", inversedBy="clients")
     *      @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     */
    protected $company;

    /**
     * @var float
     *
     * @ORM\Column(name="delay_percent", type="decimal")
     */
    protected $delayPercent;

    /**
     * @var integer
     *
     * @ORM\Column(name="deadline_days", type="integer")
     */
    protected $deadlineDays;

    /**
     * @var string
     *
     * @ORM\Column(name="reference_no", type="string")
     */
    protected $referenceNumber;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Document\Invoice", mappedBy="client", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="subject_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $invoices;

    public function __construct(Parameters $data = null){
        if(isset($data->user)){
            $this->clientUser = $data->user;
        }
        if(isset($data->company)){
            $this->company = $data->company;
        }
        $this->invoices = new ArrayCollection();
    }

    /**
     * @return \Application\Entity\User
     */
    public function getClientUser()
    {
        return $this->clientUser;
    }

    /**
     * @param \Application\Entity\User $clientUser
     */
    public function setClientUser(User $clientUser)
    {
        $this->clientUser = $clientUser;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
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
     * @return ArrayCollection
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * @param Invoice $invoice
     */
    public function addInvoice(Invoice $invoice)
    {
        $this->invoices[] = $invoice;
        $invoice->setClient($this);
    }

} 