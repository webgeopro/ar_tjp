<?php
/**
 * Created by WebSee.
 * User: SergeyTigrov
 * Date: 30.05.12
 * Time: 11:42
 */

class WebUser extends CWebUser {

    // Вместо хранения returnUrl в Session, будем хранить её в объекте Yii::app()->user
    // при необходимости можно передавать нужное значение при регистрации или входе в систему через POST параметр
    private $_returnUrl;

    /**
     * Инициализация компонента.
     * Session стартуются, только если ранее они уже были установлены.
     */
    public function init() {
        // Пропускаем инициализацию у родительского класа и делаем иницализацию у прородительского класса
        $parent = get_parent_class(get_parent_class(__CLASS__));
        $parent::init();

        $session = Yii::app()->getSession();
        if ($session->isUsed) {
            $session->open();
        }

        if($this->getIsGuest() && $this->allowAutoLogin) {
            $this->restoreFromCookie();
        } else if($this->autoRenewCookie && $this->allowAutoLogin) {
            $this->renewCookie();
        }
        if($session->getIsStarted() && $this->autoUpdateFlash) {
            $this->updateFlash();
        }

        $this->updateAuthStatus();
    }

    public function getIsGuest() {
        return Yii::app()->getSession()->getIsStarted()
                ? parent::getIsGuest()
                : true;
    }

    public function getState($key,$defaultValue=null) {
        return Yii::app()->getSession()->getIsStarted()
                ? parent::getState($key,$defaultValue)
                : $defaultValue;
    }

    public function setState($key,$value,$defaultValue=null) {
        Yii::app()->getSession()->open();
        return parent::setState($key,$value,$defaultValue);
    }

    public function hasState($key) {
        return Yii::app()->getSession()->getIsStarted()
                ? parent::hasState($key)
                : false;
    }

    public function clearStates() {
        return Yii::app()->getSession()->getIsStarted()
                ? parent::clearStates()
                : null;
    }

    public function getFlashes($delete=true) {
        return Yii::app()->getSession()->getIsStarted()
                ? parent::getFlashes($delete)
                : array();
    }

    public function getFlash($key,$defaultValue=null,$delete=true) {
        return Yii::app()->getSession()->getIsStarted()
                ? parent::getFlash($key,$defaultValue,$delete)
                : $defaultValue;
    }

    public function setFlash($key,$value,$defaultValue=null) {
        Yii::app()->getSession()->open();
        return parent::setFlash($key,$value,$defaultValue);
    }

    public function hasFlash($key) {
        return Yii::app()->getSession()->getIsStarted()
                ? parent::hasFlash($key)
                : false;
    }

    public function getReturnUrl($defaultUrl=null) {
        return $this->_returnUrl ? $this->_returnUrl : $defaultUrl;
        //return Yii::app()->getSession()->getIsStarted()
        //        ? parent::getReturnUrl($defaultUrl)
        //        : $defaultUrl;
    }

    public function setReturnUrl($value) {
        $this->_returnUrl = $value;
        // Если нужно хранить returnUrl в Session, тогда нужно удалить из конфигурации (main.php) значение по умолчанию, иначе оно присваевается при каждой инициализации объекта класса CWebUser
        //Yii::app()->getSession()->open();
        //return parent::setReturnUrl($value);
    }

    public function beforeLogin($id, $states, $fromCookie) {
        // Открываем Session, если пользователь успешно прошёл идентификацию
        // (Функция beforeLogin вызывается после успешной идентификации)
        Yii::app()->getSession()->open(); //die("Session start");
        return parent::beforeLogin($id, $states, $fromCookie);
    }
}
