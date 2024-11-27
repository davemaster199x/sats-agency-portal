<style>
#smartwizardPalace .tab-content{
    height: auto !important; /* css fix for step 1 content not displaying */
}
</style>
<div class="container">
    <div class="modal fade apiIntegrationWizardModal" id="palaceWizardModal" tabindex="-1" role="dialog" aria-labelledby="palaceWizardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5> 
                    <img src="/images/api/logo/palace_logo.png" alt="Palace logo">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="palace_api_link" value="" />
                    <div id="smartwizardPalace">
                        <ul class="nav mt-4">
                            <li class="nav-item">
                                <a href="#palaceStep-1" class="nav-link">
                                    <div class="num">1</div>
                                    <small class="title-text">How to Connect</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#palaceStep-2" class="nav-link">
                                    <span class="num">2</span>
                                    <small class="title-text">After Connected</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#palaceStep-3" class="nav-link">
                                    <span class="num">3</span>
                                    <small class="title-text">What Data can <?= $this->config->item('COMPANY_NAME_SHORT')?> See</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#palaceStep-4" class="nav-link">
                                    <span class="num">4</span>
                                    <small class="title-text">Statement of Compliance/Invoices</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#palaceStep-5" class="nav-link">
                                    <span class="num">5</span>
                                    <small class="title-text">Fees</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#palaceStep-6" class="nav-link">
                                    <span class="num">6</span>
                                    <small class="title-text">FAQ</small>
                                </a>
                            </li>
                        </ul>
                        <div  class="tab-content">
                            <div id="palaceStep-1" class="tab-pane" role="tabpanel" aria-labelledby="palaceStep-1">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>You will need to have obtained an API login from Palace (this is not the same as your login for Palace)</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Make sure <?= $this->config->item('COMPANY_NAME_SHORT') ?>  is setup as a supplier in Palace </span>
                                        </button>
                                        <!-- <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Connect via the Intergration page in <?= $this->config->item('COMPANY_NAME_SHORT') ?> Agency Portal</span>
                                        </button> -->
                                    </div>
                                </div>
                            </div>

                            <div id="palaceStep-2" class="tab-pane" role="tabpanel" aria-labelledby="palaceStep-2">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span><?= $this->config->item('COMPANY_NAME_SHORT') ?> will sync all actively serviced properties in our database with properties in Palace</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>If properties are archived in Palace but active in the <?= $this->config->item('COMPANY_NAME_SHORT') ?> System, we will contact your office to verify the property status</span>
                                        </button>          
                                    </div>
                                </div>
                            </div>
                            <div id="palaceStep-3" class="tab-pane" role="tabpanel" aria-labelledby="palaceStep-3">
                                <div class="container">
                                    <div class="list-group">
                                        <p class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1 font-italic pr-3">What Data can <?=$this->config->item('COMPANY_NAME_SHORT') ?> See</p>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Property address and status</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Tenant Details</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Key Details</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>The assigned Property Manager</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="palaceStep-4" class="tab-pane" role="tabpanel" aria-labelledby="palaceStep-4">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Statements of Compliance are uploaded at job completion and will appear in the <b>Safety</b> tab located in the property address in Palace (this can be customised after completing the integration process)</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Smoke alarm compliance criteria auto-filled in the <b>Safety</b> tab located in the property address in Palace</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>
                                                Invoices are uploaded at job completion and will appear in the <b>Invoice Document Workflow</b>, located in the financial section of Palace 
                                                (this can be customised after completing the integration process) 
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="palaceStep-5"  class="tab-pane" role="tabpanel" aria-labelledby="palaceStep-5">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Currently no fee is charged to use the Palace API</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="palaceStep-6" class="tab-pane" role="tabpanel" aria-labelledby="palaceStep-6">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action">
                                            <p class="font-weight-bold font-italic">Do I still need to send work orders? </p>
                                            <p>Yes, If you would like <?= $this->config->item('COMPANY_NAME_SHORT') ?> to attend for any service other than the annual inspection, you will need to send us a work order or create a job via the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Agency portal</p>

                                            <p class="font-weight-bold font-italic">Can <?= $this->config->item('COMPANY_NAME_SHORT') ?> just look at my lease details and create jobs automatically?</p>
                                            <p>No, you will need to send us a work order or create a job via the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Agency Portal</p>

                                            <p class="font-weight-bold font-italic">Do I still need to use the <?= $this->config->item('COMPANY_NAME_SHORT') ?> portal?</p>
                                            <p>Yes, Properties that are due to be renewed for annual service still need to be checked and processed. </p>

                                            <p class="font-weight-bold font-italic">The API connection does not appear to be syncing correctly?</p>
                                            <p>If you are experiencing any issues, please ensure that we are connected to “Liquid” and not “Legacy”</p>
                                        </button>
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

