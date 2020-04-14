<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
			// Test database connection
            'db2'=>array( // Сервер
                'class'=>'system.db.CDbConnection',
                'connectionString' => 'mysql:host=localhost;dbname=gallery2',
                'username' => 'root', //'kraisoft',
                'password' => '', //rtynfdh',
                'charset' => 'utf8',
                'nullConversion' => PDO::NULL_TO_STRING, //NULL_EMPTY_STRING,NULL_TO_STRING
                'enableParamLogging'=>false,
                'enableProfiling'=>false,
                'emulatePrepare' => true,
            ),
		),
	)
);
