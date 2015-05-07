<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 6.05.15
 * Time: 17:53
 */

namespace Application\Form;


use Application\Domain\DocumentRowDto;
use Application\Entity\Article;
use Application\Entity\Subject\Company;
use Application\Service\UnitService;
use Application\Service\VatService;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;

class DocumentRowForm extends Form {

    /**
     * @var Company
     */
    protected $company;
    /**
     * @var Article
     */
    protected $article;

    /**
     * @var VatService
     */
    protected $vatService;

    /**
     * @var UnitService
     */
    protected $unitService;

    /**
     * @var Translator
     */
    protected $translator;

    public function setTranslator(Translator $t)
    {
        $this->translator = $t;
    }

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
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param Article $article
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;
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

    public function getUnitSelect(){
        $result = array();
        foreach($this->unitService->getActiveCompanyUnits($this->company) as $unit){
            $result[$unit->getId()] = $unit->getCode();
        }
        return $result;
    }

    public function init()
    {
        $vat = new Select('vats[]');
        $vat->setAttributes(array(
            'class' => 'form-control vat'
        ));
        $vat->setValueOptions($this->vatService->getCompanyActiveVatSelect($this->company));
        $vat->setEmptyOption($this->translator->translate('DocumentRowForm.form.vat.emptyOption'));
        $this->add($vat);

        $unit = new Select('units[]');
        $unit->setAttributes(array(
            'class' => 'form-control unit'
        ));
        $unit->setValueOptions($this->getUnitSelect());
        $unit->setEmptyOption($this->translator->translate('DocumentRowForm.form.unit.emptyOption'));
        $this->add($unit);

        $name = new Text('names[]');
        $name->setAttributes(array(
            'class' => 'form-control name'
        ));
        $this->add($name);

        $quantity = new Text('quantities[]');
        $quantity->setAttributes(array(
            'class' => 'form-control quantity'
        ));
        $this->add($quantity);

        $amount = new Text('amounts[]');
        $amount->setAttributes(array(
            'class' => 'form-control amount'
        ));
        $this->add($amount);

        $vatAmount = new Text('vatAmounts[]');
        $vatAmount->setAttributes(array(
            'class' => 'form-control vatAmount'
        ));
        $this->add($vatAmount);

        $amountVat = new Text('amountVats[]');
        $amountVat->setAttributes(array(
            'class' => 'form-control amountVat'
        ));
        $this->add($amountVat);
    }

    public function getInputFilter()
    {
        if ($this->filter == null) {
            $filter = new InputFilter();

            $vat = new Input('vats[]');
            $vat->setRequired(false)->setAllowEmpty(true);
            $filter->add($vat);

            $unit = new Input('units[]');
            $unit->setRequired(false)->setAllowEmpty(true);
            $filter->add($unit);

            $name = new Input('names[]');
            $name->setRequired(false)->setAllowEmpty(true);
            $filter->add($name);

            $quantity = new Input('quantities[]');
            $quantity->setRequired(false)->setAllowEmpty(true);
            $filter->add($quantity);

            $amount = new Input('amounts[]');
            $amount->setRequired(false)->setAllowEmpty(true);
            $filter->add($amount);

            $vatAmount = new Input('vatAmounts[]');
            $vatAmount->setRequired(false)->setAllowEmpty(true);
            $filter->add($vatAmount);

            $amountVat = new Input('amountVats[]');
            $amountVat->setRequired(false)->setAllowEmpty(true);
            $filter->add($amountVat);


            $this->filter = $filter;
        }

        return $this->filter;
    }

    public function setFormValues(DocumentRowDto $rowDto){
        if($rowDto){
            if($rowDto->getUnit()){
                $this->get('units[]')->setValue($rowDto->getUnit()->getId());
            }
            if($rowDto->getVat()){
                $this->get('vats[]')->setValue($rowDto->getVat()->getId());
            }
            if($rowDto->getArticle()){
                $this->setArticle($rowDto->getArticle());
            }
            $this->get('names[]')->setValue($rowDto->getName());
            $this->get('quantities[]')->setValue($rowDto->getQuantity());
            $this->get('amounts[]')->setValue($rowDto->getAmount());
            $this->get('vatAmounts[]')->setValue($rowDto->getVatAmount());
            $this->get('amountVats[]')->setValue($rowDto->getAmountVat());
        }
    }


} 