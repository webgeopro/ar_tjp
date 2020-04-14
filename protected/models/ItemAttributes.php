<?php

/**
 * This is the model class for table "item_attributes".
 *
 * The followings are the available columns in table 'item_attributes':
 * @property string $id
 * @property string $keywords
 * @property string $description
 * @property string $author
 * @property string $dateModified
 * @property string $datePublished
 */
class ItemAttributes extends CActiveRecord
{
    public $strKeywords = ''; // Строка для работы с ключевыми словами
    public $arKeywords  = array(); // Строка для работы с ключевыми словами

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ItemAttributes the static model class
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
		return 'item_attributes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'unique'),
            array('keywords, description', 'length', 'max'=>255),
			array('author', 'length', 'max'=>128),
			array('dateModified, dateImageCreated, description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, keywords, description, author, dateModified, dateImageCreated', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'keywords' => 'Keywords',
			'description' => 'Description',
			'author' => 'Author',
			'dateModified' => 'Date Modified',
			'dateImageCreated' => 'Image Published',
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
		$criteria->compare('keywords',$this->keywords,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('author',$this->author,true);
		$criteria->compare('dateModified',$this->dateModified,true);
		$criteria->compare('dateImageCreated',$this->dateImageCreated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Именованные наборы.
     */
    public function scopes()
    {
        return array(
            'potdAuthor'=>array(
                'select' =>
                    '`'.$this::model()->tableAlias.'`.author, `'
                    . $this::model()->tableAlias.'`.description',
            ),
        );
    }

    /**
     * Действия перед валидацией данных
     * @return bool|void
     */
    public function beforeValidate()
    {
        if (is_array($this->dateImageCreated)) // Если не сработал Sanitize
            $this->dateImageCreated = $this->dateImageCreated
                ? is_array($this->dateImageCreated)
                    ? $this->dateImageCreated['Year']
                        .'-'. $this->dateImageCreated['Month']
                        .'-'. $this->dateImageCreated['Day']
                    : $this->dateImageCreated
                : date('Y-m-d');
        /* Экранирование html-сущностей */
        if (null != $this->description)
            $this->description = htmlentities($this->description);

        /* Очистить значения для keywords */
        return $this->clearKeywords();

    }

    public function afterFind()
    {
        $arKeywords = array();

        if ($this->keywords) { // Преобразуем ссылки по ключевым словам
            $kw = explode(',', $this->keywords);
            if (count($kw)) {
                foreach ($kw as $k) {
                    //$k = trim($k); // Обрезается при сохранении пазла.
                    $arKeywords[] = array(
                        urlencode(str_ireplace(' ', '+', $k)).Yii::app()->params['keySuffix'], $k
                    );
                }
            }
        }
        //return $arKeywords;
        $this->arKeywords = $arKeywords;
    }

    /**
     * Очистка введенных ключевых слов перед валидацией
     * (Отбрасываем дубли в keywords)
     * todo Обрезка до 255 символов ???
     */
    public function clearKeywords()
    {
        $attr = preg_replace('#\s*,\s*#', ',', $this->keywords); // Убираем пробелы
        $attr = explode(',', strtolower(trim($attr))); // Приводим к нижнему регистру, разрываем по ','
        if (is_array($attr) AND count($attr)) {        // Если не пустой массив ключевых слов
            $attr = array_unique($attr);               // Проверяем на уникальность
            $attr = implode(',', $attr);               // Сливаем обратно в строку
        }
        $this->keywords = $attr;                       // Наполняем свойство. Далее -> валидация

        return true;
    }

}