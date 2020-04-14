<?
    $this->pageTitle = Yii::app()->name . ' - Add to website';
    $this->breadcrumbs = $breadcrumbs;
?>

<table width="100%" cellspacing="0" cellpadding="0" class="gcBackground1">
    <tbody><tr valign="top">
        <td width="20%">
            <table cellspacing="0" cellpadding="0">
                <tbody><tr>
                    <td style="padding-bottom:5px" colspan="2">
                        <div class="gsContentDetail">
                            <div class="gbBlock gcBorder1">
                                <div class="block-imageblock-ImageBlock">
                                    <?if (@$paramToFlash)
                                        $this->widget('viewThumb', array('album'=>$this->album, 'item'=>$this->item, 'viewTitle'=>true));
                                        //$this->widget('potdItem', array('item'=>$item, 'i'=>1, 'size'=>130));
                                    else
                                        $this->widget('viewThumb', array('album'=>$this->album, 'viewTitle'=>true));
                                        //$this->widget('getAlbumThumbnail', array('album'=>$this->album));?>
                                </div>
                            </div>
                            <div class="gbBlock gcBorder1">
                                <h2> <?//todo Название миниатюры?> </h2>
                            </div>
                        </div></td>
                </tr>
                </tbody></table>
        </td>
        <td>
            <div class="gbBlock gcBackground1">
                <h2> Add to Website/Blog </h2>
            </div>
            <div id="GetUrls_<?=@$item->id?>_details">
                <div class="gbBlock">
                    <p class="gbDescription">
                        You can quickly add this puzzle to your website, post to blog or forum:
                        <br>
                        Choose a way to post a puzzle, copy the code from the appropriate box below and paste into the website editor or blog/forum posting window
                    </p>
                    <? if (@$paramToFlash): ?>

                        <div class="gbBlock">
                        <h3>HTML code (for a website or blog)</h3>

                        <p class="gbDescription">
                            Link to the puzzle:
                            <br>
                            <input type="text" value="&lt;a href=&quot;http://thejigsawpuzzles.com/<?=(Yii::app()->params['userAlbumID'] == $album['parent_id'])?Yii::app()->params['userAlbumName'].'/':''?><?=$album['componentUrl']?>/<?=$item['componentUrl']?>-jigsaw-puzzle&quot;&gt;<?=$item['title']?> puzzle on TheJigsawPuzzles.com&lt;/a&gt;" size="85" readonly="true" name="forum" onclick="this.focus(); this.select();">
                        </p>
                        <p class="gbDescription">
                            Clickable puzzle thumbnail:
                            <br>
                            <input type="text" value="&lt;a href=&quot;http://thejigsawpuzzles.com/<?=(Yii::app()->params['userAlbumID'] == $album['parent_id'])?Yii::app()->params['userAlbumName'].'/':''?><?=$album['componentUrl']?>/<?=$item['componentUrl']?>-jigsaw-puzzle&quot;&gt;&lt;img src=&quot;http://thejigsawpuzzles.com<?=Yii::app()->params['pathThumbnail']?>/<?=$item['imgUrl']?>/<?=$item['imgFullName']?>.jpg&quot; alt=&quot;<?=$item['title']?> puzzle on TheJigsawPuzzles.com&quot; title=&quot;<?=$item['title']?> puzzle on TheJigsawPuzzles.com&quot; /&gt;&lt;/a&gt;" size="85" readonly="true" name="forum" onclick="this.focus(); this.select();">
                        </p>

                        <p class="gbDescription">
                            Show the whole playable puzzle on a page:
                            <br>
                            <textarea rows="10" cols="85" readonly="true" name="forum" onclick="this.focus(); this.select();">&lt;object id="puzzleObject" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="730" height="550" align="middle"&gt;
