<?php
/**
 * Изображение по умолчанию.
 */

class DefaultImage extends CWidget {

    public function run()
    {
        $this->render('defaultImage', array(
            'itemID' => 36, // ID пазла по умолчанию (kids / динозавры)
            'duration' => Yii::app()->params['dDefaultImage'], // Время кеширования в сек (1 день)
            'name' => 'defaultImage',
        ));
    }
}