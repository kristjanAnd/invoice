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
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('Company.form.name.placeholder')
        ));
        $name->setLabel($this->translator->translate('Company.form.name.label'));
        $name->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($name);

        $code = new Text('code');
        $code->setAttributes(array(
            'id' => 'code',
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('Company.form.code.placeholder')
        ));
        $code->setLabel($this->translator->translate('Company.form.code.label'));
        $code->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($code);

        $email = new Text('email');
        $email->setAttributes(array(
            'id' => 'email',
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('Company.form.email.placeholder')
        ));
        $email->setLabel($this->translator->translate('Company.form.email.label'));
        $email->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($email);

        $address = new Textarea('address');
        $address->setAttributes(array(
            'id' => 'address',
            'class' => 'form-control input-sm',
            'rows' => 3,
            'placeholder' => $this->translator->translate('Company.form.address.placeholder')
        ));
        $address->setLabel($this->translator->translate('Company.form.address.label'));
        $address->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($address);

        $regNo = new Text('regNo');
        $regNo->setAttributes(array(
            'id' => 'regNo',
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('Company.form.regNo.placeholder')
        ));
        $regNo->setLabel($this->translator->translate('Company.form.regNo.label'));
        $regNo->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($regNo);

        $vatNo = new Text('vatNo');
        $vatNo->setAttributes(array(
            'id' => 'vatNo',
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('Company.form.vatNo.placeholder')
        ));
        $vatNo->setLabel($this->translator->translate('Company.form.vatNo.label'));
        $vatNo->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($vatNo);

        $phone = new Text('phone');
        $phone->setAttributes(array(
            'id' => 'phone',
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('Company.form.phone.placeholder')
        ));
        $phone->setLabel($this->translator->translate('Company.form.phone.label'));
        $phone->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($phone);

        return $this;
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
        }
    }
} 