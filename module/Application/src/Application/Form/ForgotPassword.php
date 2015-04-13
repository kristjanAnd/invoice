<?php

namespace Application\Form;

use Application\Entity\User;
use Zend\Form\Form;
use Zend\Form\Element\Text;
use Zend\Form\Element\Submit;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use DoctrineModule\Validator\ObjectExists;
use Zend\Validator\EmailAddress;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Zend\Mvc\I18n\Translator;

class ForgotPassword extends Form implements ObjectManagerAwareInterface
{
	protected $objectManager;
	protected $validatorClass;
    /**
     * @var Translator
     */
    protected $translator;

    /**
     *
     * @param Translator $t
     */
    public function setTranslator(Translator $t)
    {
        $this->translator = $t;
    }

	public function __construct()
	{
		$this->validatorClass = User::getClass();
	}

	public function init()
	{
		parent::__construct('forgotPassword');

        $email = new Text('email');
        $email->setAttribute('id', 'forgotEmail');
        $email->setAttribute('required', 'required')
                ->setAttribute('class', 'form-control')
		        ->setAttribute('placeholder', $this->translator->translate('forgotPassword.email.placeholder'));
        $email->setLabel($this->translator->translate('register.email.label'));
        $email->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
		$this->add($email);
		
		$submit = new Submit('submit');
        $submit->setAttribute('id', 'sendPassword');
		$submit->setAttribute('class',  'btn btn-primary btn-sm pull-right');
		$submit->setValue($this->translator->translate('forgotPassword.submitButton.submitButton'));
		$this->add($submit);
		
		return $this;
	}

	public function getInputFilter()
	{
		if ($this->filter == null) {
			$this->filter = new InputFilter();
			$inputFilter = parent::getInputFilter();
			
			$email = new Input('email');
            $email->setRequired(true);
			$email->setAllowEmpty(false);
            $objectExists = new ObjectExists(array (
				'object_repository' => $this->objectManager->getRepository(User::getClass()),
				'fields' => 'email'
			));
			$objectExists->setMessage($this->translator->translate('forgotPassword.email.notExists'), ObjectExists::ERROR_NO_OBJECT_FOUND);
            $emailAddress = new EmailAddress();
            $emailAddress->setMessage($this->translator->translate('forgotPassword.email.invalidFormat'), $emailAddress::INVALID_FORMAT);
            $email->getValidatorChain()->attach($emailAddress, true)->attach($objectExists);
            $this->filter->add($email);
		}
		
		return $this->filter;
	}
	/* (non-PHPdoc)
     * @see \DoctrineModule\Persistence\ObjectManagerAwareInterface::setObjectManager()
     */
	public function setObjectManager(\Doctrine\Common\Persistence\ObjectManager $objectManager)
	{
		$this->objectManager = $objectManager;
		
		return $this;
	}
	
	/* (non-PHPdoc)
     * @see \DoctrineModule\Persistence\ObjectManagerAwareInterface::getObjectManager()
     */
	public function getObjectManager()
	{
		return $this->objectManager;
	}
}
