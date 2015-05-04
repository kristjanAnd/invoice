<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 8:01
 */

namespace Application\Form;

use Application\Service\VatService;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;
use Zend\Validator\NotEmpty;

class VatForm  extends Form {

    /**
     * @var Translator
     */
    protected $translator;

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

    public function setTranslator(Translator $t)
    {
        $this->translator = $t;
    }

    public function init() {

        $code = new Text('code');
        $code->setAttributes(array(
            'id' => 'code',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Vat.form.code.placeholder')
        ));
        $code->setLabel($this->translator->translate('Vat.form.code.label'));
        $code->setLabelAttributes(array('class' => 'col-sm-4 control-label'));
        $this->add($code);

        $value = new Text('value');
        $value->setAttributes(array(
            'id' => 'value',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Vat.form.value.placeholder')
        ));
        $value->setLabel($this->translator->translate('Vat.form.value.label'));
        $value->setLabelAttributes(array('class' => 'col-sm-4 control-label'));
        $this->add($value);

        $status = new Select('status');
        $status->setAttributes(array(
            'id' => 'status',
            'class' => 'form-control'
        ));
        $status->setValueOptions($this->vatService->getVatStatusSelect());
        $status->setLabel($this->translator->translate('Vat.form.status.label'));
        $status->setLabelAttributes(array('class' => 'col-sm-4 control-label'));
        $this->add($status);

        return $this;
    }

    public function getInputFilter() {
        if ($this->filter == null) {
            $filter = new InputFilter();
            $notEmpty = new NotEmpty();

            $code = new Input('code');
            $code->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('VatForm.message.codeInput')), NotEmpty::IS_EMPTY));
            $filter->add($code);

            $value = new Input('value');
            $value->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('VatForm.message.valueInput')), NotEmpty::IS_EMPTY));
            $filter->add($value);

            $status = new Input('status');
            $status->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('VatForm.message.statusInput')), NotEmpty::IS_EMPTY));
            $filter->add($status);

            $this->filter = $filter;
        }

        return $this->filter;
    }
} 