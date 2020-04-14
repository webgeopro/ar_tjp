<?php

/**
 * Администрирование сайта
 * Блок пользователей доступен из модуля "User"
 * Только для администраторов.
 */
class AdminController extends Controller
{
	public $layout = 'admin';

    public $album = null; // Объект альбом
    public $item  = null; // Объект пазл
    public $albumUser = null;

    /**
     * Индексная страница
     */
    public function actionIndex()
	{
		$this->render('index', array(
            'content' => $this->renderPartial('indexPart', null, true)
        ));
	}


    /**
     * Получение страниц. Ajax.
     * Модель инициализируется через фильтр.
     */
    public function actionGetPage()
    {
        $pageLabel = Yii::app()->request->getParam('pageLabel', 'index'); // die($pageLabel);

        if ('edit_album_sort_part' == $pageLabel) // Костыль для сортировки альбомов
            $albums = Album::model()->mainAlbums()->findAll();
        if (null != $this->album)
            $albumUrl = (Yii::app()->params['userAlbumID'] == $this->album['parent_id'])
                ? Yii::app()->params['userAlbumName'] .'/'. $this->album['componentUrl']
                : $this->album['componentUrl'];

        $this->renderPartial($pageLabel, array(  //.'_part'
            'model' => @$this->item,
            'attr'  => @$this->item->attr,
            'album' => @$this->album,
            'albumUrl' => isset($albumUrl) ? $albumUrl : null,
            'albums'=> isset($albums) ? $albums : null,
        ));
    }

