<?php
/**
 * Совместимость со старыми внешними ссылками
 * {http://thejigsawpuzzles.com/download/2251138-2/Walt-and-Mickey}
 * der_id -> id + type (1 - thumb, 2 - worked, 3 - original).
 */

class DownloadController extends Controller
{
    /**
     * Получаем запись в новой БД и отдаем картинку
     *
     * @param $der_id (Формируется в Url-manager + $digit_param, $name_param)
     * @throws CHttpException
     */
    public function actionIndex($der_id)
	{
        if (ExternalLinks::model()->exists('der_id=:derID', array(':derID'=>$der_id))) {
            $link = ExternalLinks::model()->with('item')->findByPk($der_id);
            if ($link->item AND $link->type) { // Пазл существует и есть размер картинки.
                // Получить картинку
                $filename = Yii::app()->params['pathOS']
                    . Yii::app()->params['external'][$link->type]
                    . '/' . $link->item->imgUrl
                    . '/' . $link->item->imgFullName
                    . '.jpg';

                $size = filesize($filename);
                $img = file_get_contents($filename);

                header('Content-Type: image/jpeg'); // сформировать заголовок
                header("Content-Length: " . $size);  // отправляем размер изображения

                echo $img;
                Yii::app()->end(); // Завершаем работу приложения
            }
        }
        throw new CHttpException(404); // Выдаем ошибку при отсутствии пазла
        Yii::app()->end();
	}

    /**
     * Старый алгоритм.
     *
     * @param $der_id
     * @throws CHttpException
     */
    public function actionIndexOld($der_id)
	{
        if (ExternalLinks::model()->exists('der_id=:derID', array(':derID'=>$der_id))) {
            $link = ExternalLinks::model()->with('item')->findByPk($der_id);
            if ($link->item AND $link->type) { // Пазл существует и есть размер картинки. Генерация картинки.
                // Получить картинку
                $filename = Yii::app()->params['pathOS']
                    . Yii::app()->params['external'][$link->type]
                    . '/' . $link->item->imgUrl
                    . '/' . $link->item->imgFullName
                    . '.jpg';

                $size = filesize($filename);
                //$img  = imageCreateFromJPEG($filename);
                $img = file_get_contents($filename);

                #ob_start('ob_gzhandler');
                #ob_implicit_flush(0); // отключаем неявную отправку буфера
                header('Content-Type: image/jpeg'); // сформировать заголовок
                header("Content-Length: " . $size);  // отправляем размер изображения
                //if (!header_sent()) header('Content-Length: '.ob_get_length()); // если заголовки еще можно отправить, выдаем загловок Content-Length, иначе придется завершать передачу по закрытию
                //imageJPEG($img, null, 85); // отдать картинку
                echo $img;
                #header('Content-Length: '.ob_get_length());
                #ob_end_flush();

                Yii::app()->end(); // Завершаем работу приложения
            }
        }
        throw new CHttpException(404); // Выдаем ошибку при отсутствии пазла
        Yii::app()->end();
	}

}