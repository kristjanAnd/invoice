<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 17:46
 */

namespace Application\Service;


use Application\Entity\Document\Invoice;
use Application\Entity\DocumentSetting\InvoiceSetting;
use Application\Entity\Role;
use Application\Entity\Subject\Company;
use Application\Entity\User;
use Application\Entity\Vat;
use Application\Form\FilterForm;
use Zend\Form\Form;
use Zend\Stdlib\Parameters;

class InvoiceService extends DocumentService
{

    public function getFilterData(Parameters $data, User $user, $dateFormat)
    {
        $filterData = new Parameters();
        $filterData->statuses = array();
        if (!$user->isMaster() || $user->getRoles()->last()->getRoleId() !== Role::ROLE_ADMIN) {
            $filterData->clientUser = $user;
        }

        if (isset($data->invoiceUser) && $data->invoiceUser > 0) {
            $invoiceUser = $this->entityManager->getRepository(User::getClass())->findOneBy(array('id' => $data->invoiceUser));
            if ($invoiceUser && $invoiceUser->getCompany() == $user->getCompany()) {
                $filterData->invoiceUser = $invoiceUser;
            }
        }
        if (isset($data->confirmed) && $data->confirmed == 1) {
            $filterData->statuses[] = Invoice::STATUS_CONFIRMED;
        }
        if(isset($data->pending) && $data->pending == 1){
            $filterData->statuses[] = Invoice::STATUS_PENDING;
        }
        if(isset($data->archived) && $data->archived == 1){
            $filterData->statuses[] = Invoice::STATUS_ARCHIVED;
        }
        if (isset($data->clientName) && strlen(trim($data->clientName)) > 0) {
            $filterData->clientName = $data->clientName;
        }
        if (isset($data->entityId) && strlen(trim($data->entityId)) > 0) {
            $filterData->id = $data->entityId;
        }
        if (isset($data->entityNumber) && strlen(trim($data->entityNumber)) > 0) {
            $filterData->number = $data->entityNumber;
        }
        if (isset($data->fromDate) && strlen(trim($data->fromDate)) > 0) {
            $fromDate = \DateTime::createFromFormat($dateFormat, $data->fromDate);
            if($fromDate){
                $filterData->fromDate = $fromDate;
            }
        }
        if (isset($data->toDate) && strlen(trim($data->toDate)) > 0) {
            $toDate = \DateTime::createFromFormat($dateFormat, $data->toDate);
            if($toDate){
                $filterData->toDate = $toDate;
            }
        }
        return $filterData;
    }


    public function getDefaultFilterData(FilterForm $form, User $user){
        $filterData = new Parameters();
        $filterData->statuses = array();
        if (!$user->isMaster() || $user->getRoles()->last()->getRoleId() !== Role::ROLE_ADMIN) {
            $filterData->invoiceUser = $user;
        }

        foreach($form->getElements() as $element){
            if($element->getValue()){
                $name = $element->getName();
                if(in_array($name, FilterForm::$statusNames)){
                    $filterData->statuses[] = $name;
                } elseif (in_array($name, FilterForm::$dateNames)){
                    $date = \DateTime::createFromFormat($form->getFilterDateFormat(), $element->getValue());
                    if($date){
                        $filterData->$name = $date;
                    }
                } else {
                    $filterData->$name = $element->getValue();
                }
            }
        }
        return $filterData;
    }

    public function getCompanyInvoices(Company $company, Parameters $data = null){
        return $this->entityManager->getRepository(Invoice::getClass())->getCompanyInvoices($company, $data);
    }

    public function getInvoiceStatusSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            Invoice::STATUS_PENDING => $translator->translate('Invoice.status.pending'),
            Invoice::STATUS_ARCHIVED => $translator->translate('Invoice.status.archived'),
            Invoice::STATUS_CONFIRMED => $translator->translate('Invoice.status.confirmed')
        );
    }

    public function getInvoicePaymentStatusSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            Invoice::PAYMENT_STATUS_PAID => $translator->translate('Invoice.paymentStatus.paid'),
            Invoice::PAYMENT_STATUS_UNPAID => $translator->translate('Invoice.paymentStatus.unPaid')
        );
    }

    public function getInvoiceSettingByCompany(Company $company, User $user){
        $invoiceSetting = $this->entityManager->getRepository(InvoiceSetting::getClass())->findOneBy(array('company' => $company));
        if(!$invoiceSetting){
            $invoiceSetting = $this->createInvoiceSetting($company, $user);
        }
        return $invoiceSetting;
    }

    public function createInvoiceSetting(Company $company, User $user){
        $invoiceSetting = new InvoiceSetting();
        $invoiceSetting->setCompany($company);
        $invoiceSetting->setUser($user);

        $this->entityManager->persist($invoiceSetting);
        $this->entityManager->flush($invoiceSetting);

        return $invoiceSetting;
    }

    public function saveInvoiceSetting(InvoiceSetting $invoiceSetting, Parameters $data){
        if(isset($data->prefix)){
            $invoiceSetting->setPrefix($data->prefix);
        }
        if(isset($data->suffix)){
            $invoiceSetting->setSuffix($data->suffix);
        }
        if(isset($data->nextNumber)){
            $invoiceSetting->setNextNumber($data->nextNumber);
        }
        if(isset($data->languageCode)){
            $invoiceSetting->setPdfLanguageCode($data->languageCode);
        }
        if(isset($data->delayPercent)){
            $invoiceSetting->setDelayPercent($data->delayPercent);
        }
        if(isset($data->deadlineDays)){
            $invoiceSetting->setDeadlineDays($data->deadlineDays);
        }
        if(isset($data->dateFormat)){
            $invoiceSetting->setDatePdfFormat($data->dateFormat);
        }
        if(isset($data->vat) && $data->vat > 0){
            $vat = $this->entityManager->getRepository(Vat::getClass())->findOneBy(array('id' => $data->vat));
            if($vat && $vat->getCompany() == $invoiceSetting->getCompany()){
                $invoiceSetting->setVat($vat);
            }
        }
        $this->entityManager->persist($invoiceSetting);
        $this->entityManager->flush($invoiceSetting);

        return $invoiceSetting;
    }
}