<?php
/**
 * Создание статичных файла для Cron
 * User: Vah
 * Date: 08.10.13
 * Основывается на алгоритме выбора из хелпера CCacheUtils
 */

class CronCommand extends CConsoleCommand
{
    public $duration; // Время хранения кеша в секундах (24 часа)86400

    /**
     * Виджет "Случайный пазл"
     * Случайная выборка 30 пазлов из основных альбомов
     * НЕ ИСПОЛЬЗУЕТСЯ. Вместо него генерация отдельных файлов из actionFeaturedFile
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
     *
     * @param int $num Номер файла (featured1, featured17 ...) Получанем аргумент из командной строки
     */
    public function actionFeaturedFile($num=1)
    {
        $items = CCacheUtils::getItems('featured');

        $this->save(
            $this->render('featured', array('item' => $items,)),
            Yii::app()->params['pathOS'] . Yii::app()->params['pathStatic'],
            'featured_puzzle' . $num,
            'featured'
        );
    }

    /**
     * Виджет "Пазл дня"
     */
    public function actionCurrent()
    {
        $puzzles = CCacheUtils::getItems('current'); // ['item'][]

        $this->save(
            $this->render('potd', array(
                'path' => Yii::app()->params['pathWorked'],
                'potd' => $puzzles,
                'size' => Yii::app()->params['workedSize'][0],
                'duration'   => $this->duration,
                'viewCutout' => true, // Отображать ли нарезку
            )),
            Yii::app()->params['pathOS'].Yii::app()->params['pathStatic'],
            'potdCurrent',
            'current'
        );
    }

    /**
     * Виджет "Группы пазлов". Categories.
     */
    public function actionCategories()
    {
        $albumWidth = 50;  // Ширина миниатюры для альбома
        $albumHeight = 34; // Высота миниатюры для альбома

        $categories = CCacheUtils::getItems('categories');

        $this->save(
            $this->render('categories', array(
                'path' => Yii::app()->params['pathThumbnail'],
                'categories' => $categories,
                'albumWidth' => $albumWidth,
                'albumHeight'=> $albumHeight,
            )),
            Yii::app()->params['pathOS'].Yii::app()->params['pathStatic'],
            'potdCategories',
            'categories'
        );
    }

    /**
     * Виджет "Последние пазлы"
     */
    public function actionRecent()
    {
        $puzzles = CCacheUtils::getItems('recent');

        $this->save(
            $this->render('potd', array(
                'path' => Yii::app()->params['pathThumbnail'],
                'potd' => $puzzles,
                'size' => Yii::app()->params['thumbnailSize'][0],
            )),
            Yii::app()->params['pathOS'].Yii::app()->params['pathStatic'],
            'potdRecent',
            'recent'
        );
    }

    /**
     * Виджет "Новые пазлы"
     */
    public function actionNew()
    {
        $puzzles = CCacheUtils::getItems('new');

        $this->save(
            $this->render('potd', array(
                'path' => Yii::app()->params['pathThumbnail'],
                'potd' => $puzzles,
                'itemWidth'  => Yii::app()->params['thumbnailSize'][0],
                'itemHeight' => Yii::app()->params['thumbnailSize'][1],
                'size' => Yii::app()->params['thumbnailSize'][0],
                'viewCutout' => true,
            )),
            Yii::app()->params['pathOS'].Yii::app()->params['pathStatic'],
            'potdNew',
            'new'
        );
    }

    /**
     * Сохранение сгенерированного контента в файл
     */
    protected function save($content='', $path, $filename, $cacheID='')
    {
        CCacheUtils::save($content, $path, $filename, $cacheID);
    }

    /**
     * Рендеринг файла. Опирается на CConsoleCommand->renderFile()
     *
     * @param $template
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    private function render($template, array $data = array())
    {
        $file = Yii::getPathOfAlias('application.views.cron').'/'.$template.'.php';
        if (!file_exists($file))
            throw new Exception('Template '.$file.' does not exist.'); // Не обрабатывается...

        return $this->renderFile($file, $data, true); // Генерируем контент в переменную
    }
}