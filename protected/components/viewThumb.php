<?php
/**
 * Отображение миниатюры в левой части (add2web и т.п.)
 * Создана вследствие путаницы в getAlbumThumbnail
 * User: Vah
 * Date: 06.12.13
 */

class viewThumb extends CWidget {

    public $viewCut     = false; // Отображать ли значок нарезки поверх миниатюры
    public $viewCutName = false; // Отображать ли название нарезки внизу миниатюры
    public $viewTitle   = false; // Отображать ли наименование пазла (title)

    public $album; // Объект альбом
    public $item;  // Объект пазл

    public function init()
    {
        /*if (null == $this->item) {
            $itemID = is_object($this->album) ? $this->album->thumbnail_id : $this->album['thumbnail_id'];
            if (null == $itemID) return null;

            $this->item = Yii::app()->db
                ->createCommand('SELECT width, height, componentUrl, cut, title FROM item WHERE id=:itemID')
                ->bindParam(':itemID', $itemID, PDO::PARAM_INT)
                ->queryRow();
        }*/
    }

    public function run()
    {
        if (null != $this->item) { // Передан пазл
            $thumb = $this->getThumb($this->item, 'id');// Берем ресайз (130) пазла

        } elseif (null != $this->album) { // Передан альбом
            $thumb = $this->getThumb($this->album, 'thumbnail_id'); // Получить миниатюру альбома

        } else // Ничего в класс не передано (нет item, нет album)
            $thumb = $this->getDefault(); // Отображаем миниатюру по умолчанию (сейчас динозаврик из альбома Kids Puzzles)

        $this->render('viewThumb', array(
            'thumb' => $thumb,
            'url'   => $this->getUrl(),
            'title' => $this->getTitle(),
            'cutout'=> $this->getCutout(),
            'params'=> $this->getParams(),
        ));
    }

    /**
     * Поиск миниатюры альбома
     */
    private function getThumb($ob, $field)
    {
        $thumbID = is_object($ob) ? $ob->$field : $ob[$field];

        if (null == $thumbID)
            return $this->getDefault(); // Получить миниатюру по умолчанию если у альбома нет миниатюры

        return $this->getImage($thumbID);
    }

    /**
     * Получить изображение пазла
     * Ищем ресайз (130)
     */
    private function getImage($thumbID)
    {
        $image = $this->getPath($thumbID);

        return file_exists(Yii::app()->params['pathOS'].$image)
            ? $image
            : $this->getDefault();
    }

    /**
     * Получить миниатюру по умолчанию
     *
     * @return array
     */
    private function getDefault()
    {
        //$defaultID = Yii::app()->db->createCommand('SELECT id FROM item WHERE id=36')->queryScalar();
        $defaultID = Yii::app()->params['defaultItemID'];

        return $this->getPath($defaultID);
    }

    /**
     * Получить путь к файлу
     *
     * @param $thumbID
     * @return string
     */
    private function getPath($thumbID)
    {
        list($imgUrl, $imgFullName) = CImageSize::getPath($thumbID);

        return Yii::app()->params['pathThumbnail'] .'/'. $imgFullName .'/'. $imgUrl .'.jpg';
    }

    /**
     * Дополнительные поля миниатюры
     * (title, cutout)
     */
    private function getParams()
    {   //width="="
        /*if ((null == $this->item)) {
            $itemID = is_object($this->album) ? $this->album->thumbnail_id : $this->album['thumbnail_id'];
            if (null == $itemID) return null;

            $this->item = Yii::app()->db
                ->createCommand('SELECT cut, title FROM item WHERE id=:itemID')
                ->bindParam(':itemID', $itemID, PDO::PARAM_INT)
                ->queryRow();
        }*/
        return array('width'=>'', 'height'=>'');
    }

    /**
     * Получение componentUrl
     */
    private function getUrl()
    {
        $url = '/';
        if (null != $this->album) {
            if (isset($this->album['parent_id']) AND Yii::app()->params['userAlbumID'] == $this->album['parent_id'])
                $url .= Yii::app()->params['userAlbumName'] . '/';
            $url .= is_object($this->album) ? $this->album->componentUrl : $this->album['componentUrl'];
            if (null != $this->item)
                $url .= '/'. (is_object($this->item) ? $this->item->componentUrl : $this->item['componentUrl']);
            $url .= Yii::app()->params['urlSuffix'];
        }

        return $url;
    }

    /**
     * Получение title альбома / пазла
     */
    private function getTitle()
    {
        if (null != $this->item)      $ob = $this->item;
        elseif (null != $this->album) $ob = $this->album;
        else
            return '';

        return $this->getField($ob, 'title', '');
    }

    /**
     * Поучение нарезки пазла
     */
    private function getCutout()
    {
        return $this->viewCut
            ? $this->getField($this->item, 'cutout')
            : null;
    }

    /**
     * Получение значения поля объекта
     *
     * @param mixed $ob     Объект (album|item)
     * @param string $field Название поля
     * @param null $default Значение по умолчанию
     * @return mixed|null
     */
    private function getField($ob, $field, $default=null)
    {
        if (null != $ob)
            $result = is_object($ob) ? $ob->$field : $ob[$field];

        return isset($result) ? $result : $default;
    }
}