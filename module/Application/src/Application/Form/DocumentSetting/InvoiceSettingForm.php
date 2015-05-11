<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 5.05.15
 * Time: 14:52
 */

namespace Application\Form\DocumentSetting;


use Application\Entity\DocumentSetting\InvoiceSetting;
use Application\Form\DocumentSettingForm;
use Application\Entity\Document\Invoice;
use Application\Form\DocumentForm;
use Application\Service\ClientService;
use Application\Service\VatService;
use Application\Validator\MoneyValidator;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\InputFilter\Input;
use Zend\Validator\Digits;

class InvoiceSettingForm extends DocumentSettingForm {

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

    public function init(){
        parent::init();

        $this->get('prefix')->setAttributes(array('placeholder' => $this->translator->translate('InvoiceSetting.form.prefix.placeholder')));
        $this->get('prefix')->setLabel($this->translator->translate('InvoiceSetting.form.prefix.label'));

        $this->get('suffix')->setAttributes(array('placeholder' => $this->translator->translate('InvoiceSetting.form.suffix.placeholder')));
        $this->get('suffix')->setLabel($this->translator->translate('InvoiceSetting.form.suffix.label'));

        $this->get('dateFormat')->setLabel($this->translator->translate('InvoiceSetting.form.dateFormat.label'));
        $this->get('languageCode')->setLabel($this->translator->translate('InvoiceSetting.form.languageCode.label'));

        $this->get('nextNumber')->setAttributes(array('placeholder' => $this->translator->translate('InvoiceSetting.form.nextNumber.placeholder')));
        $this->get('nextNumber')->setLabel($this->translator->translate('InvoiceSetting.form.nextNumber.label'));

        $delayPercent = new Text('delayPercent');
        $delayPercent->setAttributes(array(
            'id' => 'delayPercent',
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('InvoiceSetting.form.delayPercent.placeholder')
        ));
        $delayPercent->setLabel($this->translator->translate('InvoiceSetting.form.delayPercent.label'));
        $delayPercent->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($delayPercent);

        $deadlineDays = new Text('deadlineDays');
        $deadlineDays->setAttributes(array(
            'id' => 'deadlineDays',
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('InvoiceSetting.form.deadlineDays.placeholder')
        ));
        $deadlineDays->setLabel($this->translator->translate('InvoiceSetting.form.deadlineDays.label'));
        $deadlineDays->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($deadlineDays);

        $vat = new Select('vat');
        $vat->setAttributes(array(
            'id' => 'vat',
            'class' => 'form-control input-sm'
        ));
        $vat->setValueOptions($this->vatService->getCompanyActiveVatSelect($this->company));
        $vat->setEmptyOption($this->translator->translate('InvoiceSetting.form.vat.emptyOption'));
        $vat->setLabel($this->translator->translate('InvoiceSetting.form.vat.label'));
        $vat->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($vat);

        return $this;

    }

    public function getInputFilter(){
        $this->filter = parent::getInputFilter();

        $vat = new Input('vat');
        $vat->setRequired(false)->setAllowEmpty(true);
        $this->filter->add($vat);

        $floatValidator = new MoneyValidator();
        $floatValidator->setMessage($this->translator->translate('InvoiceSetting.form.delayPercent.notDigits'), MoneyValidator::NOT_FLOAT);

        $deadlineDayDigits = new Digits();
        $deadlineDayDigits->setMessage($this->translator->translate('InvoiceSetting.form.deadlineDays.notDigits'), Digits::NOT_DIGITS);

        $delayPercent = new Input('delayPercent');
        $delayPercent->setRequired(false)->setAllowEmpty(true);
        $delayPercent->getValidatorChain()->attach($floatValidator);
        $this->filter->add($delayPercent);

        $deadlineDays = new Input('deadlineDays');
        $deadlineDays->setRequired(false)->setAllowEmpty(true);
        $deadlineDays->getValidatorChain()->attach($deadlineDayDigits);
        $this->filter->add($deadlineDays);

        return $this->filter;
    }

    public function setFormValues(InvoiceSetting $invoiceSetting){
        parent::setFormValues($invoiceSetting);
        if($invoiceSetting->getVat()){
            $this->get('vat')->setValue($invoiceSetting->getVat()->getId());
        }
        $this->get('delayPercent')->setValue($invoiceSetting->getDelayPercent());
        $this->get('deadlineDays')->setValue($invoiceSetting->getDeadlineDays());
    }

}