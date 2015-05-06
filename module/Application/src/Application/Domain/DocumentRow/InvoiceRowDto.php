<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 6.05.15
 * Time: 17:43
 */

namespace Application\Domain\DocumentRow;


use Application\Domain\DocumentRowDto;
use Application\Entity\Document\Invoice;

class InvoiceRowDto extends DocumentRowDto {

    /**
     * @var \Application\Entity\Document\Invoice $invoice
     */
    protected $invoice;

    /**
     * @return Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param Invoice $invoice
     */
    public function setInvoice(Invoice $invoice = null)
    {
        $this->invoice = $invoice;
    }


} 