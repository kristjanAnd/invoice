<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 8:03
 */

namespace Application\Service;


use Application\Entity\Subject\Company;
use Application\Entity\Vat;
use Zend\Stdlib\Parameters;

class VatService extends AbstractService {

    public function getVatsByCompany(Company $company, Parameters $data = null) {
        return $this->entityManager->getRepository(Vat::getClass())->getCompanyVats($company, $data);
    }

    public function getCompanyActiveVats(Company $company){
        return $this->entityManager->getRepository(Vat::getClass())->findBy(array('company' => $company, 'status' => Vat::STATUS_ACTIVE));
    }

    public function getCompanyActiveVatSelect(Company $company){
        $result = array();
        foreach($this->getCompanyActiveVats($company) as $vat){
            $result[$vat->getId()] = $vat->getCode();
        }
        return $result;
    }

    public function getFilterData(Parameters $data){
        $filterData = new Parameters();
        $filterData->statuses = array();
        if(isset($data->active) && $data->active == 1){
            $filterData->statuses[] = Vat::STATUS_ACTIVE;
        }
        if(isset($data->disabled) && $data->disabled == 1){
            $filterData->statuses[] = vat::STATUS_DISABLED;
        }
        return $filterData;
    }

    public function getActiveCompanyVats(Company $company){
        return $this->entityManager->getRepository(Vat::getClass())->findBy(array('company' => $company, 'status' => Vat::STATUS_ACTIVE));
    }

    /**
     * @param $id
     * @return null|Vat
     */
    public function getVatById($id){
        return $this->entityManager->getRepository(Vat::getClass())->findOneBy(array('id' => $id));
    }

    public function saveVat(Vat $vat, Parameters $data){
        if(isset($data->code)){
            $vat->setCode($data->code);
        }
        if(isset($data->value)){
            $vat->setValue($data->value);
        }
        if(isset($data->status)){
            $vat->setStatus($data->status);
        }
        $this->entityManager->persist($vat);
        $this->entityManager->flush($vat);

        return $vat;
    }

    public function getVatStatusSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            Vat::STATUS_ACTIVE => $translator->translate('Vat.status.active'),
            Vat::STATUS_DISABLED => $translator->translate('Vat.status.disabled')
        );
    }
} 