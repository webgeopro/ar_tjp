<?$boolNewPuzzle = CItemUtils::isNewPuzzle($model);?>
<script language="JavaScript">
    $(document).ready(function(){
        $("#inpTitleField").live('keyup', function(event){
            <?if($boolNewPuzzle)
                echo '$("#inpUrlField").val(clearTitle(this.value));';?>
        });
    });
</script>
<div class="form">
<?//=CHtml::beginForm('', null, array('name'=>'editPuzzle', 'class'=>'formAdmin', 'style'=>'padding-top:20px;'))?>
<?$form=$this->beginWidget('CActiveForm', array(
    'id'=>'editPuzzle',
    'enableAjaxValidation'=>false,
))?>

    <?=$form->hiddenField($model,'id')?>
    <?=$form->hiddenField($attr,'id')?>
    <?=$form->hiddenField($model,'inSearch', array('value'=>'1'))?>

    <div class="divError"><?=$form->errorSummary($model); ?></div>

    <h2>Component Url</h2>
    <div class="row">
        <?=$albumName?><br />
        <?=$form->textField($model,'componentUrl', array('class' => 'formInput', 'id'=>'inpUrlField')); ?>
        <?=$form->error($model,'componentUrl'); ?>
    </div>

    <h2>Title</h2>
    <div class="row">
        <?=$form->textField($model,'title', array('class' => 'formInput', 'id'=>'inpTitleField')); ?>
        <?=$form->error($model,'title'); ?>
    </div>
    <h2>Keywords (<span id="spKeywords"><?=(255-strlen($attr->keywords))?></span>)</h2>
    <div class="row">
        <?=$form->textArea($attr,'keywords', array(
            'class' => 'formInput','cols' => 60, 'rows' => 2, 'id' => 'areaKeywords'
        ))?>
        <?=$form->error($attr,'keywords'); ?>
    </div>

    <h2>Description</h2>
    <?=$form->textArea($attr,'description', array(
        'class' => 'formInput','cols' => 60, 'rows' => 4,
    ))?>
    <?=$form->error($attr,'description'); ?>

    <br /><hr /><br />

    <h2>Photo taken</h2>
    <?$this->widget('ext.ActiveDateSelect',array(
        'model'=>$attr,
        'attribute'=>'dateImageCreated',
        'reverse_years'=>true,
        'field_order'=>'MDY',
        'start_year'=>1970,
        'end_year'=>date("Y",time())+1,
        'year_empty'=> '',
        'month_empty'=> '',
        'day_empty'=> '',
    ))?>
    <?if (Yii::app()->user->hasFlash('fromExif')):
        echo '<span style="color: #006600">From EXIF</span>';
    endif?>
    <?/*if (!empty(Yii::app()->request->cookies['fromExif']->value)):
        echo '<span style="color: #006600">'.Yii::app()->request->cookies['fromExif']->value.'</span>';
        unset(Yii::app()->request->cookies['fromExif']); // Обнуляем flash-сообщение
    endif;*/?>
    <br />

    <?=$jsContent?>

    <br />
    <h2>Puzzle Date and Time</h2>
    <?=CHtml::checkBox('chScheduled', null, array('id'=>'chScheduled'))?>
    Scheduled
    <br />

    <?$dateCreated=new DateTime($model->dateCreated);$dateCreated=$dateCreated->format('F d Y');?>
    <?=$this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'name'    => 'Item[dateCreated]',
        'value'   => $dateCreated,
        'language'=> 'en',
        'options' => array(
            'showAnim'=>'fold',
            'dateFormat'=>'MM dd yy',
            'changeMonth' => 'true',
            'changeYear'=>'true',
            'showButtonPanel' => 'false',
        ),
        'htmlOptions'=>array(
            'style'=>'width:150px;height:20px;',
        ),
    ), true)?>
    <span>
        <?=substr($model->dateCreated, -8)?> (Hours : minutes : seconds)
    </span>
    <br /><hr /><br />

    <h2>Default cut</h2>
    <?=CHtml::dropDownList('Item[cutout]', $model->cutout, $listCutout)?>
    <br /><br />
    <h2>Author</h2>
    <?=$form->textField($attr, 'author', array('class' => 'formInput', 'id'=>'inpAuthorField')); ?>
    <?=$form->error($attr,'author'); ?>

    <br /><hr /><br />

    <h2>Static Blocks</h2>
    <?=CHtml::checkBox('staticBlock')?>Render static blocks<br />
    <?=CHtml::checkBox('staticNav')?>Render static micro navigation

    <br /><hr /><br />

    <div class="row buttons">
        <?=CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save')?>
    </div>

    <?//=CHtml::ajaxSubmitButton('Save', '/admin/editPuzzle')?><?//=CHtml::endForm()?>

<?$this->endWidget()?>
</div><!-- form -->