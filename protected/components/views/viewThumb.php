<div class='block-imageblock-ImageBlock'><?//die(print_r($this->album));die(print_r($this->item));?>
    <div class="one-image">
        <div class="giThumbnailContainer">
        <?if (isset($cutout)):?>
            <a href="<?=$url?>">
                <img src="<?=$thumb?>"
                     <?=$params['width']?> <?=$params['height']?>
                     id="IFid1" class="ImageFrame_solid"
                     onMouseOver="showPopupMenu(event,cutoutMenu,null,'<?=$cutout?>');return true;"
                     onMouseOut="hidePopupMenu(event);return true;"
                     alt="<?=$title?>" />
            </a>
        <?else:?>
            <a href="<?=$url?>">
                <img src="<?=$thumb?>"  id="IFid1" class="ImageFrame_solid" />
            </a>
        <?endif?>
        </div>
    </div>
    <?if ($this->viewTitle):?>
        <div class="gbBlock gcBorder1">
            <h2> <?=$title?> </h2>
        </div>
    <?endif?>
</div>