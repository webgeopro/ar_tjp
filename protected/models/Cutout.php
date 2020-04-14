<?php

/**
 * This is the model class for table "cutout".
 *
 * The followings are the available columns in table 'cutout':
 * @property integer $id
 * @property string $cnt
 * @property string $name
 */
class Cutout extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Cutout the static model class
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
		return 'cutout';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cnt', 'required'),
			array('cnt', 'length', 'max'=>10),
			array('name', 'length', 'max'=>30),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, cnt, name', 'safe', 'on'=>'search'),
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
			'cnt' => 'Cnt',
			'name' => 'Name',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('cnt',$this->cnt,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Получить все нарезки, преюбразовать в массив и закешировать
     */
    public static function getCutout()
    {
        $cutouts = self::model()->cache(86400)->findAll(array('select'=>'id, name')); // Раз в сутки
        if (null == $cutouts) return array('3' => Yii::app()->params['defaultCutout']); // Нарезка по умолчанию

        foreach ($cutouts as $cut)
            $arr[$cut['id']] = $cut['name'];

        return isset($arr) ? $arr : array();
    }
}