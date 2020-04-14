<?php
/**
 * Class CMakeAPuzzle
 * Хелпер для обработки файлов, полученных от flash-аплодера swfupload
 *  - Получение файла от flash-аплодера ($_FILES)
 *  - Получение дополнительных полей от формы по ajax-запросу ($_POST)
 * Дата создания: 14.05.2013
 */
class CMakeAPuzzle {

    /**
     * Перемещаем полученный flash-аплодером файл во временную папку
     * по адресу ( Yii::app()->params['pathImageUpload'] )
     *
     * @return void
     */
    public static function fileMove() //$filename=null
    {
        if (is_uploaded_file($_FILES["Filedata"]["tmp_name"]) AND !$_FILES["Filedata"]["error"]) { // Загружен без ошибок
            //if (null == $filename)
                $fileName = // Формируем временное уникальное имя файла
                    md5( Yii::app()->user->id . $_FILES["Filedata"]["name"] )
                    . '.jpg';
            move_uploaded_file( // Перемещаем файл
                $_FILES["Filedata"]["tmp_name"],
                Yii::app()->params['pathImageUpload'] .$fileName
            );
            Yii::app()->end(); // Завершаем сценарий
        }
    }

    /**
     * Получаем дополнительные поля (из $_POST).
     * Выбираем EXIF-поля.
     * Сохраняем 3-4 размера файла и раскидываем по директориям.
     *
     * @return JSON
     */
    public static function addFields()
    {
        $item = new Item; // Инициализация объекта Item
        $user = Profile::model()->findByPk(Yii::app()->user->id); // Профиль пользователя
        $fileTitle = Yii::app()->request->getParam('fileTitle');
        $fileTitleDouble = Yii::app()->request->getParam('fileTitleDouble');

        //$file = Yii::app()->params['pathImageUpload'].md5(Yii::app()->user->id.$_POST["fileName"]).'.jpg'; // Так формируется имя файла при загрузке
        $file = Yii::app()->params['pathImageUpload'] // Так формируется имя файла при загрузке
            . Yii::app()->request->csrfToken
            . md5($_POST["fileName"])
            . '.jpg';

        $item->dateImageCreated = self::getExifDate($file); // Дата создания фото
        $item->owner_id = Yii::app()->user->id;

        if (null != $fileTitle)
            $item->title = htmlspecialchars($fileTitle); // Очищенное, безопасное (Latin) имя пазла.
        else // В случае случайного полного стирания названия в поле ввода имени пазла.
           $item->title = htmlspecialchars($fileTitleDouble); // Продулиброванное имя файла.

        if (Yii::app()->getModule('user')->isAdmin()) // Сохраняем автора фотографии
            $item->author = 'TheJigsawPuzzles.com Team';
        else // Новая логика отображения фото/автор - у UA не заполняем поле
            $item->author = ''; //empty($user['fullname']) ? Yii::app()->user->name : $user['fullname'];

        // Если админом передан альбом сохраняем в него
        if (!empty($_POST["albumComponentUrl"]) AND Yii::app()->getModule('user')->isAdmin()) { //------------------
            //$album = Album::model()->findByPk($_POST["albumID"]);
            $album = Album::model()->findByAttributes(array('componentUrl'=>$_POST["albumComponentUrl"]));
        } else // В пользовательский альбом  -------------------------------------------------------------
            if (Album::model()->exists('owner_id=:ownerID', array(':ownerID'=>Yii::app()->user->id)))
                $album = Album::model()->findByAttributes(array('owner_id'=>Yii::app()->user->id));

        if (null === @$album) { // Если альбома не существует, создаем новый пользовательский от user->id
            $album = new Album;                      // Инициализируем новый альбом
            $album->owner_id = Yii::app()->user->id; // Владелец альбома
            $album->parent_id = Yii::app()->params['userAlbumID']; // Родитель (7298)
            $album->title = empty($user['fullname'])?Yii::app()->user->name:$user['fullname'];// полное имя/логин
            if ($album->validate()) $album->save(); // Валидация, сохранение, получение ID нового альбома
        }

        $albumID = empty($album['id'])?null:$album['id']; // Получаем ID альбома для вставки в AlbumItem (сост. ключ)
        $item->currentAlbumID = $albumID; // Для проверки уникальности (album + componentUrl)
        if ($item->validate()) {
            if ($item->save()) {
                $item->fileCreate($file); // Ресайз и раскидывание по директориям
                CAlbumUtils::getItemsCount($albumID, null, true); // Обновить счетчик пазлов в альбоме
                /*$ai = new AlbumItem; // Сохранение ссылки на альбом
                $ai->album_id = $albumID;
                $ai->item_id  = $item['id'];
                if ($ai->validate()) $ai->save(); // @todo Реакция на ошибку?*/
                echo CJSON::encode(array(
                    'result' => 'success',
                    'fileID' => $_POST["fileID"], 'fileTitle' => $_POST["fileTitle"], 'fileName' => $file, 'dbID' => $item->id,
                ));
            } else
                echo CJSON::encode(array('result' => 'error', 'message' => 'Save Error', 'fileTitle' => $_POST["fileTitle"]));
        } else
            echo CJSON::encode(array('result' => 'error', 'message' => 'Validate Error', 'fileTitle' => $_POST["fileTitle"]));
        Yii::app()->end();
    }

