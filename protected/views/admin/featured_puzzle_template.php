<?// Устанавливаем размеры (миниатюры, пазла дня, категорий)
if (empty($item['imgUrl'])) // Формируем imgUrl если его нет
    list($item['imgFullName'], $item['imgUrl']) = CImageSize::getPath($item['id']);
list($width, $height) = // Получаем размеры
    CImageSize::getSizeById($item, Yii::app()->params['thumbnailSize'][0], Yii::app()->params['thumbnailSize'][1]);?>

<div class='block-imageblock-ImageBlock'>
    <div class="one-image">
        <h3> Featured Puzzle </h3>

        <div class="giThumbnailContainer">
            <a href="/<?=$item['albumComponentUrl']?>/<?=$item['componentUrl']?><?=Yii::app()->params['urlSuffix']?>">
                <img src="<?=Yii::app()->params['pathThumbnail']?>/<?=$item['imgUrl']?>/<?=$item['imgFullName']?>.jpg"
                     width="<?=$width?>" height="<?=$height?>" id="IFid<?=$item['id']?>" class="ImageFrame_solid"
                     onMouseOver="showPopupMenu(event,cutoutMenu,null,'<?=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?>');return true;"
                     onMouseOut="hidePopupMenu(event);return true;"
                     alt="<?=$item['title']?>"/>
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
            <?=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?>
            <!--<?#if ($item->attr->author):?><p class="giInfo">Photo: <?#=$item->attr->author?></p><?#endif?>-->
            <?if(!empty($item['author'])):?>
                <p class="giInfo">Photo: <?=$item['author']?></p>
            <?endif?>
        </div>
    </div>
</div>