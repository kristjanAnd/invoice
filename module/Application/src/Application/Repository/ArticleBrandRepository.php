<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 30.04.15
 * Time: 17:43
 */

namespace Application\Repository;


use Application\Entity\Subject\Company;
use Zend\Stdlib\Parameters;

class ArticleBrandRepository extends AbstractRepository {

    public function getCompanyArticleBrands(Company $company, Parameters $data = null){
        $queryBuilder = $this->createQueryBuilder('Brand');
        $queryBuilder->where('Brand.company = :company')->setParameter('company', $company);
        if($data && isset($data->statuses) && count($data->statuses) > 0){
            $queryBuilder->andWhere("Brand.status IN ('" . implode("', '", $data->statuses) . "')");
        }
        return $queryBuilder->getQuery()->getResult();
    }
} 