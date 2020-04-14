<?php
/**
 * Виджет "Поиск".
 */

class searchBlock extends CWidget {

    public $duration;         // Время хранения кеша в секундах (24 часа)86400
    public $size = 19;        // Размер input-а (size="")
    public $itemUrl = '';     // Url пазла для возврата из результатов поиска
    public $albumUrl = '';    // Url альбома для возврата из результатов поиска
    public $extended = false; // Отображать форму с расширенными полями
    public $searchString = '';// Поисковый запрос
    public $defaultHint = 'Search for puzzles';

    public $inTitle;
    public $inAuthor;
    public $inKeywords;
    public $page = 1;

    public function run() {
        $this->duration = Yii::app()->params['dSearchBlock'];
        $cs = Yii::app()->clientScript; // Подключаем свои js- и css-файлы
        if ( !$cs->isScriptFileRegistered('/js/jquery.js', CClientScript::POS_HEAD) )
            $cs->registerScriptFile('/js/jquery.js', CClientScript::POS_HEAD);
        if ( !$cs->isScriptFileRegistered('/js/jquery.hint.js', CClientScript::POS_HEAD) )
            $cs->registerScriptFile('/js/jquery.hint.js', CClientScript::POS_HEAD);
        if ( !$cs->isScriptFileRegistered('/js/searchBlock.js', CClientScript::POS_HEAD) )
            $cs->registerScriptFile('/js/searchBlock.js', CClientScript::POS_HEAD);

        $this->render('searchBlock', array(
            'searchString' => $this->searchString,
        ));
	}
}