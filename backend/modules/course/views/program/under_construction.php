<!DOCTYPE>
<html>
<head>
<title>Under Construction</title>

</head>
<body>
<?php if(!empty(trim($company->message)) ){ ?>
<p class="un_backend_page" ><?php echo $company->message; ?></p>
<?php } else { ?>
<p class="un_backend_page" >This Company Is Under Construction. Please Try After Some Time!!!.</p>
<?php } ?>
</body>
</html>
<style>
.un_backend_page{
    font-size: 30px;
	margin: 19% 23%;
}
</style>