<?php
/**
 * Виджет Для отображения меню на странице администрирования.
 */

class menuAdmin extends CWidget {

    public $currentPage;         // Текущая страница
    public $contentMenu = false; // Отображать ли книтекстное меню
    public $menuList = array(    // Список пунктов меню и ассоциированных с ними контестных списков подменю
        /*'index' => array('Site Admin', '/admin/', array( // Индексная страница
                'first'=>'First Page', 'second'=>'Second Page',
            ), true,
        ),*/
        'editpuzzle' => array('Edit Puzzle', '/admin/', array( // Страница редактирования пазла
            'update_puzzle_part'=>'General', 'rotate_puzzle_part'=>'Rotate Puzzle',
            'crop_puzzle_part'=>'Crop Puzzle',
            ), false, // Не отображать слева в колонке
        ),
        'user' => array('Users', '/admin/', array( // Страница пользователей
            'first'=>'First Page', 'second'=>'Second Page',
            ), true,
        ),
        '' => array('Add Users', '/user/profile/admin/', array( // Страница добавления пользователя
            ), true,
        ),
        'addpuzzles' => array('Add Puzzles', '/admin/', array( // Страница пользователей
            'add_puzzle_web_part'=>'From Web', 'add_puzzle_disk_part'=>'From Disk',
            ), true,
        ),
        'addalbum' => array('Add Album', '/admin/', array( // Страница создания альбома
            ), true,
        ),
        'deletealbum' => array('Delete Album', '/admin/', array( // Страница удаления альбома
            ), true,
        ),
        'editalbum' => array('Edit Album', '/admin/', array( // Страница пользователей
            'edit_album_title_part'=>'Edit Title', 'edit_album_sort_part'=>'Sort Order',
            ), true,
        ),
    );

    public function run() {
        $currentPage = strtolower($this->currentPage); // В нижний регистр
        if (array_key_exists($currentPage, $this->menuList)) {
            $list = $this->menuList[$currentPage][2];
            //$address = Yii::app()->getBaseUrl(true).'/admin/'.$currentPage;
            $address = '/admin/getpage';
        } else {
            $list = array();
            $address = '/admin/getPage';
        }
        $this->render('menuAdmin', array(
            'list' => $list,
            'address' => $address,
        ));
	}
}