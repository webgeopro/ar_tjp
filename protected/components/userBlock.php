<?php
/**
 * Отображение верхней пользовательской полоски.
 *
 */

class userBlock extends CWidget {

    public $isAlbum = false; // Находимся ли мы на странице альбома
    public $user;            // Пользователь
    public $userAlbumLink;   // Ссылка на пользовательский альбом
    public $isOwner = false; // Является ли пользователь владельцем альбома
    public $album;           // Объект Альбом [/models/Album]
    public $albumUrl;        // Для добавления к makeapuzzle?album=$albumUrl

    /**
     * Первоначальная настройка
     */
    public function init()
    {
        $this->getUser();
        $this->getAlbum();
        $this->isOwner();
        $this->albumUrl = $this->getAlbumUrl();
    }

    /**
     * Основное действие виджета
     */
    public function run()
    {
        $this->render('userBlock');
	}

    /**
     * Получение данных о пользователе
     *
     * @return bool
     */
    private function getUser()
    {
        //$user = User::model()->with('profile')->findByPk(Yii::app()->user->id);
        if (Yii::app()->user->id) {
            $user = Yii::app()->db
                ->createCommand('
                    SELECT u.*, p.fullname FROM user_users u
                    LEFT JOIN user_profiles p
                      ON u.id = p.user_id
                    WHERE u.id = '. Yii::app()->user->id .'
                    LIMIT 1
                ')
                ->queryRow();

            $this->user = $user;
        }
        /*if (null != $user)
            return true;

        return false;*/
    }

    /**
     * Получить ссылку на альбом пользователя
     */
    private function getAlbum()
    {
        if (null != $this->user) {
            $album = Yii::app()->db
                ->createCommand('SELECT componentUrl FROM album WHERE owner_id='. $this->user['id'])
                ->queryScalar();

            if (null != $album)
                $this->userAlbumLink = '/User-Albums/' . $album . Yii::app()->params['urlSuffix'];

            #return true;
        }
        #return false;
    }

    /**
     * Является ли пользователь владельцем альбома или администратором
     *
     * @return bool
     */
    private function isOwner()
    {
        if (null != $this->user && null != $this->album) {
            if (is_object($this->album))
                $albumOwner = @$this->album->owner_id;
            else
                $albumOwner = @$this->album['owner_id'];
            if ($this->user['id'] == $albumOwner || Yii::app()->getModule('user')->isAdmin())
                $this->isOwner = true;
        }
    }

    /**
     * Получение ссылки для makeapuzzle
     * [/makeapuzzle?album=URL]
     */
    private function getAlbumUrl()
    {
        if (is_object($this->album))
            $albumParentID = @$this->album->parent_id;
        else
            $albumParentID = @$this->album['parent_id'];

        if (null != $this->album AND Yii::app()->params['userAlbumID'] != $albumParentID)
            return '?album='.(is_object($this->album)
                ? @$this->album->componentUrl
                : @$this->album['componentUrl']
            );

        return null;
    }
}