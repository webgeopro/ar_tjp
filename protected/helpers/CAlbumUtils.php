<?php
/**
 * User: Vah
 * Date: 29.10.13
 * Утилиты для работы с альбомами
 */

class CAlbumUtils
{

    public static function getAlbumThumb($album)
    {
        if (empty($album['thumbnail_id'])) {
            return self::setAlbumThumb($album);

        } else {
            return $album['thumbnail_id'];
        }
    }

    /**
     * Получить миниатюру альбома
     */
    public static function getThumbID($albumID)
    {
        return Yii::app()->db
            ->createCommand('SELECT COUNT(*) FROM album_item WHERE album_id=:albumID')
            ->bindParam(':albumID', $albumID, PDO::PARAM_INT)
            ->queryScalar();
    }

    /**
     * Получить миниатюру, используемую по умолчанию
     */
    public static function getDefaultThumbID()
    {
        return Yii::app()->db->createCommand('SELECT id FROM item WHERE id=36')->queryScalar();
    }

    /**
     * Является ли альбом пользовательским
     */
    public static function isUserAlbum($albumID, $parentID='')
    {
        if (null == $parentID) {
            $userAlbumID = Yii::app()->db
                ->createCommand('SELECT parent_id FROM album WHERE id=:albumID')
                ->bindParam(':albumID', $albumID, PDO::PARAM_INT)
                ->queryScalar();

            if (Yii::app()->params['userAlbumID'] == $userAlbumID)
                return true;

        } else {
            if (Yii::app()->params['userAlbumID'] == $parentID)
                return true;
        }

        return false;
    }

    /**
     * Получить количество пазлов в альбоме
     */
    public static function getItemsCount($albumID, $isAdmin=false, $update=false)
    {
        if (null == (int)$albumID) return null;

        if ($isAdmin) { // Адм инистратор видит все пазлы
        }

        $cnt = Yii::app()->db
            ->createCommand('SELECT COUNT(*) FROM album_item WHERE album_id=:albumID')
            ->bindParam(':albumID', $albumID, PDO::PARAM_INT)
            ->queryScalar();

        if ($update) // Обновить счетчик пазлов в альбоме
            self::_setItemsCount($albumID, $cnt);

        return $cnt;
    }


    /**
     * Установка количества пазлов входящих в пользовательский альбом
     */
    private static function _setItemsCount($albumID, $cnt)
    {
        return Yii::app()->db
            ->createCommand('UPDATE album SET cnt=:cnt WHERE id=:albumID')
            ->bindParam(':cnt', $cnt, PDO::PARAM_INT)
            ->bindParam(':albumID', $albumID, PDO::PARAM_INT)
            ->execute();
    }

    /**
     * Установить миниатюру альбома
     *
     * @param $albumID
     * @param $thumbnailID
     * @return int
     */
    private static function _setThumb($albumID, $thumbnailID)
    {
        Yii::app()->db
            ->createCommand('UPDATE album SET thumbnail_id=:thumbnailID WHERE id=:albumID')
            ->bindParam(':albumID', $albumID, PDO::PARAM_INT)
            ->bindParam(':thumbnailID', $thumbnailID, PDO::PARAM_INT)
            ->execute();

        return $thumbnailID;
    }

    /**
     * Получить последний закаченный пазл пользователя
     *
     * @param $userID
     * @return int
     */
    public static function getLastUserItem($userID)
    {
        return Yii::app()->db
            ->createCommand('SELECT id FROM item WHERE owner_id=:userID ORDER BY id DESC LIMIT 1')
            ->bindParam(':userID', $userID, PDO::PARAM_INT)
            ->queryScalar();
    }

    /**
     * Получить последний закаченный пазл пользователя
     *
     * @param $albumID
     * @return int
     */
    public static function getLastAlbumItem($albumID)
    {
        return Yii::app()->db
            ->createCommand('SELECT item_id FROM album_item WHERE album_id=:albumID ORDER BY item_id DESC LIMIT 1')
            ->bindParam(':albumID', $albumID, PDO::PARAM_INT)
            ->queryScalar();
    }

    /**
     * Получение / формирование названия альбома
     * ???
     * @param $album
     * @return string
     */
    public static function getAlbumTitle($album)
    {
        if (self::isUserAlbum($album)) {
            $conn = Yii::app()->db;
            $fullname = $conn
                ->createCommand('SELECT fullname FROM user_profiles WHERE user_id=:userID')
                ->bindParam(':userID', $album['owner_id'], PDO::PARAM_INT)
                ->queryScalar();

        } else { // Основной альбом
            return $album['title'];
        }
    }

