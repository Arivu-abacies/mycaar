<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
$backEndBaseUrl = str_replace('/frontend/web', '/admin', (new \yii\web\Request)->getBaseUrl());
return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'site/index',
    
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
			'enableCsrfValidation'=>false,
			'class' => 'common\components\Request',
			 'web'=> '/frontend/web',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-mycaar', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'mycaar-lms',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [ 'site/signup/<slug>' => 'site/signup'
            ],
        ],
		'urlManagerBackEnd' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => $backEndBaseUrl,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],		
        
    ],
    'params' => $params,
];
