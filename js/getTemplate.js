$(document).ready(function(){
    //var swfu = $.swfupload.getInstance('#divMovieContainer');

    // Обработка клика по кнопке отмены изображения в очереди закачек
    $('.progressCancel').live('click', function(){
        var fileID = $(this).parent().parent().attr('id'); // Для движка swfupload
        var swfu = $.swfupload.getInstance('#divMovieContainer'); // Экземпляр swfupload
        var files = swfu.getStats().files_queued;
        if (1 >= files)
            $('#divSWFUploadUI').css('visibility','hidden');

        swfu.cancelUpload(fileID); // Удаление ядром swfupload файла из очереди
        $('#'+fileID).slideUp(350); // Удаление контейнера элемента
        return false;
    });
    $("#btnStartUpload").live('click', function(){
        $('#divMovieContainer').css({
            'position': 'absolute',
            'top': '-1000px'
        });
        $('#divMovieContainer').swfupload('startUpload');
    });
    // Чистка поля ввода
    /*$("input.inputTitle").live('keyup', function(event){
        $(this).val(clearTitle(this.value));
    });*/

});

/**
 * При каждом добавлении пазлa в очередь, добавляется данный контейнер.
 * @param file
 * @param targetID
 */
function getTemplate(file, targetID)
{
    $('#divSWFUploadUI').css('visibility','visible'); /*btnStartUpload*/
    var fileName = file.name.split('.'); // Убираем расширение из имени файла
    fileName = clearTitle(fileName[0]);  // Чистим название

    var template =
        //'<form action="/makeapuzzle" method="post" id="f' + file.id + '">'
        '<div class="progressWrapper" id="' + file.id + '">'
        +'    <div class="progressContainer">'
        +'        <a href="#" class="progressCancel"> </a>' //style="visibility:hidden" onclick="return clkCancel(this)"
        +'        <div class="progressName">' + file.name + '</div>'
        +'        <div class="progressBarInProgress"></div>'
        +'        <div class="progressBarStatus">&nbsp;</div>'
        +'        <div class="progressTitle">Title:' // onblur="clearTitle(this.value)"
        +'            <input type="text" class="inputTitle" value="'+fileName+'" name="Filedata[title]" id="inp'+file.id+'" style="width:280px">'
        +'        </div>'
        +'    </div>'
        +'    <input type="hidden" id="inpName' + file.id + '" name="fileID" value="' + file.name + '">'
        +'    <input type="hidden" id="inpTitle' + file.id + '" name="fileTitle" value="' + fileName + '">'
        +'</div>'
        //+'</form>'
    ;
    $(template).appendTo('#'+targetID);
    /*if (albumName.length) { // Для передачи componentUrl альбома. Учитывается при работе админа с осн. альбомами
 var tmp = '<input type="hidden" value="'+albumName+'" name="Filedata[albumName]"';
 $(tmp).appendTo('#'+targetID);
 }*/
}

/**
 * Чистка введенного названия пазла.
 * @param val
 * @return {*}
 */
function clearTitle(val)
{
    var str = val;

    str = str.replace(/^\s+$/g, ''); // Ltrim |\s+
    //str = str.toLowerCase();
    var from = "àáäâèéëêìíïîòóöôùúüûñçÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛÑÇ·/_,:;";// remove accents, swap ñ for n, etc
    var to   = "aaaaeeeeiiiioooouuuuncAAAAEEEEIIIIOOOOUUUUNC------";
    for (var i=0, l=from.length ; i<l ; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }
    str = str.replace(/[ \^\-\+]/g, '-') //
        . replace(/[&]/g, ' and ') //
        . replace(/[^a-zA-Z0-9\-]/g, '') // remove invalid chars
        . replace(/\s+/g, '-') // collapse whitespace and replace by -
        . replace(/-+/g, '-'); // collapse dashes

    return str;
}

/**
 * Добавление ID файла (после post-обработки)
 * в отсылаемый далее список файлов формы
 * @param fileID
 */
function addFileID(fileID)
{
    var template = '<input type="hidden" value="' + fileID + '" name="uploadedFile[]">';
    var cnt = $('#cntUploadedFile').val();

    $(template).appendTo('#divUploadedFile');
    $('#cntUploadedFile').val(++cnt);
}