<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $title ?></title>

    <link rel="icon" type="image/png" href="<?= theme('favicon.png')?>" />


<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
    <link rel="stylesheet" href="/inc/css/separate/pages/error.min.css">
    <link rel="stylesheet" href="/inc/css/lib/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="/inc/css/main.css">
	<link rel="stylesheet" href="<?=theme('styles.css')?>"> 

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
	<div class="error_logo"><img width="250" class="hidden-md-down" src="<?= theme('images/login_logo.png')?>" alt=""></div>
