<?php
    session_start();
    if (true AND "POST" == $_SERVER["REQUEST_METHOD"]) { // Обработка переданного файла
                                            // AND isset($_POST['YII_CSRF_TOKEN']
        /*if (isset($_POST['YII_CSRF_TOKEN'])) {
            echo "\n ".$_POST['YII_CSRF_TOKEN']." \n";
            echo "\n ".$_SESSION['YII_CSRF_TOKEN']." \n";
        }*/
        if (isset($_FILES["Filedata"])) { // Если переслан файл  && false

            if (is_uploaded_file($_FILES["Filedata"]["tmp_name"])  // Загружен без ошибок
                && !$_FILES["Filedata"]["error"]) {

                $fileName = $_POST['YII_CSRF_TOKEN'].md5($_FILES["Filedata"]["name"]).'.jpg';
                if (move_uploaded_file(
                    $_FILES["Filedata"]["tmp_name"],
                    'C:/web/xampp/tmp/saved/'.$fileName
                )) {
                    //die(json_encode(array('success'=>true, 'fileName'=>$fileName)));
                }//die(print_r($_POST));
                /*if (isset($_POST['adminAction']) AND 'filesend' == $_POST['adminAction']) {// Ддя администратора
                    $url = '/admin/makeapuzzle' . (empty($_POST['album']) ? '' : '?album='.$_POST['album']);
                    header("Location: $url");
                }*/
                //http_redirect('/service/edit-puzzles', array('cntUploadedFile'=>1), true, HTTP_REDIRECT_PERM); // Админский интерфейс редактирования

                die('fileName='.$fileName);
            }
        }
        #echo "_FILES ".print_r($_FILES);
        #echo "_POST ".print_r($_POST);
        #echo "_SESSION ".print_r($_SESSION);
        #echo "_COOKIE ".print_r($_COOKIE);
    }


    die('STOPPED');