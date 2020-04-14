<?php
/**
 * Виджет "Пазл дня". POTD_CURRENT
 * Работа с пазлами, верхняя часть страницы.
 * 
 */

class potdCurrent extends CWidget {

    public $duration; // Время хранения кеша в секундах (24 часа)86400
    public $itemWidth  = 400; // Ширина изображения
    public $itemHeight = 400; // Высота изображения
    public $albumName  = ''; // Высота изображения

    public function run()
    {
        $this->duration = Yii::app()->params['dPotdCurrent'];
        $conn = Yii::app()->db; // Используем соединения для локального сервера.
        $sql = '
            SELECT item.*, album.componentUrl albumComponentUrl
            FROM item
            LEFT JOIN album_item
              ON (item.id = album_item.item_id)
            INNER JOIN album
              ON (album_item.album_id = album.id)
            WHERE
                (item.inSearch=1)
                AND (item.dateCreated <= "'.date("Y-m-d").'" AND album.id <> '.Yii::app()->params['potdAlbumID'].')
            ORDER BY item.dateCreated DESC
            LIMIT 1
        ';//:dateNow AND album.title = "Puzzle of the Day" . LEFT JOIN album
        $itemMain = $conn->createCommand($sql)->queryRow(); // Подготовим команду выборки
        $itemAttr = $conn
            ->createCommand('SELECT author, description FROM item_attributes WHERE id='.$itemMain['id'])
            ->queryRow();

        $puzzles['item'][] = array_merge($itemMain, $itemAttr);

        $this->render('potd', array(
            'name' => 'potd_current',
            'path' => Yii::app()->params['pathWorked'],
            'potd' => $puzzles,
            'duration'   => $this->duration,
            'itemWidth'  => $this->itemWidth,
            'itemHeight' => $this->itemHeight,
            'size' => $this->itemWidth,
            'viewCutout' => true, // Отображать ли нарезку
        ));
	}
}