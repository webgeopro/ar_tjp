<? //Создание пазла. Главная страница ?>
Создание файла. Индексная страница.
<?=CHtml::beginForm('', null, array('name'=>'editPuzzle', 'class'=>'formAdmin'))?>
    <?=CHtml::hiddenField('editPuzzle[hid]', 'val')?>
    <h2>Name</h2>
    <?=$albumName?><?=CHtml::textField('editPuzzle[hid]', 'val')?>
    <h2>Title</h2>
    <h2>Keywords</h2>
    <h2>Description</h2>
    <hr />
    <h2>Puzzle Date and Time</h2>
    <hr />
    <h2>Schedule Puzzle Publication</h2>
    <hr />
    <h2>Static Blocks</h2>
    <hr />

    <?=CHtml::ajaxSubmitButton('Save', '/admin/editPuzzle')?>
<?=CHtml::endForm()?>