<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 6.05.15
 * Time: 17:36
 */

namespace Application\Entity\DocumentRow;

use Application\Entity\Document\Invoice;
use Application\Entity\DocumentRow;
use Doctrine\ORM\Mapping as ORM;

/**
 * InvoiceRow
 *
 * @ORM\Table(name="invoice_row")
 * @ORM\Entity
 */
class InvoiceRow extends DocumentRow {

    /**
     * @var \Application\Entity\Document\Invoice $invoice
     *
     *      @ORM\ManyToOne(targetEntity="Application\Entity\Document\Invoice", inversedBy="rows")
     *      @ORM\JoinColumn(name="invoice_id", referencedColumnName="id", nullable=false)
     */
    protected $invoice;

    /**
     * @return \Application\Entity\Document\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param \Application\Entity\Document\Invoice $invoice
     */
    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }


} 