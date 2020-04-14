<? //Редактирование пазла. Обрезка
$prefix = Yii::app()->params['pathOS'];
$suffix = '/'.$model['imgUrl'].'/'.$model['imgFullName'].'.jpg'; // Окончание пути к файлу
if (file_exists($prefix . Yii::app()->params['pathSource'] . $suffix))
    $image = Yii::app()->params['pathSource'] . $suffix;
elseif (file_exists($prefix . Yii::app()->params['pathOriginal'] . $suffix))
    $image = Yii::app()->params['pathOriginal'] . $suffix;
else // Надо убрать эту часть (могут возникнуть ошибки с обрезкой)
    $image = Yii::app()->params['pathWorked'].$suffix;

$size = @GetImageSize($prefix . $image);
if (null != $size) {//die($image.print_r($size));
    $w = $size[0];
    $h = $size[1];
} elseif (!empty($model['width']) AND !empty($model['height'])) {
    $w = $model['width'];
    $h = $model['height'];
} else{
    $height = ''; $width = ''; // Если не указаны размеры устанавливаем пустые значения
}
if ($w AND $h) { // Анализ размеров
    if ($h < $w) { // альбомная ориентация
        $width = Yii::app()->params['cropSize'][0];
        $height = floor($h * $width / $w);
    } else { // портретная ориентация
        $height = Yii::app()->params['cropSize'][1];
        $width  = floor($w * $height / $h);
    }
    $ratio = $w / $width;
}
?>

<h1>Crop Puzzle</h1>
    Width: <?=($w) ? $w : $width?>,
    Height: <?=($h)? $h : $height?>

<?=CHtml::beginForm(array(
    'editPuzzle',
    'item'  => $model['componentUrl'],
    'album' => empty($albumUrl) ?: CHtml::normalizeUrl($albumUrl)))?>

    <?=CHtml::hiddenField('itemID', $model['id'])?>
    <?=CHtml::hiddenField('ratio', empty($ratio) ? 0 : $ratio);//($width && $height) ? $width / $height : 0)?>
    <?=CHtml::hiddenField('x1')?><?=CHtml::hiddenField('x2')?>
    <?=CHtml::hiddenField('y1')?><?=CHtml::hiddenField('y2')?>
    <div style="margin-top:20px;">
        <img src="<?=$image?>"
             width="<?=$width?>" height="<?=$height?>"
             id="crop_image" alt="<?=$model['title']?>" />
        <div>
            <span style="width:100px;">Width:
                <?=CHtml::textField('inpCropWidth', '', array('id'=>'inpCropWidth', 'style'=>'width:40px;'))?>
                &nbsp;&nbsp;&nbsp;
            </span>
            <span style="width:100px;">Height:
                <?=CHtml::textField('inpCropHeight', '', array('id'=>'inpCropHeight', 'style'=>'width:40px;'))?>
                &nbsp;&nbsp;&nbsp;
            </span>
            <?=CHtml::button('Set', array('id'=>'inpCropSet', 'style'=>'cursor:pointer;', 'onclick'=>'clickCropSet()'))?>
            <span style="margin-left:40px;">
                Aspect Ratio:&nbsp;
                <input type="checkbox" id="chAspectRatio" onchange="changeAspectRatio(this)" />
            </span>
            <!--<span style="width:100px;">Width: <span id="spanWidth"></span> px, &nbsp;&nbsp;&nbsp;</span>
            <span style="width:100px;">Height: <span id="spanHeight"></span> px</span>-->
        </div>
    </div>
    <hr />
    <br /><br />
    <?=CHtml::submitButton('Save')?>
<?=CHtml::endForm()?>