    /**
     * Страница редактирования пазла
     */
    public function actionEditPuzzle()
    {
        Yii::app()->clientScript
            ->registerScriptFile('/js/jquery.rotate.js', CClientScript::POS_HEAD)        // Плагин вращения
            ->registerScriptFile('/js/jquery.imgareaselect.js', CClientScript::POS_HEAD) // Плагин обрезки
            ->registerCSSFile('/css/imgareaselect-default.css');          // CSS-стили для плагина обрезки

        if (isset($_POST['Item'])) { // Обработка POST-данных =================================================
            Yii::import('ext.ActiveDateSelect'); // Импортируем расширение для работы с датами (3 select-а)
            $item = Item::model()->findByPk($_POST['Item']['id']);
            if (!empty($_POST['Item']['componentUrl'])
                AND $_POST['Item']['componentUrl'] != $item->componentUrl)
                $item->newComponentUrl = true; // Флаг изменения componentUrl

            $itemAttribute = ItemAttributes::model()->findByPk($_POST['Item']['id']);
            //todo Проверка вставки альбома для user + editorial
            if (!empty($this->album['id']))
                $item->currentAlbumID = $this->album['id'];
            $item->attributes = $_POST['Item'];
            $item->cut = $item->cutout;
            $itemAttribute->attributes = $_POST['ItemAttributes'];

            //$dateCreated = Item::dateFormat('F d Y', $item->dateCreated); // Преобразование из формата (F m d) -> (Y-m-d)
            $item->dateCreated = Item::dateFormat('F d Y', $item->dateCreated); // Преобразование из формата (F m d) -> (Y-m-d)
            //ActiveDateSelect::sanitize($item, 'dateCreated'); // Преобразуем 3 select-а в строку формата YYYY-MM-DD
            ActiveDateSelect::sanitize($itemAttribute, 'dateImageCreated');//die(print_r($itemAttribute));
//die(print_r($item));
            // Валидация и сохранение данных ------------------------------------------------------------------
            if ($item->validate())
                if ($item->save())
                    Yii::app()->user->setFlash('mainSuccess', 'Основные параметры сохранены.');

            if ($itemAttribute->validate())
                if ($itemAttribute->save())
                    Yii::app()->user->setFlash('attrSuccess', 'Дополнительные параметры сохранены.');

            if (isset($_POST['staticBlock'])) { // Обнуляем кеш для статичных блоков --------------------------
                CCacheUtils::revalidateAll();
                $this->widget('potdFeatured', array('admin'=>true)); // Выбираем из БД и генерим featured puzzles в static
                Yii::app()->user->setFlash('blockSuccess', 'Статические блоки обновлены.'); //todo Сделать более расщиренную информацию об ошибках
            }
            if (!empty($_POST['staticNav'])) { // Обнуляем кеш для микронавигации ------------------------------
                $this->widget('albumSiblings', array('admin'=>true)); // Обнуляем кеш микронавигации
                Yii::app()->user->setFlash('blockSuccess', 'Микронавигация обновлена.');
            }
            $this->redirect($this->getReturnUrl($item->componentUrl), true); // Сбрасываем POST + redirect в случае нового componentUrl

        } elseif (isset($_POST['inpRotateAngle']) AND !empty($_POST['itemID'])) { //  =========================
                                                                                // Сохраняем вращение + обрезка
            if ((int)$_POST['inpRotateAngle']) { // Угол ненулевой. Сохраняем --------------------------------
                $item = Item::model()->findByPk($_POST['itemID']); // Выбор + инициализация пазла
                if ($item) { // Если пазл существует
                    $item->resize((int)$_POST['inpRotateAngle']); // Вращаем и сохраняем все изображения кроме source
                    Yii::app()->user->setFlash('updatePuzzle', '1');
                }
            }
            $this->redirect($this->getReturnUrl(), true);
            //$this->refresh(true, $this->getReturnUrl()); // Сбрасываем POST

        } elseif ( ( isset($_POST['x1']) OR isset($_POST['y1']) OR isset($_POST['x2']) OR isset($_POST['y2']))
            AND !empty($_POST['itemID'])) { // Сохраняем Обрезку изображения паззла ===========================
            $item = Item::model()->findByPk($_POST['itemID']);
            if (!empty($this->album['id']))
                $item->currentAlbumID = $this->album['id'];

            if (null != $item) {
                $x1 = Yii::app()->request->getParam('x1', 0);
                $y1 = Yii::app()->request->getParam('y1', 0);
                $x2 = Yii::app()->request->getParam('x2', $item['width']);
                $y2 = Yii::app()->request->getParam('y2', $item['height']);
                $width  = abs($x2 - $x1);
                $height = abs($y2 - $y1);
                //$ratio = Yii::app()->request->getParam('ratio', 0);
                $item->crop($width, $height, $y1, $x1); // Обрезка пазла

                $this->redirect($this->getReturnUrl($item->componentUrl), true);
                //$this->refresh(true, $this->getReturnUrl()); // Сбрасываем POST
            }

        } elseif (Yii::app()->request->isAjaxRequest) { // Ajax-запрос контента страниц =======================
            //die(print_r($_POST));
            $this->renderPartial(isset($_POST['pageLabel']) ? $_POST['pageLabel'] : 'update_puzzle_part',
                array('albumName' => ($this->album['id'])?'/'.$this->album['componentUrl'].'/':'',
                    'model' => $this->item,
                    'attr'  => $this->item->attr,
                    'album' => @$this->album,
                ), false);

        } else { // Просто отображение (new / edit) ===========================================================
            Yii::app()->clientScript->registerScriptFile('/js/getTemplate.js', CClientScript::POS_HEAD); // Функция очистки title
            $cutout = Cutout::model()->findAll();
            $listCutout = CHtml::listData($cutout, 'id', 'name');
            if ($this->item['id']) { // Редактирование пазла --------------------------------------------------
                $albumUrl = (Yii::app()->params['userAlbumID'] == $this->album['parent_id'])
                    ? '/' . Yii::app()->params['userAlbumName'] .'/'. $this->album['componentUrl']
                    : '/' . $this->album['componentUrl'];
                $this->breadcrumbs = array(
                    $this->album['title'] => $albumUrl . Yii::app()->params['urlSuffix'],
                    $this->item['title']  => $albumUrl .'/'. $this->item['componentUrl'] . Yii::app()->params['urlSuffix']
                );
                $this->render('edit_puzzle', array(
                    'content' => $this->renderPartial(isset($_POST['pageLabel'])?$_POST['pageLabel']:'update_puzzle_part',
                        array(
                            //'albumName' => ($this->album['id'])?'/'.$this->album['componentUrl'].'/':'',
                            'albumName' => $albumUrl,
                            'model' => $this->item,
                            'attr' => $this->item->attr,
                            'listCutout' => $listCutout,
                    ), true),
                ));
            } else { // Новый пазл ----------------------------------------------------------------------------
                $this->render('edit_puzzle', array(
                    'content' => $this->renderPartial(isset($_POST['pageLabel'])?$_POST['pageLabel']:'create_puzzle_part',
                        array(
                            'albumName' => '',
                            'model' => new Item,
                            'attr'  => new ItemAttributes,
                            'listCutout' => $listCutout,
                    ), true),
                ));
            }
        }
    }

