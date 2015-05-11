<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 5.05.15
 * Time: 14:31
 */

namespace Application\Form;

use Application\Entity\Document;
use Application\Entity\DocumentSetting;
use Application\Entity\Subject\Company;
use Application\Entity\User;
use Application\Service\DocumentService;
use Application\Service\LanguageService;
use Application\Validator\MoneyValidator;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;
use Zend\Form\Form;
use Zend\Validator\Digits;
use Zend\Validator\NotEmpty;

class DocumentSettingForm extends Form
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var Company
     */
    protected $company;

    /**
     * @var DocumentService
     */
    protected $documentService;

    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * @param LanguageService $languageService
     */
    public function setLanguageService(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * @param DocumentService $documentService
     */
    public function setDocumentService(DocumentService $documentService)
    {
        $this->documentService = $documentService;
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
        $prefix = new Text('prefix');
        $prefix->setAttributes(array(
            'id' => 'prefix',
            'class' => 'form-control input-sm',
        ));
        $prefix->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($prefix);

        $suffix = new Text('suffix');
        $suffix->setAttributes(array(
            'id' => 'suffix',
            'class' => 'form-control input-sm',
        ));
        $suffix->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($suffix);

        $dateFormat = new Select('dateFormat');
        $dateFormat->setAttributes(array(
            'id' => 'dateFormat',
            'class' => 'form-control input-sm'
        ));
        $dateFormat->setValueOptions($this->documentService->getDateFormatSelect());
        $dateFormat->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($dateFormat);

        $languageCode = new Select('languageCode');
        $languageCode->setAttributes(array(
            'id' => 'languageCode',
            'class' => 'form-control input-sm'
        ));
        $languageCode->setValueOptions($this->languageService->getLanguageSelect());
        $languageCode->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($languageCode);

        $nextNumber = new Text('nextNumber');
        $nextNumber->setAttributes(array(
            'id' => 'nextNumber',
            'class' => 'form-control input-sm',
        ));
        $nextNumber->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($nextNumber);

        return $this;
    }

    public function getInputFilter()
    {
        if ($this->filter == null) {
            $filter = new InputFilter();

            $prefix = new Input('prefix');
            $prefix->setRequired(false)->setAllowEmpty(true);
            $filter->add($prefix);

            $suffix = new Input('suffix');
            $suffix->setRequired(false)->setAllowEmpty(true);
            $filter->add($suffix);

            $dateFormat = new Input('dateFormat');
            $dateFormat->setRequired(false)->setAllowEmpty(true);
            $filter->add($dateFormat);

            $languageCode = new Input('languageCode');
            $languageCode->setRequired(false)->setAllowEmpty(true);
            $filter->add($languageCode);

            $nextNumberDigits = new Digits();
            $nextNumberDigits->setMessage($this->translator->translate('DocumentSetting.form.nextNumber.notDigits'), Digits::NOT_DIGITS);

            $nextNumber = new Input('nextNumber');
            $nextNumber->setRequired(false)->setAllowEmpty(true);
            $nextNumber->getValidatorChain()->attach($nextNumberDigits);
            $filter->add($nextNumber);

            $this->filter = $filter;
        }

        return $this->filter;
    }

    public function setFormValues(DocumentSetting $documentSetting){
        if($documentSetting){
            $this->get('prefix')->setValue($documentSetting->getPrefix());
            $this->get('suffix')->setValue($documentSetting->getSuffix());
            $this->get('dateFormat')->setValue($documentSetting->getDatePdfFormat());
            $this->get('languageCode')->setValue($documentSetting->getPdfLanguageCode());
            $this->get('nextNumber')->setValue($documentSetting->getNextNumber());
        }
    }

} 