<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 17:46
 */

namespace Application\Service;


use Application\Entity\Document\Invoice;
use Application\Entity\Role;
use Application\Entity\Subject\Company;
use Application\Entity\User;
use Zend\Stdlib\Parameters;

class InvoiceService extends DocumentService
{

    public function getFilterData(Parameters $data, User $user)
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
        if (isset($data->active) && $data->active == 1) {
//            $filterData->statuses[] = Invoice::STATUS_ACTIVE;
//        }
//        if(isset($data->disabled) && $data->disabled == 1){
//            $filterData->statuses[] = Client::STATUS_DISABLED;
//        }
            if (isset($data->name) && strlen(trim($data->name)) > 0) {
                $filterData->name = $data->name;
            }
            if (isset($data->code) && strlen(trim($data->code)) > 0) {
                $filterData->code = $data->code;
            }
            if (isset($data->email) && strlen(trim($data->email)) > 0) {
                $filterData->email = $data->email;
            }
            if (isset($data->address) && strlen(trim($data->address)) > 0) {
                $filterData->address = $data->address;
            }
            if (isset($data->regNo) && strlen(trim($data->regNo)) > 0) {
                $filterData->regNo = $data->regNo;
            }
            if (isset($data->vatNo) && strlen(trim($data->vatNo)) > 0) {
                $filterData->vatNo = $data->vatNo;
            }
            return $filterData;
        }
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
}