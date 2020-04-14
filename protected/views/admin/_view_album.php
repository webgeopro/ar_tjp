<?list($width, $height) = CImageSize::getSizeById($data, 40, 40, false);?>
<tr class="<?= ($index % 2) ? 'even' : 'odd' ?>">
    <td>
        <img src="<?=Yii::app()->params['pathThumbnail']?>/<?=$data['imgUrl']?>/<?=$data['imgFullName']?>.jpg"
             height="<?=$height?>" width="<?=$width?>" class="ImageFrame_solid" alt="<?=$data['title']?>" />
    </td>
    <td><?=$data['title']?></td>
    <td><?=$data['componentUrl']?></td>
    <td><?=$data['cnt']?></td>
    <?if (!empty($href)):?>
        <td><a href="<?=$action?>?album=<?=$data['componentUrl']?>"><?=$title?></a></td>
    <?else:?>
        <td><a href="<?=$action?>?id=<?=$data['id']?>&return=true"><?=$title?></a></td>
    <?endif?>
</tr>
