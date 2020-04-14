<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'album-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'parent_id'); ?>
		<?php echo $form->textField($model,'parent_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'parent_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'owner_id'); ?>
		<?php echo $form->textField($model,'owner_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'owner_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'thumbnail_id'); ?>
		<?php echo $form->textField($model,'thumbnail_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'thumbnail_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'componentUrl'); ?>
		<?php echo $form->textField($model,'componentUrl',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'componentUrl'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'keywords'); ?>
		<?php echo $form->textField($model,'keywords',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'keywords'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textField($model,'description',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->