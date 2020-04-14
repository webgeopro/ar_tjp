<?if ($this->beginCache("new_puzzles_$num", array(
    'duration'  =>$duration,
    /*'dependency'=>array(
        'class' => 'system.caching.dependencies.CDbCacheDependency',
        'sql'   => $dependency,)*/
))):?>
    <div class='block-imageblock-ImageBlock'>
        <?if (count($potd['item'])):?>
        <?$i=0;foreach($potd['item'] as $item):$i++;?>
            <?$this->widget('potdItem', array('item'=>$item, 'i'=>$i, 'actions'=>$actions,))?>
        <?endforeach?>
        <?endif;?>
    </div>
<?$this->endCache();endif?>