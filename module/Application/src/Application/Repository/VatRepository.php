<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 8:04
 */

namespace Application\Repository;


use Application\Entity\Subject\Company;
use Zend\Stdlib\Parameters;

class VatRepository extends AbstractRepository {

    public function getCompanyVats(Company $company, Parameters $data = null){
        $queryBuilder = $this->createQueryBuilder('Vat');
        $queryBuilder->where('Vat.company = :company')->setParameter('company', $company);
        if($data && isset($data->statuses) && count($data->statuses) > 0){
            $queryBuilder->andWhere("Vat.status IN ('" . implode("', '", $data->statuses) . "')");
        }
        return $queryBuilder->getQuery()->getResult();
    }
} 