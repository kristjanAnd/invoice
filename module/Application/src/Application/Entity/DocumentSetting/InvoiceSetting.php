<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 5.05.15
 * Time: 14:17
 */

namespace Application\Entity\DocumentSetting;


use Application\Entity\DocumentSetting;
use Application\Entity\Vat;
use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 *
 * @ORM\Table(name="invoice_setting")
 * @ORM\Entity
 */
class InvoiceSetting extends DocumentSetting {

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
     * @var \Application\Entity\Vat
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Vat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vat_id", referencedColumnName="id")
     * })
     */
    protected $vat;

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



} 