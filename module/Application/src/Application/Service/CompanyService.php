<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 21.04.15
 * Time: 17:45
 */

namespace Application\Service;


use Application\Entity\Subject\Company;
use Zend\Stdlib\Parameters;

class CompanyService extends AbstractService {

    public function saveCompany(Company $company, Parameters $data){

        if(isset($data->name)){
            $company->setName($data->name);
        }
        if(isset($data->code)){
            $company->setCode($data->code);
        }
        if(isset($data->email)){
            $company->setEmail($data->email);
        }
        if(isset($data->regNo)){
            $company->setRegistrationNumber($data->regNo);
        }
        if(isset($data->phone)){
            $company->setPhone($data->phone);
        }
        if(isset($data->vatNo)){
            $company->setVatNumber($data->vatNo);
        }
        if(isset($data->address)){
            $company->setAddress($data->address);
        }

        $this->entityManager->persist($company);
        $this->entityManager->flush($company);

        return $company;
    }

    public function getCompanyById($id){
        return $this->entityManager->getRepository(Company::getClass())->findOneBy(array('id' => $id));
    }
} 