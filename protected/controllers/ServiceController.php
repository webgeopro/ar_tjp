<?php

class ServiceController extends Controller
{
    public $album = null; // Объект альбом
    public $item  = null; // Объект пазл
    public $albumUser = null;
    public $ownerName; // Имя владельца альбома


    // Ранее было в init()
    public function getAlbum()
    {
        $getAlbum = Yii::app()->request->getParam('album', null);

        if (null != $getAlbum) {
            $getAlbum = explode('/', $getAlbum); // die(print_r($getAlbum));
            if (!empty($getAlbum[1])) {
                if ('user-albums' == strtolower($getAlbum[0])) // Пользовательский альбом
                    $this->ownerName = $getAlbum[1];
                $getAlbum = $getAlbum[1];

            } else $getAlbum = $getAlbum[0];
        }
        //die($this->ownerName); die(print_r($getAlbum));
        return $getAlbum;
    }

    /**
     * Определить, пользовательский ли альбом
     * @param $username
     */
    private  function isUserAlbum($username)
    {
        return Yii::app()->db
            ->createCommand("SELECT COUNT(*) FROM user_users WHERE username='$username'")
            ->queryScalar();
    }

    /**
     * Получение Album + Item в виде объекта AR, для использования с AR
     * Работает как фильтр
     */
    public function filterInitAR($chain)
    {
        $getAlbum = $this->getAlbum();
//die('Inside initAR' . print_r($getAlbum));
        if (null != $getAlbum) { // (!empty($_GET['album'])) {
            if (Album::model()->exists('componentUrl=:componentUrl', array(':componentUrl'=>$getAlbum))) {
                $this->album = Album::model()->findByAttributes(array('componentUrl'=>$getAlbum));
                if (!empty($_GET['item'])
                    AND Item::model()->exists('componentUrl=:componentUrl', array(':componentUrl'=>$_GET['item'])) )
                {
                    /*$this->item = Item::model()->with('attr', 'cut', 'album')
                        ->findByAttributes(array('componentUrl'=>$_GET['item']));*/
                    $this->item = CAlbumUtils::getItemAR($getAlbum, $_GET['item']);
                    #die(print_r($this->item));
                }
            } elseif ($this->isUserAlbum($_GET['album'])) { // Не сущ-ий пользовательский альбом
                $this->album->parent_id = Yii::app()->params['userAlbumID'];
                $this->album->componentUrl = $_GET['album'];
                $this->album->title = $_GET['album'];
            }
        } elseif (!empty($_GET['item'])
            AND Item::model()->exists('componentUrl=:componentUrl', array(':componentUrl'=>$_GET['item']))) {

            $this->item = Item::model()->with('attr', 'cut', 'album')->findByAttributes(array('componentUrl'=>$_GET['item']));
            $this->album = Album::model()->findByAttributes(array('owner_id'=>$this->item->owner_id));

        } elseif ( !empty($_POST['itemID']) OR !empty($_GET['itemID']) ) {
            $itemID = Yii::app()->request->getParam('itemID', '');
            if (Item::model()->exists('id=:itemID', array(':itemID'=>$itemID))) {
                $this->item = Item::model()->with('attr', 'cut', 'album')->findByPk($itemID);
            }
        } else {
            // Редирект на главную если альбом не существует
            /*if (!isset($_POST['cntUploadedFile']) OR !isset($_POST['item'])) // Если не со страницы makeapuzzle
                $this->redirect(Yii::app()->homeUrl);*/
        }
        $chain->run();
    }

