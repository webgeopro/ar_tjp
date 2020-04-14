<?php
/**
 * Формирование навигационной цепочки
 * User: Vah
 * Date: 27.11.13
 */

class UserBreadcrumbs {

    /**
     * Получение адреса пользовательского альбома
     *
     * @return array|null
     */
    public static function userAlbums()
    {
        if (Yii::app()->user->isGuest)
            return null;

        $albumName = '/'. Yii::app()->params['userAlbumName']
                   . '/'. CUserAlbums::getUserAlbumNameFromUserId(Yii::app()->user->id)
                   . Yii::app()->params['urlSuffix'];
        $fullname  = CUserAlbums::getFullnameFromUserId(Yii::app()->user->id);

        return array($fullname, $albumName);
    }
}