<?php
/**
 * Корректирование.
 * Консольная команда.
 * Установка превью изображения и т.д.
 * Date: 15.02.13
 */
class CorrectCommand extends CConsoleCommand
{
    public $results = array(); // Массив обработанных пазлов
    public $errors = array(); // Массив ошибок
    public $defaultAction = 'index'; // Действие по умолчанию.
    public $rootNumber = 7; // Фиксированный ID Корневого альбома
    public $userAlbumNumber = 7298; // Фиксированный ID корня для пользовательских альбомов
    public $save = false;

    /**
     * Дефолтное действие
     */
    public function actionIndex()
    {
        echo "Choose actions: \n
                main: Main albums puzzles \n
            ";
    }

    /**
     * Предварительные настройки
     */
    public function init()
    {
        Yii::import('application.extensions.image.Image');
    }

    /**
     * Работа с основными пазлами (добавленными администраторами)
     */
    public function actionMain()
    {
        $cr = chr(10); // Символ перевода строки

        echo "$cr Start Main images correct.";

        $conn = Yii::app()->db;    // Используем соединения для локального сервера.
        //$connTJP =Yii::app()->db2; // Используем соединения для сервера TheJigsawPuzzles.com
        $cnt = 0;
        // ================================= Шаг 1. Получение списка основных пазлов
        echo "$cr Step 1. Select all puzzles";
        $sql = '
            SELECT item.*, item_attributes.author, album.componentUrl albumComponentUrl
            FROM album
            LEFT OUTER JOIN album_item
                ON (album.id = album_item.album_id)
            LEFT OUTER JOIN item
                ON (item.id = album_item.item_id)
            LEFT OUTER JOIN item_attributes
                ON (item.id = item_attributes.id)
            WHERE (album.parent_id = 0)
            GROUP BY item.id
            ORDER BY item.dateCreated DESC, item.id DESC
        '; //  AND album.id <> 7298
        $command=$conn->createCommand($sql);
        $items = $command->queryAll();

        // ================================= Шаг 2. Сохранение нового списка альбомов
        echo "$cr Step 2. Processing $cr";
        $cnt = 0; $i = 0; $cntAll = count($items); $step = round($cntAll / 20);

        foreach($items as $item) {
            $this->correctImage($item);
            if ( !(++$cnt % $step) ) print ' '.(++$i*5).'%'; // Выводим сообщение каждые 5%
        }

        echo "$cr Complete. ";
    }

