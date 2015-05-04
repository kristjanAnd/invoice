<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 23.04.15
 * Time: 16:56
 */

namespace Application\Form;

use Application\Entity\Subject;
use Application\Entity\Subject\Company;
use Application\Entity\User;
use Application\Service\ClientService;
use Application\Validator\MoneyValidator;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;
use Zend\Validator\Digits;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class SubjectForm extends Form {
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var Subject\Client
     */
    protected $client;

    protected $isClient = false;

    /**
     * @param $isClient
     * @return $this
     */
    public function setIsClient($isClient)
    {
        $this->isClient = $isClient;
        return $this;
    }

    /**
     * @var Company
     */
    protected $company;

    /**
     * @param Company $company
     * @return $this
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @var ClientService
     */
    protected $clientService;

    /**
     * @param ClientService $clientService
     */
    public function setClientService(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function setClient(Subject\Client $client)
    {
        $this->client = $client;
        $this->isClient = true;
        return $this;
    }

    public function getCompanyUsersSelect(){
        $result = array();
        if($this->company){
            foreach($this->company->getUsers() as $user){
                if($user->getStatus() !== User::STATUS_ACTIVE){
                    continue;
                }
                $result[$user->getId()] = $user->getFullName();
            }
        }
        return $result;
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
            'placeholder' => $this->translator->translate('Company.form.name.placeholder')
        ));
        $name->setLabel($this->translator->translate('Company.form.name.label'));
        $name->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($name);

        $code = new Text('code');
        $code->setAttributes(array(
            'id' => 'code',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Company.form.code.placeholder')
        ));
        $code->setLabel($this->translator->translate('Company.form.code.label'));
        $code->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($code);

        $email = new Text('email');
        $email->setAttributes(array(
            'id' => 'email',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Company.form.email.placeholder')
        ));
        $email->setLabel($this->translator->translate('Company.form.email.label'));
        $email->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($email);

        $address = new Textarea('address');
        $address->setAttributes(array(
            'id' => 'address',
            'class' => 'form-control',
            'rows' => 3,
            'placeholder' => $this->translator->translate('Company.form.address.placeholder')
        ));
        $address->setLabel($this->translator->translate('Company.form.address.label'));
        $address->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($address);

        $regNo = new Text('regNo');
        $regNo->setAttributes(array(
            'id' => 'regNo',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Company.form.regNo.placeholder')
        ));
        $regNo->setLabel($this->translator->translate('Company.form.regNo.label'));
        $regNo->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($regNo);

        $vatNo = new Text('vatNo');
        $vatNo->setAttributes(array(
            'id' => 'vatNo',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Company.form.vatNo.placeholder')
        ));
        $vatNo->setLabel($this->translator->translate('Company.form.vatNo.label'));
        $vatNo->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($vatNo);

        $phone = new Text('phone');
        $phone->setAttributes(array(
            'id' => 'phone',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('Company.form.phone.placeholder')
        ));
        $phone->setLabel($this->translator->translate('Company.form.phone.label'));
        $phone->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($phone);

        if($this->isClient){
            $status = new Select('status');
            $status->setAttributes(array(
                'id' => 'status',
                'class' => 'form-control'
            ));
            $status->setValueOptions($this->clientService->getClientStatusSelect());
            $status->setLabel($this->translator->translate('Company.form.status.label'));
            $status->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
            $this->add($status);

            $clientUser = new Select('clientUser');
            $clientUser->setAttributes(array(
                'id' => 'clientUser',
                'class' => 'form-control'
            ));
            $clientUser->setValueOptions($this->getCompanyUsersSelect());
            $clientUser->setLabel($this->translator->translate('Company.form.clientUser.label'));
            $clientUser->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
            $this->add($clientUser);

            $delayPercent = new Text('delayPercent');
            $delayPercent->setAttributes(array(
                'id' => 'delayPercent',
                'class' => 'form-control',
                'placeholder' => $this->translator->translate('Company.form.delayPercent.placeholder')
            ));
            $delayPercent->setLabel($this->translator->translate('Company.form.delayPercent.label'));
            $delayPercent->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
            $this->add($delayPercent);

            $deadlineDays = new Text('deadlineDays');
            $deadlineDays->setAttributes(array(
                'id' => 'deadlineDays',
                'class' => 'form-control',
                'placeholder' => $this->translator->translate('Company.form.deadlineDays.placeholder')
            ));
            $deadlineDays->setLabel($this->translator->translate('Company.form.deadlineDays.label'));
            $deadlineDays->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
            $this->add($deadlineDays);

            $referenceNumber = new Text('referenceNumber');
            $referenceNumber->setAttributes(array(
                'id' => 'referenceNumber',
                'class' => 'form-control',
                'placeholder' => $this->translator->translate('Company.form.referenceNumber.placeholder')
            ));
            $referenceNumber->setLabel($this->translator->translate('Company.form.referenceNumber.label'));
            $referenceNumber->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
            $this->add($referenceNumber);
        }

        return $this;
    }

    public function setDefaultClientUser(User $user){
        $this->get('clientUser')->setValue($user->getId());
    }

    public function getInputFilter() {
        if ($this->filter == null) {
            $filter = new InputFilter();
            $notEmpty = new NotEmpty();

            $name = new Input('name');
            $name->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('CompanyForm.message.nameInput')), NotEmpty::IS_EMPTY));
            $filter->add($name);

            $code = new Input('code');
            $code->setRequired(false)->setAllowEmpty(true);
            $filter->add($code);

            $email = new Input('email');
            $email->setRequired(false)->setAllowEmpty(true);
            $filter->add($email);

            $address = new Input('address');
            $address->setRequired(false)->setAllowEmpty(true);
            $filter->add($address);

            $regNo = new Input('regNo');
            $regNo->setRequired(false)->setAllowEmpty(true);
            $filter->add($regNo);

            $vatNo = new Input('vatNo');
            $vatNo->setRequired(false)->setAllowEmpty(true);
            $filter->add($vatNo);

            $phone = new Input('phone');
            $phone->setRequired(false)->setAllowEmpty(true);
            $filter->add($phone);

            if($this->isClient){
                $notEmpty1 = new NotEmpty();
                $notEmpty2 = new NotEmpty();

                $status = new Input('status');
                $status->getValidatorChain()->attach($notEmpty1->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('CompanyForm.message.statusInput')), NotEmpty::IS_EMPTY));
                $filter->add($status);

                $clientUser = new Input('clientUser');
                $clientUser->getValidatorChain()->attach($notEmpty2->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('CompanyForm.message.clientUserInput')), NotEmpty::IS_EMPTY));
                $filter->add($clientUser);

                $floatValidator = new MoneyValidator();
                $floatValidator->setMessage($this->translator->translate('subject.form.delayPercent.notDigits'), MoneyValidator::NOT_FLOAT);

                $deadlineDayDigits = new Digits();
                $deadlineDayDigits->setMessage($this->translator->translate('subject.form.deadlineDays.notDigits'), Digits::NOT_DIGITS);

                $delayPercent = new Input('delayPercent');
                $delayPercent->setRequired(false)->setAllowEmpty(true);
                $delayPercent->getValidatorChain()->attach($floatValidator);
                $filter->add($delayPercent);

                $deadlineDays = new Input('deadlineDays');
                $deadlineDays->setRequired(false)->setAllowEmpty(true);
                $deadlineDays->getValidatorChain()->attach($deadlineDayDigits);
                $filter->add($deadlineDays);

                $referenceNumber = new Input('referenceNumber');
                $referenceNumber->setRequired(false)->setAllowEmpty(true);
                $filter->add($referenceNumber);
            }

            $this->filter = $filter;
        }

        return $this->filter;
    }

    public function setFormValues(Subject $subject){
        if($subject){
            $this->get('name')->setValue($subject->getName());
            $this->get('code')->setValue($subject->getCode());
            $this->get('email')->setValue($subject->getEmail());
            $this->get('address')->setValue($subject->getAddress());
            $this->get('regNo')->setValue($subject->getRegistrationNumber());
            $this->get('vatNo')->setValue($subject->getVatNumber());
            $this->get('phone')->setValue($subject->getPhone());
            if($subject instanceof Subject\Client){
                $this->get('status')->setValue($subject->getStatus());
                $this->get('delayPercent')->setValue($subject->getDelayPercent());
                $this->get('deadlineDays')->setValue($subject->getDeadlineDays());
                $this->get('referenceNumber')->setValue($subject->getReferenceNumber());
                if($subject->getClientUser()){
                    $this->get('clientUser')->setValue($subject->getClientUser()->getId());
                }
            }
        }
    }
} 