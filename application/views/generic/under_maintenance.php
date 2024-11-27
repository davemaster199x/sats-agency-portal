<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo $title; ?></title>
<style>
#maintenance_banner{
	width: 100%;
	height: 100%;
}
</style>
</head>
<body>
	<img id="maintenance_banner" src="/images/<?php echo config_item('theme') ?>-under-maintenance.png" />
</body>
</html>