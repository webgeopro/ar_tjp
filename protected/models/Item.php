<?php

/**
 * This is the model class for table "item".
 *
 * The followings are the available columns in table 'item':
 * @property string $id
 * @property string $owner_id
 * @property string $title
 * @property integer $cutout
 * @property string $datePublished
 * @property string $dateCreated
 * @property string $componentUrl
 * @property string $countView
 */
class Item extends CActiveRecord
{
	public $imgFullName; // Полное имя, ID дополненное нулями слева до 10 цифр
    public $imgUrl;      // Директория, полученная из ID (к примеру: 34/12)
    public $arKeywords = array(); // Массив для работы с ключевыми словами
    public $itemCnt;     // Для работы с COUNT

    public $albumID    = null;     // ID альбома, применяется при удалении (проверка thumbnail_id)
    public $description = '';      // Описание. Хранится в связанной таблице
    //public $keywords = '';         // Ключевые слова. Хранятся в связанной таблице
    public $dateImageCreated = ''; // Дата съемки. Хранится в связанной таблице
    public $author = '';           // Автор фото. Хранится в связанной таблице

    public $currentAlbumID; // Текущий альбом (куда вставляется вновь созданный пазл). Используется в валидаторе.
    public $newComponentUrl = false; // Если изменяется componentUrl администратором
    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Item the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('cutout', 'numerical', 'integerOnly'=>true),
			array('id', 'numerical', 'integerOnly'=>true),
			array('id', 'unique'),
			array('owner_id', 'length', 'max'=>10),
			array('title, componentUrl', 'length', 'max'=>128),
			array('countView', 'length', 'max'=>20),
			array('dateCreated, dateImageCreated, inSearch', 'safe'),
            array('componentUrl', 'uniqueComponentUrl'), // (альбом, url) | (user, url) - unique
            // Поля связанной таблицы
            array('description', 'length', 'max'=>255),
            //array('keywords', 'length', 'max'=>255),
            // The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, owner_id, title, cutout, dateCreated, componentUrl, countView', 'safe', 'on'=>'search'),
		);
	}

    /**
     * Валидатор уникальности componentUrl (альбом+пазл или userID+пазл)
     * @param $attribute
     * @param array $params
     */
    public function uniqueComponentUrl($attribute, $params=array())
    {
        if(!$this->hasErrors()) {
            if (!Yii::app()->getModule('user')->isAdmin() && Yii::app()->user->id) {//die('1'); // User-Albums
                // Правило уникальности адреса пазла для польз. альбомов (НЕ администратора)
                $params['criteria'] = array(
                    'condition'=>'owner_id=:ownerID AND componentUrl=:componentUrl', // userID + пазл
                    //'params'=>array(':ownerID'=>$this->owner_id, ':componentUrl'=>$this->componentUrl),
                    'params'=>array(':ownerID'=>Yii::app()->user->id, ':componentUrl'=>$this->componentUrl),
                );
                if (!empty($this->id)) { // У пазла уже есть запись в базе
                    $params['criteria']['condition'] .= ' AND id<>:itemID';
                    $params['criteria']['params'][':itemID'] = $this->id;
                }

            } elseif (Yii::app()->getModule('user')->isAdmin() && $this->currentAlbumID) {//die('2'); // (int)
                // Правило уникальности адреса пазла для польз. альбомов при добавлении пазла администратором
                $params['criteria'] = array(
                    'join' => 'LEFT JOIN album_item ai ON ai.item_id = t.id ',
                    'condition' => 'ai.album_id=:albumID AND t.componentUrl=:componentUrl', // AND i.id<>:itemID
                    'params' => array(
                        ':componentUrl' => $this->componentUrl,
                        ':albumID'      => $this->currentAlbumID),
                        //':itemID'       => $this->id
                );
                if (!empty($this->id)) {// У пазла уже есть запись в базе
                    $params['criteria']['condition'] .= ' AND t.id<>:itemID';
                    $params['criteria']['params'][':itemID'] = $this->id;
                }
                /*$userID = CUserAlbums::getUserIDFromAlbumID($this->currentAlbumID);
                $params['criteria']=array(
                    'condition' => 'owner_id=:userID',
                    'params'=>array(':userID'=>$userID),
                );*/

            } elseif (!empty($this->currentAlbumID)) {//die('3'); // Main-Albums // альбом + пазл
                $items = Yii::app()->db
                    ->createCommand('SELECT item_id FROM album_item WHERE album_id=:albumID')
                    ->bindParam(':albumID', $this->currentAlbumID, PDO::PARAM_INT)
                    ->queryColumn();
                if (count($items))
                    $params['criteria']=array(
                        'condition' => '( id IN (' .implode(',', $items). ')  AND componentUrl=:componentUrl)',
                        'params'=>array(':componentUrl'=>$this->componentUrl),
                    );
            }
//die(print_r($params));
//die('End of Validator. Yii::app()->getModule(\'user\')->isAdmin()='.Yii::app()->getModule('user')->isAdmin().' :: $this->currentAlbumID='.$this->currentAlbumID);
            $validator = CValidator::createValidator('unique', $this, $attribute, $params);
            $validator->validate($this, array($attribute));
        }
    }

    /**
	 * @return array relational rules.
	 */
    public function relations()
    {
        return array(
            'album' => array(self::MANY_MANY, 'Album', 'album_item(item_id, album_id)'), // Выборка альбома (работает с основными только)
            'attr'  => array(self::HAS_ONE, 'ItemAttributes', 'id'), // Выборка дополнительных атрибутов пазла
            'cut'   => array(self::BELONGS_TO, 'Cutout', 'id'),      // Выборка вариантов нарезки
            'cutName' => array(self::BELONGS_TO, 'Cutout', 'cutout'),// Выборка вариантов нарезки
            'user'  => array(self::BELONGS_TO, 'User', 'owner_id'),  // Выборка информации о владельце
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'        => 'ID',
			'owner_id'  => 'Owner',
            'title'     => 'Title',
			'cutout'    => 'Cutout',
			'dateCreated'  => 'Date Created',
			'componentUrl' => 'Component Url',
			'countView' => 'View Count',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('owner_id',$this->owner_id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('cutout',$this->cutout);
		$criteria->compare('dateCreated',$this->dateCreated,true);
		$criteria->compare('componentUrl',$this->componentUrl,true);
		$criteria->compare('countView',$this->countView,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Формируем полное имя изображения и путь к нему
     */
    public function afterFind()
    {
        $this->imgFullName = str_pad($this->id, 10, '0', STR_PAD_LEFT);
        $this->imgUrl = substr($this->imgFullName,-2, 2).'/'.substr($this->imgFullName,-4, 2);
        //$this->componentUrl = urldecode($this->componentUrl); // Из-за старых записей gallery2
    }

    /**
     * Именованные наборы.
     */
    public function scopes()
    {
        return array(
            /*'cut' => array(
                'condition'=>'status=1',
            ),*/
        );
    }
    /*public function cutEnum()
    {
        $this->getDbCriteria()->mergeWith(array(
            'select'=>'cut+0 as cut_enum',
        ));
        return $this;
    }
    public function defaultScope()
    {
        return array(
            'select'=>'t.cut',
        );
    }*/

    /**
     * Проходим по всем миниатюрам альбомов куда входит данный пазл
     * Если да - ищем и сохраняем последниый пазл связанный с этим альбомом как миниатюру
     * @return bool|void
     */
    public function beforeDelete()
    {
        $itemId  = $this->id;
        $conn = Yii::app()->db; // Подключаемся к БД, используя DAO
        // Выбираем все альбомы где удаляемый пазл является миниатюрой
        // DISTINCT
        $sql = '
            SELECT album.id
            FROM album_item
            INNER JOIN album
                ON album_item.album_id = album.id AND album.thumbnail_id = :itemID
            WHERE album_item.item_id = :itemID
        ';
        $command = $conn->createCommand($sql);  // Подготовка sql
        $command->bindParam(':itemID', $itemId, PDO::PARAM_INT); // Подстановка значений
        $albums = $command->queryAll();

        if (empty($albums)) return true; // Если пазл не является миниатюрой, сразу удаляем

        // Выбираем у всех найденных альбомов новые миниатюры (последние к ним добавленные)
        // DISTINCT
        $sql = '
            SELECT item.id
            FROM album_item
            INNER JOIN item
              ON album_item.item_id = item.id AND item.id <> :itemID
            WHERE album_item.album_id = :albumID
            ORDER BY item.id DESC
            LIMIT 1
        ';
        $command = $conn->createCommand($sql);  // Подготовка sql
        $command->bindParam(':itemID', $itemId, PDO::PARAM_INT); // Подстановка значений
        // В цикле обновляем у выбранных альбомов миниатюры
        foreach($albums as $alb) { // todo updateByPk($command->queryAll)
            $command->bindParam(':albumID', $alb['id'], PDO::PARAM_INT); // Подстановка значений
            $thumbnailId = $command->queryScalar(); // Получаем значение ID новой миниатюры
            Album::model()->updateByPk($alb['id'], array('thumbnail_id'=>$thumbnailId)); // Обновляем
            //$this->setCount($alb['id'], false); // Обновляем количество пазлов в альбоме
        }

        return true; // Удаляем пазл
    }

    /**
     * Удаление аттрибутивной информации из связанных таблиц
     */
    public function afterDelete()
    {
        AlbumItem::model()->deleteAllByAttributes(array('item_id'=>$this->id)); // Удаляем принадлежность альбому
        ItemAttributes::model()->deleteByPk($this->id); // Удаляем атрибуты
        $this->setCount(null, false); // Обновляем количество пазлов в альбоме

        return true;
    }

    /**
     * Установить новое количество пазлов в альбоме
     *
     * @param $albumID [ ID альбома]
     * @param bool $plus [Флаг удаления / добавления]
     * @return bool
     */
    public function setCount($albumID, $plus=true)
    {
        if (empty($albumID)){
            $album = Album::model()->findByAttributes(array('owner_id'=>$this->owner_id));
            if (null === $album) return false;
        } else {
            $album = Album::model()->findByPk($albumID);
        }

        if ($plus)
            $album->saveCounters(array('cnt'=>1));
        else
            $album->saveCounters(array('cnt'=>-1));
    }

    /**
     * Действия перед валидацией данных
     * @return bool|void
     */
    public function beforeValidate()
    {
        if ($this->newComponentUrl) // Передан новый componentUrl (не формируем из title)
            return $this->uniqueUrl($this->componentUrl);
        /* Очистить значения для title/componentUrl */
        return $this->clearTitle();
    }

    /**
     * Обновление при сохранении связанных атрибутов
     * @return bool|void
     */
    public function beforeSave()
    {
        if ($this->isNewRecord ) { // Вставка новой записи
            if(!$this->cutout) $this->cutout = 3; // 100 piece Classic
            if(!$this->cut) $this->cut = 3; // 100 piece Classic
            if(!$this->dateCreated) { // Дата создания
                if (Yii::app()->getModule('user')->isAdmin()) {
                    //$date = new DateTime(''); // Текущее время
                    //$date->modify('+1 day'); // Смещаем на один день позже текущего для администратора
                    //$this->dateCreated = $date->format('Y-m-d H:i:s'); // Инициирум свойство dateCreated
                    $this->dateCreated = date('Y-m-d H:i:s');
                } else
                    $this->dateCreated = date('Y-m-d H:i:s'); // Для пользователя устанавливаем текущий день
            }
            $this->imgFullName = str_pad($this->id, 10, '0', STR_PAD_LEFT); // Алгоритм сохранения изображения
            $this->imgUrl = substr($this->imgFullName,-2, 2).'/'.substr($this->imgFullName,-4, 2);// Алгоритм сохр. из-я
            if(!$this->inSearch) // Для web-парсинга прописываем inSearch=2 (нет лишнего промежуточного сохранения)
                $this->inSearch = 3; //

        } else { // Редактирование существующей записи
            $this->inSearch = (1 < $this->inSearch)// Только что добавленный файл (при добавлении происходит одно лишнее сохранение)
                ? --$this->inSearch
                : 1; // Запись уже была отредактирована ранее. Учитывается на странице админского редактированя пазла.

            if (Yii::app()->getModule('user')->isAdmin()) {
                // Для редактирования пазла: если дата(контрола)==дата(текущий) - время(пазла)=время(текущий), иначе
                //   если дата(контрола)>дата(текущий) - время(пазла)=00:00:00, иначе
                //   время(пазла)=время(пазла) (не менять)
                switch (CDateTimeUtils::diffEqual($this->dateCreated)) {
                    case '0':  // дата(контрола)==дата(текущий) -> время(пазла):=время(текущий)
                        $this->dateCreated = date('Y-m-d H:i:s');
                        break;
                    case '1':  // дата(контрола)>дата(текущий)  -> время(пазла):=00:00:00
                        $date = new DateTime($this->dateCreated);
                        $date->setTime(0, 0, 0);
                        $this->dateCreated = $date->format('Y-m-d H:i:s');
                        break;
                    //default:   // ветвь иначе
                }
            }
        }

        return true; // Обновляем
    }

    public function afterSave()
    {
        $this->attrSave(); // Сохранение аттрибутов в связанной таблице
        #if (Yii::app()->getModule('user')->isAdmin())
            $currentAlbumID = $this->currentAlbumID;
        #else
        #    $currentAlbumID = null;

        $this->makeAlbumAndThumb($currentAlbumID); // Создаем альбом и устанавливаем по необходимости миниатюру альбома + cnt

        return true;
    }

    /**
     * Сохранение аттрибутов
     * todo Проверить дублирует ли этот скрипт модель ItemAttributes
     */
    public function attrSave()
    {
        if (ItemAttributes::model()->exists('id=:itemID', array(':itemID'=>$this->id))) {//die('Exists');
            $params = array( // Сохраняем связанные поля
                'description'  => $this->description,
                //'keywords'     => $this->keywords,
                'dateModified' => date('Y-m-d'),
                'dateImageCreated' => $this->dateImageCreated
                        ? is_array($this->dateImageCreated)
                            ? $this->dateImageCreated['Year']
                            .'-'. $this->dateImageCreated['Month']
                            .'-'. $this->dateImageCreated['Day']
                            : $this->dateImageCreated
                        : date('Y-m-d'),
            );
            if (!empty($this->currentAlbumID))
                $params['album_id'] = $this->currentAlbumID; // Новый альбом (перенос или добавление пазла)

            ItemAttributes::model()->updateByPk($this->id, $params); // Обновляем таблицу атрибутов
        } else { // Создаем новую запись
            $attr = new ItemAttributes;
            $attr->id = $this->id;
            //$attr->keywords = $this->keywords;
            $attr->description  = $this->description;
            $attr->dateModified = date('Y-m-d');
            //todo CMakeAPuzzle получение даты фото
            $attr->dateImageCreated = ($this->dateImageCreated) ? $this->dateImageCreated : date('Y-m-d');
            $attr->author = ($this->author) ? $this->author : '';
            if ($attr->validate()) $attr->save();
        }
    }

    /**
     * Создаем альбом если не существует
     * Записываем в талицу album_items новую связь альбома с пазлом
     * Если это первый пазл в альбоме - устанвливаем его миниатюрой
     * Устанавливаем новое количество пазлов
     */
    public function makeAlbumAndThumb($albumID=null)
    {//die(' ::makeAlbumAndThumb:: '.$albumID);
        if (null == $albumID)  // Выбираем пользовательский альбом
            // Проверка авторизован ли пользователь происходит в контроллере
            $album = Album::model()->findByAttributes(array('owner_id'=>Yii::app()->user->id));
            //$album = Album::model()->findByAttributes(array('owner_id'=>$this->owner_id));
        else {
            $album = Album::model()->findByPk($albumID);

            // Если один из основных альбомов (не пользовательский)
            /*if (null != $album AND Yii::app()->params['userAlbumID'] != $album->parent_id)
                // Проверяем если админ - все нормально
                if (Yii::app()->getModule('user')->isAdmin()) // Если не админ - выбираем альбом пользователя
                    $album = Album::model()->findByAttributes(array('owner_id'=>$this->owner_id));
                else
                    $album = Album::model()->findByAttributes(array('owner_id'=>Yii::app()->user->id));*/
        }
//die(' ::makeAlbumAndThumb:: '.print_r($album));
        if (null === $album) { // Альбома не существует, Создаем альбом + Устанавливаем пазл миниатюрой
            $userProfile = Profile::model()->findByPk($this->owner_id); // Профиль пользователя
            $album = new Album;
            $album->parent_id = Yii::app()->params['userAlbumID'];
            $album->owner_id = $this->owner_id;
            $album->thumbnail_id = $this->id; // Устанавливаем сохраненный пазл миниатюрой альбома
            $album->componentUrl = Yii::app()->user->name;
            $album->title = empty($userProfile->fullname) ? Yii::app()->user->name : $userProfile->fullname;
        } elseif (null == $album->thumbnail_id) // У альбома нет миниатюры
            $album->thumbnail_id = $this->id;  // Устанавливаем сохраненный пазл миниатюрой альбома

        $albumCnt = (empty($album->id)) // Определяем количество пазлов в альбоме
            ? 0
            : CAlbumUtils::getItemsCount($album->id); #die("\$albumCnt=$albumCnt");
        if ($this->isNewRecord)
            $album->cnt = (0 < $albumCnt) ? $albumCnt + 1 : 1;
        $album->cnt = $albumCnt;

        @$album->save();

        $ai = AlbumItem::model()->findByAttributes(array(
            'album_id'=> $album->id,
            'item_id' => $this->id,
        ));
        if (null == $ai) { // Если нет связи
            $ai = new AlbumItem;
            $ai->album_id = $album->id;
            $ai->item_id  = $this->id;
            if ($ai->validate()) @$ai->save();
        }
    }

    /**
     * Изменение размера изображения + вращение
     * Обновляем поля width и height в таблице
     */
    public function resize($angle)
    {
        $sep = '/'; // Разделитель директорий
        if ($angle AND !($angle % 90)) { // Угол задан и он кратен 90
            Yii::import('application.extensions.image.Image');
            $prefix = Yii::app()->params['pathOS'];
            $suffix = $sep.$this->imgUrl.$sep.$this->imgFullName.'.jpg';
            
            $source    = $prefix.Yii::app()->params['pathSource']   .$suffix;
            $original  = $prefix.Yii::app()->params['pathOriginal'] .$suffix;
            $worked    = $prefix.Yii::app()->params['pathWorked']   .$suffix;
            $thumbnail = $prefix.Yii::app()->params['pathThumbnail'].$suffix;

            if (file_exists($source)) $image = new Image($source);         // Если сущ-ет берем исходный файл,
            elseif (file_exists($original)) { // ресайз до 1024 в противном случае
                $image = new Image($original);
                /*$this->createDir($prefix.Yii::app()->params['pathSource'].$sep.$this->imgUrl);
                $image->save($source); // Сохраняем отсутствующий источник на базе Original (1024x1024)
                unset($image);
                $image = new Image($source);*/
            }
            if (null === @$image) return false; // Если image НЕ инициализирован, возвращаем ошибку

            $image->rotate($angle); // Вращение изображения
            if (file_exists($source)) // Пересохраняем уже существующее изображение
                $image->save($source, 0644, true); // Сохраняем исходное изображение

            $image->resize(Yii::app()->params['origSize'][0], Yii::app()->params['origSize'][1], Image::AUTO);
            $image->save($original); // Сохраняем ресайз 1024

            if (Yii::app()->getModule('user')->isAdmin()) { // Для администратора сохраняем превью для главной стр.
                $image->resize(Yii::app()->params['workedSize'][0], Yii::app()->params['workedSize'][1], Image::AUTO);
                $image->save($worked); // , 0644, true
            }
            $image->resize(Yii::app()->params['thumbnailSize'][0], Yii::app()->params['thumbnailSize'][1], Image::AUTO);
            $image->save($thumbnail);

            $size = @GetImageSize($original); // Получаем новые ширину и высоту
            $this->width  = $size[0]; // Обновляем ширину
            $this->height = $size[1]; // Обновляем высоту

            Yii::app()->user->setFlash('updatePuzzle', '1');
            Yii::app()->request->cookies['updatePuzzle'] = new CHttpCookie('updatePuzzle', '1');

            if ($this->validate())
                if ($this->save())
                    return true;
        }
        return false; // Возвращаем ошибку при некорректно заданном угле
    }

    /**
     * Изменение размера изображения
     * Обновляем поля width и height в таблице
     * @param $width
     * @param $height
     * @param int $offsetTop
     * @param int $offsetLeft
     * @internal param bool $admin [ресайз делается администратором-сохраняем исходный файл]
     * @return bool
     */
    public function crop($width, $height, $offsetTop=0, $offsetLeft=0)
    {
        $sep = '/'; // Разделитель директорий
        if ($width AND $height) { // Переданы размеры
            Yii::import('application.extensions.image.Image'); // Библиотека для работы с изображением
            $prefix = Yii::app()->params['pathOS'];
            $suffix = $sep . $this->imgUrl . $sep . $this->imgFullName . '.jpg';

            $source    = $prefix. Yii::app()->params['pathSource']   .$suffix;
            $original  = $prefix. Yii::app()->params['pathOriginal'] .$suffix;
            $worked    = $prefix. Yii::app()->params['pathWorked']   .$suffix;
            $thumbnail = $prefix. Yii::app()->params['pathThumbnail'].$suffix;

            if (file_exists($source)) $image = new Image($source);
            elseif (file_exists($original)){
                $image = new Image($original);
                $image->save($source); // Сохраняем отсутствующий источник на базе Original (1024x1024)
                unset($image);
                $image = new Image($source);
            }
            if (null === @$image) return false; // Если image НЕ инициализирован
            //$size = @GetImageSize($original); //die(print_r($size));
//die("$width, $height, $offsetTop, $offsetLeft");
            //die($image->render());
            $image->crop($width, $height, $offsetTop, $offsetLeft); // original
            //die($image->render());
            $image->save($original, 0644, true);

            if (Yii::app()->getModule('user')->isAdmin()) { // Если администратор
                $image->resize(Yii::app()->params['workedSize'][0], Yii::app()->params['workedSize'][1], Image::AUTO);
                $image->save($worked, 0644, true);
            }
            //die($image->render());
            $image->resize(Yii::app()->params['thumbnailSize'][0], Yii::app()->params['thumbnailSize'][1], Image::AUTO);
            //die($image->render());
            $image->save($thumbnail);

            $size = @GetImageSize($original); // Получаем новые ширину и высоту

            $this->width  = $size[0]; // Обновляем ширину
            $this->height = $size[1]; // Обновляем высоту
            //die(print_r($size));
            $this->save();
            /*if ($this->validate())
                if (!$this->save())
                    die(' -Save Error- '.print_r($this));
                else
                    die(' -Save NO Error- '.print_r($this));
            else
                die(' -Validation Error- '.print_r($this->getErrors()));*/
        }
    }

    /**
     * Очистка введенных пользователем данных (title),
     * на основе которых генерится title, componentUrl
     */
    public function clearTitle($title=null)
    {
        if (empty($this->title)) return false;

        $str = strip_tags(trim($this->title)); // Обрезка пробелов в начале и конце строки
        $normalizeChars = array( // Массив подстановок
            'Á'=>'A', 'À'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Å'=>'A', 'Ä'=>'A', 'Æ'=>'AE', 'Ç'=>'C',
            'É'=>'E', 'È'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Í'=>'I', 'Ì'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ð'=>'Eth',
            'Ñ'=>'N', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',
            'Ú'=>'U', 'Ù'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',

            'á'=>'a', 'à'=>'a', 'â'=>'a', 'ã'=>'a', 'å'=>'a', 'ä'=>'a', 'æ'=>'ae', 'ç'=>'c',
            'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e', 'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'eth',
            'ñ'=>'n', 'ó'=>'o', 'ò'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o',
            'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y',

            'ß'=>'sz', 'þ'=>'thorn', 'ÿ'=>'y',
            '&'=>' and '
        );
        $str = strtr($str, $normalizeChars); // Замена некорректных символов из массива подстановок
        $str = preg_replace("/([\s]+)|([\^\-\+\_]+)/", '-', $str); // Все пробелы (один и более) на дефис '-'
        $str = preg_replace('/[^\w\d\-]+/i', '', $str); // Убираем все, кроме букв, цифр, знаков подчеркивания '_' и дефиса '-'
        $str = preg_replace('/[\-]+/', '-', $str); // Заменяем множество подряд идущих дефисов '-' на только один
        $str = preg_replace('/\-$/', '', $str); // Убираем дефис в конце предложения
        //$this->componentUrl = trim($str); //mysql_real_escape_string()

        $componentUrl = trim($str);
        $this->title = htmlentities(trim($this->title));//mysql_real_escape_string()

        if (empty($componentUrl) OR !preg_match('/[^\-]+/', $componentUrl)) { // Пуст по причине некорректных символов или отличных от Latin1
            if ($this->isNewRecord)
                $componentUrl = md5(date('His')); // Формируем хэш для новой записи
            else
                $componentUrl = $this->id; // ID записи (int(10)) в качестве componentUrl для уже существующей
        }
        // Проверка на уникальность (Incrementator  в случае существования подобного)
        //$this->componentUrl = $this->uniqueUrl($this->componentUrl);
        if ($this->isNewRecord OR empty($this->componentUrl))
            return $this->uniqueUrl($componentUrl);
        elseif (!empty($this->currentAlbumID))
            return $this->uniqueUrl($componentUrl, $this->currentAlbumID);

        return true;
    }

    /**
     * Создание изображения пазла.
     * @param String $file [Название файла (md5)]
     * @return void
     * @todo Слить три функции(crop, resize, fileCreate) в одну
     */
    public function fileCreate($file)
    {
        $sep = '/'; // Разделитель директорий
        $imgFullName = str_pad($this->id, 10, '0', STR_PAD_LEFT);
        $imgUrl = substr($imgFullName,-2, 2).$sep.substr($imgFullName,-4, 2);

        Yii::import('application.extensions.image.Image');
        $prefix = Yii::app()->params['pathOS']; // 'D:/Web/xampp/htdocs/thejigsaw';
        $suffix = $sep.$imgUrl; //.$sep.$imgFullName.'.jpg';

        $source    = $prefix.Yii::app()->params['pathSource'].$suffix;
        $original  = $prefix.Yii::app()->params['pathOriginal'].$suffix;
        $worked    = $prefix.Yii::app()->params['pathWorked'].$suffix;
        $thumbnail = $prefix.Yii::app()->params['pathThumbnail'].$suffix;

        if (file_exists($file) AND $imgUrl) { // $imgFullName ?
            $image = new Image($file);

            if (Yii::app()->getModule('user')->isAdmin()) { // Админ Исходный файл
                $this->createDir($source);
                copy($file, $source.$sep.$imgFullName.'.jpg');
            }
            $this->createDir($original);
            $image->resize(Yii::app()->params['origSize'][0], Yii::app()->params['origSize'][1], Image::AUTO);
            $image->save($original.$sep.$imgFullName.'.jpg');

            if (Yii::app()->getModule('user')->isAdmin()) { // Админ // Worked
                $this->createDir($worked);
                $image->resize(Yii::app()->params['workedSize'][0], Yii::app()->params['workedSize'][1], Image::AUTO);
                $image->save($worked.$sep.$imgFullName.'.jpg');
            }
            $this->createDir($thumbnail);
            $image->resize(Yii::app()->params['thumbnailSize'][0], Yii::app()->params['thumbnailSize'][1], Image::AUTO);
            $image->save($thumbnail.$sep.$imgFullName.'.jpg');

            $size = @GetImageSize($original.$sep.$imgFullName.'.jpg'); // Получаем новые ширину и высоту
            $this->width  = $size[0]; // Обновляем ширину
            $this->height = $size[1]; // Обновляем высоту

            if ($this->validate()) $this->save();

            unlink($file); // Удалить загруженный файл-источник
        }
    }

    /**
     * Создаем директорию, если не существует
     */
    private function createDir($imgFullName)
    {
        /*if (true !== is_dir($prefix.$sep.$dir1)) { // Если не существует первая директория - создаем рекурсивно обе
            if ( !mkdir($prefix.$sep.$dir1.$sep.$dir2, 0700, true) )
                return false;
        } elseif(true !== is_dir($prefix.$sep.$dir1.$sep.$dir2)) { // Иначе если не существует вторая - создаем только вторую
            if ( !mkdir($prefix.$sep.$dir1.$sep.$dir2, 0700, false) )
                return false;
        }*/
        if(true !== is_dir($imgFullName)) {
            if ( !mkdir($imgFullName, 0700, true) )
                echo ' no_create'.$imgFullName; return false;
        }
        return true;
    }

    /**
     * Проверка уникальности componentUrl для каждого альбома, куда входит пазл
     * Составной ключ (album_id, componentUrl)
     * @param $url
     * @param null $album
     * @return string
     */
    private function uniqueUrl($url, $album=null)
    {
        if ((int)$album) { // Альбом передан принудительно
            $albums = array('album_id'=>(int)$album);
        } elseif($this->id) { // ID существует - редактирование записи
            $albums = AlbumItem::model()->findAllByAttributes(array('item_id'=>$this->id));
        } elseif($this->isNewRecord) { // Новая запись
            $albums = array();
        } else return false; //md5(date('His')); // Если не определен объект Item
//die(print_r($albums));die(print_r($this));
        $tmp = array(); $albumStatement = '';
        foreach($albums as $alb) {
            if (isset($alb['album_id']))
                $tmp[] = $alb['album_id'];
            else
                $tmp[] = $alb;
        } // Список альбомов к которым принадлежит пазл
        if (!empty($this->currentAlbumID)) $tmp[] = $this->currentAlbumID; // Куда вставляется
        if (1 < count($tmp)) $albumStatement = '( ai.album_id IN '.'('.implode(',', $tmp).')'.' ) AND ';
        elseif (1 == count($tmp)) $albumStatement = '( ai.album_id = '.$tmp[0].' ) AND ';
//die("\$albumStatement=$albumStatement".print_r($tmp));
        $conn = Yii::app()->db; // Соединение с базой для DAO
        $sql = '
            SELECT i.componentUrl, SUBSTRING_INDEX(i.componentUrl, "-", -1) rem
            FROM item i
            LEFT JOIN album_item ai
              ON ai.item_id = i.id
            WHERE '.$albumStatement.' ( i.componentUrl REGEXP :url ) '.($this->isNewRecord?'':'AND i.id <> '.$this->id).'
            ORDER BY (rem+0) DESC
            LIMIT 1
        ';//die($url);die($sql);
        $sqlStrict = '
            SELECT COUNT(i.componentUrl) FROM item i
            LEFT JOIN album_item ai
              ON ai.item_id = i.id
            WHERE '.$albumStatement.' ( i.componentUrl = :urlStrict ) '.($this->isNewRecord?'':'AND i.id <> '.$this->id).'
        ';//die($sqlStrict);
        $com = $conn->createCommand($sql);            // Подготовка зпроса
        $comStrict = $conn->createCommand($sqlStrict);// Подготовка зпроса
        $com->bindValue(':url', '^'.$url.'-[[:digit:]]+$', PDO::PARAM_STR); // Подстановка шаблона с именем
        $comStrict->bindValue(':urlStrict', $url, PDO::PARAM_STR);          // Строгая подстановка имени

        $itemCnt = $comStrict->queryScalar(); // Число. Строгое соответствие (именно это слово)
        $itemUrl = $com->queryScalar();       // Title (string). REGEXP ('^{{url}}-[[:digit:]]+$')
        //$itemUrl = $this->getLastRecord($itemUrl);
        $urlInc = substr($itemUrl, strlen($url)); //echo $urlInc; // Остаток строки (-128)
        $urlIncDigital = abs((int)$urlInc); //echo ' :: '.$urlIncDigital;// Остаток строки, приведенный к положительному числу
        if ($itemCnt) // Такой точно componentUrl существует
            $this->componentUrl = $url.'-'.++$urlIncDigital; // Инкремент цифрового остатка
        else
            $this->componentUrl = $url; // Уникальное имя
//die("\$itemCnt=$itemCnt::".'cUrl='.$this->componentUrl);
        return true;
    }

    /**
     * Получение соседей слева и справа для пагинатора под флеш-сборкой пазла.
     *
     * @param $item
     * @return array
     */
    public static function getSiblings($item)
    {
        /*if (null == $item or empty($item->album[0]['id'])) return array();

        $albumID = $item->album[0]['id'];
        $itemID = $item['id'];
        $connection=Yii::app()->db;
        $sql = '
            SELECT item.id, item.componentUrl, item.title
            FROM item
            LEFT JOIN album_item ai
              ON ai.item_id = item.id
            WHERE ai.album_id = :albumID
            ORDER BY item.id
        ';//OFFSET  AND (item.id >= :itemID OR  item.id <= :itemID)
        $command=$connection->createCommand($sql);
        $command->bindParam(":albumID", $albumID, PDO::PARAM_INT);
        //$command->bindParam(":itemID", $itemID, PDO::PARAM_INT);
        $siblings = $command->queryAll();
        if ($cnt = count($siblings)) {
            $res = array(
                'first' => $siblings[0],
                'last'  => $siblings[--$cnt],
            );
            foreach ($siblings as $key=>$val) {
                if ($itemID == $val['id']) {
                    $res['current'] = $siblings[$key];
                    $res['prev'] = isset($siblings[$key-1])?$siblings[$key-1]:null;
                    $res['next'] = isset($siblings[$key+1])?$siblings[$key+1]:null;

                    break;
                }
            }
            return $res;
        }
        return array();*/
    }

    /**
     * Преобразования дат из одной кодировки в другую (Y-m-d)
     *
     * @param $format String шаблон кодировки (date())
     * @param $date   String дата
     * @return string Конвертированная дата (yyyy-mm-dd, дополненная 0 слева)
     */
    public static function dateFormat($format, $date)
    {
        $dt = date_parse_from_format($format, $date);

        return
            str_pad($dt['year'], 4, '0', STR_PAD_LEFT)
            . '-' . str_pad($dt['month'], 2, '0', STR_PAD_LEFT)
            . '-' . str_pad($dt['day'], 2, '0', STR_PAD_LEFT);
    }

}