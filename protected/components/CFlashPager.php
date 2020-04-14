<?php
/**
 * User: Vah
 * Date: 01.07.13
 * Пагинатор под flash-сборкой пазла.
 */

class CFlashPager extends CLinkPager {

    public $header  = '';
    public $cssFile = '/css/pager.css';
    public $firstPageLabel= ' &nbsp;&nbsp;&nbsp;&nbsp; ';
    public $lastPageLabel = ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
    public $nextPageLabel = ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
    public $prevPageLabel = ' &nbsp;&nbsp;&nbsp;&nbsp; ';

    public $maxButtonCount = 1;
    /**
     * Creates the page buttons. Перегружаем.
     * @return array a list of page buttons (in HTML code).
     */
    protected function createPageButtons()
    {
        if(($pageCount=$this->getPageCount())<=1)
            return array();

        list($beginPage,$endPage,$dotsFirst,$dotsLast)=$this->getPageRange();

        $currentPage=$this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $buttons=array();

        // first page
        $buttons[]=$this->createPageButton($this->firstPageLabel,0,self::CSS_FIRST_PAGE,$currentPage<=0,false);

        // prev page
        if(($page=$currentPage-1) < 0)
            $page=0;
        $buttons[]=$this->createPageButton($this->prevPageLabel,$page,self::CSS_PREVIOUS_PAGE,$currentPage<=0,false);

        // next page
        if(($page=$currentPage+1) >= $pageCount-1)
            $page=$pageCount-1;
        $buttons[]=$this->createPageButton($this->nextPageLabel,$page,self::CSS_NEXT_PAGE,$currentPage>=$pageCount-1,false);

        // last page
        $buttons[]=$this->createPageButton($this->lastPageLabel,$pageCount-1,self::CSS_LAST_PAGE,$currentPage>=$pageCount-1,false);

        return $buttons;
    }

    protected function createPageButton($label,$page,$class,$hidden,$selected)
    {
        if($hidden || $selected)
            $class.=' '.($hidden ? self::CSS_HIDDEN_PAGE : self::CSS_SELECTED_PAGE);
        return '<li class="'.$class.'">'.CHtml::link($label,$this->createPageUrl($page)).'</li>';
    }

}