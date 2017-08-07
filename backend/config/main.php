<?php
use \yii\web\Request;
//use \yii\helpers\Url;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
//$url = Url::to('/site/index', true);
//$frontEndBaseUrl = str_replace('site/index', '', $url);
//$frontEndBaseUrl = str_replace('/backend/web', '/admin', (new Request)->getBaseUrl());
//$frontEndBaseUrl = str_replace('admin/', '', \Yii::$app->homeUrl);
//echo $frontEndBaseUrl;
//$frontEndBaseUrl = 'http://mycaar.com.au/';

$frontEndBaseUrl = str_replace('/backend/web', '', (new Request)->getBaseUrl());


return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
	'defaultRoute' => 'course/program/dashboard',
    'bootstrap' => ['log'],
    'layout' => 'dashboard',
    'modules' => [
      'course' => [
          'class' => 'backend\modules\course\Course',
      ],
      'user' => [
          'class' => 'backend\modules\user\module',
      ],
    ],
   
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
			'enableCsrfValidation'=>false,
			'class' => 'common\components\Request',
			'web'=> '/backend/web',
			'adminUrl' => '/admin',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-mycaar', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
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
            'rules' => [
            ],
        ],
		'urlManagerFrontEnd' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => $frontEndBaseUrl,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
             
        ], 
		'response' => [
            'formatters' => [
                'pdf' => [
                    'class' => 'robregonm\pdf\PdfResponseFormatter',
                ],
            ]
        ],
    ],
    'params' => $params,
];