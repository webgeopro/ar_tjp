<?$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Profile");
$this->breadcrumbs=array(
    //Yii::app()->user->name => array('edit'),
    $breadcrumbs[0] => $breadcrumbs[1],
    'Change password',
);?>

<h2><?#=UserModule::t("Change password"); ?></h2>
<?//$this->renderPartial('menu'); ?>

<div class="form">
<?php $form=$this->beginWidget('UActiveForm', array(
	'id'=>'changepassword-form',
	'enableAjaxValidation'=>true,
)); ?>

    <?#=CHtml::errorSummary($model)?>
    <table width="100%" cellspacing="0" cellpadding="0"><tbody>
    <tr valign="top">
        <td id="gsSidebarCol">
            <div class="gcBorder1" id="gsSidebar">
                <div class="gbBlock">
                    <h2> User Options </h2>
                    <ul>
                        <li class="gbAdminLink gbLink-core_UserPreferences">
                            <a href="/user/profile/edit">
                                Account Settings
                            </a>
                        </li>
                        <li class="gbAdminLink gbLink-core_UserChangePassword">
                            Change Password
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
                <div class="gbBlock">
                    <div class="row">
                        <h4> New Password </h4>
                        <?=$form->passwordField($model,'password')?>
                        <?=$form->error($model,'password')?>
                        <p class="hint">
                            <?php echo UserModule::t("Minimal password length 4 symbols."); ?>
                        </p>
                    </div>

                    <div class="row">
                        <h4> Verify New Password </h4>
                        <?=$form->passwordField($model,'verifyPassword')?>
                        <?=$form->error($model,'verifyPassword')?>
                    </div>

                </div>
                <div class="gbBlock gcBackground1">
                    <?=CHtml::submitButton('Save', array('class'=>'inputTypeSubmit'))?>
                </div>
            </div>
        </td>
    </tr>
    </tbody></table>

<?php $this->endWidget(); ?>
</div><!-- form -->