<?php
/**
 * Items Migrations (Command line: yiic im)
 * Консольная команда. Перенос пазлов из движка Gallery2 в новое приложение. (Без копирования файлов)
 * Date: 16.07.12
 */
class ImCommand extends CConsoleCommand
{
    private $rootNumberTJP = 7;      // Фиксированный ID Корневого альбома (для Gallery2)
    private $rootNumber = 0;         // Фиксированный ID Корневого альбома (для нового приложения)
    private $userAlbumNumber = 7298; // Фиксированный ID корня для пользовательских альбомов
    protected $file = 'select_result.json'; // Файл в который сохраняется массив результатов SELECT

    /**
     * Шаг 1.
     * Выбор из базы старого сайта всех записей пазлов
     */
    public function actionSelect()
    {
        $cr = chr(10); // Символ перевода строки
        $connTJP =Yii::app()->db2; // Используем соединения для сервера TheGigsawPuzzles.com

        echo "$cr Start Items migration. "; // Сообщение о начале работы скрипта
        echo "$cr Step 1. Select puzzles"; // Шаг 1. Получение списка пазлов для администраторских альбомов

        $sqlPuzzles = '
            SELECT ent.g_id id, item_attr.g_viewCount countView, item.g_ownerId owner_id, item.g_title title,
                der_image.g_width width, der_image.g_height height,
                item.g_description description, item.g_keywords keywords,
                FROM_UNIXTIME(item.g_originationTimestamp) dateOriginal,
                FROM_UNIXTIME(ent.g_creationTimestamp) dateCreated,
                FROM_UNIXTIME(ent.g_modificationTimestamp) dateModified,
                MID(item_attr.g_parentSequence, 3, LENGTH(item_attr.g_parentSequence)-3) parent_id,
                file.g_pathComponent componentUrl,
                custom_author.g_value author, cutout.id cutout,
                usr.g_userName username
            FROM g2_Entity ent
            LEFT JOIN g2_Item item
                ON (ent.g_id = item.g_id)
            LEFT JOIN g2_ItemAttributesMap item_attr
                ON (ent.g_id = item_attr.g_itemId)
            LEFT JOIN g2_Derivative der
                ON (ent.g_id = der.g_derivativeSourceId)
            LEFT JOIN g2_DerivativeImage der_image
                ON (der_image.g_id = der.g_id)
            LEFT JOIN g2_CustomFieldMap custom_author
                ON (ent.g_id = custom_author.g_itemId AND custom_author.g_field="Author")
            LEFT JOIN g2_CustomFieldMap custom_cutout
                ON (ent.g_id = custom_cutout.g_itemId AND custom_cutout.g_field="Cutout")
            LEFT JOIN g2_FileSystemEntity file
                ON (item.g_id = file.g_id)
            LEFT JOIN g2_User usr
                ON (item.g_ownerId = usr.g_userName)
            LEFT JOIN cutout
                ON (custom_cutout.g_value = cutout.name)
            WHERE ent.g_entityType="GalleryPhotoItem"
            GROUP BY ent.g_id
            '; //LIMIT 100 AND der.g_derivativeOperations = "scale|1024,1024" AND item.g_ownerId = 7059

        $comP = $connTJP->createCommand($sqlPuzzles); // Подготовим команду выборки пазлов основных альбомов
        $puzzles = $comP->queryAll(); // Получаем все записи

        echo "$cr Step 2. Save file";

        if (null == $puzzles) // Если массив пуст - выдаем ошибку
            echo $cr.'Step 1 error.';
        else // Сохраняем в файл
            $this->arraySaveToFile($puzzles);

        echo "$cr IM Selected finished";
    }

    /**
     * Сохранение выбранного масива записей в файл
     */
    protected function arraySaveToFile($items)
    {
        if (null == $items) return false;

        return file_put_contents(
            $this->file,
            json_encode($items)
        );
    }

