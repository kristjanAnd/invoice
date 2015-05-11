<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 24.04.15
 * Time: 17:16
 */

namespace Application\Form;

use Application\Service\ArticleService;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;
use Zend\Validator\NotEmpty;

class BrandForm extends Form {

    /**
     * @var Translator
     */
    protected $translator;

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

    public function init() {

        $name = new Text('name');
        $name->setAttributes(array(
            'id' => 'name',
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('Brand.form.name.placeholder')
        ));
        $name->setLabel($this->translator->translate('Brand.form.name.label'));
        $name->setLabelAttributes(array('class' => 'col-sm-3 control-label input-sm'));
        $this->add($name);

        $status = new Select('status');
        $status->setAttributes(array(
            'id' => 'status',
            'class' => 'form-control input-sm'
        ));
        $status->setValueOptions($this->articleService->getArticleBrandStatusSelect());
        $status->setLabel($this->translator->translate('Brand.form.status.label'));
        $status->setLabelAttributes(array('class' => 'col-sm-3 control-label input-sm'));
        $this->add($status);

        return $this;
    }

    public function getInputFilter() {
        if ($this->filter == null) {
            $filter = new InputFilter();
            $notEmpty = new NotEmpty();

            $name = new Input('name');
            $name->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('BrandForm.message.nameInput')), NotEmpty::IS_EMPTY));
            $filter->add($name);

            $status = new Input('status');
            $status->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('BrandForm.message.statusInput')), NotEmpty::IS_EMPTY));
            $filter->add($status);

            $this->filter = $filter;
        }

        return $this->filter;
    }
} 