    /**
     * Добавление файла администратором
     *
     * @param null $album
     * @return bool
     */
    public static function addFieldsByAdmin($album=null)
    {
        $file = $_FILES["Filedata"];
        if (empty($file['tmp_name']))
            return false;
        $item = new Item; // Инициализация объекта Item

        $item->dateImageCreated = self::getExifDate($file['tmp_name']); // Дата создания фото
        $item->author = 'TheJigsawPuzzles.com Team';
        $item->owner_id = Yii::app()->user->id;
        if (!empty($file['name'])) {
            $filename = explode('.', $file['name']);
            $item->title = htmlspecialchars($filename[0]); // Отбрасываем расширение
        }
die(print_r($item));
        if (null === @$album) { // Если альбома не существует, создаем новый пользовательский от user->id
            $album = new Album;                      // Инициализируем новый альбом
            $album->owner_id = Yii::app()->user->id; // Владелец альбома
            $album->parent_id = Yii::app()->params['userAlbumID']; // Родитель (7298)
            $album->title = empty($user['fullname'])?Yii::app()->user->name:$user['fullname'];// полное имя/логин
            if ($album->validate()) $album->save(); // Валидация, сохранение, получение ID нового альбома
        }
        $albumID = empty($album['id'])?null:$album['id']; // Получаем ID альбома для вставки в AlbumItem (сост. ключ)
        $item->currentAlbumID = $albumID; // Для проверки уникальности (album + componentUrl)
        if ($item->validate()) {
            if ($item->save()) {
                $item->fileCreate($file); // Ресайз и раскидывание по директориям
            }
        }
    }

    /**
     *
     */
    public static function setFlash($name, $text='From EXIF')
    {
        if (Yii::app()->getModule('user')->isAdmin()) {
            Yii::app()->user->setFlash($name, $text);
            //Yii::app()->request->cookies[$name] = new CHttpCookie($name, $text);;
        }
    }

    /**
     * Извлечение даты съемки / даты последнего изменения файла
     *
     * @param $file [Путь к файлу изображения]
     * @return string
     */
    public static function getExifDate($file)
    {
        $exif = @read_exif_data($file);          // Получаем exif-информацию из изображения

        if (!empty($exif['DateTimeOriginal'])) { // Если существует дата съемки (Date taken)
            $tmp  = @date_create_from_format('Y:m:d H:i:s', $exif['DateTimeOriginal']);
            $date = @date_format($tmp, 'Y-m-d');
            self::setFlash('fromExif', 'From EXIF'); // Выставляет флажок для editPuzzle

        } elseif (!empty($exif['DateTime'])) {   // Если существует дата изменения (Date modified)
            $tmp  = @date_create_from_format('Y:m:d H:i:s', $exif['DateTime']);
            $date = @date_format($tmp, 'Y-m-d');
            self::setFlash('fromExif', 'From EXIF');

        } elseif (!empty($exif['FileDateTime'])) { // Если существует дата создания в EXIF
            $date = @date("Y-m-d", $exif['FileDateTime']);
            self::setFlash('fromExif', 'From EXIF');

        } else
            $date = @date("Y-m-d", filemtime($file)); // Время изменения файла

        return (null == $date) ? date("Y-m-d") : $date;
    }
}