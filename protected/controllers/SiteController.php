<?php
class SiteController extends Controller
{
	public $userProfile; // Дополнительная информация о пользователе. Отображается в userBlock.
    public $albumUser = null;

    public $album, $item;

    /**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			'captcha' => array(
				'class' => 'CCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
			'info' => array( // Статичные страницы
				'class' => 'CViewAction',
			),
		);
	}

    /**
     * Фильтр доступа
     * @return array
     */
    public function filters()
    {
        return array(
            'parser +index +album +item', // Дополнительные данные пользователя для отображения в userBlock
            'itemSelect +item', // Входной парсер адресной строки для editPuzzle
        );
    }

    /**
     * Получаем дополнительную информацию о пользователе из связанной таблицы
     * @param $chain
     */
    public function filterParser($chain)
    {
        if (!Yii::app()->user->isGuest) {
            $this->userProfile = Profile::model()->findByPk(Yii::app()->user->id); // Профиль пользователя
        }
        $chain->run();
    }

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
        Yii::app()->clientScript
            ->registerMetaTag( // Прописываем META-тег description
                'Thousands of free jigsaw puzzles that will knock your socks off.'
                . ' Puzzle of the Day, full screen puzzles and a whole lot more.'
            , 'description')
            ->registerMetaTag( // Прописываем META-тег keywords
                'jigsaw,puzzles,puzzle,jigsaws,pictures,play,games,free,online,free jigsaw puzzles,free online'
                . 'jigsaw puzzles,puzzle online,puzzles online,free online jigsaw puzzles full screen,jigsaw puzzle,'
                . 'jigsaw puzzles free,online jigsaw puzzles,jigsaw puzzles online,free jigsaw puzzles to play,jigsaw'
                . 'puzzles free online,jigsaw puzzles,free online jigsaw,puzzle of the day'
            , 'keywords')
            ->registerMetaTag( // Прописываем META-тег Open Graph title
                'Jigsaw puzzles on TheJigsawPuzzles.com', null, null, array('property' => 'og:title'))
            ->registerMetaTag( // Прописываем META-тег Open Graph image
                Yii::app()->getBaseUrl(true).'/images/site_icon.png', null, null, array('property' => 'og:image'))
            ->registerMetaTag( // Прописываем META-тег Open Graph url
                'http://thejigsawpuzzles.com', null, null, array('property' => 'og:url'));
        $this->pageTitle = 'Free Jigsaw Puzzles - Jigsaw Puzzle Games at TheJigsawPuzzles.com - Play Free Online Jigsaw Puzzles';

