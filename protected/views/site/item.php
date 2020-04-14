<?$this->breadcrumbs=array(
    CHtml::decode($album['title']) => CHtml::decode("/{$album['componentUrl']}".Yii::app()->params['urlSuffix']),
    $item['title'] ? $item['title'] : $item['componentUrl'],
);?>

<table class="gcBackground1" width="100%" cellspacing="0" cellpadding="0">
    <tr valign="top">
        <td>
            <div id="gsContent" class="gcBorder1">
            <div class="gcBorder1"><?$this->widget('userBlock')?></div>
            <div id="ContentPhoto" class="gsContentPhoto">
                <table align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="right" width="200px" valign="top">
                            <?$this->widget('albumSiblings', array('album'=>$album,'puzzlePage'=>true))?>

                            <div class="gbPuzzleNameLabel"><div id="l2" style="overflow:hidden;"><div id="l3"><div id="l4"><div id="l5">
                                <p class="giTitle"></p><h2 style="font-size:1.2em;"><?=$item['title']?></h2><p></p>

                                <p class="giTitle"><?=$cutout?></p>
                                <?#=$item['cut']?$item['cut']:Yii::app()->params['defaultCutout']?>

                                <?//if (!empty($item['attrAuthor'])):?>
                                <?if (!empty($item['attr'])):?>
                                    <p class="giInfo">
                                        <?=(empty($this->albumUser) ? 'Photo: ' : 'Author: ') .$item['attr']['author']?>
                                    </p>
                                <?endif?>

                                <div class="block-core-ItemInfo giInfo">
                                </div>

                                <style type="text/css">
                                    @import url(http://www.google.com/cse/api/branding.css);
                                </style>
                                <div class="cse-branding-right" style="background-color:#FFFFFF;color:#000000">
                                    <div class="cse-branding-form">
                                        <form action="http://www.google.com/cse" id="cse-search-box" target="_blank">
                                            <div>
                                                <input type="hidden" name="cx" value="partner-pub-2927623730838808:7208357013">
                                                <input type="hidden" name="oq" value="">
                                                <input type="hidden" name="gsc.q" value="">
                                                <input type="hidden" name="gsc.page" value="1">
                                                <input type="text" name="q" size="55">
                                                <input type="submit" name="sa" value="Search">
                                            </div>
                                        </form>
                                    </div>
                                    <div class="cse-branding-logo">
                                        <img src="http://www.google.com/images/poweredby_transparent/poweredby_FFFFFF.gif" alt="Google">
                                    </div>
                                    <div class="cse-branding-text">
                                        Custom Search
                                    </div>
                                </div>

                                <a href="javascript:explorePuzzle();" alt="Explore" title="Explore"></a>
                            </div></div></div></div></div>

                            <!--<div class="gbBlock gcBorder1">
                                <p class="giTitle">
                                    <h2 style="font-size:1.2em;"><?/*=@$album->title*/?></h2>
                                </p>
                                <?/*if(!empty($item['title'])):*/?>
                                    <p class="giTitle"><?/*=$item['title']*/?></p>
                                <?/*endif*/?>
                                <p class="giTitle">
                                    <?/*=$item['cut']['name']?$item['cut']['name']:Yii::app()->params['defaultCutout']*/?>
                                </p>
                                <?/*if (!empty($item['attr']['author'])):*/?>
                                    <p class="giInfo">Photo: <?/*=$item['attr']['author']*/?></p>
                                <?/*endif*/?>
                                <?/*if(!empty($item['attr']['description'])):*/?>
                                    <p class="giDescription"><?/*=$item['attr']['description']*/?></p>
                                <?/*endif*/?>
                            </div>-->

                            <!-- Show the Left-column-lower-part photo blocks -->
                            <div id="gsLeftLowerBlock">
                                <div class="gbBlock gcBorder1"><?$this->widget('potdFeatured')?></div>
                                <div class="gbBlock gcBorder1">
                                    <div id="gbBlockDownloadEJ">
                                        <a href="http://kraisoft.com/files/everydayjigsaw.exe">
                                            <strong>Ready for more?</strong>
                                            <br>
                                            More than 12000 puzzles with up to thousands of pieces.<br>
                                            All-in-one download.
                                        </a>
                                    </div>
                                    <div id="gbBlockDownloadEJMac">
                                        <a target="_blank" href="http://itunes.apple.com/app/everyday-jigsaw/id479491701?ls=1&amp;mt=12">
                                        </a>
                                    </div>
                                    <script type="text/javascript" language="JavaScript">
                                        if (getClientOS()=="Mac OS X"){
                                            document.getElementById("gbBlockDownloadEJ").style.display="none";
                                            document.getElementById("gbBlockDownloadEJMac").style.display="block";
                                        }
                                    </script>
                                </div>
                            </div>
                        <!--</div>-->
                        </td>



<td valign="top" align="center">
    <div id="gsImageView" class="gbBlock gcBorder1">
        <table align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="top">
                    <div style="position:relative;width:730px;height:550px;">
                        <div style="position:absolute;top:0;left:0;">
                            <object id="puzzleObject" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="730" height="550" align="middle">
                                <param name="name" value="puzzleObject" />
                                <param name="allowScriptAccess" value="sameDomain" />
                                <param name="allowFullScreen" value="true" />
                                <param name="movie" value="/flash/puzzle.swf" />
                                <param name="play" value="false" />
                                <param name="loop" value="false" />
                                <param name="quality" value="high" />
                                <param name="bgcolor" value="#ffffff" />
                                <param name="wmode" value="opaque" />
                                <param name="FlashVars" value="<?=$paramToFlash?>" />
                                <embed src="/flash/puzzle.swf" swliveconnect="true" name="puzzleObject" play="false" loop="false" quality="high" bgcolor="#ffffff" wmode="opaque" width="730" height="550" align="middle" allowscriptaccess="sameDomain" allowfullscreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="<?=$paramToFlash?>" />
                            </object>
                        </div>
                        <script type="text/javascript"><!--
                            initMouseWheel();//-->
                        </script>
                        <?if ($this->afgEnabled):?>
                            <script type="text/javascript"><!--
                                window.setTimeout(removeAdSwf, 30000);
                                document.write("<div style=\"position:absolute;top:0;left:0;\">\r\n<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http:\/\/download.macromedia.com\/pub\/shockwave\/cabs\/flash\/swflash.cab#version=10,0,0,0\" width=\"730\" height=\"550\" id=\"preloader\" align=\"middle\">\r\n<param name=\"allowScriptAccess\" value=\"always\" \/>\r\n<param name=\"allowFullScreen\" value=\"false\" \/>\r\n<param name=\"movie\" value=\"\/flash\/afg_preloader.swf\" \/>\r\n<param name=\"quality\" value=\"high\" \/>\r\n<param name=\"bgcolor\" value=\"#ffffff\" \/>\r\n<param name=\"flashvars\" value=\"publisherId=ca-games-pub-4968145218643279&descriptionUrl=http%3a%2f%2fexample.com\/something.html&adtest=on&maxDuration=20000\">\r\n<embed src=\"\/flash\/afg_preloader.swf\"\r\nquality=\"high\" bgcolor=\"#000000\"\r\nwidth=\"730\" height=\"550\"\r\nname=\"preloader\"\r\nalign=\"middle\" allowScriptAccess=\"always\"\r\nallowFullScreen=\"false\"\r\ntype=\"application\/x-shockwave-flash\"\r\nflashVars=\"publisherId=ca-games-pub-4968145218643279&descriptionUrl=http%3a%2f%2fexample.com\/something.html&adtest=on&maxDuration=20000\"\r\npluginspage=\"http:\/\/www.adobe.com\/go\/getflashplayer\" \/>\r\n<\/object>\r\n<\/div>");
                                //-->
                            </script>
                        <?endif?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?$this->widget('FlashPager', array('item' => $item, 'album'=>$album));?></td>
            </tr>
        </table>

    </div>

    <div style="clear:both;"></div>
    <?$this->widget('savedPuzzle', array('page'=>'item')); //Save and Load Block ?>
</div>
<script type="text/javascript"><!--
    preloadImages('/images/save_puzzle_over.png, /images/load_puzzle_over.png');
    updateLoadBlock();//-->
</script>
</td>

<td align="left" width="190px" valign="top">
    <div class="gsContentDetail">
        <!-- Show the right-column-upper-part photo blocks -->
        <div id="gsRightUpperBlock">
            <div style="width:190px;padding:1px; margin-top:5px;margin-bottom:10px;background-color:#B4C6D6" class="gbBlock gcBackground2 gcBorder1" id="sidebar">
                <table cellspacing="0" cellpadding="0">
                    <tbody><tr>
                        <td align="left" class="gbBreadCrumbBackground" style="padding-left:5px;">
                            <h2>Actions</h2>
                        </td>
                        <td align="right" class="gbBreadCrumbBackground" style="padding-right:2px;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="padding: 5px" class="gcBackground1 iceSidebarBG iceSidebarBGImage">
                                <div class="gcBorder1" id="gsSidebar">
                                    <?$this->widget('userActions', array('album'=>$album, 'item'=>$item, 'type'=>'item',))?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody></table>
            </div>
        </div>

        <!-- Show the right-column-lower-part photo blocks -->
        <div id="gsRightLowerBlock">
            <!--<div class="gbPayPalDonate">
                <form method="post" action="https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" value="wadim@kraisoft.com" name="business">
                    <input type="hidden" value="_donations" name="cmd">
                    <input type="hidden" value="TheJigsawPuzzles.com" name="item_name">
                    <input type="hidden" value="TheJigsawPuzzles.com" name="item_number">
                    <input type="hidden" value="USD" name="currency_code">
                    <input type="hidden" value="US" name="country">
                    <input type="image" border="0" alt="PayPal - The safer, easier way to pay online" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" name="submit">
                    <img width="1" height="1" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt="">
                </form>
                to help us support the site and add features
            </div>-->

            <div class="gbHelpLink"><a href="/info/help" target="_blank">Got questions? Get Help!</a></div>

            <?$this->widget('searchBlock', array('albumUrl'=>@$album->componentUrl, 'itemUrl'=>@$item->componentUrl))?>
            <?if (empty($this->albumUser)):?>
            <div class="gbBlock gcBorder1" style="padding-left:60px;">
                <script type="text/javascript"><!--
                    google_ad_client = "ca-pub-2927623730838808";
                    /* Puzzle Pages - 300x250 */
                    google_ad_slot = "1681252516";
                    google_ad_width = 300;
                    google_ad_height = 250;
                    //-->
                </script>
                <script type="text/javascript"
                        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                </script>
            </div>
            <?endif?>
            <?/*if (isset($item['attrKeywords'])) // Для DAO
                $kw = explode(',', $item['attrKeywords']);
            elseif(isset($item['attr']['keywords'])) // Для AR + relations
                $kw = explode(',', $item['attr']['keywords']);*/

            if (!empty($item['attr']['keywords'])): // Для AR + relations ?>
            <div class="block-keyalbum-KeywordLinks gbBlock gcBorder1">
                <?$keywords = CItemUtils::afterFindAttr($item['attr']['keywords']);?>
                Keywords:
                <?foreach($keywords as $kw):?>
                <a href="/key/<?=$kw[0]?>"><?=$kw[1]?></a>
                <?endforeach?>
            </div>
            <?elseif (!empty($item['attrKeywords'])): // Заплатка Для DAO
                $keywords = CItemUtils::afterFindAttr($item['attrKeywords']);?>
                Keywords:
                <?foreach($keywords as $kw):?>
                    <a href="/key/<?=$kw[0]?>"><?=$kw[1]?></a>
                <?endforeach?>
            <?endif?>

            <div class="gbBlock gcBorder1">
                <?#=$this->renderFile('./././items/static/linksShare')?><!-- Ссылки на facebooks -->
                <div class="gbBlock gcBorder1" style="margin-left:0;margin-right:0;width:180px;">
                    <!-- AddThis Button BEGIN -->
                    <script type="text/javascript">
                        var addthis_config = {
                            data_ga_property: 'UA-145143-3',
                            data_track_clickback: true
                        };
                    </script>
                    <div class="addthis_toolbox addthis_default_style">
                        <a href="http://addthis.com/bookmark.php?v=250&username=thejigsawpuzzles" class="addthis_button_compact">Share</a>
                        <span class="addthis_separator">|</span>
                        <a class="addthis_button_facebook"></a>
                        <a class="addthis_button_twitter"></a>
                        <a class="addthis_button_stumbleupon"></a>
                        <a class="addthis_button_email"></a>
                        <a class="addthis_button_favorites"></a>
                    </div>
                    <div class="addthis_toolbox addthis_default_style" style="margin-top:1.0em; white-space:nowrap;">
                        <a class="addthis_button_facebook_like" fb:like:layout="button_count" fb:like:locale="en_US"></a>
                        <a class="addthis_button_google_plusone" g:plusone:size="small" g:plusone:href="http://thejigsawpuzzles.com" style="padding-top:2px; margin-left: 20px;"></a>
                    </div>
                    <div class="addthis_toolbox addthis_default_style" style="margin-top:1.0em; white-space:nowrap;">
                        <a class="addthis_button_pinterest_pinit" pi:pinit:url="<?=Yii::app()->getBaseUrl(true).Yii::app()->request->url ?>" pi:pinit:media="<?=$pathToImage?>" pi:pinit:description="<?=@$this->pageTitle?>" pi:pinit:layout="horizontal"></a>
                    </div>
                    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=thejigsawpuzzles"></script>
                    <!-- AddThis Button END -->
                </div>
            </div>
        </div>
    </div>

</td>
</tr>
</table>
<?//Добавлены, чтобы сместить systemLinks ниже по синий фон?>
</div>
</td>
</tr></table>

<!--  Вспомогательные поля -->
<input type="hidden" id="inpAlbumName" value="<?=$album['componentUrl']?>">
<input type="hidden" id="inpItemName" value="<?=$item['componentUrl']?>">
