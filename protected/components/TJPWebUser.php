<?php
/**
 * Изменение класса авторизации для управления стартом сессии вручную.
 * User: Vah
 * Date: 27.06.13
 */

class TJPWebUser extends CWebUser
{
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

    /**
     * Перегружаем метод для отработки входа пользователней старого движка Gallery2
     */
    protected function restoreFromCookie()
    {
        if (!empty($_COOKIE['GALLERYSID'])) {//die('Isset GALLERYSID :: ' . $_COOKIE['GALLERYSID']);

            $connection = Yii::app()->db2;
            /*$sql = '
                SELECT
                  s.g_id, s.g_userId, u.g_userName
                FROM
                  g2_sessionmap s
                  LEFT JOIN g2_user u
                    ON s.g_userId = u.g_id
                WHERE s.g_id=:sessionID
            ';*///die($sql); // "d90535b076e7ee7981ba832a9be9d50a"
            $sql = 'SELECT g_id, g_userId FROM g2_sessionmap WHERE g_id=:sessionID';

            $command=$connection->createCommand($sql);
            $command->bindParam(":sessionID", $_COOKIE['GALLERYSID'], PDO::PARAM_STR);

            $gUser = $command->queryRow();

            if (null != $gUser['g_userId'] AND User::model()->exists('id=:userID', array(':userID'=>$gUser['g_userId'])) ) {
                $user = User::model()->findByPk($gUser['g_userId']);
                //die(print_r($gUser));
                $identity = new UserIdentity($user->username,$user->password);
                $identity->escAuth = true; // Обход внутренней авторизации
                $identity->authenticate(); // die(print_r($identity));
                $this->login($identity, 3600*24*365); // Вход на сайт (Установка куки на год)

                /*
                Удаление старого куки Gallery2
                setcookie ("GALLERYSID", "", time() - 3600);
                */
            }
        } else {
            parent::restoreFromCookie();
        }
        //die(print_r($_COOKIE));
    }


}