<?php
//phpinfo();die;
date_default_timezone_set('America/Denver');
// change the following paths if necessary
//$yii=dirname(__FILE__).'/../Yii/framework/yiilite.php';
$yii=dirname(__FILE__).'/../../lib/Yii/framework/yiilite.php'; //yii 
//$yii='/var/www/Yii/framework/yiilite.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();
