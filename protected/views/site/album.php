<?$this->breadcrumbs=array(
    CHtml::decode(isset($album['title']) ? $album['title'] : @$albumName),
);
/*if (Yii::app()->user->hasFlash('updatePuzzle')):
    echo '<script type="text/javascript">alert("reload session");</script>';
    echo '<script type="text/javascript">location.reload(true);</script>';
    Yii::app()->user->getFlash('updatePuzzle'); // Обнуляем flash-сообщение
endif;*/
if (!empty(Yii::app()->request->cookies['updatePuzzle']->value)):
    unset(Yii::app()->request->cookies['updatePuzzle']); // Обнуляем flash-сообщение
    echo '<script type="text/javascript">location.reload(true);</script>';
endif;
/*<script type="text/javascript">
    var update = window.location.search.substring(1).split("&"); // Разбиваем адресную строку
    alert(update);
    //location.reload(true);
</script>*/?>
<?/*if(isset(Yii::app()->request->cookies['TJPaction']->value) AND 'update' == Yii::app()->request->cookies['TJPaction']->value):
    die('result='.Yii::app()->request->cookies['TJPaction']->value)
endif;*/?>
<table class="gcBackground1" width="100%" cellspacing="0" cellpadding="0">
    <tr valign="top">
        <td>
            <div id="gsContent" class="gcBorder1">
                <?$this->widget('userBlock', array('isAlbum'=>true, 'album'=>$album))?>
                <table id="ContentAlbum" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="250" valign="top">
                            <?$this->widget('albumSiblings', array('album'=>$album))?>
                            <div class="gbBlock" style="margin-bottom:0;">
                                <?$this->widget('searchBlock', array('albumUrl'=>@$album->componentUrl, 'itemUrl'=>@$item->componentUrl))?>
                            </div>
                            <div class="gsContentDetail" style="margin-top:0;">
                                <div class="gbBlock gcBorder1">
                                    <h2><?=$album['title']?></h2>
                                    <?if(!empty($album['description'])):?>
                                        <p class="giDescription"><?=$album['description']?></p>
                                    <?endif?>
                                    <?if ($album['cnt']):?>
                                    <div class="block-core-ItemInfo giInfo">
                                        <div class="size summary"><?=$album['cnt']?> puzzles</div>
                                    </div>
                                    <?endif?>
                                </div>
                                <table cellspacing="0" cellpadding="0" style="width: 100%"><tr>
                                    <td>
                                        <div id="gsSidebar" class="gcBorder1" style="width: 100%">
                                            <div class="gbBlock">
                                                <?$this->widget('potdFeatured')?>
                                            </div>

                                            <div class="gbHelpLink">
                                                <a href="/help" target="_blank">Got questions? Get Help!</a>
                                            </div>

                                            <div class="block-core-ItemLinks gbBlock">
                                                <?$this->widget('userActions', array('album'=>$album,'albumUser'=>$albumUser,))?>
                                            </div>
                                            <div class="gbBlock gcBorder1" style="margin-top:5px;clear:both;">
                                                <?=$this->renderFile('./././items/static/linksShareAlbum')?><!-- Ссылки на facebooks -->
                                            </div>

                                            <?//if (empty($this->albumUser)):?>
                                            <?if ($album['parent_id'] != Yii::app()->params['userAlbumID']):?>
                                                <div class="gbBlock gcBorder1" style="margin:40px 15px 20px 15px">
                                                    <!-- ValueClick Media 120x600 and 160x600 SkyScraper CODE for TheJigsawPuzzles.com -->
                                                    <script src="http://cdn.fastclick.net/js/adcodes/pubcode.min.js"></script><script type="text/javascript">document.write('<scr' + 'ipt type="text/javascript">(function () {try{VCM.media.render({sid:63255,media_id:3,media_type:7,version:"1.1"});} catch(e){document.write(\'<scr\' + \'ipt type="text/javascript" src="http://media.fastclick.net/w/get.media?sid=63255&m=3&tp=7&d=j&t=n&exc=1"></scr\' + \'ipt>\');}}());</scr' + 'ipt>');</script><noscript><a href="http://media.fastclick.net/w/click.here?sid=63255&m=3&c=1" target="_blank"><img src="http://media.fastclick.net/w/get.media?sid=63255&m=3&tp=7&d=s&c=1&vcm_acv=1.1" width=160 height=600 border=1></a></noscript>
                                                    <!-- ValueClick Media 120x600 and 160x600 SkyScraper CODE for TheJigsawPuzzles.com -->
                                                </div>
                                            <?endif?><?// Здесь проверка что альбом основной -ЗАЧЕМ?- ?>
                                                <div class="gbAmazonWidgetAlbumPage">Here's what the guys from <img src="/images/amazon_logo.png" width="74" height="14"> picked for you jigsaw puzzle lovers:
                                                    <div class="widgetContents">
                                                        <script language="JavaScript" type="text/javascript">
                                                            if(Math.random()*100 < 50){
                                                                document.write("\x3COBJECT classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http:\x2F\x2Ffpdownload.macromedia.com\x2Fget\x2Fflashplayer\x2Fcurrent\x2Fswflash.cab\" id=\"Player_7aa5f466-78c3-4c5c-b2c9-94eec5f05048\"  WIDTH=\"160px\" HEIGHT=\"400px\"\x3E \x3CPARAM NAME=\"movie\" VALUE=\"http:\x2F\x2Fws.amazon.com\x2Fwidgets\x2Fq?ServiceVersion=20070822\&MarketPlace=US\&ID=V20070822%2FUS%2Fthejigcom-20%2F8009%2F7aa5f466-78c3-4c5c-b2c9-94eec5f05048\&Operation=GetDisplayTemplate\"\x3E\x3CPARAM NAME=\"quality\" VALUE=\"high\"\x3E\x3CPARAM NAME=\"bgcolor\" VALUE=\"#FFFFFF\"\x3E\x3CPARAM NAME=\"allowscriptaccess\" VALUE=\"always\"\x3E\x3Cembed src=\"http:\x2F\x2Fws.amazon.com\x2Fwidgets\x2Fq?ServiceVersion=20070822\&MarketPlace=US\&ID=V20070822%2FUS%2Fthejigcom-20%2F8009%2F7aa5f466-78c3-4c5c-b2c9-94eec5f05048\&Operation=GetDisplayTemplate\" id=\"Player_7aa5f466-78c3-4c5c-b2c9-94eec5f05048\" quality=\"high\" bgcolor=\"#ffffff\" name=\"Player_7aa5f466-78c3-4c5c-b2c9-94eec5f05048\" allowscriptaccess=\"always\"  type=\"application\x2Fx-shockwave-flash\" align=\"middle\" height=\"400px\" width=\"160px\"\x3E\x3C\x2Fembed\x3E\x3C\x2FOBJECT\x3E");
                                                            } else {
                                                                document.write("\x3COBJECT classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http:\x2F\x2Ffpdownload.macromedia.com\x2Fget\x2Fflashplayer\x2Fcurrent\x2Fswflash.cab\" id=\"Player_67fe245d-4459-46f4-9cd6-157ec19c0fa6\"  WIDTH=\"160px\" HEIGHT=\"400px\"\x3E \x3CPARAM NAME=\"movie\" VALUE=\"http:\x2F\x2Fws.amazon.com\x2Fwidgets\x2Fq?ServiceVersion=20070822\&MarketPlace=US\&ID=V20070822%2FUS%2Fthejigcom-20%2F8009%2F67fe245d-4459-46f4-9cd6-157ec19c0fa6\&Operation=GetDisplayTemplate\"\x3E\x3CPARAM NAME=\"quality\" VALUE=\"high\"\x3E\x3CPARAM NAME=\"bgcolor\" VALUE=\"#FFFFFF\"\x3E\x3CPARAM NAME=\"allowscriptaccess\" VALUE=\"always\"\x3E\x3Cembed src=\"http:\x2F\x2Fws.amazon.com\x2Fwidgets\x2Fq?ServiceVersion=20070822\&MarketPlace=US\&ID=V20070822%2FUS%2Fthejigcom-20%2F8009%2F67fe245d-4459-46f4-9cd6-157ec19c0fa6\&Operation=GetDisplayTemplate\" id=\"Player_67fe245d-4459-46f4-9cd6-157ec19c0fa6\" quality=\"high\" bgcolor=\"#ffffff\" name=\"Player_67fe245d-4459-46f4-9cd6-157ec19c0fa6\" allowscriptaccess=\"always\"  type=\"application\x2Fx-shockwave-flash\" align=\"middle\" height=\"400px\" width=\"160px\"\x3E\x3C\x2Fembed\x3E\x3C\x2FOBJECT\x3E");
                                                            }
                                                        </script>
                                                        <NOSCRIPT><A HREF="http://ws.amazon.com/widgets/q?ServiceVersion=20070822&MarketPlace=US&ID=V20070822%2FUS%2Fthejigcom-20%2F8009%2F7aa5f466-78c3-4c5c-b2c9-94eec5f05048&Operation=NoScript">Amazon.com Widgets</A></NOSCRIPT>
                                                    </div>
                                                </div>

                                        </div>
                                    </td>
                                </tr></table>
                                <br /><br /><br />

                            </div>
                        </td>
                        <td valign="top">
                            <?$this->widget('albumList', array(
                                'album'=>$album, 'albumUser'=>$albumUser, 'newPuzzles'=>@$newPuzzles))?>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>