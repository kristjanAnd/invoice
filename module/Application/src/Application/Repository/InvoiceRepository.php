<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 19:12
 */

namespace Application\Repository;


use Application\Entity\Subject\Company;
use Zend\Stdlib\Parameters;

class InvoiceRepository extends AbstractRepository {

    public function getCompanyInvoices(Company $company, Parameters $data = null){
        $queryBuilder = $this->createQueryBuilder('Invoice');
        $queryBuilder->where('Invoice.company = :company')->setParameter('company', $company);
        if($data){
            if(isset($data->statuses) && count($data->statuses) > 0){
                $queryBuilder->andWhere("Invoice.status IN ('" . implode("', '", $data->statuses) . "')");
            }
//            if(isset($data->name)){
//                $queryBuilder->andWhere('Invoice.name LIKE :name')->setParameter('name', '%' . $data->name . '%');
//            }
//            if(isset($data->code)){
//                $queryBuilder->andWhere('Client.code LIKE :code')->setParameter('code', '%' . $data->code . '%');
//            }
//            if(isset($data->email)){
//                $queryBuilder->andWhere('Client.email LIKE :email')->setParameter('email', '%' . $data->email . '%');
//            }
//            if(isset($data->address)){
//                $queryBuilder->andWhere('Client.address LIKE :address')->setParameter('address', '%' . $data->address . '%');
//            }
//            if(isset($data->regNo)){
//                $queryBuilder->andWhere('Client.registrationNumber LIKE :regNo')->setParameter('regNo', '%' . $data->regNo . '%');
//            }
//            if(isset($data->vatNo)){
//                $queryBuilder->andWhere('Client.vatNumber LIKE :vatNo')->setParameter('vatNo', '%' . $data->vatNo . '%');
//            }
            if(isset($data->invoiceUser)){
                $queryBuilder->andWhere('Invoice.user =:invoiceUser')->setParameter('invoiceUser', $data->invoiceUser);
            }
        }
        return $queryBuilder->getQuery()->getResult();
    }
} 