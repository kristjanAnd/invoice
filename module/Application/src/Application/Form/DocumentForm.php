<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 17:03
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
use Zend\I18n\Validator\DateTime;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;
use Zend\Form\Form;
use Zend\Validator\NotEmpty;

class DocumentForm extends Form
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

    protected $dateFormat;

    protected $languageCode;

    /**
     * @return mixed
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * @param $languageCode
     * @return $this
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;
        return $this;
    }

    /**
     * @param $dateFormat
     * @return $this
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
        return  $this;
    }

    /**
     * @return mixed
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

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

    public function getCompanyUsersSelect()
    {
        $result = array();
        if ($this->company) {
            foreach ($this->company->getUsers() as $user) {
                if ($user->getStatus() !== User::STATUS_ACTIVE) {
                    continue;
                }
                $result[$user->getId()] = $user->getFullName();
            }
        }
        return $result;
    }

    public function init()
    {
        $documentDate = new Text('documentDate');
        $documentDate->setAttributes(array(
            'id' => 'documentDate',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Document.form.documentDate.placeholder')
        ));
        $documentDate->setLabel($this->translator->translate('Document.form.documentDate.label'));
        $documentDate->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($documentDate);

        $user = new Select('user');
        $user->setAttributes(array(
            'id' => 'user',
            'class' => 'form-control'
        ));
        $user->setValueOptions($this->getCompanyUsersSelect());
        $user->setLabel($this->translator->translate('Document.form.user.label'));
        $user->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($user);

        $dateFormat = new Select('dateFormat');
        $dateFormat->setAttributes(array(
            'id' => 'dateFormat',
            'class' => 'form-control'
        ));
        $dateFormat->setValueOptions($this->documentService->getDateFormatSelect());
        $dateFormat->setLabel($this->translator->translate('Document.form.dateFormat.label'));
        $dateFormat->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($dateFormat);

        $language = new Select('language');
        $language->setAttributes(array(
            'id' => 'language',
            'class' => 'form-control'
        ));
        $language->setValueOptions($this->languageService->getLanguageSelect());
        $language->setLabel($this->translator->translate('Document.form.language.label'));
        $language->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($language);

        $documentNumber = new Text('documentNumber');
        $documentNumber->setAttributes(array(
            'id' => 'documentNumber',
            'readonly' => 'readonly',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Document.form.documentNumber.placeholder')
        ));
        $documentNumber->setLabel($this->translator->translate('Document.form.documentNumber.label'));
        $documentNumber->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($documentNumber);

        $amount = new Text('amount');
        $amount->setAttributes(array(
            'id' => 'amount',
            'readonly' => 'readonly',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Document.form.amount.placeholder')
        ));
        $amount->setLabel($this->translator->translate('Document.form.amount.label'));
        $amount->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($amount);

        $vatAmount = new Text('vatAmount');
        $vatAmount->setAttributes(array(
            'id' => 'vatAmount',
            'readonly' => 'readonly',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Document.form.vatAmount.placeholder')
        ));
        $vatAmount->setLabel($this->translator->translate('Document.form.vatAmount.label'));
        $vatAmount->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($vatAmount);

        $amountVat = new Text('amountVat');
        $amountVat->setAttributes(array(
            'id' => 'amountVat',
            'readonly' => 'readonly',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Document.form.amountVat.placeholder')
        ));
        $amountVat->setLabel($this->translator->translate('Document.form.amountVat.label'));
        $amountVat->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($amountVat);

        $subjectName = new Text('subjectName');
        $subjectName->setAttributes(array(
            'id' => 'subjectName',
            'class' => 'form-control',
        ));
        $subjectName->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($subjectName);

        $subjectEmail = new Text('subjectEmail');
        $subjectEmail->setAttributes(array(
            'id' => 'subjectEmail',
            'class' => 'form-control',
        ));
        $subjectEmail->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($subjectEmail);

        $subjectAddress = new Textarea('subjectAddress');
        $subjectAddress->setAttributes(array(
            'id' => 'subjectAddress',
            'rows' => 3,
            'class' => 'form-control',
        ));
        $subjectAddress->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($subjectAddress);

        $subjectRegNo = new Text('subjectRegNo');
        $subjectRegNo->setAttributes(array(
            'id' => 'subjectRegNo',
            'class' => 'form-control',
        ));
        $subjectRegNo->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($subjectRegNo);

        $subjectVatNo = new Text('subjectVatNo');
        $subjectVatNo->setAttributes(array(
            'id' => 'subjectVatNo',
            'class' => 'form-control',
        ));
        $subjectVatNo->setLabelAttributes(array('class' => 'col-sm-5 control-label'));
        $this->add($subjectVatNo);


        return $this;
    }

    public function setDefaultUser(User $user)
    {
        $this->get('user')->setValue($user->getId());
    }

    public function setDefaultData(DocumentSetting $documentSetting)
    {
        $date = new \DateTime();
        $datePdfFormat = $documentSetting->getDatePdfFormat() ? $documentSetting->getDatePdfFormat() : $this->getDateFormat();
        $pdfLanguageCode = $documentSetting->getPdfLanguageCode() ? $documentSetting->getPdfLanguageCode() : $this->getLanguageCode();
        $this->get('dateFormat')->setValue($datePdfFormat);
        $this->get('language')->setValue($pdfLanguageCode);
        $this->get('documentDate')->setValue($date->format($this->getDateFormat()));
    }

    public function getInputFilter()
    {
        if ($this->filter == null) {
            $filter = new InputFilter();
            $notEmpty = new NotEmpty();
            $notEmpty1 = new NotEmpty();
            $notEmpty2 = new NotEmpty();
            $notEmpty3 = new NotEmpty();
            $notEmpty4 = new NotEmpty();
            $notEmpty5 = new NotEmpty();

            $documentDate = new Input('documentDate');
            $documentDate->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('DocumentForm.message.documentDateInput')), NotEmpty::IS_EMPTY));
            $filter->add($documentDate);

            $user = new Input('user');
            $user->getValidatorChain()->attach($notEmpty1->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('DocumentForm.message.userInput')), NotEmpty::IS_EMPTY));
            $filter->add($user);

            $documentNumber = new Input('documentNumber');
            $documentNumber->setRequired(false)->setAllowEmpty(true);
            $filter->add($documentNumber);

            $amountValidator = new MoneyValidator();
            $amountValidator->setMessage($this->translator->translate('Document.form.amount.notDigits'), MoneyValidator::NOT_FLOAT);

            $vatAmountValidator = new MoneyValidator();
            $vatAmountValidator->setMessage($this->translator->translate('Document.form.vatAmount.notDigits'), MoneyValidator::NOT_FLOAT);

            $amountVatValidator = new MoneyValidator();
            $amountVatValidator->setMessage($this->translator->translate('Document.form.amountVat.notDigits'), MoneyValidator::NOT_FLOAT);

            $amount = new Input('amount');
            $amount->getValidatorChain()->attach($amountValidator)
                ->attach($notEmpty2->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('DocumentForm.message.amountInput')), NotEmpty::IS_EMPTY));
            $filter->add($amount);

            $vatAmount = new Input('vatAmount');
            $vatAmount->getValidatorChain()->attach($vatAmountValidator);
            $filter->add($vatAmount);

            $amountVat = new Input('amountVat');
            $amountVat->getValidatorChain()->attach($amountVatValidator)
                ->attach($notEmpty3->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('DocumentForm.message.amountVatInput')), NotEmpty::IS_EMPTY));
            $filter->add($amountVat);

            $dateFormat = new Input('dateFormat');
            $dateFormat->getValidatorChain()->attach($notEmpty4->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('DocumentForm.message.dateFormatInput')), NotEmpty::IS_EMPTY));
            $filter->add($dateFormat);

            $language = new Input('language');
            $language->getValidatorChain()->attach($notEmpty5->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('DocumentForm.message.languageInput')), NotEmpty::IS_EMPTY));
            $filter->add($language);

            $subjectName = new Input('subjectName');
            $filter->add($subjectName);

            $subjectEmail = new Input('subjectEmail');
            $subjectEmail->setRequired(false)->setAllowEmpty(true);
            $filter->add($subjectEmail);

            $subjectAddress = new Input('subjectAddress');
            $subjectAddress->setRequired(false)->setAllowEmpty(true);
            $filter->add($subjectAddress);

            $subjectRegNo = new Input('subjectRegNo');
            $subjectRegNo->setRequired(false)->setAllowEmpty(true);
            $filter->add($subjectRegNo);

            $subjectVatNo = new Input('subjectVatNo');
            $subjectVatNo->setRequired(false)->setAllowEmpty(true);
            $filter->add($subjectVatNo);

            $this->filter = $filter;
        }

        return $this->filter;
    }

    public function setFormValues(Document $document)
    {
        if ($document) {
            if($document->getDocumentDate()){
                $this->get('documentDate')->setValue($document->getDocumentDate()->format($document->getDateFormat()));
            }
            if($document->getUser()){
                $this->get('user')->setValue($document->getUser());
            }
            $this->get('documentNumber')->setValue($document->getDocumentNumber());
            $this->get('amount')->setValue($document->getAmount());
            $this->get('vatAmount')->setValue($document->getVatAmount());
            $this->get('amountVat')->setValue($document->getAmountVat());
            $this->get('dateFormat')->setValue($document->getDateFormat());
            $this->get('language')->setValue($document->getLanguageCode());

            $this->get('subjectName')->setValue($document->getSubjectName());
            $this->get('subjectEmail')->setValue($document->getSubjectEmail());
            $this->get('subjectAddress')->setValue($document->getSubjectAddress());
            $this->get('subjectRegNo')->setValue($document->getSubjectRegNo());
            $this->get('subjectVatNo')->setValue($document->getSubjectVatNo());

        }
    }

}