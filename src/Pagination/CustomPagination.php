<?php

namespace App\Pagination;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class CustomPagination {

    private $links;
    private $items;
    private $page;
    private $totalItems;
    private $itemsPerPage;
    private $route;
    private $parameters;
    private $router;
    private $pageTemplateUrl;
    private $pageCount;

    public function __construct(Router $router) {
        $this->router = $router;
    }

    public function setPaginatedResults( $results) {
        
        $this->items = $results->getItems();
        $this->page = $results->getCurrentPageNumber();
        $this->totalItems = $results->getTotalItemCount();
        $this->itemsPerPage = $results->getItemNumberPerPage();
        $this->route = $results->getRoute();
        $this->parameters = $results->getParams();

        if ($this->totalItems > 0) {
            $this->pageCount = ceil($this->totalItems / $this->itemsPerPage);
        }

        $this->generateLinks();
    }

    public function generateLinks() {
        $this->links = array(
            'self' => $this->getCurrentPageUrl(),
            'first' => $this->getFirstPageUrl(),
            'prev' => $this->getPreviousPageUrl(),
            'next' => $this->getNextPageUrl(),
            'last' => $this->getLastPageUrl(),
            '_template' => $this->getPageTemplateUrl()
        );
    }

    /**
     * @return mixed
     */
    public function getLinks() {
        return $this->links;
    }

    /**
     * @param mixed $links
     */
    public function setLinks($links) {
        $this->links = $links;
    }

    /**
     * @return mixed
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items) {
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page) {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getTotalItems() {
        return $this->totalItems;
    }

    /**
     * @param mixed $totalItems
     */
    public function setTotalItems($totalItems) {
        $this->totalItems = $totalItems;
    }

    /**
     * @return mixed
     */
    public function getItemsPerPage() {
        return $this->itemsPerPage;
    }

    /**
     * @param mixed $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage) {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @return int
     */
    public function getPageCount() {
        return $this->pageCount;
    }

    /**
     * @param int $pageCount
     */
    public function setPageCount($pageCount) {
        $this->pageCount = $pageCount;
    }

    private function generatePageTemplateUrl() {

        //Put all the params except the "page" one.
        $params = array();
        foreach ($this->parameters as $key => $value) {
            if ($key != 'page') {
                $params[$key] = $value;
            }
        }

        $this->pageTemplateUrl = $this->router->generate($this->route, $params);

        //now append the page parameter as a placeholder.
        //dont do it with the generate because it escapes all "non url valid" chars, and we dont want that.

        if (strpos($this->pageTemplateUrl, "?") === false) {
            $this->pageTemplateUrl .= '?page={page_number}';
        } else {
            $this->pageTemplateUrl .= '&page={page_number}';
        }
    }

    private function getPageTemplateUrl() {
        if ($this->pageTemplateUrl == "") {
            $this->generatePageTemplateUrl();
        }

        return $this->pageTemplateUrl;
    }

    private function getCurrentPageUrl() {
        return str_replace("{page_number}", $this->page, $this->getPageTemplateUrl());
    }

    private function getFirstPageUrl() {
        if ($this->totalItems > 0 && $this->page > 1) {
            return str_replace("{page_number}", 1, $this->getPageTemplateUrl());
        }
    }

    private function getLastPageUrl() {
        if ($this->pageCount > 0 && $this->page < $this->pageCount) {
            return str_replace("{page_number}", $this->pageCount, $this->getPageTemplateUrl());
        }
    }

    private function getNextPageUrl() {
        if ($this->page < $this->pageCount) {
            return str_replace("{page_number}", $this->page + 1, $this->getPageTemplateUrl());
        }
    }

    private function getPreviousPageUrl() {
        if ($this->page > 1) {
            return str_replace("{page_number}", $this->page - 1, $this->getPageTemplateUrl());
        }
    }

}