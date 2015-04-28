<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4/24/14
 * Time: 3:37 PM
 */

namespace Application\Paginator;


class PaginationParameters {

    const ITEM_COUNT_PER_PAGE = 10;
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    protected $currentPageNumber;
    protected $itemCountPerPage = self::ITEM_COUNT_PER_PAGE;
    protected $sort;
    protected $order;
    protected $filters = array();
    protected $cache;

    public function __construct($currentPageNumber = null, $sort = null, $order = self::ORDER_ASC, $itemCountPerPage = self::ITEM_COUNT_PER_PAGE) {
        if ($itemCountPerPage == null) {
            $itemCountPerPage = self::ITEM_COUNT_PER_PAGE;
        }
        $this->currentPageNumber = $currentPageNumber;
        $this->sort = $sort;
        $this->order = $order;
        $this->itemCountPerPage = $itemCountPerPage;
    }

    /**
     * @return the $currentPageNumber
     */
    public function getCurrentPageNumber() {
        return $this->currentPageNumber;
    }

    /**
     * @param field_type $currentPageNumber
     */
    public function setCurrentPageNumber($currentPageNumber) {
        $this->currentPageNumber = $currentPageNumber;

        return $this;
    }

    /**
     * @return the $itemCountPerPage
     */
    public function getItemCountPerPage() {
        return $this->itemCountPerPage;
    }

    /**
     * @param string $itemCountPerPage
     */
    public function setItemCountPerPage($itemCountPerPage) {
        $this->itemCountPerPage = $itemCountPerPage;

        return $this;
    }

    /**
     * @return the $sort
     */
    public function getSort() {
        return $this->sort;
    }

    /**
     * @param field_type $sort
     */
    public function setSort($sort) {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return the $order
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * @param field_type $order
     */
    public function setOrder($order) {
        $this->order = $order;

        return $this;
    }

    public function getFilter($key) {
        if (!isset($this->filters[$key])) {

            return null;
        }

        return $this->filters[$key];
    }

    /**
     * @return the $filters
     */
    public function getFilters() {
        return $this->filters;
    }

    /**
     * @param array: $filters
     */
    public function setFilters(array $filters = array()) {
        $this->filters = $filters;
    }

    public function setFilter($key, $value) {
        $this->filters[$key] = $value;
    }

    public function setCache($cache) {
        $this->cache = $cache;
    }

    public function getCache() {
        return $this->cache;
    }

}