    /**
     * Запись в базу пазлов из сохраненного файла
     */
    public function actionInsert()
    {
        if (!file_exists($this->file)) echo "Файл записей не найден";

        $cr = chr(10); // Символ перевода строки

        $puzzles = json_decode(file_get_contents($this->file), true);

        echo "$cr Step 2. Insert puzzles $cr"; // Шаг 2. Обработка списка пазлов
        $cnt = 0; $cntAll = count($puzzles); $step = round($cntAll / 20); //  Параметры для вывода процесса работы
        echo "$cr count=$cntAll. $cr";

        $connNew = Yii::app()->db;    // Используем соединения для локального сервера.

        $sqlInsertItem = 'REPLACE INTO item
            (id, owner_id, title, cutout, dateCreated, componentUrl, countView, width, height, cut)
            VALUES (:id, :owner_id, :title, :cutout, :dateCreated, :componentUrl, :countView, :width, :height, :cutout)
        ';
        $sqlInsertItemAttr  = 'REPLACE INTO item_attributes
            (id, keywords, description, author, dateModified, dateImageCreated)
            VALUES (:id, :keywords, :description, :author, :dateModified, :dateImageCreated)
        ';
        $sqlInsertAlbumItem = 'REPLACE INTO album_item VALUES(:album_id, :item_id)';

        $comItem      = $connNew->createCommand($sqlInsertItem);
        $comItemAttr  = $connNew->createCommand($sqlInsertItemAttr);
        $comAlbumItem = $connNew->createCommand($sqlInsertAlbumItem);

        foreach ($puzzles as $val) { // Обработка
            $parent = explode('/', $val['parent_id']);

            // запись в базу --------------------------------------------------
            $comItem->bindParam(':id', $val['id'], PDO::PARAM_INT);
            $comItem->bindParam(':owner_id', $val['owner_id'], PDO::PARAM_INT);
            $comItem->bindParam(':title', $val['title'], PDO::PARAM_STR);
            $comItem->bindParam(':cutout', $val['cutout'], PDO::PARAM_INT);
            $comItem->bindParam(':dateCreated', $val['dateCreated'], PDO::PARAM_STR);
            $comItem->bindParam(':countView', $val['countView'], PDO::PARAM_INT);
            $comItem->bindParam(':width', $val['width'], PDO::PARAM_INT);
            $comItem->bindParam(':height', $val['height'], PDO::PARAM_INT);
            // запись в таблицы атрибутов -------------------------------------
            $comItemAttr->bindParam(':id', $val['id'], PDO::PARAM_INT);
            $comItemAttr->bindParam(':keywords', $val['keywords'], PDO::PARAM_STR);
            $comItemAttr->bindParam(':description', $val['description'], PDO::PARAM_STR);
            $comItemAttr->bindParam(':author', $val['author'], PDO::PARAM_STR);
            $comItemAttr->bindParam(':dateModified', $val['dateModified'], PDO::PARAM_STR);
            $comItemAttr->bindValue(':dateImageCreated', $val['dateOriginal'], PDO::PARAM_STR); //$dateNow
            // запись в таблицу связей ----------------------------------------
            $comAlbumItem->bindParam(':item_id', $val['id'], PDO::PARAM_INT);

            if ($this->userAlbumNumber == $parent[0] AND @$parent[1]) // Вставка в пользовательский альбом
                $comAlbumItem->bindParam(':album_id', $parent[1], PDO::PARAM_INT);
            elseif ($parent[0]) // Вставка в основной альбом
                $comAlbumItem->bindParam(':album_id', $parent[0], PDO::PARAM_INT);

            $componentUrl = CItemUtils::componentUrl($val['componentUrl']); // Формируем componentUrl

            $comItem->bindParam(':componentUrl', $componentUrl, PDO::PARAM_STR);

            $transaction = $connNew->beginTransaction(); // Открываем транзацию
            try {
                $comItem->execute();      // Вставка пазла
                $comAlbumItem->execute(); // Вставка связи пазла с альбомов
                $comItemAttr->execute();  // Вставка атрибутов пазла
            } catch (Exception $e) {
                $transaction->rollBack(); // Откатываем назад в случае неудачи
                echo ' ='.$cnt++.'-'.$val['id'].'= '; // Выводим уведомление на экран
                Yii::log($e, 'trace', 'system.console.CConsoleCommand'); // Логируем ошибки
            }
            $transaction->commit(); // Завершаем транзакцию если нет ошибок
            if ( !(++$cnt % $step) ) echo " #"; // Выводим сообщение каждые 5%
        }
    }

