<?
$this->breadcrumbs = $breadsCrumbsTitle;
/*$this->breadcrumbs=array(
    //$album['componentUrl'] => '/'.$album['componentUrl'].Yii::app()->params['urlSuffix'],
    $breadsCrumbsTitle[1]=>$breadsCrumbsTitle[0],
    $item['title']=> '/'.$album['componentUrl'].'/'.$item['componentUrl'].Yii::app()->params['urlSuffix'],
    'Edit puzzle',
);*/
if (!empty($item['width']) AND !empty($item['height'])):
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
                                            <?//todo Изменить для администратора?>
                                            <a href="<?=$itemAddress?>">
                                                <img src="<?=Yii::app()->params['pathThumbnail']?>/<?=$item['imgUrl']?>/<?=$item['imgFullName']?>.jpg"
                                                     width="<?=$width?>" height="<?=$height?>" id="IFid1"
                                                     class="ImageFrame_solid" alt="<?=$item['title']?>"
                                                     onMouseOver="showPopupMenu(event, cutoutMenu, null,
                                                         '<?=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?>');
                                                         return true;"
                                                     onMouseOut="hidePopupMenu(event);return true;" />
                                            </a>
                                            <div class="giCutoutLabel">
                                                <img src="/images/cutouts/<?=$item['cut']?str_replace(' ','_',$item['cut']):str_replace(' ','_',Yii::app()->params['defaultCutout'])?>.png"
                                                     alt="<?=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?>"
                                                     title="<?=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?>" />
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
            <div id="gsContent" class="gcBorder1">
                <div class="gbBlock gcBackground1">
                    <h2> Edit Details </h2>
                </div>
                <form action="" method="post" enctype="multipart/form-data" id="itemAdminForm">
                    <input type="hidden" name="serviceToken" value="1">
                    <input type="hidden" name="itemID" value="<?=$item['id']?>">

                    <div class="gbBlock">
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
                                             id="thumbnail_1" class="giThumbnail" alt="<?=$item['title']?>"/>
                                    </a>
                                </td>
                            </tr><tr>
                                <td>
                                    <input type="hidden" name="item[<?=$item['id']?>][angle]" id="inpRotateAngle" value="0">
                                    <a href="" id="aRotateCCW">
                                        <img src="/images/rotate_ccw.png" width="24" height="24" alt="Rotate counterclockwise"/>
                                    </a>&nbsp;Rotate image&nbsp;
                                    <a href="" id="aRotateCW">
                                        <img src="/images/rotate_cw.png" width="24" height="24" alt="Rotate clockwise"/>
                                    </a>
                                </td></tr>
                            </table>
                        </div>
                        <br clear=all>
                        <hr>
                    </div>
                    <div class="gbBlock gcBackground1">
                        <input type="submit" class="inputTypeSubmit"
                               name="item[change]" value="Change"/>
                        <input type="submit" class="inputTypeSubmit"
                               name="item[cancel]" value="Leave Unchanged"
                               onclick="javascript:window.location.href='<?=Yii::app()->getBaseUrl(true).'/'.$album['componentUrl'].'/'.$item['componentUrl'].Yii::app()->params['urlSuffix']?>';return false" />
                        Note: Any rotations must be undone manually.
                    </div>
            </div></td>
    </tr>
</table>