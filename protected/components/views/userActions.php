<div class="block-core-ItemLinks gbBlock">

    <?if (!empty($item['componentUrl'])):?>
    <div style="padding-top:5px;">
        <a onclick="showPersistentPopupMenu(event,cutoutMenu,null,'250 piece Elegant');return false;"
           class="gbAdminLink gbLink-puzzle_ChangeCut"
           href="/<?=$album['componentUrl']?>/<?=$item['componentUrl']?>">
            Change Cut
        </a>
    </div>
    <?endif?>

    <br style="clear:both;"/>

    <div style="padding-top:5px;">
        <a class="gbAdminLink gbLink-puzzle_DownloadEJ" href="http://kraisoft.com/files/everydayjigsaw.exe">
            Download All Puzzles
        </a>
    </div>

    <br style="clear:both;"/>

    <div style="padding-top:5px;">
        <a onclick="return addthis_sendto('email');" class="gbAdminLink gbLink-puzzle_SendToFriend" href="javascript:;">
            Send to Friend
        </a>
    </div>

    <?foreach($actions as $val):?>
        <div style="padding-top:2px;">
            <a class="gbAdminLink <?=$val[1]?>"
               href="<?=$val[0]?>?album=<?=$prefix.$album['componentUrl']?><?=empty($item['componentUrl'])?'':'&item='.$item['componentUrl']?><?=empty($val[4])?'':'&'.$val[4]?>"
               <?=empty($val[3])?:' onclick="return '.$val[3].'(this)"'?> >
                <?=$val[2]?>
            </a>
        </div>
    <?endforeach?>

    <?if ('admin' == $this->type):?>

    <div class="block-core-ItemLinks gbBlock gcBorder1">
        <a id="gbLink-puzzle_DownloadEJ" class="gbAdminLink gbLink-puzzle_DownloadEJ" href="http://kraisoft.com/files/everydayjigsaw.exe">Download All Puzzles</a>
        <script type="text/javascript" language="JavaScript">
            if (getClientOS()=="Mac OS X"){
                document.getElementById("gbLink-puzzle_DownloadEJ").href="http://itunes.apple.com/app/everyday-jigsaw/id479491701?ls=1&amp;mt=12";
                document.getElementById("gbLink-puzzle_DownloadEJ").target="_blank";
            }
        </script>
    </div>

    <?endif?>
</div>
<script language="javascript">
    function deletePuzzle(a){confirm("Are you sure you want to delete this puzzle?")&&$.get(a.href,"",function(a){"success"==a.result?history.back():alert("An error occurred while deleting the puzzle.")},"json");return!1}
    function makeHighlight(a){$.get(a.href,"",function(a){"success"==a.result?alert("Puzzle was set as album thumbnail."):alert("An error has occurred.")},"json");return!1}
    function deleteAlbum(a){confirm("Are you sure you want to delete this album?")&&$.post(a.href,"",function(){document.location.reload(!0)});return!1};
</script>