<?php

class User extends CActiveRecord
{
	const STATUS_NOACTIVE=0;
	const STATUS_ACTIVE=1;
	const STATUS_BANED=-1;

    public $arTypes = array( // Названия статусов и картинки / ссылки
        '-1' => array('Locked', 'Unlock', 'Lock'), //Lock Banned (Lock) (status, icon for change)
        '0'  => array('Pending', 'Activate', 'Lock'), //Lock Not active
        '1'  => array('Active', 'Lock', 'Unlock'), //Unlock Active
    );
	
	/**
	 * The followings are the available columns in table 'users':
	 * @var integer $id
	 * @var string $username
	 * @var string $password
	 * @var string $email
	 * @var string $activkey
	 * @var integer $createtime
	 * @var integer $lastvisit
	 * @var integer $superuser
	 * @var integer $status
	 */

    /**
     * Returns the static model of the specified AR class.
     * @param string $className
     * @return CActiveRecord the static model class
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
		return Yii::app()->getModule('user')->tableUsers;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		
		return ((Yii::app()->getModule('user')->isAdmin())?array(
			#array('username, password, email', 'required'),
			array('username', 'length', 'max'=>20, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
			array('password', 'length', 'max'=>128, 'min' => 4,'message' => UserModule::t("Incorrect password (minimal length 4 symbols).")),
			array('email', 'email'),
			array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
			//array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
			//array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
			array('status', 'in', 'range'=>array(self::STATUS_NOACTIVE,self::STATUS_ACTIVE,self::STATUS_BANED)),
			array('superuser', 'in', 'range'=>array(0,1)),
			array('username, email, createtime, lastvisit, superuser, status', 'required'),
			array('createtime, lastvisit, superuser, status', 'numerical', 'integerOnly'=>true),
		):((Yii::app()->user->id==$this->id)?array(
			array('username, email', 'required'),
			array('username', 'length', 'max'=>32, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
			array('email', 'email'),
			array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
			//array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
			//array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
		):array()));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$relations = array(
			'profile'=>array(self::HAS_ONE, 'Profile', 'user_id'),
		);
		if (isset(Yii::app()->getModule('user')->relations)) $relations = array_merge($relations,Yii::app()->getModule('user')->relations);
		return $relations;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>UserModule::t("username"),
			'password'=>UserModule::t("password"),
			'verifyPassword'=>UserModule::t("Retype Password"),
			'email'=>UserModule::t("E-mail"),
			'verifyCode'=>UserModule::t("Verification Code"),
			'id' => UserModule::t("Id"),
			'activkey' => UserModule::t("activation key"),
			'createtime' => UserModule::t("Registration date"),
			'lastvisit' => UserModule::t("Last visit"),
			'superuser' => UserModule::t("Superuser"),
			'status' => UserModule::t("Status"),
		);
	}
	
	public function scopes()
    {
        return array(
            'active'=>array(
                'condition'=>'status='.self::STATUS_ACTIVE,
            ),
            'notactvie'=>array(
                'condition'=>'status='.self::STATUS_NOACTIVE,
            ),
            'banned'=>array(
                'condition'=>'status='.self::STATUS_BANED,
            ),
            'superuser'=>array(
                'condition'=>'superuser=1',
            ),
            'notsafe'=>array(
            	'select' => 'id, username, password, email, activkey, createtime, lastvisit, superuser, status',
            ),
        );
    }
	
	public function defaultScope()
    {
        return array(
            'select' => 'id, username, email, createtime, lastvisit, superuser, status',
        );
    }
	
	public static function itemAlias($type,$code=NULL) {
		$_items = array(
			'UserStatus' => array(
				self::STATUS_NOACTIVE => UserModule::t('Not active'),
				self::STATUS_ACTIVE => UserModule::t('Active'),
				self::STATUS_BANED => UserModule::t('Banned'),
			),
			'AdminStatus' => array(
				'0' => UserModule::t('No'),
				'1' => UserModule::t('Yes'),
			),
		);
		if (isset($code))
			return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
		else
			return isset($_items[$type]) ? $_items[$type] : false;
	}

    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id,true);
        $criteria->compare('username',$this->username,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function searchAdmin()
    {
        $criteria=new CDbCriteria;
        $criteria->join = 'LEFT JOIN user_profiles AS p ON t.id=p.user_id';

        $criteria->compare('t.id',      $this->id,      true);
        $criteria->compare('t.username',$this->username,true);
        $criteria->compare('t.email',   $this->email,   true, 'OR');
        $criteria->compare('p.fullname',$this->username,true, 'OR');

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>20, // Количество пользователей на странице admin/user
            ),

        ));
    }

    /**
     * Удаляем пазлы пользовательского альбома
     *
     * @return bool|void
     */
    public function afterDelete()
    {
        $album = Album::model()->findByAttributes(array('owner_id'=>$this->id));
        if (null != $album)
            $album->delete();
        else {
            $items = Item::model()->findAllByAttributes(array('owner_id'=>$this->id));
            if (null != $items)
                foreach ($items as $item)
                    $item->delete();
        }

        return true;
    }

    /**
     * @return bool|void
     */
    public function beforeSave()
    {
        if ($this->isNewRecord) { // Новая запись
            if (Yii::app()->getModule('user')->isAdmin()) { // Действия для администратора
                $this->createtime = time();                 // Установка даты регистрации
                $this->status     = User::STATUS_ACTIVE;    // Пользователь активен без подтверждения email
            }
        }
        return true;
    }

    /**
     * @return bool|void
     */
    public function afterSave()
    {
        // Создание уникального пользовательского альбома
        if (1 == $this->status) // Если пользователь активировал запись
            $this->saveAlbum(); // Создаем ему альбом

        return true;
    }

    /**
     * Сохрание альбома
     */
    public function saveAlbum()
    {
        //$album = Album::model()->findByAttributes(array('owner_id'=>$this->id), array('order'=>'id ASC'));
        if (Album::model()->exists('owner_id=:ownerID', array(':ownerID'=>$this->id)))
            return true;

        $album = new Album;
        $profile = Profile::model()->findByPk($this->id);

        $album->parent_id = Yii::app()->params['userAlbumID'];
        $album->owner_id = $this->id;
        $album->title = (null == $profile)
            ? $this->username
            : $profile->fullname; //'+++'.
        $album->componentUrl = $this->username;

        //return
        $album->save();
    }
}