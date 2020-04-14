<?php
/**
 * Copying images (Command line: yiic ci)
 * Консольная команда. Копирования файлов изображений в новое приложение.
 * Date: 25.07.12
 */
class CiCommand extends CConsoleCommand
{
    // Фиксированный адрес базовой директории изображений Gallery2
    //private $rootTJP = 'D:\Admin\Code\thejigsawpuzzles\var\www\tjpuzzles\g2data\cache\derivative\\';
    // Фиксированный адрес директории исходных изображений Gallery2
    //private $rootSource = 'D:/Admin/Code/!thejigsawpuzzles/tjpuzzles/g2data/albums/';
    // Куда заливать исходные изображения
    //private $pathSource = 'C:/Web/xampp/htdocs/thejigsaw/items/source/';
    // Фиксированный базовой директории изображений нового приложения
    //private $rootNew = 'C:\Web\xampp\htdocs\thejigsaw\items\\';
    private $errors = array(); // Массив с отсутствующими изображениями

    /**
     * Действие по умолчанию.
     */
    public function actionIndex()
    {
        $cr = chr(10); // Символ перевода строки
        $sep = '/'; // Разделитель директорий

        Yii::beginProfile('CopyingImages');   // Профилирование производительности
        echo "$cr Start copying. "; // Сообщение о начале работы скрипта

        // ================================= Шаг 1. Получение списка пазлов (id)
        echo "$cr Step 1. Select puzzles";

        try {
            $puzzles = Item::model()->findAll(array('select'=>'id'));
        } catch (Exception $e) {
            die ($cr.'Step 1 error: '.$e);
        }

        // ================================= Шаг 2. Обработка списка пазлов
        echo "$cr Step 2. Copying images $cr";

        $cnt = 0; $cntAll = count($puzzles); $step = round($cntAll / 20); //  Параметры для вывода процесса работы
        echo "$cr count=$cntAll. ";
        $connTJP =Yii::app()->db2; // Используем соединения для сервера TheGigsawPuzzles.com

        $sqlItemImage = '
            SELECT src.g_id source_id, res.g_id res_id, thumb.g_id thumb_id
            FROM g2_Derivative der
            LEFT JOIN g2_Derivative src
              ON (der.g_DerivativeSourceId = src.g_derivativeSourceId AND src.g_derivativeOperations = "scale|1024,1024")
            LEFT JOIN g2_Derivative res
              ON (der.g_derivativeSourceId = res.g_derivativeSourceId AND res.g_derivativeOperations = "scale|400,400")
            LEFT JOIN g2_Derivative thumb
              ON (der.g_derivativeSourceId = thumb.g_derivativeSourceId AND thumb.g_derivativeOperations = "thumbnail|130")
            WHERE der.g_derivativeSourceId = :id
            LIMIT 1
        ';
        $comItemImage = $connTJP->createCommand($sqlItemImage); // Подготовим команду выборки
        foreach ($puzzles as $key=>$val) { // Обработка
            // Получение ID миниатюр
            $id = $val['id']; //echo $cr.$id;
            $comItemImage->bindParam(':id', $id, PDO::PARAM_INT);
            try { // Процесс копирования
                $image = $comItemImage->queryRow(); // Получаем очередную запись

                $this->fItemImageCopy($image['source_id'], 'original', $id, $cr, $sep);
                $this->fItemImageCopy($image['res_id'], 'worked', $id, $cr, $sep);
                $this->fItemImageCopy($image['thumb_id'], 'thumbnail', $id, $cr, $sep);
            } catch (Exception $e) {
                die ($cr.'Step 1 error: '.$e);
            }
            if ( !(++$cnt % $step) ) echo " #"; // Выводим сообщение каждые 5%
        }

        // ================================= Шаг 3. Копирование исходного изображения
        /*echo "$cr Step 3. Copying source images $cr";
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
        $conn = Yii::app()->db; // Используем соединения для локального сервера.

        $comSource = $conn->createCommand($sqlSource); // Подготовим команду выборки
        $puzzles = $comSource->queryAll(); // Получение всех элементов

        $cnt = 0; $cntAll = count($puzzles); $step = round($cntAll / 20); //  Параметры для вывода процесса работы
        echo "$cr Step 3.2. Copying items from main albums $cr";
        echo "count=$cntAll. $cr";

        foreach ($puzzles as $item) { // Копирование исходных изображений
            $this->fSourceCopy($item, $cr); // Копирование
            if ( !(++$cnt % $step) ) echo " #"; // Выводим сообщение каждые 5%
        }*/

        // ================================= Завершение ===========================
        Yii::endProfile('CopyingImages'); // Завершение профилирования
        echo "$cr End copying. "; // Сообщение об окончании работы скрипта

        if ($cnt = count($this->errors)) { // Выводим список id с отсутствующими изображениями
            echo "$cr Всего $cnt пропущенных записей: $cr";
            foreach ($this->errors as $error) {
                echo " $error; ";
            }
        }
    }

