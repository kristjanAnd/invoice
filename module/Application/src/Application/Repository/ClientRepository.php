<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 13:13
 */

namespace Application\Repository;


use Application\Entity\Subject\Company;
use Zend\Stdlib\Parameters;

class ClientRepository extends AbstractRepository {

    public function getCompanyClients(Company $company, Parameters $data = null){
        $queryBuilder = $this->createQueryBuilder('Client');
        $queryBuilder->where('Client.company = :company')->setParameter('company', $company);
        if($data){
            if(isset($data->statuses) && count($data->statuses) > 0){
                $queryBuilder->andWhere("Client.status IN ('" . implode("', '", $data->statuses) . "')");
            }
            if(isset($data->name)){
                $queryBuilder->andWhere('Client.name LIKE :name')->setParameter('name', '%' . $data->name . '%');
            }
            if(isset($data->code)){
                $queryBuilder->andWhere('Client.code LIKE :code')->setParameter('code', '%' . $data->code . '%');
            }
            if(isset($data->email)){
                $queryBuilder->andWhere('Client.email LIKE :email')->setParameter('email', '%' . $data->email . '%');
            }
            if(isset($data->address)){
                $queryBuilder->andWhere('Client.address LIKE :address')->setParameter('address', '%' . $data->address . '%');
            }
            if(isset($data->regNo)){
                $queryBuilder->andWhere('Client.registrationNumber LIKE :regNo')->setParameter('regNo', '%' . $data->regNo . '%');
            }
            if(isset($data->vatNo)){
                $queryBuilder->andWhere('Client.vatNumber LIKE :vatNo')->setParameter('vatNo', '%' . $data->vatNo . '%');
            }
            if(isset($data->clientUser)){
                $queryBuilder->andWhere('Client.clientUser =:clientUser')->setParameter('clientUser', $data->clientUser);
            }
        }
        return $queryBuilder->getQuery()->getResult();
    }
} 