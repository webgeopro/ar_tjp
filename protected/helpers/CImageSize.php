<?php
/**
 * Date: 28.01.13 Time: 19:13
 * Вычисление размеров пазла (worked / thumbnail)
 * Вычисление пути к изображению
 *
 * Используется в виджетах
 */
class CImageSize
{
    public $imgFullName; // ID записи, дополненное нулями слева до 10 цифр
    public $imgUrl; // Разбиение на директории, в соответствие с алгоритмом

    /**
     * Получение пути к изображению пазла
     * Если пустой ID - формируется 00/00/0000000000.jpg
     *
     * @param $id - ID объекта Item
     * @return array ($imgFullName, $imgUrl)
     */
    public static function getPath($id)
    {
        $imgFullName = str_pad($id, 10, '0', STR_PAD_LEFT);
        $imgUrl = substr($imgFullName,-2, 2).'/'.substr($imgFullName,-4, 2);
//echo "($imgFullName, $imgUrl)<br>";
        return array($imgFullName, $imgUrl);
    }

    /**
     * Получение размеров изображения, масштабирование, вписывание в квадрат
     * @param $w - Ширина миниатюры для альбома
     * @param $h - Ширина миниатюры для альбома
     * @param $iW - Ширина изображения (orig)
     * @param $iH - Высота изображения (orig)
     * @param $imgUrl      - Относительная часть пути к изображению (Директории)
     * @param $imgFullName - Относительная часть пути к изображению (Имя файла)
     * @param string $path - Относительная часть пути к изображению (Исходная папка - worked, thumbnail ...)
     * @return array ($width, $height)
     */
    public static function getSize($w, $h, $iW=null, $iH=null, $imgUrl, $imgFullName, $path='')
    {
        if ('' == $path) $path = Yii::app()->params['pathThumbnail'];
        //if ('' == $imgUrl OR '' == $imgFullName) list($imgUrl, $imgFullName) = $this::getPath($id);

        if (!empty($iW) AND !empty($iH)) {
            if ($iH < $iW) { // альбомная ориентация
                $width = $w;
                $height = ceil($iH * $width / $iW);
            } else { // портретная ориентация
                $height = $h;
                $width  = ceil($iW * $height / $iH);
            }
        } else { // Если не указаны размеры вписываем по высоте
            $p = Yii::app()->getBaseUrl(true).$path.'/'.$imgUrl.'/'.$imgFullName.'.jpg';
            try {
                if ($size = @GetImageSize($p)) {
                    $newW = $size[0]; $newH = $size[1];
                    if ($newH < $newW) { // альбомная ориентация
                        $width = $w;
                        $height = ceil($newH * $width / $newW);
                    } else { // портретная ориентация
                        $height = $h;
                        $width  = ceil($newW * $height / $newH);
                    }
                } else {//die("$h :: $iH :: $iW");
                    if ($h < $iH) { $width = ''; $height = $iH; }
                    else { $width = empty($iW) ? '' : $iW;
                        $height = ''; }
                }
            } catch (Exception $e) {
                $width = '';
                $height = (0<$h)?$h:'';
            }
        } //echo "$width, $height.<br>";
        return array($width, $height);
    }

    /**
     * Получение полной информации
     * Определение типа (объект / пазл)
     * @param $item - Экземпляр либо Item либо Album
     * @param int $thumbW - Ширина миниатюры для альбома
     * @param int $thumbH - Высота миниатюры для альбома
     * @param bool $full - Выводить полную информацию (+ $imgFullName + $imgUrl)
     * @return array (($imgFullName, $imgUrl, $w, $h) / ($w, $h))
     */
    public static function getSizeById($item, $thumbW=50, $thumbH=50, $full=false)
    {
        $itemWidth= null; $itemHeight=null; //die(print_r($item));

        if (is_object($item) AND 'Album' == get_class($item)) { // Если экземпляр модели Album
            if (!empty($item['item']['width']) AND !empty($item['item']['height'])) { // Получено с with('item')
                $itemWidth  = $item['item']['width']; //echo($item['item']['width']);
                $itemHeight = $item['item']['height'];
            } else { // Получаем Item по его thumbnail_id альбома
                $thumb = Item::model()->findByPk($item['thumbnail_id']);
                if (null !== $thumb) {
                    $itemWidth  = $thumb['width'];
                    $itemHeight = $thumb['height'];
                }
            }
            $itemID = $item['thumbnail_id'];
        } else {
            if (is_string($item) AND $itemID=(int)$item)
                $item = Item::model()->findByPk($itemID);
            $itemWidth  = $item['width'];
            $itemHeight = $item['height'];
            $itemID = $item['id'];
        }
        list($imgFullName, $imgUrl) = self::getPath($itemID);
        list($w, $h) = self::getSize($thumbW, $thumbH, $itemWidth, $itemHeight, $imgUrl, $imgFullName);
        //echo($imgFullName.'::'.$imgUrl.'::'.$w.'::'.$h);
        if ($full)
            return array($imgFullName, $imgUrl, $w, $h); // Выводим полную информацию

        return array($w, $h); // Выводим только ширину и высоту
    }

    /**
     * Получение новых пропорций для масштабированного изображения
     * Применяется в components/categories
     */
    public static function getRatioSize($width, $height, $albumWidth)
    {
        if ($width AND $height) {
            $ratio = $width / $albumWidth;
            $width = $albumWidth;
            $height =  ceil($height / $ratio);
        } else {
            $height = '';
            $width = $albumWidth;
        }
        return array($width, $height);
    }

}
