<?php
/**
 * Albums Migrations (am).
 * Консольная команда. Перенос альбомов из движка Gallery2 в новое приложение.
 * Date: 12.07.12
 */
class AmCommand extends CConsoleCommand
{
    public $errors = array(); // НЕ используется
    public $defaultAction = 'index'; // Действие по умолчанию.
    public $rootNumber = 7; // Фиксированный ID Корневого альбома
    public $userAlbumNumber = 7298; // Фиксированный ID корня для пользовательских альбомов

    /**
     * Действие по умолчанию.
     */
    public function actionIndex()
    {
        $cr = chr(10); // Символ перевода строки

        echo "$cr Start Albums migration. "; // Сообщение о начале работы скрипта
        Yii::beginProfile('AlbumsMigration');   // Профилирование производительности
        $conn = Yii::app()->db;    // Используем соединения для локального сервера.
        $connTJP =Yii::app()->db2; // Используем соединения для сервера TheGigsawPuzzles.com
        $cnt = 0;
        // ================================= Шаг 1. Получение списка корневых альбомов
        echo "$cr Step 1. Select";
        $sql = 'SELECT item_attr.g_itemId id, fs.g_pathComponent componentUrl,
                       item.g_title title, item_attr.g_orderWeight sort
                FROM g2_ItemAttributesMap item_attr
                LEFT OUTER JOIN g2_Item item
                    ON (item_attr.g_itemId = item.g_id)
                LEFT OUTER JOIN g2_FileSystemEntity fs
                    ON (fs.g_id = item.g_id)
                WHERE item_attr.g_parentSequence="'.$this->rootNumber.'/"
                ORDER BY item_attr.g_orderWeight
        ';
        $command = $connTJP->createCommand($sql);  // Подготовка sql
        try {
            $g2_albums = $command->queryAll();
        } catch (Exception $e) {
            die ($cr.'Step 1 error: '.$e);
        }
        // ================================= Шаг 2. Сохранение нового списка альбомов
        echo "$cr Step 2. Insert";

        $sqlInsert = '
            REPLACE INTO album
                (id, parent_id, owner_id, componentUrl, title, sort)
            VALUES
                (:albumID, 0, 1, :componentUrl, :title, :sort)
        ';
        $commandInsert = $conn->createCommand($sqlInsert); // Подготовим команду SQL
        foreach ($g2_albums as $alb) { //print_r($g2_albums);
            $transaction=$conn->beginTransaction(); // Открываем транзацию
            try {
                $componentUrl = str_replace(' &amp; ',' and ', $alb['title']);
                $commandInsert->bindParam(':albumID', $alb['id'], PDO::PARAM_INT); //html_entity_decode($alb['title']) Остается &
                //$commandInsert->bindValue(':componentUrl', str_replace(' ', '-', $componentUrl), PDO::PARAM_STR);
                $commandInsert->bindParam(':componentUrl', $alb['componentUrl'], PDO::PARAM_STR);
                $commandInsert->bindParam(':title', $alb['title'], PDO::PARAM_STR);
                $commandInsert->bindParam(':sort', $alb['sort'], PDO::PARAM_INT);
                $commandInsert->execute();
            } catch (Exception $e) {
                $transaction->rollBack(); // Откатываем назад в случае неудачи
                echo ' ='.$cnt++.'-'.$alb['id'].'= '; // Выводим уведомление на экран
                Yii::log($e, 'trace', 'system.console.CConsoleCommand'); // Логируем ошибки
            }
            $transaction->commit(); // Завершаем транзакцию если нет ошибок
        }
        // ================================= Шаг 3. Получение списка пользовательских альбомов
        echo "$cr Step 3. Select User albums";
        $sql = 'SELECT DISTINCT item.g_id id, item_attr.g_orderWeight sort, item.g_ownerId owner_id,
                       item.g_title title, fs.g_pathComponent componentUrl
                FROM g2_ItemAttributesMap item_attr
                LEFT OUTER JOIN g2_Item item
                    ON (item_attr.g_itemId = item.g_id)
                LEFT OUTER JOIN g2_FileSystemEntity fs
                    ON (fs.g_id = item.g_id)
                WHERE item_attr.g_parentSequence = "'.$this->rootNumber.'/'.$this->userAlbumNumber.'/"
                    AND item_attr.g_itemId != '.$this->userAlbumNumber.'
                ORDER BY item_attr.g_orderWeight
        ';
        $command = $connTJP->createCommand($sql);  // Подготовка sql LIKE
        try {
            $g2_user_albums = $command->queryAll();
        } catch (Exception $e) {
            die ($cr.'Step 3 error: '.$e);
        } //print_r($g2_user_albums);
        // ================================= Шаг 4. Сохранение списка пользовательских альбомов
        echo "$cr Step 4. Insert User albums $cr ";
        /*foreach ($g2_user_albums as $alb) {
            echo implode(': ', $alb);
            echo $cr;
        }
        die('Saving list complete');*/
        $sqlInsert = '
            REPLACE INTO album
                (id, parent_id, owner_id, componentUrl, title, sort)
            VALUES
                (:albumID, '.$this->userAlbumNumber.', :ownerID, :componentUrl, :title, :sort)
        ';
        $commandInsert = $conn->createCommand($sqlInsert); // Подготовим команду SQL
        $cnt = 0; $cntTest = 0; $cntAll = count($g2_user_albums); $step = round($cntAll / 20);
        echo "Всего записей: $cntAll $cr #";
        foreach ($g2_user_albums as $alb) { //print_r($g2_albums);
            //if (9204 == $alb['id']) echo $cr.implode(' : ', $alb);
            try {
                //$componentUrl = str_replace(' &amp; ',' and ', $alb['title']);
                $commandInsert->bindParam(':albumID', $alb['id'], PDO::PARAM_INT); //html_entity_decode($alb['title']) Остается &
                $commandInsert->bindParam(':ownerID', $alb['owner_id'], PDO::PARAM_INT);
                //$commandInsert->bindValue(':componentUrl', str_replace(' ', '-', $componentUrl), PDO::PARAM_STR);
                $commandInsert->bindParam(':componentUrl', $alb['componentUrl'], PDO::PARAM_STR);
                $commandInsert->bindParam(':title', $alb['title'], PDO::PARAM_STR);
                $commandInsert->bindParam(':sort', $alb['sort'], PDO::PARAM_INT);

                //echo $cr.print_r($commandInsert);
                $commandInsert->execute();

            } catch (Exception $e) { //@todo PDOException !!!
                echo ' ='.$cnt++.'-'.$alb['id'].'= '; // Выводим уведомление на экран
                Yii::log($e, 'trace', 'system.console.CConsoleCommand'); // Логируем ошибки
            }
            if ( !(++$cnt % $step) ) echo " #"; // Выводим сообщение каждые $step записей
        }
        Yii::endProfile('AlbumsMigration');
        echo "$cr End migration. "; // Сообщение об окончании работы скрипта
        //echo "$cr \$cntTest=$cntTest";
        /*if ($cnt = count($this->errors)) { // Выводим список id с отсутствующими изображениями
            echo "$cr Всего $cnt пропущенных записей: $cr";
            foreach ($this->errors as $error) {
                echo " $error; ";
            }
        }*/
    }
}