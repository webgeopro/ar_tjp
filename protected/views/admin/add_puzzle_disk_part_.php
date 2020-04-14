<? // Добавление пазла. Локальный диск
$this->breadcrumbs=array(
    'New Options',
    'Add Puzzles',
);
?>
<script type="text/javascript">
    var flagComplete = false;
    var flagError = false;
    $(function(){

        $('#divMovieContainer').swfupload({
            flash_url : "/js/swfupload.swf", // Адрес флеш ролика, который создаёт кнопку загрузки
            flash9_url : "/js/swfupload_fp9.swf", // Адрес флеш ролика (v9), который создаёт кнопку загрузки
            upload_url: "/upload.php", // Адрес php скрипта, который принимает файлы
            post_params: {
                "PHPSESSID" : "<?=session_id()?>",
                "<?=Yii::app()->request->csrfTokenName?>": "<?=Yii::app()->request->csrfToken?>"
            }, // Параметры сессии авторизованного пользователя
            file_size_limit : "12 MB", // Ограничение на объем загружаемого файла
            file_types : "*.jpg;*.png;*.gif", // Расширения загружаемых файлов
            file_types_description : "JPG Images; PNG Image; GIF Image", // Описания расширений файлов
            file_upload_limit : 0, // Ограничение кол-ва загруженных файлов (0 – любое количество)
            file_queue_limit : 0,
            custom_settings : {
                progressTarget : "fsUploadProgress",
                cancelButtonId : "btnCancel",
                thumbnail_height: <?=Yii::app()->params['origSize'][1]?>, // Высота отмасштабированного изображения
                thumbnail_width:  <?=Yii::app()->params['origSize'][0]?>, // Ширина отмасштабированного изображения
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
            .live('uploadStart', function(event, file){ // Загрузка изображения //$('#log').append('<li>Upload start - '+file.progress+'</li>');
                flagComplete = false; flagError = false;
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
            .live('queueComplete', function(event, uploadCount){ // Завершение очереди скачивания. Только с плагином.
                $('#log').append('<li>Files Uploaded. Redirect</li>');
                setInterval(swfuSubmit, 2000); //setTimeout(alertSwfu, 4000);
            })
            .live('uploadError', function (event, file, errorCode, message){
                //$('#log').append('<li>++'+file.name+'!'+errorCode+'  '+message+'</li>');
            })
            .live('uploadSuccess', function(event, file){ // Завершение загрузки
                flagComplete = false; flagError = false; // Обнуляем флаги после загрузки файла на сервер
            })
            .live('uploadComplete', function(event, file){ // Завершение загрузки
                var inpTitle = $("#inp"+file.id).val();    // Название для пазла
                var inpName  = $("#inpName"+file.id).val();// Имя файла-изображения пазла
                // Отправка формы с дополнительными полями
                $.post('/makeapuzzle', {
                    fileTitle:inpTitle, fileName:inpName, fileID:file.id, albumComponentUrl:'<?=@$albumComponentUrl?>'
                    }, function(data){
                        if ('success' == data.result) { //$('#log').append('<li>Upload title success : ' + file.name+'</li>');
                            addFileID(data.dbID); // Записываем id (из базы) объекта, для последующего перехода data.dbID
                            $("#"+file.id).slideUp(350); // Удаляем контейнер закаченного пазла
                            flagComplete = true; // Флаг окончания загрузки файла + post-данные
                        } else {// Отмечаем контейнер красным. Выдаем ошибку.
                            flagError = true;
                        }
                    },'json');
                //$(this).swfupload('startUpload'); // start the upload (if more queued) once an upload is complete
            })
        ;
    });
    // Переход на страницу редактирования загруженных файлов
    function swfuSubmit()
    {
        var cnt = $('#cntUploadedFile').val(); // Кол-во закаченных файлов
        if ( flagComplete || flagError )
            if (cnt)
                $('#formUploadedFile').submit();
            else
                $('#log').append('<li>Upload error. No files Uploaded.</li>');
    }
</script>

<h1>Add Puzzles from disk</h1>

    <form id="form1" name="form1" action="/upload.php" method="post" enctype="multipart/form-data">
        <?=CHtml::hiddenField(Yii::app()->request->csrfTokenName, Yii::app()->request->csrfToken )?>
        <div class="divMovieContainerUp">
            <div style="float:left;">Select an image<br/>(or several images):</div>
            <div id="spanButtonPlaceHolder" style="float:right;"></div>
        </div>
        <div id="divSWFUploadUI" style="margin-top: 20px;">
            <div class="fieldset flash" id="fsUploadProgress" style="<?#display:none;?>">
                <span class="legend">Selected Files</span>
                <div id="divSelectedFiles"></div>
            </div>
            <div id="divStatus">&nbsp;</div>
            <div id="divMovieContainer">
                <span id="spanButtonPlaceHolder" style="margin-top:4px;"></span>
                <input type="button" value="Start Upload" id="btnStartUpload"<?# Resized return swfu.startUpload(); btnStartResizedUpload()?>
                       class="inputTypeSubmit" style="display:none;position:relative;font-size:11px;height:21px;" />
            </div>
            <div id="log" style="height:300px;width:600px;overflow-y:auto;"></div>
        </div>
    </form>

<form id="formUploadedFile" name="formUploadedFile" method="post" action="/service/edit-puzzles">
    <div id="divUploadedFile">
        <input type="hidden" id="cntUploadedFile" name="cntUploadedFile" value="0">
    </div>
</form>