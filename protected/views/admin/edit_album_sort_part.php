<? // Редактирование порядка сортировки альбомов ?>

<h1>Edit albums sort order</h1>

<?if (isset($albums) AND count($albums)):
    echo CHtml::form('/admin/editalbum');?>
        <div id="divAlbumsSortOrder">
            <?foreach ($albums as $alb):
                list($width, $height)=CImageSize::getSizeById($alb, 100, 100);?>
                <div class="albumSortOrder"  id="<?='daso_'.$alb['sort']?>" name="<?=$alb['sort']?>" >
                    <a href="" id="aaso_<?=$alb['id']?>" title="<?=$alb['title']?>">
                    <img src="<?=Yii::app()->params['pathThumbnail'].'/'.$alb['imgUrl'].'/'.$alb['imgFullName']?>.jpg"
                         width="<?=$width?>" height="<?=$height?>" />
                    </a>
                    <?=CHtml::hiddenField('albums['.$alb['id'].']', $alb['sort'])?>
                </div>
            <?endforeach?>
        </div>
        <br style="clear:both;" />
        <?=CHtml::submitButton('Save order');
    echo CHtml::endForm();
endif;?>

<script type="text/javascript">
    $(function(){
        var first='', current=''; // Id первого и текущего отмеченных элементов
        var tmp; // Временно хранилище контента первого элемента

        $("#divAlbumsSortOrder a[id]").live('click', function(){ // Обработка клика по ссылке
            current = $(this).attr('id'); // Id текущего элемента
            if ('' != first) { // Первый элемент уже выбран
                var firstTd = $("#"+first).parent();  // Берем позицию первого элемента
                if (first == current) { // Если второй выбранный элемент тот же самы, снимаем выделение, обнуляем
                    firstTd.removeClass('asoRed'); // Удаляем выделение рамкой
                    first=''; // Удаляем отметку первого элемента
                } else { // Перемещение
                    var currentTd = $("#"+current).parent();// Берем позицию текущего элемента
                    var currentId = currentTd.attr('id');   // Значение аттрибута name текущего элемента
                    // ---------------------- Меняем местами html содержимое parent td
                    firstTd.insertBefore(currentTd); // Вставка перед выбранным элементом
                    // ------------ Меняем аттрибут name и td (ID::sort) -> updateByPk
                    currentTd.attr('id', firstTd.attr('id')+"::1");
                    firstTd.attr('id', currentId+"::1");
                    // ----------------------- CSS снимаем выделение с обоих элементов
                    currentTd.removeClass('asoRed');
                    firstTd.removeClass('asoRed');
                    first = ''; // Обнуляем
                    $(".albumSortOrder").each(function( index ) { // Новый порядок сортировки
                       $(this).find('input').val((parseInt(index) +1) * 1000);
                    });
                }
            } else { // Первый элемент еще не выбран
                // Отмечаем первый выбранный элемент
                first = current;
                tmp = $("#"+first).parent().html(); // Получаем содержимое элемента
                tmp = $("#"+first).parent().addClass('asoRed');
            }
            //alert(first+'::'+current);
            return false;
        });
    });
</script>