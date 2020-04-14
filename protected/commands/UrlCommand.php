<?php
/**
 * componentUrl (Command line: yiic componentUrl)
 * Консольная команда. Востановление componentUrl пазлов из title либо ID
 * Date: 02.08.12
 */
class UrlCommand extends CConsoleCommand
{
    /**
     * Действие по умолчанию.
     */
    public function actionIndex()
    {
        $cr = chr(10); // Символ перевода строки
        Yii::beginProfile('componentUrl');   // Профилирование производительности
        echo "$cr Start componentUrl. "; // Сообщение о начале работы скрипта

        $conn = Yii::app()->db;    // Используем соединения для локального сервера.

        // ================================= Шаг 1. Получение списка пазлов для администраторских альбомов
        echo "$cr Step 1. Select puzzles";

        try {
            $puzzles = Item::model()->findAll('componentUrl=""');
        } catch (Exception $e) {
            die ($cr.'Step 1 error: '.$e);
        }

        // ================================= Шаг 2. Обработка списка пазлов
        echo "$cr Step 2. Insert componentUrl $cr";

        $cnt = 0; $cntAll = count($puzzles); $step = round($cntAll / 20); //  Параметры для вывода процесса работы
        echo "$cr count=$cntAll. ";

        $sqlInsertItem = '
            UPDATE item
            SET componentUrl = :componentUrl
            WHERE id = :id
        ';

        $comItem  = $conn->createCommand($sqlInsertItem);

        foreach ($puzzles as $key=>$val) { // Обработка
            $id = $val['id'];
            if (0 == strlen(trim($val['title']))) {
                $componentUrl = $val['id'];
            } else {
                $componentUrl = $val['title'];
            }
            $comItem->bindParam(':id', $id, PDO::PARAM_INT);
            $comItem->bindValue(':componentUrl', str_replace(' ', '-', $componentUrl), PDO::PARAM_STR);

            try {
                $comItem->execute();      // Вставка пазла
            } catch (Exception $e) {
                echo ' ='.$cnt++.'-'.$val['id'].'= '; // Выводим уведомление на экран
                Yii::log($e, 'trace', 'system.console.CConsoleCommand'); // Логируем ошибки
            }
            if ( !(++$cnt % $step) ) echo "#"; // Выводим сообщение каждые 5%
        }

        Yii::endProfile('componentUrl');
        echo "$cr End componentUrl. "; // Сообщение об окончании работы скрипта
    }
}