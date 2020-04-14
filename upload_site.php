<?php
    session_start();
    if (true AND "POST" == $_SERVER["REQUEST_METHOD"]) { // Обработка переданного файла
                                            // AND isset($_POST['YII_CSRF_TOKEN']
        if (isset($_FILES["Filedata"])) { // Если переслан файл  && false

            if (is_uploaded_file($_FILES["Filedata"]["tmp_name"])  // Загружен без ошибок
                && !$_FILES["Filedata"]["error"]) {

                $fileName = $_POST['YII_CSRF_TOKEN'].md5($_FILES["Filedata"]["name"]).'.jpg';
                move_uploaded_file(
                    $_FILES["Filedata"]["tmp_name"],
                    '/var/www/test/upload/'.$fileName
                );
                die('fileName='.$fileName);
            }
        }
    }