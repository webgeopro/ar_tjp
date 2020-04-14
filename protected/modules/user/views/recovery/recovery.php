<?php $this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Restore");
$this->breadcrumbs=array(
	UserModule::t("Login") => array('/user/login'),
	UserModule::t("Restore"),
);
?>

<!--<h1><?//=UserModule::t("Restore")?></h1>-->

<?if(Yii::app()->user->hasFlash('recoveryMessage')):?>
    <div class="success">
        <h1><?=Yii::app()->user->getFlash('recoveryMessage')?></h1>
    </div>
    <br /><br /><br />
<?else:?>

<div class="form">
<?=CHtml::beginForm(); ?>

    <table width="100%" cellspacing="0" cellpadding="0"><tbody>
    <tr valign="top">
        <td id="gsSidebarCol">
            <div class="gcBorder1" id="gsSidebar">
                <div class="gbBlock">
                    <h2> User Options </h2>
                    <ul>
                        <li class="gbAdminLink gbLink-core_UserLogin">
                            Login
                        </li>
                        <li class="gbAdminLink gbLink-register_UserSelfRegistration">
                            <a href="/user/registration">
                                Register
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </td>
        <td>
            <div class="gcBorder1" id="gsContent">
                <div class="gbBlock">
                    <div class="gbBlock gcBackground1">
                        <h2> Recover a lost or forgotten password </h2>
                    </div>
                    <div class="gbBlock">
                        Recovering your password requires that your user account has an email address assigned,
                        and that you have access to the listed email address. A confirmation will be emailed to
                        you containing a URL which you must visit to set a new password for your account.
                        To prevent abuse, password recovery requests can not be attempted more than once in a
                        20 minute period. A recovery confirmation is valid for seven days. If it is not used
                        during that time, it will be purged from the system and a new request will have to be made.
                    </div>
                    <div>
                        <h4>Username</h4>
                        <?=CHtml::activeTextField($form,'username',array( //login_or_email
                            'id' => 'giFormUsername',
                            'style' => 'background-repeat:no-repeat;padding-left:17px;',
                        ))?>
                    </div>
                </div>

                <?=CHtml::errorSummary($form)?>

                <div class="gbBlock gcBackground1">
                    <?=CHtml::submitButton(UserModule::t("Recover"),array('class'=>'inputTypeSubmit'))?>
                </div>
            </div>
        </td>
    </tr>
    </tbody></table>

<?=CHtml::endForm()?>
</div><!-- form -->
<?endif?>