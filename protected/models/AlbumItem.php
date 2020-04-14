<?php

/**
 * This is the model class for table "album_item".
 *
 * The followings are the available columns in table 'album_item':
 * @property string $album_id
 * @property string $item_id
 */
class AlbumItem extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AlbumItem the static model class
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
		return 'album_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('album_id, item_id', 'required'),
			array('album_id, item_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('album_id, item_id', 'safe', 'on'=>'search'),
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
			'album_id' => 'Album',
			'item_id' => 'Item',
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

		$criteria->compare('album_id',$this->album_id,true);
		$criteria->compare('item_id',$this->item_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Запускаем проверку не является ли удаляемый из альбома пазл его миниатюрой
     * Если да - ищем и сохраняем последний пазл, связанный с этим альбомом как миниатюру
     * @return bool|void
     */
    protected function beforeDelete()
    {
        $itemId  = $this->item_id;  // При подстановке значений нужен режим write
        $albumId = $this->album_id; // Иначе bindParam сгенерит ошибку
        $conn = Yii::app()->db; // Подключаемся к БД, используя DAO
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
        $command->bindParam(':albumID', $albumId, PDO::PARAM_INT); // Подстановка значений
        $command->bindParam(':itemID', $itemId, PDO::PARAM_INT); // Подстановка значений
        $thumbnailId = $command->queryScalar(); // Выполняем запрос и получаем первое значение

        if (isset($thumbnailId)) { // Если есть пазл в альбоме, устанавливаем его в качестве новой миниатюры альбома
            $album = Album::model()->findByPk($albumId); // Инициализируем альбом
            $album->thumbnail_id = $thumbnailId;  // Устанавливаем новую миниатюру
            if (isset($this->album))
                $this->album->save();  // Сохраняем изменения в альбоме
        }

        return true; // Удаляем пазл из альбома в любом случае (даже если не установлен новый thumbnail)
    }
}