<?list($imgFullName, $imgUrl, $width, $height) = CImageSize::getSizeById($item, $size, $size, true);?>

<div class="one-image">
    <div class="giThumbnailContainer">
        <a href="<?=@$album['ComponentUrl']?$album['ComponentUrl']:''?>/<?=$item['componentUrl']?><?=Yii::app()->params['urlSuffix']?>">
            <img src="<?=$path?>/<?=$imgUrl?>/<?=$imgFullName?>.jpg"
                 width="<?=$width?>" height="<?=$height?>"
                 id="IFid<?=$i?>" class="ImageFrame_solid"
                 onMouseOver="showPopupMenu(event,cutoutMenu,null,'<?=$cutout?>');return true;"
                 onMouseOut="hidePopupMenu(event);return true;"
                 alt="<?=$item['title']?>" />
        </a>
        <div class="giCutoutLabel">
            <img src="/images/cutouts/<?=str_replace(' ','_',$cutout)?>.png"
                 alt="<?=$cutout?>" title="<?=$cutout?>" />
        </div>
    </div>
    <div class="giItemInfo">
        <h4 class="giDescription">
            <?=empty($item['description'])?'':$item['description']?>
        </h4>
        <p class="giTitle"><?=$item['title']?></p>
        <?if (@$viewCutout):?>
            <p class="giTitle">
                <?=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?>
            </p>
        <?endif?>
        <?if (!empty($item['author'])):?>
            <p class="giInfo">Photo: <?=$item['author']?></p>
        <?endif?>
    </div>
</div>
<!--
<?/*if (!empty($item['id'])):*/?>
<div class="one-image">
    <div class="giThumbnailContainer">
        <a href="/<?/*=$item['componentUrl']*/?><?/*=Yii::app()->params['urlSuffix']*/?>">
            <img src="<?/*=Yii::app()->params['pathThumbnail']*/?>/<?/*=$item['imgUrl']*/?>/<?/*=$item['imgFullName']*/?>.jpg"
                 width="<?/*=$item['width']*/?>" height="<?/*=$item['height']*/?>" id="IFid<?/*=$i*/?>" class="ImageFrame_solid"
                 onMouseOver="showPopupMenu(event,cutoutMenu,null,'<?/*=$item['cutout']*/?> piece Classic');return true;"
                 onMouseOut="hidePopupMenu(event);return true;"
                 alt="<?/*=$item['title']*/?>" />
        </a>
        <div class="giCutoutLabel">
            <img src="/images/cutouts/<?/*=$item['cutout']*/?>_piece_classic.png"
                 alt="<?/*=$item['cutout']*/?> piece Classic" title="<?/*=$item['cutout']*/?> piece Classic" />
        </div>
    </div>
    <div class="giItemInfo">
        <h4 class="giDescription">
            <?/*=$item['title']*/?>
        </h4>
    </div>
    <?/*if (count($actions)):*/?>
        Размещаем select
    <?//endif?>
</div>
--><?//endif;?>