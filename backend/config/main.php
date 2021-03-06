<?php
use \yii\web\Request;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

$frontEndBaseUrl = str_replace('/backend/web', '/frontend/web', (new Request)->getBaseUrl());

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
		'response' => [
            'formatters' => [
                'pdf' => [
                    'class' => 'robregonm\pdf\PdfResponseFormatter',
					'mode' => '', // Optional
					'format' => 'A4',  // Optional but recommended. http://mpdf1.com/manual/index.php?tid=184
					'defaultFontSize' => 0, // Optional
					'defaultFont' => '', // Optional
					'marginLeft' => 15, // Optional
					'marginRight' => 15, // Optional
					'marginTop' => 16, // Optional
					'marginBottom' => 16, // Optional
					'marginHeader' => 9, // Optional
					'marginFooter' => 9, // Optional
					'orientation' => 'Landscape', // optional. This value will be ignored if format is a string value.
					'options' => [
						// mPDF Variables
						// 'fontdata' => [
							// ... some fonts. http://mpdf1.com/manual/index.php?tid=454
						// ]
					]
                ],
            ]
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
			'enableCsrfValidation'=>false,
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

    ],
    'params' => $params,
];
