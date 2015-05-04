<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 29.04.15
 * Time: 9:13
 */

namespace Application\Form;


use Application\Entity\Role;
use Application\Entity\Subject\Company;
use Application\Validator\NoObjectExists;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;
use Zend\Validator\NotEmpty;

class RoleForm extends Form  implements ObjectManagerAwareInterface{

    protected $translator;

    protected $objectManager;

    protected $role;

    protected $company;

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     *
     * @param Translator $t
     */
    public function setTranslator(Translator $t)
    {
        $this->translator = $t;
    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param Role $role
     */
    public function setRole(Role $role)
    {
        $this->role = $role;
        return $this;
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

    public function init()
    {

        $name = new Text('name');
        $name->setAttributes(array(
            'id' => 'name',
            'class' => 'form-control',
            'placeholder' => $this->translator->translate('role.form.name.placeholder')
        ));
        $name->setLabel($this->translator->translate('role.form.name.label'));
        $name->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $this->add($name);

        return $this;
    }

    public function getInputFilter()
    {
        if ($this->filter == null) {
            $this->filter = new InputFilter();

            $notEmpty = new NotEmpty();

            if($this->getRole()){
                $noObjectExists = new NoObjectExists(array (
                    'object_repository' => $this->objectManager->getRepository(Role::getClass()),
                    'fields' => 'roleId',
                    'exclude' => array (
                        'roleId' => $this->getRole()->getRoleId()
                    )

                ));
            } else {
                $noObjectExists = new NoObjectExists(array (
                    'object_repository' => $this->objectManager->getRepository(Role::getClass()),
                    'fields' => 'roleId'
                ));
            }
            $noObjectExists->setCompany($this->company);
            $noObjectExists->setMessage('Selline roll on juba olemas', NoObjectExists::ERROR_OBJECT_FOUND);
            $name = new Input('name');
            $name->getValidatorChain()->attach($notEmpty->setMessage(sprintf($this->translator->translate('Validator.message.notEmpty'), $this->translator->translate('RoleForm.message.nameInput')), NotEmpty::IS_EMPTY))->attach($noObjectExists);
            $this->filter->add($name);
        }

        return $this->filter;
    }

    public function setFormValues(Role $role = null){
        if($role){
            $this->get('name')->setValue($role->getName());
        }
    }
}