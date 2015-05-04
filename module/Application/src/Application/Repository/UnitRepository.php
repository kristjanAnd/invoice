<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 30.04.15
 * Time: 17:20
 */

namespace Application\Repository;


use Application\Entity\Subject\Company;
use Zend\Stdlib\Parameters;

class UnitRepository extends AbstractRepository {

    public function getCompanyUnits(Company $company, Parameters $data = null){
        $queryBuilder = $this->createQueryBuilder('Unit');
        $queryBuilder->where('Unit.company = :company')->setParameter('company', $company);
        if($data && isset($data->statuses) && count($data->statuses) > 0){
            $queryBuilder->andWhere("Unit.status IN ('" . implode("', '", $data->statuses) . "')");
        }
        return $queryBuilder->getQuery()->getResult();
    }
} 