    /**
     * Получить componentUrl пользовательского альбома / имя пользователя
     *
     * @param int $ownerID user_id
     * @param string $username owner name
     * @return string componentUrl | username
     */
    public static function getUserAlbumUrl($ownerID, $username='')
    {
        $componentUrl = Yii::app()->db
            ->createCommand('SELECT componentUrl FROM album WHERE owner_id=:ownerID')
            ->bindParam(':ownerID', $ownerID, PDO::PARAM_INT)
            ->queryScalar();

        if (null == $componentUrl AND null == $username)
            $username = Yii::app()->db
                ->createCommand('SELECT username FROM user_users WHERE id=:ownerID')
                ->bindParam(':ownerID', $ownerID, PDO::PARAM_INT)
                ->queryScalar();

        return
            $componentUrl ? $componentUrl : $username;
    }


    /**
     * Получить имя альбома и его id по album componentUrl
     */
    public static function getAlbumByUrl($albumComponentUrl)
    {
        if (null == $albumComponentUrl)
            return null;
        $albumComponentUrl = explode('/', $albumComponentUrl);
        $addCondition = '';
#die(print_r($albumComponentUrl));
        if (!empty($albumComponentUrl[1])) {
            if ('user-albums' == strtolower($albumComponentUrl[0])) { // Пользовательский альбом
                $addCondition = ' AND parent_id=' . Yii::app()->params['userAlbumID'];
                $albumComponentUrl = $albumComponentUrl[1];
            } else
                $albumComponentUrl = $albumComponentUrl[0];
        } elseif(!empty($albumComponentUrl[0]))
            $albumComponentUrl = $albumComponentUrl[0];

        //die(print_r($albumComponentUrl));die("\$albumComponentUrl=$albumComponentUrl");
        return Yii::app()->db
            ->createCommand('SELECT id, parent_id, componentUrl, title FROM album WHERE componentUrl=:cUrl'.$addCondition)
            ->bindParam(':cUrl', $albumComponentUrl, PDO::PARAM_STR)
            ->queryRow();
    }

    /**
     * Получение имени владельца альбома
     *
     * @param $userID
     * @return string
     */
    public static function getFullname($userID)
    {
        return Yii::app()->db
            ->createCommand('SELECT fullname FROM user_profiles WHERE user_id=:userID')
            ->bindParam(':userID', $userID, PDO::PARAM_INT)
            ->queryScalar();
    }

    public static function setAlbumThumb($album)
    {
        if (self::isUserAlbum($album['id'], $album['parent_id'])) { // Если пользовательский альбом

            if ( self::getItemsCount($album['id']) ) { // Альбом не пустой
                if ($itemID = self::getLastAlbumItem($album['id']))
                    $thumbnailID = self::_setThumb($album['id'], $itemID);

            } else { // Пустой альбом
                if ($itemID = self::getLastUserItem($album['owner_id'])) // Есть закаченный пользователем пазл
                    $thumbnailID = self::_setThumb($album['id'], $itemID);

                else // Ставим картинку по умолчанию
                    $thumbnailID = self::_setThumb($album['id'], self::getDefaultThumbID());
            }
        }

        return $thumbnailID ? $thumbnailID : null;
    }

