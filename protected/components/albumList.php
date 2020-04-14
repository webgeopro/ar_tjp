<?php
/**
 * Виджет "Список пазлов альбома". albumList
 * Работа с пазлами альбома.
 */

class albumList extends CWidget {

    public $num = 5;      // Количество пазлов в столбце
    public $duration;     // Время хранения кеша в секундах (24 часа)
    public $album;        // Объект альбом
    public $albumUser;    // Объект "Владелец альбома"
    public $page = 1;     // Текущая страница
    public $limit = 50;   // Количество пазлов на странице
    public $itemWidth  = 130; // Ширина миниатюры для альбома
    public $itemHeight = 130; // Ширина миниатюры для альбома
    public $newPuzzles = false; // Отображение новых пазлов

    public function run()
    {
        $limit = $this->limit; // Тупость в php 5.2: __get() возвращает read mode а для foreach нужен write-mode
        $this->duration = Yii::app()->params['dAlbumList'];
        $connection = Yii::app()->db; // Соединение с БД. Параметры прописаны в config/main.php

        $date = (Yii::app()->getModule('user')->isAdmin()) ? null : "AND (item.dateCreated <= :data)"; // Админ видит все

        if ($this->newPuzzles) { // Отображаем новые пазлы
            $sql = '
                SELECT item.*, item_attributes.author, album.componentUrl albumComponentUrl
                FROM album
                LEFT OUTER JOIN album_item
                    ON (album.id = album_item.album_id)
                LEFT OUTER JOIN item
                    ON (item.id = album_item.item_id) '.$date.'
                LEFT OUTER JOIN item_attributes
                    ON (item.id = item_attributes.id)
                WHERE (album.parent_id = 0 AND album.id <> 7298)
                GROUP BY item.id
                ORDER BY item.dateCreated DESC, item.id DESC
                LIMIT :limit
            ';
            $command=$connection->createCommand($sql); // Подготовка sql
            if ($date) $command->bindValue(":data", date("Y-m-d")); // Подставляем текущую дату
            $command->bindParam(':limit', $limit, PDO::PARAM_INT); // Подставляем количество объектов на странице
            $items['item'] = $command->queryAll(); // Забрать все строки запроса
            $cacheName  = 'album_list_new_puzzles'; // Имя кешируемого объекта
            // Очистка кеша при + пазла
            $dependency = '
                SELECT MAX(item.id)
                FROM album
                LEFT OUTER JOIN album_item
                    ON (album.id = album_item.album_id)
                LEFT OUTER JOIN item
                    ON (item.id = album_item.item_id) AND (item.dateCreated <= "'.date("Y-m-d").'")
                WHERE (album.parent_id = 0 AND album.id <> 7298)
            ';

        } elseif (@$this->albumUser['id']) { //==== Пользовательский альбом ==============================================
            if (Yii::app()->getModule('user')->isAdmin()) // Админ видит все пазлы (с отл. публикацией)
                $allDates = '';
            elseif ($this->album['owner_id'] == Yii::app()->user->id) // Владелец альбома видит все пазлы (с отл. публикацией)
                $allDates = '';
            else
                $allDates = " AND (item.dateCreated <= '".date("Y-m-d")."')";
            //die(print_r($this->album));// die('UA');/* Формируем пагинатор */
            $cnt = CAlbumUtils::getItemsCount($this->album['id']); // Общее кол-во записей
            $pages = new CPagination($cnt); // Страницы для пагинатора
            $pages->pageVar = 'g2_page';    // Название ссылки для GET-запроса
            $pages->pageSize = $limit;      // Количество пазлов на странице
            $offset = $pages->currentPage * $pages->pageSize; // Смещение в запросе
            /* Выборка пазлов альбома с учетом пагинации */
            $items['item'] = Yii::app()->db
                ->createCommand('
                    SELECT item.* /*, ia.author*/
                    FROM album_item ai
                    LEFT JOIN item
                      ON item.id = ai.item_id '.$allDates.'
                    /*LEFT JOIN item_attributes ia
                      ON ia.id = item.id*/
                    WHERE ai.album_id = :albumID
                    ORDER BY item.dateCreated DESC, item.id DESC
                    LIMIT :offset, :limit')
                ->bindParam(':albumID', $this->album['id'], PDO::PARAM_INT)
                ->bindParam(':offset', $offset, PDO::PARAM_INT)
                ->bindParam(':limit', $limit, PDO::PARAM_INT)
                ->queryAll();

            $cacheName  = 'useralbum_list_'.$this->albumUser['id'].'_'.$this->num.'_'.$pages->currentPage; // Имя кешируемого объекта
            $this->album['componentUrl'] = "User-Albums/".$this->album['componentUrl']; // Формирование url
            //$this->album['owner_id'] = $this->albumUser['id']; // Для getActions

            $dependency = 'SELECT MAX(id) FROM item WHERE item.owner_id = '.$this->albumUser['id']; // Очистка кеша при + пазла
//die(print_r($this->album));
        } else { //==== Один из основных альбом ===================================================================
            //die(print_r($this->album));//('albumList main Albums');
            $url = $this->album['componentUrl']; // Для удобства использования
            $albumID = $this->album['id']; // Для удобства использования
            /* Запрос к БД для пагинатора. Отношение ManyToMany через таблицу album_item
             * Запрос кешируем встроенными средствами MySQL */
            $sql = '
                SELECT COUNT(item.id)
                FROM album
                LEFT OUTER JOIN album_item
                    ON (album.id = album_item.album_id)
                LEFT OUTER JOIN item
                    ON (item.id = album_item.item_id) '.$date.'
                WHERE (album.componentUrl = :albumUrl)
            ';
            $command=$connection->createCommand($sql); // Подготовка sql
            $command->bindParam(":albumUrl", $url, PDO::PARAM_STR); // Подставляем уникальный url альбома
            $nowdate = date("Y-m-d") . ' 23:59:59';
            if ($date) // Подставляем текущую дату
                $command->bindParam(":data", $nowdate, PDO::PARAM_STR);
            $cnt = $command->queryScalar(); // Получаем количество пазлов в альбоме с учетом dateCreated
            // Формируем пагинатор
            $pages = new CPagination($cnt); // Страницы для пагинатора
            $pages->pageVar = 'g2_page';    // Название ссылки для GET-запроса
            $pages->pageSize = $limit;      // Количество пазлов на странице
            $offset = $pages->currentPage * $pages->pageSize; // Смещение в запросе
            // Формируем массив пазлов с учетом пагинатора
            // Добавочная информация о пазле хранится в таблице item_attributes. Отношение HasOne
            $sql = '
                SELECT item.*, item_attributes.author, album.componentUrl albumComponentUrl
                FROM album
                LEFT OUTER JOIN album_item
                    ON (album.id = album_item.album_id)
                LEFT OUTER JOIN item
                    ON (item.id = album_item.item_id) '.$date.'
                LEFT OUTER JOIN item_attributes
                    ON (item.id = item_attributes.id)
                WHERE (album.id = :albumID)
                ORDER BY item.dateCreated DESC, item.id DESC
                LIMIT :offset, :limit
            '; // WHERE (album.componentUrl = :albumUrl)
            $command=$connection->createCommand($sql);  // Подготовка sql
            //$command->bindParam(":albumUrl", $url, PDO::PARAM_STR); // Подставляем уникальный url альбома.
            $command->bindParam(":albumID", $albumID, PDO::PARAM_INT); // Подставляем уникальный url альбома.
            if ($date)
                $command->bindParam(":data", $nowdate, PDO::PARAM_STR);
                //$command->bindValue(":data", date("Y-m-d")); // Подставляем текущую дату
            $command->bindParam(':offset', $offset, PDO::PARAM_INT); // Подставляем смещение
            $command->bindParam(':limit', $limit, PDO::PARAM_INT); // Подставляем количество объектов на странице
            $items['item'] = $command->queryAll(); // Забрать все строки запроса с учетом пагинатора

            $cacheName  = 'album_list_'.$url.'_'.$this->num.'_'.$pages->currentPage;   // Имя кешируемого объекта
        }
        $this->render('albumList', array(
            'name'  => $cacheName,   // Имя кешируемого объекта
            'path'  => Yii::app()->params['pathThumbnail'], // Путь к миниатюрам
            'items' => $items,                              // Массив пазлов альбома с учетом пагинатора
            'num'   => $this->num,                 // Количество пазлов в столбце
            'duration' => $this->duration,         // Длительность хранения кеша
            'tdWidth'  => floor(100 / $this->num), // Ширина ячейки таблицы в %
            'pages' => @$pages,                     // Объект Пагинатор
            'album' => $this->album,               // Объект Альбом
            'itemWidth' => $this->itemWidth,
            'itemHeight'=> $this->itemHeight,
            'dependency'=> empty($dependency) ? null : $dependency,
        ));
	}
}