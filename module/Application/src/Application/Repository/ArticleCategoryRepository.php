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

class ArticleCategoryRepository extends AbstractRepository {

    public function getCompanyArticleCategories(Company $company, Parameters $data = null){
        $queryBuilder = $this->createQueryBuilder('Category');
        $queryBuilder->where('Category.company = :company')->setParameter('company', $company);
        if($data && isset($data->statuses) && count($data->statuses) > 0){
            $queryBuilder->andWhere("Category.status IN ('" . implode("', '", $data->statuses) . "')");
        }
        return $queryBuilder->getQuery()->getResult();
    }
} 