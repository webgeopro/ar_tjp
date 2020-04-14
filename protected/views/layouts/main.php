<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="https://www.facebook.com/2008/fbml" xmlns:og="http://ogp.me/ns#">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<meta property="og:site_name" content="TheJigsawPuzzles.com" />
    <meta property="og:type" content="game" />
    <meta property="og:locale" content="en_US" />
    <meta property="fb:app_id" content="176873222389789" />
    
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

    <link rel="stylesheet" type="text/css" href="/css/theme.css"/>
    <link rel="stylesheet" type="text/css" href="/css/icons.css"/>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

    <title><?=CHtml::encode($this->pageTitle)?></title>


    <script type="text/javascript" src="/js/theme.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
    <script type="text/javascript"><!--
        <?if(Yii::app()->user->isGuest): echo "window.userStatus='g';";
        elseif ('admin' == Yii::app()->user->name): echo "window.userStatus='a';";  #todo: сделать проверку admin (модуль user)
        else:  echo "window.userStatus='r';";
        endif;?>

        preloadImages('/images/user_green.png, /images/submenu.png, /images/new_cutout.png, /images/preview.png');
        updateLoadBlock();
        <?if ($this->afgEnabled):?>
            function removeAdSwf() {
                document.getElementById("preloader").style.display = "none";
                var puzzleObject=getMovie("puzzleObject");
                puzzleObject.proceedToGame();
                /*Implemenation of removeAdSwf*/
            }
            function isAfgEnabled() {
                return 1;
            }
        <?else:?>
            function isAfgEnabled() {
                return 0;
            }
        <?endif?>
        //-->
    </script>

    <script type="text/javascript" src="/js/highslide/highslide-full.js"></script>
    <link rel="stylesheet" type="text/css" href="/js/highslide/highslide.css" />

    <script type="text/javascript">
        //<![CDATA[
        hs.registerOverlay({
            html: '<div class="closebutton" onclick="return hs.close(this)" title="Close"></div>',
            position: 'top right',
            fade: 2 // fading the semi-transparent overlay looks bad in IE
        });
        //]]>
    </script>

    <script type="text/javascript">
    var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-145143-3']);
        _gaq.push(['_setDomainName', 'thejigsawpuzzles.com']);
        _gaq.push(['_trackPageview']);
        (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();//-->
    </script>
    <script type="text/javascript" src="https://apis.google.com/js/plusone.js">
        {parsetags: 'explicit'}
    </script>
</head>

<body class="gallery">

    <div id="fb-root"></div>
    <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#appId=176873222389789&xfbml=1";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

    <div id="gallery">
        <div id="gsHeader" >
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="left" valign="middle">
                        <div class="gsLogo"><a href="/" onClick="return true;"></a></div>
                    </td>
                    <td width="100%" align="right" valign="top" ></td>

                    <?if (empty($this->albumUser)):?>
                    <td align="right" valign="top">
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
                    <?endif?>
                </tr>
            </table>
        </div>
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <div id="gsNavBar" class="gcBorder1">
                        <div class="gbBreadCrumb">
                            <?if(isset($this->breadcrumbs)):?>
                                <?$this->widget('zii.widgets.CBreadcrumbs', array(
                                    'homeLink' => CHtml::link('Puzzle Gallery', Yii::app()->homeUrl),
                                    'links'=>$this->breadcrumbs,
                                    'htmlOptions'=>array('class'=>'block-core-BreadCrumb'),
                                ));?>
                            <?endif?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="iceBackground iceBackgroundImage" style="padding-bottom:10px;">
                    <?=$content?>
                    <!--<div id="gsFooter" class="gcBorder1">-->

                        <div id="cutoutMenuContainer" class="cutoutMenuContainer" onMouseOut="hideCutoutMenu(event);return true;"></div>
                        <div id="popupMenuContainer" class="popupMenuContainer" onMouseOut="hidePopupMenu(event);return true;"></div>
                        <div id="popupMenuContainer2" class="popupMenuContainer" onMouseOut="hidePopupMenu(event);return true;"></div>
                        <div id="previewButtonContainer" class="previewButtonContainer" onMouseOut="hidePreviewButton(event);return true;"></div>
                        <div id="balloonForRegRight" class="balloonForRegRight"></div>
                        <div id="balloonForRegLeft" class="balloonForRegLeft"></div>



                        <!-- Start of StatCounter Code -->
                        <script type="text/javascript">
                            var sc_project=7033553;
                            var sc_invisible=1;
                            var sc_security="d14582da";
                        </script>
                        <script type="text/javascript"
                                src="http://www.statcounter.com/counter/counter.js"></script><noscript><div
                                class="statcounter"><a title="custom counter"
                                                       href="http://statcounter.com/free_hit_counter.html"
                                                       target="_blank"><img class="statcounter"
                                                                            src="http://c.statcounter.com/7033553/0/d14582da/1/"
                                                                            alt="custom counter" ></a></div></noscript>
                        <!-- End of StatCounter Code -->
                        <!-- Quantcast Tag -->
                        <script type="text/javascript">
                            var _qevents = _qevents || [];
                            (function() {
                                var elem = document.createElement('script');
                                elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
                                elem.async = true;
                                elem.type = "text/javascript";
                                var scpt = document.getElementsByTagName('script')[0];
                                scpt.parentNode.insertBefore(elem, scpt);
                            })();
                            _qevents.push({
                                qacct:"p-WU1zzER3f_BJ4"
                            });
                        </script>
                        <noscript>
                            <div style="display:none;">
                                <img src="//pixel.quantserve.com/pixel/p-WU1zzER3f_BJ4.gif" border="0" height="1" width="1" alt="Quantcast"/>
                            </div>
                        </noscript>
                        <!-- End Quantcast tag -->

                    <!--</div>-->
                    <div id="gsFooter" class="gcBorder1">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="left" width="50%">
                                    <?$this->widget('systemLinks')?>
                                </td>
                                <td align="right">
                                    &nbsp;
                                </td>
                            </tr>
                        </table>
                    </div>

                </td>
            </tr>
        </table>

        <script type="text/javascript">// <![CDATA[
            search_SearchBlock_init('Search for puzzles', 'Please enter a search term.', 'Searching in progress, please wait!');
            // ]]>
        </script>
    </div>

    <?if (empty($this->albumUser)):?>
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
    <?endif?>
    <div id="feedbackButton"><a target="_blank" href="/feedback"></a></div>
    <script type="text/javascript"><!--
            addLoadEvent(hideFeedbackButton);
        //-->
    </script>

</div><!-- page -->

</body>
</html>
