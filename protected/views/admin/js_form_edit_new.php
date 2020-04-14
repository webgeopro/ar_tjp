<?php
    $date = new DateTime();             //
    $nowDate=$date->format('m-d-Y');    //
    $date->modify('+1 day');            //
    $nextDate = $date->format('m-d-Y'); //
?>
<script type="text/javascript">
    //var time = <?=time()*100?>;
    var nowDate = new Date('<?=$nowDate?>'); //nowDate = getDate(nowDate, true);
    var nextDate = new Date('<?=$nextDate?>'); // +1 день
    var toString;
    //var inpDate; // Значение в поле ввода
    $(function(){
        var puzzleDate = new Date('<?=$model->dateCreated?>');
        if (nowDate < getDate(puzzleDate, true))
            $("#chScheduled").attr('checked', 'checked');

        $("#chScheduled").live("click", function(){
            if ($(this).is(":checked")) {
                //inpDate = $(".hasDatepicker").val(); // Сохраняем пред. значение input-a
                $(".hasDatepicker").val(getDate(nextDate, false));
            } else {
                //if (inpDate) $(".hasDatepicker").val(inpDate);
                //else
                $(".hasDatepicker").val(getDate(nowDate, false));
            }
        });

    });
    // Если дата в будущем при изменении поля ввода, взводим checkbox
    function dateChanged()
    {
        /*nowDate = getDate(nowDate, true);
         if (nowDate < getDate($(this).val(), true))
         $("#chScheduled").attr('checked', 'checked');*/ //alert('inside');
    }
    //Преобразование даты
    function getDate(date, toInt)
    {
        var str; // для хранения преобразованной даты
        var y, m, d; // Для хранения года, месяца и дня соответственно
        if (toInt) { // Обратное преобразование в миллисекунды
            str = date.getTime();
        } else { // Милисекунды в формат (YY-mm-dd)
            y = date.getFullYear();
            m = lpad(date.getMonth()+1);
            d = lpad(date.getDate());
            str = m + '-' + d + '-' + y;
        }
        return str;
    }
    // Дополнить нулями слева до двух символов
    function lpad(par)
    {   var str = String(par);
        if (2 <= str.length) return str.substr(-2); // Возвращаем два последних символа
        else if (0 == str.length) return '00';
        else return '0' + str;
    }
    /*$.datepicker({
     beforeShow: function (input, inst) {
     addPrevNextYearButtons(input);
     }
     });*/
    // Количество оставшихся символов для keywords
    function keywordsCount()
    {
        var rem = <?=$attr->keywords?>;
    }
    $("#inpKeywords").bind("click keyup", function(event){ // Слушаем нажатие клавиш и клики мыши (для отслеживания вставкок)
        var text_inp = $(this).val();               // Получить текст
        var max_letters = 255;          // Максимально допустимое кол-во символов
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