<style>
    #pie_preview_textfield{
        font-weight: bold;
        text-align: center;
        margin-top: 5px;
        position: relative;
        left: -50%;
        line-height: 12px;
        margin-bottom: 6px;
    }
    .pie_preview_textfield_div{
        position: absolute;
        left: 50%;
        bottom: 33px;
    }
    #auditProperties_btn{
        padding: 7px 10px;
    }
    .widget-tabs-nav.colored .font-icon, .widget-tabs-nav.colored .font-icon:hover{
        color: #fff;
    }
    .statistic-box{
        height: 103px!important;
    }
    .statistic-box .caption{
        min-height: 25px!important;
    }
    .statistic-box .number{
        padding: 15px 0 0!important;
    }

    @media screen and (min-width: 768px) {
        #preview{
            position:absolute;
            bottom:33px;
        }
    }
</style>

<?php
    if($this->session->country_id == 1){ //AU
        $calendy_url = "https://calendly.com/info-6717";
    }else{ //NZ
        $calendy_url = "https://calendly.com/info-6718";
    }
?>

<!-- Calendly badge widget begin -->
<link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
<script src="https://assets.calendly.com/assets/external/widget.js" type="text/javascript"></script>
<script type="text/javascript">Calendly.initBadgeWidget({url: '<?php echo $calendy_url; ?>', text: 'Book Portal Training', color: '#dc3545', branding: false});</script>
<!-- Calendly badge widget end -->

