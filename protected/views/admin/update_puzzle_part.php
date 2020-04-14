<? //Редактирование пазла. Главная страница ?>

<h1>Edit Puzzle</h1>

<?$jsEditPuzzleFile = $this->renderPartial('js_form_edit_puzzle', array(
    'model' => $model,
));
echo $this->renderPartial('_form_edit_puzzle_wo_js', array(
    'model' => $model,
    'attr'  => $attr,
    'albumName' => $albumName,
    'jsContent' => $jsEditPuzzleFile,
    'listCutout' => (null != $listCutout) ? $listCutout : array(),
));?>