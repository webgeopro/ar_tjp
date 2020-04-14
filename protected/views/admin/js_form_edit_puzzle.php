<?php // Javascript-код, обеспечивающий функционировании галочки рядом с Puzzle Date and Time

    /*$boolNewPuzzle = (isset($model['inSearch']) AND 2 == $model['inSearch']) // Признак нового пазла
        ? true
        : false;*/
    $boolNewPuzzle = CItemUtils::isNewPuzzle($model);

    $now = CDateTimeUtils::now(); // Текущая дата (DateTime)
    $nowMDY = CDateTimeUtils::format($now, 'F d Y'); // Текущая дата (2014-01-30)

    $next = CDateTimeUtils::modify(); // По умолчанию +1 день

    $diff = CDateTimeUtils::diff($model['dateCreated'], $next);
    $diffBool = (1 <= $diff->d) ? true : false; // Разница больше 1 дня
?>
<script type="text/javascript">
    var boolNewPuzzle = <?=$boolNewPuzzle ? 'true' : 'false'?>; // Признак нового пазла
    var diffBool = <?=$diffBool ? 'true' : 'false'?>; // Разница больше 1 дня
    /**
     * Первоначальные установки.
     * Начальное состояние галочки:
     *  - Для нового пазла: снята
     *  - Для редактирования пазла: если дата(пазла)>дата(текущий) - выставлена, иначе - снят
     */
    $(document).ready(function(){
        var chScheduled =
            <?=$boolNewPuzzle
                ? 'false'
                : ($diffBool
                    ? 'false' // '"checked"'
                    : '"checked"') //'false'
            ?>;
        $("#chScheduled").attr('checked', chScheduled); // prop()
    });
    /**
     * Реакция на изменение контрола даты
     *  - Для нового пазла: если дата(контрола)>дата(текущий) - выставляется, иначе - снимается
     *  - Для редактирования пазла: то же самое
     */
    $("#Item_dateCreated").live("change", function(){
        var inpDate = new Date($(this).val()); // Значение контрола даты, формат (F d Y)
        var nowDate = new Date('<?=$nowMDY?>');// Сегодняшняя дата (с сервера PHP), формат (F d Y)
        if (nowDate < inpDate)
            $("#chScheduled").attr('checked', 'checked');
        else
            $("#chScheduled").attr('checked', false);
    });
    /**
     * Реакция на приведение галочки в unchecked
     *  - Для нового пазла: если дата(контрола)>дата(текущий) - выставить дата(контрола)=дата(текущий)
     *  - Для редактирования пазла: то же самое
     */
    $("#chScheduled:not(:checked)").live("click", function(){
        if (diffBool) { // Есть разница дат (больше одного дня)
            $("#Item_dateCreated").val('<?=$nowMDY?>');
        }
    });
    /**
     * Обрезка лишних символов в поле ключевых слов.
     */
    $("#areaKeywords").live("click keyup", function(){ // Слушаем нажатие клавиш и клики мыши (для отслеживания вставок)
        var text_inp = $(this).val();               // Получить текст
        var max_letters = 255;                      // Максимально допустимое кол-во символов
        var remain = max_letters - text_inp.length; // Получить остаток символов
        if (text_inp.length <= max_letters) {       // Меньше допустимой длины
            if ($("#spKeywords").hasClass('red'))
                $("#spKeywords").removeClass('red');
        } else { // Больше допустимой длины
            if (0 > remain) // Превышен лимит букв
                if (!$("#spKeywords").hasClass('red'))
                    $("#spKeywords").addClass('red')
        }
        $("#spKeywords").text(remain); // Изменяем счетчик оставшихся символов
    });
</script>