<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 30.04.15
 * Time: 17:19
 */

namespace Application\Repository;

use Application\Paginator\PaginationParameters;
//use BitWebExtension\Doctrine\Query\MySql\MatchAgainstFunction;
//
//use BitWebExtension\Doctrine\Query\MySql\Spatial\Contains\GeographicFunction;
//
//use BitWebExtension\Doctrine\Query\MySql\Spatial\DistanceFunction;
//
//use BitWebExtension\Doctrine\Query\MySql\Spatial\GeomFunction;

use Doctrine\Common\Collections\ArrayCollection;

use DoctrineORMModule\Paginator\Adapter;

use Doctrine\ORM\QueryBuilder;

use Doctrine\ORM\Query;

use Zend\Paginator\Paginator;


use Doctrine\ORM\EntityRepository;

abstract class AbstractRepository extends EntityRepository {

    protected $usedPaginator = null;

    /**
     * Gets exact count while temporarily disabling inheritance
     * @return \Doctrine\ORM\mixed
     */
    public function findCount(QueryBuilder $queryBuilder = null) {
        if ($queryBuilder == null) {
            $queryBuilder = $this->getCountQueryBuilder();
        }

        $savedInheritanceType = $this->getEntityManager()->getClassMetadata($this->getEntityName())->inheritanceType;
        $this->getEntityManager()->getClassMetadata($this->getEntityName())->inheritanceType = 1; //Remove inheritance type for count query

        $count = $queryBuilder->getQuery()->getSingleScalarResult();

        $this->getEntityManager()->getClassMetadata($this->getEntityName())->inheritanceType = $savedInheritanceType; //Revert inheritance type

        return $count;
    }

    /**
     * Get this in your repository and add conditions if needed. Use later for findCount(QueryBuilder) function.
     * Note that you can't use inherited fields and entity alias is CurrentEntity.
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getCountQueryBuilder() {
        $queryBuilder = $this->createQueryBuilder('CurrentEntity');
        $queryBuilder->select('COUNT(CurrentEntity)');

        return $queryBuilder;
    }

    /**
     * Gets an approximate row count from the information_schema.tables for the current entity
     * @return int|NULL
     */
    public function findApproximateCount() {
        $database = $this->getEntityManager()->getConnection()->getDatabase();
        $tableName = $this->getClassMetadata()->table['name'];

        $sql = 'SELECT table_rows FROM information_schema.tables WHERE TABLE_SCHEMA=? AND  TABLE_NAME=?';
        $resultSetMapping = new \Doctrine\ORM\Query\ResultSetMapping();
        $resultSetMapping->addScalarResult('table_rows', 'table_rows');
        $query = $this->_em->createNativeQuery($sql, $resultSetMapping);
        $query->setParameter(1, $database);
        $query->setParameter(2, $tableName);
        $result = $query->getOneOrNullResult();

        return $result['table_rows'];
    }

    /**
     * Gets paginated results
     * @param PaginationParameters $paginationParameters
     * @return \Zend\Paginator\Paginator
     */
    public function findAllPaginated(PaginationParameters $paginationParameters) {
        $queryBuilder = $this->createQueryBuilder('Entity');

        return $this->getPaginatedResult($queryBuilder, $paginationParameters);
    }

