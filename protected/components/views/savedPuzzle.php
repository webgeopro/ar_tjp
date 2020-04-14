<?if('index' != $this->page): // Отображение индексной страницы?>
    <div id="gbSaveLoadBlock" class="gbSaveLoadBlock" style="padding:0.5em;margin-top:15px;">
        <div class="gbSaveBlock" id="saveBlock">
            <a href="javascript:savePuzzle();">
                <img src="/images/save_puzzle.png"
                     onMouseOver="this.src='/images/save_puzzle_over.png';"
                     onMouseOut="this.src='/images/save_puzzle.png';"
                     alt="Save this puzzle" title="Save this puzzle">
            </a>
            <div class="gbAutoSave" id="autoSaveBlock">
                <input type="checkbox" name="autosave" value="autosave" onClick="updateAutoSave(this);">
                autosave every minute
            </div>
            <div class="gbSaveStatus" id="saveStatusBlock">
                <noscript>
                    (you need to <a href="http://enable-javascript.com/" target="_blank" rel="nofollow">enable JavaScript</a>)
                </noscript>
            </div>
            <script type="text/javascript">
                document.getElementById("autoSaveBlock").style.display="block";
                document.getElementById("saveStatusBlock").style.display="none";
            </script>
        </div>
<?else:?>
    <div class="gbMainPageLoadBlock">
<?endif;?>

    <!--<div class="gbMainPageLoadBlock">-->
        <div class="gbLoadBlock" id="loadBlock"
             onMouseOver="document.getElementById('loadBlockImage').src='/images/load_puzzle_over.png';document.getElementById('savedPuzzleThumb').className='active';" onMouseOut="document.getElementById('loadBlockImage').src='/images/load_puzzle.png';document.getElementById('savedPuzzleThumb').className='';">
            <a href="javascript:loadPuzzle();"><img id="loadBlockImage" src="/images/load_puzzle.png" alt="Load saved puzzle" title="Load saved puzzle">
                <div id="savedPuzzleThumbBlock">
                    <!--<img id="savedPuzzleThumb" src="/images/spacer.gif">-->
                    <img id="savedPuzzleThumb"
                         src="<?=Yii::app()->params['pathThumbnail'].'/'.@$this->item->imgUrl.'/'.@$this->item->imgFullName?>.jpg"
                         width="<?=@$this->item->width?>" height="<?=@$this->item->height?>"
                        />
                </div>
                <div class="gbSavedPuzzleName" id="savedPuzzleNameBlock"><?=@$this->item->title?></div>
                <a id="deleteSavedPuzzleIcon" alt="Delete saved puzzle" title="Delete saved puzzle" href="javascript:deleteSavedPuzzle();"></a>
            </a>
        </div>


</div>