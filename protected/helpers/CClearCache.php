<?php
/**
 * Принудительная очистка кеша.
 * Вспомогательные функции (поиск страницы альбома с пазлом)
 * User: Vah
 * Date: 11.07.13
 */

class CClearCache {

    /**
     * Поиск страницы альбома, содержащей переданный пазл
     *
     * @param $item [объект Пазл]
     * @return null
     */
    public static function getAlbumPage($item)
    {
        if (null == $item) return null;

        //
    }

    /**
     * Очистка кеша страницы
     *
     * @param $address [адрес удаляемой из кеша страницы альбома]
     */
    public static function clearPage($address)
    {
        // Пользовательские альбомы
        // Controller . 'useralbum_list_' . $this->albumUser->id . '_' . $this->num . '_' . $pages->currentPage;
        // Основные альбомы
        //'album_list_'.$url.'_'.$this->num.'_'.$pages->currentPage;
        // widget('albumList', array('duration'=>0))
    }

    /**
     * Получить альбомы, содержащие пазл
     *
     * @param $item
     * @return array
     */
    public static function getAlbums($item)
    {
        return array();
    }
}