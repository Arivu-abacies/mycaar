<html>
	<head>
	</head>
    <body>
		<table style="border: 1px solid;" >
			<thead>
				<tr style="border: 1px solid;" >
				  <th style="border: 1px solid;" >Company . Site (Alphabetical Order) </th>
				  <th style="border: 1px solid;" >Total Number of users as at (End of <?php echo date("F Y"); ?>) </th>
				</tr>
			</thead>	
			<tbody>	
				<?php 
					$totcount = 0;
					if(isset($company_users) && !empty($company_users)) { 
						foreach($company_users as $tmp)
						{
					?>
						<tr style="border: 1px solid;"  >
							<td style="border: 1px solid;"  ><?php echo $tmp['companyname']; ?></td>
							<td style="border: 1px solid;"  ><?php 
									$totcount = $totcount + $tmp['usercount']; 
									echo $tmp['usercount']; ?></td>
						</tr>
				<?php } } ?>
			</tbody>	
			<tfoot>	
				<tr style="border: 1px solid;"  >
					<td style="border: 1px solid;"  >Total Users on CAAR</td>
					<td style="border: 1px solid;"  ><?php echo $totcount; ?></td>
				</tr>
			</tfoot>	
		</table>
	</body>	
</html>