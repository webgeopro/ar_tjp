<?php
/**
 * Создание статичных файла для Cron
 * User: Vah
 * Date: 08.10.13
 * To change this template use File | Settings | File Templates.
 */

class CronCommand extends CConsoleCommand
{
    public $duration; // Время хранения кеша в секундах (24 часа)86400

    /**
     * Виджет "Случайный пазл"
     * Случайная выборка 30 пазлов из основных альбомов
     */
    public function actionFeatured()
    {
        $num = 30; // Количество выбираемых пазлов
        $items = Yii::app()->db
            ->createCommand('
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
                LIMIT '.$num.';
            ')
            ->queryAll();

        for ($i=1; $i <= $num; $i++) {
            /*$fileData = Yii::app()->controller->renderPartial('featured_puzzle_template', array(
                'item'  => $fp[$i-1],
            ), true);
            $fileName = Yii::app()->params['pathOS'].Yii::app()->params['pathStatic'].'/featured_puzzle'.$i;
            file_put_contents($fileName, $fileData);*/

            $this->save(
                $this->render('featured', array(
                    'item'  => $items[$i-1],
                )),
                Yii::app()->params['pathOS'].Yii::app()->params['pathStatic'],
                'featured_puzzle'.$i
            );

        }
    }

    /**
     * Виджет "Случайный пазл"
     * Создание одного файла Featured
     * @param int $num Номер файла (featured1, featured17 ...)
     */
    public function actionFeaturedFile($num=1)
    {
        // Получение аргумента из командной строки
        $items = Yii::app()->db
            ->createCommand('
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
            ')
            ->queryRow();

        $this->save(
            $this->render('featured', array('item' => $items,)),
            Yii::app()->params['pathOS'] . Yii::app()->params['pathStatic'],
            'featured_puzzle' . $num
        );
    }

    /**
     * Виджет "Пазл дня"
     */
    public function actionCurrent()
    {//die('date: '.date("Y-m-d"));
        $itemMain = Yii::app()->db // Основные характеристики пазла
            ->createCommand('
                SELECT item.*, album.componentUrl albumComponentUrl
                FROM item
                LEFT JOIN album_item
                  ON (item.id = album_item.item_id)
                LEFT JOIN album
                  ON (album_item.album_id = album.id)
                WHERE
                    (item.dateCreated <= "'.date("Y-m-d").' 23:59:59" AND album.id = '.Yii::app()->params['potdAlbumID'].')
                ORDER BY item.dateCreated DESC
                LIMIT 1')
            ->queryRow();
        $itemAttr = Yii::app()->db // Аттрибутивная информация
            ->createCommand('SELECT author, description FROM item_attributes WHERE id='.$itemMain['id'])
            ->queryRow();

        $puzzles['item'][] = array_merge($itemMain, $itemAttr); // Сливаем основные хар. и аттр.

        $this->save(
            $this->render('potd', array(
                'path' => Yii::app()->params['pathWorked'],
                'potd' => $puzzles,
                'duration'   => $this->duration,
                //'itemWidth'  => Yii::app()->params['workedSize'][0],
                //'itemHeight' => Yii::app()->params['workedSize'][1],
                'size' => Yii::app()->params['workedSize'][0],
                'viewCutout' => true, // Отображать ли нарезку
            )),
            Yii::app()->params['pathOS'].Yii::app()->params['pathStatic'],
            'potdCurrent'
        );
    }

    /**
     * Виджет "Группы пазлов"
     */
    public function actionCategories()
    {
        $albumWidth = 50;  // Ширина миниатюры для альбома
        $albumHeight = 34; // Высота миниатюры для альбома
        $categories = Yii::app()->db
            ->createCommand('
                SELECT a.*, i.width, i.height FROM album a
                LEFT JOIN item i ON i.id = a.thumbnail_id
                WHERE a.parent_id = 0 AND a.id <> ' . Yii::app()->params['userAlbumID'] . '
                ORDER BY a.sort
            ')
            ->queryAll();

        $this->save(
            $this->render('categories', array(
                'path' => Yii::app()->params['pathThumbnail'],
                'categories' => $categories,
                'albumWidth' => $albumWidth,
                'albumHeight'=> $albumHeight,
            )),
            Yii::app()->params['pathOS'].Yii::app()->params['pathStatic'],
            'potdCategories'
        );
    }

    /**
     * Виджет "Последние пазлы"
     */
    public function actionRecent()
    {
        $puzzles['item'] = Yii::app()->db
            ->createCommand('
                SELECT item.*, album.componentUrl albumComponentUrl
              FROM album
              LEFT OUTER JOIN album_item
                ON album.id = album_item.album_id
              LEFT OUTER JOIN item
                ON (item.id = album_item.item_id AND item.dateCreated <= "'.date("Y-m-d").' 23:59:59")
              WHERE album.id = '.Yii::app()->params['potdAlbumID'].'
              ORDER BY item.dateCreated DESC
              LIMIT '.Yii::app()->params['potdRecentNum'].';
            ')
            ->queryAll();

        $this->save(
            $this->render('potd', array(
                'path' => Yii::app()->params['pathThumbnail'],
                'potd' => $puzzles,
                'size' => Yii::app()->params['thumbnailSize'][0],
            )),
            Yii::app()->params['pathOS'].Yii::app()->params['pathStatic'],
            'potdRecent'
        );
    }

    /**
     * Виджет "Новые пазлы"
     */
    public function actionNew()
    {//die('INSIDE NEW');
        $puzzles['item'] = Yii::app()->db
            ->createCommand('
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
            ')
            ->queryAll();

        $this->save(
            $this->render('potd', array(
                'path' => Yii::app()->params['pathThumbnail'],
                'potd'=> $puzzles,
                'itemWidth'  => Yii::app()->params['thumbnailSize'][0],
                'itemHeight' => Yii::app()->params['thumbnailSize'][1],
                'size' => Yii::app()->params['thumbnailSize'][0],
                'viewCutout' => true,
            )),
            Yii::app()->params['pathOS'].Yii::app()->params['pathStatic'],
            'potdNew'
        );
    }

    /**
     * Сохранение сгенерированного контента в файл
     */
    protected function save($content='', $path, $filename)
    {
        if (null == $path OR null == $filename)
            return false;

        @file_put_contents($path .'/'. $filename, $content);
    }

    /**
     * Рендеринг файла. Опирается на CConsoleCommand->renderFile()
     * @param $template
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    private function render($template, array $data = array())
    {
        $file = Yii::getPathOfAlias('application.views.cron').'/'.$template.'.php';
        if (!file_exists($file))
            throw new Exception('Template '.$file.' does not exist.');

        return $this->renderFile($file, $data, true); // Генерируем контент в переменную
    }
}