<!-- Palace integration not admin modal  -->
<div class="modal fade palaceNotAdminModal" id="palaceNotAdminModal" tabindex="-1" role="dialog" aria-labelledby="palaceNotAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <h5 class="text-center">Sorry! Only users who have admin access can continue.</h5>
                    
                    <p class="text-center">Here are the admin users for <b><?= $agency_name ?></b></p>
                    <ul class="text-center">
                        <?php foreach($agency_admin_users as $row): ?>
                        <li><?= ucfirst($row->fname)?> <?= ucfirst($row->lname)?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="text-center">If you would like to be an admin user, please click <a href="#" id="palace_request_access" class="font-weight-bold">here</a> to change your access level.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Palace integration admin modal  -->
<div class="modal fade palaceAdminModal" id="palaceAdminModal" tabindex="-1" role="dialog" aria-labelledby="palaceAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Can we insert USER ID/ AGENCY into body of a mail </h5> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                <div class="container p-4">
                    <p><b>FROM:</b> Info@ </p>
                    <p><b>To:</b>  <a href="#">PS.PalaceAPISupport@mrisoftware.com</a> </p>
                    <p><b>Cc:</b> &lt;portal user&gt; &lt;info@sats&gt; &lt;bdm&gt; </p>
                    <p class="font-weight-bold">Subject – Api log in for <b>&lt;agency ID&gt;</b> </p>

                    <p>Body: <br/>
                    Hi  Jermaine & Remi, 

                    We are writing to you on behalf of <b>&lt;AGENCY ID&gt;</b> to confirm that we wish to proceed with the API connection between Palace and SATS. Can you please provide <b>&lt;AGENCY ID&gt;</b> with the API credentials login information so that we can complete the integration. </p>

                    <p class="font-weight-bold"><?=config_item('COMPANY_NAME_SHORT');?></p>
                    <button id="palace_api_login_req" type="button" class="btn btn-success m-auto d-flex">Submit a request</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        var smartwizardPalace = new smartwizardManager('smartwizardPalace', 'dots');
        smartwizardPalace.addCustomButton('Continue', function () {
            $("#palaceWizardModal").hide();

                // api_link = $('#palace_api_link').val();

                swal_txt = "Do you have an API login? \n(this is not the same as your login for Palace)";

                // show swal alert
                swal({
                    title: "Palace API Authentication",
                    text: swal_txt,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Yes",
                    cancelButtonClass: "btn-danger",
                    cancelButtonText: "No",
                    closeOnConfirm: true
                },
                function(isConfirm) {

                    if (isConfirm) { // yes, continue to API login

                        // check if api is in database
                        $.ajax({
                            url: '<?= base_url("ajax/agency_api_integration_ajax/is_api_integrated"); ?>?con_service=4', //api id of palace
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                if(data){
                                    // show API login modal
                                    $("#exampleModal").modal()
                                    if ($("#palaceLogin").html() == "Re-login") {
                                        $('#smartwizardPalace .sw-btn-next').prop('disabled', false);
                                    }else {
                                        $('#smartwizardPalace .sw-btn-next').prop('disabled', true);
                                    }
                                } else {
                                    // alert('wala pa na save.. e save sa bago re redirect')
                                    $.ajax({
                                        type: "POST",
                                        url: '/ajax/api_ajax/add_agency_api_integration',  
                                        data: {
                                            connected_service: 4 //agency_api_id for palace
                                        },
                                        success: function (response) {
                                            /*
                                            swal({
                                                title: "Success!",
                                                text: response.message,
                                                type: "success",
                                                timer: 2000,
                                                showConfirmButton: false
                                            });
                                            // show API login modal
                                            $("#exampleModal").modal()
                                            if ($("#palaceLogin").html() == "Re-login") {
                                                $('#smartwizardPalace .sw-btn-next').prop('disabled', false);
                                            }else {
                                                $('#smartwizardPalace .sw-btn-next').prop('disabled', true);
                                            }
                                            */

                                            jQuery("#preloader").delay(200).fadeIn("slow");
                                            // show API login modal
                                            $("#exampleModal").modal();
                                            jQuery("#preloader").delay(200).fadeOut("slow");

                                        },
                                        error: function (response) {
                                            console.log("🚀 ~ file: _palace.php:290 ~ continueButtonPalace.on ~ response:", response)
                                            swal("Error!", response.message, "error");
                                        },
                                    });
                                        
                                }
                                
                            },
                            error: function(xhr, status, error) {
                                console.error('Error fetching data: ' + error);
                            }
                        });


                    } else { 

                        
                        /*
                        if (<?= ($user_type == 1) ? 1 : 0 ?>) { //admin
                            $('#palaceAdminModal').modal();
                        } else{ //not admin
                            $('#palaceNotAdminModal').modal();
                        }
                        */

                        // show swal alert
                        setTimeout(function() {
                        
                            swal({
                                title: "No API login account",
                                text: 'Do you want <?=$this->config->item('COMPANY_NAME_SHORT')?> to send an email to Palace on your behalf to request the API Login Credentials?',
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-success",
                                confirmButtonText: "Yes",
                                cancelButtonClass: "btn-danger",
                                cancelButtonText: "No, Cancel",
                                closeOnConfirm: true
                            },
                            function(isConfirm) {

                                if (isConfirm) { // yes, continue to API login

                                    jQuery("#preloader").delay(200).fadeIn("slow");
                                    jQuery.ajax({
                                        type: "POST",
                                        url: "/api/send_palace_api_account_request"
                                    }).done(function( ret ){
                                            
                                        jQuery("#preloader").delay(200).fadeOut("slow");                                        
                                        swal({
                                            title: "Success!",
                                            text: "You have been added to the email request sent to Palace. Please check your inbox. Once you have received the login details from Palace, return to the integrations tab in the SATS Portal and complete the Integration",
                                            type: "success",
                                            confirmButtonClass: "btn-success"
                                        },
                                        function(){
                                            window.location='/api/connections';
                                        });                                        	

                                    });	

                                }else{
                                    window.location='/api/connections';
                                }
                                
                            });

                        }, 500);                             

                    }                    

                });
        }, 'propertyME-continue-btn');
        smartwizardPalace.initialize();


        jQuery('#palace_request_access').on('click', function(e){
            e.preventDefault();
            // generate email
            jQuery.ajax({
                url: "/palace/api_admin_access_request_email",
                type: 'POST',
                dataType: 'json',
                data: {
                    'agency_id': <?=$this->session->agency_id?>
                }
            }).done(function(res){
                if (res.status) {
                    swal({
                        title: "Success.",
                        text: "Your request to change your portal access level has been submitted. Our team will contact you shortly with further instructions",
                        type: "success",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false
                    },function(isConfirm){
                        window.location = "/home";
                    });

                }else{
                    swal('Error','Email notification not sent due to error. Please contact Admin.','error');
                }
            })
        })

        jQuery("#palace_api_login_req").on('click', function(e){
            e.preventDefault();
            $(this).prop('disabled', true);
            //generate email
            $.ajax({
                url: "/palace/api_login_request_email",
                type: 'POST',
                dataType: 'json',
                data: {
                    'agency_id': <?=$this->session->agency_id?>
                }
            }).done(function(res){
                $(this).prop('disabled', false);
                if(res.status){
                    swal({
                        title: "Success.",
                        text: "Thank you, Palace will be in touch with further instructions",
                        type: "success",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false
                    },function(isConfirm){
                        window.location = "/home";
                    });
                }else{
                    swal('Error','Email notification not sent due to error. Please contact Admin.','error');
                }
            })
        })
    });
</script>