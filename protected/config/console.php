<?php

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'TheJigsawPuzzles.com',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
        'application.modules.user.models.*', // Модели модуля User
		'application.components.*',
        'application.helpers.*', // Для формирования адреса CImageHelpers
        'application.extensions.*', // Расширения (image, activeDateSelect, CountrySelectorWidget)
	),

	'modules'=>array(
	),

	// application components
	'components'=>array(
		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
			'rules'=>array(
                //'<action:(login|logout|makeapuzzle)>' => 'site/<action>',
                '<action:(makeapuzzle|um)>' => 'site/<action>',
                //'<controller:(service)>/*' => '<controller>',
                // Правила для модуля User
                'user'=>'user',
                'user/<controller:\w+>'=>'user/<controller>',
                'user/<controller:\w+>/<action:\w+>'=>'user/<controller>/<action>',
                // Правила для генератора Gii
                'gii'=>'gii',
                'gii/<controller:\w+>'=>'gii/<controller>',
                'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',
                // Правила для отображения действий из контроллера service
                'service/<action1:\w+>-<action2:\w+>'=>'service/<action1><action2>',
                'service/<action1:\w+>-<action2:\w+>-<action3:\w+>'=>'service/<action1><action2><action3>',
                //Правила для отображения альбомов и пазлов
                '<albumName>' => array( 'site/album', 'urlSuffix'=>'-jigsaw-puzzle', 'caseSensitive'=>false),
                '<albumName>/<itemName>' => array( 'site/item', 'urlSuffix'=>'-jigsaw-puzzle', 'caseSensitive'=>false),

                // Временная админка
                'admin/<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'admin/<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'admin/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'db'=>array(
            'class'=>'system.db.CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=thejigsaw',
			'emulatePrepare' => true,
			'username' => 'root',//tjp_test
			'password' => '', //campingaz
			'charset' => 'utf8',
            'enableParamLogging'=>false,
            'enableProfiling'=>false,
		),
        'db2'=>array( // Сервер
            'class'=>'system.db.CDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=gallery2', //thejigsaw_gal
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'nullConversion' => PDO::NULL_TO_STRING, //NULL_EMPTY_STRING,NULL_TO_STRING
            'enableParamLogging'=>false,
            'enableProfiling'=>false,
            'emulatePrepare' => true,
        ),
        'cache'=>array(
            'class' => 'system.caching.CFileCache',
            'directoryLevel' => 2,
        ),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
                    'levels'=>'error, warning, profile', //trace, info,
				),
			),
		),
        'logError'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'trace, info', //trace, info,
                ),
            ),
        ),
        'clientScript'=>array(
        ),
	),


	'params'=>array(
        // using Yii::app()->params['rootNew']
		'adminEmail'    => 'vahtang@darahvelidze.ru',
        'pathThumbnail' => '/items/thumbnail', // Путь к каталогу с миниатюрами
        'pathWorked'    => '/items/worked',    // Путь к каталогу с отмасштабированными изображениями
        'pathOriginal'  => '/items/original',  // Путь к каталогу с 1024x1024
        'pathStatic'    => '/items/static',    // Путь к каталогу со статикой
        'pathSource'    => '/items/source',   // Путь к каталогу с исходными изображениями
        'urlSuffix'     => '-jigsaw-puzzle',   // Дописываемая часть к адресной строке
        'keySuffix'     => '-puzzles',         // Дописываемая часть к ссылкам по ключевым словам
        'defaultCutout' => 150,                // Нарезка по умолчанию
        'userAlbumName' => 'User-Albums',      // Общая категория "пользовательские альбомы"
        'userAlbumID'   => 7298,               // ID пользовательских альбомов
        'potdAlbumID'   => 6442,               // ID пользовательских альбомов

        'rootTJP'       => 'D:/Admin/Code/!thejigsawpuzzles/var/www/tjpuzzles/g2data/cache/derivative/', // Фиксированный адрес базовой директории изображений Gallery2
        'rootSource'    => 'D:/Admin/Code/!thejigsawpuzzles/tjpuzzles/g2data/albums/', // Фиксированный адрес директории исходных изображений Gallery2
        'rootNew'       => 'C:/Web/xampp/htdocs/thejigsaw/items/', // Фиксированный базовой директории изображений нового приложения
        'pathOS'        => 'C:/Web/xampp/htdocs/thejigsaw', // Путь к сайту в разных ОС
        'pathImageUpload' => 'C:/web/xampp/tmp/saved/', // Временная папка для загрузок изображений

        'thumbnailSize' => array(130, 130),    // Ширина и высота миниатюр
        'workedSize'    => array(400, 400),    // Ширина и высота масштабированных пазлов
        'origSize'      => array(1024, 1024),  // Ширина и высота рабочих изображений пазлов

        'potdRecentNum' => 7,      // Количество пазлов в линейке в 'Recent' на главной стр.
        'newPuzzlesNum' => 6,      // Количество пазлов в столбце в 'New puzzles' на главной стр.
    ),
);