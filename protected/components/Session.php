<?php
/**
 * Created by WebSee.
 * User: SergeyTigrov
 * Date: 30.05.12
 * Time: 11:45
 */
 
class Session extends CDbHttpSession {
    private $_isCookieHasSession;

    /**
     * Определяет использовались ли ранее Session
     * @return bool результат true, если Session ранее испоьзовались, иначе false
     */
    public function getIsUsed() {
        return $this->getIsStarted() || $this->getIsCookieHasSession();
    }

    /**
     * Проверяем наличие идентификатора Session в Cookie
     * @return bool результат true, если идентификатор Session уже хранится в Cookie, иначе false
     */
    public function getIsCookieHasSession() {
        if ($this->_isCookieHasSession === null) {
            $cookies = Yii::app()->getRequest()->getCookies();
            $this->_isCookieHasSession = $cookies[$this->getSessionName()] !== null;
        }

        return $this->_isCookieHasSession;
    }

    /**
     * Открываем Session с проверкой не открыта ли она уже
     */
    public function open() {
        if (!$this->getIsStarted()) {
            parent::open();
        }
    }

    /**
     * Дополнительно к родительскому методу destroy() добавляем удаление идентификатора Session из Cookie
     */
    public function destroy() {
        parent::destroy();

        $cookieParams = $this->getCookieParams();
        setcookie($this->getSessionName(), '', 0, $cookieParams['path'], $cookieParams['domain']);
    }
}