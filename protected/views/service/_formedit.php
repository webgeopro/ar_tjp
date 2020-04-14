<?if (!empty($item['width']) AND !empty($item['height'])):
    if ($item['height'] < $item['width']) { // альбомная ориентация
        $width = $itemWidth;
        $height = floor($item['height'] * $width / $item['width']);
    } else { // портретная ориентация
        $height = $itemHeight;
        $width  = floor($item['width'] * $height / $item['height']);
    }
else: // Если не указаны размеры устанавливаем пустые значения
    $height = '';
    $width = '';
endif;
$cut = empty($item['cut']['name'])
     ? empty($item['cut']) ? Yii::app()->params['defaultCutout'] : $item['cut']
     : $item['cut']['name'];
?>

<table class="gcBackground1" width="100%" cellspacing="0" cellpadding="0">
    <tr valign="top">
        <td width="20%">
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="2" style="padding-bottom:5px">
                        <div class="gsContentDetail">
                            <div class="gbBlock gcBorder1">
                                <div class="block-imageblock-ImageBlock">
                                    <div class="one-image">
                                        <div class="giThumbnailContainer">
                                            <a href="<?=$itemAddress?>">
                                                <img src="<?=Yii::app()->params['pathThumbnail']?>/<?=$item['imgUrl']?>/<?=$item['imgFullName']?>.jpg"
                                                     width="<?=$width?>" height="<?=$height?>" id="IFid1"
                                                     class="ImageFrame_solid" alt="<?=$item['title']?>"
                                                     onMouseOver="showPopupMenu(event, cutoutMenu, null, '<?=$cut?>');                                                         return true;"
                                                     onMouseOut="hidePopupMenu(event);return true;" />
                                            </a>
                                            <div class="giCutoutLabel">
                                                <img src="/images/cutouts/<?=str_replace(' ','_',$cut)?>.png"
                                                     alt="<?=$cut?>"
                                                     title="<?=$cut?>" />
                                            </div>
                                        </div>
                                        <div class="giItemInfo"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="gbBlock gcBorder1">
                                <h2><?=$item['title']?></h2>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
        <td>
            <div id="gsContent" class="gcBorder1" style="padding-bottom:10px;">
                <div class="gbBlock gcBackground1">
                    <h2> Edit Details </h2>
                </div>
                <!--<form action="" method="post" enctype="multipart/form-data" id="itemAdminForm">
                    <input type="hidden" name="serviceToken" value="1">
                    <input type="hidden" name="itemID" value="<?=$item['id']?>">-->
                <div>
                    <div style="clear:both">
                        <input type="checkbox" name="item[<?=$item['id']?>][chItemDelete]" id="<?$item['id']?>">
                        Delete this puzzle:
                        <a href="<?=$itemAddress?>">
                            <?=$item['componentUrl']?>
                        </a>
                        <h4> Title </h4>
                    </div>
                    <div style="float:left">
                        <input type="text" id="title_<?=$item['id']?>" size="50"
                               name="item[<?=$item['id']?>][title]" value="<?=$item['title']?>"/>
                        <h4> Description </h4>
                        <textarea id="description_<?=$item['id']?>" rows="4" cols="50"
                                  name="item[<?=$item['id']?>][description]"><?=$item['attr']['description']?></textarea>
                        <h4> Default cut </h4>
                        <?=CHtml::dropDownList('item['.$item['id'].'][cutout]', $item['cutout'], $listCutout)?>
                    </div>
                    <div style="float:left; padding-left:50px;">
                        <table><tr>
                            <td>
                                <a href="<?=$itemAddress?>">
                                    <img src="<?=Yii::app()->params['pathThumbnail']?>/<?=$item['imgUrl']?>/<?=$item['imgFullName']?>.jpg"
                                         width="<?=$width?>" height="<?=$height?>"
                                         onLoad="this.style.visibility=''"
                                         id="thumbnail_<?=$item['id']?>" class="giThumbnail" alt="<?=$item['title']?>"/>
                                </a>
                            </td>
                        </tr><tr>
                            <td>
                                <input type="hidden" name="item[<?=$item['id']?>][angle]" id="inpRotateAngle_<?=$item['id']?>" value="0">
                                <a href="" class="aRotateCCW" id="aRotateCCW_<?=$item['id']?>">
                                    <img src="/images/rotate_ccw.png" width="24" height="24" alt="Rotate counterclockwise"/>
                                </a>&nbsp;Rotate image&nbsp;
                                <a href="" class="aRotateCW" id="aRotateCW_<?=$item['id']?>">
                                    <img src="/images/rotate_cw.png" width="24" height="24" alt="Rotate clockwise"/>
                                </a>
                            </td>
                        </tr></table>
                    </div>
                    <br clear=all>
                </div>
            </div>
        </td>
    </tr>
</table>