    /**
     * Получение владельца альбома по переданному componentUrl
     *
     * @param $componentUrl Адрес пользовательского альбома (без User-Albums)
     * @param $isUser boolean Если запрашивается пользовательский альбом
     * @return array | null users.* + profiles.fullname
     */
    public static function getUser($componentUrl, $isUser=false)
    {
        $addCondition = $isUser
            ? ' AND parent_id=' . Yii::app()->params['userAlbumID']
            : '';
        $userID = Yii::app()->db
            ->createCommand('SELECT owner_id FROM album WHERE componentUrl=:componentUrl' . $addCondition)
            ->bindParam(':componentUrl', $componentUrl, PDO::PARAM_STR)
            ->queryScalar();
//die("\$userID=$userID");
        if (null != $userID)
            return Yii::app()->db
                ->createCommand('
                    SELECT u.id, u.username, u.email, p.fullname
                    FROM user_users u
                    LEFT JOIN user_profiles p
                      ON u.id = p.user_id
                    WHERE u.id = '.$userID.'
                ')
                ->queryRow();

        return null;
    }

    /**
     * Получение пазла по его componentUrl, принадлежащему альбому с albumID
     * Вспомогательная функция
     *
     * @param $albumID
     * @param $itemComponentUrl
     * @return int itemID
     */
    public static function getItemIdInAlbum($albumID, $itemComponentUrl)
    {
        return Yii::app()->db
            ->createCommand('
                SELECT i.id
                FROM album_item ai
                LEFT JOIN item i
                  ON ai.album_id = :albumID AND ai.item_id=i.id
                WHERE i.componentUrl = :componentUrl
                LIMIT 1;
            ')
            ->bindParam(':albumID', $albumID, PDO::PARAM_INT)
            ->bindParam(':componentUrl', $itemComponentUrl, PDO::PARAM_STR)
            ->queryScalar();
    }

    /**
     * Получение пазла, входящего в альбом
     * Используется механизм Active Record
     *
     * @param string $albumComponentUrl
     * @param string $itemComponentUrl
     * @param bool $getAlbum
     * @return array CActiveRecord | null
     */
    public static function getItemAR($albumComponentUrl='', $itemComponentUrl, $getAlbum=false)
    {
        $album = self::getAlbumByUrl($albumComponentUrl); // array(id, parent_id, ...)
        // todo Получить AR Album
//die("\$itemComponentUrl=$itemComponentUrl");//die(print_r($album));
        if (null != $album) { // Учитываем альбом
            $itemID = self::getItemIdInAlbum($album['id'], $itemComponentUrl);//die(print_r($itemID));
            $item = Item::model()->with('attr')->findByPk($itemID);
        } else
            $item = Item::model()->with('attr')->findByAttributes(array('componentUrl'=>$itemComponentUrl));

        return $getAlbum
            ? array($album, $item)
            : $item;
    }

    /**
     * Получение пазла по его ID
     * Используется механизм Active Record
     *
     * @param $itemID
     * @param bool $getAlbum
     * @return array
     */
    public static function getItemARFromItemID($itemID, $getAlbum=false)
    {
        $item = Item::model()->with('attr')->findByPk($itemID);
        if (null != $item) {
            $album = self::getAlbumFromItemID($item['id']); // Ищем первый альбом пазла (На данный момент - единственный)
            return $getAlbum
                ? array($album, $item)
                : $item;
        }
        return null;
    }

    /**
     * Получение альбома по ID пазла, входящего в него
     *
     * @param $itemID
     * @return mixed
     */
    private static function getAlbumFromItemID($itemID)
    {
        return Yii::app()->db
            ->createCommand('
                SELECT a.*
                FROM album_item ai
                LEFT JOIN album a
                  ON ai.album_id=a.id
                WHERE ai.item_id = :itemID
                LIMIT 1;
            ')
            ->bindParam(':itemID', $itemID, PDO::PARAM_INT)
            ->queryRow();
    }

    /**
     * Получение ID всех пазлов, входящих в альбом
     *
     * @param $album
     */
    public static function getAllItemsIDsFromAlbum($album)
    {
        if (is_array($album) AND !empty($album['id'])) // Передан массив, связанный с альбомом (id, parent_id и т.д.)
            $albumID = $album['id'];
        elseif ((int)$album) // Передан ID альбома
            $albumID = $album;

        return Yii::app()->db
            ->createCommand('SELECT item_id FROM album_item WHERE album_id = :albumID')
            ->bindParam(':albumID', $albumID, PDO::PARAM_INT)
            ->queryColumn();
    }

    /**
     * Получение полей всех пазлов, входящих в альбом
     *
     * @param $album
     */
    public static function getAllItemsFromAlbum($album)
    {
        if (is_int($album)) // Передан ID альбома
            $albumID = $album;
        elseif (is_array($album) AND !empty($album['id'])) // Передан массив, связанный с альбомом (id, parent_id и т.д.)
            $albumID = $album['id'];

        return Yii::app()->db
            ->createCommand('
                SELECT i.id, i.title, i.componentUrl, i.cut, i.width, i.height
                FROM item i
                LEFT JOIN album_item ai
                  ON ai.item_id = i.id
                WHERE ai.album_id = :albumID')
            ->bindParam(':albumID', $albumID, PDO::PARAM_INT)
            ->queryAll();

    }

}