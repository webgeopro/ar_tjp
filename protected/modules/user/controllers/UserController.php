<?php

class UserController extends Controller
{
	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;
    public $layout = '//layouts/admin';

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
            array('allow',  // allow all users
                'actions'=>array('autocomplete'),
                'users'=>array('*'),
            ),
            array('allow',  // Разрешаем доступ только для администраторов
				'actions'=>array('index', 'view', 'statusChange', 'delete', 'edit'),
                'users'=>Yii::app()->getModule('user')->getAdmins(),
			),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
	}

	/**
	 * Displays a particular model.
	 */
	public function actionView()
	{
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/css/admin.css');
        $model = $this->loadModel();
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Lists all users.
	 */
	public function actionIndex()
	{
        Yii::app()->clientScript
            ->registerScriptFile(Yii::app()->baseUrl.'/js/common.js')
            ->registerCSSFile(Yii::app()->baseUrl.'/css/admin.css');
        $model=new User('searchAdmin');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['User'])) {
            $model->attributes=$_GET['User'];
            $model->email=$_GET['User']['username'];
        }
        //die(print_r($model));
        $this->render('index',array(
            'model' => $model,
            //'arTypes' => $this->arTypes,
        ));
        /*$dataProvider=new CActiveDataProvider('User', array(
			'criteria'=>array(
		        //'condition'=>'status>'.User::STATUS_BANED,
		    ),
			'pagination'=>array(
				'pageSize'=>Yii::app()->controller->module->user_page_size,
			),
		));

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
            'content' => 'New Content',
		));*/
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
				$this->_model=User::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}


    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
     * @throws CHttpException
     * @return CActiveRecord
     */
	public function loadUser($id=null)
	{
		if($this->_model===null)
		{
			if($id!==null || isset($_GET['id']))
				$this->_model=User::model()->findbyPk($id!==null ? $id : $_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

    /**
     * Поле автоподстановки имени пользователя.
     */
    public function actionAutocomplete()
    {
        $query = Yii::app()->getRequest()->getParam('term');//User[username]

        if(Yii::app()->request->isAjaxRequest && $query) {
            $criteria = new CDbCriteria;
            $criteria->join = 'LEFT JOIN user_profiles AS p ON t.id=p.user_id';

            $criteria->addSearchCondition('t.username',$query, true);
            $criteria->addSearchCondition('t.email',   $query, true, 'OR');
            $criteria->addSearchCondition('p.fullname',$query, true, 'OR');

            $criteria->limit = 20;

            $users = User::model()->findAll($criteria);
            // обрабатываем результат
            $result = array();
            foreach($users as $user) {
                $result[] = array('id'=>$user['id'], 'label'=>$user['username'], 'value'=>$user['username']);
            }
            echo CJSON::encode($result);
            Yii::app()->end();
        }
    }

    /**
     * Изменение статуса пользователя. Ajax - запрос.
     */
    public function actionStatusChange($id=null, $status=null)
    {
        switch (@strtolower($status)) {
            case 'lock':
                $statusOk = true; $statusInt = User::STATUS_BANED; $statusNew = 'Unlock'; $text = 'Locked';
                break;
            case 'activate':
                $statusOk = true; $statusInt = User::STATUS_ACTIVE; $statusNew = 'Lock'; $text = 'Active';
                break;
            case 'unlock':
                $statusOk = true; $statusInt = User::STATUS_ACTIVE;  $statusNew = 'Lock'; $text = 'Active';
                break;
            default: $statusOk = false;
        }
        if(Yii::app()->request->isAjaxRequest && $id && $statusOk) {
            if ( User::model()->updateByPk($id, array('status' => $statusInt)) ) {
                $result = array(
                    'result'=>'success', 'id'=>$id, 'text'=>$text,
                    'statusNew'=>$statusNew, 'statusOld'=>$status,
                );
                echo CJSON::encode($result);
                Yii::app()->end();
            }
        }

        echo CJSON::encode(array('result'=>'error'));
        Yii::app()->end();
    }

    /*public function actionDelete()
    {
        $this->redirect(array(
            $this->redirect(array('/user/admin/delete')),
            'backUrl' => '',
        ));
    }*/
}
