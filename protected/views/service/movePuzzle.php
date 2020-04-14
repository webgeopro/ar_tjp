<?$this->breadcrumbs = array(
    $album['title'] => "/{$album['componentUrl']}".Yii::app()->params['urlSuffix'],
    'Move puzzle',
);?>
<br /><br />
<?if (isset($result) AND 'success'==$result):?>
    <h2>Result: Success.</h2>
    <a href="<?=$newAddress?>">New puzzle address</a>
    <?//Ветвь не используется. Redirect.?>
<?else:
echo CHtml::beginForm('', 'post', array('name' => 'formMovePuzzle', 'id' => 'formMovePuzzle'))?>
<input type="hidden" name="serviceToken" value="1">
<input type="hidden" name="itemID" value="<?=$item['id']?>">
<input type="hidden" name="albumID" id="inpAlbumID" value="<?=$album['id']?>">
<input type="hidden" name="albumOldID" value="<?=$album['id']?>">
<h3><?=$item['title']?></h3>
<?if (Yii::app()->user->hasFlash('movePuzzleResult')):?>
    <h3><span style="color:#005E00;"><?=Yii::app()->user->getFlash('movePuzzleResult')?></span></h3>
    <br/>
<?endif;?>
<table>
    <tr>
        <td style="vertical-align:top;">
            <img src="<?=Yii::app()->params['pathThumbnail']?>/<?=$item['imgUrl']?>/<?=$item['imgFullName']?>.jpg"
                 id="IFid1" class="ImageFrame_solid" alt="<?=$item['title']?>" />
        </td>
        <td style="vertical-align:top;padding: 1px 6px;">
            <?$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
                'model' => $album,
                'attribute' => 'title',
                //'source' =>Yii::app()->createUrl('service/albums-complete'),
                'source' => '/service/albums-complete',
                'options'=>array(
                    'minLength'=>'2', // Автоподбор включается после набора 2-х символов
                    'select' =>'js: function(event, ui) { // Ф-ия вызывается после выбора из выпад. списка
                        this.value = ui.item.value;
                        $("#inpAlbumID").val(ui.item.id); // Передаем ID альбома в скрытое поле
                        return false;
                    }',
                ),
                'htmlOptions' => array(
                    'style' => 'width:400px;height:20px;',
                ),
            ));?>
            <br /><br />
            <?=CHtml::submitButton('Move')?>
        </td>
    </tr>
</table>
<?=CHtml::endForm();
endif;?>
<br />