<?php


namespace App\Util;

use JMS\Serializer\Annotation as Serializer;

class Paginator
{
    /**
     * @var  int
     * @Serializer\Groups({"Default"})
     * @Serializer\Type("int")
     */
    private $page;

    /**
     * @var  int
     * @Serializer\Groups({"Default"})
     * @Serializer\Type("int")
     */
    private $totalItems;

    /**
     * @var  int
     * @Serializer\Groups({"Default"})
     * @Serializer\Type("int")
     */
    private $itemsPerPage;

    /**
     * @var  int
     * @Serializer\Groups({"Default"})
     * @Serializer\Type("int")
     */
    private $pageCount;

    /**
     * @var  mixed
     * @Serializer\Groups({"Default"})
     * @Serializer\Type("array")
     */
    private $data;

    private $paginationItems;
    private $enableRightSkipLink;
    private $enableLeftSkipLink;

    function initialize() {
        $this->calculatePages();
        $this->calculatePaginationItems();
    }
    public function setPagination($pagItems){
        foreach($pagItems[0] as $key=>$value){
            switch($key){
                case "pageNumber":
                    $this->page=$value[0];
                    break;
                case "entriesPerPage":
                    $this->itemsPerPage=$value[0];
                    break;
                case "totalPages":
                    $this->pageCount=$value[0];
                    break;
                case "totalEntries":
                    $this->totalItems=$value[0];
                    break;
            }
        }
        
    }
    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getPage(){
        return $this->page;
    }

    public function getTotalItems(){
        return $this->totalItems;
    }

    public function getItemsPerPage(){
        return $this->itemsPerPage;
    }



    private function calculatePages(){
        $mod = $this->totalItems % $this->itemsPerPage;
        $pages = floor($this->totalItems / $this->itemsPerPage);

        if ($mod > 0){
            //add one page for the remaining items
            $pages++;           
        }

        $this->setPageCount($pages);

    }



    private function calculatePaginationItems(){
        $items = [];
        //hay 5 o menos paginas
        if($this->pageCount <= 5){
            $this->setEnableLeftSkipLink(false);
            $this->setEnableRightSkipLink(false);
            for($i = 1; $i <= $this->pageCount; $i++){
                array_push($items, $i);
            }
        }

        else if($this->pageCount > 5 && $this->page < 3){
            $this->setEnableLeftSkipLink(false);
            $this->setEnableRightSkipLink(true);
            for($i = 1; $i <= 5; $i++){
                array_push($items, $i);
            }
        }

        else if($this->pageCount > 5 && $this->page >= ($this->pageCount - 2)){
            $this->setEnableLeftSkipLink(true);
            $this->setEnableRightSkipLink(false);
            for($i = $this->pageCount - 4 ; $i <= $this->pageCount ; $i++){
                array_push($items, $i);
            }
        }

        else if($this->pageCount > 5 && $this->page <= ($this->pageCount - 3) && $this->page >= 3){
            $this->setEnableLeftSkipLink(true);
            $this->setEnableRightSkipLink(true);
            for($i = $this->page - 2 ; $i <= $this->page + 2 ; $i++){
                array_push($items, $i);
            }
        }

        $this->setPaginationItems($items);
    }

    public function getPageCount()
    {
        return $this->pageCount;
    }

    public function setPageCount($pageCount)
    {
        $this->pageCount = $pageCount;
    }


    /**
     * @param mixed $totalItems
     */
    public function setTotalItems($totalItems)
    {
        $this->totalItems = $totalItems;
    }

    /**
     * @param mixed $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getEnableRightSkipLink()
    {
        return $this->enableRightSkipLink;
    }

    public function setEnableRightSkipLink($enableRightSkipLink)
    {
        $this->enableRightSkipLink = $enableRightSkipLink;
    }

    public function getEnableLeftSkipLink()
    {
        return $this->enableLeftSkipLink;
    }


    public function setEnableLeftSkipLink($enableLeftSkipLink)
    {
        $this->enableLeftSkipLink = $enableLeftSkipLink;
    }

    public function getPaginationItems()
    {
        return $this->paginationItems;
    }

    public function setPaginationItems($paginationItems)
    {
        $this->paginationItems = $paginationItems;
    }


}