<?/*if ($this->beginCache($name, array(
    'duration'  =>$this->duration,
))):*/?>
<div class="block-search-SearchBlock gbBlock gcBorder1" style="margin-bottom:0;">
    <?$form=$this->beginWidget('CActiveForm', array(
        'action' => '/search',
        'id' => 'formSearchBlock',
        'method' => 'get',
        'enableAjaxValidation' => false,
    ))?>
    <!--<form method="post" action="/search" id="formSearchBlock">-->

        <?if ($this->extended):?>
        <div style="text-align:left;">
            <div style="margin:0 auto;width:450px;">
                <!--<input type="text" class="textbox" value="<?/*=$searchString*/?>"
                       name="inpSearchCriteria"
                       size="50" id="searchCriteria">-->
                <div style="float: left;">
                    <?=CHtml::textField('inpSearchCriteria', CHtml::decode($searchString), array(
                        'class' => 'textbox',
                        'size'  => 50,
                    ))?>
                    <br />
                    <?=CHtml::checkBox('inTitle', $this->inTitle, array('uncheckValue'=>0))?>
                    <?=CHtml::label('Search titles', 'inTitle')?>
                    <?=CHtml::checkBox('inAuthor', $this->inAuthor, array('uncheckValue'=>0))?>
                    <?=CHtml::label('Search authors', 'inAuthor')?>
                    <?=CHtml::checkBox('inKeywords', $this->inKeywords, array('uncheckValue'=>0))?>
                    <?=CHtml::label('Search keywords', 'inKeywords')?>
                </div>
                <div style="float:right;">
                    <?=CHtml::submitButton('Search', array('class'=>'inputTypeSubmit'))?>
                </div>
                <br style="clear:both;" /><br />
                <div id="divSearchPage">
                    <?=CHtml::hiddenField('page', $this->page)?>
                    <?=CHtml::button('« Back', array('class'=>'inputTypeSubmit', 'id'=>'btnSearchPrev'))?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?=CHtml::button('Next »', array('class'=>'inputTypeSubmit', 'id'=>'btnSearchNext'))?>
                </div>
            <?$this->endWidget()?>
            <div></div>
            </div>
        </div>
        <?else:?>
        <div style="text-align:left;margin-bottom:0;">
            <input type="hidden" value="<?=$this->albumUrl?>" name="albumUrl">
            <input type="hidden" value="<?=$this->itemUrl?>" name="itemUrl">

            <input type="text" class="textbox"
                   title="<?=$this->defaultHint?>"
                   name="inpSearchCriteria"
                   size="<?=$this->size?>" id="searchCriteria">
            <input type="image" src="/images/search_button.png" name="searchButton" id="searchButton">
            <?$this->endWidget()?>
        <?endif?>
        </div>
</div>
<!--<br style="clear:both;" />-->
<?//$this->endCache();endif?>

