var flagComplete = false;
var flagError = false;
$(function(){
    $('#divMovieContainer').swfupload({
        flash_url : "/js/swfupload.swf", // Адрес флеш ролика, который создаёт кнопку загрузки
        flash9_url : "/js/swfupload_fp9.swf", // Адрес флеш ролика (v9), который создаёт кнопку загрузки
        upload_url: "/addpuzzles", // Адрес php скрипта, который принимает файлы
        //post_params: {"PHPSESSID" : "<?=session_id()?>"}, // Параметры сессии авторизованного пользователя
        file_size_limit : "3 MB", // Ограничение на объем загружаемого файла
        file_types : "*.jpg;*.png;*.gif", // Расширения загружаемых файлов
        file_types_description : "JPG Images; PNG Image; GIF Image", // Описания расширений файлов
        file_upload_limit : 0, // Ограничение кол-ва загруженных файлов (0 – любое количество)
        file_queue_limit : 0,
        custom_settings : {
            progressTarget : "fsUploadProgress",
            cancelButtonId : "btnCancel",
            thumbnail_height: 130, // Высота отмасштабированного изображения
            thumbnail_width:  130, // Ширина отмасштабированного изображения
            thumbnail_quality: 85 // Качество сжатия (jpeg)
        },
        debug: false,
        // Настройки отображения и текста кнопки
        button_image_url: "/images/swfButton.gif", // Изображение кнопки
        button_width: "75",  // Ширина кнопки в px
        button_height: "21", // Высота кнопки в px
        button_placeholder_id: "spanButtonPlaceHolder", // Контейнер для кнопки
        button_text: '<span class="theFont">Add images</span>', // Текст кнопки //Make a puzzle
        button_text_style: ".theFont { font-size: 11; border-width: 1px;font-family: Verdana,Arial,Helvetica,sans-serif;}",
        button_text_left_padding: 2, // Смещение текста слева
        button_text_top_padding: 2  // Смещение текста сверху
    })
    .live('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){ // Завершение выбора файлов
        $("#fsUploadProgress").css('display', 'block'); // Отображаем глобальный div {Selected files}
    })
    .live('fileQueued', function(event, file){ // Добавление очередного изображения
        getTemplate(file, 'divSelectedFiles'); // Вставка шаблона для контента
        })
    .live('uploadProgress', function(event, file, bytesLoaded){ //$('#log').append('<li>Upload progress - '+bytesLoaded+'</li>');
        var percentage = Math.round((bytesLoaded/file.size)*100); // Длина от целого элемента
        $('div#'+file.id).find('div.progressBarInProgress').css('width', percentage+'%');
        })
    .live('uploadComplete', function(event, file){ // Завершение загрузки
        var inpTitle = $("#inp"+file.id).val();    // Название для пазла
        var inpName  = $("#inpName"+file.id).val();// Имя файла-изображения пазла
        // Отправка формы с дополнительными полями
        $.post('/addpuzzles', {fileTitle:inpTitle, fileName:inpName, fileID:file.id}, function(data){
            if ('success' == data.result) { //$('#log').append('<li>Upload title success : ' + file.name+'</li>');
                addFileID(data.dbID); // Записываем id (из базы) объекта, для последующего перехода data.dbID
                $("#"+file.id).slideUp(350); // Удаляем контейнер закаченного пазла
                flagComplete = true; // Флаг окончания загрузки файла + post-данные
            } else {// Отмечаем контейнер красным. Выдаем ошибку.
                flagError = true;
            }
        },'json');
        //$(this).swfupload('startUpload'); // start the upload (if more queued) once an upload is complete
    });
});

