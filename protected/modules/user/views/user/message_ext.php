<?#$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Login"); ?>
<div class="gbBlock gcBackground1">
    <h2> <?=$header?> </h2>
</div>

<div class="gbBlock">
    <?if (Yii::app()->user->hasFlash('registration')):?>
        <h1 style="margin:0px;">
            <img src="/images/email_sent.png" alt="Email has been sent" title="Email has been sent" width="64" height="46" style="float:left;margin-right:10px;">
            An email has been sent to you
        </h1>
    <?endif?>

    <h2 class="giTitle">
        <?=@$title?>
    </h2>
    <p class="giDescription">
        <?=@$content?>
    </p>
</div>