        $this->render('index', array(
            //'userBlockTop' => $this->renderPartial('userBlockTop', null, true),
        ));
	}

    /**
     * Отображение альбома
     */
    public function actionAlbum()
    {
        if (isset($_GET['userAlbumName'])) {
            //$albumUser = User::model()->find('username=:username', array(':username'=>$_GET['userAlbumName']));
            $albumUser = CAlbumUtils::getUser($_GET['userAlbumName'], true);
            $albumName = $_GET['userAlbumName'];
        } else
            $albumName = Yii::app()->request->getParam('albumName', '');
//die(print_r($albumUser));
        if (isset($_GET['new-puzzles'])) { // Новые пазлы
            $album = array('title'=>'Recent Updates','keywords'=>'recent updates',
                'title'=>'Recent Updates', 'description'=>'Recent Updates', 'cnt'=>50,
                'imgUrl'=>'00/00', 'imgFullName'=>'0000000000', 'componentUrl'=>'new-puzzles');
            $newPuzzles = true;

        } elseif ( Album::model()->exists('componentUrl=:cUrl', array(':cUrl'=>$albumName)) ) {// Проверка сущ-я альбома
            $db = Yii::app()->db;
            if (!empty($albumUser['id']))
                $album = $db
                    ->createCommand('SELECT * FROM album WHERE componentUrl=:cUrl AND owner_id=:ownerID LIMIT 1')
                    ->bindParam(':cUrl', $albumName, PDO::PARAM_STR)
                    ->bindParam(':ownerID', $albumUser['id'], PDO::PARAM_INT)
                    ->queryRow();
            else // Если существует пользователь
                $album = $db
                    ->createCommand('SELECT * FROM album WHERE componentUrl=:cUrl LIMIT 1')
                    ->bindParam(':cUrl', $albumName, PDO::PARAM_STR)
                    ->queryRow();
            list($album['imgFullName'], $album['imgUrl']) = CImageSize::getPath($album['thumbnail_id']); // Для META-тегов
            if (empty($album['componentUrl']))
                $this->redirect(Yii::app()->homeUrl);

        } else { // Редирект на главную если альбом не существует
            $this->redirect(Yii::app()->homeUrl);
        }
        #die(print_r($album));
        if ($album) { // Установка МЕТА-тегов
            $title = htmlspecialchars_decode($album['title'], ENT_QUOTES);
            $this->pageTitle = $title.' puzzles on TheJigsawPuzzles.com';
            Yii::app()->clientScript
                ->registerMetaTag( // Прописываем META-тег description
                    $album['description']
                        . ' free online jigsaw puzzles on TheJigsawPuzzles.com.'
                        . 'Play full screen, enjoy Puzzle of the Day and thousands more.'
                    , 'description')
                ->registerMetaTag( // Прописываем META-тег keywords
                    $album['keywords']
                        . ',jigsaw,puzzle,puzzles,jigsaw puzzle,jigsaw puzzles,free,online,full screen'
                    , 'keywords')
                ->registerMetaTag( // Прописываем META-тег OG title
                    $title.' jigsaw puzzles', null, null, array('property' => 'og:title'))
                ->registerMetaTag( // Прописываем META-тег OG image @todo: Прописать адрес сайта напрямую.
                    Yii::app()->getBaseUrl(true)."/items/thumbnail/{$album['imgUrl']}/{$album['imgFullName']}.jpg",
                    null, null, array('property' => 'og:image'))
                ->registerMetaTag( // Прописываем META-тег OG url @todo: Прописать адрес сайта напрямую.
                    Yii::app()->getBaseUrl(true).'/'.$album['componentUrl'].Yii::app()->params['urlSuffix'],
                    null, null, array('property' => 'og:url'))
            ;
        }

        $this->render('album', array(
            'album' => isset($album['componentUrl']) ? $album : false,
            'albumName' => @$albumName,
            'albumUser' => @$albumUser,
            'newPuzzles' => isset($newPuzzles) ? true : false,
            //'userBlockTop'  => $this->renderPartial('userBlockTop', null, true),
        ));
    }

    /**
     * Отображение пазла
     */
    public function actionItem()
    {
        if ( null!= $this->item ) { // Пазл определен в префильтре

            $album = $this->album; // Объект получен в префильтре
            $item  = $this->item;  // Объект получен в префильтре
            $this->albumUser =     // Флажок показа рекламы на странице
                (!empty($this->album['parent_id']) AND Yii::app()->params['userAlbumID'] == $this->album['parent_id'])
                    ? true
                    : null;

            $this->pageTitle = $item['title']
               . ' jigsaw puzzle in '
               . $album['title']
               . ' puzzles on TheJigsawPuzzles.com';
            Yii::app()->clientScript
                ->registerMetaTag( // Прописываем META-тег description
                    $item['title']
                    . ' puzzle in '
                    . $album['title']
                    . ' jigsaw puzzles on TheJigsawPuzzles.com. '
                    . 'Play full screen, enjoy Puzzle of the Day and thousands more.'
                , 'description')
                ->registerMetaTag( // Прописываем META-тег keywords
                    $item['title'].','
                    . $album['title'].','
                    . @$item['attr']['keywords'].','
                    //. $item['attrKeywords'].','
                    . @$album['keywords'].','
                    . ',jigsaw,puzzle,puzzles,jigsaw puzzle,jigsaw puzzles'
                , 'keywords')
                ->registerMetaTag( // Прописываем META-тег Open Graph title
                    $item['title'].' jigsaw puzzle', null, null, array('property' => 'og:title'))
                ->registerMetaTag( // Прописываем META-тег Open Graph image @todo: Прописать адрес сайта напрямую.
                    Yii::app()->getBaseUrl(true)."/items/thumbnail/{$item['imgUrl']}/{$item['imgFullName']}.jpg",
                    null, null, array('property' => 'og:image'))
                ->registerMetaTag( // Прописываем META-тег Open Graph url @todo: Прописать адрес сайта напрямую.
                    Yii::app()->getBaseUrl(true)."/{$album['componentUrl']}/{$item['componentUrl']}-jigsaw-puzzle",
                    null, null, array('property' => 'og:url'))
                ->registerScript(null, // Прописываем внутренние скрипты
                    "window.puzzleId='{$item['id']}';
                     window.puzzleName='{$item['title']}';
                     window.puzzleThumbId='".Yii::app()->params['pathThumbnail']."/{$item['imgUrl']}/{$item['imgFullName']}.jpg';", 0)
            ;
            // Передаем в flash параметр нарезки пазла
            if (empty($_GET['cutout'])) { // Нарезка из базы или по умолчанию
                //$cutout = ($item['cut']['name'])?$item['cut']['name']:Yii::app()->params['defaultCutout'];
                $cutout = ($item['cut']) ? $item['cut'] : Yii::app()->params['defaultCutout'];
            } else // Нарезка передается в адресной строке
                $cutout = $_GET['cutout']; // (int)

            $suffix = '/'. $item['imgUrl'] .'/'. $item['imgFullName'] .'.jpg'; // Окончание пути к файлу
            $image = Yii::app()->params['pathOriginal'] . $suffix; // Для пользовательских пазлов

            $paramToFlash = // Информация для flash-движка собирания пазла
                'sourceImageURL='.urlencode($image)
                . '&puzzleId='.urlencode($item['id'])
                . '&puzzleName='.urlencode($item['title'])
                . '&puzzleThumbId='.urlencode($item['componentUrl'])
                . '&cutout='.urlencode($cutout);

            // Выставляем флаг (Нужна предварительная анимация)
            if (isset($album->id) AND in_array($album->id, Yii::app()->params['afgEnabledAlbums']) )
                $this->afgEnabled = true;

            $this->render('item', array(
                'item'  => $item,
                'album' => $album,
                'paramToFlash'=> $paramToFlash,
                'pathToImage' => Yii::app()->getBaseUrl(true).$image,
                'cutout' => $cutout,
            ));
        } else {
            // Редирект на страницу альбома если пазла не существует
            $this->redirect(Yii::app()->homeUrl .'/'. $_GET['albumName']);
        }
    }

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error = Yii::app()->errorHandler->error) {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionFeedback()
	{
		$model = new ContactForm;

		if(isset($_POST['ContactForm'])) {
			$model->attributes = $_POST['ContactForm'];//die(print_r($model));
			if($model->validate()) {
				$body   = $this->renderPartial('_contact', array('model'=>$model), true);
                $headers= "From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],'Feedback - TheJigsawPuzzles.com',$body,$headers);
				Yii::app()->user->setFlash(
                    'feedback',
                    '<strong>Thank you - your message has been sent</strong>.<br/><br/>
                    If necessary, we will be in contact as soon as possible.');
				$this->refresh();
			}
        } else { // Данные для подстановки в поля (разные модели)
            if (!Yii::app()->user->isGuest) {
                $user = Yii::app()->db
                    ->createCommand('
                        SELECT u.email, p.fullname
                        FROM user_users u
                        LEFT JOIN user_profiles p ON u.id=p.user_id
                        WHERE u.id = '.Yii::app()->user->id.'
                        LIMIT 1')
                    ->queryRow();
                $model->name  = $user['fullname'];
                $model->email = $user['email'];
            }
        }
		$this->render('feedback',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model = new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax'] === 'login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];#die('pre'.print_r($_POST).'</pre>');
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login()) {
                $this->refresh(true);
                //$this->redirect(Yii::app()->user->returnUrl);
            }
		}
		// display the login form
		$this->render('login',array('model'=>$model));
        //$this->redirect(Yii::app()->user->returnUrl);
	}

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);

        //unset($_COOKIE['TJPCOOKIE']); // Стирание переменной сессии
    }

    /**
     * Получить список возможных действий над пазлом альбома
     * Используется в виджете albumList.
     * @todo Переписать в виде отдельного виджета
     */
    public function getActions($id, $itemName='', $albumName='', $itemID=null)
    {
        if(Yii::app()->user->isGuest) // Список действия для гостя
            return '';
        elseif (Yii::app()->getModule('user')->isAdmin()) { // Список действия для администратора
            if (!empty($itemID)) { // Формирование адреса если передан itemID и нет itemName и albumName
                $item = Item::model()->findByPk($itemID); //print_r($item);
                if (empty($item->componentUrl) AND !empty($item->id)) // Если запись сущ. без cUrl
                    $actionUrl  = '?itemID='.$item->id; //$albumUrl = '&album='.$albumName;
                else $actionUrl  = '?item='.$item->componentUrl;
            } else $actionUrl  = '?item='.$itemName.'&album='.$albumName; // Подходит для большинства случаев
            return $this->renderPartial('getActions', array(
                'actions' => array(
                    'Delete Puzzle' => Yii::app()->getBaseUrl(true).'/service/delete-puzzle'.$actionUrl,
                    'Edit Puzzle' => array(
                        Yii::app()->getBaseUrl(true).'/admin/editpuzzle'.$actionUrl,
                        'false',
                    ),
                    'Make Highlight' => Yii::app()->getBaseUrl(true).'/service/make-highlight'.$actionUrl,
                    'Move Puzzle' => array(
                        Yii::app()->getBaseUrl(true).'/service/move-puzzle'.$actionUrl,
                        //Yii::app()->getBaseUrl(true).'/admin/movepuzzle'.$actionUrl,
                        'false',
                    ),
            )), true);
        } elseif ($id == Yii::app()->user->id) { // Список действия для владельца альбома. Пользовательские альбомы.
            return $this->renderPartial('getActions', array(
                'actions' => array(
                    'Delete Puzzle' => Yii::app()->getBaseUrl(true)
                        . '/service/delete-puzzle?item'.($itemID?'ID='.$itemID:'='.$itemName)
                        . ($albumName ? '&album='.$albumName : ''),
                    'Edit Puzzle' => array(
                        Yii::app()->getBaseUrl(true)
                            . '/service/edit-puzzles?item'.($itemID?'ID='.$itemID:'='.$itemName)
                            . ($albumName ? '&album='.$albumName : ''),
                        'false',
                    ),
                )), true);
        } else // Список действия для зарегистрированного пользователя
            return '';
    }

    /**
     * Сделать свой пазл
     */
    public function actionMakeapuzzle()
    {
        if (Yii::app()->user->isGuest) $this->redirect(Yii::app()->homeUrl, true);

        Yii::app()->clientScript->registerPackage('swfupload'); // Подключение скриптов загрузчика swfupload
        $fullname = Yii::app()->db
            ->createCommand('SELECT fullname FROM user_profiles WHERE user_id='.Yii::app()->user->id)
            ->queryScalar();

        if (isset($_FILES["Filedata"])) // Если переслан файл
            CMakeAPuzzle::fileMove(); // Перемещаем файл

        elseif(!empty($_POST["fileName"])) // Получаем дополнительные поля к файлу
            //die(print_r($_POST["fileName"]));
            CMakeAPuzzle::addFields(); // Обрабатываем дополнительные поля и сохраняем файлы

        else { // Никаких данных не передано. Отображение страницы.
            if (Yii::app()->getModule('user')->isAdmin())
                $cnt = 10000000; // Без ограничений для администратора
            else {
                $cnt = CUserAlbums::getItemsCount(Yii::app()->user->id, true); // Получить кол-во пазлов пользователя
                                                                               // И  обновить cnt в album
                //$cnt = Yii::app()->params['defaultCount'] - Item::model()->countByAttributes(array('owner_id'=>Yii::app()->user->id));
                $cnt = Yii::app()->params['defaultCount'] - $cnt;
                $cnt = (0 < $cnt) ? $cnt : 0;
            }
            $albumComponentUrl = Yii::app()->request->getParam('album', null);
            $this->layout = 'simple';
            $albumName = CUserAlbums::getUserAlbumNameFromUserId(Yii::app()->user->id);
            $this->render('makeapuzzle', array(
                'user' => Yii::app()->user,
                'fullname' => $albumComponentUrl
                    ? $albumComponentUrl
                    : ($fullname ? $fullname : Yii::app()->user->name),
                'albumAddress' => $albumComponentUrl
                    ? '/'. $albumComponentUrl .Yii::app()->params['urlSuffix']
                    : '/User-Albums/'
                        . ($albumName ? $albumName : Yii::app()->user->name)
                        . Yii::app()->params['urlSuffix'],
                'cntPuzzles' => $cnt,
                'albumComponentUrl' => $albumComponentUrl,
            ));
        }
    }

    /**
     * Получение адреса миниатюры.
     * Применяется при сохранении состояния пазла.
     */
    public function actionGetThumbnail()
    {//die('/items/thumbnail/02/02/0000060202.jpg');//die('http://thejigsaw/items/thumbnail/02/02/0000060202.jpg');

        $componentUrl = Yii::app()->request->getParam('g2_itemId', null);
        if ($componentUrl) {

            $item = Item::model()->findByPk($componentUrl);//die(print_r($item));
            if (null == $item)
                $item = Item::model()->findByAttributes(array('componentUrl'=>$componentUrl));

            if (!empty($item->imgUrl)) {
                /*$file = //CHtml::encode(
                    Yii::app()->getBaseUrl(true)
                    . Yii::app()->params['pathThumbnail']
                    . '/'. $item->imgUrl
                    . '/'. $item->imgFullName
                    . '.jpg';//);*/
                $file = Yii::app()->params['pathOS']
                    . Yii::app()->params['pathThumbnail']
                    . '/' . $item->imgUrl
                    . '/' . $item->imgFullName
                    . '.jpg';
                if (true){// && Yii::app()->request->isAjaxRequest) { //true AND
                    if (file_exists($file)) { // Переработана логика
                        $size = filesize($file);
                        $img = file_get_contents($file);
                        header('Content-Type: image/jpeg'); // сформировать заголовок
                        header("Content-Length: " . $size);  // отправляем размер изображения
                        echo $img;
                    }
                } else
                    echo $file; // Путь к файлу

            } //else die('Object not found');
        }
        Yii::app()->end(); // Завершаем работу приложения
    }

    /**
     * Фильтр входных значений для действий:
     *   - item
     */
    public function filterItemSelect($chain)
    {
        $albumName = Yii::app()->request->getParam('albumName', '');
        $itemName  = Yii::app()->request->getParam('itemName', '');
        $userAlbumName = Yii::app()->request->getParam('userAlbumName', '');
        $userItemName = Yii::app()->request->getParam('userItemName', '');
        $itemID = Yii::app()->request->getParam('itemId', '');

        //die("\$albumName=$albumName \$itemName=$itemName \$userAlbumName=$userAlbumName \$userItemName=$userItemName \$itemID=$itemID");
        if (null != $itemID)
            list($this->album, $this->item) = CAlbumUtils::getItemARFromItemID($itemID, true);

        elseif (null != $userAlbumName) // Пользовательский альбом
            list($this->album, $this->item) = CAlbumUtils::getItemAR(
                $userAlbumName,
                (null != $userItemName) ? $userItemName : $itemName,
                true);
        else
            list($this->album, $this->item) = CAlbumUtils::getItemAR($albumName, $itemName, true);

        //die(print_r($this->album));
        //die(print_r($this->item));

        $chain->run();
    }

}