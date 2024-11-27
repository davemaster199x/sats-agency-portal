<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $title; ?></title>

   <link rel="icon" type="image/png" href="<?= theme('favicon.png') ?>" />

   <link rel="stylesheet" href="/inc/css/separate/elements/steps.min.css">
    <link rel="stylesheet" href="/inc/css/separate/vendor/jquery-steps.min.css">
    <link rel="stylesheet" href="/inc/css/lib/lobipanel/lobipanel.min.css">
    <link rel="stylesheet" href="/inc/css/separate/vendor/lobipanel.min.css">
    <link rel="stylesheet" href="/inc/css/lib/jqueryui/jquery-ui.min.css">
    <link rel="stylesheet" href="/inc/css/separate/pages/widgets.min.css">
    <link rel="stylesheet" href="/inc/css/lib/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="/inc/css/lib/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="/inc/css/lib/bootstrap-sweetalert/sweetalert.css">
	<link rel="stylesheet" href="/inc/css/separate/vendor/sweet-alert-animations.min.css">
    <link rel="stylesheet" href="/inc/css/separate/vendor/bootstrap-select/bootstrap-select.min.css">
    <link rel="stylesheet" href="/inc/css/separate/vendor/select2.min.css">
    <link rel="stylesheet" href="/inc/css/separate/vendor/bootstrap-daterangepicker.min.css">
	<link rel="stylesheet" href="/inc/css/lib/flatpickr/flatpickr.css">
    <link rel="stylesheet" href="/inc/css/separate/pages/profile.min.css">
    <link rel="stylesheet" href="/inc/css/separate/vendor/pnotify.min.css">
    <link rel="stylesheet" href="/inc/css/main.css">
    <link rel="stylesheet" href="/inc/css/gherx.css">
	<link rel="stylesheet" href="/inc/css/jc.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.4.2/jquery.fancybox.min.css" />
	<link rel="stylesheet" href="/inc/css/lib/charts-c3js/c3.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="/inc/croppie/croppie.css" />
	<link rel="stylesheet" href="/inc/css/separate/pages/project.min.css">

	<!-- datatable css -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap.min.css" />
	<link rel="stylesheet" href="https://cdn.datatables.net/searchpanes/2.2.0/css/searchPanes.dataTables.min.css" />
	<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css" />

	<!-- smartwizard -->
	<link href="https://cdn.jsdelivr.net/npm/smartwizard@6/dist/css/smart_wizard_all.min.css" rel="stylesheet" type="text/css" />
    
    <?php if($this->config->item('theme') === "sas"): ?>
        <?php if (in_array($this->uri->uri_string(), array("/", "home", "sys/search"))): ?>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.7/dist/css/autoComplete.02.min.css">
        <?php endif; ?>
    <?php endif; ?>

	<link rel="stylesheet" href="<?=theme('styles.css')?>"> 
	
	
    <!-- JS start -->
    <script src="/inc/js/lib/jquery/jquery-3.2.1.min.js"></script>
    <script src="/inc/js/lib/popper/popper.min.js"></script>
    <script src="/inc/js/lib/tether/tether.min.js"></script>
    <script src="/inc/js/lib/bootstrap/bootstrap.min.js"></script>
    <script src="/inc/js/plugins.js"></script>
    <script src="/inc/js/jc.js"></script>
	<script src="/inc/js/lib/flatpickr/flatpickr4.js"></script>
    <script src="/inc/js/gherx.js"></script>
	<script src="/inc/js/lib/bootstrap-sweetalert/sweetalert.min.js"></script>


    <script type="text/javascript" src="/inc/js/lib/jqueryui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/inc/js/lib/lobipanel/lobipanel.min.js"></script>
    <script type="text/javascript" src="/inc/js/lib/match-height/jquery.matchHeight.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="/inc/js/lib/bootstrap-select/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="/inc/js/lib/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="/inc/js/app.js"></script>
	<script type="text/javascript" src="/inc/js/lib/hide-show-password/bootstrap-show-password.min.js"></script>
	<script type="text/javascript" src="/inc/js/lib/hide-show-password/bootstrap-show-password-init.js"></script>
	<script src="/inc/js/lib/html5-form-validation/jquery.validation.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.4.2/jquery.fancybox.min.js"></script>
	<script src="/inc/croppie/croppie.js"></script>
	<script type="text/javascript" src="/inc/js/lib/jquery-idle-master/jquery.idle.js"></script>

	<!-- datatable js -->
	<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/searchpanes/2.2.0/js/dataTables.searchPanes.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>

	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>

	<!-- smartwizard -->
	<script src="https://cdn.jsdelivr.net/npm/smartwizard@6/dist/js/jquery.smartWizard.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="/inc/js/smartwizard-manager.js"></script>

	<!-- custom datatable -->
	<link rel="stylesheet" href="/inc/css/custom-datatables-searchpanes.css"/>
	<script type="text/javascript" src="/inc/js/datatables-manager.js"></script>
    <!-- JS end -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
    
		
	<style>
	@media print {
		@page {
			size: auto;
		}
	}
	.user_abrv_name_div{
		text-align:right;
	}
	.sats_header .dropdown-toggle::after,
	.site-header-shown .dropdown-toggle::after{
		display:none;
	}
	.dropdown.dropdown-typical a.dropdown-toggle {
		color: #adb7be;
	}

	/* table row height */
	.bootstrap-table .table td, .fixed-table-body .table td, .table td {
		height: 40px !important;
	}

	/* grey icon and blue hover */
	.font-icon{
		color:#adb7be;
	}
	.font-icon:hover{
		color:#00a8ff;
	}
	.font-icon-del:hover{
		color:#fa424a
	}

	/* logout */
	.logout:hover{
		color: #fa424a;
	}
	.logout{
		color: #adb7be;
	}
	.side-menu-list .left_menu_bubble{
		position: absolute;
		top: 2px;
	}
	.pagi_count {
		margin-bottom: 15px;
	}

	.red_bubbles {
		border-radius: 50%;
		height: 23px;
		width: 23px;
		line-height: 20px;
	}

	table a:hover, table a:visited, table a:link, table a:active
	{
		text-decoration: none;
		border-bottom: none;
	}
	.site-header .site-header-collapsed {
		width: auto;
	}

	.switch_agency_lbl{
		color: #fa424a;
	}
	.switch_agency_lbl:hover{
		color: #00a8ff;
	}
	.dropdown.dropdown-typical a.dropdown-toggle .font-icon-revers {
		color: #fa424a;
	}
	#switch_agency_ul li {
		margin-bottom: 3px;
	}
	#console_terms_view_only{
		margin: 0 20%;
    	display: none;
	}

    .autoComplete_wrapper>input {width: 550px !important;}
    .autoComplete_wrapper>ul {overflow-y:hidden;}
    
    </style>
    
    <?php $this->load->view('templates/phone_mobile_mask'); ?>
</head>

<body class="with-side-menu">
<div id="preloader">
	<div id="status">&nbsp;</div>
</div>

<?php 
	$this->load->view('/templates/themes/'. $this->config->item('theme') .'/header_theme.php');
?> 
