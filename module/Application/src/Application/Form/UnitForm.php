<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 24.04.15
 * Time: 15:08
 */

namespace Application\Form;

use Application\Service\UnitService;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;
use Zend\Validator\NotEmpty;

class UnitForm extends Form {

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var UnitService
     */
    protected $unitService;

    /**
     * @param UnitService $unitService
     */
    public function setUnitService(UnitService $unitService)
    {
        $this->unitService = $unitService;
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
            'placeholder' => $this->translator->translate('Unit.form.code.placeholder')
        ));
        $code->setLabel($this->translator->translate('Unit.form.code.label'));
        $code->setLabelAttributes(array('class' => 'col-sm-4 control-label'));
        $this->add($code);

        $status = new Select('status');
        $status->setAttributes(array(
            'id' => 'unit',
            'class' => 'form-control'
        ));
        $status->setValueOptions($this->unitService->getUnitStatusSelect());
        $status->setLabel($this->translator->translate('Unit.form.status.label'));
        $status->setLabelAttributes(array('class' => 'col-sm-4 control-label'));
        $this->add($status);

        return $this;
    }

    public function getInputFilter() {
        if ($this->filter == null) {
            $filter = new InputFilter();
            $notEmpty = new NotEmpty();

            $code = new Input('code');
            $code->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('UnitForm.message.codeInput')), NotEmpty::IS_EMPTY));
            $filter->add($code);

            $status = new Input('status');
            $status->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('UnitForm.message.statusInput')), NotEmpty::IS_EMPTY));
            $filter->add($status);

            $this->filter = $filter;
        }

        return $this->filter;
    }
} 