<?php
/**
 * Date: 04.02.13 Time: 11:15
 * Работа с сохраненным пазлом
 */
class savedPuzzle extends CWidget
{
    public $item; // Объект пазл (модель Item)
    public $page; // Имя страницы, вызвавшей виджет

    public function run()
    {
        $itemID = explode(':&', @$_COOKIE['savedPuzzle']); // Получаем данные куки
        $itemID = $itemID[0]; // Первая часть содержит Id записи

        // Отображаем виджет только при существовании пазла
        if (!empty($itemID) AND Item::model()->exists('id=:itemID', array(':itemID'=>$itemID)) ) {
            $this->item = Item::model()->findByPk($itemID);
            if (empty($this->item->imgUrl))
                list($this->item->imgFullName, $this->item->imgUrl,
                     $this->item->width, $this->item->height) =
                        CImageSize::getSizeById($itemID, null, null, true);
            else
                list($this->item->width, $this->item->height) =
                    CImageSize::getSizeById($itemID);


        }
        
        $this->render('savedPuzzle');
    }
}
