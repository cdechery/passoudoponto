<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; <?php echo $this->config->item('charset');?>"/>
<meta charset="<?php echo $this->config->item('charset');?>"/>
<?php
	if( !isset($min_template) ) {
		$min_template = "basic";
	}

	$min_debug = "";
	if( ENVIRONMENT!='production' ) {
		$min_debug = "&debug=true";
	}

?>

<script type="application/javascript" src="<?php echo base_url('javascript')?>"></script>
<script type="application/javascript" src="<?php echo static_url('min/g='.$min_template.'_js'.$min_debug)?>"></script>
<link href='http://fonts.googleapis.com/css?family=Lato:300,400,900' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php echo static_url('min/g='.$min_template.'_css'.$min_debug)?>"/>
<!--[if lt IE 9]>
<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?php
	if( !empty($cust_js) ) {
?>
<script type="application/javascript" src="<?php echo static_url('min/f='.implode(",",$cust_js).$min_debug)?>"></script>
<?php
	}

	if( !empty($cust_css) ) {
?>
<link rel="stylesheet" type="text/css" href="<?php echo static_url('min/f='.implode(",",$cust_css).$min_debug)?>"/>
<?php
	}
?>