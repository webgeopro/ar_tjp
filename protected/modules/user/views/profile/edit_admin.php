<?$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Profile");
    if (Yii::app()->getModule('user')->isAdmin())
        $this->breadcrumbs=array('Пользователи'=>'/admin/user', $model['username'], 'Edit profile');
    else
        $this->breadcrumbs=array($model['username']=>array('profile'), 'Edit profile');
?>
<!--<h2><?=UserModule::t('Edit profile')?></h2>-->

<div class="form">
    <?$form=$this->beginWidget('UActiveForm', array(
        'id'=>'profile-form',
        'enableAjaxValidation'=>true,
        'htmlOptions' => array('enctype'=>'multipart/form-data'),
    ));?>

<table width="100%" cellspacing="0" cellpadding="0"><tbody>
    <tr valign="top">
        <td id="gsSidebarCol">
            <div class="gcBorder1" id="gsSidebar">
                <div class="gbBlock">
                    <h2> User Options </h2>
                    <ul>
                        <li class="gbAdminLink gbLink-core_UserPreferences">
                            Account Settings
                        </li>
                        <li class="gbAdminLink gbLink-core_UserChangePassword">
                            <a href="/user/profile/changepassword">
                                Change Password
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </td>
        <td>
            <div class="gcBorder1" id="gsContent">
                <div class="gbBlock gcBackground1">
                    <h2> Account Settings </h2>
                </div>
                <?if(Yii::app()->user->hasFlash('profileMessage')):?>
                    <div class="success">
                        <?=Yii::app()->user->getFlash('profileMessage')?>
                    </div>
                <?endif?>


                <div class="gbBlock">
                    <div>
                        <h4> Username </h4>
                        <p class="giDescription">
                            <?#=$model['username']?>
                            <?=$form->textField($model, 'username')?>
                        </p>
                    </div>
                    <div>
                        <h4> Full Name </h4>
                        <?=$form->textField($profile, 'fullname')?>
                    </div>
                    <div>
                        <h4> Email Address </h4>
                        <?=$form->textField($model, 'email')?>
                    </div>
                    <div>
                        <h4> New Password </h4>
                        <?=$form->textField($model,'password')?>
                        <?=$form->error($model,'password')?>
                        <p class="hint">
                            <?=UserModule::t("Minimal password length 4 symbols.")?>
                        </p>
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
                        'firstText' => '',
                    ));?>
                </div>
                <div>
                    <h4 class="label_Gender">Gender</h4>
                    <?=CHtml::activeDropDownList($profile, 'gender',
                        array('M' => 'Male', 'F' => 'Female'),
                        array('empty' => ''))
                    ?>
                </div>
                <div>
                    <h4 class="label_Birthday">Birthday</h4>
                    <input type="hidden" value="Birthday" name="g2_form[customFields][2][field]">
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
                </div>
                <?=$form->errorSummary(array($model, $profile))?>
            </div>
            <div class="gbBlock gcBackground1">
                <?=CHtml::submitButton($model->isNewRecord?'Create':'Save', array('class'=>'inputTypeSubmit'))?>
                <?=CHtml::resetButton('Reset', array('class'=>'inputTypeSubmit'))?>
                <?=CHtml::button('Cancel', array('class'=>'inputTypeSubmit', 'onclick'=>'history.back()'))?>
            </div>
            </div>
        </td>
    </tr>
</tbody></table>

<?php $this->endWidget(); ?>

</div><!-- form -->
