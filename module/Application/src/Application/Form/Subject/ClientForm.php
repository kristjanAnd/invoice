<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 11.05.15
 * Time: 15:37
 */

namespace Application\Form\Subject;


use Application\Entity\Subject\Client;
use Application\Entity\User;
use Application\Form\SubjectForm;
use Application\Service\ClientService;
use Application\Validator\MoneyValidator;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\InputFilter\Input;
use Zend\Validator\Digits;
use Zend\Validator\NotEmpty;

class ClientForm extends SubjectForm {

    /**
     * @var Client
     */
    protected $client;

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

    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    public function init(){
        parent::init();

        $status = new Select('status');
        $status->setAttributes(array(
            'id' => 'status',
            'class' => 'form-control input-sm'
        ));
        $status->setValueOptions($this->clientService->getClientStatusSelect());
        $status->setLabel($this->translator->translate('Company.form.status.label'));
        $status->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($status);

        $clientUser = new Select('clientUser');
        $clientUser->setAttributes(array(
            'id' => 'clientUser',
            'class' => 'form-control input-sm'
        ));
        $clientUser->setValueOptions($this->getCompanyUsersSelect());
        $clientUser->setLabel($this->translator->translate('Company.form.clientUser.label'));
        $clientUser->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($clientUser);

        $delayPercent = new Text('delayPercent');
        $delayPercent->setAttributes(array(
            'id' => 'delayPercent',
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('Company.form.delayPercent.placeholder')
        ));
        $delayPercent->setLabel($this->translator->translate('Company.form.delayPercent.label'));
        $delayPercent->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($delayPercent);

        $deadlineDays = new Text('deadlineDays');
        $deadlineDays->setAttributes(array(
            'id' => 'deadlineDays',
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('Company.form.deadlineDays.placeholder')
        ));
        $deadlineDays->setLabel($this->translator->translate('Company.form.deadlineDays.label'));
        $deadlineDays->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($deadlineDays);

        $referenceNumber = new Text('referenceNumber');
        $referenceNumber->setAttributes(array(
            'id' => 'referenceNumber',
            'class' => 'form-control input-sm',
            'placeholder' => $this->translator->translate('Company.form.referenceNumber.placeholder')
        ));
        $referenceNumber->setLabel($this->translator->translate('Company.form.referenceNumber.label'));
        $referenceNumber->setLabelAttributes(array('class' => 'col-sm-2 control-label input-sm'));
        $this->add($referenceNumber);

        return $this;
    }

    public function setDefaultClientUser(User $user){
        $this->get('clientUser')->setValue($user->getId());
    }

    public function getInputFilter(){
        $filter = parent::getInputFilter();
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

        return $filter;
    }

    public function setFormValues(Client $client){
        parent::setFormValues($client);
        $this->get('status')->setValue($client->getStatus());
        $this->get('delayPercent')->setValue($client->getDelayPercent());
        $this->get('deadlineDays')->setValue($client->getDeadlineDays());
        $this->get('referenceNumber')->setValue($client->getReferenceNumber());
        if($client->getClientUser()){
            $this->get('clientUser')->setValue($client->getClientUser()->getId());
        }
    }
} 