    /**
     * Формирование GET-параметров (componetUrl альбома + componetUrl пазла) адресной строки
     *
     * @param null $itemComponentUrl
     * @param bool $usePathInfo
     * @return string
     */
    private function getReturnUrl($itemComponentUrl=null, $usePathInfo=true)
    {
        $albumUrl = '?album=';
        $itemUrl = '&item=';

        $albumUrl .= (Yii::app()->params['userAlbumID'] == $this->album['parent_id'])
            ? Yii::app()->params['userAlbumName'] . '/' . $this->album['componentUrl']
            : $this->album['componentUrl'];
        $itemUrl .= $itemComponentUrl ? $itemComponentUrl : $this->item['componentUrl'];

        return Yii::app()->getBaseUrl(true) // http://имя_сайта
            . '/'
            . ($usePathInfo ? Yii::app()->request->pathInfo : '') // Добавляем controller + action
            . '/'
            . $albumUrl . $itemUrl; // album componentUrl + item componentUrl
    }

    /**
     * Добавление пазла.
     * Загрузка с web, парсинг url. Загрузка с диска.
     */
    public function actionAddPuzzles()
    {
        $matches = array(); $images = array(); // Массивы совпадений и обработанных данных

        if (isset($_POST['inpUrl'])) { // Обработка URL. Web-парсинг ============================================
            $opts = array(
                'http'=>array(
                    'method'=>"GET",
                    'header'=>"Accept-language: en\r\n" . "Cookie: foo=bar\r\n"
            ));
            $context = stream_context_create($opts); // Используем потоковый контекст
            $html = @file_get_contents($_POST['inpUrl'], false, $context) or die('Connection lost, refresh the page');
            //if (stripos($html, '<img') !== false) // Если есть тег с изображением
            preg_match_all('#<\s*img [^\>]*src\s*=\s*(["\'])(.*?)\1#im', $html, $matches, PREG_SET_ORDER); // Поиск всех изображений

            if (is_array($matches) && !empty($matches)) { // Если что-то найдено
                foreach ($matches as $img) {
                    $tmp['source'] = $img[0]; // Копируем всю исходную информацию
                    $url = preg_grep('#\s*src\s*=\s*(["\'])(.*?)\s*#im', $img, PREG_GREP_INVERT); // Выбираем только адрес http://...
                    $tmp['url'] = $url[2]; // Выбираем только адрес http://...
                    $images[] = $tmp;
                }
            } else { // Возможно передано изображение
                $img = imagecreatefromstring($html);
                if (null != $img) { // Передано изображение, сохраняем
                    $item = new Item(); // Создаем объект Пазл
                    $item->owner_id = Yii::app()->user->id; // Владелец пазла
                    $item->title = substr(strrchr($_POST['inpUrl'], '/'), 1); // Оставляем только имя файла
                    $item->inSearch = 2;
                    if (!empty($this->album['id']))                 // Если передан альбом,
                        $item->currentAlbumID = $this->album['id']; // Сохраняем в этот альбом

                    if (null == $item->title){
                        $item->title = md5('H:i:s');
                        $file = Yii::app()->params['pathImageUpload'].md5($item->title).'.jpg'; // Имя временного файла
                    } else
                        $file = Yii::app()->params['pathImageUpload'].md5($item->title).'.jpg'; // Имя временного файла
                    if (file_put_contents($file, $html)) { // Сохраняем в файл
                        $item->dateImageCreated = CMakeAPuzzle::getExifDate($file);
                        if ($item->validate()) // При валидации подставляется componentUrl
                            if ($item->save()) { // Успешное сохранение Пазла. В модели подставляется dateCreated
                                $item->fileCreate($file); // Ресайз и раскидывание по директориям
                                $saveResult[] = array('success', $_POST['inpUrl']);
                            } else // Ошибка при сохранении
                                $saveResult[] = array('error', $_POST['inpUrl']);
                        Yii::app()->user->setFlash('result', $saveResult); // Устанавливаем сообщение о результатах
                    }
                }
            }
            unset($html); // Удаляем контент с изображениями

        } elseif(isset($_POST['files'])) { // Обработка загруженных файлов ======================================
            die('Обработка ITEMS');// Переложено на site/makeapuzzle

        } elseif(isset($_POST['chUrls'])) { // Обработка распарсенных адресов. Создание и сохранение пазлов. ====
            if (is_array($_POST['chUrls']) AND count($_POST['chUrls'])) { // Не пустой массив
                set_time_limit(150); // Увеличиваем время работы скрипта до 2.5 минут
                $saveResult = array(); // Массив результатов обработки файлов для последующего отображения в view
                foreach ($_POST['chUrls'] as $url=>$value) { // Проходим по всем ссылка, с отмеченными чекбоксами
                    $item = new Item(); // Создаем объект Пазл
                    $item->owner_id = Yii::app()->user->id; // Владелец пазла
                    $item->title = substr(strrchr($url, '/'), 1); // Оставляем только имя файла
                    if (!empty($this->album['id']))                 // Если передан альбом,
                        $item->currentAlbumID = $this->album['id']; // Сохраняем в этот альбом
                    $file = Yii::app()->params['pathImageUpload'].md5($item->title).'.jpg';

                    if (@copy($url, $file)) { // Файл скопирован
                        $item->dateImageCreated = CMakeAPuzzle::getExifDate($file);
                        if ($item->validate()) // При валидации подставляется componentUrl
                            if ($item->save()) { // Успешное сохранение Пазла. В модели подставляется dateCreated
                                $item->fileCreate($file); // Ресайз и раскидывание по директориям
                                $saveResult[] = array('success', $url);
                            } else { // Ошибка при сохранении
                                $saveResult[] = array('error', $url);
                            }
                    } else // Ошибка при копировании файла (нет исходника)
                        $saveResult[] = array('copy error', $url);
                }
                Yii::app()->user->setFlash('result', $saveResult); // Устанавливаем сообщение о результатах
            }
        }
        // Отображение формы. Общая + для веб-парсинга
        $this->render('add_item', array(
            'content' => $this->renderPartial(
                'add_puzzle_web_part', array(
                    'images' => $images,
                    'album'  => @$this->album,
                ), true),
            //'breadcrumbs' => CBreadcrumbs::getNode($this->album),
        ));
    }

