<?$this->breadcrumbs=array('Search',);?>
<table cellspacing="0" cellpadding="0" width="100%" class="gcBackground1">
    <tbody><tr valign="top">
        <td width="20%">
            <table cellspacing="0" cellpadding="0">
                <tbody><tr>
                    <td style="padding-bottom:5px" colspan="2">
                        <div class="gsContentDetail">
                            <div class="gbBlock gcBorder1">

                                <div class="block-imageblock-ImageBlock">
                                    <div class="one-image">
                                        <div class="giThumbnailContainer">
                                            <?//$this->widget('getAlbumThumbnail', array('default'=>true))?>
                                            <?$this->widget('defaultImage')?>
                                        </div>
                                        <div class="giItemInfo">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div></td>
                </tr>
            </tbody></table>
        </td>
        <td>
            <div id="gallery">
                <div style="width:480px;font-size:1.5em;">
                <?$this->widget('searchBlock', array(
                'searchString' => $searchString,
                'extended' => true,
                'inTitle'    => $this->inTitle,
                'inAuthor'   => $this->inAuthor,
                'inKeywords' => $this->inKeywords,
                'page' => $this->page,
                ));?>
                </div>
            <table id="gsThumbMatrix" width="100%">
            <tr valign="top">
                <?$i=0;foreach($objects as $item):
                if (array_key_exists($item['cutout'], $this->cutout))
                    $item['cutout'] = array_key_exists($item['cutout'], $this->cutout)
                        ? $this->cutout[$item['cutout']]
                        : Yii::app()->params['defaultCutout'];

                list($imgFullName, $imgUrl) = CImageSize::getPath($item['id']);
                list($width, $height) =
                    CImageSize::getSizeById($item, Yii::app()->params['thumbnailSize'][0], Yii::app()->params['thumbnailSize'][1]);

                if(!($i % $num)):?></tr><tr valign="top"><?endif?>

                    <td class="giItemCell" style="width:<?=$tdWidth?>%">
                        <div class="giThumbnailContainer">
                            <a href="/<?=$item['albumComponentUrl']?>/<?=$item['componentUrl']?><?=Yii::app()->params['urlSuffix']?>"
                               alt="<?=$item['title']?>" title="<?=$item['title']?>">
                                <img src="<?=$path?>/<?=$imgUrl?>/<?=$imgFullName?>.jpg"
                                     height="<?=$height?>" width="<?=$width?>"
                                     id="IFid<?=$i?>" class="ImageFrame_solid"
                                     onMouseOver="showPopupMenu(event,cutoutMenu,null,'<?=$item['cutout']?>');return true;"
                                     onMouseOut="hidePopupMenu(event);return true;"
                                     alt="<?=$item['title']?>" />
                            </a>
                            <div class="giCutoutLabel">
                                <img src="/images/cutouts/<?=str_replace(' ','_',$item['cutout'])?>.png"
                                     alt="<?=$item['cutout']?>"
                                     title="<?=$item['cutout']?>" />
                            </div>
                        </div>
                        <p class="giTitle">
                            <a href="/<?=$item['albumComponentUrl']?>/<?=$item['componentUrl']?><?=Yii::app()->params['urlSuffix']?>">
                                <?=$item['title']?>
                            </a>
                        </p>
                        <p class="giTitle"><?=$item['cutout']?></p>
                        <?if(!empty($item['author'])):?>
                            <p class="giInfo">Photo: <?=$item['author']?></p>
                        <?endif?>
                    </td>
                <?$i++;endforeach?>
            </tr>
            </table>
            </div>
    </td>
    </tr></tbody></table>