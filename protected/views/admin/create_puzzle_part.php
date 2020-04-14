<? //Создание пазла. Главная страница ?>

<h1>Create Puzzle</h1>

<?=$this->renderPartial('_form_edit_puzzle', array(
    'model' => $model,
    'attr'  => $attr,
    'albumName' => $albumName,
    'listCutout' => (null != $listCutout) ? $listCutout : array(),
))?>