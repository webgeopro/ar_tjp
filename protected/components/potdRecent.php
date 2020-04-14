<?php
/**
 * Виджет "Последние пазлы". POTD_RECENT
 * Работа с пазлами, верхняя часть страницы.
 */

class potdRecent extends CWidget {

    public $num;      // Количество пазлов в линейке
    public $duration; // Время хранения кеша в секундах (24 часа)86400
    public $itemWidth  = 130; // Ширина миниатюры
    public $itemHeight = 130; // Высота миниатюры

    public function run() {
        $this->duration = Yii::app()->params['dPotdRecent'];
        $this->num = Yii::app()->params['potdRecentNum']; // По умолчанию = 7
        $conn = Yii::app()->db; // Используем соединения для локального сервера.
        $limit = $this->num;    // Ограничение php 5.3

        $sqlRecent='
          SELECT item.*, album.componentUrl albumComponentUrl
          FROM album
          LEFT OUTER JOIN album_item
            ON album.id = album_item.album_id
          LEFT OUTER JOIN item
            ON (item.id = album_item.item_id AND item.dateCreated <= "'.date("Y-m-d").'")
          WHERE album.id = '.Yii::app()->params['potdAlbumID'].'
          ORDER BY item.dateCreated DESC
          LIMIT :limit;
        ';
        $comRecent = $conn->createCommand($sqlRecent); // Подготовим команду выборки

        $comRecent->bindParam(':limit', $limit, PDO::PARAM_INT);

        $potd['item'] = $comRecent->queryAll(); // Получить все записи

        $this->render('potd', array(
            'name' => 'potd_recent_'.$this->num,
            'path' => Yii::app()->params['pathThumbnail'],
            'potd' => $potd,
            'duration' => $this->duration,
            'size' => $this->itemWidth,
        ));
	}
}