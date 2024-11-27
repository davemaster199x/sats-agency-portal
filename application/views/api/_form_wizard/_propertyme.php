<style>
#smartwizardPME .tab-content{
    height: auto !important; /* css fix for step 1 content not displaying */
}
</style>
<div class="container">
    <div class="modal fade apiIntegrationWizardModal" id="propertyMeWizard" tabindex="-1" role="dialog" aria-labelledby="propertyMeWizardLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5> 
                    <img src="/images/api/logo/pme_logo.png" alt="PropertyME logo">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="propertyME_api_link" value="" />
                    <div id="smartwizardPME" >
                        <ul class="nav mt-4">
                            <li class="nav-item">
                                <a href="#PMEstep-1" class="nav-link">
                                    <div class="num">1</div>
                                    <small class="title-text">How to Connect</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#PMEstep-2" class="nav-link">
                                    <span class="num">2</span>
                                    <small class="title-text">After Connected</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#PMEstep-3" class="nav-link">
                                    <span class="num">3</span>
                                    <small class="title-text">What Data can <?= $this->config->item('COMPANY_NAME_SHORT')?> See</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#PMEstep-4" class="nav-link">
                                    <span class="num">4</span>
                                    <small class="title-text">Statement of Compliance/Invoices</small>
                                </a>
                            </li>                            
                            <li class="nav-item">
                                <a href="#PMEstep-5" class="nav-link">
                                    <span class="num">5</span>
                                    <small class="title-text">Fees</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#PMEstep-6" class="nav-link">
                                    <span class="num">6</span>
                                    <small class="title-text">FAQ</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#PMEstep-7" class="nav-link">
                                    <span class="num">7</span>
                                    <small class="title-text">Connect</small>
                                </a>
                            </li>
                        </ul>
                        <div  class="tab-content">
                            <div id="PMEstep-1" class="tab-pane" role="tabpanel" aria-labelledby="PMEstep-1">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>You will need to be an Admin user in PropertyMe</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Make sure <?= $this->config->item('COMPANY_FULL_NAME') ?> is setup as a supplier in PropertyMe with Bill account <b>485- Fire protection</b></span>
                                        </button>
                                        <!-- <button type="button" class="list-group-item list-group-item-action">Connect via the Intergration page in <?= $this->config->item('COMPANY_NAME_SHORT') ?> Agency Portal</button> -->
                                    </div>
                                </div>
                            </div>

                            <div id="PMEstep-2" class="tab-pane" role="tabpanel" aria-labelledby="PMEstep-2">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span><?=$this->config->item('COMPANY_NAME_SHORT')?> will sync all actively serviced properties in our database with properties in PropertyMe</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>If properties are archived in PropertyMe but active in the <?= $this->config->item('COMPANY_NAME_SHORT') ?> System, we will contact your office to verify the property status</span>
                                        </button>         
                                    </div>
                                </div>
                            </div>
                            <div id="PMEstep-3" class="tab-pane" role="tabpanel" aria-labelledby="PMEstep-3">
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
                            <div id="PMEstep-4" class="tab-pane" role="tabpanel" aria-labelledby="PMEstep-4">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Statements of Compliance are uploaded and will appear in the <b>Documents</b> tab located in the property address in PropertyMe 
                                                (this can be customised after completing the integration process) </span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Invoices are uploaded and will appear in the <b>Due</b> tab located in the Bills section of PropertyMe 
                                                (this can be customised after completing the integration process)</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>
                                                Invoices can be auto approved in PropertyMe for <?=$this->config->item('COMPANY_NAME_SHORT')?>, 
                                                if you do not want our invoices to be auto approved, please amend this setting in Property Me (see below image). 
                                                Please also check that the <b>BSB (<?=$country_row->bsb?>) and Account number (<?=$country_row->ac_number?>)</b> for <?=$this->config->item('COMPANY_FULL_NAME')?> is correct. 
                                                You can either add or edit this by clicking on the 'Payment Methods Tab' (see below image).
                                            </span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action">
                                            <img src="/images/api/<?=$this->config->item('theme')?>_propertyME_supplier.png" class="w-75 d-flex mx-auto" alt="propertMe_supplier">
                                        </button>
                                    </div>
                                </div>
                            </div>                            
                            <div id="PMEstep-5"  class="tab-pane" role="tabpanel" aria-labelledby="PMEstep-5">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Currently no fee is charged to use the PropertyMe API</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="PMEstep-6" class="tab-pane" role="tabpanel" aria-labelledby="PMEstep-6">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action">
                                            <p class="font-weight-bold font-italic">Do I still need to send work orders? </p>
                                            <p>Yes, If you would like <?= $this->config->item('COMPANY_NAME_SHORT') ?> to attend for any service other than the annual inspection, you will need to send us a work order or create a job via the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Agency portal </p>
                                            <div class="mb-5">
                                                <img src="/images/api/<?=$this->config->item('theme')?>_pme_edit_supplier_contact.png" class="w-75 d-flex mx-auto" alt="propertMe_supplier">
                                            </div>

                                            <p class="font-weight-bold font-italic">Can <?= $this->config->item('COMPANY_NAME_SHORT') ?> just look at my lease details and create jobs automatically?</p>
                                            <p>No, you will need to send us a work order or create a job via the <?= $this->config->item('COMPANY_NAME_SHORT') ?> Agency Portal</p>

                                            <p class="font-weight-bold font-italic">Do I still need to use the <?= $this->config->item('COMPANY_NAME_SHORT') ?> portal?</p>
                                            <p>Yes, Properties that are due to be renewed for annual service still need to be checked and processed.</p>
                                        </button>
                                    </div>
                                    
                                </div>
                            </div>

                            <div id="PMEstep-7" class="tab-pane" role="tabpanel" aria-labelledby="PMEstep-7">
                                <div class="container text-center">
                                    <h3 class="mt-2 mb-4">PropertyMe API <br>Authentication</h3>
                                             
                                    <p class="mt-4">Please note, you must have ADMIN user access in PropertyMe to initiate this connection.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    jQuery(document).ready(function() {
        var smartwizardPME = new smartwizardManager('smartwizardPME', 'dots');

        smartwizardPME.addCustomButton('Continue', function () {
            // api_link = 'https://login.propertyme.com/sign-in?ReturnUrl=%2Fconnect%2Fauthorize%2Fcallback%3Fresponse_type%3Dcode%26state%3Dabc123%26client_id%3D5ff326e1-18f3-4c9e-9092-607ad116c81e%26scope%3Dcontact%253Aread%2520property%253Aread%2520property%253Awrite%2520activity%253Aread%2520communication%253Aread%2520transaction%253Awrite%2520transaction%253Aread%2520offline_access%26redirect_uri%3Dhttps%253A%252F%252Fagencydev.sats.com.au%252Fapi%252Fcallback_pme';

           
            api_link = "<?= $this->config->item('PME_AUTH_LINK') ?>";

            window.location = api_link;

            // $.ajax({
            //     url: '<?= base_url("ajax/agency_api_integration_ajax/is_api_integrated"); ?>?con_service=1', //api id of propertyme
            //     type: 'GET',
            //     dataType: 'json',
            //     success: function(data) {
            //         if(data){
            //             // alert('naka save na.. redirect nalang')
            //             window.location = api_link;
            //         } else {
            //             // alert('wala pa na save.. e save sa bago re redirect')
            //             $.ajax({
            //                 type: "POST",
            //                 url: '<?php echo site_url(); ?>ajax/api_ajax/add_agency_api_integration',  
            //                 data: {
            //                     connected_service: 1 //agency_api_id for propertyME
            //                 },
            //                 success: function (response) {
            //                     swal({
            //                         title: "Success!",
            //                         text: response.message,
            //                         type: "success",
            //                         timer: 2000,
            //                         showConfirmButton: false
            //                     }, function() {
            //                         window.location = api_link;
            //                     });
                    
            //                 },
            //                 error: function (response) {
            //                     console.log("🚀 ~ file: _propertyme.php:234 ~ continueButton.on ~ response:", response);
            //                     swal("Error!", response.message, "error");
            //                 },
            //             });
            //         }
                    
            //     },
            //     error: function(xhr, status, error) {
            //         console.error('Error fetching data: ' + error);
            //     }
            // });
        }, 'propertyME-continue-btn');

        smartwizardPME.initialize();

    });
</script>