    /**
     * Шаг 3. Шаг 4.
     * Установка миниатюр альбомов.
     * Исправление некорректных дат.
     */
    public function actionCorrect()
    {
        $cr = chr(10); // Символ перевода строки
        date_default_timezone_set('America/Denver');
        $conn = Yii::app()->db; // Вновь устанавливаем соединение с БД

        echo "$cr Step 3. Insert thumbnail. Insert number of puzzles $cr";

        $sqlAlbumItem = '
            SELECT DISTINCT album_id, max(item_id) thumbnail_id, COUNT(item_id) cnt
            FROM album_item GROUP BY album_id';
        $sqlUpdateAlbum = 'UPDATE album SET thumbnail_id=:thumbnail_id, cnt= :cnt WHERE id=:id';
        $comAlbumItem = $conn->createCommand($sqlAlbumItem);
        $comAlbum = $conn->createCommand($sqlUpdateAlbum);
        try {
            $thumbs = $comAlbumItem->queryAll();
        } catch (Exception $e) {
            die ($cr.'Step 3 error: '.$e);
        }
        $cnt = 0; $cntAll = count($thumbs); $step = round($cntAll / 20);
        foreach ($thumbs as $th) {
            $comAlbum->bindParam(':id', $th['album_id'], PDO::PARAM_INT);
            $comAlbum->bindParam(':thumbnail_id', $th['thumbnail_id'], PDO::PARAM_INT);
            $comAlbum->bindParam(':cnt', $th['cnt'], PDO::PARAM_INT);
            try {
                $comAlbum->execute(); // Обновление thumbnail_id
            } catch (Exception $e) {
                echo ' ='.$cnt++.'-'.$th['album_id'].'= '; // Выводим уведомление на экран
            }

            if ( !(++$cnt % $step) ) echo " #"; // Выводим сообщение каждые 5%
        }

        echo "$cr Step 4. Correct dates. $cr"; // Исправленне некорректных дат создания изображений.

        $now = date('Y-m-d');

        $sqlUpdate = 'UPDATE `item_attributes` SET dateImageCreated="'.$now.'"
                      WHERE ( dateImageCreated IS NULL OR dateImageCreated >"'.$now.'")';
        $comUpdate = $conn->createCommand($sqlUpdate);
        $comUpdate->execute();
    }


