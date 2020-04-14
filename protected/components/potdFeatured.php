<?php
/**
 * Виджет "Случайный пазл". POTD_FEATURED
 * Отображение одного из 30 кешированных пазлов. Случайное отображение со всей базы
 * 
 */

class potdFeatured extends CWidget {

    public $duration;      // Время хранения кеша в секундах (24 часа)
    public $randomNum;     // Случайное число от 1 до $this->num (30)
    public $admin = false; // Флаг администрирования
    public $num = 30;      // Кол-во генерируемых файлов

    public function run() {
        $this->duration = Yii::app()->params['dPotdFeatured'];
        if ( $this->admin AND Yii::app()->getModule('user')->isAdmin() ) { // Администрирование. Обновление статики.
            $conn = Yii::app()->db; // Используем соединения для локального сервера.
            $limit = $this->num;    // Ограничение php 5.3
            // Случайная выборка 30 пазлов из основных альбомов
            $sqlFeatured='
                SELECT item.*, album.componentUrl albumComponentUrl, attr.*
                FROM album
                LEFT OUTER JOIN album_item
                  ON album.id = album_item.album_id
                INNER JOIN item
                  ON (item.id = album_item.item_id AND item.dateCreated <= "'.date("Y-m-d").'")
                LEFT OUTER JOIN item_attributes attr
                  ON item.id = attr.id
                WHERE album.parent_id = 0 AND album.id <> 7298
                ORDER BY RAND()
                LIMIT :limit;
            ';
            $comFeatured = $conn->createCommand($sqlFeatured); // Подготовим команду выборки
            $comFeatured->bindParam(':limit', $limit, PDO::PARAM_INT);

            $fp = $comFeatured->queryAll(); // Получить все записи

            for ($i=1; $i <= $this->num; $i++) {
                $fileData = Yii::app()->controller->renderPartial('featured_puzzle_template', array(
                    'item'  => $fp[$i-1],
                ), true);
                $fileName = Yii::app()->params['pathOS'].Yii::app()->params['pathStatic'].'/featured_puzzle'.$i;
                file_put_contents($fileName, $fileData);
                // Сохранять конкретные ошибки создания статических пазлов
            }

        } else { // Обычная работа по рендерингу статического блока
            $this->randomNum = rand(1, $this->num);
            $this->renderFile('./././items/static/featured_puzzle'.$this->randomNum);
        }
	}
}