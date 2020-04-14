<?php
/**
 * Date: 22.04.13
 * Различные методы (исправляющие, уточняющие, "забытые").
 */
class FinalCommand extends CConsoleCommand
{
    /**
     * Заполняет таблицу обратной совместимости со старыми внешними ссылками вида
     * http://thejigsawpuzzles.com/download/2251138-2/Walt-and-Mickey
     * Метод для Всех альбомов (основных и пользовательских)
     */
    public function actionExtLinks()
    {
        $cr = PHP_EOL; // Символ перевода строки

        $connTJP = Yii::app()->db2;    // Используем соединения для локального сервера.

        Yii::beginProfile('LinksMigration'); // Начало профилирования производительности
        echo "$cr Start External links corrections."; // Сообщение о начале работы скрипта
        // ================================= Шаг 1. Выборка пазлов основного альбома
        echo "$cr Step 1. Select items from main albums.";

        $sql = '
            SELECT ent.g_id id, der.g_id der_id, der.g_derivativeOperations der_type
            FROM g2_Entity ent
            LEFT JOIN g2_Derivative der
                ON (ent.g_id = der.g_derivativeSourceId)
            WHERE ent.g_entityType="GalleryPhotoItem"
            ';
        $com = $connTJP->createCommand($sql); // Подготовим команду выборки пазлов основных альбомов
        $puzzles = $com->queryAll(); // Выбор всех пазлов
        //file_put_contents('puzzles_ext_link.txt', var_export($puzzles, true));die("Stop");
        // ================================= Шаг 2. Заполнение вспомогательной таблицы ссылок
        $cnt = 0; $cntAll = count($puzzles); $step = round($cntAll / 20); $i = 0; //  Параметры для вывода процесса работы
        echo "$cr Count All = $cntAll.";
        echo "$cr Step 2. Inserting items.";

        $conn = Yii::app()->db;    // Используем соединения для локального сервера.

        $sqlInsert = 'REPLACE INTO external_links VALUES(:der_id, :item_id, :type_id)'; // Запрос на вставку
        $comInsert = $conn->createCommand($sqlInsert); // Подготовка запроса
        foreach ($puzzles as $item) { // Копирование исходных изображений
            switch ($item['der_type']) {
                case 'thumbnail|130': // thumbnail
                    $type = 1; break;
                case 'scale|400,400': // worked
                    $type = 2; break;
                case 'scale|1024,1024': // original
                    $type = 3; break;
                default: $type = 0;
            }
            // Подстановка данных
            $comInsert->bindParam(':der_id', $item['der_id'], PDO::PARAM_INT);
            $comInsert->bindParam(':item_id', $item['id'], PDO::PARAM_INT);
            $comInsert->bindParam(':type_id', $type, PDO::PARAM_INT);

            $comInsert->execute(); // Вставка пазла

            if ( !(++$cnt % $step) ) print ' '.(++$i*5).'%'; // Выводим сообщение каждые 5%
        }
        Yii::endProfile('LinksMigration'); // Окончание профилирования производительности
        echo "$cr End corrections."; // Сообщение об окончании работы скрипта
    }

