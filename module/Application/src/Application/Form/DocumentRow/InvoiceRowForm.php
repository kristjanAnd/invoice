<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 6.05.15
 * Time: 18:12
 */

namespace Application\Form\DocumentRow;


use Application\Domain\DocumentRow\InvoiceRowDto;
use Application\Form\DocumentRowForm;
use Zend\Form\Element\Text;
use Zend\InputFilter\Input;

class InvoiceRowForm extends DocumentRowForm {

    public function init(){
        parent::init();
        $invoiceId = new Text('invoiceIds[]');
        $invoiceId->setAttributes(array(
            'class' => 'form-control input-sm',
        ));
        $this->add($invoiceId);

        return $this;

    }

    public function getInputFilter(){
        $this->filter = parent::getInputFilter();
        $invoiceId = new Input('invoiceIds[]');
        $invoiceId->setRequired(false)->setAllowEmpty(true);
        $this->filter->add($invoiceId);

        return $this->filter;
    }

    public function setFormValues(InvoiceRowDto $rowDto){
        parent::setFormValues($rowDto);
        if($rowDto->getInvoice()){
            $this->get('invoiceIds[]')->setValue($rowDto->getInvoice()->getId());
        }
    }
} 