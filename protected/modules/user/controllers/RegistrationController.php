<?php

class RegistrationController extends Controller
{
	public $defaultAction = 'registration';
	


	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		/*return (isset($_POST['ajax']) && $_POST['ajax']==='registration-form')?array():array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);*/
	}
	/**
	 * Registration user
	 */
	public function actionRegistration() {
            $model = new RegistrationForm;
            $profile=new Profile;
            $profile->regMode = true;

			if(isset($_POST['ajax']) && $_POST['ajax']==='registration-form') // ajax validator
			{
				echo UActiveForm::validate(array($model,$profile));
				Yii::app()->end();
			}
			
		    if (Yii::app()->user->id) {
		    	$this->redirect(Yii::app()->controller->module->profileUrl);
		    } else {
		    	if(isset($_POST['RegistrationForm'])) {
					$model->attributes=$_POST['RegistrationForm'];
					$profile->attributes=((isset($_POST['Profile'])?$_POST['Profile']:array()));
                    //Yii::import('ext.ActiveDateSelect'); // Импортируем расширение для работы с датами (3 select-а)
                    //ActiveDateSelect::sanitize($profile, 'birthday'); // Объединяем в одну строку (YYYY-MM-DD)
                    $profile->birthday = CDateTimeUtils::sanitize($_POST['Profile']['birthday']);
                    $profile->country = $_POST['Profile']['country']; // Что-то с safe attributes
                    $profile->gender = $_POST['Profile']['gender'];
                    //die(print_r($model));
                    //die(print_r($profile));
                    if($model->validate() && $profile->validate())
					{
						$soucePassword = $model->password;
						$model->activkey=UserModule::encrypting(microtime().$model->password);
						$model->password=UserModule::encrypting($model->password);
						                                  // Из второго аргумента берем salt, для корректного сравнения:
                        $model->verifyPassword=UserModule::encrypting($model->verifyPassword, $model->password);
						$model->createtime=time();
						$model->lastvisit=((Yii::app()->controller->module->loginNotActiv||(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false))&&Yii::app()->controller->module->autoLogin)?time():0;
						$model->superuser=0;
						$model->status=((Yii::app()->controller->module->activeAfterRegister)?User::STATUS_ACTIVE:User::STATUS_NOACTIVE);
                        //die(print_r($model));
                        if ($model->save()) {
							$profile->user_id = $model->id;
							$profile->save();
							if (Yii::app()->controller->module->sendActivationMail) {
								$activation_url = $this->createAbsoluteUrl('/user/activation/activation',array("activkey" => $model->activkey, "email" => $model->email));
								UserModule::sendMail(
                                    $model->email,
                                    'TheJigsawPuzzles.com Account Activation',
                                    'Hello, '.$model->username
                                    ."\r\n".'You receive this email because you have registered at TheJigsawPuzzles.com'
                                    ."\r\n".'Your username is: '.$model->username
                                    ."\r\n".'To finish the registration process please click the following link: '.$activation_url
                                    ."\r\n".'If you did not register at this site then please ignore this email.  The registration will not become valid and you will not receive any further emails.  Sorry for the inconvenience.'
                                    ."\r\n".'Thank you!'
                                );
							}
							
							if ((Yii::app()->controller->module->loginNotActiv||(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false))&&Yii::app()->controller->module->autoLogin) {
									$identity=new UserIdentity($model->username,$soucePassword);
									$identity->authenticate();
									Yii::app()->user->login($identity,0);
									$this->redirect(Yii::app()->controller->module->returnUrl);
							} else {
								if (!Yii::app()->controller->module->activeAfterRegister&&!Yii::app()->controller->module->sendActivationMail) {
									Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Contact Admin to activate your account."));
									$flashRegistration = UserModule::t("Thank you for your registration. Contact Admin to activate your account.");
								} elseif(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false) {
									Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please {{login}}.",array('{{login}}'=>CHtml::link(UserModule::t('Login'),Yii::app()->controller->module->loginUrl))));
                                    $flashRegistration = UserModule::t("Thank you for your registration. Please {{login}}.",array('{{login}}'=>CHtml::link(UserModule::t('Login'),Yii::app()->controller->module->loginUrl)));
								} elseif(Yii::app()->controller->module->loginNotActiv) {
									Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email or login."));
                                    $flashRegistration = UserModule::t("Thank you for your registration. Please check your email or login.");
								} else {
									Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email."));
                                    $flashRegistration = UserModule::t("Thank you for your registration. Please check your email.");
									Yii::app()->user->setFlash('content',"This email contains account activation link. After clicking the link registration will be finished and your account activated.");
                                    $flashContent = "This email contains account activation link. After clicking the link registration will be finished and your account activated.";

                                }
                                Yii::app()->request->cookies['flashRegistration'] = new CHttpCookie('flashRegistration', $flashRegistration);
                                if (!empty($flashContent))
                                    Yii::app()->request->cookies['flashContent'] = new CHttpCookie('flashContent', $flashContent);

                                $this->refresh();
							}
						}
					} else $profile->validate();
				}
			    $this->render('/user/registration',array('model'=>$model,'profile'=>$profile));
		    }
	}
}