<div class="row">
    <div class="col-md-6 columns">
        <div class="card-block_tt">
            
            <!-- NEW HOME WIDGET START -->
            <div class="row">
                <div class="col-md-12 columns">
                    <section class="widget">
                        <header class="widget-header-dark">Portfolio Compliance</header>
                        <div class="tab-content widget-tabs-content">
                            <div class="tab-pane active" id="w-1-tab-1" role="tabpanel">
                                <?php
                                    $compliance_percent = $compliance / $tot_porfolio * 100;
                                    //$tot_complicance = $tot_porfolio - $non_compliance_count;
                                    //$compliance_percent = $tot_complicance / $tot_porfolio * 100;
                                ?>
                                
                                <?php if($tot_porfolio<=0){ ?>
                                    
                                    <div class="text-center"><a href="javascript::" data-fancybox data-src="#hidden-content">Please Add Total Portfolio</a></div>
                                
                                <?php }else{ ?>
                                    
                                    <div class="circle-progress-bar-typical pieProgress"
                                         role="progressbar" data-goal="<?php echo round($compliance_percent,1); ?>"
                                         data-barcolor="#00a8ff"
                                         data-barsize="10"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        <span class="pie_progress__number"></span>
                                    </div>
                                
                                <?php } ?>
                            
                            
                            </div>
                            <div class="tab-pane" id="w-1-tab-2" role="tabpanel" style="min-height: 250px;">
                                <div id="donut-chart" style="height:250px;"></div>
                                
                                <a style="position: absolute;right: 35px;" data-fancybox data-src="#hidden-content" class="fbox_link" href="javascript:;">
                                    <div id="portfolio_div">Total Portfolio (<?php echo $tot_porfolio; ?>)</div>
                                </a>
                            </div>
                            <div class="tab-pane txt-center" id="w-1-tab-3" role="tabpanel">
                                <!--<div id="gauge-chart"></div>-->
                                <canvas id="gauge_canvas"></canvas>
                            </div>
                        </div>
                        <div class="widget-tabs-nav colored">
                            <ul class="tbl-row nav" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active blue" data-toggle="tab" href="#w-1-tab-1" role="tab">
                                        <i class="font-icon fa fa-pie-chart"></i>
                                        COMPLIANCE
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link red" data-toggle="tab" href="#w-1-tab-2" role="tab">
                                        <i class="font-icon fa font-icon-chart-3"></i>
                                        BREAKDOWN
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link orange tt_gauge_tab_link" data-toggle="tab" href="#w-1-tab-3" role="tab">
                                        <i class="font-icon fa fa-tachometer"></i>
                                        RISK LEVEL
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </section>
                </div>
            </div>
            <!-- NEW HOME WIDGET END -->
            
            
            <!-- Non-Compliant link
            <a href="/reports/non_compliant">
                <div class="non_comp_custom_link">
                    <i class="fa fa-arrow-circle-right"></i>
                </div>
            </a>
            -->
            
            
            <div style="display: none;" id="hidden-content" class="total_porfolio_div">
                <h2>Total Porfolio</h2>
                <p><input name="tot_porfolio" type="text" id="tot_porfolio" class="form-control" value="<?php echo $tot_porfolio; ?>" /></p>
                <div><button type="button" id="tot_porfolio_save_btn" class="btn btn-inline">Save</button></div>
            </div>
            
            
            
            <div class="row">
                
                <div class="col-lg-12 dahsboard-column">
                    
                    <div class="row">
                        
                        <div class="col-md-9 columns">
                            <section>
                                <?php echo form_open('/sys/search','id=search_form'); ?>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input name="search" type="text" id="search" class="form-control" placeholder="Search Property">
                                        <div class="input-group-append">
															<span class="input-group-text search_span">
																<span class="glyphicon glyphicon-search"></span>
															</span>
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </section>
                        </div>
                        <div class="col-md-3 columns">
                            <div  style="display:none;" id="audit_button_box" class="audit_button_box txt-center"><a id="auditProperties_btn" class="btn btn-sm" href="#">Audit My Properties</a></div>
                        </div>
                    
                    </div>
                    
                    
                    <section class="box-typical box-typical-dashboard panel panel-default scrollable">
                        <header class="box-typical-header panel-heading">
                            <h3 class="panel-title">
                                <a href="/logs/activity" target="_blank">
                                    Recent Activity
                                </a>
                            </h3>
                        </header>
                        <div class="box-typical-body panel-body">
                            <table class="tbl-typical">
                                <tr>
                                    <th><div>Date</div></th>
                                    <th><div>&nbsp;</div></th>
                                    <th><div>User</div></th>
                                    <th><div>Title</div></th>
                                    <th><div>Details</div></th>
                                </tr>
                                
                                <?php
                                    foreach ( $recent_activity  as $index => $row ){
                                        ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y H:i',strtotime($row->created_date)); ?></td>
                                            <td><img class="profile_pic_small border border-info" src="<?php echo ( isset($row->photo) && $row->photo != '' )?"{$user_photo_upload_path}/{$row->photo}":$default_avatar; ?>" /></td>
                                            <td>
                                                <?php
                                                    if( $row->created_by_staff == -4 ){ // Console API
                                                        
                                                        echo "Console";
                                                        
                                                    }else{ // default, users
                                                        
                                                        echo $this->jcclass->formatStaffName($row->fname,$row->lname);
                                                        
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo $row->title_name; ?></td>
                                            <td>
                                                <?php
                                                    echo $this->jcclass->parseDynamicLink($row);
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                ?>
                            </table>
                        </div>
                    </section>
                    
                    
                    <section class="box-typical box-typical-dashboard panel panel-default scrollable">
                        <header class="box-typical-header panel-heading">
                            <h3 class="panel-title">
                                <a href="/user_accounts" target="_blank">
                                    Our Team
                                </a>
                            </h3>
                        </header>
                        <div class="box-typical-body panel-body">
                            <div class="contact-row-list">
                                <?php
                                    foreach ( $our_team  as $index => $row ){
                                        
                                        $user_account_link = "/user_accounts/my_profile/{$row->agency_user_account_id}";
                                        
                                        ?>
                                        <article class="contact-row">
                                            <div class="user-card-row">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell tbl-cell-photo">
                                                        <a href="<?php echo $user_account_link; ?>">
                                                            <img src="<?php echo ( isset($row->photo) && $row->photo != '' )?"{$user_photo_upload_path}/{$row->photo}":$default_avatar; ?>" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="tbl-cell">
                                                        <p class="user-card-row-name"><a href="<?php echo $user_account_link; ?>"><?php echo $this->jcclass->formatStaffName($row->fname,$row->lname); ?></a></p>
                                                        <p class="user-card-row-mail"><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a></p>
                                                    </div>
                                                    <div class="tbl-cell tbl-cell-status"><?php echo $row->job_title; ?></div>
                                                </div>
                                            </div>
                                        </article>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div><!--.box-typical-body-->
                    </section><!--.box-typical-dashboard-->
                
                
                </div><!--.col-->
            
            </div>
        </div>
    </div>
    
    <!-- RIGHT COL -->
    <div class="col-md-6 columns">
        <div class="row">
            
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-sm-6">
                        <a href="/jobs/help_needed">
                            <article class="statistic-box" style="background-color: pink;">
                                <div>
                                    <div class="number"><?php echo $esc_jobs_num; ?></div>
                                    <div class="caption"><div>Help Needed</div></div>
                                </div>
                            </article>
                        </a>
                    </div><!--.col-->
                    
                    <div class="col-sm-6">
                        <?php if( $this->gherxlib->agency_info()->state=="QLD" ){
                            $qld_upgrade_quotes_link = "/reports/qld_upgrade_quotes?status_filter=2";
                            $qld_upgrade_quotes_link_total = $get_qld_upgrade_quotes_total;
                            $qld_upgrade_quotes_link_title = "Approve Quotes";
                        }else{
                            $qld_upgrade_quotes_link="#";
                            $qld_upgrade_quotes_link_total = "";
                            $qld_upgrade_quotes_link_title = "";
                        }
                        ?>
                        <a target="_blank" href="<?php echo $qld_upgrade_quotes_link; ?>">
                            <article class="statistic-box" style="background-color:#00a8ff;">
                                <div>
                                    <div class="number"><?php echo $qld_upgrade_quotes_link_total; ?></div>
                                    <div class="caption"><div><?php echo $qld_upgrade_quotes_link_title; ?></div></div>
                                </div>
                            </article>
                        </a>
                    </div><!--.col-->
                    
                    <div class="col-sm-6">
                        <a href="/jobs?job_status=Booked">
                            <article class="statistic-box green">
                                <div>
                                    <div class="number"><?php echo $booked_jobs; ?></div>
                                    <div class="caption"><div>Jobs Booked</div></div>
                                </div>
                            </article>
                        </a>
                    </div><!--.col-->
                    <div class="col-sm-6">
                        <a target="_blank" href="/reports/completed_jobs/?pdf=1&output_type=I&from=<?php echo date('01/m/Y') ?>&to=<?php echo date('t/m/Y') ?>">
                            <article class="statistic-box yellow">
                                <div>
                                    <div class="number"><?php echo $comp_jobs; ?></div>
                                    <div class="caption"><div>Jobs Completed in <?php echo date("F"); ?></div></div>
                                </div>
                            </article>
                        </a>
                    </div><!--.col-->
                    
                    <div class="col-sm-6">
                        <a href="/jobs/service_due">
                            <article class="statistic-box red <?php echo ($pending_jobs == 0 || ( date('d') >= 1 && date('d') <= 14 ) )?'fadeIt':''; ?>">
                                <div>
                                    <div class="number">
                                        <span id="auto_renew_d">0</span>
                                    </div>
                                    <div class="caption"><div>Days Until Auto-Renew</div></div>
                                </div>
                            </article>
                        </a>
                    </div><!--.col-->
                    <div class="col-sm-6">
                        <a href="/sms/job_feedback">
                            <article class="statistic-box purple">
                                <div>
                                    <div class="number"><?php echo $job_feedback; ?></div>
                                    <div class="caption"><div>Tenant Job Feedback</div></div>
                                </div>
                            </article>
                        </a>
                    </div><!--.col-->
                    
                    <?php if($check_agency_accounts_reports_preference) { ?>
                        <div class="col-sm-6">
                            <a href="/reports/unpaid_invoices">
                                <article class="statistic-box yellow">
                                    <div>
                                        <div class="number">
                                            <span id="auto_renew_d"><?php echo "$".number_format($tot_invoice_bal_not_overdue,2) ?></span>
                                        </div>
                                        <div class="caption"><div>Unpaid Invoices</div></div>
                                    </div>
                                </article>
                            </a>
                        </div><!--.col-->
                        <div class="col-sm-6">
                            <a href="/reports/overdue_invoices">
                                <article class="statistic-box green">
                                    <div>
                                        <div class="number">
                                            <span id="auto_renew_d"><?php echo "$".number_format($tot_invoice_bal_overdue,2) ?></span>
                                        </div>
                                        <div class="caption"><div>Overdue Invoices</div></div>
                                    </div>
                                </article>
                            </a>
                        </div><!--.col-->
                    <?php } ?>
                
                </div><!--.row-->
            </div><!--.col-->
        </div><!--.row-->
        <div class="row">
            
            <div class="col-lg-12 dahsboard-column">
                
                <section class="card card-blue-fill">
                    <header class="card-header">
                        Noticeboard
                    </header>
                    <div class="card-block">
                        <p class="card-text">
                            <?php echo $noticeboard; ?>
                        </p>
                    </div>
                </section>
            
            </div><!--.col-->
        </div>
    </div>
</div>



<!-- Monthly Portfolio Popup Start -->
<?php
    $tot_prop_timestamp =  date('Y-m-d', strtotime($get_tot_prop_timestamp->tot_prop_timestamp));
    $oneMonth = date('Y-m-d', strtotime('+1 month', strtotime($tot_prop_timestamp)));

?>
<a style="display:none;" data-fancybox data-src="#porfolio_monthly_popup" id="porfolio_monthly_popup_link" href="javascript:void(0);">click me</a>
<div style="display: none;" id="porfolio_monthly_popup">
    <p class="txt-center" style="color:#00a8ff;font-size:19px;">It has been some time since you updated your portfolio number.</p>
    <div class="txt-center"><img style="margin-bottom:15px;height:140px;" src="/images/update_portfolio.jpg"></div>
    <p class="txt-center">
        In order for us to provide you with the most accurate compliance figure we need to confirm the amount of properties in your portfolio.
        <br/>The more accurate the number you provide us will determine the accuracy of your compliance %.</p>
    <div class="total_porfolio_div txt-center">
        <p>
            <label style="color:#00a8ff;">Total Portfolio</label>
            <input style="max-width: 100px;margin:auto;" name="tot_porfolio2" type="text" id="tot_porfolio2" class="form-control" value="<?php echo $tot_porfolio; ?>" />
        </p>
        <div><button style="margin:0;" type="button" id="tot_porfolio_save_btn2" class="btn btn-inline">Save</button></div>
    </div>
</div>
<!-- Monthly Portfolio Popup END -->


<!-- Welcome Text lightbox -->
<a data-fancybox data-src="#welcome_msg_fb" id="welcome_msg_fb_link" href="javascript:void(0);">click me</a>
<div style="display: none;" id="welcome_msg_fb" >
    <?php
        $currDate =  date('Y-m-d');
        $feb1 = date('2019-02-01');
        $isFeb1 = ($currDate>=$feb1)?true:false;
    ?>
    <div class="text-center">
        <img style="padding-bottom:30px" src="/images/logo.png" alt="">
        <div class="welcome-img"><img src='/images/welcome-img.png' height= 70px; width= 250px;></div>
        
        <?php if($isFeb1==true){ ?>
            <p>Our Portal is available 24/7 and is simple to use with features such as:</p>
        <?php }else{
            ?>
            <p>A new look and feel has been applied to the portal whilst retaining all other functions.</p>
            <?php
        } ?>
    
    
    </div>
    <div class="uliuli_wtf">
        <?php if($isFeb1==false){ ?>
            <p>We’ve kept it simple whilst adding new features such as:</p>
            <p>We've kept it simple whilst adding new features such as:</p>
        <?php } ?>
        
        <div class="align-middle">
            <ul class="welcome_box_ul">
                <li>Track current compliance</li>
                <li>New reporting options</li>
                <li>View progress of jobs live</li>
                <li>Individual Property Managers with own  user Login & Master Login</li>
                <li>Individual User Profile pages with Photos and contact details</li>
                <li>API Integration with the leading property management software providers (Please contact us if you need help on how to connect)</li>
            </ul>
        </div>
    </div>
    <div class="text-center">
        <p>If you have any trouble logging in or require further assistance navigating the portal, please contact our friendly Customer Service team on <?php echo ( $this->config->item('country')==1 ) ? "1300 41 66 67" : "0508 766 532" ?>.</p>
        <div class="checkbox text-center txt-red">
            <input type="checkbox" id="welcome_msg_chk" />
            <label for="welcome_msg_chk" class="welcome-msg-text">Don't show this message again</label>
        </div>
    </div>
</div>


<style>
    .widget-time.aquamarine .widget-time-content {
        color: #404041;
        background-color: #FFFF00;
        border-color: #00a8ff;
    }

    .qld_upgrade_weeks_div {
        color: #fff !important;
        background-color: #fa424a !important;
        border-color: #fa424a !important;
    }

    .qld_upgrade_div_left{
        padding-left: 0;
        padding-right: 5px;
    }

    .qld_upgrade_div_right{
        padding-left: 5px;
        padding-right: 0;
    }

    .c3-chart-arc path {
        stroke-width: 2.5px;
    }
    .glyphicon-search{
        cursor: pointer;
    }
    .search_span{
        color: white !important;
        background-color: #00a8ff;
        border: 1px solid #00a8ff;
    }
    #search{
        border: 1px solid #00a8ff;
    }
    #portfolio_div{
        float: right;
        position: relative;
        bottom: 23px;
        font-size: 12px;
        font-family: sans-serif;
        font-weight: 400;
    }
    a.fbox_link, a.fbox_link:focus, a.fbox_link:hover{
        color: black;
    }
    #tot_porfolio_save_btn{
        margin: 0;
    }
    .fadeIt{
        opacity: 0.2;
    }
    .non_comp_custom_link{
        float: left;
        position: relative;
        bottom: 26px;
        left: 67%;
    }
    .fa-hourglass-1{
        font-size: 55px;
        position: relative;
        top: 6px;
        right: 12px;
    }
    #welcome_msg_fb,
    #welcome_msg_fb_link{
        display: none;
    }
    .welcome_box_ul{
        list-style-type: initial;
        margin: auto;
        margin-bottom: 20px;
        text-align: left;
        margin-left:7px;
        color:#015a8a;
    }
    .welcome_box_ul li{
        margin-left:10px;
    }
    .uliuli_wtf{
        width: 45%;
        margin:auto;
    }

    .welcome-img{
        padding-bottom: 20px;
    }

    .welcome-msg-text{
        color:#b32025;
    }

