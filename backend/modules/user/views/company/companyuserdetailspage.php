<?php 

use yii\helpers\Html;
$this->title = 'Monthly Users Report';

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="company-create">
		<h1><?= Html::encode($this->title) ?></h1>
		<div class="col-md-12">	
		<div class="col-md-6"> 
			<form method="post" >
				<input class="col-md-3" style="width:200px" placeholder="Select The Date" type="text" id="requireddate" name="requireddate" value="<?php if(isset($requireddate)){ echo $requireddate; }?>" />
				<input style="margin-left:50px" class="col-md-3 btn btn-success" type="submit" value="SHOW" />
			</form>
		</div>	
		<div class="col-md-6"> 
		<?php if(isset($company_users) && !empty($company_users)) {  ?>
			<form download method="post" target="_blank" action="<?=\Yii::$app->homeUrl ?>user/company/company-details-pdf">
				<input class="form-control" type="hidden" id="requireddate2" name="requireddate2" value="<?php if(isset($requireddate)){ echo $requireddate; }?>" />
				<input class="btn btn-primary" type="submit" value="Download To PDF" />
			</form>
		<?php } ?>
		</div>
		</div>
		<div style="margin-left:50px;">
		<?php if(isset($company_users) && !empty($company_users)) {  ?>
		<table border="1" style="border: 1px solid; border-collapse: collapse;" >
			<thead>
				<tr style="background-color: #DAEEF3;">
				  <th style="border: 1px solid; border-collapse: collapse; text-align: left; line-height: 23px; width:
50%;" >Company . Site </th>
				  <th style="border: 1px solid; border-collapse: collapse; text-align: left; line-height: 23px; width:
50%; " >Total Number of users as at <br>(End of <?php echo date("F Y",strtotime('01-'.$requireddate)); ?>) </th>
				</tr>
			</thead>	
			<tbody>	
				<?php 
					$totcount = 0;
					
						foreach($company_users as $tmp)
						{
					?>
						<tr>
							<td style="border: 1px solid; border-collapse: collapse; line-height: 23px;"  ><?php echo $tmp['companyname']; ?></td>
							<td style="border: 1px solid; border-collapse: collapse; line-height: 23px;"  ><?php 
									$totcount = $totcount + $tmp['usercount']; 
									echo $tmp['usercount']; ?></td>
						</tr>
				<?php } ?>
				<tr>
				
				</tr>
			</tbody>	
			<tfoot>	
								<tr>
					<td style="border: 1px solid; border-collapse: collapse; font-weight: bold; padding: 10px 0px; line-height: 23px;"  ></td>
					<td style="border: 1px solid; border-collapse: collapse; font-weight: bold; padding: 10px 0px; line-height: 23px;"  ></td>
				</tr>
				<tr style="background-color: #DAEEF3;">
					<td style="border: 1px solid; border-collapse: collapse; font-weight: bold; line-height: 23px;"  >Total Users on CAAR</td>
					<td style="border: 1px solid; border-collapse: collapse; font-weight: bold; line-height: 23px;"  ><?php echo $totcount; ?></td>
				</tr>
			</tfoot>	
		</table>
		<?php } ?>
		<div>
</div>
<script>
$(document).ready(function(){
	$('#requireddate').datepicker({
		autoclose: true, 
		todayHighlight: true,
		format: 'mm-yyyy',
		clearBtn: true
	});
});
</script>