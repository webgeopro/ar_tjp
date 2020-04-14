<?php

class ProfileController extends Controller
{
	public $defaultAction = 'profile';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;
	/**
	 * Shows a particular model.
	 */
	public function actionProfile()
	{
        if (Yii::app()->getModule('user')->isAdmin()){
            $model = $this->loadUser();
            $this->render('profile',array(
                'model'=>$model,
                'profile'=>$model->profile,
            ));
        } else { // Закрываем доступ к профилю всем кроме администраторов
            $this->redirect(Yii::app()->controller->module->profileEditUrl); // Редирект на редактирование профиля
        }
	}


    /**
     * @return array action filters
     */
    public function filters()
    {
        return CMap::mergeArray(parent::filters(),array(
            'accessControl', // perform access control for CRUD operations
        ));
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',  // Разрешаем доступ только для администраторов
                'actions'=>array('admin'),
                'users'=>Yii::app()->getModule('user')->getAdmins(),
            ),
            array('deny',  // deny all users
                'actions'=>array('admin'),
                'users'=>array('*'),
            ),
            array('allow',  // deny all users
                //'actions'=>array('autocomplete'),
                'users'=>array('*'),
            ),
        );
    }
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionEdit()
	{
		$model   = $this->loadUser(); // AR User
		$profile = $model->profile;   // AR Profile

		if(isset($_POST['ajax']) && $_POST['ajax']==='profile-form') { // ajax validator
			echo UActiveForm::validate(array($model,$profile));
			Yii::app()->end();
		}
		if (isset($_POST['User'])) {//die(print_r($_POST['Profile'])); // Если переданы данные из формы или обнулена дата рождения
            // Устанавливаем флажок изменения email для изменения title альбома пользователя в будущем
            $model->attributes = $_POST['User']; // Присваивание безопасных полей для User

            $profile->attributes = $_POST['Profile']; // Присваивание безопасных полей для Profile
            if (null != $_POST['Profile']['fullname'] AND $profile->fullname != $_POST['Profile']['fullname']) {
                $isFullnameChanged = true;
                $profile->fullname = $_POST['Profile']['fullname'];
            } else
                $isFullnameChanged = false;

            $profile->birthday = $this->sanitize($profile->birthday);
            ActiveDateSelect::sanitize($profile, 'birthday');
            $profile->gender   = $_POST['Profile']['gender'];
            $profile->country  = $_POST['Profile']['country'];

            //die(print_r($profile)); //die('bd='.print_r($profile->birthday));
			if($model->validate() && $profile->validate()) {//die(print_r($profile)); die('after validate');
				$model->save(); // Сохрание User (email)
                if ($profile->save()) {//die('bd='.$profile->birthday); // Сохранение Profile (fullname, gender, country, birthday)
                    if ($isFullnameChanged) // Обновляем title альбома AND !Yii::app()->user->isAdmin()
                        CUserAlbums::saveAlbumFieldByUserId(Yii::app()->user->id, 'title', $profile->fullname);
                }
                Yii::app()->user->setFlash('profileMessage',UserModule::t("Changes is saved."));
                $this->refresh(true);
			} else
                $profile->validate();
        }
        $breadcrumbs = UserBreadcrumbs::userAlbums($model->id); // die(print_r($breadcrumbs));

        $this->render('edit',array(
			'model'=>$model,
			'profile'=>$profile,
            'breadcrumbs'=>$breadcrumbs,
		));
	}

    /**
     * Редактирование пользователя администратором
     * @param int
     * @param string
     */
    public function actionAdmin($id=null, $address='')
	{
        if ($id)
            $model = $this->loadUser4Admin($id);
        else
            $model = new User;
        //die(print_r($model));
		if (null == $model->profile) {
            $profile = new Profile;
            if (!empty($profile->user_id))
                $profile->user_id = $model->id;
        } else
            $profile = $model->profile;

		// ajax validator
		if(isset($_POST['ajax']) && $_POST['ajax']==='profile-form') {
			echo UActiveForm::validate(array($model,$profile));
			Yii::app()->end();
		}
		//die(print_r($_POST));
		if(isset($_POST['User'])) {
            if (empty($_POST['User']['password'])) // Только для непустых значений
                unset($_POST['User']['password']);

            $model->attributes   = $_POST['User'];
            $profile->attributes = $_POST['Profile'];

            if (!empty($_POST['User']['password'])) // Если установлен новый пароль -> кодируем
                $model->password = UserModule::encrypting($_POST['User']['password']);

            Yii::import('application.extensions.ActiveDateSelect'); // Импортируем расширение для работы с датами (3 select-а)

            if (null != $_POST['Profile']['fullname'] AND $profile->fullname != $_POST['Profile']['fullname']) {
                $isFullnameChanged = true; // Флаг изменения componentUrl альбома
                $profile->fullname = $_POST['Profile']['fullname'];
            } else
                $isFullnameChanged = false;
            //die(print_r($model));
            $profile->birthday = $this->sanitize($profile->birthday);
            ActiveDateSelect::sanitize($profile, 'birthday');
            $profile->gender   = $_POST['Profile']['gender'];
            $profile->country  = $_POST['Profile']['country'];

			if($model->validate()&&$profile->validate()) {
                $model->save(); // Сохрание User (email)
                $profile->user_id = $model->id;
                if ($profile->save()) { // Сохранение Profile (fullname, gender, country, birthday)
                    if ($isFullnameChanged) // Обновляем title альбома AND !Yii::app()->user->isAdmin()
                        CUserAlbums::saveAlbumFieldByUserId($model->id, 'title', $profile->fullname);
                }
				Yii::app()->user->setFlash('profileMessage',UserModule::t("Changes is saved."));
				if ($address) {
                    $address = ('undefined' == $address) ? '' : $address;
                    $this->redirect(array('/admin/user?'.$address));
                } elseif( Yii::app()->getModule('user')->isAdmin() ) { // Переадресация для админа
                    $page = Yii::app()->request->getParam('User_page', null);
                    $url = '/admin/user' . ($page ? '?User_page='.$page : '');
                    $this->redirect(array($url));
                } else {
                    $this->redirect(array('/user/profile'));
                }
			} else $profile->validate();
		}

		$this->render('edit_admin',array(
			'model'   => $model,
			'profile' => $profile,
		));
	}
	
	/**
	 * Change password
	 */
	public function actionChangepassword() {
		$model = new UserChangePassword;
		if (Yii::app()->user->id) {
			
			if(isset($_POST['ajax']) && $_POST['ajax']==='changepassword-form') { // ajax validator
				echo UActiveForm::validate($model);
				Yii::app()->end();
			}
			if(isset($_POST['UserChangePassword'])) {
					$model->attributes=$_POST['UserChangePassword'];
					if($model->validate()) {
						$new_password = User::model()->notsafe()->findbyPk(Yii::app()->user->id);
						$new_password->password = UserModule::encrypting($model->password);
						$new_password->activkey = UserModule::encrypting(microtime().$model->password);
						$new_password->save();
						Yii::app()->user->setFlash('profileMessage',UserModule::t("New password is saved."));
						$this->redirect(array("profile"));
					}
			}
            $breadcrumbs = UserBreadcrumbs::userAlbums(Yii::app()->user->id);
			$this->render('changepassword',array('model'=>$model, 'breadcrumbs'=>$breadcrumbs));
	    }
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
	 */
	public function loadUser()
	{
		if($this->_model===null) {
			if(Yii::app()->user->id) //$this->_model=Yii::app()->controller->module->user();
                $this->_model=User::model()->with('profile')->findbyPk(Yii::app()->user->id);
			if($this->_model===null)
				$this->redirect(Yii::app()->controller->module->loginUrl);
		}
        if ( empty($this->_model->profile) AND !empty($this->_model->id) ) { // Если не определен Profile
            $this->_model->profile = Profile::model()->findByPk($this->_model->id);
            if (null === $this->_model->profile) {
                $this->_model->profile = new Profile;
                $this->_model->profile->user_id = $this->_model->id;
            }
        }

		return $this->_model;
	}

    /**
     * Загрузка пользователя для редактирования администратором
     * @param null $id
     * @return CActiveRecord
     * @throws CHttpException
     */
    public function loadUser4Admin($id=null)
	{
        if($this->_model===null)
        {
            if($id!==null || isset($_GET['id']))
                $this->_model=User::model()->with('profile')->findbyPk($id!==null ? $id : $_GET['id']);
            if($this->_model===null)
                throw new CHttpException(404,'The requested page does not exist.');
        }
        return $this->_model;
	}

    /**
     * Преобразование массива даты (Год, месяц, день) в строку в формате "YYYY-MM-DD"
     * 0000-00-00 в случае отсутствия значений
     * Запускается если передан $_POST['user'] (e-mail передается всегда)
     *
     * @param \Profile|string $bday
     * @return string "YYYY-MM-DD" | null
     */
    private function sanitize($bday=null)
    {
        $birthday = $bday;
        if (isset($_POST['Profile']['birthday'])) { // Передается в виде массива
            if (is_array($_POST['Profile']['birthday'])) { // Хоть один параметр не нулевой
                $birthday = $_POST['Profile']['birthday']['Year']      ? $_POST['Profile']['birthday']['Year'] : '0000';
                $birthday.= '-'.($_POST['Profile']['birthday']['Month']? $_POST['Profile']['birthday']['Month']: '00');
                $birthday.= '-'.($_POST['Profile']['birthday']['Day']  ? $_POST['Profile']['birthday']['Day']  : '00');
            } elseif (null == $_POST['Profile']['birthday']) { // Если нет значений, null
                $birthday = null; // Обнуляем дату
            }
        }
        return '0000-00-00' == $birthday
            ? null
            : $birthday;
    }

}