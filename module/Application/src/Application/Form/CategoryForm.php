<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 24.04.15
 * Time: 17:18
 */

namespace Application\Form;

use Application\Service\ArticleService;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;
use Zend\Validator\NotEmpty;

class CategoryForm extends Form {

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
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Category.form.name.placeholder')
        ));
        $name->setLabel($this->translator->translate('Category.form.name.label'));
        $name->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
        $this->add($name);

        $code = new Text('code');
        $code->setAttributes(array(
            'id' => 'code',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Category.form.code.placeholder')
        ));
        $code->setLabel($this->translator->translate('Category.form.code.label'));
        $code->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
        $this->add($code);

        $description = new Textarea('description');
        $description->setAttributes(array(
            'id' => 'description',
            'class' => 'form-control',
            'rows' => 3,
            'placeholder' => $this->translator->translate('Category.form.description.placeholder')
        ));
        $description->setLabel($this->translator->translate('Category.form.description.label'));
        $description->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
        $this->add($description);

        $status = new Select('status');
        $status->setAttributes(array(
            'id' => 'unit',
            'class' => 'form-control'
        ));
        $status->setValueOptions($this->articleService->getCategoryStatusSelect());
        $status->setLabel($this->translator->translate('Category.form.status.label'));
        $status->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
        $this->add($status);

        return $this;
    }

    public function getInputFilter() {
        if ($this->filter == null) {
            $filter = new InputFilter();
            $notEmpty = new NotEmpty();

            $name = new Input('name');
            $name->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('CategoryForm.message.nameInput')), NotEmpty::IS_EMPTY));
            $filter->add($name);

            $code = new Input('code');
            $code->setRequired(false)->setAllowEmpty(true);
            $filter->add($code);

            $description = new Input('description');
            $description->setRequired(false)->setAllowEmpty(true);
            $filter->add($description);

            $status = new Input('status');
            $status->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('CategoryForm.message.statusInput')), NotEmpty::IS_EMPTY));
            $filter->add($status);

            $this->filter = $filter;
        }

        return $this->filter;
    }
} 