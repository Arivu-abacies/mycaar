<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/mycaar.css?1422792966',
		'css/theme-default/bootstrap.css?1422792965',
		'css/theme-default/materialadmin.css?1425466319',
		'css/theme-default/font-awesome.min.css?1422529194',
    ];
    public $jsOptions = [
		'async' => 'async',
		'position' => \yii\web\view::POS_HEAD,
    ];
    public $js = [
		//'js/libs/jquery/jquery-1.11.2.min.js',
		//'js/libs/jquery/jquery-migrate-1.2.1.min.js',
		'js/libs/bootstrap/bootstrap.min.js',
		'js/core/source/App.js',
		'js/core/source/AppNavigation.js',
		'js/core/source/AppOffcanvas.js',
		'js/core/source/AppCard.js',
		'js/core/source/AppForm.js',
		'js/core/source/AppNavSearch.js',
		'js/core/source/AppVendor.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