</style>
<script src="/inc/js/lib/d3/d3.min.js"></script>
<script src="/inc/js/lib/charts-c3js/c3.min.js"></script>
<script src="/inc/js/lib/gauge/gauge2.min.js"></script>
<script src="/inc/js/lib/asPieProgress/jquery-asPieProgress.min.js"></script>
<script>
    function IcUpgradePerWeek(countdown_week){

        var upgraded_prop = parseInt(<?php echo $upgraded_prop; ?>);
        var tot_porfolio = parseInt(<?php echo $tot_porfolio; ?>)

        //console.log("upgraded_prop: "+upgraded_prop);
        //console.log("tot_porfolio: "+tot_porfolio);
        //console.log("countdown_week: "+countdown_week);

        var ic_uprade_per_week = Math.ceil((tot_porfolio-upgraded_prop)/countdown_week);
        //var ic_uprade_per_week = Math.ceil(tot_porfolio/countdown_week);
        //jQuery("#ic_uprade_per_week").html(ic_uprade_per_week);

    }


    function IcUpgradeCountdownTimer(){

        // countdown
        var countDownDate = new Date("Jan 1, 2022 00:00:00").getTime();


        // Update the count down every 1 second
        var x = setInterval(function() {

            // Get todays date and time
            var now = new Date().getTime();

            // Find the distance between now and the count down date
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var weeks = Math.floor( distance / (1000 * 60 * 60 * 24 * 7) );
            var days = Math.floor( ( distance % (1000 * 60 * 60 * 24 * 7) ) / (1000 * 60 * 60 * 24) );
            var hours = Math.floor( ( distance % (1000 * 60 * 60 * 24) ) / (1000 * 60 * 60) );
            var minutes = Math.floor( (distance % (1000 * 60 * 60) ) / (1000 * 60) );
            var seconds = Math.floor( (distance % (1000 * 60)) / 1000 );

            // inserted --> IC upgrade for week
            IcUpgradePerWeek(weeks);

            jQuery("#countdown_week").html(weeks);
            jQuery("#countdown_day").html(days);
            //jQuery("#countdown_hour").html(hours);
            //jQuery("#countdown_min").html(minutes);
            //jQuery("#countdown_sec").html(seconds);

            // If the count down is finished, write some text
            if (distance < 0) {
                clearInterval(x);
                //document.getElementById("demo").innerHTML = "EXPIRED";
                jQuery("#countdown_week").html(0);
                jQuery("#countdown_day").html(0);
                //jQuery("#countdown_hour").html(0);
                //jQuery("#countdown_min").html(0);
                //jQuery("#countdown_sec").html(0);
            }
        }, 1000);

    }




    function AutoRenewCountdownTimer(){

        var d = new Date();
        <?php
        if( $this->session->country_id == 1 ){ // AU ?>
        var auto_renew_day = 15;
        <?php
        }else if( $this->session->country_id == 2 ){ // NZ ?>
        var auto_renew_day = 1;
        <?php
        }
        ?>

        var this_day = d.getDate();
        // month starts from 0-11
        var this_month = d.getMonth();
        if(this_day>auto_renew_day){
            this_month++;
        }
        var this_year = d.getFullYear();

        // countdown
        var countDownDate = new Date(parseInt(this_year), parseInt(this_month), auto_renew_day, 17).getTime();


        // Update the count down every 1 second
        var x = setInterval(function() {

            // Get todays date and time
            var now = new Date().getTime();

            // Find the distance between now and the count down date
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor( distance / (1000 * 60 * 60 * 24) );

            jQuery("#auto_renew_d").html(days);

            // If the count down is finished, write some text
            if (distance < 0) {

                clearInterval(x);
                jQuery("#auto_renew_d").html(0);

            }

        }, 1000);

    }

    // Pie Chart
    function PieChart(){

        var compliance = <?php echo $compliance ?>;
        var non_compliance = <?php echo $tot_porfolio; ?>-compliance;

        // pie chart
        var pieChart = c3.generate({
            color: {
                pattern: ['#00a8ff', '#dc3545']
            },
            bindto: '#pie-chart1',
            data: {
                columns: [
                    ['Compliant ('+compliance+')', compliance],
                    ['Non Compliant ('+non_compliance+')', non_compliance]
                ],
                type : 'pie'
            },
            padding: {
                bottom: 30
            }


        });

    }

    function DonutChart(){

        var compliance = <?php echo $compliance ?>;
        var non_compliance = <?php echo $tot_porfolio; ?>-compliance;

        var donutChart = c3.generate({
            color: {
                pattern: ['#00a8ff', '#dc3545']
            },
            bindto: '#donut-chart',
            data: {
                columns: [
                    ['Compliant ('+compliance+')', compliance],
                    ['Non Compliant ('+non_compliance+')', non_compliance]
                ],
                type : 'donut'
            },
            donut: {
                title: "BREAKDOWN"
            }
        });

    }


    function offer_2fa(){

        swal({
                title: "",
                text: "Two-factor Authentication is available, would like to set it up now?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Yes",
                cancelButtonClass: "btn-danger",
                cancelButtonText: "No, Later",
                closeOnConfirm: true,
                showLoaderOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {

                if (isConfirm) {

                    window.location='/user_accounts/my_profile/<?php echo $this->session->aua_id; ?>/?popup_2fa=1';

                }

            });

    }
    
    $(document).ready(function() {

        //progress circle
        $(".circle-progress-bar-typical").asPieProgress({
            namespace: 'asPieProgress',
            speed: 25
        });

        $(".circle-progress-bar-typical").asPieProgress("start");
        //progress circle end

        DonutChart();
        
        <?php
        if( $this->session->flashdata('access_denied') == 1 ){ ?>
        // update success
        swal({
            title: "Access Denied!",
            text: "You are not allowed to access that page",
            type: "warning",
            confirmButtonClass: "btn-warning"
        });
        <?php
        }
        ?>
        
        
        <?php
        if( $this->session->flashdata('initial_setup_success') == 1 ){ ?>
        // update success
        swal({
            title: "Success!",
            text: "Initial Setup Complete",
            type: "success",
            confirmButtonClass: "btn-success"
        });
        <?php
        }
        ?>
        
        
        <?php
        if($tot_prop_timestamp=='-0001-11-30'){ //not valid/set
        ?>
        jQuery("#porfolio_monthly_popup_link").click();
        <?php
        }else{
        if(date('Y-m-d')>=$oneMonth){
        ?>
        jQuery("#porfolio_monthly_popup_link").click();
        <?php
        }
        }
        ?>
        
        
        <?php
        if( $hide_welcome_msg == 0 ){ ?>
        jQuery("#welcome_msg_fb_link").click();
        <?php
        }
        ?>
        
        <?php
        // if user has no active 2FA, offer 2FA via popup
        if( $offer_2fa == true ){ ?>
        offer_2fa();
        <?php
        }
        ?>



        // NEW QLD Legislation countdown timer
        IcUpgradeCountdownTimer();
        
        
        <?php
        if($pending_jobs > 0){ ?>
        // Auto-Renew countdown timer
        AutoRenewCountdownTimer();
        <?php
        }
        ?>


        // hide welcome message
        jQuery("#welcome_msg_chk").click(function(){

            jQuery.ajax({
                type: "POST",
                url: "/user_accounts/hide_welcome_message"
            }).done(function( ret ) {

            });

        });




        // Update total portfolio
        jQuery("#tot_porfolio_save_btn").click(function(){

            var tot_porfolio = jQuery(this).parents("div.total_porfolio_div:first").find("#tot_porfolio").val();

            jQuery.ajax({
                type: "POST",
                url: "/home/update_total_portfolio",
                data: {
                    tot_porfolio: tot_porfolio
                }
            }).done(function( ret ) {

                swal({
                    title: "Success!",
                    text: "Total Portfolio Updated",
                    type: "success",
                    confirmButtonClass: "btn-success",
                    closeOnConfirm: false
                },function(){

                    location.reload();

                });

            });

        });

        // Update total portfolio 2 (For Monthly Popup)
        jQuery("#tot_porfolio_save_btn2").click(function(){

            var tot_porfolio = jQuery("#tot_porfolio2").val();

            jQuery.ajax({
                type: "POST",
                url: "/home/update_total_portfolio",
                data: {
                    tot_porfolio: tot_porfolio
                }
            }).done(function( ret ) {

                $.fancybox.close(); // close fancybox

                swal({
                    title: "Success!",
                    text: "Thank you <?php echo $aua_row->fname; ?>",
                    type: "success",
                    confirmButtonClass: "btn-success",
                    showConfirmButton: false,
                    timer: 2000 // 2 seconds
                });

            });

        });



        // search
        jQuery(".glyphicon-search").click(function(e){
            e.preventDefault();
            jQuery("#search_form").submit();
        });

        // pie chart
        //PieChart();

        $('.panel').each(function () {
            try {
                $(this).lobiPanel({
                    sortable: true,
                    editTitle: false,
                    unpin: false,
                    reload: false
                }).on('dragged.lobiPanel', function(ev, lobiPanel){
                    $('.dahsboard-column').matchHeight();
                });
            } catch (err) {}
        });


        $(window).resize(function(){
            //drawChart();
            setTimeout(function(){
            }, 1000);
        });


        $('#auditProperties_btn').on('click',function(e){
            e.preventDefault();

            swal(
                {
                    title: "",
                    text: '<?=$this->config->item('COMPANY_NAME_SHORT')?> Provides a FREE Audit Service for our Medium to High Risk clients. If you would like to go ahead please click Yes and your <?=$this->config->item('COMPANY_NAME_SHORT')?> Representative will contact you shortly.',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Yes!",
                    cancelButtonClass: "btn-danger",
                    cancelButtonText: "No, Cancel",
                    closeOnConfirm: false,
                    closeOnCancel: true,
                },
                function(isConfirm){
                    if(isConfirm){

                        jQuery.ajax({
                            type: "POST",
                            url: "<?php echo base_url('/home/audit_properties') ?>",
                            dataType: 'json',
                            data: {
                                agency_id: <?php echo $this->session->agency_id ?>,
                            }
                        }).done(function(data){
                            if(data.status){
                                swal({
                                    title:"Success!",
                                    text: "Request successfully submitted",
                                    type: "success",
                                    showCancelButton: false,
                                    confirmButtonText: "OK",
                                    closeOnConfirm: false,

                                },function(isConfirm){
                                    if(isConfirm){
                                        location.reload();
                                    }
                                });
                            }else{
                                location.reload();
                            }
                        });

                    }

                }
            );

        })

        // fullscreen height bug fix
        jQuery(".glyphicon-resize-full").click(function(){

            jQuery(".box-typical.box-typical-dashboard .box-typical-body").css("height",'100%');

        });

        $('.tt_gauge_tab_link').click(function(){
            setTimeout(function() {
                gauge_tt()
            }, 500);
        })
        show_hide_audit_button();
        
    });

    function show_hide_audit_button(){
        var gaugeMaxVal  = 100;
        var compliance_2 = <?php echo $compliance ?>;
        var non_compliance_2 = <?php echo $tot_porfolio; ?>-compliance_2;
        var total_portfolio_2 = <?php echo $tot_porfolio; ?>;
        var currGaugeValPercnet = Math.floor((non_compliance_2/total_portfolio_2) * 100);
        var currGaugeVal = (currGaugeValPercnet/gaugeMaxVal)* 100;

        if(currGaugeVal>=35){
            document.getElementById("audit_button_box").style.display = "block";
        }

    }

    function gauge_tt(){

        // GAUGE CODE START
        var compliance_2 = <?php echo $compliance ?>;
        var non_compliance_2 = <?php echo $tot_porfolio; ?>-compliance_2;
        var total_portfolio_2 = <?php echo $tot_porfolio; ?>;

        //var gaugeMaxVal  = (total_portfolio_2 / total_portfolio_2) * 100;
        var gaugeMaxVal  = 100;
        var devide_2 = Math.floor(gaugeMaxVal / 3);
        var zone1_2 = 0 + devide_2;
        var zone2_2 = zone1_2 + devide_2;
        var zone3_2 = zone2_2 + devide_2;

        var currGaugeValPercnet = Math.floor((non_compliance_2/total_portfolio_2) * 100);
        var currGaugeVal = (currGaugeValPercnet/gaugeMaxVal)* 100;

        var opts = {
            angle: 0, /// The span of the gauge arc
            lineWidth: 0.40, // The line thickness
            radiusScale: 1,
            pointer: {
                length: 0.6, // Relative to gauge radius
                strokeWidth: 0.035 // The thickness
            },
            staticZones: [
                {strokeStyle: "#46c35f", min: 0, max: zone1_2},
                {strokeStyle: "#FFA500", min: zone1_2+1, max: zone2_2},
                {strokeStyle: "#fa424a", min: zone2_2+1, max: gaugeMaxVal},

            ],
            staticLabels: {
                font: "10px sans-serif",  // Specifies font
                //labels: [0, 50, 100],  // Print labels at these values
                labels: [0,50,gaugeMaxVal],
                color: "#000000",  // Optional: Label text color
                fractionDigits: 0  // Optional: Numerical precision. 0=round off.
            },

            colorStart: '#6FADCF',   // Colors
            colorStop: '#8FC0DA',    // just experiment with them
            strokeColor: '#E0E0E0',   // to see which ones work best for you
            generateGradient: true,
            highDpiSupport: true,

        };
        var target = document.getElementById('gauge_canvas'); // your canvas element
        var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
        gauge.maxValue = gaugeMaxVal; // set max gauge value
        gauge.setMinValue(0);  // set min value
        gauge.set(currGaugeVal); // set actual value

        /*if(currGaugeVal>=35){
            document.getElementById("audit_button_box").style.display = "block";
        }*/
        // GAUGE CODE END

    }


</script>