    /**
     * Gets paginated results by criteria
     * @param array $criteria
     * @param PaginationParameters $paginationParameters
     * @param array $orderBy Extra order by for ordering
     * @return \Zend\Paginator\Paginator
     */
    public function findByPaginated(array $criteria, PaginationParameters $paginationParameters, array $orderBy = array ()) {
        $queryBuilder = $this->createQueryBuilder('Entity');
        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $queryBuilder->andWhere($queryBuilder->getRootAlias() . '.' . $field . ' IN (:' . $field . 'Value)')->setParameter($field . 'Value', '"' . implode('","', $value) . '"');
            } else if ($value === null) {
                $queryBuilder->andWhere($queryBuilder->getRootAlias() . '.' . $field . ' IS NULL');
            } else {
                $queryBuilder->andWhere($queryBuilder->getRootAlias() . '.' . $field . ' = :' . $field . 'Value')->setParameter($field . 'Value', $value);
            }
        }


        foreach ($orderBy as $order => $sort) {
            $queryBuilder->addOrderBy($queryBuilder->getRootAlias() . '.' . $order, $sort);
        }

        return $this->getPaginatedResult($queryBuilder, $paginationParameters);
    }

    public function fetchPairs($column1, $column2, $criteria = array ()) {
        $results = array ();
        foreach ($this->findBy($criteria) as $object) {
            $methodName1 = 'get' . ucfirst($column1);
            $methodName2 = 'get' . ucfirst($column2);

            $results[$object->$methodName1()] = $object->$methodName2();
        }

        return $results;
    }

    protected function getUninheritedResult(QueryBuilder $queryBuilder, $hydrationMode = null) {
        if ($hydrationMode == null) {
            $hydrationMode = Query::HYDRATE_OBJECT;
        }
        $savedInheritanceType = $this->getEntityManager()->getClassMetadata($this->getEntityName())->inheritanceType;
        $this->getEntityManager()->getClassMetadata($this->getEntityName())->inheritanceType = 1; //Remove inheritance type for count query

        $result = $queryBuilder->getQuery()->getResult($hydrationMode);

        $this->getEntityManager()->getClassMetadata($this->getEntityName())->inheritanceType = $savedInheritanceType; //Revert inheritance type

        return $result;
    }

    /**
     * An internal method for getting paginated results from querybuilder
     * @param QueryBuilder $queryBuilder
     * @param PaginationParameters $paginationParameters
     * @param $resultKey Deprecated, use HIDDEN keyword instead
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginatedResult(QueryBuilder $queryBuilder, PaginationParameters $paginationParameters, $resultKey = null) {
        if ($paginationParameters->getOrder() != null && $paginationParameters->getSort() != null) {

            if (!is_array($paginationParameters->getSort()) && !is_array($paginationParameters->getOrder())) {
                $sort = array($paginationParameters->getSort());
                $order = array($paginationParameters->getOrder());
            } else {
                if (count($paginationParameters->getSort()) != count($paginationParameters->getOrder())) {
                    throw new \InvalidArgumentException('Sort and order arrays must be the same length');
                }
                $sort = $paginationParameters->getSort();
                $order = $paginationParameters->getOrder();
            }

            foreach ($sort as $key => $oneSortField) {
                if (!strstr($oneSortField, '.')) {
                    $alias = $queryBuilder->getRootAlias() . '.';
                } else {
                    $alias = '';
                }
                $queryBuilder->addOrderBy($alias . $oneSortField, $order[$key]);
            }
        }

        if ($paginationParameters->getFilters() != null) {
            foreach ($paginationParameters->getFilters() as $field => $value) {
                if ($value == '' || $queryBuilder->getParameter($field) != null) {
                    continue;
                }
                if (!strstr($field, '.')) {
                    $entityField = $queryBuilder->getRootAlias() . '.' . $field;
                } else {
                    $entityField = $field;
                }

                if (is_array($value)) {
                    $queryBuilder->andWhere($entityField . ' IN (\'' . implode("', '", $value) . '\')');
                } else {
                    $queryBuilder->andWhere($entityField . ' = :' . str_replace('.', '_', $field))->setParameter(str_replace('.', '_', $field), $value);
                }
            }
        }

        if ($this->usedPaginator == null) {
            $paginatorTool = new \Doctrine\ORM\Tools\Pagination\Paginator($queryBuilder);
        } else {
            $paginatorTool = $this->usedPaginator;
        }

        $paginator = new Paginator(new Adapter\DoctrinePaginator($paginatorTool));
        $paginator->setItemCountPerPage($paginationParameters->getItemCountPerPage());
        $paginator->setCurrentPageNumber($paginationParameters->getCurrentPageNumber());
        if ($paginationParameters->getCache() != null) {
            $paginator->setCache($paginationParameters->getCache());
        }
        if ($resultKey !== null) {
            foreach ($paginator as &$itemElement) {
                $itemElement = $itemElement[$resultKey];
            }
        }

        return $paginator;
    }

    public function sanitizeIntArray(array $ints) {
        $result = array();
        foreach ($ints as $int) {
            if ($int !== null) {
                $result[] = (int)$int;
            }
        }

        return $result;
    }

    public function orderByIds($orderedIds, ArrayCollection $entities) {
        $entities = $entities->toArray();

        $result = array();
        foreach ($entities as $entity) {
            $result[$entity->getId()] = $entity;
        }
        $finalResult = array();
        foreach ($orderedIds as $id) {
            $finalResult[] = $result[$id];
        }

        return new ArrayCollection($finalResult);
    }

    protected function refreshConnection() {
        $connection = $this->getEntityManager()->getConnection();
        $connection->commit();
        $connection->beginTransaction();
    }
}