    /**
     * Сохранение и редактирование пересланного файла
     */
    public function actionMakeapuzzle()
    {//die(' ::actionMakeapuzzle:: '.print_r($this->album));
        if (isset($_FILES["Filedata"])) { // Если переслан файл
            #1. Новая запись
            $item = new Item; // Создаем объект Пазл
            $attr = new ItemAttributes; // Создаем объект Пазл
            $item->owner_id = Yii::app()->user->id; // Владелец пазла
            $item->title = $_FILES["Filedata"]["name"]; // Оставляем только имя файла
            $item->inSearch = 2;
            if (!empty($this->album['id']))                 // Если передан альбом,
                $item->currentAlbumID = $this->album['id']; // Сохраняем в этот альбом
            $file = Yii::app()->params['pathImageUpload'].$item->title; // Путь к загружаемому файлу
            $url = $_FILES["Filedata"]["tmp_name"];
            if (@copy($url, $file)) { // Файл скопирован
                $item->dateImageCreated = CMakeAPuzzle::getExifDate($file);
                if ($item->validate()) // При валидации подставляется componentUrl
                    if ($item->save()) { // Успешное сохранение Пазла. В модели подставляется dateCreated
                        $item->fileCreate($file); // Ресайз и раскидывание по директориям
                        //die('ITEM SAVE'.print_r($item));
                        $saveResult[] = array('success', $url);
                    } else { // Ошибка при сохранении
                        $saveResult[] = array('error', $url);
                        die('ITEM SAVE ERROR'.print_r($item));
                    }
                else die('NO VALIDATE'.print_r($item->getErrors()));
            } else // Ошибка при копировании файла (нет исходника)
                $saveResult[] = array('copy error', $url);
            $url = '/admin/editpuzzle'
                . (!empty($this->album)
                    ? '?album='
                        . (Yii::app()->params['userAlbumID'] == $this->album['parent_id']
                            ? Yii::app()->params['userAlbumName'] .'/'. $this->album['componentUrl']
                            : $this->album['componentUrl']
                        ) .'&'
                    : '?'
                  )
                .'item=' .$item['componentUrl'];
            $this->redirect($url);
            die('after redirect');
            $this->render('makeapuzzle', array('saveResult' => $saveResult, 'model' => $item, 'attr' => $attr,));

        } elseif (isset($_POST['Item'])) { // Сохранение добавочных/доп. полей
            // Перекинуто в /admin/editpuzzle
            die(print_r($_POST));
            #4. Сохраняем расширенные значения
            CMakeAPuzzle::addFieldsByAdmin($this->album); // Обрабатываем дополнительные поля и сохраняем файлы
        }
        die('After actions from makeapuzzle');
    }

