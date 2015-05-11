<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 5.05.15
 * Time: 16:40
 */

namespace Application\Form;


use Application\Service\ArticleService;
use Zend\Form\Element\Select;
use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;
use Application\Entity\Subject\Company;

class AddArticleForm extends Form {

    /**
     * @var Translator
     */
    protected $translator;
    /**
     * @var Company
     */
    protected $company;

    /**
     * @var ArticleService
     */
    protected $articleService;

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
     * @return $this
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
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

        $articleType = new Select('articleType');
        $articleType->setAttributes(array(
            'id' => 'articleType',
            'class' => 'form-control input-sm'
        ));
        $articleType->setValueOptions($this->articleService->getArticleTypeSelect());
        $articleType->setLabel($this->translator->translate('ArticleAdd.form.articleType.label'));
        $articleType->setLabelAttributes(array('class' => 'col-sm-1 control-label input-sm'));
        $this->add($articleType);


        $brand = new Select('brand');
        $brand->setAttributes(array(
            'id' => 'add-brand',
            'class' => 'form-control input-sm'
        ));
        $brand->setEmptyOption($this->translator->translate('ArticleAdd.form.brand.emptyOption'));
        $brand->setValueOptions($this->getArticleBrandSelect());
        $brand->setLabel($this->translator->translate('ArticleAdd.form.brand.label'));
        $brand->setLabelAttributes(array('class' => 'col-sm-1 control-label input-sm'));
        $this->add($brand);

        $category = new Select('category');
        $category->setAttributes(array(
            'id' => 'add-category',
            'class' => 'form-control input-sm'
        ));
        $category->setEmptyOption($this->translator->translate('ArticleAdd.form.category.emptyOption'));
        $category->setValueOptions($this->getArticleCategorySelect());
        $category->setLabel($this->translator->translate('ArticleAdd.form.category.label'));
        $category->setLabelAttributes(array('class' => 'col-sm-1 control-label input-sm'));
        $this->add($category);

        $article = new Select('article');
        $article->setAttributes(array(
            'id' => 'add-article',
            'class' => 'form-control input-sm'
        ));
        $article->setEmptyOption($this->translator->translate('ArticleAdd.form.article.emptyOption'));
        $article->setValueOptions($this->articleService->getItemSelect($this->company));
        $article->setLabel($this->translator->translate('ArticleAdd.form.article.label'));
        $article->setLabelAttributes(array('class' => 'col-sm-1 control-label input-sm'));
        $this->add($article);

        return $this;
    }
} 