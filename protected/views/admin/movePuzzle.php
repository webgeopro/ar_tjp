<?$this->breadcrumbs=array(
    $album['title'] => "/{$album['componentUrl']}".Yii::app()->params['urlSuffix'],
    'Move puzzle',
);?>
<br /><br />
<?=CHtml::beginForm('', 'post', array('name' => 'formMovePuzzle', 'id' => 'formMovePuzzle'))?>
<input type="hidden" name="serviceToken" value="1">
<input type="hidden" name="itemID" value="<?=$item['id']?>">
<h3><?=$item['title']?></h3>
<table>
    <tr>
        <td style="vertical-align:top;">
            <img src="<?=Yii::app()->params['pathThumbnail']?>/<?=$item['imgUrl']?>/<?=$item['imgFullName']?>.jpg"
                 id="IFid1" class="ImageFrame_solid" alt="<?=$item['title']?>" />
        </td>
        <td style="vertical-align:top;padding: 1px 6px;">
            <?if (count($item['album'])):?>
            <ul style="list-style-type:none;margin:0;">
                <?foreach($item['album'] as $alb):?>
                <li>
                    <?=$alb['title']?>&nbsp;<a href="" class="aDeleteAlbumItem" name="<?=$alb['id']?>"
                                               title="Delete from <?=$alb['title']?>"> X </a>
                </li>
                <?endforeach?>
                <input type="hidden" id="inpDeleteAlbumItemID" name="inpDeleteAlbumItemID" value="">
            </ul>
            <?endif?>
        </td>
        <td style="vertical-align:top;">
            <?=CHtml::dropDownList('albumName', '', $albumList)?>
            <br />
            <?=CHtml::submitButton('Save')?>
        </td>
    </tr>
</table>
<?=CHtml::endForm()?>
<br />