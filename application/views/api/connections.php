<style>
    .faded{
        opacity: 0.35;
    }
    .greenBorder{
        border: 2px solid #46c35f;
    }
    .company_logo{
        cursor: pointer;
    }
    .form-control:focus {
        color: #495057;
        background-color: #fff;
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0rem rgba(0, 123, 255, .25)
    }

    .btn-secondary:focus {
        box-shadow: 0 0 0 0rem rgba(108, 117, 125, .5)
    }

    .close:focus {
        box-shadow: 0 0 0 0rem rgba(108, 117, 125, .5)
    }

    .mt-200 {
        margin-top: 200px
    }

    .autocomplete {
        /*the container must be positioned relative:*/
        position: relative;
        display: inline-block;
    }
    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100%;
        left: 0;
        right: 0;
    }
    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }
    .autocomplete-items div:hover {
        /*when hovering an item:*/
        background-color: #e9e9e9;
    }
    .autocomplete-active {
        /*when navigating through the items using the arrow keys:*/
        background-color: DodgerBlue !important;
        color: #ffffff;
    }
    .no-sort::after { display: none !important; }
    .no-sort::before { display: none !important; }

    .no-sort { pointer-events: none !important; cursor: default !important; }
    /* .sw-theme-dots>ul.step-anchor>li.active>a:after {
        margin-left: 3px !important;
    }
    .sw-theme-dots>ul.step-anchor>li.done>a:after {
        margin-left: 2px !important;
    } */
    .modal-body {
        position: relative;
        -webkit-box-flex: 1;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        padding: 0rem !important;
    }
   .sw-btn-prev, .sw-btn-next{
        width: 50% !important;
    }  
    .btn-toolbar {
        width: 100% !important;
    }
    .sw-theme-default .sw-toolbar {
        background: white !important;
        border-radius: 0!important;
        padding-left: 0px !important;
        padding-right: 0px !important;
        padding: 0px !important;
        margin-bottom: 0!important;
    }
    .mr-2, .mx-2 {
        margin-right: 0rem !important;
    }
    .num {
        border-color: #0082c6;
    }
    .num {
        border: solid 1px #919fa9;
        -webkit-border-radius: 50%;
        border-radius: 50%;
        display: inline-block;
        vertical-align: middle;
        width: 24px;
        height: 24px;
        text-align: center;
        line-height: 22px;
        position: relative;
        top: -1px;
        margin: 0 4px 0 0;
    }
    .nav-item .nav-link {
        text-align: center;
        vertical-align: middle;
    }
    .active .num {
        border-color: #0082c6;
    }
    #console_fb_terms{
        margin: 0 20%;
        display: none;
    }
    /* #console_accept_terms_btn{
        display: none;
    } */
    .ul_terms{
        list-style-type: disc !important;
    }

    .list-group-item{
        border: none;
    }

    #console_fb_terms{
        margin: 0 20%;
        display: none;
    }
    #console_accept_terms_btn{
        display: none;
    }
    .ul_terms{
        list-style-type: disc !important;
    }
    #preloader{
        opacity: 0.7;
    }
    #pt_preference_tbl_div table td{
        border: none;
    }
    #pt_select_settings{
        border: 3px solid #16b4fc;
    }
    .ur_connected_api_logo{
        width: 250px !important;
    }
    
    #smartwizard .tab-content{
        height: auto !important; /* css fix for step 1 content not displaying */
    }
    .smartwizard .sw-btn-prev {
        width: 50% !important;
        background: #ffffff !important;
        border: solid 1px #00a8ff !important;
        color: #00a8ff !important;
    }

    .smartwizard .sw-btn-next {
        width: 50% !important;
        color: #ffffff !important;
        background: #00a8ff !important;
        border: solid 1px #00a8ff !important;
        border-radius: 0px !important;
    }
