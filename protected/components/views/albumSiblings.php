<?if ($this->beginCache($name, array('duration'=>$this->duration))):?>
    <table cellspacing="0" cellpadding="0"><tr><td style="padding-top:5px" colspan="2">
    <?if (count($items['item'])):?>
    <div class="gsContentDetail gcBorder1"><div class="gbNavigatorMicroThums"><div>
        <table cellpadding="0" cellspacing="0" <?#width="100%"?>>
            <tr>
                <?$i=0; $path = empty($albumUrl)?'':$albumUrl.'/';
                foreach($items['item'] as $item):
                    if (empty($item['imgUrl'])) { // Полученные без модели Album
                        if (isset($item['thumbnail_id'])) $itemID = $item['thumbnail_id']; // Альбом
                        else $itemID = $item['id']; // Пазл

                        list($imgFullName, $imgUrl) = CImageSize::getPath($itemID);
                    } else {
                        $imgFullName = $item['imgFullName'];
                        $imgUrl = $item['imgUrl'];
                    }
                    list($width, $height) = CImageSize::getSizeById($item);
                    if(!($i % $this->num)) echo '</tr><tr>';?>

                    <td id="microThumb" align="center" width="50" height="50">
                        <a href="/<?=$path.$item['componentUrl'].Yii::app()->params['urlSuffix']?>">
                            <img src="<?=Yii::app()->getBaseUrl(true).Yii::app()->params['pathThumbnail']?>/<?=$imgUrl?>/<?=$imgFullName?>.jpg"
                                 height="<?=$height?>" width="<?=$width?>"
                                 class="giThumbnailIce" title="<?=$item['title']?>" />
                        </a>
                    </td>
                <?$i++;endforeach;?>
            </tr>
        </table>
    </div></div></div>
    <?endif;?>
    </td></tr></table>
<?$this->endCache();endif?>