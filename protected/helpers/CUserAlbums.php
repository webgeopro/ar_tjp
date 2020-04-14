<?php
/**
 * User: Vah
 * Date: 18.11.13
 * Класс для работы с пользовательскими альбомами
 */

class CUserAlbums {

    /**
     * Предварительные настройки
     */
    protected static function init()
    {
    }

    /**
     * Получение пазлов входящих в пользовательский альбом
     */
    public static function getItems()
    {
        self::init();
    }

    /**
     * Получение пазла входящего в пользовательский альбом
     *
     * @param $componentUrl
     * @param $owner
     * @return Array Пазл + imgFullName + imgUrl
     */
    public static function getItem($componentUrl, $owner=null)
    {//die("\$owner=$owner");
        $userAlbumID = self::getUserAlbumIdFromUserId($owner); //die("\$userAlbumID=$userAlbumID");
        $sql = self::getOneSQL($componentUrl, $userAlbumID); // Формирование текста SQL-запроса

        $item = Yii::app()->db                               // Выборка пазла
            ->createCommand($sql)
            ->bindParam(':componentUrl', $componentUrl, PDO::PARAM_STR)
            ->queryRow();
        list($item['imgFullName'], $item['imgUrl']) = CImageSize::getPath($item['id']); // Добавление полей для
                                                                                        // форм-я пути к изображению
        if (CAlbumUtils::isUserAlbum($userAlbumID) AND $owner) // Если UA
            $item['attrAuthor'] = self::getFullnameFromUserId($owner);

        return $item;
    }

    /**
     * Получение количества пазлов входящих в пользовательский альбом
     */
    public static function getItemsCount($userID, $update=false)
    {
        if (null == $userID)
            return null;

        $albumID = self::getUserAlbumIdFromUserId($userID); // Получаем имя + id альбома пользователя
        if (null == $albumID)
            return null;

        $cnt = Yii::app()->db
                ->createCommand('SELECT COUNT(*) FROM album_item WHERE album_id=:albumID')
                ->bindParam(':albumID', $albumID, PDO::PARAM_INT)
                ->queryScalar();

        if ($update) // Обновить количество пазлов (cnt в album)
            self::setItemsCount($albumID, $cnt);

        return $cnt;
    }

    /**
     * Установка количества пазлов входящих в пользовательский альбом
     */
    public static function setItemsCount($userAlbumID, $cnt)
    {
        return Yii::app()->db
            ->createCommand('UPDATE album SET cnt=:cnt WHERE id=:albumID')
            ->bindParam(':cnt', $cnt, PDO::PARAM_INT)
            ->bindParam(':albumID', $userAlbumID, PDO::PARAM_INT)
            ->execute();
    }

    /**
     * Формирование запроса на выборку единичного пазла
     * Editorial
     */
    protected static function getOneSQL($componentUrl, $userAlbumID)
    {
        return $userAlbumID

            ?  'SELECT i.*, ia.keywords attrKeywords, ia.author attrAuthor
                FROM album_item ai
                LEFT JOIN item i ON i.id = ai.item_id
                LEFT JOIN item_attributes ia ON ia.id = i.id
                WHERE ai.album_id=' .$userAlbumID. ' AND i.componentUrl = "' .$componentUrl. '"
                LIMIT 1'

            :  'SELECT i.*, ia.keywords attrKeywords, ia.author attrAuthor
                FROM item i
                LEFT JOIN item_attributes ia ON ia.id = i.id
                WHERE i.componentUrl=:componentUrl
                LIMIT 1';
    }

    /**
     * Формирование запроса на выборку единичного пазла
     * Пользовательские альбомы
     *
     * @param $componentUrl адрес пазла
     * @param $ownerID int ID владельца альбома
     * @return string
     */
    protected static function getOneUserSQL($componentUrl, $ownerID)
    {
        $userAlbumID = self::getUserAlbumIdFromUserId($ownerID);
        // В РАБОТЕ...
        $item = 'SELECT i.*, ia.keywords attrKeywords, ia.author attrAuthor
            FROM album_item ai
            LEFT JOIN item i ON i.id = ai.item_id
            WHERE ai.album_id=' .$userAlbumID. ' AND i.componentUrl = "' .$componentUrl. '"
            LIMIT 1';
    }

    /**
     * Формирование запроса по выборке нескольких записей
     */
    protected static function getAllSQL()
    {

    }

