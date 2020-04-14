<?php
$this->pageTitle = Yii::app()->name . ' - ' . UserModule::t("Registration");
$this->breadcrumbs = array(UserModule::t("Registration"))?>

<?if (!empty(Yii::app()->request->cookies['flashRegistration']->value)):?>
    <?if (!empty(Yii::app()->request->cookies['flashContent']->value)):?>
        <div class="gbBlock gcBackground1">
            <h2> User registration </h2>
        </div>
        <h1 style="margin:0px;">
            <img src="/images/email_sent.png" alt="Email has been sent" title="Email has been sent" width="64" height="46" style="float:left;margin-right:10px;">
            An email has been sent to you
        </h1>
        <p class="giDescription">
            This email contains account activation link. After clicking the link registration will be finished and your account activated.
        </p>
        <?unset(Yii::app()->request->cookies['flashContent']); // Обнуляем flash-сообщение?>
    <?else:?>
        <div class="success">
            <h1><?=Yii::app()->request->cookies['flashRegistration']->value?></h1>
        </div>
        <br /><br /><br />
    <?endif?>
    <?unset(Yii::app()->request->cookies['flashRegistration']); // Обнуляем flash-сообщение?>
<?else:?>

<div class="form" style="margin-top:10px;">
<?php $form=$this->beginWidget('UActiveForm', array(
	'id'=>'registration-form',
	'enableAjaxValidation'=>true,
	'disableAjaxValidationAttributes'=>array('RegistrationForm_verifyCode'),
	'htmlOptions' => array('enctype'=>'multipart/form-data'),
)); ?>

	<!--<p class="note">
	    <?#=UserModule::t('Fields with <span class="required">*</span> are required.'); ?>
	    </p>-->
	
	<?#=$form->errorSummary(array($model,$profile)); ?>

    <table width="100%" cellspacing="0" cellpadding="0"><tbody>
    <tr valign="top">
        <td id="gsSidebarCol">
            <div class="gcBorder1" id="gsSidebar">
                <div class="gbBlock">
                    <h2> User Options </h2>
                    <ul>
                        <li class="gbAdminLink gbLink-core_UserLogin">
                            <a href="/user/login">
                                Login
                            </a>
                        </li>
                        <li class="gbAdminLink gbLink-register_UserSelfRegistration">
                            Register
                        </li>
                    </ul>
                </div>
            </div>
        </td>
        <td>
            <div class="gcBorder1" id="gsContent">
                <div class="gbBlock">
                    <div class="gbBlock gcBackground1">
                        <h2> Register As New User </h2>
                    </div>
                    <div>
                        <h4>Username<span class="giSubtitle"> (required) </span></h4>
                        <?=$form->textField($model,'username',array('size'=>32)); ?>
                        <?=$form->error($model,'username'); ?>
                    </div>
                    <div>
                        <h4>Full Name<span class="giSubtitle"> (required) </span></h4>
                        <?=$form->textField($profile,'fullname',array('size'=>32)); ?>
                        <?=$form->error($profile,'fullname'); ?>
                    </div>
                    <div>
                        <h4>Email Address<span class="giSubtitle"> (required) </span></h4>
                        <?=$form->textField($model,'email',array('size'=>32)); ?>
                        <?=$form->error($model,'email'); ?>
                    </div>
                    <div>
                        <h4>Password
                            <span class="giSubtitle">
                                (required)<br />
                                <?=UserModule::t("Minimal password length 4 symbols.")?>
                            </span>
                        </h4>
                        <?=$form->passwordField($model,'password',array('size'=>32)); ?>
                        <?=$form->error($model,'password'); ?>
                    </div>
                    <div>
                        <h4>Verify Password<span class="giSubtitle"> (required) </span></h4>
                        <?=$form->passwordField($model,'verifyPassword',array('size'=>32))?>
                        <?=$form->error($model,'verifyPassword')?>
                    </div>
                </div>
                <div class="gbBlock">
                    <div>
                        <h4 class="label_Country">Country</h4>
                        <?$this->widget('application.extensions.CountrySelectorWidget', array(
                            'value' => $profile->country,
                            'name' => Chtml::activeName($profile, 'country'),
                            'id' => Chtml::activeId($profile, 'country'),
                            'useCountryCode' => true,
                            'firstEmpty' => true,
                        ));?>
                        <?=$form->error($profile,'country'); ?>
                    </div>
                    <div>
                        <h4 class="label_Gender">Gender</h4>
                        <?=CHtml::activeDropDownList($profile, 'gender',
                            array('M' => 'Male', 'F' => 'Female'),
                            array('empty' => '')
                        )?>
                        <?=$form->error($profile,'gender'); ?>
                    </div>
                    <div>
                        <h4 class="label_Birthday">Birthday</h4>
                        <?$this->widget('ext.ActiveDateSelect',array(
                            'model'=>$profile,
                            'attribute'=>'birthday',
                            'reverse_years'=>true,
                            'field_order'=>'DMY',
                            'start_year'=>1902,
                            'end_year'=>date("Y",time())-15, // Не младше 15 лет
                            'year_empty'=> '',
                            'month_empty'=> '',
                            'day_empty'=> '',
                        ))?>
                        <?=$form->error($profile,'birthday'); ?>
                    </div>
                </div>
                <div class="gbBlock gcBackground1">
                    <?=CHtml::submitButton(UserModule::t("Register"), array('class'=>'inputTypeSubmit')); ?>
                </div>
            </div>
        </td>
    </tr>
    </tbody></table>

<?$this->endWidget()?>
</div><!-- form -->
<?endif;?>