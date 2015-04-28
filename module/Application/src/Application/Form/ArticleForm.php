<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 21.04.15
 * Time: 17:04
 */

namespace Application\Form;


use Application\Entity\Article;
use Application\Entity\Subject\Company;
use Application\Service\UnitService;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;
use Zend\Validator\NotEmpty;

class ArticleForm extends Form {

    /**
     * @var Translator
     */
    protected $translator;

    protected $company;
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

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    public function getUnitSelect(){
        $result = array();
        foreach($this->unitService->getCompanyUnits($this->company) as $unit){
            $result[$unit->getId()] = $unit->getCode();
        }
        return $result;
    }

    public function init() {

        $randId = new Text('randId');
        $randId->setAttributes(array(
            'id' => 'randId',
        ));
        $this->add($randId);

        $name = new Text('name');
        $name->setAttributes(array(
            'id' => 'name',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Article.form.name.placeholder')
        ));
        $name->setLabel($this->translator->translate('Article.form.name.label'));
        $this->add($name);

        $code = new Text('code');
        $code->setAttributes(array(
            'id' => 'code',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Article.form.code.placeholder')
        ));
        $code->setLabel($this->translator->translate('Article.form.code.label'));
        $this->add($code);

        $salePrice = new Text('salePrice');
        $salePrice->setAttributes(array(
            'id' => 'salePrice',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Article.form.salePrice.placeholder')
        ));
        $salePrice->setLabel($this->translator->translate('Article.form.salePrice.label'));
        $this->add($salePrice);

        $unit = new Select('unit');
        $unit->setAttributes(array(
            'id' => 'unit',
            'class' => 'form-control'
        ));
        $unit->setEmptyOption($this->translator->translate('Article.form.unit.emptyOption'));
        $unit->setValueOptions($this->getUnitSelect());
        $unit->setLabel($this->translator->translate('Article.form.unit.label'));
        $this->add($unit);

        $quantity = new Text('quantity');
        $quantity->setAttributes(array(
            'id' => 'quantity',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Article.form.quantity.placeholder')
        ));
        $quantity->setLabel($this->translator->translate('Article.form.quantity.label'));
        $this->add($quantity);

        return $this;
    }

    public function getInputFilter() {
        if ($this->filter == null) {
            $filter = new InputFilter();
            $notEmpty = new NotEmpty();

            $name = new Input('name');
            $name->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('ArticleForm.message.nameInput')), NotEmpty::IS_EMPTY));
            $filter->add($name);

            $code = new Input('code');
            $code->setRequired(false)->setAllowEmpty(true);
            $filter->add($code);

            $unit = new Input('unit');
            $unit->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('ArticleForm.message.unitInput')), NotEmpty::IS_EMPTY));
            $filter->add($unit);

            $salePrice = new Input('salePrice');
            $salePrice->setRequired(false)->setAllowEmpty(true);
            $filter->add($salePrice);

            $quantity = new Input('quantity');
            $quantity->setRequired(false)->setAllowEmpty(true);
            $filter->add($quantity);

            $this->filter = $filter;
        }

        return $this->filter;
    }

    public function setFormValues(Article $article){
        if($article){
            $this->get('name')->setValue($article->getName());
            $this->get('code')->setValue($article->getCode());
            $this->get('salePrice')->setValue($article->getSalePrice());
            if($article->getUnit()){
                $this->get('unit')->setValue($article->getUnit()->getId());
            }
            $this->get('quantity')->setValue($article->getQuantity());
        }
    }

} 