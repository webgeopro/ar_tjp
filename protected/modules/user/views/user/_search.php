<!--<div class="wide form" style="float:left;width:400px;">-->

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
    'htmlOptions' => array(
        'name' => 'findForm',
    ),
)); ?>

	<!--<div class="row">
		<?#$form->label($model,'id'); ?>
		<?#$form->textField($model,'id'); ?>
	</div>-->

	<div class="row buttons">
		<?#=$form->label($model,'username')?>
		<?#=$form->textField($model,'username',array('size'=>50,'maxlength'=>50))?>

        <?$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'model'=>$model,
            'attribute'=>'username',
            'source' =>Yii::app()->createUrl('/user/user/autocomplete'),
            'options'=>array(
                'minLength'=>'2',
                'select' =>'js: function(event, ui) {
                    //this.value = ui.item.label;
                    location.href = "/profile/admin/";
                    document.findForm.submit();
                    // Переправлем на страницу редактирования профиля
                    return false;
                }',
            ),
            'htmlOptions'=>array(
                'style'=>'width:300px;float:left;'
            ),
        ));?>

        <?//=$form->hiddenField($model,'username', array('style'=>'display: none;'))?>

        <?=CHtml::submitButton('Find')?>
        <?#=CHtml::resetButton('Clear')?><!-- Доработать ояищение адресной строки и поля ввода -->
	</div>

	<div class="row buttons">

	</div>

<?php $this->endWidget(); ?>

<!--</div>--><!-- search-form -->