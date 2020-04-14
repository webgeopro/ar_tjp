<?php

class ActivationController extends Controller
{
	public $defaultAction = 'activation';

	/**
	 * Activation user account
	 */
	public function actionActivation () {
		$email = $_GET['email'];
		$activkey = $_GET['activkey'];
		if ($email&&$activkey) {
			$find = User::model()->notsafe()->with('profile')->findByAttributes(array(
                'email'    => $email,
                'activkey' => $activkey
            ));
			if (isset($find)&&$find->status) {
			    $this->render('/user/message_ext',array(
                    'header' => 'User activation',
                    'title'  => 'Your registration was successful and your account has been activated.',
                    'content'=> 'You can now <a href="/user/login">login</a> to your account with your username and password.',
                ));
			} elseif(isset($find->activkey) && ($find->activkey==$activkey)) {
				$find->activkey = UserModule::encrypting(microtime());
				$find->status = 1;
				$find->save();
			    $this->render('/user/message_ext',array(
                    'header' => 'User activation',
                    'title'=>'Your registration was successful and your account has been activated.',
                    'content'=>'You can now <a href="/user/login">login</a> to your account with your username and password.',
                ));
			} else {
			    $this->render('/user/message_ext',array(
                    'title'=>UserModule::t("User activation"),
                    'content'=>UserModule::t("Incorrect activation URL.")
                ));
			}
		} else {
			$this->render('/user/message_ext',array(
                'title'=>UserModule::t("User activation"),
                'content'=>UserModule::t("Incorrect activation URL.")
            ));
		}
	}

}