    /**
     * Получение componentUrl альбома пользователя, зная ID владельца
     * Возвращение username в случае отсутствия componentUrl
     *
     * @param int $ownerID
     * @param string $username
     * @return string
     */
    public static function getUserAlbumNameFromUserId($ownerID, $username='')
    {
        $componentUrl = Yii::app()->db
            ->createCommand('SELECT componentUrl FROM album WHERE owner_id=:ownerID')
            ->bindParam(':ownerID', $ownerID, PDO::PARAM_INT)
            ->queryScalar();

        return (null == $componentUrl)
            ? $username
            : $componentUrl;
    }

    /**
     * Получить имя пользователя, зная имя альбома
     */
    public static function getUsernameFromAlbumName($albumName)
    {
        return Yii::app()->db
            ->createCommand('SELECT owner_id from album WHERE componentUrl=:albumName AND parent_id='.Yii::app()->params['userAlbumID'].' LIMIT 1')
            ->bindParam(':albumName', $albumName, PDO::PARAM_STR)
            ->queryScalar();
    }

    /**
     * Получить ID владельца альбома, зная ID альбома
     */
    public static function getUserIDFromAlbumID($albumID)
    {
        return Yii::app()->db
            ->createCommand('
                SELECT u.id from album a
                LEFT JOIN user_users u
                  ON u.id = a.owner_id
                WHERE a.id=:albumID
                LIMIT 1
            ')
            ->bindParam(':albumID', $albumID, PDO::PARAM_INT)
            ->queryScalar();
    }

    /**
     * Получение ID альбома пользователя, зная ID владельца
     *
     * @param $ownerID
     * @return mixed
     */
    protected static function getUserAlbumIdFromUserId($ownerID)
    {
        if ($ownerID)
            return Yii::app()->db
                ->createCommand('SELECT id FROM album WHERE owner_id=:ownerID')
                ->bindParam(':ownerID', $ownerID, PDO::PARAM_INT)
                ->queryScalar();

        return null;
    }

    /**
     * Получение ID альбома пользователя по ID владельца альбома
     *
     * @param string $fields Поля SQL-запроса
     * @param $ownerID int Владелец альбома
     * @return Array | null Альбом пользователя
     */
    public static function getUserAlbumFieldsFromUserId($fields='*', $ownerID)
    {
        if ($ownerID)
            return Yii::app()->db
                ->createCommand('SELECT ' .$fields. ' FROM album WHERE owner_id=:ownerID')
                ->bindParam(':ownerID', $ownerID, PDO::PARAM_INT)
                ->queryRow();

        return null;
    }

    /**
     * Получение названия альбома пользователя по адресу альбома
     * В случае отсутствия названия выдается полное имя пользователя
     *
     * @param $albumName
     * @param null $userProfile
     * @return mixed
     */
    public static function getUserAlbumTitleFromAlbumName($albumName, $userProfile=null)
    {
        $albumTitle = Yii::app()->db
            ->createCommand('SELECT title FROM album WHERE componentUrl=:albumName LIMIT 1')
            ->bindParam(':albumName', $albumName, PDO::PARAM_STR)
            ->queryScalar();

        if (null == $albumTitle)
            $albumTitle = empty($userProfile->fullname)
                ? $userProfile->user->username
                : $userProfile->fullname;

        return $albumTitle;
    }

    /**
     * Получение имени владельца альбома
     * (Дубль из CAlbumsUtils)
     * @param $userID
     * @return string
     */
    public static function getFullnameFromUserId($userID)
    {
        return Yii::app()->db
            ->createCommand('SELECT fullname FROM user_profiles WHERE user_id=:userID')
            ->bindParam(':userID', $userID, PDO::PARAM_INT)
            ->queryScalar();
    }

    /**
     * Обновление поля альбома, используя user id
     *
     * @param $userID
     * @param $field
     * @param string $value
     * @return bool
     */
    public static function saveAlbumFieldByUserId($userID, $field, $value='')
    {
        if (null == $userID OR '' == $field) return false;

        $albumID = self::getUserAlbumIdFromUserId($userID); // Получаем ID альбома
        if (null == $albumID) return false;

        $value = htmlspecialchars($value);

        return Yii::app()->db
            ->createCommand('UPDATE album SET `' .$field. '` = :val WHERE id = ' .$albumID)
            ->bindParam(':val', $value, PDO::PARAM_STR)
            ->execute();
    }
}