<?php
/**
 * Виджет "---". userActions
 * todo Сделать опрятным
 */

class userActions extends CWidget {

    public $album; // Текущий альбом
    public $albumUser; // Текущий пользователь(владелец) альбом
    public $item;  // Текущий пазл (может быть null)
    public $type='album'; // Разное отображение на cтранице альбома и пазла

    public function run()
    {//die(print_r($this->album));
        $prefix = $this->isUserAlbum()
            ? Yii::app()->params['userAlbumName'] . '/'
            : '';
        if ('album' == $this->type) { // Страница отображения альбома
            if(Yii::app()->user->isGuest) { // Список действия для гостя
                $actions = array(           // (url, css, title, {ссылка с id}, доп.поля)
                    'addToWebsite' => array('/service/add-to-website', 'gbLink-geturls_GetUrls', 'Add to Website/Blog'),
                );
            } elseif (Yii::app()->getModule('user')->isAdmin()) { // Список действия для администратора //'admin' ==
                $actions = array(
                    //'addAlbum'     => array('/service/add-album', 'gbLink-core_ItemAdmin-core_ItemAddAlbum', 'Add Album'),
                    'addItems'    => array('/admin/addpuzzles', 'gbLink-core_ItemAdmin-core_ItemAdd', 'Add Items'),
                    'addToWebsite' => array('/service/add-to-website', 'gbLink-geturls_GetUrls', 'Add to Website/Blog'),
                    'deleteAlbum'  => array('/admin/deletealbum', 'gbLink-core_ItemAdmin-core_ItemDelete-album gbLink-core_ItemAdmin-core_ItemDelete',
                        'Delete Album', 'deleteAlbum', 'id='.@$this->album['id']),
                    //'deletePuzzles'=> array('/service/delete-puzzles', 'gbLink-core_ItemAdmin-core_ItemDelete-album gbLink-core_ItemAdmin-core_ItemDelete', 'Delete Puzzles'),
                    'editAlbum'    => array('/admin/editalbum', 'gbLink-core_ItemAdmin-core_ItemEdit-album gbLink-core_ItemAdmin-core_ItemEdit', 'Edit Album'),
                    //'moveAlbum'    => array('/service/move-album', 'gbLink-core_ItemAdmin-core_ItemMove-album gbLink-core_ItemAdmin-core_ItemMove', 'Move Album'),
                    'editUsers' => array('/admin/user', 'gbLink-core_ItemAdmin-core_ItemEdit-album', 'Edit Users'),
                );
            } elseif ((!empty($this->album['owner_id']) AND $this->album['owner_id'] == Yii::app()->user->id)
                        OR null != $this->albumUser ) { // Список действия для владельца альбома
                //if (null != $this->albumUser) $this->album['componentUrl'] = $this->albumUser['username'];
                $actions = array( // Пользовательские альбомы
                    'addItems'    => array('/makeapuzzle', 'gbLink-simple_SimpleUpload', 'Make a Puzzle'),
                    'addToWebsite' => array('/service/add-to-website', 'gbLink-geturls_GetUrls', 'Add to Website/Blog'),
                    //'deleteAlbum'  => array('/service/delete-album', 'gbLink-core_ItemAdmin-core_ItemDelete-album gbLink-core_ItemAdmin-core_ItemDelete', 'Delete Album'),
                    //'deletePuzzles'=> array('/service/delete-puzzles', 'gbLink-core_ItemAdmin-core_ItemDelete-album gbLink-core_ItemAdmin-core_ItemDelete', 'Delete Puzzles'),
                    //'editAlbum'    => array('/service/edit-album', 'gbLink-core_ItemAdmin-core_ItemEdit-album gbLink-core_ItemAdmin-core_ItemEdit', 'Edit Album'),
                );
            } else { // Список действия для зарегистрированного пользователя не на своей странице
                $actions = array(
                    'addItems'    => array('/makeapuzzle', 'gbLink-core_ItemAdmin-core_ItemAdd', 'Add Items'),
                    'addToWebsite' => array('/service/add-to-website', 'gbLink-geturls_GetUrls', 'Add to Website/Blog'),
                );
            }
        } else { // Страница отображения пазла
            $itemOwner = isset($this->item->owner_id)
                ? $this->item->owner_id
                : isset($this->item['owner_id'])
                    ? $this->item['owner_id']
                    : null;

            if(Yii::app()->user->isGuest) { // Список действия для гостя
                $actions = array(           // (url, css, title)
                    //'downloadAllPuzzles' => array('http://kraisoft.com/files/everydayjigsaw.exe', 'gbLink-puzzle_DownloadEJ', 'Download All Puzzles'),
                    'addToWebsite' => array('/service/add-to-website', 'gbLink-geturls_GetUrls', 'Add to Website/Blog'),
                );
            } elseif (Yii::app()->getModule('user')->isAdmin()) { // Список действия для администратора //'admin' ==
                $actions = array(
                    //'downloadAllPuzzles' => array('http://kraisoft.com/files/everydayjigsaw.exe', 'gbLink-puzzle_DownloadEJ', 'Download All Puzzles'),
                    'addToWebsite'  => array('/service/add-to-website', 'gbLink-geturls_GetUrls', 'Add to Website/Blog'),
                    'deletePuzzle'  => array('/service/delete-puzzle', 'gbLink-core_ItemAdmin-core_ItemDelete-album gbLink-core_ItemAdmin-core_ItemDelete', 'Delete Puzzle', 'deletePuzzle'),
                    'editPuzzle'   => array('/admin/editpuzzle', 'gbLink-core_ItemAdmin-core_ItemEdit-album gbLink-core_ItemAdmin-core_ItemEdit', 'Edit Puzzle'),
                    'makeHighlight' => array('/service/make-highlight', 'gbLink-core_ItemAdmin-core_ItemAddAlbum', 'Make Highlight', 'makeHighlight'),
                    'movePuzzle'    => array('/service/move-puzzle', 'gbLink-core_ItemAdmin-core_ItemMove-album gbLink-core_ItemAdmin-core_ItemMove', 'Move Puzzle'),
                );
            } elseif ($itemOwner == Yii::app()->user->id) { // Список действия для владельца альбома
                $actions = array(
                    //'downloadAllPuzzles' => array('http://kraisoft.com/files/everydayjigsaw.exe', 'gbLink-puzzle_DownloadEJ', 'Download All Puzzles'),
                    'addToWebsite' => array('/service/add-to-website', 'gbLink-geturls_GetUrls', 'Add to Website/Blog'),
                    'deletePuzzle' => array('/service/delete-puzzle', 'gbLink-core_ItemAdmin-core_ItemDelete-album gbLink-core_ItemAdmin-core_ItemDelete', 'Delete Puzzle', 'deletePuzzle'),
                    'editPuzzle' => array('/service/edit-puzzles', 'gbLink-core_ItemAdmin-core_ItemEdit-album gbLink-core_ItemAdmin-core_ItemEdit', 'Edit Puzzle'),
                );
            } else { // Список действия для зарегистрированного пользователя
                $actions = array(
                    //'changeCut' => array('/service/change-cut', 'gbLink-puzzle_ChangeCut', 'Change Cut'),
                    //'downloadAllPuzzles' => array('http://kraisoft.com/files/everydayjigsaw.exe', 'gbLink-puzzle_DownloadEJ', 'Download All Puzzles'),
                    'addToWebsite' => array('/service/add-to-website', 'gbLink-geturls_GetUrls', 'Add to Website/Blog'),
                );
            }
        }

        $this->render('userActions', array(
            'actions' => $actions,
            'album'   => $this->album,
            'item'    => $this->item,
            'prefix'  => $prefix,
        ));
	}

    /**
     * Проверка является ли текущий альбом пользовательским.
     * (Проверяется поле parent_id)
     *
     * @return bool
     */
    private function isUserAlbum()
    {
        #if (null == $this->album)
        #    return false;
        if (!empty($this->album) AND Yii::app()->params['userAlbumID'] == $this->album['parent_id'])
            return true;

        return false;
    }
}