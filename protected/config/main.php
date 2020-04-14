<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
$domain = 'thejigsaw'; //216.55.178.207
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'TheJigsawPuzzles.com',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'application.modules.user.models.*',     // Сторонний модуль User
        'application.modules.user.components.*', // Сторонний модуль User
        'application.helpers.*', // Для Image-extension
        'application.extensions.*', // Расширения (image, activeDateSelect, CountrySelectorWidget)
	),

	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'klen',
			'ipFilters'=>array('127.0.0.1','::1'),
            //'generatorPaths' => array('ext.giix-core', ),
		),
        'user'=>array(
            'hash' => 'md5',
        ),
	),

	'components'=>array(
		'session'=>array(
            'sessionName'=>'TJPSESSID',
            'class' => 'TJPSession', // 'system.web.CDbHttpSession', // 'Session'
            'connectionID' => 'db',
            'cookieParams' => array('domain' => '.'.$domain),
            'autoStart' => false,
            'timeout' => 24*3600*30,
        ),
		'user'=>array(
            'class' => 'TJPWebUser', //CWebUser
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
            'autoRenewCookie'=>true,
            //'identityCookie' => array('domain' => $domain),
            #'loginUrl' => array('/user/login'),
		),
        'image'=>array(
            'class'=>'application.extensions.image.CImageComponent',
            'driver'=>'GD', // GD or ImageMagick
        ),
        /*'request'=>array(
            'enableCsrfValidation'=>true, // Защита от CSRF-атак
        ),*/
        'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
			'rules'=>array(
                '<action:(clearUrl|makeapuzzle|getThumbnail|logout|feedback)>' => 'site/<action>',

                // Правила для статичных страниц
                'info/<page:\w+>' => 'site/info/view/<page>',

                // Правила для модуля User
                'user'=>'user',
                'user/<controller:\w+>'=>'user/<controller>',
                'user/<controller:\w+>/<action:\w+>'=>'user/<controller>/<action>',

                // Правила для генератора Gii // todo Убрать в prodaction
                'gii'=>'gii',
                'gii/<controller:\w+>'=>'gii/<controller>',
                'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',

                // Правила для отображения действий из контроллера service
                'service/<action1:\w+>-<action2:\w+>'=>'service/<action1><action2>',
                'service/<action1:\w+>-<action2:\w+>-<action3:\w+>'=>'service/<action1><action2><action3>',
                'service/*' => '/service',

                // Поисковый движок
                'search/*' => '/search',
                'key/<keySearchCriteria>-puzzles' => '/search',

                // Правила для администрирования сайта
                'admin'=>'admin',
                'admin/user'=>'user/user',
                'admin/user/<action:\w+>'=>'user/user/<action>',
                'admin/user/admin/<action:\w+>'=>'user/admin/<action>',
                'admin/<controller:\w+>'=>'admin/<controller>',
                'admin/<controller:\w+>/<action:\w+>'=>'admin/<controller>/<action>',

                // Совместимость для старых внешних ссылок из Gallery2
                'download/<der_id:\d+>-<digit_param:\d+>/<name_param:>' => '/download/index',

                //Правила для отображения альбомов и пазлов
                '/itemId=<itemId>' => '/site/item', // Для сохр. пазлов, переход с Гл. стр.
                '/new-puzzles' => '/site/album/new-puzzles', // Последние добавленные файлы
                'User-Albums/<userAlbumName>' => array( 'site/album', 'urlSuffix'=>'-jigsaw-puzzle', 'caseSensitive'=>false),
                'User-Albums/<userAlbumName>/<userItemName>' => array( 'site/item', 'urlSuffix'=>'-jigsaw-puzzle', 'caseSensitive'=>false),
                '<albumName>' => array( 'site/album', 'urlSuffix'=>'-jigsaw-puzzle', 'caseSensitive'=>false),
                '<albumName>/<itemName>' => array( 'site/item', 'urlSuffix'=>'-jigsaw-puzzle', 'caseSensitive'=>false),
			),
		),
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=thejigsaw',
			'emulatePrepare' => true,
			'username' => 'root',//tjp_test
			'password' => '',//campingaz
			'charset' => 'utf8',
            'enableParamLogging'=>true,
            'enableProfiling'=>true,
		),
        'db2'=>array( // Сервер
            'class'=>'system.db.CDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=gallery2',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'enableParamLogging'=>true,
            'enableProfiling'=>true,
        ),
        'cache'=>array( // Кеширование
            'class' => 'system.caching.CFileCache',
            'directoryLevel' => 2, // Два уровня вложенности папок
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
                    'levels'=>'trace, error, warning',// info,
                    'categories'=>'system.*',
                    'filter'=>'CLogFilter',
                ),
                /*array( // Логгирование на почту
					'class'=>'CEmailLogRoute',
                    'levels'=>'error, warning',// info, trace,
                    'emails'  => array('logging@thejigsawpuzzles.com', ),
                    'subject' => 'Thejigsawpuzzles.com Error',
                ),*/
				array(
					'class'=>'CWebLogRoute',
                    'showInFireBug'=>true,
				),
			),
		),
        'clientScript'=>array(
            /*'class'=>'application.components.ExtendedClientScript',
            'combineFiles'=>false,
            'compressCss'=>true,
            'compressJs'=>true,
            'excludeFiles'=>array(
            ),*/
            'scriptMap'=>array(
                'jquery.js'=>false,
                'jquery.min.js'=>false,
            ),
            //'enableJavaScript'=>false, // Автогенерация js-файлов фреймворком
            'packages' => array( // Пакеты js + ccs-файлов
                //'jqmin' => array('baseUrl'=>'/js/', 'js'=>array('jquery.min.js')),
                'swfupload' => array( // make a puzzle, flash-upload
                    'baseUrl' => '/js/',
                    'js'=>array('swfupload.js', 'jquery.swfupload.js', 'swfupload.queue.js', 'getTemplate.js'),
                    'css' => array('../css/swfupload.css'),
                ),
                'editpuzzle' => array( // make a puzzle, flash-upload
                    'baseUrl' => '/js/',
                    'js'=>array('jquery.rotate.js', 'common.js'),
                ),
            ),
        ),
	),

	// using Yii::app()->params['pathOS']
	'params'=>array(
		'adminEmail'    => 'garry@kraisoft.com',      // Адрес администратора сайта
        'techEmail'     => 'vahtang@darahvelidze.ru', // Адрес почты технической поддержки
        'pathThumbnail' => '/items/thumbnail', // Путь к каталогу с миниатюрами
        'pathWorked'    => '/items/worked',    // Путь к каталогу с ресайзами {400x400}
        'pathOriginal'  => '/items/original',  // Путь к каталогу с ресайзами {1024x1024}
        'pathSource'    => '/items/source',    // Путь к каталогу с исходными изображениями
        'pathStatic'    => '/items/static',    // Путь к каталогу с исходными изображениями
        'urlSuffix'     => '-jigsaw-puzzle',   // Дописываемая часть к адресной строке
        'keySuffix'     => '-puzzles',         // Дописываемая часть к ссылкам по ключевым словам
        'defaultCutout' => '100 piece classic',// Нарезка по умолчанию
        'userAlbumName' => 'User-Albums',      // Общая категория "пользовательские альбомы"
        'userAlbumID'   => 7298,               // ID пользовательских альбомов
        'potdAlbumID'   => 6442,               // ID пользовательских альбомов
        'thumbnailSize' => array(130, 130),    // Ширина и высота миниатюр
        'workedSize'    => array(400, 400),    // Ширина и высота масштабированных пазлов
        'origSize'      => array(1024, 1024),  // Ширина и высота рабочих изображений пазлов
        'cropSize'      => array(600, 600),    // Ширина и высота изображений пазлов на стр. обрезки (admin/crop)
        'defaultCount'  => 30, // max кол-во пазлов в пользовательском альбоме
        'pathOS'        => 'C:/Web/xampp/htdocs/thejigsaw', // Путь к сайту в разных ОС
        //'pathOS'        => dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR,
        'pathImageUpload' => 'C:/web/xampp/tmp/saved/', // Временная папка для загрузок изображений
        'external' => array(null, '/items/thumbnail', '/items/worked', '/items/original', '/items/source'),

        'potdRecentNum' => 7,      // Количество пазлов в линейке в 'Recent' на главной стр.
        'newPuzzlesNum' => 6,      // Количество пазлов в столбце в 'New puzzles' на главной стр.
        
        'afgEnabledAlbums' => array( // Список альбомов с предварительной анимацией перед сборкой пазла
            393083, // Money
            8250,   // Halloween
        ),
        'defaultItemID' => 36, // Динозавр из альбома Kids-Puzzle
        // Время кеширования для виджетов (в сек.)
        'dAlbumList' => 1,     // Список пазлов альбома //86400 (24 часа)
        'dAlbumSiblings' => 1, // Список соседних аль бомов //86400 (24 часа)
        'dCategories' => 1,    // Категории //86400 (24 часа)
        'dDefaultImage' => 86400,  // Изображение по умолчанию //86400 (24 часа)
        'dGetAlbumThumbnail' => 1, // Миниатюра альбома//86400 (24 часа)
        'dNewPuzzles' => 1,    // Новые пазлы //86400 (24 часа)
        'dPotdCurrent' => 1,   // Пазл дня //86400 (24 часа)
        'dPotdFeatured' => 1,  // Случайный пазл //86400 (24 часа)
        'dPotdRecent' => 1,    // Последние пазлы //86400 (24 часа)
        'dSearchBlock' => 1,   // Поиск /* Не используется */
	),
);