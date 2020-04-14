<?php
/**
 * Виджет "Список соседних альбомов". albumSiblings
 * Отображение "соседей" выбранного альбома.
 */

class albumSiblings extends CWidget {

    public $num = 3;      // Количество миниатюр альбомов в строке
    public $duration ;    // Время хранения кеша в секундах (24 часа)
    public $album;        // Объект альбом
    public $itemWidth  = 50; // Ширина миниатюры для альбома
    public $itemHeight = 50; // Высота миниатюры для альбома
    public $limit = 15;      // Количество миниатюр альбомов для показа
    public $admin = false;   // Флаг администрирования
    public $puzzlePage = false; // Отображение соседей пазла

    public function run() {
        $this->duration = Yii::app()->params['dAlbumSiblings'];

        if ( $this->admin AND Yii::app()->getModule('user')->isAdmin() ) { // Администрирование. Обновление статики.
            echo Yii::app()->cache->get('album_siblings');
            $fullKey = 'Yii.COutputCache.'.'album_siblings'.'..'.Yii::app()->controller->id.'/'.Yii::app()->controller->action->id.'....';
            die('Обновление статики-'.Yii::app()->controller->id.' :: '.Yii::app()->controller->action->id);
            $duration = -1;
        }
        if ( isset($this->album['parent_id']) AND
                   Yii::app()->params['userAlbumID'] != $this->album['parent_id']) { // Отображение соседей

            if($this->puzzlePage) { // Отображается пазл ==========================================================
                // Отображение конкретного для данного альбома набора пазлов - соседей
                if ( isset($this->album['parent_id']) //Если НЕпользовательский альбом, показываем соседей
                     AND Yii::app()->params['userAlbumID'] != $this->album['parent_id'] ):

                    if (Yii::app()->getModule('user')->isAdmin()) // Админ видит все пазлы (с отл. публикацией)
                        $allDates = '';
                    else
                        $allDates = " AND (item.dateCreated <= '".date("Y-m-d")."')";
                    $url = isset($this->album->componentUrl) ? $this->album->componentUrl : $this->album['componentUrl'];
                    $limit = $this->limit;

                    $connection=Yii::app()->db; // Соединение с БД. Параметры прописаны в config/main.php
                    // Добавочная информация о пазле хранится в таблице item_attributes. Отношение HasOne
                    $sql = '
                        SELECT item.*
                        FROM album
                        LEFT OUTER JOIN album_item
                          ON (album_item.album_id = album.id)
                        LEFT OUTER JOIN item
                          ON (album_item.item_id= item.id)
                        WHERE album.componentUrl = :albumUrl '.$allDates.'
                        ORDER BY item.dateCreated DESC
                        LIMIT :limit
                    '; //:albumParentId
                    $command=$connection->createCommand($sql);  // Подготовка sql
                    $command->bindParam(":albumUrl", $url, PDO::PARAM_STR); // Подставляем уникальный url альбома.
                    $command->bindParam(":limit", $limit, PDO::PARAM_INT); // Подставляем уникальный url альбома.
                    $items['item'] = $command->queryAll(); // Забрать все строки запроса с учетом пагинатора

                    $this->render('albumSiblings', array(
                        'name'  => 'album_siblings_'.$this->album['id'], // Имя кешируемого объекта
                        'items' => $items, // Массив пазлов альбома с учетом пагинатора
                        'albumUrl'   => $url, // componentUrl альбома
                    ));
                endif;

            } else { // Отображается альбом. Один файл для всех альбомов ==========================================
                //$items['item'] = Album::model()->siblings()->with('item')->findAll();// Отображение миниатюр пазлов последних 15 добавленных альбомов
                $conn = Yii::app()->db;
                // Для убыстрения работы убираем контроль сущ-я thumb
                $items['item'] = $conn
                    ->createCommand('
                        SELECT
                          a.id, a.thumbnail_id, a.componentUrl, a.title, i.id,
                          i.cut, i.id itemId, i.width, i.height
                        FROM album a
                        LEFT JOIN item i
                          ON i.id = a.thumbnail_id
                        WHERE a.parent_id = 0 AND a.id <> 7298
                        ORDER BY a.sort
                        LIMIT 15')
                    ->queryAll(); // , a.id DESC
                    //->queryColumn(); //$albums = '(' . implode(',', $albums) . ')';

                $this->render('albumSiblings', array(
                    'name'  => 'album_siblings', // Имя кешируемого объекта
                    'items' => $items,           // Массив пазлов альбома с учетом пагинатора
                ));
            }
        }
	}
}