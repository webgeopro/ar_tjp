<?php
/**
 * Формирование навигационной цепочки
 * User: Vah
 * Date: 27.11.13
 */

class CBreadcrumbs {

    /**
     * Получение адреса пользовательского альбома
     *
     * @return array|null
     */
    public static function userAlbums()
    {
        if (Yii::app()->user->isGuest)
            return null;

        $albumName = Yii::app()->params['userAlbumName'] .'/'. CUserAlbums::getUserAlbumNameFromUserId(Yii::app()->user->id);
        $fullname  = CUserAlbums::getFullnameFromUserId(Yii::app()->user->id);

        return array($albumName => $fullname);
    }

    /**
     * Формирование элемента цепочки навигации
     *
     * @param $ob Сам элемент
     * @param string $key Поле Title
     * @param string $value Поле Url (ComponentUrl)
     * @return array
     */
    public static function getNode($ob, $key='title', $value='componentUrl')
    {
        if (is_object($ob)) {
            $value = empty($ob->$value) ? '' : $ob->$value;
            $key   = empty($ob->$key) ? $value : $ob->$key;
            $out   = array($key ,$value);

        } elseif (is_array($ob)) {//die($ob[$value].'::');die(print_r($ob));die('HERE3');
            $value = empty($ob[$value]) ? '' : $ob[$value];
            $key   = empty($ob[$key]) ? $value : $ob[$key];
            $out   = array($key ,$value);

        } else
            $out = array($ob, $ob);

        return $out;
    }
}