    /**
     * Изменение миниатюр альбомов.
     * Если существует на лок.машине файл-миниатюра альбома, указанная на thejigsawpuzzles.com,
     * устанавливаем ее миниатюрой.
     */
    public function actionThumbs()
    {
        $mainAlbums = Album::model()->mainAlbums()->findAll(); // Получаем основные альбомы

        $connTJP = Yii::app()->db2; // Используем соединения для локального сервера.
        $sqlThumb = '
            SELECT
              der.g_derivativeSourceId source_id
            FROM
              g2_ChildEntity,
              g2_Derivative
            LEFT JOIN g2_Derivative der
              ON (der.g_id = g2_Derivative.g_derivativeSourceId)
            WHERE g2_ChildEntity.g_parentId = :album_id
              AND g2_ChildEntity.g_id = g2_Derivative.g_id
              AND g2_Derivative.g_derivativeType = 1
        '; //g2_ChildEntity.g_id,
        $comThumb = $connTJP->createCommand($sqlThumb);

        $conn = Yii::app()->db; // Используем соединения для локального сервера.
        $sqlUpdateAlbum = 'UPDATE album SET thumbnail_id=:thumbnail_id WHERE id=:album_id';
        $comUpdateAlbum = $conn->createCommand($sqlUpdateAlbum);

        foreach($mainAlbums as $alb) {
            $comThumb->bindValue(':album_id', $alb['id'], PDO::PARAM_INT);
            $itemID = $comThumb->queryScalar();
            $imgFullName = str_pad($itemID, 10, '0', STR_PAD_LEFT);
            $imgUrl = '/'.substr($imgFullName,-2, 2).'/'.substr($imgFullName,-4, 2);

            $file = Yii::app()->params['pathOS'].Yii::app()->params['pathThumbnail'].$imgUrl.'/'.$imgFullName.'.jpg';

            if (file_exists($file)) { // Проверка существования
                $comUpdateAlbum->bindValue(':album_id', $alb['id'], PDO::PARAM_INT);
                $comUpdateAlbum->bindParam(':thumbnail_id', $itemID, PDO::PARAM_INT);
                //echo "\n$file";
                $comUpdateAlbum->execute(); // Обновление thumbnail_id
            }
        }
    }

    /**
     * Перенос времени отложенных публикаций.
     * Правка времени - время исх. пазлов на сервере отстает на 1 час.
     */
    public function actionDatePublished()
    {
        $connTJP = Yii::app()->db2; // Используем соединения для удаленного сервера (thejigsawpuzzles.com).
        $sql = "
          SELECT g_itemId itemID, FROM_UNIXTIME(g_parameterValue+3600) dateCreated
          FROM g2_PluginParameterMap
          WHERE g_pluginId='schedule' AND g_parameterName='publishDate'
        ";
        $com = $connTJP->createCommand($sql);
        $items = $com->queryAll();

        $conn = Yii::app()->db;     // Используем соединения для локального сервера.
        //$sql = "UPDATE item set dateCreated=:dateCreated WHERE id=:itemID";
        //$com = $conn->createCommand($sql);

        foreach ($items as $item) {
            $conn
                ->createCommand("UPDATE item set dateCreated='".$item['dateCreated']."' WHERE id=".$item['itemID'])
                ->execute();

            //$com->bindParam(':itemID', $item['itemID'], PDO::PARAM_INT);
            //$com->bindParam(':dateCreated', $item['dateCreated'], PDO::PARAM_STR);
            //$com->execute();
        }

        die('Finished.');
    }

    /*public function actionUpdateInSearch()
    {
        // Выбор пазлов, принадлежащих "основным альбомам"
        $conn = Yii::app()->db;
        $items = $conn
            ->createCommand('
                SELECT item.id
                FROM album
                LEFT OUTER JOIN album_item
                    ON (album.id = album_item.album_id)
                LEFT OUTER JOIN item
                    ON (item.id = album_item.item_id)
                WHERE album.parent_id = 0 AND album.id <> 7298
            ')
            ->queryColumn();

        // Пометка флажка у всех, входящих в них пазлов
        $comm = $conn->createCommand('UPDATE item SET inSearch=1 WHERE id=:itemID');

        foreach ($items as $item) {
            $comm
                ->bindParam(':itemID', $item, PDO::PARAM_INT)
                ->execute();
        }
    }*/

    /**
     * Устанавливаем inSearch у ВСЕХ импортированных пазлов равным 1.
     * Это поле используется для маркировки новых добавленных пазлов.
     *
     * @return mixed
     */
    public function actionUpdateInSearch()
    {
        return Yii::app()->db
            ->createCommand('UPDATE item SET inSearch=1')
            ->execute();
    }

    /**
     * Устанавливает поле superuser=1 после миграции для id=6 (admin)
     *
     * @return mixed
     */
    public function actionSetAdmin()
    {
        return Yii::app()->db
            ->createCommand('UPDATE user_users SET superuser=1 WHERE id=6')
            ->execute();
    }
}
