<?$this->breadcrumbs=array(
    $fullname => $albumAddress,
    'Make a puzzle',
);?>
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
            file_size_limit : "8 MB", // Ограничение на объем загружаемого файла
            file_types : "*.jpg;*.png;*.gif", // Расширения загружаемых файлов
            file_types_description : "JPG Images; PNG Image; GIF Image", // Описания расширений файлов
            file_upload_limit : <?=$cntPuzzles?>, // Ограничение кол-ва загруженных файлов (0 – любое количество)
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
            button_text: '<span class="theFont">&nbsp;&nbsp;Browse...</span>', // Текст кнопки //Make a puzzleAdd images
            button_text_style: ".theFont { font-size: 11; border-width: 1px;font-family: Verdana,Arial,Helvetica,sans-serif;}",
            button_text_left_padding: 2, // Смещение текста слева
            button_text_top_padding: 2  // Смещение текста сверху
        })
            .bind('uploadStart', function(event, file){ // Загрузка изображения //$('#log').append('<li>Upload start - '+file.progress+'</li>');
                flagComplete = false; flagError = false;
            })
            .bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){ // Завершение выбора файлов
                $("#fsUploadProgress").css('display', 'block'); // Отображаем глобальный div {Selected files}
            })
            .bind('fileQueued', function(event, file){ // Добавление очередного изображения
                getTemplate(file, 'divSelectedFiles'); // Вставка шаблона для контента
            })
            .bind('uploadProgress', function(event, file, bytesLoaded){ //$('#log').append('<li>Upload progress - '+bytesLoaded+'</li>');
                var percentage = Math.round((bytesLoaded/file.size)*100); // Длина от целого элемента
                $('div#'+file.id).find('div.progressBarInProgress').css('width', percentage+'%');
            })
            .bind('queueComplete', function(event, uploadCount){ // Завершение очереди скачивания. Только с плагином.
                $('#log').append('<li>Files Uploaded. Redirect</li>');
                setInterval(swfuSubmit, 2000); //setTimeout(alertSwfu, 4000);
            })
            .bind('uploadError', function (event, file, errorCode, message){
                //$('#log').append('<li>++'+file.name+'!'+errorCode+'  '+message+'</li>');
            })
            .bind('uploadSuccess', function(event, file){ // Завершение загрузки
                flagComplete = false; flagError = false; // Обнуляем флаги после загрузки файла на сервер
            })
            .bind('uploadComplete', function(event, file){ // Завершение загрузки
                var inpTitle = $("#inp"+file.id).val();    // Название для пазла
                var inpName  = $("#inpName"+file.id).val();// Имя файла-изображения пазла
                var inpTitleD= $("#inpTitle"+file.id).val();// Имя файла-изображения пазла. Дубль, в случае стертого названия в input-е.
                // Отправка формы с дополнительными полями
                $.post('/makeapuzzle', {
                    fileTitle:inpTitle, fileTitleDouble:inpTitleD, fileName:inpName, fileID:file.id, albumComponentUrl:'<?=@$albumComponentUrl?>'
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
        var cnt = $('#cntUploadedFile').val(); // Количсетво закаченных файлов
        if ( flagComplete || flagError )
            if (cnt)
                $('#formUploadedFile').submit();
            else
                $('#log').append('<li>Upload error. No files Uploaded.</li>');
    }
</script>
<!--<h1>Make a puzzle</h1>
<h2>You can upload [ <?/*=$cntPuzzles*/?> ] more puzzles.</h2>-->

<?if (0 < $cntPuzzles):?>
    <table cellspacing="0" cellpadding="0" width="100%" class="gcBackground1">
        <tbody><tr valign="top">
            <td width="20%">
                <table cellspacing="0" cellpadding="0">
                    <tbody><tr>
                        <td style="padding-bottom:5px" colspan="2">
                            <div class="gsContentDetail">
                                <div class="gbBlock gcBorder1">

                                    <div class="block-imageblock-ImageBlock">
                                        <div class="one-image">
                                            <div class="giThumbnailContainer" style="font-size:0.8em;">
                                                <?$this->widget('getAlbumThumbnail', array('albumComponentUrl'=>$albumComponentUrl))?>
                                            </div>
                                            <div class="giItemInfo"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td style="padding:0;margin:0;">
                <form id="form1" name="form1" action="/upload.php" method="post" enctype="multipart/form-data">
                    <?=CHtml::hiddenField(Yii::app()->request->csrfTokenName, Yii::app()->request->csrfToken )?>
                <div> <?# id="gbPrivacyPolicy"?>
                    <strong>&nbsp; Make puzzles from your pictures</strong>
                    <hr style="border:0;height:1px;background-color:#000;margin:4px 0;"/>
                    <p>You can add <strong><?=$cntPuzzles?></strong> more puzzles to your album.</p>
                    <div class="divMovieContainerUp">
                        <div style="float:left;">Select an image<br/>(or several images):</div>
                        <div id="spanButtonPlaceHolder" style="float:right;"></div>
                    </div>

                        <div id="divSWFUploadUI" style="margin-top:20px;visibility:hidden;">
                            <div class="fieldset flash" id="fsUploadProgress" style="<?#display:none;?>">
                                <span class="legend-user">Selected images:</span>
                                <div id="divSelectedFiles">
                                    <!--<input type="hidden" name="albumComponentUrl" value="<?/*=$albumComponentUrl*/?>" />-->
                                </div>
                            </div>
                            <div id="divStatus">&nbsp;</div>
                            <div id="divMovieContainer">
                                <!--<span id="spanButtonPlaceHolder" style="margin-top:4px;"></span>Start Upload-->
                                <input type="button" value="Make Puzzles" id="btnStartUpload"<?# Resized return swfu.startUpload(); btnStartResizedUpload()?>
                                       class="inputTypeSubmit" style="<?#display:none;?>position:relative;font-size:11px;height:21px;" />
                            </div>
                            <div id="log" style="height:50px;width:600px;overflow-y:auto;"></div>
                        </div>

                </div>
                </form>
            </td>
        </tr>
        </tbody>
    </table>
<?endif?>

<form id="formUploadedFile" name="formUploadedFile" method="post" action="/service/edit-puzzles">
    <div id="divUploadedFile">
        <input type="hidden" id="cntUploadedFile" name="cntUploadedFile" value="0" />
        <input type="hidden" name="albumComponentUrl" value="<?=@$albumComponentUrl?>"  />
    </div>
</form>