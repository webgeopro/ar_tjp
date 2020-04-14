<?php
/**
 * Виджет "Категории". categories
 * Работа с пазлами, левая часть страницы.
 * 
 */

class categories extends CWidget {

    public $albumWidth = 50; // Ширина миниатюры для альбома
    public $albumHeight = 34; // Ширина миниатюры для альбома
    public $duration; // Время хранения кеша в секундах (24 часа)
    private $userAlbumNumber = 7298; // Фиксированный ID корня для пользовательских альбомов

    public function run() {
        $this->duration = Yii::app()->params['dCategories'];

        /*$categories = Album::model()  // Получить все альбомы
        ->with('cover')
        ->findAll(array(
            'condition' => '`'.Album::model()->tableAlias.'`.parent_id = 0 AND '
                          .'`'.Album::model()->tableAlias.'`.id <> '.$this->userAlbumNumber,
            'order'  => '`'.Album::model()->tableAlias.'`.sort',
        ));*/
        $categories = Yii::app()->db
            ->createCommand('
                SELECT a.*, i.width, i.height FROM album a
                LEFT JOIN item i ON i.id = a.thumbnail_id
                WHERE a.parent_id = 0 AND a.id <> ' . $this->userAlbumNumber . '
                ORDER BY a.sort
            ')
            ->queryAll();

        $this->render('categories', array(
            'name' => 'categories',
            'path' => Yii::app()->params['pathThumbnail'],
            'categories' => $categories,
            'duration'   => $this->duration,
            'albumWidth' => $this->albumWidth,
            'albumHeight'=> $this->albumHeight,
        ));
	}
}