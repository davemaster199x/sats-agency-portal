<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>404 Page Not Found</title>


<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
    <link rel="stylesheet" href="/inc/css/separate/pages/error.min.css">
    <link rel="stylesheet" href="/inc/css/lib/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="/inc/css/lib/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/inc/css/main.css">
	<?php if ($_ENV['THEME'] == 'sas') : ?>
		<link rel="stylesheet" href="/theme/sas/styles.css"> 
	<?php endif; ?>

<style type="text/css">

::selection { background-color: #E13300; color: white; }
::-moz-selection { background-color: #E13300; color: white; }

body {
	background-color: #fff;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}

a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 19px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

#container {
	margin: 10px;
	border: 1px solid #D0D0D0;
	box-shadow: 0 0 8px #D0D0D0;
}

p {
	margin: 12px 15px 12px 15px;
}
.error_logo{
	margin-bottom:30px;
}
.page-error-box .error-code{
	font-size:100px;
	margin-bottom: 18px;
}
.page-error-box .error-description{
	line-height: 17px;
	margin-bottom: 24px;
	font-size: 14px;
}
.txt-red{
	color:#fa424a;
}
</style>
</head>
<body>


	<div class="page-error-box">
	<div class="error_logo"><img width="250" class="hidden-md-down" src="<?= $_ENV['THEME'] == 'sas' ? 'https://smokealarmsolutions.com.au/wp-content/uploads/2023/04/smoke-alarm-solutions-logo.svg' : '/images/logo.png' ?>" alt=""></div>


    <div class="error-code txt-red">Oops!</div>
	<div class="error-title txt-red">404 Page not found</div>
	<div class="error-description">The page you are looking for might have been removed had its name changed or is temporarily unavailable.</div>


	<a href="/" class="btn">GO TO MAIN PAGE</a>
	<body>
	</html>
