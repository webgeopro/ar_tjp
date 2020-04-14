<? //Редактирование пазла. Вращение ?>

<?if (!empty($model['width']) AND !empty($model['height'])):
    if ($model['height'] < $model['width']) { // альбомная ориентация
        $width = Yii::app()->params['thumbnailSize'][0];
        $height = floor($model['height'] * $width / $model['width']);
    } else { // портретная ориентация
        $height = Yii::app()->params['thumbnailSize'][1];
        $width  = floor($model['width'] * $height / $model['height']);
    }
else: // Если не указаны размеры устанавливаем пустые значения
    $height = '';
    $width = '';
endif;
?>
<script type="text/javascript">
//$(document).ready(function(){
    var angleQuant = 90;
    var imageAngle1, imageAngle0;
    /**
     * Вращение миниатюры по часовой стрелке
     */
    function rotateCW()
    {
        imageAngle0  = parseInt($('#inpRotateAngle').val());
        imageAngle1 = imageAngle0 + angleQuant;
        if (360 == imageAngle1)
            imageAngle1 = 0;
        $('#inpRotateAngle').val(imageAngle1);
        $("#thumbnail_1").rotate({
            duration: 1000,
            angle: imageAngle0,
            animateTo: imageAngle1
        });
        return false;
    }
    /**
     * Вращение миниатюры против часовой стрелке
     */
    function rotateCCW()
    {
        imageAngle0 = parseInt($('#inpRotateAngle').val());
        imageAngle1 = imageAngle0 - angleQuant;
        if (-360 == imageAngle1)
            imageAngle1 = 0;
        $('#inpRotateAngle').val(imageAngle1);
        $("#thumbnail_1").rotate({
            duration: 1000,
            angle: imageAngle0,
            animateTo: imageAngle1
        });
        return false;
    }
//});
</script>

<h1>Rotate Puzzle</h1>
<?=CHtml::beginForm(array(
    'editPuzzle',
    'item'  => $model['componentUrl'],
    'album' => empty($albumUrl) ?: CHtml::normalizeUrl($albumUrl)))?>

    <?=CHtml::hiddenField('itemID', $model['id'])?>

    <div style="margin-top:20px;height:<?=($width<$height)?$height:$width?>px;">
        <input type="hidden" name="inpRotateAngle" id="inpRotateAngle" value="0">
        <a href="" onclick="return rotateCCW(); return false;">
            <img src="/images/rotate_ccw.png" width="24" height="24" alt="Rotate counterclockwise" />
        </a>
        <img src="<?=Yii::app()->params['pathThumbnail']?>/<?=$model['imgUrl']?>/<?=$model['imgFullName']?>.jpg<?='?='.rand(1,99999)?>"
             width="<?=$width?>" height="<?=$height?>"
             id="thumbnail_1" alt="<?=$model['title']?>" />
        <a href="" onclick="return rotateCW(); return false;">
            <img src="/images/rotate_cw.png" width="24" height="24" alt="Rotate clockwise" />
        </a>
    </div>
    <hr />
    <br /><br />
    <?=CHtml::submitButton('Save')?>
<?=CHtml::endForm()?>
