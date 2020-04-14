<?php
$this->breadcrumbs=array(
    empty($this->album->componentUrl)?'Admin Options':$this->album->componentUrl,
    'Add Album',
);?>
<table style="width:100%;">
    <tr>
        <td style="width:250px;background-color:#ffffee;vertical-align:top;">
            <?$this->widget('menuAdmin')?>
        </td>
        <td style="vertical-align:top;">
            <?$form=$this->beginWidget('CActiveForm', array(
                'id'=>'album-form',
                'enableAjaxValidation'=>true,
                'enableClientValidation'=>true,
            ));
            echo CHtml::hiddenField('Album[parent_id]', '0');?>
            <div id="divAdminContent">
                <div class="gcBorder1" id="gsContent">
                    <div class="gbBlock gcBackground1">
                        <h2> Add Album </h2>
                    </div>
                    <div class="gbBlock">
                        <h4>Component URL <span class="giSubtitle">(required)</span></h4>
                        <p class="giDescription">
                            The name of this album on your hard disk.  It must be unique in this album.
                            Only use alphanumeric characters, underscores or dashes.  You will be able to rename it later.
                        </p>
                        <!--/Street-View/-->
                        <div class="row">
                            <?=$form->textField($model,'componentUrl', array('size'=>'30'));?>
                            <?=$form->error($model,'componentUrl'); ?>
                        </div>
                        <h4> Title </h4>
                        <p class="giDescription">
                            This is the album title.
                        </p>
                        <div class="row">
                            <?=$form->textField($model,'title', array('size'=>'40'));?>
                            <?=$form->error($model,'title'); ?>
                        </div>
                        <h4> Keywords </h4>
                        <p class="giDescription">
                            Keywords are not visible, but are searchable.
                        </p>
                        <div class="row">
                            <?=$form->textArea($model,'keywords', array('cols'=>'60','rows'=>'2'));?>
                            <?=$form->error($model,'keywords'); ?>
                        </div>
                        <?=$form->errorSummary($model)?>
                    </div>

                    <?if(Yii::app()->user->hasFlash('result')):?>
                        <div class="flash-success">
                            <?=Yii::app()->user->getFlash('result');?>
                        </div>
                    <?endif?>
                    <div class="gbBlock gcBackground1">
                        <input type="submit" value="Create" name="Album[action][create]" class="inputTypeSubmit">
                    </div>
                </div>

            </div>
            <?$this->endWidget()?>
        </td>
    </tr>
</table>