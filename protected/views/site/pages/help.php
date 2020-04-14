<?php
$this->pageTitle='Help with TheJigsawPuzzles.com';
$this->breadcrumbs=array(
    'Help with TheJigsawPuzzles.com',
);?>

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
                                    <?$this->widget('defaultImage')?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div></td>
        </tr>
        </tbody></table>
</td>
<td>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.qc').click(function(event){
                $(this).siblings('.ac').toggle();
                event.stopImmediatePropagation();
            });
            $("a[href^=#]").click(function(){
                var anc = this.href.split('#');
                if (anc[1])
                    $("#"+anc[1]).click();

                return false;
            });
        });
    </script>
    <div id="gbHelp">
        <h1>Need help with the site?</h1>
        Just choose a topic below to get information on troubleshooting common issues, get help with playing puzzles and discover some cool features of TheJigsawPuzzles.com:
        <h1>Troubleshooting</h1>
        <div>
            <div class="qc"><a id="flash" name="flash"></a>Puzzles don't open when I click on them. I get blank square/red cross/crossed circle instead of a puzzle.</div>
            <div class="ac"><p>First of all, don't worry - you should be able to fix this issue within a minute or two with just a few mouse clicks. There's a small plug-in in your system called Adobe Flash Player (used for online games), and this plug-in got misconfigured. Now we'll get things back to normal.</p><p>Which browser do you use for opening our site?</p>
                <div>
                    <div class="qc"><img height="16" width="16" class="left" src="/images/help/ie_icon.png">I'm using Internet Explorer</div>
                    <div class="ac">You most probably need to update Flash player installed in your system using <a rel="nofollow" target="_blank" href="http://get.adobe.com/flashplayer/">this page</a>. Just click on the link and follow the on-page instructions. Once you've got the message that the Flash Player is installed/updated, try to open the puzzle again.</div>
                </div>
                <div>
                    <div class="qc"><img height="16" width="16" class="left" src="/images/help/chrome_icon.png">I'm using Google Chrome</div>
                    <div class="ac">Unfortunately the latest update rolled out Jul 31st, 2012 to all Chrome browsers contained an updated version of Flash Player causing a vast number of compatibility problems. However there is a simple workaround letting you switch to the version of Flash Player which works properly until Google and Adobe sort out these problems. Please refer to <a rel="nofollow" target="_blank" href="http://www.enounce.com/chrome-disable-pepperflash">this guide</a> for the detailed instructions on how to adjust your Chrome settings.</div>
                </div>
                <div>
                    <div class="qc"><img height="16" width="16" class="left" src="/images/help/firefox_icon.png">I'm using Firefox</div>
                    <div class="ac">You most probably need to update Flash player installed in your system using <a rel="nofollow" target="_blank" href="http://get.adobe.com/flashplayer/">this page</a>. Just click on the link and follow the on-page instructions. Once you've got the message that the Flash Player is installed/updated, try to open the puzzle again.
                        <div class="qc">I've updated Flash Player and still can't open puzzles</div>
                        <div class="ac">In case you have Real Player installed in the system the problem may be caused by a compatibility issue between Adobe Flash Player and Real Player. <a rel="nofollow" target="_blank" href="http://helpx.adobe.com/flash-player/kb/crash-leaving-browser-page-flash.html">This solution</a> should help resolve the compatibility issue without the need to uninstall  anything.</div>
                    </div>
                </div>
                <div>
                    <div class="qc"><img height="16" width="16" class="left" src="/images/help/safari_icon.png">I'm using Safari</div>
                    <div class="ac">You most probably need to update Flash player installed in your system using <a rel="nofollow" target="_blank" href="http://get.adobe.com/flashplayer/">this page</a>. Just click on the link and follow the on-page instructions. Once you've got the message that the Flash Player is installed/updated, try to open the puzzle again.</div>
                </div>
            </div>
        </div>
        <div>
            <div class="qc">I can't switch the puzzle to full screen.</div>
            <div class="ac">Just follow <a href="#flash">this solution</a> to update settings and you will be able to play full screen puzzles in no time.</div>
        </div>
        <div>
            <div class="qc"><a id="fix_save" name="fix_save"></a>I'm trying to save my unfinished puzzles but can't retrieve them later to continue playing.</div>
            <div class="ac">
                Whatever the symptoms are (load button just doesn't appear, or you open the puzzle another day and it's not there, or you click "Load" and the puzzle doesn't start from where you saved it), the cure is as follows:
                <ol>
                    <li>Make sure your browser isn't in "Private" mode (some mode with a padlock or eye icon, covering your online tracks). Private mode is good sometimes, but it also erases your saved puzzles.</li>
                    <li>Make sure your browser have cookies enabled and don't erases it automatically. You may refer to <a rel="nofollow" target="_blank" href="http://support.google.com/accounts/bin/answer.py?hl=en&amp;answer=61416">this guide from Google</a> to get cookies enabled.</li>
                    <li>Try to clear the browser cookies completely (here's the <a rel="nofollow" target="_blank" href="http://www.wikihow.com/Clear-Your-Browser's-Cookies">short guide</a> on how to do this.</li>
                    <li>If nothing helps it means you need to update Adobe Flash Player plugin installed in your system using the <a rel="nofollow" target="_blank" href="http://get.adobe.com/flashplayer/">following page</a>.</li>
                </ol>
            </div>
        </div>
        <div>
            <div class="qc"><a id="fix_cutout_menu" name="fix_cutout_menu"></a>I no longer can select the number of pieces I'd like in a puzzle. Did you change something?</div>
            <div class="ac">
                Nope, the feature's still there, it just stopped working on your computer. In most cases it takes seconds to restore this feature following the steps below:
                <ol>
                    <li>Open the same page again and press Ctrl+R keys on the keyboard. This will force refresh the site and, nine times out of ten, this is enough to bring cutout selection menu back to normal.</li>
                    <li>If the problem persists please try to clear your browser's cache by following <a rel="nofollow" target="_blank" href="http://www.wikihow.com/Clear-Your-Browser's-Cache">these instructions</a> to see if it eliminates the problem.</li>
                    <li>If the problem's still there the most probable reason for the issue is the JavaScript disabled in your browser. Please refer to <a rel="nofollow" target="_blank" href="http://www.heart.org/HEARTORG/form/enablescript.html">this guide</a> to get it re-enabled.</li>
                </ol>
            </div>
        </div>
        <div>
            <div class="qc">I no longer have this clicking sound when pieces connect. How do I bring it back?</div>
            <div class="ac">
                <p><img height="130" width="163" class="right border" src="/images/help/open_menu.jpg">First, click the "Menu" button in the Upper-left corner of the puzzle board.</p>
                <hr>
                <p><img height="187" width="130" class="right border" src="/images/help/mute.jpg">Once the menu slides down, click the third button (the one with a speaker) to mute/unmute game sounds.</p>
                <hr>
            </div>
        </div>
        <h1>Cool Features</h1>
        <div>
            <div class="qc">I need larger pieces and more space to sort it out.</div>
            <div class="ac">
                <p><img height="130" width="165" class="right border" src="/images/help/fullscreen.jpg">Prepare to meet one of the coolest features of TheJigsawPuzzles.com: the full screen mode! Just click the button in the lower-right corner (the one with four arrows) and the puzzle board will be enlarged to the whole screen. Larger and nicer pieces, more space, more fun! You just need to give it a try.</p>
                <p>Click the button again or press ESC key to return to the windowed mode.</p>
                <hr>
                <p><img height="90" width="177" class="right border" src="/images/help/zoom.jpg">And here's one more helpful feature: use zoom buttons in the upper-left corner to zoom in/out on the board - this will provide more space outside the field to sort puzzle pieces.</p>
                <hr>
                <div class="qc">Something's not working right when I switch to full screen.</div>
                <div class="ac">Just follow <a href="#flash">this solution</a> to update settings and you will be able to play full screen puzzles in no time.</div>
            </div>
        </div>
        <div>
            <div class="qc">I hate having to separate piece piles piece-by-piece. Is there a tool to move many pieces at once?</div>
            <div class="ac">
                <p>Of cource there is one. Two, actually. For a start, try this. Click and drag the mouse, not on a puzzle piece but on empty space of the board. Blue selection frame appears over pieces. Select all the pieces you want to move with this frame.</p>
                <p><img height="307" width="295" class="border" src="/images/help/select_multiple_pieces.jpg"></p>
                <p>Then just move all these pieces at once with the mouse.</p>
                <hr>
                <p><img height="130" width="163" class="right border" src="/images/help/open_menu.jpg">Wanna clear the center of the board even faster? Here's how:</p>
                <p>First, click the "Menu" button in the upper-left corner of the puzzle board.</p>
                <hr>
                <p><img height="187" width="130" class="right border" src="/images/help/to_edges.jpg">Once the menu slides down, click the topmost button to have all the spare pieces moved to the sides of the board.</p>
                <hr>
            </div>
        </div>
        <div>
            <div class="qc">I'd like a choice of a few different background colors for a puzzle. White isn't always good.</div>
            <div class="ac">
                <p><img height="130" width="163" class="right border" src="/images/help/open_menu.jpg">We've prepared a LOT of backgrounds with different colors and patterns - there's a right one for any puzzle:</p>
                <p>First, click the "Menu" button in the upper-left corner of the puzzle board.</p>
                <hr>
                <p><img height="187" width="130" class="right border" src="/images/help/change_background.jpg">Once the menu slides down, click the second button (the one with colored rectangles) as many times as you want to cycle through different background colors and patterns.</p>
                <hr>
            </div>
        </div>
        <div>
            <div class="qc">I'd like to be able to see the picture of the puzzle I'm working on while I am working on it.</div>
            <div class="ac">
                <p><img height="130" width="178" class="right border" src="/images/help/show_preview.jpg">Just click the icon in the upper-right corner of the puzzle board - this will open the preview of the completed puzzle.</p>
                <hr>
                <p><img height="200" width="278" class="right border" src="/images/help/preview.jpg">Once the preview opens you may click and drag the picture border (while holding the mouse button) to change preview size.</p>
                <p>Click the small green arrow in the corner to make the preview shrink into an icon.</p>
                <hr>
            </div>
        </div>
        <h1>How do I..?</h1>
        <div>
            <div class="qc">100 piece puzzles are child's play. I need more puzzles cut to 200 pieces. Wait, how about 300?</div>
            <div class="ac">
                <p><img height="230" width="353" class="right border" src="/images/help/cutout_menu.jpg">No problem - you choose the number and shape of pieces in just ANY puzzle. From 20 to 500 pieces - play it at any difficulty you want. Here's how:</p>
                <p>Instead of clicking a puzzle thumbnail just move your mouse over it. A menu should appear at the upper-right corner of the thumbnail. Move your mouse over the desired piece shape in the menu (we've got Classic, Elegant, Mosaic and Square pieces - try them all). Then click the number of pieces you want to cut puzzle into and wait a couple of seconds - the puzzle should load with the desired cut.</p>
                <p>Also, while the puzzle is open you may click the "Change Cut" link in the upper part of the right column of the page to change the cut.</p>
                <hr>
                <div class="qc">No menus appear when I roll my mouse over puzzle thumbnails.</div>
                <div class="ac">Don't worry; just follow <a href="#fix_cutout_menu">these steps</a> to get cutout selection menu working.</div>
            </div>
        </div>
        <div>
            <div class="qc">How can I save an unfinished puzzle so I can come back and finish it later?</div>
            <div class="ac">
                <p><img height="160" width="225" class="left border" src="/images/help/save_this_puzzle.jpg">You may click "Save this puzzle" button under an unfinished puzzle - this will save your progress with the puzzle. If you're in full screen mode you need to exit fullscreen to see the button.</p>
                <p>You can also check "autosave every minute" to always have your unfinished puzzle saved recently, just in case.</p>
                <hr>
                <p><img height="160" width="331" class="right border" src="/images/help/load_saved_puzzle.jpg">After you click "Save this puzzle" a new button ("Load saved puzzle") should appear next to it on every puzzle page and on the main page under the Puzzle of the Day. Later you may click "Load saved puzzle" button under some puzzle or on the site's main page to bring up your unfinished puzzle and resume playing.</p>
                <hr>
                <div class="qc">I click "Save this puzzle" and nothing happens.</div>
                <div class="ac">Don't worry, there's a <a href="#fix_save">step-by-step guide</a> on how to get Save feature working within minutes.</div>
            </div>
        </div>
        <div>
            <div class="qc">How do I delete a saved puzzle? I don't need it anymore.</div>
            <div class="ac">
                <p><img height="160" width="301" class="right border" src="/images/help/delete_saved_puzzle.jpg">When you move your mouse over a previously saved puzzle ("Load saved puzzle" button) a small trash can appears in the lower-right corner. Clicking it will delete your saved puzzle.</p>
                <p>Saved puzzle is also deleted when you finish it and overwritten when you save another puzzle.</p>
                <hr>
            </div>
        </div>
        <div>
            <div class="qc">How do I rotate pieces to fit into puzzle?</div>
            <div class="ac">
                <p>Actually there are no puzzles with rotating pieces on the site yet; all pieces are already aligned in the right direction. But we're thinking of adding a rotation feature in some future update.</p>
            </div>
        </div>
        <div>
            <div class="qc">How do I turn off this clicking sound when pieces connect?</div>
            <div class="ac">
                <p><img height="130" width="163" class="right border" src="/images/help/open_menu.jpg">First, click the "Menu" button in the Upper-left corner of the puzzle board.</p>
                <hr>
                <p><img height="187" width="130" class="right border" src="/images/help/mute.jpg">Once the menu slides down, click the third button (the one with a speaker) to mute/unmute game sounds.</p>
                <hr>
            </div>
        </div>
        <h1>My Stuff</h1>
        <div>
            <div class="qc">I have some great pics I'd like to turn into puzzles.</div>
            <div class="ac">
                <p>It's as easy as 1-2-3, and you'll even have your personal (private) album on the site and be able to share your puzzles with friends or family. How cool is that?</p>
                <p><img height="110" width="213" class="right border" src="/images/help/sign_up.jpg"><strong>1. </strong>First, you need an account on the site (this will be your personal space for created puzzles and other cool features). Signing up is (of course) free and only takes a minute. Click the "Sign Up" link in the upper-right corner of any page, choose a username and password and enter some details such as full name and email address (please note: the rest of the fields are optional). Double-check your email - we'll send an account activation message to this address.</p>
                <hr>
                <p><img height="110" width="213" class="right border" src="/images/help/sign_in.jpg"><strong>2. </strong>Check your email - you should now have a message in your Inbox containing account activation link. Click that link (if you are allowed to) or just copy in from the message and paste into the browser's address bar.<br>Your account is now active! Visit the site again and click the "Sign In" link in the upper-right corner of any page. Enter the username and password you chose before.</p>
                <hr>
                <p><img height="170" width="250" class="right border" src="/images/help/make_a_puzzle.jpg"><strong>3. </strong>You're all set! There's the big "Make a Puzzle" button in the upper-right part of the main page - click the button and choose a pic (or even several pics) to be turned into puzzles.</p>
                <p>Once your pic is uploaded to the site you can customize various details of your freshly made puzzle (such as title and default amount of pieces), play it, share it, delete it etc. Congratulations on your first creation!</p>
                <p>Remember: you can always find all the puzzles you made under the link "My Puzzles" in every page's header.</p>
                <hr>
            </div>
        </div>
        <div>
            <div class="qc">Why do I need to create an account on your site?</div>
            <div class="ac">
                <p>Actually, you don't have to. Playing puzzles here is not only free, it won't bother you with forms and passwords. You will only want to create your account when you want some personal stuff on TheJigsawPuzzles.com: to make puzzles from your own pics (and store it in your personal album), cut puzzles to 300, 400 and 500 pieces, mark completed puzzles (coming soon), compete for best times (coming soon), receive new puzzles via email (coming soon) and many more great "coming soon"s.</p>
                <p>Signing up for an account only takes a minute and is absolutely free (just like the rest of the site). And we certainly will never share your personal information (such as email address) with any 3rd parties. We're not into this kind of things here at TheJigsawPuzzles.</p>
            </div>
        </div>
        <div>
            <div class="qc">I've made some nice puzzles out of my pics. How do I share it with everyone on the site?</div>
            <div class="ac"><p>Unfortunately at this time we don't accept user submissions for public puzzle albums. You may only make your own puzzles (using "Make a puzzle" button) and share a link for the puzzle you created (or the whole "My Puzzles" album) with your friends and family (via email, IM, etc.) or post it on your own website.</p>
                <p>We're planning to start featuring user-created puzzles on general site pages in some future update.</p></div>
        </div>
        <h1>Still need help?</h1>
        <p>Just click <a href="/feedback">Feedback</a> (the green button sticking to the right edge of every page) and let us know. We should be able to get back to you within one business day.</p>
    </div>
    <script type="text/javascript">
        preloadImages('/images/triangle_bullet_down.png,/images/triangle_bullet_right.png');
        /*initHelpBlock(document.getElementById('gbHelp'));*/
    </script></td>
</tr>
</tbody></table>