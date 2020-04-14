/**
 * Общий файл сценариев для всех страниц.
 * User: Vah
 * Date: 20.08.12
 */

$(document).ready(function(){

    /** ==============================================
     * Сценарии для страницы просмотра пазла
     * site/item <= /albumName/ItemName
     * ===============================================
     */

    /**
     * Отработка клика по ссылке make-highlight
     * Установить пазл миниатюрой альбома
     */
    $('#aMakeHighlight').click(function(){
        $.get(
            "/service/make-highlight",
            {
                'album' : $("#inpAlbumName").val(),
                'item'  : $("#inpItemName").val()
            },
            function (data) {
                if (data['result'] == 'success') {
                    alert('Выполнено. \nПазл установлен как миниатюра альбома.');
                } else if (data['result'] == 'errorNotInAlbum') {
                    alert('Ошибка: пазл не принадлежит текущему альбому.');
                } else if (data['result'] == 'errorAccess') {
                    alert('Недостаточно прав для внесения измений.');
                } else if (data['result'] == 'errorSave') {
                    alert('Произошла ошибка при сохранении миниатюры.');
                } else if (data['result'] == 'errorPOST') {
                    alert('Ошибка передачи данных. \nПопробуйте еще раз.');
                } else {
                    alert('При авторизации произошла ошибка.');
                }
            },
            "json");
        return false;
    });
    /**
     * Отработка клика по ссылке delete-puzzle
     * Удаление пазла
     */
    $('#aDeletePuzzle').click(function(){
        if (confirm('Вы действительно хотите удалить пазл?')) {
            $.get(
                "/service/delete-puzzle",
                {
                    'album' : $("#inpAlbumName").val(),
                    'item'  : $("#inpItemName").val()
                },
                function (data) {
                    if (data['result'] == 'success') {
                        alert('Выполнено. \nПазл удален.');
                        window.location.href = data['returnUrl'];
                    } else if (data['result'] == 'errorAccess') {
                        alert('Недостаточно прав для удаления пазла.');
                    } else if (data['result'] == 'errorDelete') {
                        alert('Произошла ошибка при удалении пазла.');
                    } else {
                        alert('При авторизации произошла ошибка.');
                    }
                },
                "json");
        }
        return false;
    });

    /** ==============================================
     * Сценарии для страницы редактирования пазла
     * site/item <= /albumName/ItemName
     * ===============================================
     */
    var angleQuant = 90;
    var imageAngle1, imageAngle0;
    /**
     * Вращение миниатюры по часовой стрелке
     */
    $('#aRotateCW').click(function(){
        imageAngle0  = parseInt($('#inpRotateAngle').val());
        imageAngle1 = imageAngle0 + angleQuant;
        if (360 == imageAngle1)
            imageAngle1 = 0;
        $('#inpRotateAngle').val(imageAngle1);
        $("#thumbnail_1").rotate({
            duration: 1000,
            angle: imageAngle0,
            animateTo: imageAngle1
        });
        return false;
    });
    /**
     * Вращение миниатюры против часовой стрелке
     */
    $('#aRotateCCW').click(function(){
        imageAngle0 = parseInt($('#inpRotateAngle').val());
        imageAngle1 = imageAngle0 - angleQuant;
        if (-360 == imageAngle1)
            imageAngle1 = 0;
        $('#inpRotateAngle').val(imageAngle1);
        $("#thumbnail_1").rotate({
            duration: 1000,
            angle: imageAngle0,
            animateTo: imageAngle1
        });
        return false;
    });

    /** ==============================================
     * Сценарии для страницы редактирования пазла после makeAPuzzle
     * service/edit-puzzles
     * ===============================================
     */
    //var angleQuant = 90;
    //var imageAngle1, imageAngle0;
    /**
     * Вращение миниатюры по часовой стрелке
     */
    $('.aRotateCW').click(function(event){
        var itemID  = this.id.split('_').pop(); // Получаем id (aRotateCW_654654)
        imageAngle0 = parseInt($('#inpRotateAngle_'+itemID).val());
        imageAngle1 = imageAngle0 + angleQuant;
        if (360 == imageAngle1)
            imageAngle1 = 0;
        $('#inpRotateAngle_'+itemID).val(imageAngle1);
        $('#thumbnail_'+itemID).rotate({
            duration: 1000,
            angle: imageAngle0,
            animateTo: imageAngle1
        });
        event.stopImmediatePropagation(); // Останавливаем дальнейшее распространение события
        return false;
    });
    /**
     * Вращение миниатюры против часовой стрелке
     */
    $('.aRotateCCW').click(function(event){
        var itemID  = this.id.split('_').pop();
        imageAngle0 = parseInt($('#inpRotateAngle_'+itemID).val());
        imageAngle1 = imageAngle0 - angleQuant;
        if (-360 == imageAngle1)
            imageAngle1 = 0;
        $('#inpRotateAngle_'+itemID).val(imageAngle1);
        $("#thumbnail_"+itemID).rotate({
            duration: 1000,
            angle: imageAngle0,
            animateTo: imageAngle1
        });
        event.stopImmediatePropagation();
        return false;
    });

    /** ==============================================
     * Сценарии для страницы переноса пазла
     * ===============================================
     */

    /**
     * Удаление пазла из альбома
     */
    $('.aDeleteAlbumItem').click(function(){
        $('#inpDeleteAlbumItemID').val($(this).attr('name'));

        $('#formMovePuzzle').submit();

        return false;
    });

    /** ==============================================
     * Сценарии для страницы редактирования пользователей
     * ===============================================
     */

    /**
     * Добавление адреса возврата в адресную строку
     */
    /*$('.aProfileEdit').click(function(){
        var href = decodeURIComponent(location.href);
        var params = href.split('?');
        location.href = '/user/profile/admin?id='+this.name+'&address=' + params[1];
        //alert(href); // location.host +
        return false;
    });*/

    /**
     * Изменение статуса пользователя
     */
    $('.aStatusChange[name]').live('click', function(){
        var params = this.name.split('::');
        var td = $(this).parent('td');
        $.get(
            "/admin/user/statusChange", {'id' : params[0], 'status' : params[1]},
            function (data) { // Получаем JSON
                if (data['result'] == 'success') {//alert('<img src="/images/admin/'+data['statusNew']+'.png"></a>');
                    var tdContent = data['text']
                        + '<a class="aStatusChange" href=""'
                            + ' name="' + params[0] + '::' + data['statusNew'] + '"'
                            //+ ' title="'+ data['statusOld'] + '"'
                            + ' title="'+ data['statusNew'] + '"'
                            + ' style="float:right;">'
                        + '<img src="/images/admin/'+data['statusNew']+'.png"></a>';
                    td.html(tdContent); // Обновляем td (parent элемента)
                } else {
                    alert('При смене статуса произошла ошибка.');
                }
            },
            "json");
        //alert('CliCk');
        return false;
    });
});