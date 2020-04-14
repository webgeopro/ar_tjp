<?php
$this->pageTitle = Yii::app()->name . ' - Feedback';
$this->breadcrumbs = array(
    'Feedback',
);?>

<table cellspacing="0" cellpadding="0" width="100%" class="gcBackground1">
    <tbody>
    <tr valign="top">
        <td width="20%">
            <table cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td style="padding-bottom:5px" colspan="2">
                        <div class="gsContentDetail">
                            <div class="gbBlock gcBorder1">
                                <div class="block-imageblock-ImageBlock">
                                    <div class="one-image">
                                        <div class="giThumbnailContainer">
                                            <?$this->widget('defaultImage')?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="gbBlock gcBorder1">
                                <!--<h2> Puzzle Gallery </h2>-->
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <td><?
            if (Yii::app()->user->hasFlash('feedback')):
                echo Yii::app()->user->getFlash('feedback');
            else:
                $form=$this->beginWidget('CActiveForm', array(
                    'action'=>'',
                    'id'=>'contact-form',
                    'enableClientValidation'=>true,
                    'clientOptions'=>array(
                        'validateOnSubmit'=>true,
                    ),
                ));
            ?>
                <table cellspacing="5" style="margin-top:5px;">
                    <tbody>
                    <tr>
                        <td align="right" valign="top"><img height="44" width="48"
                                                            src="/images/feedback_48.png"></td>
                        <td>
                            <div style="float:right; width: 300px; margin-left: 30px;">
                                <a href="/info/help"><img height="48" width="35" style="float: left; margin-right: 5px;"
                                                     src="/images/help_icon_big.png"></a>
                                <h2 class="gbTitle">Need help with puzzles?</h2>
                                <p>You may try our <a href="/info/help"><strong>Help page</strong></a> first - it has many
                                    questions answered, problems solved and cool features explained.</p>
                            </div>
                            <div style="width: 300px;">
                                <h2 class="gbTitle">Feedback</h2>
                                <p>Please enter your message and contact information - we'll get back to you as soon as
                                    possible.</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Name:</strong></td>
                        <td><?=$form->textField($model, 'name', array('maxlength'=>80, 'size'=>80));?>
                            <?=$form->error($model,'name'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Email Address:</strong></td>
                        <td>
                            <?=$form->textField($model,'email',array('maxlength'=>80, 'size'=>80)); ?>
                            <?=$form->error($model,'email'); ?>
                        </td>
                    </tr>
                    <!--<tr>
                        <td align="right"><strong>Subject:</strong></td>
                        <td>
                            <input type="text" value="" name="g2_form[custSubject]" maxlength="80" size="80">
                        </td>
                    </tr>-->
                    <tr>
                        <td align="right"><strong>Message:</strong></td>
                        <td>
                            <!--<textarea name="g2_form[custMessage]" style="width:100%" rows="10"></textarea>-->
                            <?=$form->textArea($model,'body',array('rows'=>10, 'style'=>'width:100%'));?>
                            <?=$form->error($model,'body');?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"></td>
                        <td style="padding-top:5px; padding-bottom: 5px;">
                            <?=CHtml::submitButton('Send Feedback', array('class'=>'inputTypeSubmit'));?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            <?$this->endWidget();?>
            <?endif?>
        </td>
    </tr>
    </tbody>
</table>