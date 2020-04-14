<?$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Login");
$this->breadcrumbs=array(UserModule::t("Login"),);?>

<!--<h1><?//UserModule::t("Login"); ?></h1>-->

<?if(Yii::app()->user->hasFlash('loginMessage')):?>
    <div class="success">
        <?=Yii::app()->user->getFlash('loginMessage')?>
    </div>
<?endif?>

<!--<p><?//=UserModule::t("Please fill out the following form with your login credentials:")?></p>-->

<div class="form">
<?php echo CHtml::beginForm(); ?>

	<!--<p class="note"><?//=UserModule::t('Fields with <span class="required">*</span> are required.')?></p>-->
	
	<?//=CHtml::errorSummary($model)?>
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
                        <h2> Login to your account </h2>
                    </div>
                    <div>
                        <h4>Username</h4>
                        <?=CHtml::activeTextField($model,'username',array(
                            'id' => 'giFormUsername',
                            'style' => 'background-repeat:no-repeat;padding-left:17px;',
                        ))?>
                    </div>
                    <div>
                        <h4>Password</h4>
                        <?=CHtml::activePasswordField($model,'password',array(
                            'id' => 'giFormPassword',
                            'style' => 'background-repeat:no-repeat;padding-left:17px;',
                        ))?>
                    </div>
                    <?if(Yii::app()->user->hasFlash('loginError')):?>
                        <div class="errorMessage">
                            <?=Yii::app()->user->getFlash('loginError')?>
                        </div>
                    <?endif?>
                </div>
                <div class="gbBlock">
                    Lost or forgotten passwords can be retrieved using the
                    <a href="/user/recovery">recover password</a>
                    page
                </div>
                <div class="gbBlock gcBackground1">
                    <?=CHtml::submitButton(UserModule::t("Login"),array('class'=>'inputTypeSubmit'))?>
                </div>
            </div>
        </td
    </tr>
    </tbody></table>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->


<?php
$form = new CForm(array(
    'elements'=>array(
        'username'=>array(
            'type'=>'text',
            'maxlength'=>32,
        ),
        'password'=>array(
            'type'=>'password',
            'maxlength'=>32,
        ),
        'rememberMe'=>array(
            'type'=>'checkbox',
        )
    ),

    'buttons'=>array(
        'login'=>array(
            'type'=>'submit',
            'label'=>'Login',
        ),
    ),
), $model);
?>