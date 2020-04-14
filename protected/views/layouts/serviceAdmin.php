<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="https://www.facebook.com/2008/fbml" xmlns:og="http://ogp.me/ns#">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="/css/gallery.css"/>
    <link rel="stylesheet" type="text/css" href="/css/theme.css"/>
    <link rel="stylesheet" type="text/css" href="/css/icons.css"/>

</head>

<body class="gallery">

<div id="fb-root"></div>

<div id="gallery" class="gecko">
    <div id="gsHeader" >
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td align="left" valign="middle">
                    <div class="gsLogo"><a href="/" onClick="return true;"></a></div>
                </td>
                <td width="100%" align="right" valign="top" >
                </td>
                <td  align="right" valign="top">
                    <table cellspacing="0" cellpadding="0" valign="top">
                        <tr>
                            <td align="right">
                                <div style="padding:0 0 0 10px;">
                                    <!-- ValueClick Media 468x60 and 728x90 Banner CODE for TheJigsawPuzzles.com -->
                                    <script src="http://cdn.fastclick.net/js/adcodes/pubcode.min.js"></script><script type="text/javascript">document.write('<scr' + 'ipt type="text/javascript">(function () {try{VCM.media.render({sid:63255,media_id:1,media_type:5,version:"1.1"});} catch(e){document.write(\'<scr\' + \'ipt type="text/javascript" src="http://media.fastclick.net/w/get.media?sid=63255&m=1&tp=5&d=j&t=n&exc=1"></scr\' + \'ipt>\');}}());</scr' + 'ipt>');</script><noscript><a href="http://media.fastclick.net/w/click.here?sid=63255&m=1&c=1" target="_blank"><img src="http://media.fastclick.net/w/get.media?sid=63255&m=1&tp=5&d=s&c=1&vcm_acv=1.1" width=728 height=90 border=1></a></noscript>
                                    <!-- ValueClick Media 468x60 and 728x90 Banner CODE for TheJigsawPuzzles.com -->
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
<table width="100%" cellspacing="0" cellpadding="0">
    <tr><td>
        <div id="gsNavBar" class="gcBorder1">

            <div class="gbBreadCrumb">
                <div class="block-core-BreadCrumb">
                    <?if(isset($this->breadcrumbs)):?>
                    <?$this->widget('zii.widgets.CBreadcrumbs', array(
                        'homeLink' => CHtml::link('Puzzle Gallery', Yii::app()->homeUrl),
                        'links'=>$this->breadcrumbs,
                        'htmlOptions'=>array('class'=>'block-core-BreadCrumb'),
                    ));?>
                    <?endif?>
                </div>
            </div>
        </div>
    </td></tr><tr><td class="iceBackground iceBackgroundImage">
    <?=$content?>
    <div id="gsFooter" class="gcBorder1">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td align="left" width="50%">
                    <div class="gbSystemLinks">
                        <?$this->widget('systemLinks')?>
                    </div>
                </td>
                <td align="right">&nbsp;</td>
            </tr>
        </table>

        <div style="text-align:center; margin:10px;">
            <script type="text/javascript"><!--
            google_ad_client = "ca-pub-2927623730838808";
            /* Page Footer - 728x90 */
            google_ad_slot = "9261383833";
            google_ad_width = 728;
            google_ad_height = 90;
            //-->
            </script>
            <script type="text/javascript"
                    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
            </script>
        </div>
    </div>
</td></tr></table>


</div>
<div id="feedbackButton"><a href="/feedback" target="_blank"></a></div>
<script type="text/javascript"><!--
addLoadEvent(hideFeedbackButton);
//-->
</script>
<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
</body>
</html>