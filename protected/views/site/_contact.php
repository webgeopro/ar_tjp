
== Feedback received from TheJigsawPuzzles.com website ==

From: <?=$model->name?> <?= $model->email ? '('.$model->email.')' : '' ?>

<?if (!Yii::app()->user->isGuest):?>
Username: <?=Yii::app()->user->name?>
<?endif?>


Message:
<?=$model->body?>