    /**
     * Получение Album + Item в виде массива, для использования с DAO
     * Работает как фильтр
     */
    public function filterInitDao($chain)
    {
        $getAlbum = $this->getAlbum();
        $conn = Yii::app()->db;

        if ((null != $getAlbum)) {
            if (Album::model()->exists('componentUrl=:cUrl', array(':cUrl'=>$getAlbum))) {
                if (null != $this->ownerName)
                    $albumUser = CAlbumUtils::getUser($this->ownerName, true);
                if (!empty($albumUser['id']))
                    $this->album = $conn  //id,  thumbnail_id, parent_id, componentUrl, title
                        ->createCommand('SELECT * FROM album WHERE componentUrl=:cUrl AND owner_id=:ownerID LIMIT 1')
                        ->bindParam(':cUrl', $getAlbum, PDO::PARAM_STR)
                        ->bindParam(':ownerID', $albumUser['id'], PDO::PARAM_INT)
                        ->queryRow();
                else
                    $this->album = $conn  //id,  thumbnail_id, parent_id, componentUrl, title
                        ->createCommand('SELECT * FROM album WHERE componentUrl=:cUrl LIMIT 1')
                        ->bindParam(':cUrl', $getAlbum, PDO::PARAM_STR)
                        ->queryRow();

            if (empty($this->album['thumbnail_id'])) { // Не установлена миниатюра альбома
                //Устанавливаем последний пазл в качестве миниатюры. Если нет пазла, берем пазл по умолчанию.
                $this->album['thumbnail_id'] = CAlbumUtils::getAlbumThumb($this->album);
            }
            list($this->album['imgFullName'], $this->album['imgUrl']) = CImageSize::getPath($this->album['thumbnail_id']);

            if ( !empty($_GET['item']) AND Item::model()->exists('componentUrl=:cUrl', array(':cUrl'=>$_GET['item'])) )
                // album -> album_item -> item
                if (!empty($albumUser['id']))
                    $this->item = $conn->createCommand('
                            SELECT id, componentUrl, title, cut cutout, width, height
                            FROM item WHERE componentUrl=:cUrl AND owner_id=:ownerID LIMIT 1')
                        ->bindParam(':cUrl', $_GET['item'], PDO::PARAM_STR)
                        ->bindParam(':ownerID', $albumUser['id'], PDO::PARAM_INT)
                        ->queryRow();
                if (null == $this->item)
                    $this->item = $conn->createCommand('
                            SELECT id, componentUrl, title, cut cutout, width, height
                            FROM item i WHERE i.componentUrl=:cUrl LIMIT 1
                        ')->bindParam(':cUrl', $_GET['item'], PDO::PARAM_STR)->queryRow();

            list($this->item['imgFullName'], $this->item['imgUrl']) = CImageSize::getPath($this->item['id']);
            /*LEFT JOIN item_attributes ia ON i.id=ia.id*/ /*, ia.**/
            } elseif ($this->isUserAlbum($getAlbum)) {
                $this->album['parent_id'] = Yii::app()->params['userAlbumID'];
                $this->album['componentUrl'] = $getAlbum;
                $this->album['title'] = $getAlbum;
            }
        } elseif (!empty($_GET['item'])
            AND Item::model()->exists('componentUrl=:componentUrl', array(':componentUrl'=>$_GET['item']))) {
            echo '!empty($_GET[item]';
        } elseif ( !empty($_POST['itemID']) OR !empty($_GET['itemID']) ) {
            echo '!empty($_POST[itemID]) OR !empty($_GET[itemID])';
        } else {
            //die('Else');
        }
        $chain->run();
    }

    /**
     * Фильтр входных значений для действий:
     */
    public function filterParser($chain)
    {
        $albumName = Yii::app()->request->getParam('album', '');
        $itemName  = Yii::app()->request->getParam('item', '');

        list($this->album, $this->item) = CAlbumUtils::getItemAR($albumName, $itemName, true);

        $chain->run();
    }

    /**
     * Дефолтное действие
     */
    public function actionIndex()
	{
		$this->render('index', array('action'=>'default action Index'));
	}

    /**
     * Изменить миниатюру альбома текущим пазлом
     */
    public function actionMakeHighlight()
    {
        if (Yii::app()->getModule('user')->isAdmin()) { // Действие доступно только администраторам

            $inAlbum = AlbumItem::model()->exists( // Проверяем есть ли такой пазл в альбоме
                'album_id=:albumID AND item_id=:itemID',
                array(':albumID'=>$this->album['id'], ':itemID'=>$this->item['id'])
            );
            if ($inAlbum) { // Пазл присутствует в альбоме, изменяем миниатюру альбома
                $this->album->thumbnail_id = $this->item['id'];
                if ($this->album->save()) // Сохраняем новое значение
                    die(json_encode(array('result'=>'success')));
                else
                    die(json_encode(array('result'=>'errorSave')));
            } else {
                die(json_encode(array('result'=>'errorNotInAlbum')));
            }
        }

        die(json_encode(array('result'=>'errorAccess'))); // Возвращаем ошибку доступа
    }


    /**
     * Редактировать пазл
     */
    public function actionEditPuzzles()
    {
        Yii::app()->clientScript->registerPackage('editpuzzle');
        // Определение пользовательского альбома
        /*if (Yii::app()->params['userAlbumID'] == $this->album['parent_id']) { // User Album
            die('U-A'); die(print_r($this->album));

        } else {
            die('Non U-A');
        }*/

            if (empty($this->album['componentUrl'])) { // Пользовательские альбомы//0 == $this->album['parent_id']
                $albumUrl = CAlbumUtils::getUserAlbumUrl(Yii::app()->user->id, Yii::app()->user->name);
                $itemAddress = '/User-Albums/'. $albumUrl .'/'. $this->item['componentUrl']. Yii::app()->params['urlSuffix'];
                $albumAddress = '/User-Albums/'. $albumUrl .Yii::app()->params['urlSuffix'];
                $breadsCrumbsTitle = array($this->album, $this->album['parent_id']);

            } else { // Основные альбомы //die($this->album['componentUrl']);
                $itemAddress = '/'. $this->album['componentUrl']. '/'. $this->item['componentUrl']. Yii::app()->params['urlSuffix'];
                $albumAddress = '/'. $this->album['componentUrl']. Yii::app()->params['urlSuffix'];
                //$fullname = CAlbumUtils::getFullname($this->album['owner_id']);
                $breadsCrumbsTitle = array(
                    $this->album['title'] => $this->ownerName ? '/User-Albums'. $albumAddress : $albumAddress,
                    //$this->ownerName ? '/User-Albums'. $itemAddress : $itemAddress
                    $this->item['title']
                );
            }
        $albumComponentUrl = Yii::app()->request->getParam('albumComponentUrl', null);
        $form = ''; // Переменная для рендеринга внутреннего шаблона
        if ($albumComponentUrl AND Yii::app()->getModule('user')->isAdmin()) { // Тупая перебивка альбома если он передан админом из makeapuzzle
            $albumAddress = '/'. $albumComponentUrl. Yii::app()->params['urlSuffix'];
            $form.='<input type="hidden" name="albumComponentUrl" value="'.$albumComponentUrl.'"/>'; // Передаем альбом
        }

        if(!empty($_POST['cntUploadedFile'])) { // Обработка полученных значений // Отображение формы для makeApuzzle
            //die('cntUploadedFile');
            //@todo Меняем условие - для всех
            $cutout = Cutout::model()->findAll();//die(print_r($_POST));
            if (count($_POST['uploadedFile'])) {
                $items = Item::model()->with('attr')->findAllByPk($_POST['uploadedFile']);
            } else {
                $items = array();
                //die('Выборка из БД последних (за сегодня файлов)');
                //$items = Item::model()->findAllByPk($_POST['uploadedFile']); // Последние
            }

            foreach ($items as $item) {
                $form .= $this->renderPartial('_formedit', array( // Отображаем форму
                    'album' => $item->album,
                    'item'  => $item,
                    'itemWidth'  => Yii::app()->params['thumbnailSize'][0],
                    'itemHeight' => Yii::app()->params['thumbnailSize'][1],
                    'itemAddress' => '/User-Albums/'.Yii::app()->user->name.'/'.$item['componentUrl'].Yii::app()->params['urlSuffix'],
                    'listCutout'  => CHtml::listData($cutout, 'id', 'name'),
                    'cutout' => $cutout,
                ), true);
            }
            $this->render('editPuzzles', array('content' => $form,)); // Отображаем форму

        } elseif (isset($_POST['item'])) { // Обработка результатов редактирования

            if (Yii::app()->getModule('user')->isAdmin() AND $albumComponentUrl)
                $currentAlbumID = CAlbumUtils::getAlbumByUrl($albumComponentUrl);

            foreach ($_POST['item'] as $id=>$item) { //die(print_r($_POST));
                if (Item::model()->exists('id=:itemID', array(':itemID'=>$id))) {
                    $obj = Item::model()->findByPk($id);

                    if (isset($item['chItemDelete'])) {$obj->delete(); continue;} // Удаляем
                    if ($item['angle']) { // Передан угол поворота
                        @$obj->resize($item['angle']); // Вращение
                        Yii::app()->user->setFlash('updatePuzzles', 1); // Устанавливаем куки для обновления кеша картинок (в случае их поворота)
                    }
                    $obj->attributes = $item;
                    if (!empty($currentAlbumID['id'])) // Если пазл админом в editorial
                        $obj->currentAlbumID = $currentAlbumID['id'];
                    $obj->cut = $item['cutout'];

                    if ($obj->validate()) // Проверка корректности переданных значений
                        $obj->save(); // Сохранение пазла
                }
            }
            if ($albumComponentUrl)
                $this->redirect('/'. $albumComponentUrl .Yii::app()->params['urlSuffix']);     // Переход на страницу основного альбома
            $this->redirect($this->ownerName ? '/User-Albums'. $albumAddress : $albumAddress); // Переход на страницу пользовательского альбома

        } elseif (empty($_POST['serviceToken'])) { // Отображение формы редактирования todo Унифицируем. Оптимизируем!!!
            //die(print_r($breadsCrumbsTitle));die('serviceToken');
            if (null === @$this->item->owner_id) $this->redirect('/'); // Переход на главную если нет владельца
            if (null === $this->album) { // Если нет альбома, создаем url на пользовательский альбом
                $owner = User::model()->findByPk($this->item->owner_id);
                $this->album = new Album;
                $this->album->componentUrl = $owner->username;
            }
            $this->layout = 'serviceUser';
            $cutout = Cutout::model()->findAll();
            $this->render( 'editPuzzlesUser', array( // Отображаем форму
                'album' => $this->album,
                'item'  => $this->item,
                'itemWidth'  => Yii::app()->params['thumbnailSize'][0],
                'itemHeight' => Yii::app()->params['thumbnailSize'][1],
                'itemAddress' => $itemAddress,
                'listCutout'  => CHtml::listData($cutout, 'id', 'name'),
                'cutout' => $cutout,
                'breadsCrumbsTitle' => isset($breadsCrumbsTitle) ? $breadsCrumbsTitle : '',
            ));

        } else {die('edit. old.'); // Обработка полученных значений // Устаревшее
            if (!empty($_POST['chItemDelete'])) { // Если отмечен checkbox 'удалить' - удаляем пазл
                if ($this->item->delete())
                    $this->redirect($albumAddress); // Переход на страницу альбома
                else
                    $this->refresh(); // Прекращаем дальнейшие изменения и сбрасываем переданные POST-ом значения
            }
            // Вращение пазла
            if (!empty($_POST['inpRotateAngle'])) { // Изменена ориентация пазла. Вращаем. Сохраняем.
                // Работаем с изменением изображения (проверять ли кратность 90?)
                $this->item->resize($_POST['inpRotateAngle']);
            }
            // Простое редактирование атрибутов
            $this->item->description = trim($_POST['item']['description']);
            $this->item->title = trim($_POST['item']['title']);
            $this->item->cutout = $_POST['cutout'];
            if ($this->item->validate() ) { // Если значение корректны
                if ($this->item->save()) // Обновляем характеристики пазла (+ внутри модели title, dateModified)
                    $this->redirect($albumAddress);
            }
            $this->refresh();
        }
    }

    /**
     * Удалить пазл.
     * Атрибуты из связанных таблиц удаляются в модели Item автоматически.
     */
    public function actionDeletePuzzle()
    {
        if (!Yii::app()->request->isAjaxRequest) Yii::app()->end(); // Только ajax-запросы

        if ( Yii::app()->getModule('user')->isAdmin() // Действие доступно только администраторам
             OR ($this->item['owner_id'] == Yii::app()->user->id) ) { // либо владельцу пазла ||  AND $this->item['id']

            if (Item::model()->findByPk($this->item['id'])->delete()) // Удаляем пазл
                die(json_encode(array(
                    'result'=>'success',
                    'returnUrl'=>Yii::app()->getBaseUrl(true).'/'.$this->album['componentUrl'].Yii::app()->params['urlSuffix'],
                )));
            else
                die(json_encode(array('result'=>'errorDelete')));
        }

        die(json_encode(array('result'=>'errorAccess'))); // Возвращаем ошибку доступа
    }

    /**
     * Переместить пазл (может принадлежать нескольким альбомам)
     * Доступно только администратору (через filters+accessControl)
     */
    public function actionMovePuzzle()
    {
        Yii::app()->clientScript // Подключаем необходимые ccs-, js-файлы
            ->registerScriptFile('/js/jquery.js', CClientScript::POS_HEAD)
            ->registerScriptFile('/js/common.js', CClientScript::POS_HEAD) // Общий файл сценариев
        ;
        $itemID     = Yii::app()->request->getParam('itemID');
        $albumID    = Yii::app()->request->getParam('albumID');
        $albumOldID = Yii::app()->request->getParam('albumOldID');

        if (Yii::app()->request->isPostRequest) { // Только POST-запрос
            if ($itemID AND $albumID AND $albumID != $albumOldID) { // Все поля + перенос в др. альбом
                if (Album::model()->exists('id=:albumID', array(':albumID'=>$albumID))) { // Альбом существует в БД
                    $item = Item::model()->findByPk($itemID);
                    if (null != $item) {//die("\$itemID=$itemID::\$albumID=$albumID::\$albumOldID=$albumOldID." . print_r($item));
                        $item->currentAlbumID = $albumID; // Новый альбом (Обрабатывается внутри models/Item)
                        $userID = CUserAlbums::getUserIDFromAlbumID($albumID); // Владелец нового альбома
                        if ($userID)
                            $item->owner_id = $userID; // Задаем нового владельца пазла (владелец альбома)
                        if ($item->validate()) {
                            @$item->setCount($albumID); // Пересчитываем кол-во пазлов в новом альбоме
                            if ($item->save()) {
                                $ai = AlbumItem::model()->findByAttributes(array(
                                    'item_id'  => $itemID,
                                    'album_id' => $albumOldID,
                                ));                  // Выбираем связь с прежним альбомом и удаляем её.
                                if (null != $ai)     // Проводим через ядро с обработкой beforeDelete
                                    $ai->delete();   // в котором устанавливается new thumb.
                                $album = Album::model()->findByPk($albumID);
                                if (null != $album) { // Новый альбом выбран
                                    Yii::app()->user->setFlash('movePuzzleResult', 'Move Puzzle: Success.');
                                    $this->redirect($this->getReturnUrl($album, $item->componentUrl));
                                }
                            }
                        } else die(print_r($item));
                    }
                }
            }
            $this->refresh(); // Сбрасываем post значения
        }

        $this->render('movePuzzle', array(
            'item'  => $this->item,
            'album' => empty($this->album['id']) ? null : $this->album,
        ));
    }

    /**
     * Добавить альбом
     */
    public function actionAddAlbum()
    {
        $this->render('index', array(
            'action' => 'Action Add-Album',
            'album'  => $this->album,
        ));
    }

    /**
     * Добавить Пазл
     */
    public function actionAddPuzzle()
    {
        $this->render('index', array('action'=>'Action Add-Puzzle'));
    }

    /**
     * Добавить на веб-сайт
     */
    public function actionAddToWebsite()
    {
        if (null != $this->item AND !empty($this->item['id'])) {
            $suffix = '/'.$this->item['imgUrl'].'/'.$this->item['imgFullName'].'.jpg'; // Окончание пути к файлу
            if (file_exists(Yii::app()->params['pathSource'].$suffix))
                $image = Yii::app()->params['pathSource'].$suffix; // Для пазлов созданных администратором
            else
                $image = Yii::app()->params['pathOriginal'].$suffix; // Для пользовательских пазлов

            $cutout = isset($this->item['cutout'])
                ? $this->item['cutout']
                : Yii::app()->params['defaultCutout'];

            $paramToFlash = // Информация для flash-движка собирания пазла
                'sourceImageURL='.Yii::app()->getBaseUrl(true).urlencode($image)
                . '&puzzleId='.urlencode($this->item['id'])
                . '&puzzleName='.$this->item['title'] //urlencode($this->item['title'])
                . '&puzzleThumbId='.urlencode($this->item['componentUrl'])
                . '&cutout='.urlencode($cutout)
                . '&isEmbedded=1';
        }
        $albumPrefix = Yii::app()->params['userAlbumID'] == $this->album['parent_id']
            ? '/' . Yii::app()->params['userAlbumName'] .'/'
            : '/';
        if (!empty($this->album['componentUrl']))
            $breadcrumbs[$this->album['title'] ? html_entity_decode($this->album['title']) : $this->album['componentUrl']] =
                $albumPrefix . $this->album['componentUrl'] . Yii::app()->params['urlSuffix'];
        if (!empty($this->item['componentUrl']))
            $breadcrumbs[$this->item['title'] ? html_entity_decode($this->item['title']) : $this->item['componentUrl']] =
                $albumPrefix . $this->album['componentUrl'] .'/'. $this->item['componentUrl'] . Yii::app()->params['urlSuffix'];
        $breadcrumbs[] = 'Add to website';
//(Yii::app()->params['userAlbumID'] == $album['parent_id'])?Yii::app()->params['userAlbumName'].'/':''><=$album['componentUrl']>/<=$item['componentUrl']>

        $this->render('add2web', array(
            'item'  => $this->item,
            'album' => $this->album,
            'paramToFlash' => empty($paramToFlash) ? null : $paramToFlash,
            'cutout' => isset($cutout) ? $cutout : Yii::app()->params['defaultCutout'],
            'breadcrumbs' => $breadcrumbs,
        ));
    }

    /**
     * Удалить альбом
     */
    public function actionDeleteAlbum()
    {
        $this->render('index', array('action'=>'Action Delete-Album'));
    }


    /**
     * Редактировать пазл
     */
    public function actionEditAlbum()
    {
        $this->render('index', array('action'=>'Action Edit-Album'));
    }

    /**
     * Переместить альбом
     * Альбомы - древовидная структура
     * todo Tree Behavior для Active Record
     */
    public function actionMoveAlbum()
    {
        $this->render('index', array('action'=>'Action Move-Album'));
    }

    /**
     * Автодополнение для поля ввода альбома на стр. move-puzzle
     */
    public function actionAlbumsComplete()
    {
        $title = Yii::app()->getRequest()->getParam('term');

        if(Yii::app()->request->isAjaxRequest && $title) {
            $albums = Album::model()->findAll(array('condition'=>"title LIKE '%$title%'"));
            $result = array();
            foreach($albums as $album)
                $result[] = array('id'=>$album['id'], 'label'=>$album['title'], 'value'=>$album['title']);

            echo CJSON::encode($result);
            Yii::app()->end();
        }
    }

    /**
     * Формирование GET-параметров (componetUrl альбома + componetUrl пазла) адресной строки
     *
     * @param array $album
     * @param null $itemComponentUrl
     * @param bool $usePathInfo
     * @return string
     */
    private function getReturnUrl($album, $itemComponentUrl, $usePathInfo=true)
    {
        $albumUrl = '?album=';
        $itemUrl = '&item=' . $itemComponentUrl;

        $albumUrl .= (Yii::app()->params['userAlbumID'] == $album['parent_id'])
            ? Yii::app()->params['userAlbumName'] . '/' . $album['componentUrl']
            : $album['componentUrl'];

        return Yii::app()->getBaseUrl(true) // http://имя_сайта
        . '/'
        . ($usePathInfo ? Yii::app()->request->pathInfo : '') // Добавляем controller + action
        . '/'
        . $albumUrl . $itemUrl; // album componentUrl + item componentUrl
    }

    /**
     * фильтр доступа на основе ролей.
     * Роли определяются в accessRules()
     *
     * @return array
     */
	public function filters()
	{
        return array(
            'accessControl',
            'InitAR + makeHighlight editPuzzles deletePuzzle movePuzzle addAlbum',
            'parser + deletePuzzle',
            //'InitAR - addToWebsite',
            'initDao + addToWebsite',
		);
	}

    /**
     * Правила доступа на основе ролей.
     * Авторизация через сторонний модуль User
     *
     * @return array
     */
    public function accessRules()
    {

        return array(
            array('allow', 'actions'=>array('addToWebsite', 'albumsComplete'), 'users'=>array('*'),),

            array('deny', // Запрещаем доступ для гостей
                'actions'=>array(
                    'editPuzzles', 'makeHighlight', 'movePuzzle', 'deletePuzzle', 'makeapuzzle',
                    'deleteAlbum', 'AddAlbum', 'addPuzzle', 'editAlbum', 'moveAlbum',
                ),
                'users'=>array('?'),
            ),
            array('allow', // Разрешаем для зарегистрированных пользователей
                'actions'=>array('editPuzzles', 'deletePuzzle', 'makeapuzzle',),
                'users'=>array('@'), //*
            ),
            array('allow', // Разрешаем только для администраторов
                'actions'=>array(
                    'makeHighlight', 'movePuzzle',
                    'deleteAlbum', 'AddAlbum', 'addPuzzle', 'editAlbum', 'moveAlbum',
                ),
                'users'=>Yii::app()->getModule('user')->getAdmins(), //roles
            ),

            array('deny','users'=>array('*'),),
        );
    }
}