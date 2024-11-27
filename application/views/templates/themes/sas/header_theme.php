<?php
$CI  = & get_instance();
$data = $this->templatedatahandler->getData();
extract($data);
$user_photo_path = '/uploads/user_accounts/photo';
$user_photo = $user->photo;
$user_full_abrv_name = $this->jcclass->formatStaffName($user->fname,$user->lname);
// get escalate jobs

if( $alt_agencies_count>0 ){
	
	$user_params = array(
		'sel_query' => 'aua.agency_user_account_id',
		'active' => 1,
		'agency_id' => $this->session->agency_id,
		'aua_id' => $this->session->aua_id
	);
	$num_rows = $this->user_accounts_model->get_user_accounts($user_params)->num_rows();

	if( $num_rows > 0 ){
		$welcome_agency_name = $user->agency_name. " (Default)";
	}else{
		$welcome_agency_name = $user->agency_name. " (Not Default Agency)";
	}

}else{
	$welcome_agency_name = $user->agency_name;
}

?>
    <style>
        a.dropdown-item:hover .glyphicon-log-out {
            color: #00607f !important;
        }
    </style>
<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary text-light">
    <div class="container-fluid">

        <a class="navbar-brand" href="/home" >
            <!-- <img src="/images/logo.png" alt="logo" style="height: 50px"> -->
            <img src="<?= theme('images/logo.svg')?>" alt="logo" >
        </a>
        <div class="site-header-collapsed sats_header">
            <div class="site-header-collapsed-in">
            
                <div class="dropdown dropdown-typical">
                    <a title="Welcome" class="dropdown-toggle text-light" id="dd-header-social" href="javascript:void(0)" >
                            <span class="lbl">Welcome <?php echo $user_full_abrv_name; ?> from <?php echo $welcome_agency_name; ?></span>
                    </a>
                </div>

                <?php
                // get alternate agencies
                $alt_agencies_count = $data['alt_agencies_count'];
                if( $alt_agencies_count > 0 ){ ?>

                    <div class="dropdown dropdown-typical switch_agency_div">
                        <a data-toggle="tooltip" title="Switch Agency" class="dropdown-toggle" id="switch_agency_icon" href="javascript:void(0);">
                            <span class="font-icon font-icon-revers"></span>
                            <!--<span class="lbl switch_agency_lbl">Switch Agency</span>-->
                        </a>
                    </div>
                <?php } ?>

            </div>
        </div>
        
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item <?= active_link('home','index', 'active') ?>">
                    <a class="nav-link" href="/home">Home</a>
                </li>
                <li class="nav-item dropdown <?= active_link('properties','index', 'active') ?> <?= active_link('properties','add', 'active') ?>">
                    <a class="nav-link dropdown-toggle" href="#" id="propertiesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Properties
                    </a>
                    <div class="dropdown-menu" aria-labelledby="propertiesDropdown">
                        <a class="dropdown-item <?= active_link('properties','index', 'active') ?>" href="/properties">My Properties</a>
                        <a class="dropdown-item <?= active_link('properties','add', 'active') ?>" href="/properties/add">Add a NEW Property</a>
                    </div>
                </li>
                <li class="nav-item dropdown 
                    <?= active_link('jobs','index', 'active') ?>
                    <?= active_link('jobs','create', 'active') ?>
                    <?= active_link('jobs','service_due', 'active') ?>
                    <?= active_link('jobs','calendar', 'active') ?>
                    <?= active_link('jobs','help_needed', 'active') ?>
                ">
                    <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Jobs
                    </a>
                    <div class="dropdown-menu" aria-labelledby="servicesDropdown">
                        <a class="dropdown-item <?= active_link('jobs','index', 'active') ?>" href="/jobs">Active Jobs</a>
                        <a class="dropdown-item <?= active_link('jobs','create', 'active') ?>" href="/jobs/create">Create a Job</a>
                        <a class="dropdown-item <?= active_link('jobs','help_needed', 'active') ?>" href="/jobs/help_needed">
                            Help Needed
                            <?php
                            if( $esc_jobs_num > 0 ){ ?>
                                <span class="label label-pill label-danger">
                                    <?php echo $esc_jobs_num; ?>
                                </span>
                            <?php
                            }
                            ?>
                        </a>
                        <a class="dropdown-item <?= active_link('jobs','service_due', 'active') ?>" href="/jobs/service_due">
                            Due for <?=  $agency_row->allow_upfront_billing == 1 ?'Subscription':'Service'; ?>
                            <?php
                                if( $service_due_jobs > 0 ){ ?>
                                <span class="label label-pill label-danger">
                                    <?php echo $service_due_jobs; ?>
                                </span>
                            <?php
                            }
                            ?>
                        </a>
                        <a class="dropdown-item <?= active_link('jobs','calendar', 'active') ?>" href="/jobs/calendar">Calendar</a>
                    </div>
                </li>
            
            
                <?php
                // show only to ADMIN
                if( $user->user_type == 1 || $user->user_type == 2 ): ?>
                    <li class="nav-item <?= active_link('agency','profile', 'active') ?>">
                        <a class="nav-link" href="/agency/profile">
                            Agency Profile
                        </a>
                    </li>
                <?php endif; ?>
            

                <li class="nav-item dropdown 
                    <?= active_link('reports','index', 'active') ?>
                    <?= active_link('resources','index', 'active') ?>
                    <?= active_link('logs','activity', 'active') ?>
                ">
                    <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Reports
                    </a>
                    <div class="dropdown-menu" aria-labelledby="reportsDropdown">
                        <a class="dropdown-item <?=active_link('reports','index', 'active') ?>" href="/reports">Reports</a>
                        <a class="dropdown-item <?=active_link('resources','index', 'active') ?>" href="/resources">Resources</a>
                        <a class="dropdown-item <?=active_link('logs','activity', 'active') ?>" href="/logs/activity">Activity Logs</a>
                    </div>
                  
                    <!-- <a class="nav-link" href="/reports">Reports</a> -->
                </li>

                <!-- <li class="nav-item <?= active_link('resources','index', 'active') ?>">
                    <a class="nav-link" href="/resources">Resources</a>
                </li> -->
                
                <?php
                // show only to ADMIN
                if( $user->user_type == 1 ): ?>
                <li class="nav-item dropdown 
                    <?=active_link('user_accounts','index', 'active') ?>
                    <?=active_link('user_accounts','logins', 'active') ?>
                ">
                    <a class="nav-link dropdown-toggle" href="#" id="usersDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Users
                    </a>
                    <div class="dropdown-menu" aria-labelledby="usersDropdown">
                        <a class="dropdown-item <?=active_link('user_accounts','index', 'active') ?>" href="/user_accounts">User Account</a>
                        <a class="dropdown-item <?=active_link('user_accounts','logins', 'active') ?>" href="/user_accounts/logins">User Logins</a>
                    </div>
                </li>
                <?php endif; ?>

                <!-- <li class="nav-item <?= active_link('logs','activity', 'active') ?>">
                    <a class="nav-link" href="/logs/activity">
                        Activity Logs
                    </a>
                </li> -->

                <?php
                // only show to agency connected to API
                if( $this->system_model->integrated_api() != '' ): ?>

                    <li class="nav-item <?= active_link('api','api_properties', 'active') ?>">
                        <a class="nav-link" href="/api/api_properties">
                            <?php echo $this->system_model->integrated_api(); ?> Properties
                        </a>
                    </li>

                <?php endif; ?>

                <li class="nav-item <?= active_link('api','connections', 'active') ?>">
                    <a class="nav-link" href="/api/connections">Integration</a>
                </li>               

                <li class="nav-item dropdown 
                    <?=active_link('user_accounts','index', 'active') ?>
                    <?=active_link('user_accounts','logins', 'active') ?>
                ">
                    <a class="nav-link dropdown-toggle" href="#" id="usersDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        
                        <img src="<?php echo ( isset($user_photo) && $user_photo != '' )?"{$user_photo_path}/{$user_photo}":'/images/avatar-2-64.png'; ?>" alt="" class="profile">
                       
                    </a>
                    <div class="dropdown-menu user" aria-labelledby="usersDropdown">
                        <a class="dropdown-item" href="/user_accounts/my_profile/<?= $this->session->aua_id; ?>">User Profile</a>
                        <a class="dropdown-item" href="/user_accounts/logout"><span class="font-icon glyphicon glyphicon-log-out logout"></span> Logout</a>
                    </div>
                </li>
                
            </ul>
        </div>
    </div>
</nav>

<div class="page-content" style="padding-left: 15px;">
						
    <div class="container-fluid">

