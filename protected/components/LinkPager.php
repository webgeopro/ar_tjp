<?php
/**
 * User: Vah
 * Date: 18.06.13
 * Time: 18:23
 * 1 ... 6 7 8 9 10 11 12 ... 21
 */

class LinkPager extends CLinkPager {

    public $header  = 'Page:';
    public $cssFile = '/css/pager.css';
    public $firstPageLabel= ' &nbsp;&nbsp;&nbsp;&nbsp; ';
    public $lastPageLabel = ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
    public $nextPageLabel = ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
    public $prevPageLabel = ' &nbsp;&nbsp;&nbsp;&nbsp; ';

    public $maxButtonCount = 7;
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

        // internal pages
        $buttons[]=$this->createPageButton(1,0,self::CSS_INTERNAL_PAGE,false,$currentPage<=0);
        $this->createDots($dotsFirst, $buttons);
        for($i=$beginPage; $i<=$endPage; ++$i)
            $buttons[] = $this->createPageButton($i+1,$i,self::CSS_INTERNAL_PAGE,false,$i==$currentPage);
        $this->createDots($dotsLast, $buttons);
        $buttons[]=$this->createPageButton($pageCount,$pageCount-1,self::CSS_INTERNAL_PAGE,false,$currentPage>=$pageCount-1);

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

    /**
     * @return array the begin and end pages that need to be displayed.
     */
    protected function getPageRange()
    {
        $dotsFirst = false; $dotsLast = false;
        $currentPage=$this->getCurrentPage();
        $pageCount=$this->getPageCount();
        $border = 6; $halfBorder = 3;
        if ($pageCount > $this->maxButtonCount) { // Если страниц больше 10
            if ($currentPage < $border) { //(5<6)
                $beginPage = 1; //0 (Первый элемент вставляем всегда)
                $dotsLast = true;
                $endPage   = $border;

            } elseif ($currentPage + $halfBorder >= $pageCount) { //(5+3>11)
                $dotsFirst  = true;
                //$beginPage = $this->maxButtonCount - $border + 1;
                $beginPage = $pageCount - $border;
                $endPage   = $pageCount - 2; //$this->maxButtonCount

            } else {
                //посередине - пять страниц, с обоих сторон обрамлённых многоточием.
                //активная страница, таким образом, окажется в центре переключателей.
                $dotsFirst = true;
                $beginPage = $currentPage - 2;
                $endPage   = $currentPage + 2;
                $dotsLast  = true;
            }

        } else {
            $beginPage=max(1, $currentPage-(int)($this->maxButtonCount/2)); //0
            if(($endPage=$beginPage+$this->maxButtonCount-1)>=$pageCount) {
                $endPage=$pageCount-2; //-1
                $beginPage=max(1,$endPage-$this->maxButtonCount+1); //0
            }
        }

        return array($beginPage,$endPage,$dotsFirst,$dotsLast);
    }

    protected function createDots($bool, &$buttons)
    {
        if ($bool)
            $buttons[] = '<li>...</li>';
    }

}