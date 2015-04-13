<?php

namespace Application\Form;


use Application\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Element\Password;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Digits;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;
use Application\Validator\NoObjectExists;
use Zend\Validator\NotEmpty;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;

class RegisterForm extends Form  implements ObjectManagerAwareInterface{

    /**
     * @var Translator
     */
    protected $translator;

    protected $user;

    protected $objectManager;

    protected $configOptions = array();

    /**
     * @param mixed $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function __construct(array $configOptions = null){
        if(is_array($configOptions)){
            $this->configOptions = $configOptions;
        }

        parent::__construct('registerForm');
    }

    public function addPersonalCode(){
        return isset($this->configOptions['addPersonalCode']) && $this->configOptions['addPersonalCode'] == true ? true : false;
    }

    public function addPhone(){
        return isset($this->configOptions['addPhone']) && $this->configOptions['addPhone'] == true ? true : false;
    }

    public function addFirstName(){
        return isset($this->configOptions['addFirstName']) && $this->configOptions['addFirstName'] == true ? true : false;
    }

    public function addLastName(){
        return isset($this->configOptions['addLastName']) && $this->configOptions['addLastName'] == true ? true : false;
    }

    /**
     *
     * @param Translator $t
     */
    public function setTranslator(Translator $t)
    {
        $this->translator = $t;
    }

    public function init()
    {

        if($this->addFirstName()){
            $firstName = new Text('firstName');
            $firstName->setAttributes(array(
                'id' => 'firstName',
                'class' => 'form-control',
                'placeholder' => $this->translator->translate('register.firstName.placeholder')
            ));
            $firstName->setLabel($this->translator->translate('register.firstName.label'));
            $firstName->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
            $this->add($firstName);
        }

        if($this->addLastName()){
            $lastName = new Text('lastName');
            $lastName->setAttributes(array(
                'id' => 'lastName',
                'class' => 'form-control',
                'placeholder' => $this->translator->translate('register.lastName.placeholder')
            ));
            $lastName->setLabel($this->translator->translate('register.lastName.label'));
            $lastName->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
            $this->add($lastName);
        }

        if($this->addPhone()){
            $phone = new Text('phone');
            $phone->setAttributes(array(
                'id' => 'phone',
                'class' => 'form-control',
                'placeholder' => $this->translator->translate('register.phone.placeholder')
            ));
            $phone->setLabel($this->translator->translate('register.phone.label'));
            $phone->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
            $this->add($phone);
        }

        if($this->addPersonalCode()){
            $personalCode = new Text('personalCode');
            $personalCode->setAttributes(array(
                'id' => 'personalCode',
                'class' => 'form-control',
                'placeholder' => $this->translator->translate('register.personalCode.placeholder')
            ));
            $personalCode->setLabel($this->translator->translate('register.personalCode.label'));
            $personalCode->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
            $this->add($personalCode);
        }

        $email = new Text('email');
        $email->setAttributes(array(
            'id' => 'email',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('register.email.placeholder')
        ));
        $email->setLabel($this->translator->translate('register.email.label'));
        $email->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
        $this->add($email);

        $password = new Password('password');
        $password->setAttribute('id', 'password');
        $password->setAttribute('placeholder', $this->translator->translate('register.password.placeholder'));
        $password->setAttribute('class', 'form-control');
        $password->setLabel($this->translator->translate('register.password.label'));
        $password->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
        $this->add($password);

        $passwordRepeat = new Password('passwordRepeat');
        $passwordRepeat->setAttribute('id', 'passwordRepeat');
        $passwordRepeat->setAttribute('placeholder', $this->translator->translate('register.passwordRepeat.placeholder'));
        $passwordRepeat->setAttribute('class', 'form-control');
        $passwordRepeat->setLabel($this->translator->translate('register.passwordRepeat.label'));
        $passwordRepeat->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
        $this->add($passwordRepeat);

        $submit = new Submit('submit');
        $submit->setAttribute('class', 'btn btn-primary btn-sm pull-right');
        $submit->setValue($this->translator->translate('register.submit.value'));
        $this->add($submit);

        return $this;
    }

    public function getInputFilter()
    {
        if ($this->filter == null) {
            $this->filter = new InputFilter();


            if($this->addFirstName()){
                $firstName = new Input('firstName');
                $this->filter->add($firstName);
            }

            if($this->addLastName()){
                $lastName = new Input('lastName');
                $this->filter->add($lastName);
            }

            if($this->addPhone()){
                $phone = new Input('phone');
                $this->filter->add($phone);
            }

            if($this->addPersonalCode()){
                if($this->user){
                    $noObjectExists = new NoObjectExists(array (
                        'object_repository' => $this->objectManager->getRepository(User::getClass()),
                        'fields' => 'personalCode',
                        'exclude' => array (
                            'personalCode' => $this->user->getPersonalCode()
                        )

                    ));
                } else {
                    $noObjectExists = new NoObjectExists(array (
                        'object_repository' => $this->objectManager->getRepository(User::getClass()),
                        'fields' => 'personalCode'
                    ));
                }
                $personalCode = new Input('personalCode');
                $personalCode->setRequired(false);
                $personalCode->setAllowEmpty(true);
                $digits = new Digits();
                $digits->setMessage($this->translator->translate('register.personalCode.notDigits'), Digits::NOT_DIGITS);
                $length = new StringLength();
                $length->setMax(11);
                $length->setMin(11);
                $length->setMessage($this->translator->translate('register.personalCode.incorrectLength.Toolong'), StringLength::TOO_LONG);
                $length->setMessage($this->translator->translate('register.personalCode.incorrectLength.TooShort'), StringLength::TOO_SHORT);
                $personalCode->getValidatorChain()->attach($digits, true)->attach($length, true)->attach($noObjectExists);
                $this->filter->add($personalCode);
            }


            $email = new Input('email');
            if($this->user){
                $noObjectExists = new NoObjectExists(array (
                    'object_repository' => $this->objectManager->getRepository(User::getClass()),
                    'fields' => 'email',
                    'exclude' => array (
                        'email' => $this->user->getEmail()
                    )

                ));
            } else {
                $noObjectExists = new NoObjectExists(array (
                    'object_repository' => $this->objectManager->getRepository(User::getClass()),
                    'fields' => 'email'
                ));
            }
            $noObjectExists->setMessage($this->translator->translate('register.message.emailExists'), NoObjectExists::ERROR_OBJECT_FOUND);
            $emailAddress = new EmailAddress();
            $emailAddress->setMessage($this->translator->translate('register.message.emailInvalidFormat'), $emailAddress::INVALID_FORMAT);
            $email->getValidatorChain()
                ->attach($emailAddress, true)
                ->attach($noObjectExists, true);
            $this->filter->add($email);

            $password = new Input('password');
            $password->setRequired(true);
            $password->setAllowEmpty(false);
            $length = new StringLength();
            $length->setMax(20);
            $length->setMin(5);
            $length->setMessage($this->translator->translate('register.password.incorrectLength.long'), StringLength::TOO_LONG);
            $length->setMessage($this->translator->translate('register.password.incorrectLength.short'), StringLength::TOO_SHORT);
            $password->getValidatorChain()->attach($length);
            $this->filter->add($password);

            $passowordRepeat = new Input('passwordRepeat');
            $passowordRepeat->setRequired(true);
            $passowordRepeat->setAllowEmpty(false);
            $passowordRepeat->getValidatorChain()->attach(new Identical('password'));
            $this->filter->add($passowordRepeat);
        }

        return $this->filter;
    }

    /**
     * Set the object manager
     *
     * @param ObjectManager $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get the object manager
     *
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }
}