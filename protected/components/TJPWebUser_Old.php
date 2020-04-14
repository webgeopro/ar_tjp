<?php
/**
 * Изменение класса авторизации для управления стартом сессии вручную.
 * User: Vah
 * Date: 27.06.13
 * Time: 10:44
 */

class TJPWebUser extends CWebUser
{
    public function init()
    {
        if (!isset($_COOKIE['login'])) {
            CApplicationComponent::init();
            if($this->getIsGuest() && $this->allowAutoLogin)
                $this->restoreFromCookie();
            else if($this->autoRenewCookie && $this->allowAutoLogin)
                $this->renewCookie();
            $this->updateFlash();
        } else {
            parent::init();
        }
    }

    public function login($identity,$duration=0)
    {
        $cookie=new CHttpCookie('login','1');
        Yii::app()->request->getCookies()->add('login', $cookie);
        Yii::app()->getSession()->open();
        parent::login($identity,$duration=0);
    }

    public function logout($destroySession=true)
    {
        Yii::app()->request->getCookies()->remove('login');
        Yii::app()->getSession()->destroy();
        parent::logout(true); // $destroySession
    }

    public function getIsGuest()
    {
        if (!isset($_COOKIE['login'])) {
            return true;
        }
        return parent::getIsGuest();
    }


}