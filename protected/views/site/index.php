<table class="gcBackground1 gcBorder1" align="center" cellspacing="0" cellpadding="0" width="980">
    <tr>
        <td colspan="3">
            <?//<div class="gcBorder1">$userBlockTop</div>?>
            <div class="gcBorder1"><?$this->widget('userBlock')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="3" align="center">
            <div id="gsPastDays">
                <?//$this->widget('potdRecent', array('num'=>7, ))?>
                <?=$this->renderFile('./././items/static/potdRecent')?>
            </div>
        </td>
    </tr>
    <tr valign="top">
        <td align="left" style="padding-left:10px;padding-right:5px;">
            <div id="gsAlbumList" class="gcBorder1">
                <div class="gbBlock" style="border:0;margin-bottom:9px;">
                    <?$this->widget('searchBlock')?>
                </div>
                <img src="/images/categories.jpg" alt="Categories" title="Categories" class="giHeader">
                <?//$this->widget('categories')?>
                <?=$this->renderFile('./././items/static/potdCategories')?>
            </div>
        </td>
        <td align="left" style="padding-left:5px;padding-right:10px;">
            <div id="gsWelcome" class="gcBorder1">
                <p><h1>Welcome to TheJigsawPuzzles.com,</h1>
                an ever-growing collection of free online jigsaw puzzles. Albums on the left have hundreds of free jigsaw puzzles already - feel free to explore and play it all. Or, bookmark and check this page daily for a cool Puzzle of the Day!</p>
                <p><strong>Tip:</strong> While solving a puzzle, click the button in the lower-right corner to go fullscreen - you're gonna like it.</p>
                <p><strong>Missing a feature?</strong> We're working hard on keeping ahead of other jigsaw puzzle games, and every nice idea counts. Hit "Feedback" button on the right and let us know!</p>
            </div>
            <div id="gsPuzzleOfTheDay" class="gcBorder1">
                <a href="javascript:;" onclick="return addthis_sendto('favorites');">
                    <img src="images/add_to_faves.png" alt="Add this site to Favorites" title="Add this site to Favorites" class="giHeader" style="float:right;padding-top:16px">
                </a>
                <img src="images/puzzle_of_the_day.jpg" alt="Puzzle of the Day" title="Puzzle of the Day" class="giHeader">

                <?//$this->widget('potdCurrent', array( ))?>
                <?=$this->renderFile('./././items/static/potdCurrent')?>
                <div style="float:right; margin-top:-40px; margin-right:5px; white-space:nowrap; width:173px;">
                    <?=$this->renderFile('./././items/static/linksShare')?><!-- Ссылки на facebooks -->
                </div>

                <?$this->widget('savedPuzzle', array('page'=>'index'))?> <!-- Save and Load Block -->

                <div class="gbBlockMainAdsense">
                    <p>Ads by Google:</p>
                    <script type="text/javascript"><!--
                        google_ad_client = "ca-pub-2927623730838808";
                        /* Home Page - 300x250 */
                        google_ad_slot = "4406153237";
                        google_ad_width = 300;
                        google_ad_height = 250;
                        //-->
                    </script>
                    <script type="text/javascript"
                            src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                    </script>
                </div>

                <div class="gbBlockMainDownloadEJ" id="gbBlockMainDownloadEJ">
                    <p>
                        <strong>Looking for more?</strong>
                        Get
                        <strong>Everyday Jigsaw</strong>
                        , free jigsaw puzzle game that'll rock your world. Play on all your computers and mobiles, online or offline, 10'000 puzzles with up to thousands of pieces:
                    </p>
                    <div class="gbBlockMainDownloadEJIos" id="gbBlockMainDownloadEJIos">
                        <a href="http://itunes.apple.com/app/everyday-jigsaw/id535457234?s=1&mt=8" target="_blank" rel="nofollow" alt="for iPhone and iPad" title="for iPhone and iPad"></a>
                    </div>
                    <div class="gbBlockMainDownloadEJPc" id="gbBlockMainDownloadEJPC">
                        <a href="http://kraisoft.com/files/everydayjigsaw.exe" target="_blank" rel="nofollow" alt="for PC" title="for PC"></a>
                    </div>
                    <div class="gbBlockMainDownloadEJMac" id="gbBlockMainDownloadEJMac">
                        <a href="http://itunes.apple.com/app/everyday-jigsaw/id479491701?ls=1&mt=12" target="_blank" rel="nofollow" alt="for Mac" title="for Mac"></a>
                    </div>
                </div>
            </div>

            <div style="clear:both;"></div>

            <script type="text/javascript"><!--
                preloadImages('/images/load_puzzle_over.png');
                updateLoadBlock(); //-->
            </script>
            <!-- More Games Banner -->
            <!--<div class="gbMoreGamesBlock" id="gbMoreGamesBlock">
                <a href="http://kraisoft.com/games/" target="_blank" rel="nofollow" alt="Some more games you should check out" title="Some more games you should check out"></a>
            </div>
            <div class="gbMoreGamesBlockMac" id="gbMoreGamesBlockMac">
                <a href="http://itunes.apple.com/app/everyday-jigsaw/id479491701?ls=1&mt=12" target="_blank" rel="nofollow" alt="Over 8000 puzzles in the Everyday Jigsaw game for Mac" title="Over 8000 puzzles in the Everyday Jigsaw game for Mac"></a>
            </div>
            <script language="JavaScript" type="text/javascript">
                if (getClientOS()=="Mac OS X"){
                    document.getElementById("gbMoreGamesBlock").style.display="none";
                    document.getElementById("gbMoreGamesBlockMac").style.display="block";
                }
            </script>-->

        </td>
        <td align="left" style="padding: 0 10px 0 20px;margin:0;">
            <div style="float:right;padding-right:10px;height:50px;" id="makeAPuzzleBlock">
                <?//if (!Yii::app()->user->isGuest):?>
                <a href="<?=Yii::app()->user->isGuest?'/info/registration':'/makeapuzzle'?>">
                    <img src="/images/make_a_puzzle_b.png" width="183" height="57" alt="Make a puzzle!"
                         title="Make a Puzzle!" style="">
                </a>
                <?//endif?>
            </div>

            <noscript>
                <div id="JSDisabledWarning" class="gbJSDisabledWarning">
                    Warning! You can't change puzzle difficulty, save or make puzzles until you enable JavaScript in your browser.
                    <a href="http://enable-javascript.com/" target="_blank" rel="nofollow">Here's how</a>.
                </div>
            </noscript>
            <div style="clear:both;"></div>
            <div id="gsNewPuzzles" class="gcBorder1" style="z-index:1;">
                <a href="/new-puzzles">
                    <img src="/images/new_puzzles.jpg" alt="New Puzzles" title="New Puzzles" class="giHeader">
                </a>

                <?//$this->widget('newPuzzles', array('num'=>6,))?>
                <?=$this->renderFile('./././items/static/potdNew')?>

                <h2 class="giMoreNewPuzzles">
                    <a href="/new-puzzles">More new puzzles</a>
                </h2>
            </div>
        </td>
    </tr>
</table>
