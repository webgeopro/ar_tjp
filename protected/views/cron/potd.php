<div class='block-imageblock-ImageBlock'>
    <?if (count($potd['item'])):
    $i=0;
    foreach($potd['item'] as $item):
        $i++;
        list($imgFullName, $imgUrl, $width, $height) =
            CImageSize::getSizeById($item, $size, $size, true);
        //$componentUrl = urldecode($item['componentUrl']); // Преобразуем url ?>
        <div class="one-image">
            <div class="giThumbnailContainer">
                <a href="<?=isset($item['albumComponentUrl'])?$item['albumComponentUrl']:@$albumComponentUrl?>/<?=$item['componentUrl']?><?=Yii::app()->params['urlSuffix']?>">
                    <img src="<?=$path?>/<?=$imgUrl?>/<?=$imgFullName?>.jpg"
                         <?=$width?'width="'.$width.'"':''?><?=$height?'height="'.$height.'"':''?>
                         id="IFid<?=$i?>" class="ImageFrame_solid"
                         onMouseOver="
                             showPopupMenu(event,cutoutMenu,null,'<?=$item['cut']?>');
                             showPreviewButton(event,'<?=Yii::app()->params['pathWorked']?>/<?=$imgUrl?>/<?=$imgFullName?>.jpg');return true;"
                         onMouseOut="hidePopupMenu(event); hidePreviewButton(event); return true;"
                         alt="<?=$item['title']?>" />
                </a>
                <div class="giCutoutLabel">
                    <img src="/images/cutouts/<?=$item['cut']?str_replace(' ','_',$item['cut']):str_replace(' ','_',Yii::app()->params['defaultCutout'])?>.png"
                         alt="<?=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?>"
                         title="<?=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?>" />
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
    <?endforeach?>
    <?endif;?>
</div>
