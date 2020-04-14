<?php
/**
 * Виджет Для отображения шаблона (создан для рендеринга шаблона в шаблоне)
 * @todo: Придумать элегантное решение
 */

class potdItem extends CWidget {

    public $item; // Объект пазл
    public $i;    // Счетчик для image ID
    public $actions = array(); // Список возможных действий пользователя
    public $size; // Размер миниатюры
    public $path; // Путь к изображению

    public function run() {
        if (!empty($this->item['cut']['name']))
            $cutout = $this->item['cut']['name'];
        elseif (!empty($this->item['cutout']))
            $cutout = $this->item['cutout'];
        else
            $cutout = Yii::app()->params['defaultCutout'];

        $this->render('potdItem', array(
            'item'    => $this->item,
            'i'       => $this->i,
            'actions' => $this->actions,
            'size' => empty($this->size) ? Yii::app()->params['thumbnailSize'][0]: $this->size,
            'path' => empty($this->path) ? Yii::app()->params['pathThumbnail']   : $this->path,
            'cutout'  => $cutout,
        ));
	}
}