<?php $this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Restore");
$this->breadcrumbs=array(
	UserModule::t("Login") => array('/user/login'),
	UserModule::t("Restore"),
);
?>

    <div class="success">
        <h1><?=UserModule::t("Please check your email. An instructions was sent to your email address.")?></h1>
    </div>
    <br /><br /><br />
