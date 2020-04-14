<?if ($this->beginCache($name, array( // Кешируем блок (только ограничение по времени)
    'duration'  =>$duration,
))):?>
    <div class='block-imageblock-ImageBlock'>
        <?if (null != $itemID):
            list($imgFullName, $imgUrl) = CImageSize::getPath($itemID);?>
            <div class="one-image">
                <div class="giThumbnailContainer">
                    <a href="/">
                        <img src="<?=Yii::app()->getBaseUrl(true)?>/items/thumbnail/<?=$imgUrl?>/<?=$imgFullName?>.jpg" />
                    </a>
                </div>
            </div>
        <?endif;?>
    </div>
<?$this->endCache();endif?>