<?php

namespace Application\Form;


use Application\Entity\Subject\Company;
use Application\Entity\User;
use Application\Service\AdminService;
use Application\Service\LanguageService;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Password;
use Zend\Form\Element\Select;
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

    protected $company;

    protected $objectManager;

    protected $configOptions = array();

    /**
     * @var AdminService
     */
    protected $adminService;

    /**
     * @param AdminService $adminService
     */
    public function setAdminService(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * @param mixed $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param LanguageService $languageService
     */
    public function setLanguageService(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * @param array $configOptions
     */
    public function setConfigOptions(array $configOptions)
    {
        $this->configOptions = $configOptions;
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

    public function addRole(){
        return isset($this->configOptions['addRole']) && $this->configOptions['addRole'] == true ? true : false;
    }

    public function addStatus(){
        return isset($this->configOptions['addStatus']) && $this->configOptions['addStatus'] == true ? true : false;
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
                'class' => 'form-control input-sm',
                'placeholder' => $this->translator->translate('register.firstName.placeholder')
            ));
            $firstName->setLabel($this->translator->translate('register.firstName.label'));
            $firstName->setLabelAttributes(array('class' => 'col-sm-3 control-label input-sm'));
            $this->add($firstName);
        }

        if($this->addLastName()){
            $lastName = new Text('lastName');
            $lastName->setAttributes(array(
                'id' => 'lastName',
                'class' => 'form-control input-sm',
                'placeholder' => $this->translator->translate('register.lastName.placeholder')
            ));
            $lastName->setLabel($this->translator->translate('register.lastName.label'));
            $lastName->setLabelAttributes(array('class' => 'col-sm-3 control-label input-sm'));
            $this->add($lastName);
        }

        if($this->addPhone()){
            $phone = new Text('phone');
            $phone->setAttributes(array(
                'id' => 'phone',
                'class' => 'form-control input-sm',
                'placeholder' => $this->translator->translate('register.phone.placeholder')
            ));
            $phone->setLabel($this->translator->translate('register.phone.label'));
            $phone->setLabelAttributes(array('class' => 'col-sm-3 control-label input-sm'));
            $this->add($phone);
        }

        if($this->addPersonalCode()){
            $personalCode = new Text('personalCode');
            $personalCode->setAttributes(array(
                'id' => 'personalCode',
                'class' => 'form-control input-sm',
                'placeholder' => $this->translator->translate('register.personalCode.placeholder')
            ));
            $personalCode->setLabel($this->translator->translate('register.personalCode.label'));
            $personalCode->setLabelAttributes(array('class' => 'col-sm-3 control-label input-sm'));
            $this->add($personalCode);
        }

        $email = new Text('email');
        $email->setAttributes(array(
            'id' => 'email',
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('register.email.placeholder')
        ));
        $email->setLabel($this->translator->translate('register.email.label'));
        $email->setLabelAttributes(array('class' => 'col-sm-3 control-label input-sm'));
        $this->add($email);

        $language = new Select('language');
        $language->setAttributes(array(
            'id' => 'language',
            'class' => 'form-control input-sm'
        ));
        $language->setValueOptions($this->languageService->getLanguageSelect());
        $language->setLabel($this->translator->translate('register.language.label'));
        $language->setLabelAttributes(array('class' => 'col-sm-3 control-label input-sm'));
        $this->add($language);


        if($this->addRole() && !$this->isMaster()){
            $role = new Select('role');
            $role->setAttributes(array(
                'id' => 'role',
                'class' => 'form-control input-sm'
            ));
            $role->setValueOptions($this->adminService->getRoleSelectForCompany($this->company));
            $role->setLabel($this->translator->translate('register.role.label'));
            $role->setLabelAttributes(array('class' => 'col-sm-3 control-label input-sm'));
            $this->add($role);
        }

        if($this->addStatus() && !$this->isMaster()){
            $status = new Select('status');
            $status->setAttributes(array(
                'id' => 'status',
                'class' => 'form-control input-sm'
            ));
            $status->setValueOptions($this->adminService->getUserStatusSelect());
            $status->setLabel($this->translator->translate('register.status.label'));
            $status->setLabelAttributes(array('class' => 'col-sm-3 control-label input-sm'));
            $this->add($status);
        }

        $password = new Password('password');
        $password->setAttribute('id', 'password');
        $password->setAttribute('placeholder', $this->translator->translate('register.password.placeholder'));
        $password->setAttribute('class', 'form-control input-sm');
        $password->setLabel($this->translator->translate('register.password.label'));
        $password->setLabelAttributes(array('class' => 'col-sm-3 control-label input-sm'));
        $this->add($password);


        $passwordRepeat = new Password('passwordRepeat');
        $passwordRepeat->setAttribute('id', 'passwordRepeat');
        $passwordRepeat->setAttribute('placeholder', $this->translator->translate('register.passwordRepeat.placeholder'));
        $passwordRepeat->setAttribute('class', 'form-control input-sm');
        $passwordRepeat->setLabel($this->translator->translate('register.passwordRepeat.label'));
        $passwordRepeat->setLabelAttributes(array('class' => 'col-sm-3 control-label input-sm'));
        $this->add($passwordRepeat);

        $submit = new Submit('submit');
        $submit->setAttribute('class', 'btn btn-primary btn-sm pull-right');
        $submit->setValue($this->translator->translate('register.submit.value'));
        $this->add($submit);

        return $this;
    }

    public function removePasswordValidation(){
        $this->getInputFilter()->get('password')->setRequired(false)->setAllowEmpty(true);
        $this->getInputFilter()->get('passwordRepeat')->setRequired(false)->setAllowEmpty(true);
    }

    public function getInputFilter()
    {
        if ($this->filter == null) {
            $this->filter = new InputFilter();

            $notEmpty = new NotEmpty();
            $notEmpty1 = new NotEmpty();
            $notEmpty2 = new NotEmpty();
            $notEmpty3 = new NotEmpty();
            $notEmpty4 = new NotEmpty();
            $notEmpty5 = new NotEmpty();
            $notEmpty6 = new NotEmpty();
            $notEmpty7 = new NotEmpty();
            $notEmpty8 = new NotEmpty();
            $notEmpty9 = new NotEmpty();

            if($this->addFirstName()){
                $firstName = new Input('firstName');
                $firstName->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('register.message.firstNameInput')), NotEmpty::IS_EMPTY));
                $this->filter->add($firstName);
            }

            if($this->addLastName()){
                $lastName = new Input('lastName');
                $lastName->getValidatorChain()->attach($notEmpty1->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('register.message.lastNameInput')), NotEmpty::IS_EMPTY));
                $this->filter->add($lastName);
            }

            if($this->addPhone()){
                $phone = new Input('phone');
                $phone->getValidatorChain()->attach($notEmpty2->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('register.message.phoneInput')), NotEmpty::IS_EMPTY));
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
                $personalCode->getValidatorChain()->attach($digits, true)->attach($length, true)->attach($noObjectExists)
                    ->attach($notEmpty3->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('register.message.personalCodeInput')), NotEmpty::IS_EMPTY));
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
                ->attach($noObjectExists, true)
                ->attach($notEmpty4->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('register.message.emailInput')), NotEmpty::IS_EMPTY));
            $this->filter->add($email);

            $language = new Input('language');
            $language->getValidatorChain()->attach($notEmpty5->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('register.message.languageInput')), NotEmpty::IS_EMPTY));
            $this->filter->add($language);

            if($this->addRole() && !$this->isMaster()){
                $role = new Input('role');
                $role->getValidatorChain()->attach($notEmpty6->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('register.message.roleInput')), NotEmpty::IS_EMPTY));
                $this->filter->add($role);
            }

            if($this->addStatus() && !$this->isMaster()){
                $status = new Input('status');
                $status->getValidatorChain()->attach($notEmpty9->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('register.message.statusInput')), NotEmpty::IS_EMPTY));
                $this->filter->add($status);
            }

            $password = new Input('password');
            $password->setRequired(true);
            $password->setAllowEmpty(false);
            $length = new StringLength();
            $length->setMax(20);
            $length->setMin(5);
            $length->setMessage($this->translator->translate('register.password.incorrectLength.long'), StringLength::TOO_LONG);
            $length->setMessage($this->translator->translate('register.password.incorrectLength.short'), StringLength::TOO_SHORT);
            $password->getValidatorChain()->attach($length)
                ->attach($notEmpty7->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('register.message.passwordInput')), NotEmpty::IS_EMPTY));
            $this->filter->add($password);

            $identical = new Identical('password');
            $identical->setMessage($this->translator->translate('register.passwordRepeat.notSame'), Identical::NOT_SAME);

            $passowordRepeat = new Input('passwordRepeat');
            $passowordRepeat->setRequired(true);
            $passowordRepeat->setAllowEmpty(false);
            $passowordRepeat->getValidatorChain()->attach($identical)
                ->attach($notEmpty8->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('register.message.passwordRepeatInput')), NotEmpty::IS_EMPTY));
            $this->filter->add($passowordRepeat);

        }

        return $this->filter;
    }

    public function populateWithValues(){
        if($this->user){
            if($this->addFirstName()){
                $this->get('firstName')->setValue($this->user->getFirstName());
            }

            if($this->addLastName()){
                $this->get('lastName')->setValue($this->user->getLastName());
            }

            if($this->addPhone()){
                $this->get('phone')->setValue($this->user->getPhone());
            }

            if($this->addPersonalCode()){
                $this->get('personalCode')->setValue($this->user->getPersonalCode());
            }

            $this->get('email')->setValue($this->user->getEmail());
            $this->get('language')->setValue($this->user->getLanguageCode());

            if($this->addRole() && !$this->isMaster()){
                $this->get('role')->setValue($this->user->getRoles()->last()->getId());
            }

            if($this->addStatus() && !$this->isMaster()){
                $this->get('status')->setValue($this->user->getStatus());
            }
        }
    }

    private function isMaster(){
        return $this->user && $this->user->isMaster() ? true : false;
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