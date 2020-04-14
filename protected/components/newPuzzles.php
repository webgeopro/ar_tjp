<?php
/**
 * Виджет "Новые пазлы". newPuzzles
 * Работа с пазлами, верхняя часть страницы.
 */

class newPuzzles extends CWidget {

    public $num = 6; // Количество пазлов в столбце
    public $duration; // Время хранения кеша в секундах (24 часа)
    public $itemWidth  = 130; // Ширина миниатюры
    public $itemHeight = 130; // Высота миниатюры

    public function run() {
        $this->duration = Yii::app()->params['dNewPuzzles'];
        $conn = Yii::app()->db; // Используем соединения для локального сервера.
        $limit = $this->num;    // Ограничение php 5.3
        $sql = '
            SELECT DISTINCT item.*, attr.author, attr.description, album.componentUrl albumComponentUrl
            FROM item
            LEFT JOIN album_item
              ON (item.id = album_item.item_id)
            LEFT JOIN album
              ON (album_item.album_id = album.id)
            LEFT JOIN item_attributes attr
              ON (item.id = attr.id)
            WHERE  album.parent_id = 0
                   AND album.id <> '.Yii::app()->params['userAlbumID'].'
                   AND album.id <> '.Yii::app()->params['potdAlbumID'].'
                   AND item.dateCreated <= "'.date("Y-m-d").'"
            GROUP BY item.id
            ORDER BY item.dateCreated DESC
            LIMIT :limit
        ';
        $com = $conn->createCommand($sql); // Подготовим команду выборки

        //$com->bindValue(':dateNow', date("Y-m-d"), PDO::PARAM_STR);
        $com->bindParam(':limit', $limit, PDO::PARAM_INT);

        $puzzles['item'] = $com->queryAll(); // Получить все записи

        $this->render('potd', array(
            'name'=> 'new_puzzles_'.$this->num,
            'path' => Yii::app()->params['pathThumbnail'],
            'potd'=> $puzzles,
            'duration'   => $this->duration,
            'itemWidth'  => $this->itemWidth,
            'itemHeight' => $this->itemHeight,
            'viewCutout' => true,
            'size' => $this->itemWidth,
        ));
	}
}