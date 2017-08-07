<?php 
namespace console\controllers;

use yii\base\Model;
use yii\console\Controller;

use common\models\Unit;
use common\models\ResetSchedule;
use yii\helpers\Url;

use Yii;

class ResetController extends Controller {
	
	public function actionUnit($id){
		$unit = Unit::findOne($id);
		if($unit != null)
			$unit->resetUnit();
		//after resetting,again set the cronjob for next time
		$schedule = ResetSchedule::find()->where(['unit_id'=>$id])->one();
		//make the new cron command
		$months = $unit->auto_reset_period;
		$today = time();
		$monthsLater = strtotime("+{$months} months", $today);
		$month = (int)date('m', $monthsLater);
		$date = (int)date('d', $monthsLater);
		
		//create the cron time
		//minute hour day month weekday
		$cron_time = "0 1 $date $month *";
		$new_cron_command = $cron_time.' cd /home/wordpressmonks/public_html/works/mycaar_lms && php yii reset/unit '.$id.PHP_EOL;
		$old_command = $schedule->cron_time.' cd /home/wordpressmonks/public_html/works/mycaar_lms && php yii reset/unit '.$id.PHP_EOL;
		$schedule->cron_time = $cron_time;
		$schedule->actual_time = $monthsLater;
		if($schedule->save()){
			if(file_exists('/tmp/crontab.txt')){
				//write cron_tab
				$output = shell_exec('crontab -l');
				 if($old_command){
					//removing
					 $output = str_replace($old_command, "", $output);
					 file_put_contents('/tmp/crontab.txt', $output); 
				} 
				file_put_contents('/tmp/crontab.txt', $output.$new_cron_command); 
				exec('crontab /tmp/crontab.txt');					
				//print for debugging
				//$output = shell_exec('crontab -l');					
			}
			return true;
		}
	}
}
?>