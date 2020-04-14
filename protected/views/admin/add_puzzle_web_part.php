<? // Добавление пазла. Парсинг веб-адреса ?>

<script type="text/javascript">
    $('.aUrlPreview').live('click', function(){
        var src = this.href; // Адрес изображения
        var w = window.open('/preview.html', 'Image preview'); // Создаем новое окно
        w.focus(); // Передаем ему фокус ввода
        w.onload = function(){
            var img = w.document.createElement('img'); // создать img в документе нового окна
            img.src = src; // Задаем адрес картинки
            var body = w.document.body;
            body.insertBefore(img, body.firstChild); // вставить первым элементом в новое body
        }
        return false; // Блокируем переход по ссылке
    });
</script>

<h1 style="margin-top:20px;">Add Puzzle from Web</h1>

<?=CHtml::beginForm('addPuzzles');
    if (isset($album)) {
        if (isset($album['componentUrl']))
            echo CHtml::hiddenField('album', (Yii::app()->params['userAlbumID'] == $album['parent_id'])
                    ? Yii::app()->params['userAlbumName'] .'/'. $album['componentUrl']
                    : $album['componentUrl']
            );
        elseif (is_string($album))
            echo CHtml::hiddenField('album', $album);
    }
    if (isset($images)):
        if (Yii::app()->user->hasFlash('result')):
            $flash = Yii::app()->user->getFlash('result');
            echo '<table border="0">';
            foreach ($flash as $res):?>
                <tr>
                    <td <?=('success'==$res[0])?:'style="color:red;"';?> > <?=$res[0]?>: </td>
                    <td><?=$res[1]?></td>
                </tr>
            <?endforeach;
            echo '</table>';
        endif;
        if ($cnt = count($images)):
            $i = 0; // Счетчик "полосования" таблицы
            echo '<h2> '.$cnt.' matches </h2><br />';
            echo '<table><tr style="background-color:#6FACCF;"><td></td><td>URL</td></tr>';
            foreach ($images as $img):?>
            <tr style="background-color:#<?=($i++ % 2)?'fff':'dfeffc'?>;">
                <td><?=CHtml::checkBox('chUrls['.$img['url'].']')?></td>
                <td><a href="<?=$img['url']?>" class="aUrlPreview" target="_blank"><?=$img['url']?></a></td>
            </tr>
            <?endforeach;
            echo '</table>';
            echo CHtml::submitButton('Add URLs');
        else:
            echo '<h2> 0 matches </h2><br /><br />';
            echo CHtml::textField('inpUrl', null, array('style'=>'width:500px')).'<br />';
            echo CHtml::submitButton('Find URLs');
        endif;
    else:
        echo '<h2> Input URL </h2><br /><br />';
        echo CHtml::textField('inpUrl', null, array('style'=>'width:500px')).'<br />';
        echo CHtml::submitButton('Find URLs');
    endif;
CHtml::endForm();?>
