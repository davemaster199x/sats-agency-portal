

<link rel="stylesheet" href="/inc/css/lib/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="/inc/css/separate/pages/calendar.min.css">
<style type="text/css">


.calendar-page .calendar-page-content-in{
	/*margin: 0px 314px -1px -1px!important;*/
	margin: auto!important;
}
.calendar-page .calendar-page-side{
	width: 315px!important;
}
.calendar-page .calendar-page-content{
	margin-right: -316px!important;
}
.calendar-page-side .flatpickr-calendar{
	box-shadow:none!important;
}
.calendar-page-side .flatpickr-months{
	padding:0!important;
}
.calendar-page-side .flatpickr-current-month{
	/*left:0!important;*/
}

.calendar-page-side span.flatpickr-weekday{
	width:10.48%!important;
}
h5 .cal_search_box{
	position: absolute;
	width: 500px;
	left: 250px;
	top: -11px;
}
.has-event-ting{
	border: 2px solid #b4151b!important;
}
.cal_header_tt{
	border-bottom: 1px solid #d8e2e7;
	padding-bottom: 10px;
}
#cal_search_form{
	margin-top: 15px;
}
.colors-guide-list li{
	float: left;
	margin-right: 20px;
}

</style>

<section class="box-typical">

	<div class="col-md-12">

		<div class="row cal_header_tt">
			<div class="col-md-3">
				<h5 style="padding-left:20px;margin-bottom:0;position:relative;" class="m-t-lg"><?php echo $title; ?></h5>
			</div>
			<div class="col-md-4">
				<?php echo form_open(base_url('/jobs/json_cal_job'),'id="cal_search_form"'); ?>
				<div class="cal_search_box">
				<div class="row">
					<div class="col-lg-8 ">
						<input class="form-control" type="text" name="cal_search" placeholder="Property Address">
					</div>

					<div class="col-lg-4ss">
						<input class="btn" type="submit" value="Search">
					</div>
				</div>

				</div>
				</form>
			</div>
			<div class="col-md-5">
				<div class="calendar-page-side-section-in" style="margin-top:22px;float:right;">
					<ul class="colors-guide-list">
						<li>
							<div class="color-double"><div></div></div>
							Booked Jobs
						</li>
						<li>
							<div class="color-double green"><div></div></div>
							Completed Jobs
						</li>
					</ul>
				</div>
			</div>
		</div>

	</div>


	<div class="box-typical-body">

		<div class="calendar-page">
				<div class="calendar-page-content">
					<div class="calendar-page-content-in">
						<div id='calendar'></div>
					</div><!--.calendar-page-content-in-->
				</div><!--.calendar-page-content-->

			</div><!--.calendar-page-->
		
	</div>
	<!--.box-typical-body-->


</section>
<!--.box-typical-->


	
	<script type="text/javascript" src="/inc/js/lib/match-height/jquery.matchHeight.min.js"></script>
	<script type="text/javascript" src="/inc/js/lib/moment/moment-with-locales.min.js"></script>
	<script src="/inc/js/lib/fullcalendar/fullcalendar.min.js"></script>
	<script src="/inc/js/lib/fullcalendar/fullcalendar-init.js"></script>
	
	
	<script>


		jQuery(document).ready(function() {



		}); //document ready end


	   
	</script>