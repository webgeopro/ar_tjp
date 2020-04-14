<?php

/**
 * This is the model class for table "album".
 *
 * The followings are the available columns in table 'album':
 * @property string $id
 * @property string $parent_id
 * @property string $owner_id
 * @property string $thumbnail_id
 * @property string $componentUrl
 * @property string $title
 * @property string $keywords
 * @property string $description
 */
class Album extends CActiveRecord
{
    public $imgFullName; // Полное имя, ID дополненное нулями слева до 10 цифр
    public $imgUrl;      // Директория, полученная из ID (к примеру: 34/12)
    public $itemCnt;     // Для работы COUNT

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Album the static model class
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
		return 'album';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_id, owner_id, thumbnail_id', 'length', 'max'=>10),
			array('componentUrl, title', 'length', 'max'=>128),
			array('keywords, description', 'length', 'max'=>255),
            array('componentUrl', 'uniqueAlbums'),
            //array('componentUrl', 'uniqueForMainAlbums'), //, 'message'=>'Test Error', 'enableClientValidation'=>true
            #array('componentUrl', 'unique', 'criteria'=>array('condition'=>'parent_id<>7298')), //=0
            //array('componentUrl', 'unique', 'criteria'=>array('condition'=>'parent_id==7298')), //=0
            //array('title', 'unique'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, parent_id, owner_id, thumbnail_id, componentUrl, title, keywords, description', 'safe', 'on'=>'search'),
		);
	}

    /**
     * Проверка уникальности для основных альбомов
     * @param $attribute
     * @param array $params
     */
    public function uniqueForMainAlbums($attribute,$params=array())
    {
        if(!$this->hasErrors()) {
            $params['criteria'] = array( // Только для пользовательских альбомов
                'condition'=>'parent_id <> :parentID',
                'params'=>array(':parentID'=>Yii::app()->params['userAlbumID']),
            );
            $validator=CValidator::createValidator('unique',$this,$attribute,$params);
            $validator->validate($this,array($attribute));
        }
    }

    /**
     * Проверка уникальности для основных альбомов
     * @param $attribute
     * @param array $params
     */
    public function uniqueMainAlbums($attribute,$params=array())
    {//print_r($attribute);
        if(!$this->hasErrors() AND Yii::app()->getModule('user')->isAdmin()) {
            $cnt = $this->countByAttributes(array('parent_id'=>'0', 'componentUrl'=>$this->componentUrl));
            if ($cnt)
                $this->addError($attribute, 'ComponentUrl no unique.');
        }
    }

    /**
     * Проверка уникальности для альбомов.
     * Свой алгоритм проверки для основных и свой для пользовательских.
     * (тип проверки выбирается на основе поля parent_id)
     *
     * @param $attribute
     * @param array $params
     */
    public function uniqueAlbums($attribute, $params=array())
    {
        if(!$this->hasErrors()) {
            if (Yii::app()->params['userAlbumID'] == $this->parent_id) { // Если альбом пользовательский
                $cnt = $this->countByAttributes(array(
                    'parent_id'   => Yii::app()->params['userAlbumID'], // Поиск среди полльз. альбомов
                    'componentUrl'=> $this->componentUrl,
                ));
                if ($cnt) // Найден альбом с подобным componentUrl
                    $this->addError($attribute, 'ComponentUrl no unique.'); // Формируем ошибку валидации

            } else {
                $params['criteria'] = array(
                    'condition' => 'componentUrl=:componentUrl',
                    'params'    => array(':componentUrl'=>$this->componentUrl),
                );
                if (!empty($this->id)) { // У пазла уже есть запись в базе. Исключаем его из проверки.
                    $params['criteria']['condition'] .= ' AND id <> :albumID';
                    $params['criteria']['params'][':albumID'] = $this->id;
                }
                $validator = CValidator::createValidator('unique', $this, $attribute, $params);
                $validator->validate($this, array($attribute));
            }

                $cnt = $this->countByAttributes(array('parent_id'=>'0', 'componentUrl'=>$this->componentUrl));

        }
    }

    /**
	 * @return array relational rules.
	 */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'item'  => array(self::MANY_MANY, 'Item', 'album_item(album_id, item_id)'),
            'cover' => array(self::BELONGS_TO, 'Item', 'thumbnail_id'),
            'itemStat'=> array(self::STAT, 'Item', 'album_item(album_id, item_id)'),
            // Не учитывается поле Item.dateCreated. НЕ ИСПОЛЬЗУЕТСЯ
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'parent_id' => 'Parent',
			'owner_id' => 'Owner',
			'thumbnail_id' => 'Thumbnail',
			'componentUrl' => 'Component Url',
			'title' => 'Title',
			'keywords' => 'Keywords',
			'description' => 'Description',
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
		$criteria->compare('parent_id',$this->parent_id,true);
		$criteria->compare('owner_id',$this->owner_id,true);
		$criteria->compare('thumbnail_id',$this->thumbnail_id,true);
		$criteria->compare('componentUrl',$this->componentUrl,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('keywords',$this->keywords,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Формируем полное имя файла миниатюры, связанного с альбомом и путь к нему
     */
    public function afterFind()
    {
        $this->imgFullName = str_pad($this->thumbnail_id, 10, '0', STR_PAD_LEFT);
        $this->imgUrl = substr($this->imgFullName,-2, 2).'/'.substr($this->imgFullName,-4, 2);
    }

    /**
     * Действия перед валидацией данных
     * @return bool|void
     */
    public function beforeValidate()
    {
        return $this->clearTitle(); // Очистить значения для componentUrl
    }

    /**
     * Удаление аттрибутивной информации из связанных таблиц
     */
    public function beforeDelete() //after
    {
        $this->deleteItems($this->parent_id == Yii::app()->params['userAlbumID']);

        return true;
    }

    /**
     * Удаление пазлов принадлежащих ТОЛЬКО этому альбому
     *
     * @param bool $bool [Пользовательский альбом]
     * @return bool
     */
    protected function deleteItems($bool = false)
    {
        $albumID =  $this->id;
        if ($bool) {// Это пользовательский альбом
            // Удаляем все пазлы без проверок
            $items = Item::model()->findAllByAttributes(array(
                'owner_id'  => $this->owner_id,
            ));
            foreach ($items as $item)
                $item->delete();

            return true;
        }
        // Ищем пазлы принадлежащие только этому альбому
        $connection=Yii::app()->db;
        $sql = '
            SELECT item.id
            FROM album
            LEFT OUTER JOIN album_item
              ON (album_item.album_id = album.id)
            LEFT OUTER JOIN item
              ON (album_item.item_id= item.id)
            WHERE album.id = :albumID
        ';
        $command = $connection->createCommand($sql);
        $command->bindParam(":albumID", $albumID, PDO::PARAM_INT);
        $items = $command->queryAll();

        $sql = '
            SELECT COUNT(*)
            FROM album_item ai
            WHERE item_id = :itemID AND album_id <> :albumID
        ';
        $command = $connection->createCommand($sql);
        // Удаляем уникальные пазлы (принадлежащие только этому альбому)
        foreach ($items as $item) {
            $command->bindParam(":itemID",  $item['id'], PDO::PARAM_INT);
            $command->bindParam(":albumID", $albumID,    PDO::PARAM_INT);
            if ( 0 == $command->queryScalar() )   // Удаляем пазл - его нет в других альбомах
                Item::model()->findByPk($item['id'])->delete();
        }

        return true;
    }

    /**
     * Именованные наборы.
     */
    public function scopes()
    {
        $t = $this->model()->tableAlias;
        return array(
            'siblings'=>array( // В виджете albumSiblings. 15 Последних альбомов.
                'condition' => "$t.parent_id = 0 AND $t.id <> 7298",
                'order' => "$t.id DESC",
                'limit' => 15,
            ),
        );
    }

    /**
     * Список основных альбомов c исключениями
     * Используется в ServiceController actionMovePuzzle
     * @param array $ids
     * @return \Album
     */
    public function mainAlbums($ids=array())
    {
        $str = '';
        $table = $this->model()->tableAlias;

        if (count($ids)) {
            foreach ($ids as $id) {
                $str .= $id['id'].',';
            }
            $str = ' AND '.$table.'.id NOT IN ('.substr($str, 0, -1).') ';
        }

        $this->getDbCriteria()->mergeWith(array(
            'select' => 'id, thumbnail_id, title, sort',
            'condition' => $table.'.parent_id = 0 AND '.$table.'.id <> '.Yii::app()->params['userAlbumID'].$str,
            'order' => $table.'.sort',
        ));

        return $this;
    }

    /**
     * Очистка введенных пользователем данных (fullname),
     * на основе которых генерится componentUrl альбома
     */
    public function clearTitle()
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
        $str = preg_replace('/[\-]+/', '-', $str); // Убираем все, кроме букв, цифр, знаков подчеркивания '_' и дефиса '-'
        $str = preg_replace('/\-$/', '', $str); // Убираем дефис в конце предложения
        //$this->componentUrl = trim($str); //mysql_real_escape_string()
        $componentUrl = trim($str); //mysql_real_escape_string()
        //$this->title = trim($this->title);//mysql_real_escape_string()

        if (empty($componentUrl) OR !preg_match('/[^\-]+/', $componentUrl)) { // Пуст по причине некорректных символов или отличных от Latin1
            if ($this->isNewRecord)
                $componentUrl = md5(date('His')); // Hid
            else
                $componentUrl = $this->id;
        }
        // Проверка на уникальность (Incrementator  в случае существования подобного)
        if ($this->isNewRecord OR empty($this->componentUrl))
            return $this->uniqueUrl($componentUrl);

        return true;
    }

    /**
     * Проверка уникальности componentUrl для каждого альбома, куда входит пазл
     * Составной ключ (album_id, componentUrl)
     * @param $url
     * @internal param null $album
     * @return string
     */
    private function uniqueUrl($url)
    {
        $conn = Yii::app()->db; // Соединение с базой для DAO
        $sql = '
            SELECT componentUrl, SUBSTRING_INDEX(componentUrl, "-", -1) rem
            FROM album
            WHERE componentUrl REGEXP :url '.($this->isNewRecord?'':'AND id <> '.$this->id).'
            ORDER BY (rem+0) DESC
            LIMIT 1
        ';
        $sqlStrict = '
            SELECT COUNT(componentUrl)
            FROM album
            WHERE componentUrl = :urlStrict '.($this->isNewRecord?'':'AND id <> '.$this->id).'
        ';
        $com = $conn->createCommand($sql);            // Подготовка зпроса
        $comStrict = $conn->createCommand($sqlStrict);// Подготовка зпроса
        $com->bindValue(':url', '^'.$url.'-[[:digit:]]+$', PDO::PARAM_STR); // Подстановка шаблона с именем
        $comStrict->bindValue(':urlStrict', $url, PDO::PARAM_STR);          // Строгая подстановка имени

        $itemCnt = $comStrict->queryScalar(); // Число. Строгое соответствие (именно это слово)
        $itemUrl = $com->queryScalar();       // Title (string). REGEXP ('^{{url}}-[[:digit:]]+$')

        $urlInc = substr($itemUrl, strlen($url)); // Остаток строки (-128)
        $urlIncDigital = abs((int)$urlInc); // Остаток строки, приведенный к положительному числу
        if ($itemCnt) // Такой точно componentUrl существует
            $this->componentUrl = $url.'-'.++$urlIncDigital; // Инкремент цифрового остатка
        else
            $this->componentUrl = $url; // Уникальное имя

        return true;
    }

    /**
     * Удаление всех пазлов, входящих в альбом
     */
    public function deleteItemsFromAlbum()
    {
        $items = CAlbumUtils::getAllItemsIDsFromAlbum($this->id); // Получение всех ID пазлов, входящих в альбом [array]
        foreach ($items as $val) {
            Item::model()->findByPk($val)->delete(); // Запускаем всю цепочку событий (beforeDelete+afterDelete)
        }
    }

}