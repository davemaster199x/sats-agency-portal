<?php
// get user data
$data = $this->templatedatahandler->getData();
extract($data);
?>
<ul class="side-menu-list">
	<li class="<?php echo active_link('home','index') ?>">
		<a href="/home">
			<i class="font-icon font-icon-home"></i>
			<span class="lbl">Home</span>
		</a>
	</li>
	<li class="<?php echo active_link('properties','index') ?>">
		<a href="/properties">
			<i class="font-icon font-icon-build"></i>
			<span class="lbl">My Properties</span>
		</a>
	</li>
	<?php
		if($this->config->item('country') == 1){
			if( in_array($data['agency_state'], ['ACT', 'NSW']) ){ ?>
				<li class="<?php echo active_link('compliance','compliance_helper') ?>">
					<a href="/compliance/nsw_inspection_details">
						<i class="font-icon font-icon-doc"></i>
						<span class="lbl">NSW Inspection Details</span>
					</a>
				</li>
	<?php
			}
		}
	?>
	<li class="<?php echo active_link('jobs','index') ?>">
		<a href="/jobs">
			<i class="font-icon font-icon-speed"></i>
			<span class="lbl">Active Jobs</span>
		</a>
	</li>
	<li class="<?php echo active_link('jobs','help_needed') ?>">
		<a href="/jobs/help_needed">
			<i class="fa fa-exclamation-triangle"></i>
			<?php
			if( $esc_jobs_num > 0 ){ ?>
				<span class="label label-pill label-danger left_menu_bubble red_bubbles">
					<?php echo $esc_jobs_num; ?>
				</span>
			<?php
			}
			?>
			<span class="lbl">Help Needed</span>
		</a>
	</li>
	<?php if($renewal_agency_status === 1): ?>
	<li class="<?php echo active_link('jobs','service_due') ?>">
		<a href="/jobs/service_due">
			<i class="fa fa-hourglass-3"></i>
			<?php if( $service_due_jobs > 0 ): ?>
				<span class="label label-pill label-danger left_menu_bubble red_bubbles">
					<?php echo $service_due_jobs; ?>
				</span>
			<?php endif; ?>
			<span class="lbl">Due for <?php echo ( $agency_row->allow_upfront_billing == 1 )?'Subscription':'Service'; ?></span>
		</a>
	</li>
	<?php endif; ?>
	<li class="<?php echo active_link('properties','add') ?>">
		<a href="/properties/add">
			<i class="font-icon fa fa-plus-square"></i>
			<span class="lbl">Add a NEW Property</span>
		</a>
	</li>
	<li class="<?php echo active_link('jobs','create') ?>">
		<a href="/jobs/create">
			<i class="font-icon font-icon-contacts"></i>
			<span class="lbl">Create a Job</span>
		</a>
	</li>
	<?php
	// show only to ADMIN
	if( $user->user_type == 1 || $user->user_type == 2 ){ ?>
		<li class="<?php echo active_link('agency','profile') ?>">
			<a href="/agency/profile">
				<i class="font-icon font-icon-user"></i>
				<span class="lbl">Agency Profile</span>
			</a>
		</li>
	<?php
	}
	?>
	<li class="<?php echo active_link('reports','index') ?>">
		<a href="/reports">
			<i class="fa fa-pie-chart"></i>
			<span class="lbl">My Reports</span>
		</a>
	</li>
	<li class="<?php echo active_link('resources','index') ?>">
		<a href="/resources">
			<i class="font-icon font-icon-doc"></i>
			<span class="lbl">My Resources</span>
		</a>
	</li>

	<?php
	// show only to ADMIN
	if( $user->user_type == 1 ){ ?>

		<li class="<?php echo active_link('user_accounts','index') ?>">
			<a href="/user_accounts">
				<i class="font-icon font-icon-users"></i>
				<span class="lbl">User Accounts</span>
			</a>
		</li>
		<li class="<?php echo active_link('user_accounts','logins') ?>">
			<a href="/user_accounts/logins" id="user_log_link">
				<i class="glyphicon glyphicon-log-in"></i>
				<span class="lbl">User Logins</span>
			</a>
		</li>

	<?php
	}
	?>


	<li class="<?php echo active_link('logs','activity') ?>">
		<a href="/logs/activity">
			<i class="font-icon font-icon-zigzag"></i>
			<span class="lbl">Activity Logs</span>
		</a>
	</li>
	<li class="<?php echo active_link('jobs','calendar') ?>">
		<a href="/jobs/calendar">
			<i class="font-icon font-icon-calend"></i>
			<span class="lbl">Calendar</span>
		</a>
	</li>

	<?php
	// only show to agency connected to API
	if( $this->system_model->integrated_api() != '' ){ ?>

		<li class="<?php echo active_link('api','api_properties') ?>">
			<a href="/api/api_properties">
				<i class="fa fa-share-alt"></i>
				<span class="lbl"><?php echo $this->system_model->integrated_api(); ?> Properties</span>
			</a>
		</li>

	<?php
	}

	//if( $this->session->agency_id == 1448 ){ // visible to Adams Test Agency only
	?>
		<li class="<?php echo active_link('api','connections') ?>">
			<a href="/api/connections">
				<i class="fa fa-share-alt"></i>
				<span class="lbl">Integrations</span>
			</a>
		</li>

	<?php
	//}
	?>
</ul>
