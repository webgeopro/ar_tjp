<?php

/**
 * This is the model class for table "g2_user". From another DB server.
 * Connection: dbTJP
 *
 * The followings are the available columns in table 'g2_user':
 * @property integer $g_id
 * @property string $g_userName
 * @property string $g_fullName
 * @property string $g_hashedPassword
 * @property string $g_email
 * @property string $g_language
 * @property integer $g_locked
 */
class G2User extends CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return G2User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    // отдаём соединение, описанное в компоненте dbTJP
    public function getDbConnection()
    {
        return Yii::app()->db2;
    }

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'g2_user'; //db2.
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('g_id, g_userName', 'required'),
			array('g_id, g_locked', 'numerical', 'integerOnly'=>true),
			array('g_userName', 'length', 'max'=>32),
			array('g_fullName, g_hashedPassword, g_language', 'length', 'max'=>128),
			array('g_email', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('g_id, g_userName, g_fullName, g_hashedPassword, g_email, g_language, g_locked', 'safe', 'on'=>'search'),
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
			'g_id' => 'G',
			'g_userName' => 'G User Name',
			'g_fullName' => 'G Full Name',
			'g_hashedPassword' => 'G Hashed Password',
			'g_email' => 'G Email',
			'g_language' => 'G Language',
			'g_locked' => 'G Locked',
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

		$criteria->compare('g_id',$this->g_id);
		$criteria->compare('g_userName',$this->g_userName,true);
		$criteria->compare('g_fullName',$this->g_fullName,true);
		$criteria->compare('g_hashedPassword',$this->g_hashedPassword,true);
		$criteria->compare('g_email',$this->g_email,true);
		$criteria->compare('g_language',$this->g_language,true);
		$criteria->compare('g_locked',$this->g_locked);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}