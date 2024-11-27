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

<!-- Header -->
<header class="site-header">
    <div class="container-fluid">
        <a href="/home" class="site-logo">
            <img class="hidden-md-down" src="/images/logo.png" alt="">
            <img class="hidden-lg-down" src="/images/logo.png" alt="">
        </a>


            <button id="show-hide-sidebar-toggle" class="show-hide-sidebar">
            <span>toggle menu</span>
        </button>

        <button class="hamburger hamburger--htla">
            <span>toggle menu</span>
        </button>



        <!-- Header icons -->
        <div class="site-header-content">
            <div class="site-header-content-in">



                <!-- Left Header -->
                <div class="mobile-menu-right-overlay"></div>
                <div class="site-header-collapsed sats_header">
                    <div class="site-header-collapsed-in">
                        <!-- <div class="dropdown dropdown-typical">
                            <a data-toggle="tooltip" title="Create a Job" class="dropdown-toggle" id="dd-header-marketing" href="/jobs/create">
                                <span class="font-icon font-icon-contacts"></span>
                                <span class="lbl">Create a Job</span>
                            </a>
                        </div>
                        <div data-toggle="tooltip" title="Add a NEW Property" class="dropdown dropdown-typical">
                            <a title="Add a NEW Property" class="dropdown-toggle" id="dd-header-social" href="/properties/add">
                                <span class="font-icon fa fa-plus-square"></span>
                                <span class="lbl">Add a NEW Property</span>
                            </a>
                        </div>-->

                        <div class="dropdown dropdown-typical">
                            <a title="Welcome" class="dropdown-toggle" id="dd-header-social" href="#" style="color:#00a8ff;">
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


                <!-- Right Header -->
                

                <div class="site-header-shown">

                    <div class="dropdown user-menu">
                        <?php
                        $form_attr = array(
                            'class' => 'site-header-search closed'
                        );
                        echo form_open('/sys/search',$form_attr);
                        ?>
                            <input type="text" placeholder="Search" name="search" />
                            <button type="submit">
                                <span class="font-icon-search"></span>
                            </button>
                            <div class="overlay"></div>
                        </form>
                    </div>

                    <div class="dropdown user-menu">
                        <a data-toggle="tooltip" title="<?php echo $user_full_abrv_name; ?>" class="dropdown-toggle" href="/user_accounts/my_profile/<?php echo $this->session->aua_id; ?>">
                            <button class="dropdown-toggle" id="dd-user-menu" type="button">
                                <img src="<?php echo ( isset($user_photo) && $user_photo != '' )?"{$user_photo_path}/{$user_photo}":'/images/avatar-2-64.png'; ?>" alt="">
                            </button>
                        </a>
                    </div>

                    <div class="dropdown user-menu">
                        <a href="/user_accounts/logout" data-toggle="tooltip" title="Logout">
                            <span class="font-icon glyphicon glyphicon-log-out logout"></span>
                        </a>
                    </div>

                </div>

                <div class="site-header-shown">
                    <?php if($agency_info->agent_number!=""){ ?>
                        <div class="dropdown dropdown-typical">
                            <a style="color:#00a8ff;" data-toggle="tooltip" title="Phone" class="dropdown-toggle" href="tel:<?php echo $agency_info->agent_number; ?>">
                                <span style="color:#00a8ff;" class="glyphicon glyphicon-earphone"></span>
                                <span class="lbl"><?php echo $agency_info->agent_number; ?></span>
                            </a>
                        </div>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>

</header>


	<div class="mobile-menu-left-overlay"></div>
	<nav class="side-menu side-menu-big-icon">
	    <?php $this->load->view('templates/main_menu/left_panel_menu'); ?>
	</nav><!--.side-menu-->

    <div class="page-content">
						
		<!-- Price Error Banner -->
		<?php
		/*
			
			$agency_name_q = $this->gherxlib->agency_info();
			$agency_name = $agency_name_q->agency_name;					

			// agency is excluded to price increase
			$piea_sql = $this->db->query("
			SELECT * 
			FROM `price_increase_excluded_agency` 
			WHERE `agency_id` = {$this->session->agency_id}
			AND (
				`exclude_until` >= '".date('Y-m-d')."' OR
				`exclude_until` IS NULL
			)
			");		

			// agency is completed
			$aci_sql = $this->db->query("
			SELECT * 
			FROM `agency_completed_increase` 
			WHERE `agency_id` = {$this->session->agency_id}
			AND `agency_completed` = 1
			");			
				
			if( $piea_sql->num_rows() == 0 && $aci_sql->num_rows() == 0  ){
		?>
		<div class="container-fluid">
			<div role="alert" class="alert alert-warningss alert-fill alert-close alert-dismissible fade show" style="background: #FFFF00;color: #000 !important;">
												
							<p>Hello <?php echo $agency_name; ?>,</p>
							<p><strong>SATS are aware that there is an error that is not showing the new price increase from the 1st August 2022.</strong></p>
							<p>Please be advised the price increase is still going ahead and we have our friendly team looking into this. For now, please continue to create a job with the previous pricing and we will be able to amend this on our end. </p>
							<p>Thank you for your understanding and patience whilst SATS rectify this issue, if you have any further concerns, please do not hesitate to contact the team.</p>
							<p>SATS</p>

			</div>
		</div>
		<?php } 
		*/
		?>
		<!-- Price Error Banner end -->

<div class="container-fluid">