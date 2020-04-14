<?php
/**
 * Виджет "Миниатюра альбома".
 * Отображения миниатюры альбома (основного / пользовательского) Пока только ПОЛЬЗОВАТЕЛЬСКОГО
 * Последний добавленный в случае отсутствия миниатюры
 * potdFeatured в случае пустого альбома
 */

class getAlbumThumbnail extends CWidget {

    public $duration; // Время хранения кеша в секундах (24 часа)
    public $itemWidth  = 130; // Ширина изображения
    public $itemHeight = 130; // Высота изображения
    public $album; // Объект Альбом
    public $default = false; // Показывать ли дефолтную картинку
    public $albumComponentUrl; // Передача адреса альбома (makeapuzzle)

    public function run()
    { /*todo РЕФАКТОРИНГ!!! */
        $this->duration = Yii::app()->params['dGetAlbumThumbnail'];
        $tmp = null;
        $conn = Yii::app()->db;
        if (bul != $this->albumComponentUrl and null == $this->album)
            $this->album = CAlbumUtils::getAlbumByUrl($this->albumComponentUrl);
        if (null == $this->album) {
            //$album = Album::model()->findByAttributes(array('owner_id'=>Yii::app()->user->id));
            if (Yii::app()->user->isGuest){ // У пользователя нет своего альбома
                $tmp =  $this->getDefault();        // Выбираем дефолтную картинку

            } else
                $album = $conn
                    ->createCommand('
                        SELECT * FROM album
                        WHERE owner_id='. Yii::app()->user->id .'
                        ORDER BY id DESC
                        LIMIT 1
                    ')
                    ->queryRow();
        } else {//die(print_r($this->album));
            if (isset($this->album['thumbnail_id'])) {
                $album = $this->album;

            } elseif(isset($this->album['id']))
                $album = $conn
                    ->createCommand('
                        SELECT * FROM album
                        WHERE id='. $this->album['id'] .'
                        ORDER BY id DESC
                        LIMIT 1
                    ')
                    ->queryRow();
        }

        if (!empty($album['thumbnail_id']) AND (null == $tmp)) {
            //$tmp = Item::model()->with('cutName')->findByPk($album->thumbnail_id);
            $tmp = $conn
                ->createCommand('
                    SELECT * FROM item
                    WHERE id=' . $album['thumbnail_id'] .'
                    LIMIT 1')
                ->queryRow();
        }
        if (null == $tmp) {//die('here!'); // Выбираем последний пазл пользователя
            /*$tmp = Item::model()->with('cutName')->findByAttributes(
                array('owner_id' => Yii::app()->user->id),
                array('order' => 'dateCreated DESC')
            );*/
            if (Yii::app()->user->id)
                $tmp = $conn
                    ->createCommand('
                        SELECT * FROM item
                        WHERE owner_id=' . Yii::app()->user->id .'
                        ORDER BY dateCreated DESC
                        LIMIT 1')
                    ->queryRow();
            else $tmp = $this->getDefault();
        }

        if ($this->default && null == $tmp) // выводим дефолтную картинку
            $tmp = $this->getDefault();


        if (null == $tmp) { // Выбираем potdFeatured
            //$randomNum = rand(1, 30);
            //$this->renderFile('./././items/static/featured_puzzle'.$randomNum);
            // Ничего не выводим

        } else {
            $albumComponentUrl = '/'.Yii::app()->params['userAlbumName'].'/'.Yii::app()->user->name;
            if (null !== $tmp['cut']) {
                $tmp['cut'] = $tmp['cut'];
            }
            $puzzles['item'][] = $tmp;
            $this->render('potd', array(
                'name'=> 'potd_thumbnail_'.Yii::app()->user->id,
                'path' => Yii::app()->params['pathThumbnail'],
                'potd'=> $puzzles,
                'duration'   => $this->duration,
                'itemWidth'  => $this->itemWidth,
                'itemHeight' => $this->itemHeight,
                'size' => $this->itemWidth,
                'viewCutout' => true, // Отображать ли нарезку
                'albumComponentUrl' => $albumComponentUrl,
            ));
        }
	}

    /**
     * Пазл по умолчанию
     * @return mixed
     * Item::model()->findByPk(36);
     */
    protected function getDefault()
    {
        return Yii::app()->db->createCommand('SELECT * FROM item WHERE id=36')->queryRow();
    }
}