    /**
     * Работа с пазлами пользовательских альбомов
     */
    public function actionUser()
    {
        $cr = chr(10);          // Символ перевода строки

        echo "$cr Start User images correct."; // ---- Шаг 1.
        $items = Yii::app()->db // Получаем все пазлы
            ->createCommand('
                SELECT item.*, item_attributes.author, album.componentUrl albumComponentUrl
                FROM album
                LEFT OUTER JOIN album_item
                    ON album.id = album_item.album_id
                LEFT OUTER JOIN item
                    ON item.id = album_item.item_id
                LEFT OUTER JOIN item_attributes
                    ON item.id = item_attributes.id
                WHERE (album.parent_id = 7298)
                GROUP BY item.id
                ORDER BY item.dateCreated DESC, item.id DESC
            ')
            ->queryAll(); //  AND album.owner_id=7059

        echo "$cr Step 2. Processing $cr"; // -------- Шаг 2.
        $cnt = 0; $i = 0; $cntAll = count($items); $step = round($cntAll / 20);

        foreach($items as $item) {
            $this->correctImage($item, true);
            if ($step AND !(++$cnt % $step) ) print ' '.(++$i*5).'%'; // Выводим сообщение каждые 5%
        }
        echo "$cr Complete. ";
    }

    /**
     * Работа с пазлами пользовательских альбомов
     */
    public function actionDelUserPuzzles()
    {
        $cr = chr(10);          // Символ перевода строки

        echo "$cr Start User images deleting."; // ---- Шаг 1.
        $items = Yii::app()->db // Получаем все пазлы
            ->createCommand('
                SELECT item.*, item_attributes.author, album.componentUrl albumComponentUrl
                FROM album
                LEFT OUTER JOIN album_item
                    ON album.id = album_item.album_id
                LEFT OUTER JOIN item
                    ON item.id = album_item.item_id
                LEFT OUTER JOIN item_attributes
                    ON item.id = item_attributes.id
                WHERE (album.parent_id = 7298 AND album.owner_id=7059)
                GROUP BY item.id
                ORDER BY item.dateCreated DESC, item.id DESC
            ')
            ->queryAll(); //  AND album.owner_id=7059

        echo "$cr Step 2. Processing $cr"; // -------- Шаг 2.
        $cnt = 0; $i = 0; $cntAll = count($items); $step = round($cntAll / 20);

        foreach($items as $item) {
            $this->deleteImages($item);
            if ($step AND !(++$cnt % $step) ) print ' '.(++$i*5).'%'; // Выводим сообщение каждые 5%
        }
        echo "$cr Complete. ";
    }

    /**
     * Заполнение пустых полей ширины и высоты
     */
    /*public function actionSize()
    {
        $items = Yii::app()->db // Пазлы с пустыми полями
            ->createCommand('
                SELECT * FROM item
                WHERE (width = 0) OR (height = 0)
            ')
            ->queryAll();

        foreach ($items as $item) {

        }
    }*/

    /**
     * Пост обработка
     */
    public function afterAction($action=null,$params=null, $exitCode=0)
    {
        if (count($this->errors)) // Если сформирован массив ошибок
            if ($this->save) { // Сохранить на диск

            } else {
                foreach ($this->errors as $id=>$err) {
                    list($path, $fullPath) = $err;
                    echo chr(10).$id."::".$path."::".$fullPath.' = Error';
                }
            }
        else echo chr(10)."No errors";

        if (count($this->results)) // Если сформирован...
            if ($this->save) { // Сохранить на диск

            } else {
                foreach ($this->results as $id=>$res) {
                    list($path, $fullPath) = $res;
                    echo chr(10).$id."::".$path."::".$fullPath.' = Action';
                }
            }
        else echo chr(10)."No actions";
    }


    /**
     * Создает ресайз изображения на диске.
     * Внутренняя.
     *
     * @param array $item Пазл
     * @param bool $isUser Флаг UA
     * @return bool
     */
    private function correctImage($item, $isUser=false)
    {
        list($imgFullName, $imgUrl) = CImageSize::getPath($item['id']);
        $prefix = Yii::app()->params['pathOS'];
        $suffix = '/'.$imgUrl.'/'.$imgFullName.'.jpg';
        $paths = array(
            array('pathOriginal',  'origSize'),
            array('pathWorked',    'workedSize'),
            array('pathThumbnail', 'thumbnailSize'),
        );
        if ($isUser) // Для пользовательских альбомов нет worked-ресайза (400x400)
            unset($paths[1]); // Убираем соответствующий элемент массива
        $source   = $prefix.Yii::app()->params['pathSource'].$suffix;
        $original = $prefix.Yii::app()->params['pathOriginal'].$suffix;

        foreach($paths as $path) { // Проходим по всем ресайзам
            $img = $prefix.Yii::app()->params[$path[0]].$suffix;
            if (!file_exists($img)) { // Создаем
                $target = $prefix.Yii::app()->params[$path[0]].'/'.$imgUrl;

                if (file_exists($source))       $file = $source;
                elseif (file_exists($original)) $file = $original;
                else {
                    $this->errors[$item['id']] = array($path[0], $img);
                    return false;
                }
                $this->ensureDirectory($target);
                try {
                    $image = new Image($file);
                    $image->resize(Yii::app()->params[$path[1]][0], Yii::app()->params[$path[1]][1], Image::AUTO);
                    @$image->save($img);
                } catch(Exception $e) {
                    $this->errors[$item['id']] = array('imageCreate', $target);
                }
                $this->results[$item['id']] = array($path[0], $target);
            }
        }
        return true;
    }

    /**
     * Удаление ресайзов конкретного пазла
     *
     * @param array $item Пазл
     * @param bool $all Флаг удаления исходного (source) изображения
     * @return bool
     */
    private function deleteImages($item, $all=false)
    {
        list($imgFullName, $imgUrl) = CImageSize::getPath($item['id']);
        $prefix = Yii::app()->params['pathOS'];
        $suffix = '/'.$imgUrl.'/'.$imgFullName.'.jpg';
        $paths = array(
            array('pathOriginal',  'origSize'),
            array('pathWorked',    'workedSize'),
            array('pathThumbnail', 'thumbnailSize'),
        );
        $source = $prefix.Yii::app()->params['pathSource'].$suffix;

        if (!file_exists($source))
            unset($paths[0]); // Если не сущ-ет source оставляем original
        if ($all)             // Установлен флаг принудительного удаления исходника
            unlink($source);  // Удаляем исходное изображение
        foreach($paths as $path) { // Проходим по всем ресайзам
            $img = $prefix.Yii::app()->params[$path[0]].$suffix;
            if (file_exists($img)) {
                unlink($img);
            }
        }
        return true;
    }

    /**
     * Добавление нового поля cut(enum)
     */
    public function actionCutAdd()
    {
        echo chr(10) . 'Insert rows. Start.';
        $items = Yii::app()->db
            ->createCommand('SELECT id, cutout FROM item')
            ->queryAll();
        $conn = Yii::app()->db;
        $com = $conn->createCommand('UPDATE item SET cut=:cutout WHERE id=:itemID');

        foreach ($items as $item) {
            $com->bindParam(':itemID', $item['id'], PDO::PARAM_INT);
            $com->bindParam(':cutout', $item['cutout'], PDO::PARAM_INT);
            $com->execute();
        }
        echo chr(10) . 'Insert rows. Finished.';
    }

    /**
     * Ищем в базе пустые значение width, height
     * По source / original находим размеры пазла и вставляем в таблицу
     */
    public function actionSize()
    {
        $conn = Yii::app()->db;
        $items = $conn
            ->createCommand('SELECT id FROM item WHERE width=0 OR height=0')
            ->queryColumn();
        /*$items = $conn // Тестовая корректировка
            ->createCommand('SELECT id FROM item WHERE owner_id=7059')
            ->queryColumn();*/
        //while ($itemID = $conn->queryScalar()) { // Проходим по каждому пазлу и считываем размер его изображения
        foreach($items as $itemID) { // Проходим по каждому пазлу и считываем размер его изображения
            list($imgFullName, $imgUrl) = CImageSize::getPath($itemID);
            $origPath = Yii::app()->params['pathOS']
                . Yii::app()->params['pathOriginal']
                . '/' . $imgUrl . '/' . $imgFullName
                .'.jpg';
            $sourcePath = Yii::app()->params['pathOS']
                . Yii::app()->params['pathSource']
                . '/' . $imgUrl . '/' . $imgFullName
                .'.jpg';
            if (file_exists($origPath))
                $size = getimagesize($origPath);
            elseif(file_exists($sourcePath))
                $size = getimagesize($sourcePath);

            if (!empty($size)) {
                $w = $size[0];
                $h = $size[1];
                $conn->createCommand("UPDATE item SET width=$w, height=$h WHERE id=$itemID") // Вставка в БД
                     ->execute();
            } else
                $this->errors[$itemID] = array('File_not_exists', $itemID);
        }
    }

    /**
     * Предварительные установки для main, user, delete
     * Не используется
     */
    private function doInit($item)
    {
        list($imgFullName, $imgUrl) = CImageSize::getPath($item['id']);
        $prefix = Yii::app()->params['pathOS'];
        $suffix = '/'.$imgUrl.'/'.$imgFullName.'.jpg';
        $paths = array(
            array('pathOriginal',  'origSize'),
            array('pathWorked',    'workedSize'),
            array('pathThumbnail', 'thumbnailSize'),
        );
        $source   = $prefix.Yii::app()->params['pathSource'].$suffix;
        $original = $prefix.Yii::app()->params['pathOriginal'].$suffix;
    }
}