<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 7.05.15
 * Time: 15:04
 */

namespace Application\Form;

use Application\Entity\ArticleSetting;
use Application\Entity\Document;
use Application\Entity\DocumentSetting;
use Application\Entity\Subject\Company;
use Application\Entity\User;
use Application\Service\DocumentService;
use Application\Service\LanguageService;
use Application\Service\UnitService;
use Application\Service\VatService;
use Application\Validator\MoneyValidator;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;
use Zend\Form\Form;
use Zend\Validator\Digits;
use Zend\Validator\NotEmpty;
class ArticleSettingForm extends Form {
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var Company
     */
    protected $company;

    /**
     * @var VatService
     */
    protected $vatService;

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

    /**
     * @param VatService $vatService
     */
    public function setVatService(VatService $vatService)
    {
        $this->vatService = $vatService;
    }

    /**
     * @param Company $company
     * @return $this
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    public function setTranslator(Translator $t)
    {
        $this->translator = $t;
    }

    public function init()
    {
        $unit = new Select('unit');
        $unit->setAttributes(array(
            'id' => 'unit',
            'class' => 'form-control'
        ));
        $unit->setEmptyOption($this->translator->translate('ArticleSetting.form.unit.emptyOption'));
        $unit->setValueOptions($this->unitService->getCompanyActiveUnitSelect($this->company));
        $unit->setLabel($this->translator->translate('ArticleSetting.form.unit.label'));
        $unit->setLabelAttributes(array('class' => 'col-sm-4 control-label'));
        $this->add($unit);

        $vat = new Select('vat');
        $vat->setAttributes(array(
            'id' => 'vat',
            'class' => 'form-control'
        ));
        $vat->setValueOptions($this->vatService->getCompanyActiveVatSelect($this->company));
        $vat->setEmptyOption($this->translator->translate('ArticleSetting.form.vat.emptyOption'));
        $vat->setLabel($this->translator->translate('ArticleSetting.form.vat.label'));
        $vat->setLabelAttributes(array('class' => 'col-sm-4 control-label'));
        $this->add($vat);


        $quantity = new Text('quantity');
        $quantity->setAttributes(array(
            'id' => 'quantity',
            'class' => 'form-control quantity',
            'placeholder' => $this->translator->translate('ArticleSetting.form.quantity.placeholder')
        ));
        $quantity->setLabel($this->translator->translate('ArticleSetting.form.quantity.label'));
        $quantity->setLabelAttributes(array('class' => 'col-sm-4 control-label'));
        $this->add($quantity);

        return $this;
    }

    public function getInputFilter()
    {
        if ($this->filter == null) {
            $filter = new InputFilter();

            $unit = new Input('unit');
            $unit->setRequired(false)->setAllowEmpty(true);
            $filter->add($unit);

            $vat = new Input('vat');
            $vat->setRequired(false)->setAllowEmpty(true);
            $filter->add($vat);

            $quantityValidator = new MoneyValidator();
            $quantityValidator->setMessage($this->translator->translate('Document.form.amountVat.notDigits'), MoneyValidator::NOT_FLOAT);

            $quantity = new Input('quantity');
            $quantity->setRequired(false)->setAllowEmpty(true);
            $quantity->getValidatorChain()->attach($quantityValidator);
            $filter->add($quantity);

            $this->filter = $filter;
        }

        return $this->filter;
    }

    public function setFormValues(ArticleSetting $articleSetting){
        if($articleSetting){
            if($articleSetting->getVat()){
                $this->get('vat')->setValue($articleSetting->getVat()->getId());
            }
            if($articleSetting->getUnit()){
                $this->get('unit')->setValue($articleSetting->getUnit()->getId());
            }
            $this->get('quantity')->setValue($articleSetting->getQuantity());
        }
    }

    public function disableFields(){
        foreach($this->getElements() as $element){
            $element->setAttribute('disabled', 'disabled');
        }
    }
} 