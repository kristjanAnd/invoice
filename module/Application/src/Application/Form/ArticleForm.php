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
use Application\Service\ArticleService;
use Application\Service\UnitService;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
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
     * @var ArticleService
     */
    protected $articleService;

    /**
     * @param UnitService $unitService
     */
    public function setUnitService(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    /**
     * @param ArticleService $articleService
     */
    public function setArticleService(ArticleService $articleService)
    {
        $this->articleService = $articleService;
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
        return $this;
    }

    public function getUnitSelect(){
        $result = array();
        foreach($this->unitService->getActiveCompanyUnits($this->company) as $unit){
            $result[$unit->getId()] = $unit->getCode();
        }
        return $result;
    }

    public function getArticleBrandSelect(){
        $result = array();
        foreach($this->articleService->getActiveCompanyArticleBrands($this->company) as $brand){
            $result[$brand->getId()] = $brand->getName();
        }
        return $result;
    }

    public function getArticleCategorySelect(){
        $result = array();
        foreach($this->articleService->getActiveCompanyArticleCategories($this->company) as $category){
            $result[$category->getId()] = $category->getName();
        }
        return $result;
    }

    public function init() {

        $name = new Text('name');
        $name->setAttributes(array(
            'id' => 'name',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Article.form.name.placeholder')
        ));
        $name->setLabel($this->translator->translate('Article.form.name.label'));
        $name->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($name);

        $code = new Text('code');
        $code->setAttributes(array(
            'id' => 'code',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Article.form.code.placeholder')
        ));
        $code->setLabel($this->translator->translate('Article.form.code.label'));
        $code->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($code);

        $salePrice = new Text('salePrice');
        $salePrice->setAttributes(array(
            'id' => 'salePrice',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Article.form.salePrice.placeholder')
        ));
        $salePrice->setLabel($this->translator->translate('Article.form.salePrice.label'));
        $salePrice->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($salePrice);

        $unit = new Select('unit');
        $unit->setAttributes(array(
            'id' => 'unit',
            'class' => 'form-control'
        ));
        $unit->setEmptyOption($this->translator->translate('Article.form.unit.emptyOption'));
        $unit->setValueOptions($this->getUnitSelect());
        $unit->setLabel($this->translator->translate('Article.form.unit.label'));
        $unit->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($unit);

        $brand = new Select('brand');
        $brand->setAttributes(array(
            'id' => 'brand',
            'class' => 'form-control'
        ));
        $brand->setEmptyOption($this->translator->translate('Article.form.brand.emptyOption'));
        $brand->setValueOptions($this->getArticleBrandSelect());
        $brand->setLabel($this->translator->translate('Article.form.brand.label'));
        $brand->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($brand);

        $category = new Select('category');
        $category->setAttributes(array(
            'id' => 'category',
            'class' => 'form-control'
        ));
        $category->setEmptyOption($this->translator->translate('Article.form.category.emptyOption'));
        $category->setValueOptions($this->getArticleCategorySelect());
        $category->setLabel($this->translator->translate('Article.form.category.label'));
        $category->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($category);

        $quantity = new Text('quantity');
        $quantity->setAttributes(array(
            'id' => 'quantity',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Article.form.quantity.placeholder')
        ));
        $quantity->setLabel($this->translator->translate('Article.form.quantity.label'));
        $quantity->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($quantity);

        $status = new Select('status');
        $status->setAttributes(array(
            'id' => 'status',
            'class' => 'form-control'
        ));
        $status->setValueOptions($this->articleService->getArticleStatusSelect());
        $status->setLabel($this->translator->translate('Article.form.status.label'));
        $status->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($status);

        $description = new Textarea('description');
        $description->setAttributes(array(
            'id' => 'description',
            'class' => 'form-control',
            'rows' => 3,
            'placeholder' => $this->translator->translate('Article.form.description.placeholder')
        ));
        $description->setLabel($this->translator->translate('Article.form.description.label'));
        $description->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($description);

        return $this;
    }

    public function getInputFilter() {
        if ($this->filter == null) {
            $filter = new InputFilter();
            $notEmpty = new NotEmpty();
            $notEmpty1 = new NotEmpty();
            $notEmpty2 = new NotEmpty();

            $name = new Input('name');
            $name->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('ArticleForm.message.nameInput')), NotEmpty::IS_EMPTY));
            $filter->add($name);

            $code = new Input('code');
            $code->setRequired(false)->setAllowEmpty(true);
            $filter->add($code);

            $unit = new Input('unit');
            $unit->getValidatorChain()->attach($notEmpty1->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('ArticleForm.message.unitInput')), NotEmpty::IS_EMPTY));
            $filter->add($unit);

            $brand = new Input('brand');
            $brand->setRequired(false)->setAllowEmpty(true);
            $filter->add($brand);

            $description = new Input('description');
            $description->setRequired(false)->setAllowEmpty(true);
            $filter->add($description);

            $category = new Input('category');
            $category->setRequired(false)->setAllowEmpty(true);
            $filter->add($category);

            $salePrice = new Input('salePrice');
            $salePrice->setRequired(false)->setAllowEmpty(true);
            $filter->add($salePrice);

            $quantity = new Input('quantity');
            $quantity->setRequired(false)->setAllowEmpty(true);
            $filter->add($quantity);

            $status = new Input('status');
            $status->getValidatorChain()->attach($notEmpty2->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('ArticleForm.message.statusInput')), NotEmpty::IS_EMPTY));
            $filter->add($status);

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
            if($article->getBrand()){
                $this->get('brand')->setValue($article->getBrand()->getId());
            }
            if($article->getCategory()){
                $this->get('category')->setValue($article->getCategory()->getId());
            }
            $this->get('quantity')->setValue($article->getQuantity());
            $this->get('status')->setValue($article->getStatus());
        }
    }

} 