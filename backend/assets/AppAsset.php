<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/mycaar.css',
		//'https://storage.googleapis.com/code.getmdl.io/1.0.6/material.indigo-pink.min.css',
		'css/theme-default/bootstrap.css?1422792965',
		'css/theme-default/materialadmin.css?1425466319',
		'css/theme-default/font-awesome.min.css?1422529194',
		'css/theme-default/material-design-iconic-font.min.css',
		'css/theme-default/libs/summernote/summernote.css?1425218701',
		//'css/theme-default/libs/DataTables/jquery.dataTables.css?1423553989',
		//'css/theme-default/libs/DataTables/extensions/dataTables.colVis.css?1423553990',
		//'css/theme-default/libs/DataTables/extensions/dataTables.tableTools.css?1423553990',
		'css/theme-default/libs/morris/morris.core.css?1420463396'
    ];
    public $jsOptions = [
		//'async' => 'async',
		'position' => \yii\web\view::POS_HEAD,
    ];
    public $js = [
		//'js/libs/jquery/jquery-1.11.2.min.js',
		//'js/libs/jquery/jquery-migrate-1.2.1.min.js',
		'js/libs/bootstrap/bootstrap.min.js',
		'js/libs/spin.js/spin.min.js',
		'js/libs/autosize/jquery.autosize.min.js',
		'js/libs/nanoscroller/jquery.nanoscroller.min.js',
		'js/core/source/App.js',
		'js/core/source/AppNavigation.js',
		'js/core/source/AppOffcanvas.js',
		'js/core/source/AppCard.js',
		'js/core/source/AppForm.js',
		'js/core/source/AppNavSearch.js',
		'js/core/source/AppVendor.js',
		'js/libs/bootstrap-datepicker/bootstrap-datepicker.js',
		'js/libs/summernote/summernote.min.js',
		'js/libs/multi-select/jquery.multi-select.js',
		'js/libs/morris.js/morris.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
    ];
}
