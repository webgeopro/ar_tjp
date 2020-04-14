<?php
/**
 * Виджет "Системные ссылки". systemLinks
 * Ссылки на возможные действия в самом низу страницы
 */

class systemLinks extends CWidget {

    public $myPuzzlesAddress; // Адрес ссылки "My Puzzles"

    /**
     * Предварительные настройки
     */
    public function init()
    {
        if (!Yii::app()->user->isGuest)
            $this->myPuzzlesAddress =
                '/User-Albums/'
                . CUserAlbums::getUserAlbumNameFromUserId(Yii::app()->user->id)
                . Yii::app()->params['urlSuffix'];
    }

    public function run()
    {
        if (Yii::app()->getModule('user')->isAdmin()) { // Список действия для администратора
            $actions = array(
                'Help'  => '/info/help',
                'Site Admin' => '/admin',
                'My Puzzles' =>  $this->myPuzzlesAddress,
                'My Profile' => '/user/profile/edit',
                'Privacy Policy' => '/info/policy',
                'Feedback'   => '/feedback',
                'Sign Out'   => '/user/logout',
            );
        } elseif (!Yii::app()->user->isGuest) { // Список действия для зарегистрированного пользователя
            $actions = array(
                'Help'  => '/info/help',
                'My Puzzles' => $this->myPuzzlesAddress,
                'My Profile' => '/user/profile/edit',
                'Privacy Policy' => '/info/policy',
                'Feedback'   => '/feedback',
                'Sign Out'   => '/user/logout',
            );
        } else { // Список действия для гостя
            $actions = array(
                'Help'  => '/info/help',
                'Sign In'  => '/user/login',
                'Sign Up'  => '/user/registration',
                'Privacy Policy' => '/info/policy',
                'Feedback'   => '/feedback',
            );
        }


        $this->render('systemLinks', array(
            'actions' => $actions,
        ));
	}
}