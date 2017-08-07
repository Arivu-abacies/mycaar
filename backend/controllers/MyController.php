<?php


namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use common\models\SiteMeta;
use common\models\SetPassword;
use yii\web\UploadedFile;

/**
 * Site controller
 */

 class MyController extends Controller {
    public function actionPdf(){
        Yii::$app->response->format = 'pdf';
        $this->layout = '//print';
        return $this->render('myview', []);
    }
}
