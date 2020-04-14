<?php
/**
 * Date: 09.10.13
 * Утилиты для кеширование
 * (Ревалидация ...)
 *
 * Используется в AdminController, console cron
 */
class CCacheUtils
{
    const FILE_EXT = 'log.err'; // Раширение файла ошибок
    public static $path;    // Директория логов
    const MAX_DIFF = 600;   // 600 секунд - перерендеринг логов

    /*public static function init()
    {
        self::$path = __DIR__ . 'err';
    }*/

    /**
     * Очистка конкретного кеша
     *
     * @param $cacheID
     * @param string $inc Инкремент для featured_puzzle (featured_puzzle5)
     * @return bool
     */
    public static function revalidate($cacheID, $inc='')
    {
        $filename = array(
            'current'    => 'potdCurrent',
            'categories' => 'potdCategories',
            'new'        => 'potdNew',
            'recent'     => 'potdRecent',
            'featured'   => 'featured_puzzle' . $inc,
        );
        if (array_key_exists($cacheID, $filename)) {
            self::save(
                self::getContent(self::getItems($cacheID), $cacheID),
                Yii::app()->params['pathOS'].Yii::app()->params['pathStatic'],
                $filename[$cacheID],
                $cacheID
            );
            return true;

        } else
            return false;
    }

    /**
     * Очистка кеша всех виджетов
     * @return bool
     */
    public static function revalidateAll()
    {
        //self::init();

        self::revalidate('recent');
        self::revalidate('categories');
        self::revalidate('new');
        self::revalidate('current');

        for ($i=1; $i<=30; $i++)
            self::revalidate('featured', $i);

    }

    /**
     * Выполнение запроса. Получение записи
     */
    public static function query($sql)
    {
        return Yii::app()->db
            ->createCommand($sql)
            ->queryRow();
    }

    /**
     * Выполнение запроса. Получение всех записей
     */
    public static function queryAll($sql)
    {
        return Yii::app()->db
            ->createCommand($sql)
            ->queryAll();
    }

    /**
     * Сохранение сгенерированного контента в файл
     */
    public static function save($content='', $path, $filename, $cacheID='')
    {
        if (null == $path OR null == $filename)
            return false;

        if (0 >= file_put_contents($path .'/'. $filename, $content)) {
            $logFile = Yii::app()->params['pathOS'] .'/protected/'. $cacheID .'.'. self::FILE_EXT;
            self::log($logFile, "\n--- No File: $cacheID"); // Записываем в файл ошибок
        }
    }