    /**
     * Переместить пазл
     * НЕ ИСПОЛЬЗУЕТСЯ.
     */
    public function actionMovePuzzle()
    {
        Yii::app()->clientScript // Подключаем необходимые ccs-, js-файлы
            ->registerScriptFile('/js/jquery.js', CClientScript::POS_HEAD)
            ->registerScriptFile('/js/common.js', CClientScript::POS_HEAD) // Общий файл сценариев
        ;
        if (isset($_POST['serviceToken']) AND !empty($this->item['id'])) { // Получена форма и выбран пазл

            if (isset($_POST['albumName'])) { // Обработка добавления в альбом
                // Добавление пазла в новый альбом
                if (!AlbumItem::model()->exists(
                        'item_id=:itemID AND album_id=:albumID',
                        array(':itemID'=>$this->item['id'], ':albumID'=>$_POST['albumName'])))
                {
                    $albumItem = new AlbumItem; // Записи нет. Добавляем новую.
                    $albumItem->item_id  = $this->item['id']; // Передается через input-hidden
                    $albumItem->album_id = $_POST['albumName']; // Id из select-а формы
                    if ($albumItem->validate()) { // Проверка корректности присваиваемых величин
                        //$albumItem->save(); // Сохранение
                    }
                    // Обновляем количество пазлов в альбоме с album_id = $_POST['albumName']
                    $item = new Item;
                    @$item->setCount($_POST['albumName']);
                }
                // Удаление пазла из пред. альбома

            }
        }

        $albums = Album::model()->mainAlbums($this->item['album'])->findAll();
        $albumList = CHtml::listData($albums, 'id', 'title');

        $this->render('movePuzzle', array(
            'albumList' => $albumList, // array для dropDownList
            'item'  => $this->item,
            'album' => empty($this->album['id']) ? null : $this->album,
        ));
    }

    /**
     * Страница редактирования альбома
     * Редактирование title, Порядок сортировки
     * Доступ на основе accessControl только для администраторов
     */
    public function actionEditAlbum()
    {
        if (isset($_POST['Album'])) { //$albumID = Yii::app()->request->getParam('album["id"]', null);
            $albumID = isset($_POST['Album']['id']) ? $_POST['Album']['id'] : null; // Получаем Id из формы
            $album = Album::model()->findByPk($albumID); // Выбираем альбом по полученному Id
            if (null !== $album) { // Альбом существует
                $album->attributes = $_POST['Album']; // И для title и для sort
                if ($album->validate()) // Данные корректны
                    if ($album->save()) {
                        Yii::app()->user->setFlash('editTitle', 'success'); // Ok
                        $this->redirect('/admin/editalbum?album='.$album->componentUrl, true); // Обновление стр. с новым componentUrl
                    }
                    else Yii::app()->user->setFlash('editTitle', 'error'); // Ошибки при сохранении
                #else die(print_r($album->errors));
            }
            $content = $this->renderPartial('edit_album_title_part', null, true);

        } elseif (isset($_POST['albums'])) { // Отображаем стр. редактирования порядка сортировки альбомов
            foreach($_POST['albums'] as $id=>$sort) {
                Album::model()->updateByPk($id, array('sort'=>$sort));
            }
            $albums = Album::model()->mainAlbums()->findAll();
            Yii::app()->user->setFlash('sortOrder', 'Порядок альбомов изменен.');
            $content = $this->renderPartial('edit_album_sort_part', array('albums'=>$albums), true);

        } else { // По умолчанию отображаем страницу редактирования названия альбома
            if (null == $this->album) // список альбомов
                $content = $this->renderPartial('edit_album_list_part', array(
                    'dataProvider' => $this->getMainAlbums(),
                ), true);
            else
                $content = $this->renderPartial('edit_album_title_part', null, true);
        }

        $this->render('edit_album', array('content' => $content,)); // Отображаем общую форму с нужным контентом
    }

    /**
     * Добавить альбом
     */
    public function actionAddAlbum()
    {
        $album = new Album;
        $album->parent_id = 0; // Устанавливается в модели
        $this->performAjaxValidation($album);
        Yii::app()->clientScript->registerCSSFile('/css/form.css', CClientScript::POS_HEAD);

        if (!empty($_POST['Album'])) {
            $album->attributes = $_POST['Album'];
            $album->owner_id  = Yii::app()->user->id;

            if ($album->validate()) {
                $album->save();
                Yii::app()->user->setFlash('result', 'Album was added');
            } else {
                Yii::app()->user->setFlash('result', 'Error');
            }
        }
        $this->render('add_album', array( // Генерация формы добавления альбома
            'model' => $album,
        ));
    }