    /**
     * Дублирование
     * Нужно для избежания ошибки MySQL s. has gone away
     */
    public function actionMain()
    {
        $cr = chr(10);
        $conn = Yii::app()->db;

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
    }
    /**
     * Копирование для пользовательских альбомов
     */
    public function actionUser()
    {
        $cr = chr(10);

        echo "$cr Step 4. Copying user source images $cr"
            ."$cr Step 4.1. Select items from user albums $cr";

        $puzzles = Yii::app()->db
            ->createCommand('
                SELECT item.id, item.componentUrl, album.componentUrl albumComponentUrl
                FROM item
                LEFT JOIN album_item ai
                  ON (ai.item_id = item.id)
                LEFT JOIN album
                  ON (album.id = ai.album_id)
                WHERE album.parent_id = 7298
                ORDER BY album.componentUrl ASC
            ;')
            ->queryAll();

        $cnt = 0; $cntAll = count($puzzles); $step = round($cntAll / 20); //  Параметры для вывода процесса работы
        echo "$cr Step 3.2. Copying items from main albums $cr";
        echo "count=$cntAll. $cr";

        foreach ($puzzles as $item) { // Копирование исходных изображений
            $this->fUserSourceCopy($item, $cr); // Копирование
            if ( !(++$cnt % $step) ) echo " #"; // Выводим сообщение каждые 5%
        }

        $this->showErrors();
    }

    /**
     * Отображение ошибок
     */
    public function showErrors()
    {
        $cr = chr(10);
        if ($cnt = count($this->errors)) { // Выводим список id с отсутствующими изображениями
            echo "$cr Всего $cnt пропущенных записей: $cr";
            foreach ($this->errors as $error) {
                echo " $error; ";
            }
        }
    }

    /**
     * Копирование изображения.
     * Создание директорий в случае необходимости.
     *
     * @param $source_id
     * @param $type
     * @param $id   Id записи
     * @param $cr   Символ перевода строки
     * @param $sep  Разделитель директорий
     * @return bool
     */
    public function fItemImageCopy($source_id, $type, $id, $cr, $sep)
    {
        if (empty($source_id)) {
            $this->errors[] = ' Empty source_id::'.$id.$cr;
            return false;
        }
        $prefix1 = $source_id[0]; // Алгоритм работы с директориями Gallery2. Первая директория
        // Алгоритм работы с директориями Gallery2. Формируем вторую (вложенную) директорию
        if (100 < $source_id) { // после 100
            $prefix2 = $source_id[1]; // У нас минимум id с 32
            $address  = Yii::app()->params['rootTJP'].$prefix1.$sep.$prefix2.$sep;
        } else { // до 100
            $address  = Yii::app()->params['rootTJP'].'0'.$sep.$prefix1.$sep;
        }
        $source = $address.$source_id.'.dat';
        // Обрабатываем в соответствии с нашими правилами размещения файлов по ID
        $imgFullName = str_pad($id, 10, '0', STR_PAD_LEFT); // Дополняем ID нулями
        // Разбиваем на две вложенные директории (target)
        $dir1 = substr($imgFullName,-2, 2); // Первый уровень
        $dir2 = substr($imgFullName,-4, 2); // Второй уровень
        $imgAddress = $dir1.$sep.$dir2;     // Директории вместе с разделителем
        $target = Yii::app()->params['rootNew'].$type.$sep.$imgAddress; // Абсолютный путь (target) без имени файла

        if (file_exists($source)) { // Если файл-источник существует - копируем  в новую директорию
            //  Создаем директорию если не существует
            if (true !== is_dir(Yii::app()->params['rootNew'].$type.$sep.$dir1)) { //echo $cr.$target; // Если не существует первая директория - создаем рекурсивно обе
                if ( !mkdir($target, 0700, true) ) {
                    $this->errors[] = 'make_dir_both::'.$target.$cr;
                    return false;
                }
            } elseif(true !== is_dir(Yii::app()->params['rootNew'].$type.$sep.$dir1.$sep.$dir2)) { //echo $cr.$target; // Иначе если не существует вторая - создаем только вторую
                if ( !mkdir($target, 0700, false) ) {
                    $this->errors[] = 'make_dir_second::'.$target.$cr;
                    return false;
                }
            }
            // Иначе просто копируем файл
            $targetName = $target.$sep.$imgFullName.'.jpg';

            if (!file_exists($targetName)
                || (file_exists($targetName) && (filesize($targetName) != filesize($source)))
            ) { // Файл существует и имеет иной размер
                if ( !@copy($source, $target.$sep.$imgFullName.'.jpg') ) {
                     $this->errors[] = 'copy::'.$type.'::'.$id.'::'.$target.$cr;
                    return false;
                }
            }
        } else { //echo $source.$cr;// Если отсутствуем файл Gallery2 - записываем id записи в массив ошибок
            $this->errors[] = $type.'::'.$id.'::'.@$source.$cr;
            return false;
        }
        return true;
    }

    /**
     * Копирование исходного изображения.
     * Здесь возникла путаница с source из пред. пункта
     * Source хранится в директории g2data/albums/Название_альбома/Название_пазла_вытянутое_из_title
     *
     * @param $item
     * @param $cr
     * @return bool
     */
    public function fSourceCopy($item, $cr)
    {
        $source = Yii::app()->params['rootSource'].$item['albumComponentUrl'].'/'.$item['componentUrl'];

        if (file_exists($source)) { // Если файл существует
            $imgFullName = str_pad($item['id'], 10, '0', STR_PAD_LEFT); // Дополняем ID нулями
            //$target = Yii::app()->params['rootNew'].Yii::app()->params['pathSource']
            $target = Yii::app()->params['pathOS'].Yii::app()->params['pathSource']
                .'/'.substr($imgFullName,-2, 2).'/'.substr($imgFullName,-4, 2);

            //  Создаем директорию если не существует
            $this->ensureDirectory($target);

            $targetName = $target.'/'.$imgFullName.'.jpg';
            if ( !file_exists($targetName)
                || (file_exists($targetName) && (filesize($targetName) != filesize($source)) )
            ) {
                if ( !@copy($source, $targetName) ) { // Создавать директорию не надо - уже сделано ранее
                    $this->errors[] = 'copy::source_image::'.$source.'::'.$target.$cr;
                    return false;
                }
            }

        } else { // Исходное изображение не существует
            $this->errors[] = ' Empty source images :: '.$item['albumComponentUrl'].'/'.$item['componentUrl'].$cr;
            return false;
        }

        return true;
    }

    /**
     *
     * @param $item
     * @param $cr
     * @return bool
     */
    public function fUserSourceCopy($item, $cr)
    {
        //$source = Yii::app()->params['rootSource'].$item['albumComponentUrl'].'/'.$item['componentUrl'];
        $file1 = Yii::app()->params['rootSource'].'User-Albums1/'.$item['albumComponentUrl'].'/'.$item['componentUrl'];
        $file2 = Yii::app()->params['rootSource'].'User-Albums2/'.$item['albumComponentUrl'].'/'.$item['componentUrl'];
        $file3 = Yii::app()->params['rootSource'].'User-Albums3/'.$item['albumComponentUrl'].'/'.$item['componentUrl'];
        $file4 = Yii::app()->params['rootSource'].'User-Albums4/'.$item['albumComponentUrl'].'/'.$item['componentUrl'];

        if (file_exists($file1))
            $source = $file1;
        elseif (file_exists($file2))
            $source = $file2;
        elseif (file_exists($file3))
            $source = $file3;
        elseif (file_exists($file4))
            $source = $file4;
        //echo $cr."$file1 :: $file2 :: $file3 || ".$source.$cr;
        if (!empty($source)) { // Если файл существует
            $imgFullName = str_pad($item['id'], 10, '0', STR_PAD_LEFT); // Дополняем ID нулями
            //$target = Yii::app()->params['rootNew'].Yii::app()->params['pathSource']
            $target = Yii::app()->params['pathOS'].Yii::app()->params['pathSource']
                .'/'.substr($imgFullName,-2, 2).'/'.substr($imgFullName,-4, 2);
            //echo "$cr target::$target $cr";
            //  Создаем директорию если не существует
            $this->ensureDirectory($target);

            $targetName = $target.'/'.$imgFullName.'.jpg';
            if ( !file_exists($targetName)
                || (file_exists($targetName) && (filesize($targetName) != filesize($source)) )
            ) {
                if ( !@copy($source, $targetName) ) { // Создавать директорию не надо - уже сделано ранее
                    $this->errors[] = 'copy::user_source_image::'.$source.'::'.$target.$cr;
                    return false;
                }
            }

        } else { // Исходное изображение не существует
            $this->errors[] = ' Empty user source images :: '.$item['albumComponentUrl'].'/'.$item['componentUrl'].$cr;
            return false;
        }

        return true;
    }
}