<html>
	<head>
	</head>
    <body>
		<h4>Monthly Users Report </h4>
		<table border="1" style="border: 1px solid; border-collapse: collapse;" >
			<thead>
				<tr style="background-color: #DAEEF3;">
				  <th style="border: 1px solid; border-collapse: collapse; text-align: left; line-height: 23px; width:
50%;" >Company . Site </th>
				  <th style="border: 1px solid; border-collapse: collapse; text-align: left; line-height: 23px; width:
50%; " >Total Number of users as at <br>(End of <?php echo date("F Y",$requireddate); ?>) </th>
				</tr>
			</thead>	
			<tbody>	
				<?php 
					$totcount = 0;
					if(isset($company_users) && !empty($company_users)) { 
						foreach($company_users as $tmp)
						{
					?>
						<tr>
							<td style="border: 1px solid; border-collapse: collapse; line-height: 23px;"  ><?php echo $tmp['companyname']; ?></td>
							<td style="border: 1px solid; border-collapse: collapse; line-height: 23px;"  ><?php 
									$totcount = $totcount + $tmp['usercount']; 
									echo $tmp['usercount']; ?></td>
						</tr>
				<?php } } ?>
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
	</body>	
</html>