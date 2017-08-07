<?php

namespace console\controllers;

use yii\base\Model;
use yii\console\Controller;
use common\models\QuickEmail;
use common\models\User;
use yii\helpers\Url;

use Yii;

class MycronController extends Controller {

    public function actionIndex() {	
		$quickemail = QuickEmail::find()->where(['status'=>0])->all();
		if($quickemail)
		{		
			foreach($quickemail as $tmp)
			{					
			$obj = json_decode($tmp->message,true);	
			
		  $message=Yii::$app
            ->mail
            ->compose(
                ['html' => 'passwordCron'],
                ['username' => $tmp->to_email,'password'=>$obj['password'],'loginLink'=>$obj['loginLink'],'resetLink'=>$obj['resetLink']] 
            )
            ->setFrom([Yii::$app->params['supportEmail'] => 'MyCaar '])
            ->setTo($tmp->to_email)
            ->setSubject($tmp->subject);
            
		 $message->getSwiftMessage()->getHeaders()->addTextHeader('MIME-version', '1.0\n');
		 $message->getSwiftMessage()->getHeaders()->addTextHeader('charset', ' iso-8859-1\n');
		 $message->send();	
					
			  $model = QuickEmail::findOne($tmp->q_id);
			  $model->status = 1;
			  $model->save(); 			  
			}
		}	     		
    }


}