&lt;param name="name" value="puzzleObject"&gt;&lt;/param&gt;
&lt;param name="allowScriptAccess" value="sameDomain"&gt;&lt;/param&gt;
&lt;param name="allowFullScreen" value="true"&gt;&lt;/param&gt;
&lt;param name="movie" value="http://thejigsawpuzzles.com/flash/puzzle.swf"&gt;&lt;/param&gt;
&lt;param name="play" value="false"&gt;&lt;/param&gt;
&lt;param name="loop" value="false"&gt;&lt;/param&gt;
&lt;param name="quality" value="high"&gt;&lt;/param&gt;
&lt;param name="wmode" value="opaque"&gt;&lt;/param&gt;
&lt;param name="FlashVars" value="<?=$paramToFlash?>"&gt;&lt;/param&gt;
&lt;embed src="http://thejigsawpuzzles.com/flash/puzzle.swf" swliveconnect="true" name="puzzleObject" play="false" loop="false" quality="high" wmode="opaque" width="730" height="550" align="middle" allowscriptaccess="sameDomain" allowfullscreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="<?=$paramToFlash?>"&gt;&lt;/embed&gt;&lt;/object&gt;&lt;br&gt;
&lt;span style="text-align:left;font: normal 12px Arial, Helvetica, sans-serif; line-height:25px;"&gt;&lt;a href="http://thejigsawpuzzles.com" target="_blank" style="color:#666666; text-decoration:none;"&gt;&amp;raquo; More free online jigsaw puzzles at TheJigsawPuzzles.com&lt;/a&gt;&lt;/span&gt;
                            </textarea>
                        </p>

                    </div>
                    <div class="gbBlock">
                        <h3>BBCode (for a forum)</h3>
                        <p class="gbDescription">
                            Link to the puzzle:
                            <br>
                            <input type="text" value="[url=http://thejigsawpuzzles.com/<?=(Yii::app()->params['userAlbumID'] == $album['parent_id'])?Yii::app()->params['userAlbumName'].'/':''?><?=$album['componentUrl']?>/<?=$item['componentUrl']?>-jigsaw-puzzle]<?=$item['title']?> puzzle on TheJigsawPuzzles.com[/url]" size="85" readonly="true" name="forum" onclick="this.focus(); this.select();">
                        </p>
                        <p class="gbDescription">
                            Clickable puzzle thumbnail:
                            <br>
                            <input type="text" value="[url=http://thejigsawpuzzles.com/<?=(Yii::app()->params['userAlbumID'] == $album['parent_id'])?Yii::app()->params['userAlbumName'].'/':''?><?=$album['componentUrl']?>/<?=$item['componentUrl']?>-jigsaw-puzzle][img]<?=Yii::app()->params['pathThumbnail']?>/<?=$item['imgUrl']?>/<?=$item['imgFullName']?>.jpg[/img][/url]" size="85" readonly="true" name="forum" onclick="this.focus(); this.select();">
                        </p>
                    </div>

                    <?else: // Отображаем ссылки на альбом 4440ed9bd98ad44807c219fc7675d19a?>
                    <div class="gbBlock">
                        <?if (null == $album):?>
                            <h3>Album doesn't exists.</h3>
                        <?else:?>
                            <h3>HTML code (for a website or blog)</h3>
                            <p class="gbDescription">
                                Link to the album:<br>
                                <input type="text" value="&lt;a href=&quot;http://thejigsawpuzzles.com/<?=(Yii::app()->params['userAlbumID'] == $album['parent_id'])?Yii::app()->params['userAlbumName'].'/':''?><?=$album['componentUrl']?>-jigsaw-puzzle&quot;&gt;<?=$album['title']?> puzzle on TheJigsawPuzzles.com&lt;/a&gt;" size="85" readonly="true" name="forum" onclick="this.focus(); this.select();">
                            </p>
                            <p class="gbDescription">
                                Clickable album thumbnail:
                                <br>
                                <input type="text" value="&lt;a href=&quot;http://thejigsawpuzzles.com/<?=(Yii::app()->params['userAlbumID'] == $album['parent_id'])?Yii::app()->params['userAlbumName'].'/':''?><?=$album['componentUrl']?>-jigsaw-puzzle&quot;&gt;&lt;img src=&quot;http://thejigsawpuzzles.com<?=Yii::app()->params['pathThumbnail']?>/<?=$album['imgUrl']?>/<?=$album['imgFullName']?>.jpg&quot; alt=&quot;<?=$album['title']?> puzzle on TheJigsawPuzzles.com&quot; title=&quot;<?=$album['title']?> puzzle on TheJigsawPuzzles.com&quot; /&gt;&lt;/a&gt;" size="85" readonly="true" name="forum" onclick="this.focus(); this.select();">
                            </p>
                            <h3>BBCode (for a forum)</h3>
                            <p class="gbDescription">
                                Link to the album:
                                <br>
                                <input type="text" value="[url=http://thejigsawpuzzles.com/<?=(Yii::app()->params['userAlbumID'] == $album['parent_id'])?Yii::app()->params['userAlbumName'].'/':''?><?=$album['componentUrl']?>-jigsaw-puzzle]<?=$album['title']?> puzzle on TheJigsawPuzzles.com[/url]" size="85" readonly="true" name="forum" onclick="this.focus(); this.select();">
                            </p>
                            <p class="gbDescription">
                                Clickable album thumbnail:
                                <br>
                                <input type="text" value="[url=http://thejigsawpuzzles.com/<?=(Yii::app()->params['userAlbumID'] == $album['parent_id'])?Yii::app()->params['userAlbumName'].'/':''?><?=$album['componentUrl']?>-jigsaw-puzzle][img]<?=Yii::app()->params['pathThumbnail']?>/<?=$album['imgUrl']?>/<?=$album['imgFullName']?>.jpg[/img][/url]" size="85" readonly="true" name="forum" onclick="this.focus(); this.select();">
                            </p>
                        <?endif?>
                    <?endif?>

                </div></div></td>
    </tr>
    </tbody></table>

