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

    public function getCompanyInvoices(Company $company, Parameters $data = null) {
        $queryBuilder = $this->createQueryBuilder('Invoice');
        $queryBuilder->where('Invoice.company = :company')->setParameter('company', $company);
        if($data){
            if(isset($data->statuses) && count($data->statuses) > 0){
                $queryBuilder->andWhere("Invoice.status IN ('" . implode("', '", $data->statuses) . "')");
            }
            if(isset($data->id)){
                $queryBuilder->andWhere('Invoice.id =:id')->setParameter('id', $data->id);
            }
            if(isset($data->number)){
                $queryBuilder->andWhere('Invoice.fullNumber LIKE :number')->setParameter('number', '%' . $data->number . '%');
            }
            if(isset($data->clientName)){
                $queryBuilder->andWhere('Invoice.subjectName LIKE :clientName')->setParameter('clientName', '%' . $data->clientName . '%');
            }
            if(isset($data->fromDate)){
                $queryBuilder->andWhere('Invoice.documentDate >= :fromDate')->setParameter('fromDate', $data->fromDate);
            }
            if(isset($data->toDate)){
                $queryBuilder->andWhere('Invoice.documentDate >= :toDate')->setParameter('toDate', $data->toDate);
            }
            if(isset($data->invoiceUser)){
                $queryBuilder->andWhere('Invoice.user =:invoiceUser')->setParameter('invoiceUser', $data->invoiceUser);
            }
        }
        return $queryBuilder->getQuery()->getResult();
    }
} 