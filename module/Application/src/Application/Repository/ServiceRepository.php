<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 9:23
 */

namespace Application\Repository;


use Application\Entity\Subject\Company;
use Zend\Stdlib\Parameters;

class ServiceRepository extends AbstractRepository {

    public function getCompanyServices(Company $company, Parameters $data = null){
        $queryBuilder = $this->createQueryBuilder('Service');
        $queryBuilder->where('Service.company = :company')->setParameter('company', $company);
        if($data){
            if(isset($data->statuses) && count($data->statuses) > 0){
                $queryBuilder->andWhere("Service.status IN ('" . implode("', '", $data->statuses) . "')");
            }
            if(isset($data->name)){
                $queryBuilder->andWhere('Item.name LIKE :name')->setParameter('name', '%' . $data->name . '%');
            }
            if(isset($data->code)){
                $queryBuilder->andWhere('Item.code LIKE :code')->setParameter('code', '%' . $data->code . '%');
            }
            if(isset($data->category)){
                $queryBuilder->andWhere('Item.category =:category')->setParameter('category', $data->category);
            }
            if(isset($data->brand)){
                $queryBuilder->andWhere('Item.brand =:brand')->setParameter('brand', $data->brand);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }
} 