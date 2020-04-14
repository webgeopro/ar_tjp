<?php
    set_time_limit(0); // Время работы скрипта неограничено
    $cr = chr(10);     // Символ перевода строки
    $address = '/var/www/test/protected'; // Директория логов
    
    /*echo `./yiic cron new > cron_new.log` . $cr. ' Cron New finished';
    echo `./yiic cron recent > cron_recent.log` . $cr. ' Cron Recent finished';
    echo `./yiic cron current > cron_current.log` . $cr. ' Cron Current finished';
    echo `./yiic cron categories > cron_categories.log` . $cr. ' Cron Categories finished';
    echo `./yiic cron featured > cron_featured.log` . $cr. ' Cron Featured finished';*/

    //echo $cr. ' All Cron operations finished.';

    /**
     * Выполнение консольной команды
     * @param $comm
     * @param $fileName
     * @param $mess
     */
    function query($comm, $fileName, $mess)
    {
        echo $comm;
        echo getErrors($fileName, $mess);
    }

    /**
     * Отображение ошибки
     * @param $fileName
     * @param $mess
     * @return string
     */
    function getErrors($fileName, $mess)
    {
        global $address;
        $res = $mess . ' Finished';
        $file = $address .'/'. $fileName;

        if (file_exists($file)) {
            $errors = file_get_contents($file);
            if (null != $errors)
                $res .= 'with errors:' . chr(10) . $errors;
        }

        return $res . chr(10);
    }

    query(`yiic cron new > cron_new.log`, 'cron_new.log', 'Cron New');
    query(`yiic cron recent > cron_recent.log`, 'cron_recent.log', 'Cron Recent');
    query(`yiic cron current > cron_current.log`, 'cron_current.log', 'Cron Current');
    query(`yiic cron categories > cron_categories.log`, 'cron_categories.log', 'Cron Categories');
    query(`yiic cron featured > cron_featured.log`, 'cron_featured.log', 'Cron Featured');
