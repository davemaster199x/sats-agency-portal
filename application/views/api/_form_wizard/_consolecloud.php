<div class="container">
    <div class="modal fade apiIntegrationWizardModal" id="consoleCloudModal" tabindex="-1" role="dialog" aria-labelledby="consoleCloudModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5> 
                    <div class="text-center w-100">
                        <img src="/images/api/logo/cc_logo.png" alt="ConsoleCloud logo">
                        <h5 class="mt-2">To proceed, you must accept the following terms and conditions</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="consolecloud_api_link" value="" />
                    <div id="smartwizardConsoleCloud">
                        <ul class="nav mt-4">
                            <li class="nav-item">
                                <a href="#consoleCloudStep-1" class="nav-link">
                                    <span class="num">1</span>
                                    <small class="title-text">Adding compliance items</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#consoleCloudStep-2" class="nav-link">
                                    <span class="num">2</span>
                                    <small class="title-text">Visit not required for compliance</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#consoleCloudStep-3" class="nav-link">
                                    <span class="num">3</span>
                                    <small class="title-text">Data discrepancies</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#consoleCloudStep-4" class="nav-link">
                                    <span class="num">4</span>
                                    <small class="title-text">Currently serviced properties</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#consoleCloudStep-5" class="nav-link">
                                    <span class="num">5</span>
                                    <small class="title-text">Delivery of documents into console</small>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#consoleCloudStep-6" class="nav-link">
                                    <span class="num">6</span>
                                    <small class="title-text">Confirm</small>
                                </a>
                            </li>
                        </ul>
                        <div  class="tab-content">
                            <div id="consoleCloudStep-1" class="tab-pane" role="tabpanel" aria-labelledby="consoleCloudStep-1">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Any compliance items assigned to <?=$this->config->item('COMPANY_NAME_SHORT')?> will trigger a visit and an annual Subscription Fee</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span><?=$this->config->item('COMPANY_NAME_SHORT')?> will attend all active properties only when required, to meet your states legislation</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="consoleCloudStep-2" class="tab-pane" role="tabpanel" aria-labelledby="consoleCloudStep-2">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>If you require any additional visits outside of what is required for legislation (eg, Beeping Alarm<?php echo ( $this->session->country_id == 1 && $agency_row->state != 'QLD' )?'/Change of Tenancy':null; ?>) please create a job in our Agency Portal or email us at <a href="<?= make_email('info'); ?>"><?= make_email('info'); ?></a></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="consoleCloudStep-3" class="tab-pane" role="tabpanel" aria-labelledby="consoleCloudStep-3">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span><?=$this->config->item('COMPANY_NAME_SHORT')?> will not amend our existing expiry dates if the property has previously been serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?>, and there is a discrepancy between Console/<?=$this->config->item('COMPANY_NAME_SHORT')?> expiry date data. However, for new properties, where applicable, please add the last inspection date and subscription expiry date so that <?=$this->config->item('COMPANY_NAME_SHORT')?> can ensure that there are no data discrepancies.</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="consoleCloudStep-4" class="tab-pane" role="tabpanel" aria-labelledby="consoleCloudStep-4">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Any property that is currently serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?> will remain on the same service option, we will not adjust the service based on data received from Console. If you wish to change the services that <?=$this->config->item('COMPANY_NAME_SHORT')?> conducts on a property, you must do this via the <a href="/jobs/create">Agency Portal</a> or by contacting our friendly Customer Service team on <?php echo $agency_row->agent_number; ?>. Any new properties added to our database via Console will only have the service type applied that is the compliance item.</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="consoleCloudStep-5" class="tab-pane" role="tabpanel" aria-labelledby="consoleCloudStep-5">
                                <div class="container">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-start gap-1">
                                            <i class="fa fa-check-circle fa-xl text-success" aria-hidden="true"></i> 
                                            <span>Upon job completion, we will upload into Console, the Statement of Compliance (Workflows > Compliance) and where applicable, the Invoice (Accounts>Bills).</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="consoleCloudStep-6"  class="tab-pane" role="tabpanel" aria-labelledby="consoleCloudStep-6">
                                <div class="container">
                                    <div class="checkbox">
                                        <input name="console_accept_terms_chk" type="checkbox" id="console_accept_terms_chk">
                                        <label for="console_accept_terms_chk">By ticking, you are confirming that you have read, understood and agree the terms and conditions.</label>
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
<script>
    jQuery(document).ready(function() {
        var smartwizardConsoleCloud = new smartwizardManager('smartwizardConsoleCloud', 'dots');
        smartwizardConsoleCloud.addCustomButton('Agree and continue', function () {
            if ($('#console_accept_terms_chk').prop('checked')) {
                $("#consoleCloudModal").hide();

                // api_link = $('#consolecloud_api_link').val();
        
                jQuery('#load-screen').show();
                jQuery.ajax({
                    url: "/console/log_user_who_accepted_terms",
                    type: 'POST'
                }).done(function( crm_ret ){
                    jQuery('#load-screen').hide();

                    // check if api is in database
                    $.ajax({
                        url: '<?= base_url("ajax/agency_api_integration_ajax/is_api_integrated"); ?>?con_service=5', //api id of console cloud
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if(data){
                                // launch fancybox
                                $.fancybox.open({
                                    src  : '#console_fb'
                                });
                            } else {
                                // alert('wala pa na save.. e save sa bago re redirect')
                                $.ajax({
                                    type: "POST",
                                    url: '<?php echo site_url(); ?>ajax/api_ajax/add_agency_api_integration',  
                                    data: {
                                        connected_service: 5 //agency_api_id for console cloud
                                    },
                                    success: function (response) {
                                        swal({
                                            title: "Success!",
                                            text: response.message,
                                            type: "success",
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                        // launch fancybox
                                        $.fancybox.open({
                                            src  : '#console_fb'
                                        });
                                    },
                                    error: function (response) {
                                        console.log("🚀 ~ file: _consolecloud.php:197 ~ finishButtonConsoleCloud.on ~ response:", response)
                                        swal("Error!", response.message, "error");
                                    },
                                });
                                    
                            }
                            
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching data: ' + error);
                        }
                    });
            
                });
                
            } else {
                swal("Ooops!", "Please tick the checkbox to proceed!", "warning");
            }
        }, 'consolecloud-continue-btn disabled');
        smartwizardConsoleCloud.initialize();

 
        // Checkbox change event
        $('#console_accept_terms_chk').on('change', function() {
            // Check the state of the checkbox and toggle the "disabled" class on the finish button
            checkTC();
        });

        // Function to check if the T&C is accepted and toggle the "disabled" class on the finish button
        function checkTC() {
            var finishButton = $('.consolecloud-continue-btn');

            // Toggle the "disabled" class based on the state of the checkbox
            if ($('#console_accept_terms_chk').prop('checked')) {
            finishButton.removeClass('disabled');
            } else {
            finishButton.addClass('disabled');
            }
        }
    });
</script>