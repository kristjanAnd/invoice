<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\Element\Submit;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Form\Element\Password;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;
use Zend\Mvc\I18n\Translator;

class NewPassword extends Form
{
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

    public function init()
    {
        parent::__construct('newPassword');
        
        $password = new Password('password');
        $password->setAttributes(array(
            'id' => 'password',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('newPassword.new.placeholder')
        ));
        $password->setLabel($this->translator->translate('newPassword.new.label'));
        $password->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
        $this->add($password);

        $passwordRepeat = new Password('passwordRepeat');
        $passwordRepeat->setAttributes(array(
            'id' => 'passwordRepeat',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('newPassword.repeat.placeholder')
        ));
        $passwordRepeat->setLabel($this->translator->translate('newPassword.repeat.label'));
        $passwordRepeat->setLabelAttributes(array('class' => 'col-sm-3 control-label'));
        $this->add($passwordRepeat);
        
        $submit = new Submit('submit');
        $submit->setAttribute('class', 'btn btn-primary btn-sm pull-right');
        $submit->setValue($this->translator->translate('newPassword.submit.value'));
        $this->add($submit);
        
        return $this;
    }

    public function getInputFilter()
    {
        if ($this->filter == null) {
            $this->filter = new InputFilter();
            
            $password = new Input('password');
            $password->setRequired(true);
            $password->setAllowEmpty(false);
            $length = new StringLength();
            $length->setMax(20);
            $length->setMin(5);
            $length->setMessage($this->translator->translate('newPassword.password.incorrectLength.long'), StringLength::TOO_LONG);
            $length->setMessage($this->translator->translate('newPassword.password.incorrectLength.short'), StringLength::TOO_SHORT);
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
}

?>