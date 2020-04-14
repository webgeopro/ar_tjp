<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Vah
 * Date: 08.02.13
 * Time: 16:55
 * Приложение для тестирования.
 */
class TmpCommand extends CConsoleCommand
{
    private $rootSource = 'D:/Admin/Code/!thejigsawpuzzles/tjpuzzles/g2data/albums/';


    public function actionIndex()
    {
        $cr = chr(10); // Символ перевода строки

        echo "$cr Start Items migration. "; // Сообщение о начале работы скрипта

        $conn = Yii::app()->db;    // Используем соединения для локального сервера.

        /*$now = date('Y-m-d');

        $sqlUpdate = 'UPDATE `item_attributes` SET dateImageCreated="'.$now.'"
                              WHERE ( dateImageCreated IS NULL OR dateImageCreated >"'.$now.'")';
        $comUpdate = $conn->createCommand($sqlUpdate);
        $comUpdate->execute();*/
        // ================================= Шаг 3. Копирование исходного изображения
        echo "$cr Step 3. Copying source images $cr";
        echo "$cr Step 3.1. Select items from main albums $cr";
        $sqlSource = '
            SELECT item.id, item.componentUrl, album.componentUrl albumComponentUrl
            FROM item
            LEFT JOIN album_item ai
              ON (ai.item_id = item.id)
            LEFT JOIN album
              ON (album.id = ai.album_id)
            WHERE album.parent_id = 0 AND album.id <> 7298
            ORDER BY album.componentUrl ASC ;
        ';
        $comSource = $conn->createCommand($sqlSource); // Подготовим команду выборки
        $puzzles = $comSource->queryAll();

        $cnt = 0; $cntAll = count($puzzles); $step = round($cntAll / 20); //  Параметры для вывода процесса работы
        echo "$cr Step 3.2. Copying items from main albums $cr";
        echo "count=$cntAll. $cr";

        foreach ($puzzles as $item) { // Копирование исходных изображений
            $this->fSourceCopy($item, $cr); // Копирование
            if ( !(++$cnt % $step) ) echo " #"; // Выводим сообщение каждые 5%
        }

        Yii::endProfile('ItemsMigration');
        echo "$cr End migration. "; // Сообщение об окончании работы скрипта
    }



    public function fSourceCopy($item, $cr)
    {
        $source = $this->rootSource.$item['albumComponentUrl'].'/'.$item['componentUrl'];
        if (file_exists($source)) { // Если файл существует
            $imgFullName = str_pad($item['id'], 10, '0', STR_PAD_LEFT); // Дополняем ID нулями
            $target = Yii::app()->params['pathOS'].Yii::app()->params['pathSource'].'/'
                .substr($imgFullName,-2, 2).'/'.substr($imgFullName,-4, 2).$imgFullName.'.jpg';

            if ( !@copy($source, $target) ) { // Создавать директорию не надо - уже сделано ранее
                $this->errors[] = 'copy::source_image::'.$source.'::'.$target.$cr;
                return false;
            }
        } else { // Исходное изображение не существует
            $this->errors[] = ' Empty source images :: '.$item['albumComponentUrl'].'/'.$item['componentUrl'].$cr;
            return false;
        }

        return true;
    }
}