<script type="text/javascript">
    var selWidth, selHeight, ias, imageOptions;
    var imageWidth=<?=($w)?$w:$width?>, imageHeight=<?=($h)?$h:$height?>;
    var _x1=0, _x2=100, _y1=0, _y2=100;
    $(function () {
        ias = $('img#crop_image').imgAreaSelect({ // включаем инструмент обрезки изображения
            handles: true, // Отображать "узелки" на выделении
            instance: true, // Вернуть экземпляр объекта для последующих манипуляций
            <?if ($w):?> imageWidth: <?=$w?>,<?endif?>
            <?if ($h):?> imageHeight: <?=$h?>,<?endif?>

            onSelectChange: function (img, selection) {
                $("#inpCropWidth").val(selection.width);
                $("#inpCropHeight").val(selection.height);
                /*$("#spanWidth").html(selection.width);
                $("#spanHeight").html(selection.height);*/
            },
            onSelectEnd: function (img, selection) {
                selWidth = selection.width;   // Сохраняем в переменную ширину выделения
                selHeight = selection.height; // Сохраняем в переменную высоту выделения
                _x1 = selection.x1; // Устанавливаем координаты начала выделения
                _y1 = selection.y1; // Устанавливаем координаты начала выделения
                /*_x2 = selection.x2; // Устанавливаем координаты конца выделения
                _y2 = selection.y2; // Устанавливаем координаты конца выделения*/
                $('input[name="x1"]').val(selection.x1); // Записываем значение в форму. Лев. угол
                $('input[name="y1"]').val(selection.y1); // Записываем значение в форму. Лев. угол
                $('input[name="x2"]').val(selection.x2); // Записываем значение в форму. Прав. угол
                $('input[name="y2"]').val(selection.y2); // Записываем значение в форму. Прав. угол
            }
            /*onInit: function (img, selection) {
                imageWidth = img.imageWidth;
                imageHeight = img.imageHeight;
            }*/
        });
    });

    // Устанавливаем пропорциональное выделение
    function changeAspectRatio(ob)
    {
        var size = getAspectRatio(selWidth, selHeight);  // Получаем отношение сторон
        var ratio = size[0] + ':' + size[1];
        if (ob.checked) { // Если отмечен checkbox
            ias.setOptions({aspectRatio : ratio}); // Фиксируем соотношение сторон
            ias.update();
        } else {
            ias.setOptions({aspectRatio:''}); // Сбрасываем фиксацию соотношения сторон
        }
    }

    // Получить соотношение сторон. бОльшую принимаем за 1.
    function getAspectRatio(w, h)
    {
        var tmp;
        if (w > h) {
            first = (w / h).toFixed(1); // Ограничить одним знаком после запятой (1.2)
            second = 1;
        } else {
            first = 1;
            second = (h / w).toFixed(1);
        }
        return Array(first, second);
    }

    /**
     * Отработка клика по кнопке "Set".
     * Установка новых ширины и высоты выделения.
     * Если выделение выходит за границы изображения - выделение ограничиваем границами.
     */
    function clickCropSet()
    {
        _x2 = parseInt($("#inpCropWidth").val()); // Вторая координата
        _y2 = parseInt($("#inpCropHeight").val()); // Вторая координата
        if (imageWidth < (_x1 + _x2)) {
            _x2 = imageWidth-_x1; // Выход за границы

        }
        //$("#spanWidth").html(_x1+'_'+_x2); // Для разработки. Контроль параметров
        if (imageHeight < (_y1 + _y2)) {
            _y2 = imageHeight-_y1;// Выход за границы

        }
        //$("#spanHeight").html(_y1+'_'+_y2); // Для разработки
        ias.setSelection(_x1, _y1, _x1+_x2, _y1+_y2); // Устанавливаем новое выделение
        ias.setOptions({ show: true }); // Отобразить выделение
        ias.update(); // Обновить экземпляр imgAreaSelect

        $("#inpCropWidth").val(_x2);
        $("#inpCropHeight").val(_y2);
        $('input[name="x2"]').val(_x1+_x2); // Записываем значение в форму. Прав. угол
        $('input[name="y2"]').val(_y1+_y2); // Записываем значение в форму. Прав. угол
    }
</script>