    /**
     * Получение текста запроса
     * @param $name
     * @return string
     */
    public static function getItems($name)
    {
        switch ($name) {
            case 'current':
                $itemMain = self::query('
                    SELECT item.*, album.componentUrl albumComponentUrl
                    FROM item
                    LEFT JOIN album_item
                      ON (item.id = album_item.item_id)
                    LEFT JOIN album
                      ON (album_item.album_id = album.id)
                    WHERE
                        (item.dateCreated <= "'.date("Y-m-d").' 23:59:59" AND album.id = '.Yii::app()->params['potdAlbumID'].')
                    ORDER BY item.dateCreated DESC
                    LIMIT 1');
                $itemAttr = self::query('
                    SELECT author, description FROM item_attributes WHERE id='.$itemMain['id']
                );
                $result['item'][] = array_merge($itemMain, $itemAttr);
                break;
            case 'new':
                $result['item'] = self::queryAll('
                    SELECT DISTINCT item.*, attr.author, attr.description, album.componentUrl albumComponentUrl
                    FROM item
                    LEFT JOIN album_item
                      ON (item.id = album_item.item_id)
                    LEFT JOIN album
                      ON (album_item.album_id = album.id)
                    LEFT JOIN item_attributes attr
                      ON (item.id = attr.id)
                    WHERE  album.parent_id = 0
                           AND album.id <> '.Yii::app()->params['userAlbumID'].'
                           AND album.id <> '.Yii::app()->params['potdAlbumID'].'
                           AND item.dateCreated <= "'.date("Y-m-d").' 23:59:59"
                    GROUP BY item.id
                    ORDER BY item.dateCreated DESC
                    LIMIT '.Yii::app()->params['newPuzzlesNum'].';
                ');
                break;
            case 'recent':
                $result['item'] = self::queryAll('
                    SELECT item.*, album.componentUrl albumComponentUrl
                    FROM album
                    LEFT OUTER JOIN album_item
                      ON album.id = album_item.album_id
                    LEFT OUTER JOIN item
                      ON (item.id = album_item.item_id AND item.dateCreated <= "'.date("Y-m-d").' 23:59:59")
                    WHERE album.id = '.Yii::app()->params['potdAlbumID'].'
                    ORDER BY item.dateCreated DESC
                    LIMIT '.Yii::app()->params['potdRecentNum'].';
                ');
                break;
            case 'categories':
                $result = self::queryAll('
                    SELECT a.*, i.width, i.height FROM album a
                    LEFT JOIN item i
                      ON i.id = a.thumbnail_id
                    WHERE a.parent_id = 0 AND a.id <> ' . Yii::app()->params['userAlbumID'] . '
                    ORDER BY a.sort
                ');
                break;
            case 'featured':
                $result = self::query('
                    SELECT item.*, album.componentUrl albumComponentUrl, attr.*
                    FROM album
                    LEFT OUTER JOIN album_item
                      ON album.id = album_item.album_id
                    INNER JOIN item
                      ON (item.id = album_item.item_id AND item.dateCreated <= "'.date("Y-m-d").' 23:59:59")
                    LEFT OUTER JOIN item_attributes attr
                      ON item.id = attr.id
                    WHERE album.parent_id = 0 AND album.id <> 7298
                    ORDER BY RAND()
                    LIMIT 1;
                ');
                break;
            default:
                $result = '';
        }
        $logFile = Yii::app()->params['pathOS'] .'/protected/'. $name .'.'. self::FILE_EXT;
        @unlink($logFile); // Очищаем файл

        if (null == $result)
            self::log($logFile, "\n--- No items: $name"); // Записываем в файл ошибок

        return $result;
    }

    /**
     * Рендеринг внутреннего шаблона
     *
     * @param $_viewFile_
     * @param null $_data_
     * @param bool $_return_
     * @return string [Вывод в файл]
     */
    public static function render($_viewFile_,$_data_=null,$_return_=true)
    {
        // Заводим спец. имя переменной чтобы избежать конфликта при извлечении данных (extract())
        if(is_array($_data_))
            extract($_data_,EXTR_PREFIX_SAME,'data');
        else
            $data=$_data_;

        if($_return_) { // Рендеринг в переменную. Буферизируем вывод.
            ob_start();
            ob_implicit_flush(false);
            require($_viewFile_);

            return ob_get_clean(); // Передаем вывод в переменную
        }
        else
            require($_viewFile_);
    }

    /**
     * Получение отрендеренного контента
     * @param $items
     * @param $cacheID
     * @return string
     */
    public static function getContent($items, $cacheID='')
    {
        switch ($cacheID) {
            case 'current':
                $file = self::getTemplateAlias('potd');
                $data = array(
                    'path' => Yii::app()->params['pathWorked'],
                    'potd' => $items,
                    'itemWidth'  => Yii::app()->params['workedSize'][0],
                    'itemHeight' => Yii::app()->params['workedSize'][1],
                    'size' => Yii::app()->params['workedSize'][0],
                    'viewCutout' => true, // Отображать ли нарезку
                );
                break;
            case 'new':
                $file = self::getTemplateAlias('potd');
                $data = array(
                    'path' => Yii::app()->params['pathThumbnail'],
                    'potd'=> $items,
                    'itemWidth'  => Yii::app()->params['thumbnailSize'][0],
                    'itemHeight' => Yii::app()->params['thumbnailSize'][1],
                    'size' => Yii::app()->params['thumbnailSize'][0],
                    'viewCutout' => true,
                );
                break;
            case 'recent':
                $file = self::getTemplateAlias('potd');
                $data = array(
                    'path' => Yii::app()->params['pathThumbnail'],
                    'potd' => $items,
                    'size' => Yii::app()->params['thumbnailSize'][0],
                );
                break;
            case 'categories':
                $file = self::getTemplateAlias('categories');
                $data = array(
                    'path' => Yii::app()->params['pathThumbnail'],
                    'categories' => $items,
                    'albumWidth' => 50,
                    'albumHeight'=> 34,
                );
                break;
            case 'featured':
                $file = self::getTemplateAlias('featured');
                $data = array(
                    'item' => $items,
                );
                break;
            default:
                $result = '';
        }

        $result = self::render($file, $data);

        $logFile = Yii::app()->params['pathOS'] .'/protected/'. $cacheID .'.'. self::FILE_EXT; // Файл ошибок
        if (0 >= strlen($result)) //
            self::log($logFile, "\n--- No Data: $cacheID"); // Записываем в файл ошибок

        return $result;
    }

    /**
     * Получение пути к файлу шаблона
     * @param $template
     * @return string
     * @throws Exception
     */
    public static function getTemplateAlias($template)
    {
        $file = Yii::getPathOfAlias('application.views.cron').'/'.$template.'.php';
        if (!file_exists($file))
            throw new Exception('Template '.$file.' does not exist.');

        return $file;
    }

    /**
     * Логирование результатов
     *
     * @param $file
     * @param $mess
     */
    public static function log($file, $mess)
    {
        if (self::checkFile($file)) {// Проверка времени создания файла
            $f = fopen($file, 'a');// Открыть файл
            fwrite($f, $mess); // Дописать информацию в файл
            fclose($f); // Надо ли???
        }
    }

    /**
     * Проверка времени создания файла.
     *
     * @param $file Путь + Имя файла лога ошибок
     * @param null $now
     * @return bool
     */
    public static function checkFile($file, $now=null)
    {
        $fmtime = @filemtime($file); // Получение времени последней модификации файла

        if (false != $fmtime) {
            $diff = date('U') - $fmtime; // Разница времени файла лога от текущего времени

            if (self::MAX_DIFF >= $diff)
                return true; // Файл устарел

        } elseif (!file_exists($file)) // Файла не сущ-ет, перерендеринг
            return true;

        return false;
    }

}
