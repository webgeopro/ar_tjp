<?php
require(__DIR__ . '/helpers/CDateTimeUtils.php'); // Подключаем библиотеку для работы с datetime

set_time_limit(0);  // Время работы скрипта неограничено
$cr = chr(10);      // Символ перевода строки
$address = __DIR__; // Рабочая директория
$path    = $address . '/../items/static'; // Директория статичных файлов
$logpath = $address . '/runtime';         // Директория логов
$logName = 'cronjob.log';                 // Название файла логов результатов работы
$logfile = $logpath . '/' . $logName;     // Название файла логов результатов работы + Полный путь
$maxSize = 1048576;                       // Максимальный размер файла-логов cronjob.log [1 Мегабайт]
define('FILE_EXT', '.err');               // Раширение файла ошибок

/**
 * Предварительные настройки.
 * Определение размера файла логов.
 * Создание нового файла в случае превышения размера.
 */
function init()
{
    global $logpath, $logName, $logfile, $maxSize;
    $logs = array();
    $maxInc = 0;

    $fileSize = filesize($logfile);
    if ($maxSize < $fileSize) { // Предельный размер файла достигнут

        $files = new DirectoryIterator($logpath);
        /** Определение инкремента для файла логов [cronjob.log.7] */
        foreach ($files as $file) {
            if ($file->isDot()) continue;
            $fullname = $file->getFilename();
            $splits = explode('.', $fullname);
            $name = $splits[0];
            $inc  = array_pop($splits);

            if ($logName == $name.'.log')
                if (null != $inc AND (int)$inc)
                    $logs[] = $inc;
        }
        if (0 < count($logs))
            $maxInc = max($logs);
        $maxInc++;
        $newLogfile = $logpath . '/' . $logName . '.' . $maxInc;
        $content = file_get_contents($logfile);
        if (file_put_contents($newLogfile, $content))
            unlink($logfile);
    }
}

/**
 * Обработка запроса (проверка файла, рендеринг, логирование)
 *
 * @param $comm     Консольная команда рендеринга
 * @param $logName  Имя файла-лога
 * @param $fileName Имя проверяемого (отрендеренного ранее) файла
 * @param $mess     Сообщение в файле-логе
 */
function process($comm, $logName, $fileName, $mess)
{
    global $path, $address;
    $file = $path . '/' . $fileName . '';
    $now = CDateTimeUtils::now(); // Текущая дата + время (datetime)

    _log($fileName, $now, ' Checking file ');// Логирование
    /** Проверка даты последней модификации отрендеренного ранее файла. */
    if (check($file, $now)) { // Файл устарел, перерендеринг
        $errors = query($comm, $logName, $mess); // Пересохранить файл
        $errors.= @file_get_contents($address . '/' . $logName . FILE_EXT);//die($address . '/' . $logName . FILE_EXT);
        _log($fileName, $now, ' Rendering file ', $errors ? $errors : '');// Логирование
    }
}

/**
 * Проверка времени создания файла
 * @param $file
 * @param null $now
 * @return bool True - , False -
 */
function check($file, $now=null)
{
    $fmtime = @filemtime($file); // Получение времени последней модификации файла
    if (false != $fmtime) { //
        $fmdate = date('Y-m-d', $fmtime); //$fmdate = date_create_from_format('U', $fmtime);// + $timezone
        if (CDateTimeUtils::diffWithoutSeconds($fmdate)) // Сравнение текущей даты с датой посл. модиф.  файла
            return true; // Файл устарел
    } elseif (!file_exists($file)) // Файла не сущ-ет, перерендеринг
        return true;

    return false;
}

/**
 * Логирование результатов
 */
function _log($fileName, $datetime, $mess, $errors='')
{
    global $logfile, $cr;

    $f = fopen($logfile, 'a');// Открыть файл
    $str = CDateTimeUtils::format($datetime, 'Y-m-d H:i:s') . $mess . $fileName . $cr; // Строка сообщения
    if ($errors)
        $str.= $errors . $cr;
    fwrite($f, $str); // Дописать информацию в файл
    fclose($f); // Надо ли???
}

/**
 * Выполнение консольной команды
 * @param $comm
 * @param $logName
 * @param $mess
 * @return string
 */
function query($comm, $logName, $mess)
{
    $log = shell_exec("./$comm 2>&1");
    //echo getErrors($logName.'.err', $mess);

    return $log;
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

// =========================================================================================== //

init(); // Предварительные настройки

process('yiic cron new > cron_new.log', 'new.log', 'potdNew', 'Cron New');
process('yiic cron recent > cron_recent.log', 'recent.log', 'potdRecent', 'Cron Recent');
process('yiic cron current > cron_current.log', 'current.log', 'potdCurrent', 'Cron Current');
process('yiic cron categories > cron_categories.log', 'categories.log', 'potdCategories', 'Cron Categories');

for ($i=1; $i<=30; $i++)
    process('yiic cron featuredFile --num='.$i.' > featured.log',
        'cron_featured.log', 'featured_puzzle'.$i, 'Cron Featured'
    );

echo `chown -R apache:apache /var/www/test/items/static`;