</style>
<section class="box-typical box-typical-padding">

    <!-- <h5 class="m-t-lg with-border"><?php echo $title; ?></h5> -->


    <!-- list -->
    <div class="box-typical-body">
        
        <?php if ( count($is_integrated) > 0 ): // connected ?>            

            <?php
            foreach ($is_integrated as $row):

                /*
                //escape TAPI API and BRICKS and AGENT API, OURPROPERTY and PROPERTYTREE API is hold for now
                if (in_array($row->agency_api_id, [2,7,6,3])) {
                    continue;
                }
                */
                
                $logo_img =  'logo/'.$row->img_name.'_logo.png';
                ?>
                <!-- PropertyMe Welcome Connected Page -->
                <?php if($row->agency_api_id == 1 ): ?>
                <div class="container">
                    <h1 class="text-center mt-4">You are connected! <i class="fa fa-check-circle text-success" aria-hidden="true"></i> </h1>
                    <img src="/images/api/<?=$logo_img?>" alt="PropertyMe logo" class="d-flex mx-auto mb-4 ur_connected_api_logo">

                    <p class="font-weight-bold mb-4">What is next?</p>
                    <ul class="ul-list-bullet">
                        <li><?=$this->config->item('COMPANY_NAME_SHORT')?> will sync all actively serviced properties in our database with properties in PropertyMe</li>
                        <li>If properties are archived in PropertyMe but active in the <?= $this->config->item('COMPANY_NAME_SHORT') ?> System, we will contact your office to verify the property status </li>                        
                    </ul>

                    <p class="font-weight-bold mt-4 mb-4">What Data can <?= $this->config->item('COMPANY_NAME_SHORT') ?> See:</p>
                    <ul class="ul-list-bullet">
                        <li>Property address and status </li>
                        <li>Tenant Details </li>
                        <li>Key Details </li>
                        <li>The assigned Property Manager </li>
                    </ul>

                    <p class="font-weight-bold mt-4 mb-4">Invoices:</p>
                    <ul class="ul-list-bullet">
                        <li>Invoices are uploaded and will appear in the 'Due' tab located in the Bills section of PropertyMe (can be disabled by <?= $this->config->item('COMPANY_NAME_SHORT') ?> at your request) </li>
                        <li>
                            <p>Invoices can be auto approved for <?= $this->config->item('COMPANY_NAME_SHORT') ?>, please check the preference selected for <?= $this->config->item('COMPANY_NAME_SHORT') ?> in our supplier details </p>
                            <img src="/images/api/<?=$this->config->item('theme')?>_propertyME_supplier.png" class="w-75 d-flex mx-auto" alt="propertMe_supplier">
                        </li>
                    </ul>

                    <p class="font-weight-bold mt-4 mb-4">Statement of Compliance: </p>
                    <ul class="ul-list-bullet">
                        <li>Statements of Compliance are uploaded and will appear in the 'Documents' tab located in the property address in PropertyMe (can be disabled by <?= $this->config->item('COMPANY_NAME_SHORT') ?> at your request) </li>
                    </ul>

                    <p class="font-weight-bold mt-4 mb-4">Fees:</p>
                    <ul class="ul-list-bullet">
                        <li>Currently no fee is charged to use the PropertyMe API </li>
                    </ul>

                    <p class="font-weight-bold mt-4 mb-4">FAQ:</p>
                    <p class="font-weight-bold font-italic">Do I still need to send work orders? </p>
                    <p>Yes, If you would like <?= $this->config->item('COMPANY_NAME_SHORT') ?> to attend for any service other than the annual inspection, you will need to send us a work order or create a job via the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Agency Portal </p>
                    <div class="mb-5">
                        <img src="/images/api/<?=$this->config->item('theme')?>_pme_edit_supplier_contact.png"  class="w-75 d-flex mx-auto" alt="propertMe_supplier">
                    </div>

                    <p class="font-weight-bold font-italic">Can <?= $this->config->item('COMPANY_NAME_SHORT') ?> just look at my lease details and create jobs automatically? </p>
                    <p>No, you will need to send us a work order or create a job via the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Agency Portal </p>

                    <p class="font-weight-bold">Do I still need to use the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Portal? </p>
                    <p>Yes, Properties that are due to be renewed for annual service still need to be checked and processed. </p>
                </div>
            <?php endif; ?>

                <!-- Palace Welcome Connected Page -->
                <?php if($row->agency_api_id == 4 ): ?>
                <div class="container">
                    <h1 class="text-center mt-4 mb-4">You are connected! <i class="fa fa-check-circle text-success" aria-hidden="true"></i></h1>
                    <img src="/images/api/<?=$logo_img?>" alt="Palace logo" class="d-flex mx-auto mb-4">

                    <p class="font-weight-bold mt-4 mb-4">What is next?</p>
                    <ul class="ul-list-bullet">
                        <li><?= $this->config->item('COMPANY_NAME_SHORT') ?> will sync all actively serviced properties in our database with properties in Palace</li>
                        <li>If properties are archived in Palace but active in the <?= $this->config->item('COMPANY_NAME_SHORT') ?> System, we will contact your office to verify the property status </li>                        
                    </ul>

                    <p class="font-weight-bold mt-4 mb-4">What Data can <?= $this->config->item('COMPANY_NAME_SHORT') ?> See:</p>
                    <ul class="ul-list-bullet">
                        <li>Property address and status </li>
                        <li>Tenant Details </li>
                        <li>Key Details </li>
                        <li>The assigned Property Manager </li>
                    </ul>

                    <p class="font-weight-bold mt-4 mb-4">Invoices:</p>
                    <ul class="ul-list-bullet">
                        <li>Invoices are uploaded and will appear in the Invoice Document Workflow, located in the financial section of Palace (can be disabled by <?= $this->config->item('COMPANY_NAME_SHORT') ?> at your request) </li>
                    </ul>

                    <p class="font-weight-bold mt-4 mb-4">Statement of Compliance: </p>
                    <ul class="ul-list-bullet">
                        <li>Statements of Compliance are uploaded and will appear in the 'Safety' tab located in the property address in Palace (can be disabled by <?= $this->config->item('COMPANY_NAME_SHORT') ?> at your request) </li>
                        <li>Smoke alarm compliance criteria auto -filled in the 'Safety' tab located in the property address in Palace </li>
                    </ul>

                    <p class="font-weight-bold mt-4 mb-4">Fees:</p>
                    <ul class="ul-list-bullet">
                        <li>Currently no fee is charged to use the Palace API </li>
                    </ul>

                    <p class="font-weight-bold mt-4 mb-4">FAQ:</p>
                    <p class="font-weight-bold font-italic">Do I still need to send work orders? </p>
                    <p>Yes, If you would like <?= $this->config->item('COMPANY_NAME_SHORT') ?> to attend for any service other than the annual inspection, you will need to send us a work order or create a job via the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Agency Portal </p>

                    <p class="font-weight-bold font-italic">Can <?= $this->config->item('COMPANY_NAME_SHORT') ?> just look at my lease details and create jobs automatically? </p>
                    <p>No, you will need to send us a work order or create a job via the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Agency Portal </p>

                    <p class="font-weight-bold">Do I still need to use the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Portal? </p>
                    <p>Yes, Properties that are due to be renewed for annual service still need to be checked and processed. </p>

                    <p class="font-weight-bold font-italic">The API connection does not appear to be syncing correctly?  </p>
                    <p>If you are experiencing any issues, please ensure that we are connected to “Liquid” and not “Legacy” </p>
                </div>
            <?php endif; ?>

                <!-- Console Cloud Welcome Connected Page -->
                <?php if($row->agency_api_id == 5 ): ?>
                <div id="console_api_container" class="container">
                    <h1 class="text-center mt-4 mb-4">You are connected! <i class="fa fa-check-circle text-success" aria-hidden="true"></i> </h1>
                    <img src="/images/api/<?=$logo_img?>" alt="Console logo" class="d-flex mx-auto mb-4">

                    <h5>Terms and Conditions</h5>

                    <p class="font-weight-bold uppercase mt-4">Adding compliance items:</p>
                    <ul>
                        <li>
                            <p class="text-justify">Any compliance items assigned to <?=$this->config->item('COMPANY_NAME_SHORT')?> will trigger a visit and an annual Subscription Fee</p>
                        </li>
                        <li>
                            <p class="text-justify"><?=$this->config->item('COMPANY_NAME_SHORT')?> will attend all active properties only when required, to meet your states legislation</p>
                        </li>
                    </ul>

                    <p class="font-weight-bold uppercase">Visit not required for compliance:</p>
                    <ul>
                        <li>
                            <p class="text-justify">If you require any additional visits outside of what is required for legislation (eg, Beeping Alarm<?php echo ( $this->session->country_id == 1 && $agency_row->state != 'QLD' )?'/Change of Tenancy':null; ?>) please create a job in our Agency Portal or email us at <a href="<?= make_email('info'); ?>"><?= make_email('info'); ?></a></p>
                        </li>
                    </ul>

                    <p class="font-weight-bold uppercase">Data discrepancies:</p>
                    <ul>
                        <li>
                            <p class="text-justify"><?=$this->config->item('COMPANY_NAME_SHORT')?> will not amend our existing expiry dates if the property has previously been serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?>, and there is a discrepancy between Console/<?=$this->config->item('COMPANY_NAME_SHORT')?> expiry date data. However, for new properties, where applicable, please add the last inspection date and subscription expiry date so that <?=$this->config->item('COMPANY_NAME_SHORT')?> can ensure that there are no data discrepancies.</p>
                        </li>
                    </ul>

                    <p class="font-weight-bold uppercase">Currently serviced properties:</p>
                    <ul>
                        <li>
                            <p class="text-justify">Any property that is currently serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?> will remain on the same service option, we will not adjust the service based on data received from Console. If you wish to change the services that <?=$this->config->item('COMPANY_NAME_SHORT')?> conducts on a property, you must do this via the <a href="/jobs/create">Agency Portal</a> or by contacting our friendly Customer Service team on <?php echo $agency_row->agent_number; ?>. Any new properties added to our database via Console will only have the service type applied that is the compliance item.</p>
                        </li>
                    </ul>

                    <p class="font-weight-bold uppercase">Delivery of documents into console</p>
                    <ul>
                        <li>
                            <p class="text-justify">Upon job completion, we will upload into Console, the Statement of Compliance (Workflows > Compliance) and where applicable, the Invoice (Accounts>Bills).</p>
                        </li>
                    </ul>

                    <table id="console_api_logs" class="tbl-typical">
                        <thead>
                        <th><div>Date</div></th>
                        <th><div>User</div></th>
                        <th><div>Title</div></th>
                        <th><div>Details</div></th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            <?php endif; ?>


            <!-- PropertyTree Connected Page -->
            <?php 
                
                if($row->agency_api_id == 3 ){ 
                
                // check if full connected
                $pt_agen_pref_sql = $this->db->query("
                SELECT COUNT(`pt_ap_id`) AS pt_ap_count
                FROM `propertytree_agency_preference`
                WHERE `agency_id` = {$this->session->agency_id}
                AND `active` = 1
                ");

                $fully_connected = ( $pt_agen_pref_sql->row()->pt_ap_count > 0 )?true:false;
                $pt_title = ( $fully_connected == true )?'You are Connected':'You are connected to the light version of Property Tree';
                ?>

                <div class="container">

                    <h1 class="text-center mt-4"><?php echo $pt_title; ?> <i class="fa fa-check-circle text-success" aria-hidden="true"></i> </h1>

                    <?php
                    if( $fully_connected == false ){ // only show on partial connection ?>
                        <p class="text-center">(Want to update to the full version to receive Invoices and Statements of compliance in property Tree? Click <a id="pt_pref_link_popup" href="javascript:void(0);">HERE</a> to update)</p>
                    <?php
                    }
                    ?>                    

                    <img src="/images/api/<?=$logo_img?>" alt="PropertyMe logo" class="d-flex mx-auto mb-4 ur_connected_api_logo">                    

                    <p class="font-weight-bold mb-4">What is next?</p>
                    <ul class="ul-list-bullet">
                        <li><?=$this->config->item('COMPANY_NAME_SHORT')?> will sync all actively serviced properties in our database with properties in Property Tree</li>
                        <li>If properties are archived in Property Tree but active in the <?=$this->config->item('COMPANY_NAME_SHORT')?> System, we will contact your office to verify the property status</li>                        
                    </ul>

                    <p class="font-weight-bold mt-4 mb-4">What Data can <?= $this->config->item('COMPANY_NAME_SHORT') ?> See:</p>
                    <ul class="ul-list-bullet">
                        <li>Property address and status </li>
                        <li>Tenant Details </li>
                        <li>Key Details </li>
                        <li>The assigned Property Manager </li>
                    </ul>   
                    
                    <?php
                    if( $fully_connected == true ){ ?>

                        <p class="font-weight-bold mt-4 mb-4">Invoices:</p>
                        <ul class="ul-list-bullet">
                            <li>Invoices are uploaded directly into 'Create Creditor Invoice' located in the accounting section of Property Tree (can be disabled by <?= $this->config->item('COMPANY_NAME_SHORT') ?> at your request)</li>           
                        </ul>

                        <p class="font-weight-bold mt-4 mb-4">Statement of Compliance: </p>
                        <ul class="ul-list-bullet">
                            <li>Statements of Compliance are uploaded directly into the 'Compliance' tab located within the property address in Property Tree (can be disabled by <?= $this->config->item('COMPANY_NAME_SHORT') ?> at your request)</li>
                            <li>Smoke alarm compliance criteria auto-completed in the 'Compliance Register' located within the property address in Property Tree</li>
                        </ul>

                        <p class="font-weight-bold mt-4 mb-4">Fees:</p>
                        <ul class="ul-list-bullet">
                            <li>Currently no fee is charged to use the Property Tree API</li>
                        </ul>

                    <?php
                    }
                    ?>                    

                    <p class="font-weight-bold mt-4 mb-4">FAQ:</p>

                    <p class="font-weight-bold font-italic">Do SATS close compliance items when complete?</p>
                    <p>Yes. <?= $this->config->item('COMPANY_NAME_SHORT') ?> will close any compliance item when complete and open a new one.</p>

                    <p class="font-weight-bold font-italic">Do I still need to send work orders? </p>
                    <p>Yes, If you would like <?= $this->config->item('COMPANY_NAME_SHORT') ?> to attend for any service other than the annual inspection, you will need to send us a work order or create a job via the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Agency Portal </p>
                   
                    <p class="font-weight-bold font-italic">Can <?= $this->config->item('COMPANY_NAME_SHORT') ?> just look at my lease details and create jobs automatically? </p>
                    <p>No, you will need to send us a work order or create a job via the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Agency Portal </p>

                    <p class="font-weight-bold">Do I still need to use the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Portal? </p>
                    <p>Yes, Properties that are due to be renewed for annual service still need to be checked and processed. </p>
                
                </div>

            <?php 
            } 
            
            ?>

            <?php endforeach; ?>
        
        <?php else: // not connected ?>

            <div class="api-integration">

                <div class="api-integration__dropdown dropdown show">
                    <a class="btn btn-primary dropdown-toggle text-wrap p-2" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space:normal">
                        <?= $this->config->item('COMPANY_NAME_SHORT') ?> offers connections with various Trust Accounting Software platforms.<br/> Please select your software from the list below:
                    </a>

                    <div class="dropdown-menu w-100" aria-labelledby="dropdownMenuLink">
                        <?php foreach ($agency_apis as $row):
                            //escape TAPI API and BRICKS and AGENT API, OURPROPERTY and PROPERTYTREE API is hold for now
                            if (in_array($row->agency_api_id, [2,7,6, 5,3])) {
                                continue;
                            }

                            // temporary show only PropertyMe API in SAS as requested by ness
                            if( config_item('theme') === 'sas' && $row->agency_api_id != 1){
                                continue;
                            }
                            
                            $logo_img =  'logo/'.$row->img_name.'_logo.png';
                            $api_link = null;
                            $is_integrated = 0;
                            
                            // check if permission granted on crm
                            $permission_granted = ( $row->permission_granted )?1:0;
                            
                            $connected = ( $row->connected )?1:0;
                            
                            // connected
                            if( $row->agency_api_id == 5  ){ // console using webhook API that uses API keys, instead of agency tokens
                                
                                // check if agency has API key stored
                                $cak_sql = $this->db->query("
                                SELECT COUNT(`id`) AS cak_count
                                FROM `console_api_keys`
                                WHERE `agency_id` = {$this->session->agency_id}
                                AND `active` = 1
                                ");
                                
                                if( $cak_sql->row()->cak_count > 0 ){
                                    
                                    $is_integrated = 1;
                                    $logo_img = $row->img_name.'_connected.png';
                                    
                                }
                                
                                
                            }else{ // using agency tokens
                                
                                if( $connected == 1 ){
                                    $logo_img = $row->img_name.'_connected.png';
                                }
                                
                            }
                            
                            if( $row->agency_api_id == 1 ){
                                $api_link = $this->api_model->pme_auth_link();
                            }
                            
                            if( $row->agency_api_id == 6 ){
                                $api_link = base_url()."ourtradie";
                            }
                            
                            if( $row->agency_api_id == 4 ){
                                $api_link = "";
                            }
                            ?>
                            <a class="dropdown-item company_logo lead" href="javascript:void(0);"
                               id="api_id_<?php echo $row->agency_api_id; ?>"
                               data-api_id="<?php echo $row->agency_api_id; ?>"
                               data-is_permission_granted="<?php echo $permission_granted; ?>"
                               data-api_link="<?php echo $api_link; ?>"
                               data-is_integrated="<?php echo $is_integrated; ?>"
                               data-image="/images/api/<?= $logo_img ?>"
                               data-is_connected="<?php echo $connected; ?>"
                            >
                                <img src="/images/api/<?= $logo_img ?>" width="210" height="60" alt="logo" class="" />
                                <?= $row->api_name ?>
                            </a>
                        <?php endforeach; ?>

                    </div>

                </div>
            </div>
            
        <?php endif; ?>

    </div><!--.box-typical-body-->

    <!-- <link href="/inc//css/smart_wizard.min.css" rel="stylesheet" type="text/css" />
    <link href="/inc//css/smart_wizard_theme_dots.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/inc//js/jquery.smartWizard.min.js"></script> -->




    <link rel="stylesheet" href="/inc/css/lib/datatables-net/datatables.min.css">
    <link rel="stylesheet" href="/inc/css/separate/vendor/datatables-net.min.css">
    <script src="/inc/js/lib/datatables-net/datatables.min.js"></script>
    <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
    <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>


    <div class="container">
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Palace API Login</h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                    </div>
                    <div class="modal-body">
                        <div id="smartwizard">
                            <ul class="nav mt-4">
                                <li class="nav-item"><a class="nav-link" href="#step-1"><span class="num">1</span><small class="title-text">Account Info</small></a></li>
                                <li class="nav-item"><a class="nav-link" href="#step-2"><span class="num">2</span><small class="title-text">Supplier</small></a></li>
                                <li class="nav-item"><a class="nav-link" href="#step-3"><span class="num">3</span><small class="title-text">Agent</small></a></li>
                                <li class="nav-item"><a class="nav-link" href="#step-4"><span class="num">4</span><small class="title-text">Diary Group</small></a></li>
                                <li class="nav-item"><a class="nav-link" href="#step-5"><span class="num">5</span><small class="title-text">Complete</small></a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-6"> <input type="text" class="form-control" placeholder="Username" id="palaceUser" required> </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-6"> <input type="password" class="form-control" placeholder="Password" id="palacePass" required> </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-6">
                                            <!--
                                            <select id="palacePermi" class="form-control" required>
                                                <option value="">-- Select system --</option>
                                                <option value="Legacy">Live</option>
                                                <option value="Liquid">Liquid</option>
                                            </select>
                                            -->
                                            <input type="hidden" id="palacePermi" value="Liquid" />
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-3 text-right">
                                            <button class="btn btn-primary" id="palaceLogin" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Logging in..">Login</button>
                                            <button class="btn btn-primary" id="palaceLoginLoading" style="display: none;" disabled><i class='fa fa-spinner fa-spin '></i> Logging in..</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
                                    <div class="row" style="display: none;">
                                        <div class="col-md-5">
                                            <label class="form-label semibold" for="exampleInput"></label>
                                            <div class="input-group mb-3">

                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <label class="form-label semibold" for="exampleInput">Connected Supplier Id</label>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" id="suppId" disabled>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary btn-danger" type="button" id="removeSupp">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <label for="suppInput">Please select the <?=$this->config->item('COMPANY_NAME_SHORT')?> supplier you created in Palace.</label>
                                                <div class="autocomplete" style="width: 100%">
                                                    <input id="suppInput" class="form-control" type="text" name="myApi" placeholder="Search supplier here...">
                                                </div>
                                            </div>
                                            <table class="table table-striped table-bordered api_supp_table" style="width: 100%" >
                                                <thead>
                                                <tr>
                                                    <th class="no-sort">Reference</th>
                                                    <th class="no-sort">Primary Person</th>
                                                    <th class="no-sort"></th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3">
                                    <div class="row" style="display: none;">
                                        <div class="col-md-5">
                                            <label class="form-label semibold" for="exampleInput"></label>
                                            <div class="input-group mb-3">

                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <label class="form-label semibold" for="exampleInput">Connected Agent Id</label>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" id="agentId" disabled>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary btn-danger" type="button" id="removeAgent">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <label for="agentInput">Which user will approve invoices?</label>
                                                <div class="autocomplete" style="width: 100%">
                                                    <input id="agentInput" class="form-control" type="text" name="myApi" placeholder="Search agent here...">
                                                </div>
                                            </div>
                                            <table class="table table-striped table-bordered api_agent_table" style="width: 100%" >
                                                <thead>
                                                <tr>
                                                    <th class="no-sort">Email</th>
                                                    <th class="no-sort">Primary Person</th>
                                                    <th class="no-sort"></th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                                <div id="step-4" class="tab-pane" role="tabpanel" aria-labelledby="step-4">
                                    <div class="row" style="display: none;">
                                        <div class="col-md-5">
                                            <label class="form-label semibold" for="exampleInput"></label>
                                            <div class="input-group mb-3">

                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <label class="form-label semibold" for="exampleInput">Connected Diary Id</label>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" id="diaryId" disabled>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary btn-danger" type="button" id="removeDiary">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <label for="diaryInput">How would you like a <?=$this->config->item('COMPANY_NAME_SHORT')?> visit to appear in your diary?</label>
                                                <div class="autocomplete" style="width: 100%">
                                                    <input id="diaryInput" class="form-control" type="text" name="myApi" placeholder="Search diary here...">
                                                </div>
                                            </div>
                                            <table class="table table-striped table-bordered api_diary_table" style="width: 100%" >
                                                <thead>
                                                <tr>
                                                    <th class="no-sort">Diary Group Description</th>
                                                    <th class="no-sort"></th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                                <div id="step-5" class="tab-pane" role="tabpanel" aria-labelledby="step-5">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <label for="diaryInput">The following details have been selected:</label><br>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="diaryInput">Connected Supplier Name:</label>
                                                <div class="autocomplete" style="width: 100%">
                                                    <input type="text" class="form-control" id="suppName" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="diaryInput">Connected Agent Name:</label>
                                                <div class="autocomplete" style="width: 100%">
                                                    <input type="text" class="form-control" id="agentName" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="diaryInput">Connected Diary Description:</label>
                                                <div class="autocomplete" style="width: 100%">
                                                    <input type="text" class="form-control" id="diaryName" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php
        $this->load->view('api/_form_wizard/_propertyme');
        $this->load->view('api/_form_wizard/_palace');
        $this->load->view('api/_form_wizard/_consolecloud');
    ?>
</section><!--.box-typical-->

<!-- PMe more details - START -->
<a style="display: none" href="javascript:;" id="pme_details_fb_link" class="fb_trigger" data-fancybox data-src="#pme_details_fb">Trigger the fancybox</a>
<div id="pme_details_fb" class="fancybox" style="display:none; border-radius: 5px;" >

    <h4>Select agency to connect!</h4>
    <div class="body-typical-body" style="padding-top:25px;">
        <form action="<?php echo base_url(); ?>ourtradie/updateagencyid" id="jform" method="post" accept-charset="utf-8">
            <div class="form-group row">
                <div class="col-md-12" id="sms_temp_left_panel">
                    <div class="form-group row">
                        <label class="col-md-3 form-control-label">Agency List</label>
                        <div class="col-md-8">
                            <p class="form-control-static">
                                <select name="agency_id" id="user_class" class="form-control" data-validation="[NOTEMPTY]">
                                    <option value="">--- Select ---</option>
                                    <?php
                                        foreach ($_SESSION['list'] as $row) {
                                            foreach ($row as $key) { ?>
                                                <option value="<?php echo $key['AgencyID']; ?>"><?php echo $key['AgencyName']; ?></option>
                                                <?php
                                            }
                                        }
                                    ?>
                                </select>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 form-control-label">&nbsp;</label>
                        <div class="col-md-8">
                            <p class="form-control-static">
                                <button type="submit" class="btn" id="btn_submit">Connect</button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
        //print_r($_SESSION['list']);
    ?>

</div>
<!-- PMe more details - END -->



<!-- fancybox start -->
<div id="console_fb" class="fancybox" style="display:none;width:650px;">

    <h3>Insert Activation Key</h3>
    <p>Already have the activation key from Console’s Marketplace? Just paste it in the box below and press the “Yes Activate” button. If you don’t have the key yet, please click <a target="_blank" href="https://www.youtube.com/watch?v=bi-sRxmFxbw">HERE</a> to watch a brief tutorial, then click the “No, Redirect to Console” button to continue.</p>

    <textarea class="form-control mb-3" id="console_api_key" style="height:150px;"></textarea>
    <input type="hidden" id="console_api_link" />
    
    <?php
        // dynamic console connect link
        $console_link = ( ENVIRONMENT == 'production' )?'https://app.console.com.au':'https://sandbox.saas-uat.console.com.au/';
    ?>
    <a href="<?php echo $console_link; ?>" target="_blank">
        <button type="button" id="console_active_btn_no" class="btn btn-danger mr-3">No, Redirect to Console</button>
    </a>

    <button type="button" id="console_active_btn_yes" class="btn btn-success float-right">Yes!, Activate</button>

</div>

<div id="console_fb_terms" class="fancybox">

    <p>To proceed, you must read and accept the following terms and conditions:</p>

    <div>
        <?php echo $this->system_model->console_terms_and_conditions(); ?>
    </div>

    <div class="checkbox">
        <input name="console_accept_terms_chk" type="checkbox" id="console_accept_terms_chk">
        <label for="console_accept_terms_chk">By ticking, you are confirming that you have read, understood and agree the terms and conditions.</label>
    </div>

    <div class="text-right">
        <button type="button" id="console_accept_terms_btn" class="btn btn-success">Agree and continue</button>
    </div>

</div>
<!-- fancybox end -->

<script>
    jQuery(document).ready(function(){

        table = $('.api_table').DataTable();

        var supploaded = 0;
        var agentloaded = 0;
        var diaryloaded = 0;

        jQuery("#palaceLogin").click(function(){

            var palaceUser = $("#palaceUser").val();
            var palacePass = $("#palacePass").val();
            // var palacePassConfirm = $("#palacePassConfirm").val();
            var palacePermi = $("#palacePermi").val();

            if (palaceUser != "" && palacePass != "" && palacePermi != "") {
                if (palacePass !== "") {
                    supploaded = 0;
                    agentloaded = 0;
                    diaryloaded = 0;
                    $("#palaceLoginLoading").show();
                    $("#palaceLogin").hide();
                    jQuery.ajax({
                        url: "/api/connect_palace",
                        type: 'POST',
                        data: {
                            'palaceUser': palaceUser,
                            'palacePass': palacePass,
                            'palacePermi': palacePermi,
                            'agencyId' : <?=$this->session->agency_id?>
                        },
                        dataType: 'json'
                    }).done(function( ret ){

                        var api_result = parseInt(ret.result);

                        if ( api_result == 1 ) {
                            setTimeout(function(){
                                $('.sw-btn-next').prop('disabled', false);
                                $('.sw-btn-next').trigger( "click" );
                                $("#palaceLoginLoading").hide();
                                $("#palaceLogin").show();
                                $("#palaceLogin").html("Re-login");
                                $(".sw-btn-prev").attr('style', 'color: #6c7a86 !important;');
                                $(".sw-btn-prev").html('← Previous');
                                $(".sw-btn-next").html('Next→');
                                $(".sw-btn-prev").hover(function() {
                                    $(this).attr('style', 'background-color: #ffffff !important; color: #6c7a86 !important;border: solid 1px #d8e2e7 !important;');
                                });
                                $(".sw-btn-next").hover(function() {
                                    $(this).attr('style', 'background-color: #46c35f !important;    border: solid 1px #46c35f !important;');
                                });
                            }, 1000);
                        }else {

                            setTimeout(function(){
                                swal("Invalid Login", ret.error, "error");
                                $("#palaceLoginLoading").hide();
                                $("#palaceLogin").show();
                                $("#palaceLogin").html("Re-login");
                            }, 1000);

                        }


                    });
                }else {
                    swal("Cancelled", "Password does not match.", "error");
                }
            }else {
                setTimeout(function(){
                    swal("Cancelled", "All fields can not be empty.", "error");
                }, 1);
            }
        });

        /*
        $('#smartwizard').smartWizard({
            selected: 0,
            theme: 'dots',
            autoAdjustHeight:true,
            transitionEffect:{
                animation: 'fade'
            },
            enableUrlHash: false,
            toolbar: {
                position: 'none',
            },
        });
        */

        $('#smartwizard').smartWizard({
            selected: 0,
            theme: 'dots',
            autoAdjustHeight:true,
            transitionEffect:'fade',
            showStepURLhash: false
        });

        function autocomplete(inp, arr) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function(e) {
                var a, b, i, val = this.value;
                /*close any already open lists of autocompleted values*/
                closeAllLists();
                if (!val) { return false;}
                currentFocus = -1;
                /*create a DIV element that will contain the items (values):*/
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                /*append the DIV element as a child of the autocomplete container:*/
                this.parentNode.appendChild(a);
                /*for each item in the array...*/
                for (i = 0; i < arr.length; i++) {
                    /*check if the item starts with the same letters as the text field value:*/
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                        /*create a DIV element for each matching element:*/
                        b = document.createElement("DIV");
                        /*make the matching letters bold:*/
                        b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                        b.innerHTML += arr[i].substr(val.length);
                        /*insert a input field that will hold the current array item's value:*/
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                        /*execute a function when someone clicks on the item value (DIV element):*/
                        b.addEventListener("click", function(e) {
                            /*insert the value for the autocomplete text field:*/
                            inp.value = this.getElementsByTagName("input")[0].value;
                            /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                }
            });

            /*execute a function presses a key on the keyboard:*/
            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    /*If the arrow DOWN key is pressed,
                    increase the currentFocus variable:*/
                    currentFocus++;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 38) { //up
                    /*If the arrow UP key is pressed,
                    decrease the currentFocus variable:*/
                    currentFocus--;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 13) {
                    /*If the ENTER key is pressed, prevent the form from being submitted,*/
                    e.preventDefault();
                    if (currentFocus > -1) {
                        /*and simulate a click on the "active" item:*/
                        if (x) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                /*a function to classify an item as "active":*/
                if (!x) return false;
                /*start by removing the "active" class on all items:*/
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                /*add class "autocomplete-active":*/
                x[currentFocus].classList.add("autocomplete-active");
            }

            function removeActive(x) {
                /*a function to remove the "active" class from all autocomplete items:*/
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }

            function closeAllLists(elmnt) {
                /*close all autocomplete lists in the document,
                except the one passed as an argument:*/
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }

            /*execute a function when someone clicks in the document:*/
            document.addEventListener("click", function (e) {
                closeAllLists(e.target);
            });
        }

        //$("#smartwizard .sw-btn-group").hide();
        $("#smartwizard .toolbar").attr('style','display: none !important'); // needs to apply css, this way bec .hide or .css is not working
        $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {

            window.addEventListener("keydown", function(event) {
                // arrow key
                if ([37, 39].indexOf(event.keyCode) > -1) {
                    event.preventDefault();
                    alert("You have to click next step.")
                }
            });

            if (stepNumber == 0 && stepDirection == "backward") {
                $('.sw-btn-next').prop('disabled', false);
            }
            if (stepNumber == 1 && stepDirection == "forward") {
                if ($("#suppId").val() !== "Not yet connected") {
                    setTimeout(function(){
                        $('.sw-btn-next').prop('disabled', false);
                    }, 500);
                }else {
                    setTimeout(function(){
                        $('.sw-btn-next').prop('disabled', true);
                    }, 500);
                }
            }

            if (stepNumber == 1 && stepDirection == "forward" && supploaded == 0) {
                //$(".sw-btn-group").show();
                $("#smartwizard .toolbar").attr('style','display: flex !important'); // needs to apply css, this way bec .hide or .css is not working
                $('.api_supp_table').DataTable().destroy();
                var table = $(".api_supp_table tbody");
                jQuery("#preloader").delay(200).fadeIn("slow");
                jQuery.ajax({
                    url: "/palace/get_palace_supplier",
                    type: 'POST',
                    data: {
                        'agency_id': <?=$this->session->agency_id?>
                    }
                }).done(function( ret ){
                    var res = JSON.parse(ret);
                    $("#suppId").val(res.supp == null || res.supp == "" ? "Not yet connected" : res.supp);
                    $("#suppName").val(res.suppName == null || res.suppName == "" ? "Not yet connected" : res.suppName.SupplierCompanyName);
                    // if (res.suppName != null && res.suppName != "") {
                    //     jQuery(this).val(res.suppName.SupplierCompanyName);
                    //     search_pme_datatable(res.suppName.SupplierCompanyName);
                    // }
                    table.empty();
                    var supplierReference = [];
                    $.each(res.palace, function (a, b) {
                        var dis = (res.supp == b.SupplierCode) ? 'disabled' : '';
                        var btnSuc = "btn-primary";
                        var btnTxt = "Select";
                        if (res.supp == b.SupplierCode) {
                            btnSuc = 'btn-success';
                            btnTxt = 'Selected';
                        }
                        supplierReference.push(b.SupplierCompanyName);
                        table.append("<tr><td>"+b.SupplierCompanyName+"</td>" +
                            "<td>" + b.SupplierContactFirstName + " " +b.SupplierContactLastName +"</td>" +
                            "<td> <button type='button' class='btn con_supp "+btnSuc+"' id-attr='"+b.SupplierCode+"' "+dis+" name-attr='"+b.SupplierCompanyName+"' style='display: block;margin: auto;'>"+btnTxt+"</button></td></tr>");
                    });
                    $(".api_supp_table").show();
                    if ( $.fn.dataTable.isDataTable( '.api_supp_table' ) ) {
                        table = $('.api_supp_table').DataTable();
                    }
                    else {
                        table = $('.api_supp_table').DataTable( {
                            "bInfo" : false,
                            "pageLength": 5,
                            // "bPaginate": false,
                            "lengthChange": false,
                            "columnDefs": [
                                { "orderable": false, "targets": 0 }
                            ]
                            // "searching": false,
                        } );
                    }
                    supploaded = 1;
                    jQuery("#preloader").delay(200).fadeOut("slow");
                    $('#DataTables_Table_0_filter label').hide();
                    table.search("^&*").draw();
                    $("#DataTables_Table_0_paginate").hide();

                    if ($("#suppId").val() !== "Not yet connected") {
                        setTimeout(function(){
                            $('.sw-btn-next').prop('disabled', false);
                        }, 500);
                    }else {
                        setTimeout(function(){
                            $('.sw-btn-next').prop('disabled', true);
                        }, 500);
                    }

                    // autocomplete(document.getElementById("suppInput"), supplierReference);
                    function search_pme_datatable(text){
                        jQuery('.api_supp_table').DataTable().search( text, false, true ).draw();
                    }
                    jQuery(document).on("click","#suppInputautocomplete-list div",function(){
                        var text = $("#suppInput").val();
                        search_pme_datatable(text);
                    })
                    jQuery("#suppInput").keyup(function(){
                        var text = jQuery(this).val();
                        if (text.length > 2) {
                            search_pme_datatable(text);
                            $("#DataTables_Table_0_paginate").hide();
                        }
                        if (text == "" || text.length == 0) {
                            search_pme_datatable("");
                            $("#DataTables_Table_0_paginate").show();
                        }
                    });

                });
            }

            if (stepNumber == 1 && stepDirection == "backward") {
                if ($("#suppId").val() !== "Not yet connected") {
                    setTimeout(function(){
                        $('.sw-btn-next').prop('disabled', false);
                    }, 500);
                }else {
                    setTimeout(function(){
                        $('.sw-btn-next').prop('disabled', true);
                    }, 500);
                }
            }

            if (stepNumber == 1 && stepDirection == "forward" && $(".sw-btn-next").is(":disabled")) {
                swal('','Please add agency diary code first.','error');
            }

            if (stepNumber == 2 && stepDirection == "forward" && agentloaded == 0) {
                $('.api_agent_table').DataTable().destroy();
                var table = $(".api_agent_table tbody");
                jQuery("#preloader").delay(200).fadeIn("slow");
                jQuery.ajax({
                    url: "/palace/get_palace_agent",
                    type: 'POST',
                    data: {
                        'agency_id': <?=$this->session->agency_id?>
                    }
                }).done(function( ret ){
                    var res = JSON.parse(ret);
                    $("#agentId").val(res.agent == null || res.agent == "" ? "Not yet connected" : res.agent);
                    $("#agentName").val(res.agentName == null || res.agentName == "" ? "Not yet connected" : res.agentName.AgentFullName);
                    table.empty();
                    $.each(res.palace, function (a, b) {
                        var dis = (res.agent == b.AgentCode) ? 'disabled' : '';
                        var btnSuc = "btn-primary";
                        var btnTxt = "Select";
                        if (res.agent == b.AgentCode) {
                            btnSuc = 'btn-success';
                            btnTxt = 'Selected';
                        }

                        table.append("<tr><td>"+b.AgentEmail1+"</td>" +
                            "<td>" + b.AgentFullName + "</td>" +
                            "<td> <button type='button' class='btn con_agent "+btnSuc+"' id-attr='"+b.AgentCode+"' "+dis+" name-attr='"+b.AgentFullName+"' style='display: block;margin: auto;'>"+btnTxt+"</button></td></tr>");
                    });
                    $(".api_agent_table").show();
                    if ( $.fn.dataTable.isDataTable( '.api_agent_table' ) ) {
                        table = $('.api_agent_table').DataTable();
                    }
                    else {
                        table = $('.api_agent_table').DataTable( {
                            "bInfo" : false,
                            "pageLength": 5,
                            // "bPaginate": false,
                            "lengthChange": false,
                            "columnDefs": [
                                { "orderable": false, "targets": 0 }
                            ]
                            // "searching": false,
                        } );
                    }
                    agentloaded = 1;
                    jQuery("#preloader").delay(200).fadeOut("slow");
                    $('#DataTables_Table_1_filter label').hide();
                    // table.search("^&*").draw();
                    $("#DataTables_Table_1_paginate").show();

                    if ($("#agentId").val() !== "Not yet connected") {
                        setTimeout(function(){
                            $('.sw-btn-next').prop('disabled', false);
                        }, 500);
                    }else {
                        setTimeout(function(){
                            $('.sw-btn-next').prop('disabled', true);
                        }, 500);
                    }

                    // autocomplete(document.getElementById("suppInput"), supplierReference);
                    function search_pme_datatable(text){
                        jQuery('.api_agent_table').DataTable().search( text, false, true ).draw();
                    }
                    jQuery(document).on("click","#suppInputautocomplete-list div",function(){
                        var text = $("#agentInput").val();
                        search_pme_datatable(text);
                    })
                    jQuery("#agentInput").keyup(function(){
                        var text = jQuery(this).val();
                        if (text.length > 2) {
                            search_pme_datatable(text);
                            $("#DataTables_Table_1_paginate").hide();
                        }
                        if (text == "" || text.length == 0) {
                            search_pme_datatable("");
                            $("#DataTables_Table_1_paginate").show();
                        }
                    });
                });
            }

            if (stepNumber == 2 && stepDirection == "forward") {
                if ($("#agentId").val() !== "Not yet connected") {
                    setTimeout(function(){
                        $('.sw-btn-next').prop('disabled', false);
                    }, 500);
                }else {
                    setTimeout(function(){
                        $('.sw-btn-next').prop('disabled', true);
                    }, 500);
                }
            }

            if (stepNumber == 2 && stepDirection == "backward") {
                if ($("#agentId").val() !== "Not yet connected") {
                    setTimeout(function(){
                        $('.sw-btn-next').prop('disabled', false);
                    }, 500);
                }else {
                    setTimeout(function(){
                        $('.sw-btn-next').prop('disabled', true);
                    }, 500);
                }
            }

            if (stepNumber == 3 && stepDirection == "forward" && diaryloaded == 0) {
                //$(".sw-btn-group").show();
                $("#smartwizard .toolbar").attr('style','display: flex !important'); // needs to apply css, this way bec .hide or .css is not working
                $('.api_diary_table').DataTable().destroy();
                var table = $(".api_diary_table tbody");
                jQuery("#preloader").delay(200).fadeIn("slow");
                jQuery.ajax({
                    url: "/palace/get_palace_diary",
                    type: 'POST',
                    data: {
                        'agency_id': <?=$this->session->agency_id?>
                    }
                }).done(function( ret ){
                    var res = JSON.parse(ret);
                    $("#diaryId").val(res.diary == null || res.diary == "" ? "Not yet connected" : res.diary);
                    $("#diaryName").val(res.diaryName == null || res.diaryName == "" ? "Not yet connected" : res.diaryName.DiaryGroupDescription);
                    table.empty();
                    $.each(res.palace, function (a, b) {
                        var dis = (res.diary == b.DiaryGroupCode) ? 'disabled' : '';
                        var btnSuc = "btn-primary";
                        var btnTxt = "Select";
                        if (res.diary == b.DiaryGroupCode) {
                            btnSuc = 'btn-success';
                            btnTxt = 'Selected';
                        }

                        table.append("<tr><td>" + b.DiaryGroupDescription + "</td>" +
                            "<td> <button type='button' class='btn con_diary "+btnSuc+"' id-attr='"+b.DiaryGroupCode+"' "+dis+" name-attr='"+b.DiaryGroupDescription+"' style='display: block;margin: auto;'>"+btnTxt+"</button></td></tr>");
                    });
                    $(".api_diary_table").show();
                    if ( $.fn.dataTable.isDataTable( '.api_diary_table' ) ) {
                        table = $('.api_diary_table').DataTable();
                    }
                    else {
                        table = $('.api_diary_table').DataTable( {
                            "bInfo" : false,
                            "pageLength": 5,
                            // "bPaginate": false,
                            "lengthChange": false,
                            "columnDefs": [
                                { "orderable": false, "targets": 0 }
                            ]
                            // "searching": false,
                        } );
                    }
                    diaryloaded = 1;
                    jQuery("#preloader").delay(200).fadeOut("slow");
                    $('#DataTables_Table_2_filter label').hide();
                    // table.search("Smoke Alarm Test").draw();
                    // $("#diaryInput").val("Smoke Alarm Test");
                    $("#DataTables_Table_2_paginate").show();

                    if ($("#diaryId").val() !== "Not yet connected") {
                        setTimeout(function(){
                            $('.sw-btn-next').prop('disabled', false);
                        }, 500);
                    }else {
                        setTimeout(function(){
                            $('.sw-btn-next').prop('disabled', true);
                        }, 500);
                    }

                    // autocomplete(document.getElementById("suppInput"), supplierReference);
                    function search_pme_datatable(text){
                        jQuery('.api_diary_table').DataTable().search( text, false, true ).draw();
                    }
                    jQuery(document).on("click","#suppInputautocomplete-list div",function(){
                        var text = $("#diaryInput").val();
                        search_pme_datatable(text);
                    })
                    jQuery("#diaryInput").keyup(function(){
                        var text = jQuery(this).val();
                        if (text.length > 2) {
                            search_pme_datatable(text);
                            $("#DataTables_Table_2_paginate").hide();
                        }
                        if (text == "" || text.length == 0) {
                            search_pme_datatable("");
                            $("#DataTables_Table_2_paginate").show();
                        }
                    });
                });
            }

            if(stepNumber == 4 && stepDirection == "forward"){
                setTimeout(function(){
                    $(".sw-btn-next").html("Finish");
                    $(".sw-btn-next").removeClass("disabled"); }, 500);
            }else {
                $(".sw-btn-next").html("Next");
                $(".sw-btn-next").removeClass("disabled");
            }

        });

        jQuery(".sw-btn-next").click(function(){
            if(diaryloaded == 1 && agentloaded == 1 && supploaded == 1 && $('.sw-btn-next').html() == "Finish"){

                //email notification
                jQuery("#preloader").delay(200).fadeIn("slow");
                jQuery.ajax({
                    url: "/palace/ajax_palace_connection_notification_email",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'agency_id': <?=$this->session->agency_id?>
                    }
                }).done(function( ret ){

                    jQuery("#preloader").delay(200).fadeOut("slow");

                    if(ret.status){

                        /*
                        swal({
                            title: "You are now connected to Palace!",
                            text: "",
                            type: "success",
                            showCancelButton: false,
                            showConfirmButton: false,
                            confirmButtonText: "OK",
                            closeOnConfirm: false,
                            closeOnConfirm: false,
                            allowOutsideClick: false,
                            timer: 2000
                        },function(isConfirm){
                            swal.close();
                            location.reload();
                            // $("#api_id_4").attr("src", "/images/api/palace_connected.png");                            
                        });
                        */

                        window.location='/api/select_agency_preference/?api=4';

                    }else{
                        swal('Error','Connection success but email notification not sent due to error. Please contact Admin.','error');
                    }

                });


            }
        })

        jQuery(document).on("click",".con_agent",function(){
            var but = $(this);
            var id = $(this).attr("id-attr");
            var name = $(this).attr("name-attr");
            var agencyId = "<?=$this->session->agency_id?>";

            jQuery('#load-screen').show();
            jQuery.ajax({
                url: "/palace/update_agent_id_by_agency",
                type: 'POST',
                data: {
                    'id': id,
                    'agencyId': agencyId
                }
            }).done(function( crm_ret ){
                if (crm_ret == false) {
                    swal('','Please add agency diary code first.','error');
                    $('#load-screen').hide(); //hide loader
                }else {
                    $("#agentId").val(id);
                    $("#agentName").val(name);
                    $('#load-screen').hide(); //hide loader

                    but.addClass('btn-success');

                    $("#agency_filter > option").each(function() {
                        if ($(this).val() == agencyId) {
                            $(this).css("color", "");
                        }
                    });

                    $('.con_agent').each(function(i, obj) {
                        if ($(this).prop('disabled', true)) {
                            $(this).prop('disabled', false);
                            $(this).removeClass('btn-success');
                            $(this).text("Select");
                        }
                        if (id == $(this).attr("id-attr")) {
                            $(this).prop('disabled', true);
                            $(this).addClass('btn-success');
                            $(this).text("Selected");
                        }
                    });
                    // swal('','Successfully updated agent for this agency.','info');
                    swal({
                        title:"",
                        text: "Successfully updated agent for this agency.",
                        type: "info",
                        showCancelButton: false,
                        showConfirmButton: false,
                        confirmButtonText: "OK",
                        closeOnConfirm: false,
                        closeOnConfirm: false,
                        allowOutsideClick: false,
                        timer: 3000
                    },function(isConfirm){
                        swal.close();
                        $('.sw-btn-next').trigger( "click" );
                        $('.sw-btn-next').prop('disabled', false);
                    });
                }

            });
        })

        jQuery(document).on("click",".con_diary",function(){
            var but = $(this);
            var id = $(this).attr("id-attr");
            var name = $(this).attr("name-attr");
            var agencyId = "<?=$this->session->agency_id?>";

            jQuery('#load-screen').show();
            jQuery.ajax({
                url: "/palace/update_diary_id_by_agency",
                type: 'POST',
                data: {
                    'id': id,
                    'agencyId': agencyId
                }
            }).done(function( crm_ret ){

                $("#diaryId").val(id);
                $("#diaryName").val(name);
                $('#load-screen').hide(); //hide loader

                but.addClass('btn-success');

                $("#agency_filter > option").each(function() {
                    if ($(this).val() == agencyId) {
                        $(this).css("color", "");
                    }
                });

                $('.con_diary').each(function(i, obj) {
                    if ($(this).prop('disabled', true)) {
                        $(this).prop('disabled', false);
                        $(this).removeClass('btn-success');
                        $(this).text("Select");
                    }
                    if (id == $(this).attr("id-attr")) {
                        $(this).prop('disabled', true);
                        $(this).addClass('btn-success');
                        $(this).text("Selected");
                    }
                });
                // swal('','Successfully updated Diary for this agency.','info');
                swal({
                    title:"",
                    text: "Successfully updated diary for this agency.",
                    type: "info",
                    showCancelButton: false,
                    showConfirmButton: false,
                    confirmButtonText: "OK",
                    closeOnConfirm: false,
                    closeOnConfirm: false,
                    allowOutsideClick: false,
                    timer: 3000
                },function(isConfirm){
                    swal.close();
                    $('.sw-btn-next').trigger( "click" );
                    $('.sw-btn-next').prop('disabled', false);
                });

            });
        })

        jQuery(document).on("click",".con_supp",function(){
            var but = $(this);
            var id = $(this).attr("id-attr");
            var name = $(this).attr("name-attr");
            var agencyId = "<?=$this->session->agency_id?>";

            jQuery('#load-screen').show();
            jQuery.ajax({
                url: "/palace/update_supplier_id_by_agency",
                type: 'POST',
                data: {
                    'id': id,
                    'agencyId': agencyId
                }
            }).done(function( crm_ret ){

                if (crm_ret == false) {
                    swal('','Please add agency diary code and agent id first.','error');
                    $('#load-screen').hide(); //hide loader
                }else {
                    $("#suppId").val(id);
                    $("#suppName").val(name);
                    $('#load-screen').hide(); //hide loader

                    but.addClass('btn-success');

                    $("#agency_filter > option").each(function() {
                        if ($(this).val() == agencyId) {
                            $(this).css("color", "");
                        }
                    });

                    $('.con_supp').each(function(i, obj) {
                        if ($(this).prop('disabled', true)) {
                            $(this).prop('disabled', false);
                            $(this).removeClass('btn-success');
                            $(this).text("Select");
                        }
                        if (id == $(this).attr("id-attr")) {
                            $(this).prop('disabled', true);
                            $(this).addClass('btn-success');
                            $(this).text("Selected");
                        }
                    });
                    // swal('','Successfully updated supplier for this agency.','info');
                    swal({
                        title:"",
                        text: "Successfully updated supplier for this agency.",
                        type: "info",
                        showCancelButton: false,
                        showConfirmButton: false,
                        confirmButtonText: "OK",
                        closeOnConfirm: false,
                        closeOnConfirm: false,
                        allowOutsideClick: false,
                        timer: 3000
                    },function(isConfirm){
                        swal.close();
                        $('.sw-btn-next').trigger( "click" );
                        $('.sw-btn-next').prop('disabled', false);
                    });
                }

            });

        })

        jQuery(document).on("click","#removeAgent",function(){

            var agencyId = "<?=$this->session->agency_id?>";
            if (agencyId == 0) {
                swal('','Please select an agency.','info');
            }else {
                swal({
                        html:true,
                        title: "Warning!",
                        text: "You are about to remove agent Id on this agency. Are you sure you want to continue?",
                        type: "warning",
                        customClass: 'swal-dup_prop',

                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes",
                        cancelButtonText: "Cancel!",
                        cancelButtonClass: "btn-danger",
                        closeOnConfirm: true,
                        showLoaderOnConfirm: true,
                        closeOnCancel: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            jQuery('#load-screen').show();
                            jQuery.ajax({
                                url: "/palace/remove_agent_id_by_agency",
                                type: 'POST',
                                data: {
                                    'agencyId': agencyId
                                }
                            }).done(function( crm_ret ){

                                if (crm_ret == false) {
                                    swal('','Please remove agency supplier first.','error');
                                    $('#load-screen').hide(); //hide loader
                                }else {
                                    $("#agentId").val("Not yet connected");
                                    $("#agentName").val("Not yet connected");

                                    $("#agency_filter > option").each(function() {
                                        if ($(this).val() == agencyId) {
                                            $(this).css("color", "red");
                                        }
                                    });

                                    $('.con_agent').each(function(i, obj) {
                                        if ($(this).prop('disabled', true)) {
                                            $(this).prop('disabled', false)
                                            $(this).removeClass('btn-success');
                                            $(this).text("Select");
                                        }
                                    });
                                    swal('','Successfully removed agent for this agency.','info');
                                    $('#load-screen').hide(); //hide loader
                                }
                                if ($("#agentId").val() !== "Not yet connected") {
                                    setTimeout(function(){
                                        $('.sw-btn-next').prop('disabled', false);
                                    }, 500);
                                }else {
                                    setTimeout(function(){
                                        $('.sw-btn-next').prop('disabled', true);
                                    }, 500);
                                }
                            });
                        }
                    });
            }

        })

        jQuery(document).ready(function (e) {
            // show the list
            const url = new URL (window.location.href)
            const list = url.searchParams.get('list')
            console.log('==== list: ', list);

            if(list == "true"){
                // pop-up lightbox
                jQuery("#pme_details_fb_link").click();
            }

        })

        jQuery(document).ready(function (e) {
            // show the list
            const url = new URL (window.location.href)
            const updated = url.searchParams.get('updated')
            console.log('==== updated: ', updated);

            if(updated == "true"){
                // pop-up lightbox
                jQuery('#load-pme_details_fb').hide();
                swal({
                    title: "Success!",
                    text: "Agency connected successfully!",
                    type: "success",
                    confirmButtonClass: "btn-success"
                });
            }

        })

        jQuery(document).on("click","#removeDiary",function(){

            var agencyId = "<?=$this->session->agency_id?>";
            var agentId = $("#agentId").val();
            var diaryId = $("#diaryId").val();


            if (agencyId == 0) {
                swal('','Please select an agency.','info');
            }else {
                swal({
                        html:true,
                        title: "Warning!",
                        text: "You are about to remove Diary Id on this agency. Are you sure you want to continue?",
                        type: "warning",
                        customClass: 'swal-dup_prop',

                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes",
                        cancelButtonText: "Cancel!",
                        cancelButtonClass: "btn-danger",
                        closeOnConfirm: true,
                        showLoaderOnConfirm: true,
                        closeOnCancel: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            jQuery('#load-screen').show();
                            jQuery.ajax({
                                url: "/palace/remove_diary_id_by_agency",
                                type: 'POST',
                                data: {
                                    'agencyId': agencyId
                                }
                            }).done(function( crm_ret ){

                                if (crm_ret == false) {
                                    swal('','Please remove agency agent first.','error');
                                    $('#load-screen').hide(); //hide loader
                                }else {
                                    $("#diaryId").val("Not yet connected");
                                    $("#diaryName").val("Not yet connected");

                                    $("#agency_filter > option").each(function() {
                                        if ($(this).val() == agencyId) {
                                            $(this).css("color", "red");
                                        }
                                    });

                                    $('.con_diary').each(function(i, obj) {
                                        if ($(this).prop('disabled', true)) {
                                            $(this).prop('disabled', false)
                                            $(this).removeClass('btn-success');
                                            $(this).text("Select");
                                        }
                                    });
                                    swal('','Successfully removed Diary for this agency.','info');
                                    $('#load-screen').hide(); //hide loader
                                }
                                if ($("#diaryId").val() !== "Not yet connected") {
                                    setTimeout(function(){
                                        $('.sw-btn-next').prop('disabled', false);
                                    }, 500);
                                }else {
                                    setTimeout(function(){
                                        $('.sw-btn-next').prop('disabled', true);
                                    }, 500);
                                }
                            });

                        }
                    });
            }

        })

        jQuery(document).on("click","#removeSupp",function(){

            var agencyId = "<?=$this->session->agency_id?>";
            var agentId = $("#agentId").val();
            var diaryId = $("#diaryId").val();

            if (agencyId == 0) {
                swal('','Please select an agency.','info');
            }else {
                swal({
                        html:true,
                        title: "Warning!",
                        text: "You are about to remove supplier Id on this agency. Are you sure you want to continue?",
                        type: "warning",
                        customClass: 'swal-dup_prop',

                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes",
                        cancelButtonText: "Cancel!",
                        cancelButtonClass: "btn-danger",
                        closeOnConfirm: true,
                        showLoaderOnConfirm: true,
                        closeOnCancel: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            jQuery('#load-screen').show();
                            jQuery.ajax({
                                url: "/palace/remove_supplier_id_by_agency",
                                type: 'POST',
                                data: {
                                    'agencyId': agencyId
                                }
                            }).done(function( crm_ret ){
                                if (crm_ret == false) {
                                    swal('','Please remove agency diary code and agent id first.','error');
                                    $('#load-screen').hide(); //hide loader
                                }else {
                                    $("#suppId").val("Not yet connected");
                                    $("#suppName").val("Not yet connected");

                                    $("#agency_filter > option").each(function() {
                                        if ($(this).val() == agencyId) {
                                            $(this).css("color", "red");
                                        }
                                    });

                                    $('.con_supp').each(function(i, obj) {
                                        if ($(this).prop('disabled', true)) {
                                            $(this).prop('disabled', false)
                                            $(this).removeClass('btn-success');
                                            $(this).text("Select");
                                        }
                                    });
                                    swal('','Successfully removed supplier for this agency.','info');
                                    $('#load-screen').hide(); //hide loader
                                }

                                if ($("#suppId").val() !== "Not yet connected") {
                                    setTimeout(function(){
                                        $('.sw-btn-next').prop('disabled', false);
                                    }, 500);
                                }else {
                                    setTimeout(function(){
                                        $('.sw-btn-next').prop('disabled', true);
                                    }, 500);
                                }
                            });
                        }
                    });
            }

        })
        
        <?php
        if( $user_has_alt_agency == true ){ ?>

        // PMea
        jQuery("#api_id_1").click(function(){

            var api_link = jQuery(this).attr("data-api_link");
            var is_permission_granted = jQuery(this).attr("data-is_permission_granted");

            if( is_permission_granted == 1 ){

                swal_txt = "You are about to connect as <?php echo $agency_name; ?>.\n\nPlease note, you must have ADMIN user access in PropertyMe to initiate this connection.\n\n Would you like to proceed?";

                swal({
                        title: "PropertyMe API Authentication",
                        text: swal_txt,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes!",
                        cancelButtonClass: "btn-danger",
                        cancelButtonText: "No, Cancel",
                        closeOnConfirm: true
                    },
                    function(isConfirm) {

                        if (isConfirm) { // yes
                            window.location=api_link;
                        }

                    });

            }


        });
        
        <?php
        }
        ?>



        // connect API
        jQuery(".company_logo").click(function(){

            var api_link = jQuery(this).attr("data-api_link");
            var api_id = jQuery(this).attr("data-api_id");

            var is_permission_granted = jQuery(this).attr("data-is_permission_granted");
            var is_integrated = jQuery(this).attr("data-is_integrated");


            // if( is_permission_granted == 1 ){

            if( api_id == 5){ // console

                swal({
                        title: "",
                        text: 'To connect your portfolio to the Console API, there is a fee of $3.00 + GST per property per year, imposed by Console. This fee will be displayed on your invoice for your records.\n\nIf you wish to continue, please press "Yes" to accept the fee or "No, cancel" to not proceed.',
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes!",
                        cancelButtonClass: "btn-danger",
                        cancelButtonText: "No, Cancel",
                        closeOnConfirm: true
                    },
                    function(isConfirm) {

                        if (isConfirm) { // yes

                            // insert link
                            jQuery("#console_api_link").val(api_link);

                            // show consolecloud modal with wizard
                            $("#consoleCloudModal").modal();

                            // launch fancybox
                            // $.fancybox.open({
                            //     src  : '#console_fb_terms'
                            // });

                        }

                    });

            }else{ // other API

                if( api_link != '' ){

                    if( api_id == 1 ){ // PMe only

                        $('#propertyME_api_link').val(api_link);
                        // show PropertyMe modal with wizard
                        $("#propertyMeWizard").modal();


                        // swal_txt = "Please note, you must have ADMIN user access in PropertyMe to initiate this connection.\n\n Would you like to proceed?";


                        // swal({
                        //     title: "PropertyMe API Authentication",
                        //     text: swal_txt,
                        //     // type: "warning",
                        //     imageUrl: api_logo,
                        //     imageWidth: 400,
                        //     imageHeight: 200,
                        //     imageAlt: "PropertyMe Logo",
                        //     showCancelButton: true,
                        //     confirmButtonClass: "btn-success",
                        //     confirmButtonText: "Yes!",
                        //     cancelButtonClass: "btn-danger",
                        //     cancelButtonText: "No, Cancel",
                        //     closeOnConfirm: true
                        // },
                        // function(isConfirm) {

                        //     if (isConfirm) { // yes
                        //         window.location = api_link;
                        //     }

                        // });

                    }else{ // other non-PMe API except console

                        window.location = api_link;

                    }

                }

            }

            // }

        });


        // Palace
        jQuery("#api_id_4").click(function(e){

            e.preventDefault(); // disable link

            var is_permission_granted = jQuery(this).attr("data-is_permission_granted");
            // if (is_permission_granted != "1") {
            //     return;
            // }

            $('#palace_api_link').val(jQuery(this).attr("data-api_link"));
            $("#palaceWizardModal").modal();

            // $("#exampleModal").modal()
            // if ($("#palaceLogin").html() == "Re-login") {
            //     $('.sw-btn-next').prop('disabled', false);
            // }else {
            //     $('.sw-btn-next').prop('disabled', true);
            // }

        });


        // yes, verify integration
        jQuery("#console_active_btn_yes").click(function(){

            var api_key = jQuery("#console_api_key").val();

            if( api_key != '' ){

                jQuery('#load-screen').show();
                jQuery.ajax({
                    url: "/console/verify_integration",
                    type: 'POST',
                    data: {
                        'api_key': api_key
                    }
                }).done(function( crm_ret ){
                    $.fancybox.close();
                    location.reload();

                    // toggleDisplayCss('#console_api_container');
                    // location.reload();
                    // swal({
                    //     title:"Success!",
                    //     text: "Console Cloud is now Activated!",
                    //     type: "success",
                    //     showCancelButton: false,
                    //     confirmButtonText: "OK",
                    //     closeOnConfirm: true,

                    // },function(isConfirm){
                    //     if(isConfirm){
                    //         $.fancybox.close();
                    //         location.reload();
                    //     }
                    // });

                });

            }else{
                swal("", "Please enter activation key", "error");
            }

        });


        // <?php
        // if( $this->session->flashdata('pme_api_integ_success') &&  $this->session->flashdata('pme_api_integ_success') == 1 ){
        // ?>
        //     swal({
        //         title: "Success!",
        //         text: "SATS and PropertyME are now connected",
        //         type: "success",
        //         confirmButtonClass: "btn-success"
        //     });
        // <?php
        // }
        // ?>
        
        <?php if(in_array('5', array_column($is_integrated, 'agency_api_id'))): ?>
        // Fetch console api logs data
        $.ajax({
            url: '<?= base_url("ajax/api_ajax/get_api_logs"); ?>?logTitle=90', //log title id for console
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var tableBody = $('#console_api_logs tbody');
                tableBody.empty();

                $.each(data.recent_activity, function(index, item){
                    var row = '<tr>';
                    row += '<td>' + item.created_date + '</td>';
                    row += '<td><img class="profile_pic_small border border-info" src="'+item.image+'"/>&nbsp;&nbsp;' + item.name + '</td>';
                    row += '<td>'+ item.title_name +'</td>';
                    row += '<td>'+ item.details +'</td>';
                    row += '</tr>';
                    tableBody.append(row);
                });

            },
            error: function(xhr, status, error) {
                console.error('Error fetching data: ' + error);
            }
        });
        
        <?php endif; ?>

        function toggleDisplayCss(myContainerID){
            $(myContainerID).css('display', function(index, value) {
                return value === 'none' ? 'block' : 'none';
            });
        }                                

    });
</script>
