<?php

class LoginController extends Controller
{
	public $defaultAction = 'login';

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if (Yii::app()->user->isGuest) {
			$model=new UserLogin;
			// collect user input data
			if(isset($_POST['UserLogin'])) {//die(print_r($_POST));
				$model->attributes=$_POST['UserLogin'];//die(print_r($model));
				// validate user input and redirect to previous page if valid
				if($model->validate()) {//die(print_r($model));
					$this->lastViset();//die(print_r($_COOKIE));die(print_r(Yii::app()->session));die(print_r($_SESSION));
                    $this->redirect('/'); // Редирект на главную страницу сайта (thejigsawpuzzle.com)
					if (strpos(Yii::app()->user->returnUrl,'/index.php')!==false)
						$this->redirect(Yii::app()->controller->module->returnUrl);
					else
						$this->redirect(Yii::app()->user->returnUrl);
				} else {
                    Yii::app()->user->setFlash('loginError', 'Your login information is incorrect. Please try again.'); // Ok
                }//die('stop');
			} elseif(isset($_POST['LoginForm'])) { // Обработка входа с первой страницы
                $model->attributes=$_POST['LoginForm'];//die(print_r($model));
                if($model->validate()) {//die(print_r($model));
                    $this->lastViset();
                    $this->redirect('/'); // Редирект на главную страницу сайта (thejigsawpuzzle.com)

                } else {//die('No_Validate');
                    Yii::app()->user->setFlash('loginError', 'Your login information is incorrect. Please try again.'); // Ok
                }

            }
			//
			 $this->render('/user/login',array('model'=>$model));
		} else {
			//die('authed');
            $this->redirect(Yii::app()->controller->module->returnUrl);
        }
	}
	
	private function lastViset() {
		$lastVisit = User::model()->notsafe()->findByPk(Yii::app()->user->id);
		$lastVisit->lastvisit = time();
		$lastVisit->save();
	}

}