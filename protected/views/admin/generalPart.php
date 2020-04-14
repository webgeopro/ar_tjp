<? //Редактирование пазла. Главная страница ?>

<?=CHtml::beginForm('', null, array('name'=>'editPuzzle', 'class'=>'formAdmin', 'style'=>'padding-top:20px;'))?>
    <?=CHtml::hiddenField('editPuzzle[hid]', 'val')?>

    <h2>Component Url</h2>
    <?=$albumName?><?=CHtml::textField('editPuzzle[componentUrl]', $this->item['componentUrl'],
    array('class' => 'formInput',))?>

    <h2>Title</h2>
    <?=CHtml::textField('editPuzzle[title]', $this->item['title'], array(
        'class' => 'formInput',
    ))?>

    <h2>Keywords</h2>
    <?=CHtml::textArea('editAttr[keywords]', $this->item['attr']['keywords'], array(
        'cols' => 60, 'rows' => 2,
    ))?>
    <h2>Description</h2>
    <?=CHtml::textArea('editAttr[description]', $this->item['attr']['description'], array(
        'cols' => 60, 'rows' => 4,
    ))?>
    <hr />
    <h2>Puzzle Date and Time</h2>
    <?$this->widget('ext.ActiveDateSelect',array(
        'model'=>$this->item,
        'attribute'=>'dateCreated',
        'reverse_years'=>true,
        'field_order'=>'DMY',
        'start_year'=>2005,
        'end_year'=>date("Y",time())+1, // Не младше 15 лет
        'year_empty'=> '',
        'month_empty'=> '',
        'day_empty'=> '',
    ))?>
    <hr />
    <h2>Schedule Puzzle Publication</h2>
    <?$this->widget('ext.ActiveDateSelect',array(
        'model'=>$this->item->attr,
        'attribute'=>'datePublished',
        'reverse_years'=>true,
        'field_order'=>'DMY',
        'start_year'=>2005,
        'end_year'=>date("Y",time())+1, // Не младше 15 лет
        'year_empty'=> '',
        'month_empty'=> '',
        'day_empty'=> '',
    ))?>
    <hr />
    <h2>Static Blocks</h2>
        <?=CHtml::checkBox('staticBlock')?>Render static blocks<br />
        <?=CHtml::checkBox('staticNav')?>Render static micro navigation
    <hr />

    <?=CHtml::ajaxSubmitButton('Save', '/admin/editPuzzle')?>
<?=CHtml::endForm()?>