<div class="gbBlockNavTop">
    <div class="gbNavigator">
        <?$this->widget('LinkPager', array('pages' => $pages,))?>
    </div>
</div>
<?if ($this->beginCache($name, array(
    'duration'   => $duration,
    'dependency' => !(empty($dependency))
        ? array(
            'class' => 'system.caching.dependencies.CDbCacheDependency',
            'sql'   => $dependency,)
        : null,))
    ):
    if (count($items['item'])):?>

<table id="gsThumbMatrix" width="100%">
    <tr valign="top">
    <?$i=0;foreach($items['item'] as $item):
        $componentUrl = urlencode($item['componentUrl']); // Преобразуем url
        list($imgFullName, $imgUrl) = CImageSize::getPath($item['id']);
        list($width, $height) =
            CImageSize::getSizeById($item, Yii::app()->params['thumbnailSize'][0], Yii::app()->params['thumbnailSize'][1]);
        if(!($i % $num)) echo '</tr><tr valign="top">';?>

        <td class="giItemCell" style="width:<?=$tdWidth?>%">
            <div class="giThumbnailContainer">
                <a href="/<?=($this->newPuzzles)?$item['albumComponentUrl']:$album['componentUrl']?>/<?=$componentUrl?><?=Yii::app()->params['urlSuffix']?>"
                   alt="<?=$item['title']?>" title="<?=$item['title']?>">
                    <img src="<?=$path?>/<?=$imgUrl?>/<?=$imgFullName?>.jpg"
                         height="<?=$height?>" width="<?=$width?>"
                         id="IFid<?=$i?>" class="ImageFrame_solid"
                         onMouseOver="
                             showPopupMenu(event,cutoutMenu,null,'<?=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?>');
                             showPreviewButton(event,'<?=Yii::app()->params['pathWorked']?>/<?=$imgUrl?>/<?=$imgFullName?>.jpg');return true;"
                         onMouseOut="hidePopupMenu(event);return true;"
                         alt="<?=$item['title']?>" />
                </a>
                <div class="giCutoutLabel">
                    <img src="/images/cutouts/<?=$item['cut']?str_replace(' ','_',$item['cut']):str_replace(' ','_',Yii::app()->params['defaultCutout'])?>.png"
                         alt="<?=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?>"
                         title="<?=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?>" />
                </div>
            </div>
                <div class="block-core-ItemLinks"><?//die($componentUrl . $album['componentUrl']);
                    if (@$this->albumUser['id']) // Пользовательский альбом
                        //$this->controller->renderDynamic('getActions', $album['owner_id'], null, null, $item['id']);
                        $this->controller->renderDynamic('getActions', $album['owner_id'], $componentUrl, $album['componentUrl']);
                    elseif ($this->newPuzzles) {}
                    else
                        $this->controller->renderDynamic('getActions', $album['owner_id'], $componentUrl, $album['componentUrl']);
                ?></div>
                <p class="giTitle">
                    <a href="/<?=($this->newPuzzles)?$item['albumComponentUrl']:$album['componentUrl']?>/<?=$componentUrl?><?=Yii::app()->params['urlSuffix']?>">
                        <?=$item['title']?>
                    </a>
                </p>
                <!--<p class="giTitle"><?=$item['cutout']?$item['cutout']:Yii::app()->params['defaultCutout']?> piece Classic</p>-->
                <p class="giTitle"><?=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?></p>
                <?if(!empty($item['author'])):?>
                    <p class="giInfo">Photo: <?=$item['author']?></p>
                <?endif?>
            </td>
    <?$i++;endforeach?>
    </tr>
</table>
<?else:?>
    <?if (isset($this->albumUser['id'])):?>
        <div class="giDescription gbEmptyAlbum">
            <h3 class="emptyAlbum">
                There are no puzzles here yet.
                <br/>
                <a href="/makeapuzzle">
                    <img width="166" height="36" title="Make a puzzle!" alt="Make a puzzle!" src="/images/make_a_puzzle.png">
                </a>
            </h3>
        </div>
    <?endif;?>
<?endif;?>
<?$this->endCache();endif; //@todo endCache НЕ нужен -> if(...)?>
<div class="gbBlockNavTop">
    <div class="gbNavigator">
        <?$this->widget('LinkPager', array('pages' => $pages,))?>
    </div>
</div>
<script type="text/javascript">
    function doAction(a){confirm("Are you sure you want to delete this puzzle?")&&a.value.length&&$.get(a.value,"",function(a){"success"==a.result?document.location.reload(!0):alert("An error occurred while deleting the puzzle.")},"json");return!1}
    function toPage(a,b){a.length&&(b?window.open(a,"_blank"):location.href=a);return!1};
    function getFunc(a){if("#"==a.value[0]){var b=a.value.slice(1);return toPage(b,a.title)}return doAction(a)};
</script>
<?/*
 * function toPage(a,b){alert('!!');return false; a.value.length&&(b?window.open(a.value,"_blank"):location.href=a.value);return!1};
 * function getFunc(ob){
        if ('#' == ob.value[0]){
            var address = ob.value.slice(1);
            return toPage(address, ob.title);
        }
        return doAction(ob);
}*/?>