<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 18:13
 */

namespace Application\Form\Document;


use Application\Entity\Document\Invoice;
use Application\Entity\DocumentSetting\InvoiceSetting;
use Application\Form\DocumentForm;
use Application\Service\ClientService;
use Application\Service\VatService;
use Application\Validator\MoneyValidator;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\InputFilter\Input;
use Zend\Validator\Digits;

class InvoiceForm extends DocumentForm {
    /**
     * @var ClientService
     */
    protected $clientService;
    /**
     * @var VatService
     */
    protected $vatService;

    /**
     * @param VatService $vatService
     */
    public function setVatService(VatService $vatService)
    {
        $this->vatService = $vatService;
    }

    /**
     * @param ClientService $clientService
     */
    public function setClientService(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function init(){
        parent::init();

        $client = new Select('client');
        $client->setAttributes(array(
            'id' => 'client',
            'class' => 'form-control'
        ));
        $client->setValueOptions($this->clientService->getCompanyActiveClientSelect($this->company));
        $client->setEmptyOption($this->translator->translate('Invoice.form.client.emptyOption'));
        $client->setLabel($this->translator->translate('Invoice.form.client.label'));
        $client->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($client);

        $vat = new Select('vat');
        $vat->setAttributes(array(
            'id' => 'vat',
            'class' => 'form-control'
        ));
        $vat->setValueOptions($this->vatService->getCompanyActiveVatSelect($this->company));
        $vat->setEmptyOption($this->translator->translate('Invoice.form.vat.emptyOption'));
        $vat->setLabel($this->translator->translate('Invoice.form.vat.label'));
        $vat->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($vat);

        $this->get('subjectName')->setAttributes(array('placeholder' => $this->translator->translate('Invoice.form.clientName.placeholder')));
        $this->get('subjectName')->setLabel($this->translator->translate('Invoice.form.clientName.label'));

        $this->get('subjectEmail')->setAttributes(array('placeholder' => $this->translator->translate('Invoice.form.clientEmail.placeholder')));
        $this->get('subjectEmail')->setLabel($this->translator->translate('Invoice.form.clientEmail.label'));

        $this->get('subjectAddress')->setAttributes(array('placeholder' => $this->translator->translate('Invoice.form.clientAddress.placeholder')));
        $this->get('subjectAddress')->setLabel($this->translator->translate('Invoice.form.clientAddress.label'));

        $this->get('subjectRegNo')->setAttributes(array('placeholder' => $this->translator->translate('Invoice.form.clientRegNo.placeholder')));
        $this->get('subjectRegNo')->setLabel($this->translator->translate('Invoice.form.clientRegNo.label'));

        $this->get('subjectVatNo')->setAttributes(array('placeholder' => $this->translator->translate('Invoice.form.clientVatNo.placeholder')));
        $this->get('subjectVatNo')->setLabel($this->translator->translate('Invoice.form.clientVatNo.label'));

        $delayPercent = new Text('delayPercent');
        $delayPercent->setAttributes(array(
            'id' => 'delayPercent',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Invoice.form.delayPercent.placeholder')
        ));
        $delayPercent->setLabel($this->translator->translate('Invoice.form.delayPercent.label'));
        $delayPercent->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($delayPercent);

        $deadlineDays = new Text('deadlineDays');
        $deadlineDays->setAttributes(array(
            'id' => 'deadlineDays',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Invoice.form.deadlineDays.placeholder')
        ));
        $deadlineDays->setLabel($this->translator->translate('Invoice.form.deadlineDays.label'));
        $deadlineDays->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($deadlineDays);

        $referenceNumber = new Text('referenceNumber');
        $referenceNumber->setAttributes(array(
            'id' => 'referenceNumber',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Invoice.form.referenceNumber.placeholder')
        ));
        $referenceNumber->setLabel($this->translator->translate('Invoice.form.referenceNumber.label'));
        $referenceNumber->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($referenceNumber);

        $deadlineDate = new Text('deadlineDate');
        $deadlineDate->setAttributes(array(
            'id' => 'deadlineDate',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Invoice.form.deadlineDate.placeholder')
        ));
        $deadlineDate->setLabel($this->translator->translate('Invoice.form.deadlineDate.label'));
        $deadlineDate->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($deadlineDate);

        return $this;

    }

    public function getInputFilter(){
        $this->filter = parent::getInputFilter();

        $client = new Input('client');
        $client->setRequired(false)->setAllowEmpty(true);
        $this->filter->add($client);

        $vat = new Input('vat');
        $vat->setRequired(false)->setAllowEmpty(true);
        $this->filter->add($vat);

        $floatValidator = new MoneyValidator();
        $floatValidator->setMessage($this->translator->translate('Invoice.form.delayPercent.notDigits'), MoneyValidator::NOT_FLOAT);

        $deadlineDayDigits = new Digits();
        $deadlineDayDigits->setMessage($this->translator->translate('Invoice.form.deadlineDays.notDigits'), Digits::NOT_DIGITS);

        $delayPercent = new Input('delayPercent');
        $delayPercent->setRequired(false)->setAllowEmpty(true);
        $delayPercent->getValidatorChain()->attach($floatValidator);
        $this->filter->add($delayPercent);

        $deadlineDays = new Input('deadlineDays');
        $deadlineDays->setRequired(false)->setAllowEmpty(true);
        $deadlineDays->getValidatorChain()->attach($deadlineDayDigits);
        $this->filter->add($deadlineDays);

        $referenceNumber = new Input('referenceNumber');
        $referenceNumber->setRequired(false)->setAllowEmpty(true);
        $this->filter->add($referenceNumber);

        $deadlineDate = new Input('deadlineDate');
        $deadlineDate->setRequired(false)->setAllowEmpty(true);
        $this->filter->add($deadlineDate);

        return $this->filter;
    }

    public function setFormValues(Invoice $invoice){
        parent::setFormValues($invoice);
        if($invoice->getClient()){
            $this->get('client')->setValue($invoice->getClient()->getId());
        }
        if($invoice->getDeadlineDate()){
            $this->get('deadlineDate')->setValue($invoice->getDeadlineDate()->format($invoice->getDateFormat()));
        }
        if($invoice->getVat()){
            $this->get('vat')->setValue($invoice->getVat()->getId());
        }
        $this->get('delayPercent')->setValue($invoice->getDelayPercent());
        $this->get('deadlineDays')->setValue($invoice->getDeadlineDays());
        $this->get('referenceNumber')->setValue($invoice->getReferenceNumber());
    }

    public function setDefaultData(InvoiceSetting $invoiceSetting)
    {
        parent::setDefaultData($invoiceSetting);
        if($invoiceSetting->getDelayPercent()){
            $this->get('delayPercent')->setValue($invoiceSetting->getDelayPercent());
        }
        if($invoiceSetting->getVat()){
            $this->get('delayPercent')->setValue($invoiceSetting->getDelayPercent());
        }
        if($invoiceSetting->getVat()){
            $this->get('vat')->setValue($invoiceSetting->getVat()->getId());
        }
        if($invoiceSetting->getDeadlineDays()){
            $this->get('deadlineDays')->setValue($invoiceSetting->getDeadlineDays());
            $date = \DateTime::createFromFormat($this->getDateFormat(), $this->get('documentDate')->getValue());
            if($date){
                $date->modify('+' . $invoiceSetting->getDeadlineDays() . ' days');
                $this->get('deadlineDate')->setValue($date->format($this->getDateFormat()));
            }
        }
    }
} 