    /**
     * Действие по умолчанию.
     * Отключено
     */
    public function Index()
    {
        $cr = chr(10); // Символ перевода строки
        Yii::beginProfile('ItemsMigration');   // Профилирование производительности
        echo "$cr Start Items migration. "; // Сообщение о начале работы скрипта

        //$conn = Yii::app()->db;    // Используем соединения для локального сервера.
        $connTJP =Yii::app()->db2; // Используем соединения для сервера TheGigsawPuzzles.com

        // ================================= Шаг 1. Получение списка пазлов для администраторских альбомов
        echo "$cr Step 1. Select puzzles";

        $sqlPuzzles = '
            SELECT ent.g_id id, item_attr.g_viewCount countView, item.g_ownerId owner_id, item.g_title title,
                der_image.g_width width, der_image.g_height height,
                item.g_description description, item.g_keywords keywords,
                FROM_UNIXTIME(item.g_originationTimestamp) dateOriginal,
                FROM_UNIXTIME(ent.g_creationTimestamp) dateCreated,
                FROM_UNIXTIME(ent.g_modificationTimestamp) dateModified,
                MID(item_attr.g_parentSequence, 3, LENGTH(item_attr.g_parentSequence)-3) parent_id,
                file.g_pathComponent componentUrl,
                custom_author.g_value author, cutout.id cutout,
                usr.g_userName username
            FROM g2_Entity ent
            LEFT JOIN g2_Item item
                ON (ent.g_id = item.g_id)
            LEFT JOIN g2_ItemAttributesMap item_attr
                ON (ent.g_id = item_attr.g_itemId)
            LEFT JOIN g2_Derivative der
                ON (ent.g_id = der.g_derivativeSourceId)
            LEFT JOIN g2_DerivativeImage der_image
                ON (der_image.g_id = der.g_id)
            LEFT JOIN g2_CustomFieldMap custom_author
                ON (ent.g_id = custom_author.g_itemId AND custom_author.g_field="Author")
            LEFT JOIN g2_CustomFieldMap custom_cutout
                ON (ent.g_id = custom_cutout.g_itemId AND custom_cutout.g_field="Cutout")
            LEFT JOIN g2_FileSystemEntity file
                ON (item.g_id = file.g_id)
            LEFT JOIN g2_User usr
                ON (item.g_ownerId = usr.g_userName)
            LEFT JOIN cutout
                ON (custom_cutout.g_value = cutout.name)
            WHERE ent.g_entityType="GalleryPhotoItem" AND der.g_derivativeOperations = "scale|1024,1024"
            /*ORDER BY der.g_derivativeSourceId ASC, der.g_derivativeSize DESC */
            /*LIMIT 100 der.g_derivativeOperations = "scale|1024,1024"  der.g_derivativeType = 2*/
            ';// AND item_attr.g_parentSequence LIKE "/'.$this->rootNumberTJP.'/'.$this->userAlbumNumber.'/%"
            //CAST(custom_cutout.g_value AS UNSIGNED [Используем нарезку целиком {50 piece Mosaic}]
        $comP = $connTJP->createCommand($sqlPuzzles); // Подготовим команду выборки пазлов основных альбомов
        try {
            $puzzles = $comP->queryAll();
        } catch (Exception $e) {
            die ($cr.'Step 1 error: '.$e);
        }

        // ================================= Шаг 2. Обработка списка пазлов
        echo "$cr Step 2. Insert puzzles $cr";//die(print_r($puzzles));//die('\n cnt='.count($puzzles));

        $cnt = 0; $cntAll = count($puzzles); $step = round($cntAll / 20); //  Параметры для вывода процесса работы
        //$dateNow = date('Y-m-d', strtotime("yesterday")); // Ставим датой публикации вчерашний день
        echo "$cr count=$cntAll. $cr";

        $sqlInsertItem = 'REPLACE INTO item
            (id, owner_id, title, cutout, dateCreated, componentUrl, countView, width, height)
            VALUES (:id, :owner_id, :title, :cutout, :dateCreated, :componentUrl, :countView, :width, :height)
        ';
        $sqlInsertItemAttr  = 'REPLACE INTO item_attributes
            (id, keywords, description, author, dateModified, dateImageCreated)
            VALUES (:id, :keywords, :description, :author, :dateModified, :dateImageCreated)
        ';
        $sqlInsertAlbumItem = 'REPLACE INTO album_item VALUES(:album_id, :item_id)';
        //$sqlInsertItemImage = 'REPLACE INTO item_image VALUES(:item_id, :image_id)';
        $connNew = Yii::app()->db;    // Используем соединения для локального сервера.

        $comItem      = $connNew->createCommand($sqlInsertItem);
        $comItemAttr  = $connNew->createCommand($sqlInsertItemAttr);
        $comAlbumItem = $connNew->createCommand($sqlInsertAlbumItem);
        //$comItemImage = $connNew->createCommand($sqlInsertItemImage);

        foreach ($puzzles as $val) { // Обработка
            $parent = explode('/', $val['parent_id']);

            // запись в базу --------------------------------------------------
            $comItem->bindParam(':id', $val['id'], PDO::PARAM_INT);
            $comItem->bindParam(':owner_id', $val['owner_id'], PDO::PARAM_INT);
            $comItem->bindParam(':title', $val['title'], PDO::PARAM_STR);
            $comItem->bindParam(':cutout', $val['cutout'], PDO::PARAM_INT);
            $comItem->bindParam(':dateCreated', $val['dateCreated'], PDO::PARAM_STR);
            $comItem->bindParam(':countView', $val['countView'], PDO::PARAM_INT);
            $comItem->bindParam(':width', $val['width'], PDO::PARAM_INT);
            $comItem->bindParam(':height', $val['height'], PDO::PARAM_INT);
            // запись в таблицы атрибутов -------------------------------------
            $comItemAttr->bindParam(':id', $val['id'], PDO::PARAM_INT);
            $comItemAttr->bindParam(':keywords', $val['keywords'], PDO::PARAM_STR);
            $comItemAttr->bindParam(':description', $val['description'], PDO::PARAM_STR);
            $comItemAttr->bindParam(':author', $val['author'], PDO::PARAM_STR);
            $comItemAttr->bindParam(':dateModified', $val['dateModified'], PDO::PARAM_STR);
            $comItemAttr->bindValue(':dateImageCreated', $val['dateOriginal'], PDO::PARAM_STR); //$dateNow
            // запись в таблицу связей ----------------------------------------
            $comAlbumItem->bindParam(':item_id', $val['id'], PDO::PARAM_INT);

            if ($this->userAlbumNumber == $parent[0] AND @$parent[1]) { // Вставка в пользовательский альбом
                $comAlbumItem->bindParam(':album_id', $parent[1], PDO::PARAM_INT);
                //$componentUrl = $val['username'];
            } elseif ($parent[0]) { // Вставка в основной альбом
                $comAlbumItem->bindParam(':album_id', $parent[0], PDO::PARAM_INT);
                //$componentUrl = str_replace(' &amp; ',' and ', $val['title']);
            }
            /*if ('' == $componentUrl)
                $componentUrl = CItemUtils::clearTitle($val['title'], true); // Формируем componentUrl*/
            $componentUrl = CItemUtils::componentUrl($val['componentUrl']); // Формируем componentUrl
            //$comItem->bindValue(':componentUrl', str_replace(' ', '+', $componentUrl), PDO::PARAM_STR);
            $comItem->bindParam(':componentUrl', $componentUrl, PDO::PARAM_STR);

            // @todo Отказаться от транзакции?
            $transaction = $connNew->beginTransaction(); // Открываем транзацию
            try {
                $comItem->execute();      // Вставка пазла
                $comAlbumItem->execute(); // Вставка связи пазла с альбомов
                $comItemAttr->execute();  // Вставка атрибутов пазла
                //$comItemImage->execute();  // Вставка связей пазла с изображением
            } catch (Exception $e) {
                $transaction->rollBack(); // Откатываем назад в случае неудачи
                echo ' ='.$cnt++.'-'.$val['id'].'= '; // Выводим уведомление на экран
                Yii::log($e, 'trace', 'system.console.CConsoleCommand'); // Логируем ошибки
            }
            $transaction->commit(); // Завершаем транзакцию если нет ошибок
            if ( !(++$cnt % $step) ) echo " #"; // Выводим сообщение каждые 5%
        }

        // ================================= Шаг 3. Установка миниатюр альбомов
        echo "$cr Step 3. Insert thumbnail. Insert number of puzzles $cr";
        $conn = Yii::app()->db; // Вновь устанавливаем соединение с БД
        $sqlAlbumItem = '
            SELECT DISTINCT album_id, max(item_id) thumbnail_id, COUNT(item_id) cnt
            FROM album_item GROUP BY album_id';
        $sqlUpdateAlbum = 'UPDATE album SET thumbnail_id=:thumbnail_id, cnt= :cnt WHERE id=:id';
        $comAlbumItem = $conn->createCommand($sqlAlbumItem);
        $comAlbum = $conn->createCommand($sqlUpdateAlbum);
        try {
            $thumbs = $comAlbumItem->queryAll();
        } catch (Exception $e) {
            die ($cr.'Step 3 error: '.$e);
        }
        $cnt = 0; $cntAll = count($thumbs); $step = round($cntAll / 20);
        foreach ($thumbs as $th) {
            $comAlbum->bindParam(':id', $th['album_id'], PDO::PARAM_INT);
            $comAlbum->bindParam(':thumbnail_id', $th['thumbnail_id'], PDO::PARAM_INT);
            $comAlbum->bindParam(':cnt', $th['cnt'], PDO::PARAM_INT);
            try {
                $comAlbum->execute(); // Обновление thumbnail_id
            } catch (Exception $e) {
                echo ' ='.$cnt++.'-'.$th['album_id'].'= '; // Выводим уведомление на экран
            }

            if ( !(++$cnt % $step) ) echo " #"; // Выводим сообщение каждые 5%
        }

        // ================================= Шаг 4. Исправленне некорректных дат создания изображений.
        echo "$cr Step 4. Correct dates. $cr";

        $now = date('Y-m-d');

        $sqlUpdate = 'UPDATE `item_attributes` SET dateImageCreated="'.$now.'"
                      WHERE ( dateImageCreated IS NULL OR dateImageCreated >"'.$now.'")';
        $comUpdate = $conn->createCommand($sqlUpdate);
        $comUpdate->execute();


        Yii::endProfile('ItemsMigration'); // Заканчиванием профилирование производительности
        echo "$cr End migration. "; // Сообщение об окончании работы скрипта
    }
}