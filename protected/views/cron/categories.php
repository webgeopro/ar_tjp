<table class="giAlbumList">
    <?if (count($categories))://$i=0;
        foreach($categories as $cat)://$i++;
            list($cat['imgFullName'], $cat['imgUrl']) = CImageSize::getPath($cat['thumbnail_id']);
            if ($cat['thumbnail_id']) // Установлена миниатюра альбома
                list($width, $height) = CImageSize::getRatioSize($cat['width'], $cat['height'], $albumWidth);
            else // Миниатюра не установлена в БД
                list($width, $height) = array(50, 38, $albumWidth);
            ?>
        <tr valign="top">
            <td>
                <a href="/<?=$cat['componentUrl']?><?=Yii::app()->params['urlSuffix']?>"
                   alt="<?=$cat['title']?>" title="<?=$cat['title']?>">
                    <img src="<?=$path?>/<?=$cat['imgUrl']?>/<?=$cat['imgFullName']?>.jpg"
                         class="giAlbumThumbnail" title="<?=$cat['title']?>" alt="<?=$cat['title']?>"
                         height="<?=$height?>" width="<?=$width?>">
                </a>
            </td>
            <td>
                <div class="giTitle">
                    <a href="/<?=$cat['componentUrl']?><?=Yii::app()->params['urlSuffix']?>"
                       alt="<?=$cat['title']?>" title="<?=$cat['title']?>"><?=$cat['title']?></a>
                </div>
                <div class="giInfo">
                    <?=$cat['cnt']?> puzzles
                </div>
            </td>
        </tr>
        <?endforeach?>
    <?endif;?>
        <tr>
            <td colspan="2" style="height:10px;"> </td>
        </tr>
</table>