    /**
     * Удаление альбома
     */
    public function actionDeleteAlbum()
    {//die(print_r($_GET).'AFTER GET');
        if (!empty($_GET['id'])) {
            $album = Album::model()->findByPk($_GET['id']);
            //if (!empty($_GET['userAlbum'])) { // Пользовательский альбом
            if (null == $album) { // Пользовательский альбом
                $album = Album::model()->findByAttributes(array('owner_id'=>$_GET['id']));
                if (null != $album)
                    $album->delete();
                else {
                    $items = Item::model()->findAllByAttributes(array('owner_id'=>$_GET['id']));
                    if (null != $items)
                        foreach ($items as $item)
                            $item->delete();
                }
            } else
                //$album->delete();
                $album->deleteItemsFromAlbum(); // Удаляем все пазлы, входящие в альбом (пустой альбом остается)
//die('STOP');
            if (empty($_GET['return']))
                Yii::app()->end();
            Yii::app()->user->setFlash('result', 'Album was deleted');
        }

        $this->render('delete_album', array(
            'dataProvider' => $this->getMainAlbums(),
        ));

    }

    /**
     * Получить основные альбомы для отображения
     * @return CActiveDataProvider
     */
    protected function getMainAlbums()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'parent_id = 0 AND id <> '.Yii::app()->params['userAlbumID'];
        $criteria->order = 'sort';

        $dataProvider = new CActiveDataProvider('Album', array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>20,
            ),
        ));

        return $dataProvider;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='album-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }


    /**
     * Фильтр доступа
     * @return array
     */
    public function filters()
	{
        return array(
            'accessControl',
            'parser +editAlbum +makeapuzzle', // Входной парсер адресной строки для editPuzzle
            'itemSelect +addPuzzles +editPuzzle +getPage', // Входной парсер адресной строки для editPuzzle
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
            array('allow', // Разрешаем только для администраторов
                'users' => Yii::app()->getModule('user')->getAdmins(), //array('vah'),//
            ),
            array('deny', // Запрещаем доступ для гостей
                'users'=>array('?', '*'), //
            ),
        );
    }

    /**
     * Фильтр входных значений для действий:
     */
    public function filterParser($chain)
    {
        $albumName = Yii::app()->request->getParam('album', '');
        $itemName  = Yii::app()->request->getParam('item', '');

        if ($itemName) // Если передано имя пазла - ищем в базе
            $this->item = Item::model()->with('attr', 'album')->findByAttributes(array('componentUrl'=>$itemName));
        elseif ( !empty($_POST['itemID']) OR !empty($_GET['itemID']) ) {
            $itemID = Yii::app()->request->getParam('itemID', '');
            if (Item::model()->exists('id=:itemID', array(':itemID'=>$itemID)))
                $this->item = Item::model()->with('attr', 'cut', 'album')->findByPk($itemID);
        } else
            $this->item = new Item;

        if ($albumName) {
            $albumName = explode('/', $albumName); // User-Albums/Money
            if (!empty($albumName[1]) AND 'user-albums' == strtolower($albumName[0])) // Пользовательский альбом
                $this->album = Album::model()->findByAttributes(array(
                    'componentUrl' => $albumName[1],
                    'parent_id'    => Yii::app()->params['userAlbumID'],
                ));
            else
                $this->album = Album::model()->findByAttributes(array('componentUrl'=>$albumName[0]));
//die(print_r($this->album).print_r($albumName));
        } elseif($this->item) { // Если альбом не существует, но есть имя пазла - обратная выборка по пазлу
            $album = $this->item->album();
            $this->album = @$album[0];
        } else { // Если ничего не передано
            $this->album = new Album;
        }

        $chain->run();
    }

    /**
     * Фильтр входных значений для действий:
     *   - EditPuzzle
     */
    public function filterItemSelect($chain)
    {
        $albumName = Yii::app()->request->getParam('album', '');
        $itemName  = Yii::app()->request->getParam('item', '');
        //die("\$albumName=$albumName \$itemName=$itemName");
        list($this->album, $this->item) = CAlbumUtils::getItemAR($albumName, $itemName, true);

        /*if ($album = Album::model()->findByAttributes(array('componentUrl'=>$albumName)) ) {
            //$item =
        } else { // Выбор пазла из пользовательского альбома

        }*/


        $chain->run();
    }
}