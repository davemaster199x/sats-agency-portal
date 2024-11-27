

<section class="box-typical box-typical-padding">
<h5 class="m-t-lg with-border"><a href="/jobs/create"><?php echo $title; ?></a></h5>
        <!-- Header -->
        <header class="box-typical-header">
            <div class="box-typical box-typical-padding">
                <?php 
                    echo form_open(base_url('jobs/create'),'id="create_job_search_form"'); 
                ?>
                    <div class="form-groupsss row">
                        <div class="float-left">
                            <label for="exampleSelect" class="col-sm-12 form-control-label">Property Manager</label>
                            <div class="col-sm-12" style="width:300px;">
								<select id="pm" name="pm" class="form-control field_g2 select2-photo">
							<option value="">---</option>														
                            <option <?php  echo ( $this->input->get_post('pm') == '0' )?'selected="selected"':''; ?> value="0" data-photo="<?php echo $this->config->item('photo_empty'); ?>">No PM assigned</option>
							<?php
							foreach( $user_pm->result() as $row ){ ?>
								<option <?php echo ($this->input->get_post('pm')==$row->agency_user_account_id)?'selected="selected"':'' ?>  data-photo="<?php echo $this->jcclass->displayUserImage($row->photo); ?>" value="<?php echo $row->agency_user_account_id; ?>"><?php echo $this->jcclass->formatStaffName($row->fname,$row->lname); ?></option>
							<?php
							}
							?>
						</select>
                            </div>
                        </div>
                        <div class="float-left">
                            <label class="col-sm-12 form-control-label">Search</label>
                            <div class="col-sm-12">
                               
                                    <input type="text" class="form-control" id="search" name="search" value="<?php echo $this->input->get_post('search') ?>" placeholder="Text">
                     
                            </div>
                        </div>

                        <div class="float-left">
                            <label class="col-sm-12 form-control-label">&nbsp;</label>
                            <div class="col-sm-12">
								<input type="submit" class="btn btn-inline" id="create_job_search_btn" value="Search">
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </form>
            </div>
        </header>

        <!-- list -->
        <div class="box-typical-body">

            <div class="table-responsive">
                <table class="table table-hover main-table">
                    <thead>
                        <tr>
                            <th>Address</th>
                            <th>Property Manager</th>
                            <th>Service Type</th>
                            <th>Create Job</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $ps->result() as $row ): ?>
                            <tr>
                                <td>
                                    <a href="/properties/property_detail/<?php echo $row->property_id ?>" target="blank"><?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3}"; ?></a>
                                </td>
                                <td>
                                    <?php
                                        if( isset($row->pm_id_new) && $row->pm_id_new != 0 ){
                                            echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
                                            echo "{$row->pm_fname} {$row->pm_lname}";
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?= Alarm_job_type_model::icons($row->ajt_id); ?>
                                </td>
                                <td class="create_job_td">
                                    <input name="property_id" type="hidden" class="property_id" value="<?php echo $row->property_id; ?>" />

                                    <div class="float-left">
                                        <button type="button" class="btn  btn_create_job btn-sm">Create Job</button>
                                    </div>

                                    <a href="javascript:;" class="fb_trigger" style="display:none;" data-fancybox data-src="#hidden-content-<?php echo $row->property_services_id; ?>">
									Trigger the fancybox
								</a>

                                    <!---- LIGHTBOX ------->
                                    <div style="display: none;" class="fancy_box_popup chops" data-ajtID="<?php echo $row->property_services_id; ?>" id="hidden-content-<?php echo $row->property_services_id; ?>">

                                        <div class="row">
                                            <div class="col-lg-12 columns">
                                                <h2>Create a Job</h2>
                                                <hr class="gherx_hr" />
                                            </div>

                                            <div class="col-lg-4 columns" style="padding-top: 10px;">
                                                <b>Please select the Job Type</b>
                                            </div>
                                            <div class="col-lg-8 columns">
                                                <button data-val="Fix or Replace" id="repair_button" class="btn btn-inline btn-danger-outline repair_button job_type_btn" type="button">Repair</button>
                                                <button data-val="Change of Tenancy" id="change_tenancy_button" class="btn btn-inline btn-danger-outline change_tenancy_button job_type_btn" type="button">Change of Tenancy</button>
                                                <?php 
                                                if( $this->session->country_id==1 && $row->p_state=="QLD" ){ ?>
                                                <button data-val="Lease Renewal" id="lease_renewal_button" class="btn btn-inline btn-danger-outline lease_renewal_button job_type_btn" type="button">Lease Renewal</button>
                                                <?php } ?>
                                                <!--<button data-val="Fix or Replace" id="fr_button" class="btn btn-inline btn-danger-outline fr_button job_type_btn" type="button">Fix & Replace</button>-->
                                            </div>
                                            <div class="col-lg-12 columns">
                                                <hr class="gherx_hr" />
                                            </div>
                                        </div>

                                        <div class="create_job_box">
                                           <?php echo form_open(base_url('jobs/create_job'),"id='form_create_job$row->property_services_id'"); ?>

                                            <div class="row row_prop_vacant hiddenv2">

                                                <div class="col-lg-4 columns" style="padding-top: 10px;"><b>Vacant/Will be Vacant?</b></div>
                                                <div class="col-lg-8 columns">
                                                    <div class="form-group">
                                                        <select id="prop_vacant" name="prop_vacant" class="form-control col-lg-6 prop_vacant">
                                                            <option value="">Please Select</option>
                                                                                    <option value='0'>No</option>	
                                                                                    <option value='1'>Yes</option>																																			
                                                                                    </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 columns">
                                                    <hr class="gherx_hr" />
                                                </div>
                                            </div>

                                            <div class="row for_notes_row hiddenv2">

                                                <div class="col-lg-4 columns" style="padding-top: 10px;"><b>Which alarm in the property is faulty? (If unknown please write unknown)</b></div>
                                                <div class="col-lg-8 columns">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="job_comments_faulty_alarm" id="job_comments_faulty_alarm" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 columns">
                                                    <hr class="gherx_hr" />
                                                </div>

                                            </div>

                                            <div class="row for_notes_row hiddenv2">

                                                <div class="col-lg-4 columns" style="padding-top: 10px;"><b>What is wrong with the alarm?</b></div>
                                                <div class="col-lg-8 columns">
                                                    <div class="form-group">
                                                        <select class="form-control" name="job_comments_wrong_with_the_alarm" id="job_comments_wrong_with_the_alarm">
                                                            <option value="">Please Select</option>
                                                            <option value='Beeping'>Beeping</option>	
                                                            <option value='Missing'>Missing</option>																																			
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 columns">
                                                    <hr class="gherx_hr" />
                                                </div>

                                            </div>

                                            <div class="row for_notes_row hiddenv2">

                                                <div class="col-lg-4 columns" style="padding-top: 10px;"><b>What is the brand of the problematic alarm?</b></div>
                                                <div class="col-lg-8 columns">
                                                    <div class="form-group">
                                                        <select class="form-control" name="job_comments_faulty_brand" id="job_comments_faulty_brand">
                                                            <option value="">Please Select</option>
                                                            <option value='Brooks'>Brooks</option>	
                                                            <option value='Emerald Planet'>Emerald Planet</option>	
                                                            <option value='Red'>Red</option>	
                                                            <option value='Detector Inspector'>Detector Inspector</option>
                                                            <option value='Unknown'>Unknown</option>	                                                            																																		
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 columns">
                                                    <hr class="gherx_hr" />
                                                </div>

                                            </div>                                            

                                            <div class="row row_urgent_job" style="display: none;">

                                            <div class="col-lg-4 columns" style="padding-top: 10px;"><b>Urgent Job?</b></div>
                                            <div class="col-lg-8 columns">
                                                <div class="form-group">
                                                    <select id="urgent_job" name="urgent_job" class="form-control col-lg-6">
                                                        <option value="">Please Select</option>
                                                                                <option value='0'>No</option>	
                                                                                <option value='1'>Yes</option>																																			
                                                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 columns">
                                                <hr class="gherx_hr" />
                                            </div>
                                            </div>
                                            
                                            
                                        <div class="hiddenv2 row_rel_prop_hidden">
                                            
                                            <div class="row">

                                                <div class="col-lg-4 columns lease_start_date_box">
                                                    <div class="form-group">
                                                        <div class="input-group flatpickr" data-wrap="true">
                                                            <input type="text" class="form-control" name="lease_start_date" id="lease_start_date" data-input placeholder="Lease Start Date" />
                                                            <span class="input-group-append" data-toggle>
                                                                                                                    <span class="input-group-text">
                                                                                                                        <i class="font-icon font-icon-calend"></i>
                                                                                                                    </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 columns">
                                                    <div class="form-group"><input type="text" class="form-control" name="work_order" placeholder="Work Order" /></div>
                                                </div>

                                                <div class="col-lg-4 columns">
                                                    <div class="form-group"><input type="text" class="form-control" name="lockbox_code" placeholder="Lockbox Code" /></div>
                                                </div>
                                                
                                                <div class="col-lg-4 columns">
                                                    <div class="form-group"><input type="text" class="form-control" name="key_number" value="<?php echo $row->key_number; ?>" placeholder="Key Number" /></div>
                                                </div>


                                                <div class="col-lg-12 columns">
                                                    <hr class="gherx_hr" />
                                                </div>
                                            </div>
                                            
                                            

                                            <div class="row property_vacant_section" style="display:none;">
                                                <div class="col-lg-4" style="padding-top: 10px;">
                                                    <b>Property Vacant</b>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <div class="input-group flatpickr" data-wrap="true">
                                                            <input type="text" class="form-control" name="vacant_from" placeholder="Vacant From" data-input />
                                                            <span class="input-group-append" data-toggle>
                                                                                        <span class="input-group-text">
                                                                                            <i class="font-icon font-icon-calend"></i>
                                                                                        </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <div class="input-group flatpickr" data-wrap="true">
                                                            <input type="text" class="form-control" name="vacant_to" placeholder="Vacant To" data-input />
                                                            <span class="input-group-append" data-toggle>
                                                                                        <span class="input-group-text">
                                                                                            <i class="font-icon font-icon-calend"></i>
                                                                                        </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 columns">
                                                    <hr class="gherx_hr" />
                                                </div>
                                            </div>
                                            
                                            
                                            

                                            <table class="table">

                                                <!-- TENANTS -->
                                                <tr>
                                                    <td colspan="100%" style="padding: 0px;">
                                                        <div class="tenant_section loader_wrapper_pos_rel">
														<div class="loader_block_v2" style="display: none;"> <div id="div_loader"></div></div>
                                                            <div class="tenants_ajax_box"></div>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </table>

                                            <div class="row">
                                                <div class="col-lg-12 columns">
                                                    <hr class="gherx_hr" />
                                                </div>
                                                <div class="col-lg-12">
                                                    <label class="job_comments_label" style="font-weight: bold;">Job Comments</label>
                                                    <div class="form-group">
                                                        <textarea name="job_comments" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12 columns">
                                                     <input type="hidden" name="hid_prop_id" value="<?php echo $row->property_id; ?>">
                                                     <input type="hidden" id="hid_prop_state" name="hid_prop_state" value="<?php echo $row->p_state; ?>">
                                                     <input type="hidden" name="hid_job_type" value="">
                                                     <Input type="hidden" name="hid_ajt" value="<?php echo $row->property_services_id; ?>">
                                                     <Input type="hidden" name="hid_ajt_id" value="<?php echo $row->alarm_job_type_id; ?>">
                                                    <?php
                                                    $has_attended_in_30_days = 0;
                                                    if( $row->property_id > 0 ){

                                                        // get completed job in 30 days
                                                        $job_sql_str = "
                                                        SELECT `id`,`status`,`date`
                                                        FROM `jobs`
                                                        WHERE `property_id` = {$row->property_id}
                                                        AND `status` = 'Completed'                                                        
                                                        AND `del_job` = 0                                    
                                                        AND `assigned_tech` NOT IN(1,2)
                                                        ORDER BY `date` DESC
                                                        ";
                                                        $job_sql = $this->db->query($job_sql_str);
                                                        $job_row = $job_sql->row();                                                        
                                                        if( $job_row->date >= date('Y-m-d',strtotime("-30 days")) ){
                                                            $has_attended_in_30_days = 1;
                                                        }

                                                    }                                                    
                                                    ?>
                                                    <input type="hidden" class="has_attended_in_30_days" value="<?php echo $has_attended_in_30_days; ?>">
                                                    <button type="button" id="btn_create_new_job"  name="btn_create_new_job" class="btn btn-inline" style="float:right;margin-right:0px;">Create Job</button>
                                                </div>
                                            </div>
                                                    
                                                    </div>
                                                    
                                            <?= form_close(); ?>
                                        </div>
                                        
                                    </div>
                                    
                                </td>
                            </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation example" style="text-align:center">

                <?php echo $pagination; ?>

            </nav>
            <div class="pagi_count"><?php echo $pagi_count; ?></div>

        </div>
        <!--.box-typical-body-->


    </section>
    <!--.box-typical-->



<style>
    .btn.btn-inline.btn_create_job {
        margin-right: 15px;
    }

    .btn.btn-inline {
        /*margin: 0;*/
    }
</style>
<script>

    

    function getTenants(obj) {

        var property_id = obj.parents("td.create_job_td:first").find(".property_id").val();
        var tenants_block = obj.parents("td.create_job_td:first").find(".tenants_ajax_box");
        var loader_block = obj.parents("td.create_job_td:first").find(".loader_block_v2");
        var fb_trigger = obj.parents("td.create_job_td:first").find(".fb_trigger");

        console.log("property_id: " + property_id);

        // clear all tenants div
        $('.tenants_ajax_box').empty();

        //load tenants ajax box (via ajax)
        tenants_block.load('/properties/tenants_ajax', {
                prop_id: property_id
            }, function(response, status, xhr) {

                $('.loader_block_v2').hide();
				$('[data-toggle="tooltip"]').tooltip(); //init tooltip
				phone_mobile_mask(); //init phone/mobile mask
				mobile_validation(); //init mobile validation
				phone_validation(); //init phone validation
				add_validate_tenant(); //init new tenant validation

                fb_trigger.click(); // trigger fancybox popup
            }

        );

    }


    function reset(obj){
        obj.parents('.create_job_td').find('.job_type_btn').removeClass('active');
        
        obj.parents('.create_job_td').find('.hiddenv2').css('visibility','hidden');
        obj.parents('.create_job_td').find('.lease_start_date_box').css('visibility','hidden');
        
        obj.parents('.fancy_box_popup').find('select[name="prop_vacant"]').val(""); //clear prop vacant
        obj.parents('.fancy_box_popup').find('textarea[name="job_comments"]').val(""); //clear job comments
        obj.parents('.fancy_box_popup').find('input[name="lease_start_date"]').val(""); //clear lease start date input
    }

    jQuery(document).ready(function() {


        //success/error message sweel alert pop  start
        <?php if( $this->session->flashdata('status') &&  $this->session->flashdata('status') == 'success' ){?>
            swal({
                title: "Success!",
                text: "<?php echo $this->session->flashdata('success_msg') ?>",
                type: "success",
                confirmButtonClass: "btn-success"
            });
        <?php }else if(  $this->session->flashdata('status') &&  $this->session->flashdata('status') == 'error'  ){ ?>
            swal({
                title: "Error!",
                text: "<?php echo $this->session->flashdata('error_msg') ?>",
                type: "error",
                confirmButtonClass: "btn-danger"
            });
        <?php } ?>
        //success/error message sweel alert pop  end


        //init datepicker
        jQuery('.flatpickr').flatpickr({
            dateFormat: "d/m/Y"
		});
		
			//select2
			$(".select2-photo").not('.manual').select2({
			templateSelection: select2Photos,
			templateResult: select2Photos
		    });


		// load tenant section via ajax
        jQuery(document).on("click", ".btn_create_job", function() {
            console.log("Bugo");

            var obj = jQuery(this);
            var prop_id = obj.parents('.create_job_td').find('input[name="property_id"]');
            var tenant_box = obj.parents('.create_job_td').find(".tenants_ajax_box");
            
            obj.parents('.create_job_td').find('input[name="hid_job_type"]').val("");

            getTenants(obj);
            reset(obj);
            
        });

		
		$(document).on('click','.job_type_btn',function(){  // job type active button 
			var obj = $(this);
            var dataVal = obj.data('val');

            obj.parents('.fancy_box_popup').find('input[name="hid_job_type"]').val(dataVal);

			$('.job_type_btn').removeClass('active');
			obj.addClass('active');


            obj.parents('.fancy_box_popup').find('.row_rel_prop_hidden').css('visibility', 'hidden');

		}); 

        $('.repair_button').on('click', function(e) { // repair button
			e.preventDefault();
			var obj = $(this);
			obj.parents('.fancy_box_popup').find('.row_prop_vacant').css('visibility', 'visible');
            obj.parents('.fancy_box_popup').find('.for_notes_row').css('visibility', 'visible');
            $('.row_urgent_job').css('display', 'none')
			obj.parents('.fancy_box_popup').find('label.job_comments_label').html("Describe the Problem and/or Provide Additional Information"); // changed job comments label
            obj.parents('.fancy_box_popup').find('.lease_start_date_box').css('visibility','hidden');
			reset(obj);
        });

        $('.fr_button').on('click', function(e) { // fix & replace button
			e.preventDefault();
			var obj = $(this);
			obj.parents('.fancy_box_popup').find('.row_prop_vacant').css('visibility', 'visible');
            obj.parents('.fancy_box_popup').find('.for_notes_row').css('visibility', 'visible');
            $('.row_urgent_job').css('display', 'flex')
			obj.parents('.fancy_box_popup').find('label.job_comments_label').html("Describe the Problem and/or Provide Additional Information"); // changed job comments label
            obj.parents('.fancy_box_popup').find('.lease_start_date_box').css('visibility','hidden');
			reset(obj);
        });

        $('.change_tenancy_button').on('click', function(e) { // changed tenancy button
			e.preventDefault();
			var obj = $(this);
            var prop_state = obj.parents('.fancy_box_popup').find('#hid_prop_state').val();

            if(prop_state=="NSW"){
                swal(
                    {
                        title: "",
                        text: "Change of Tenancy callouts are no longer required due to Legislation changes, however <?=$this->config->item('COMPANY_NAME_SHORT')?> will attend if you need us to.<br/>Do you wish to proceed? ",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes, Proceed",
                        cancelButtonClass: "btn-danger",
                        cancelButtonText: "No, Cancel!",
                        closeOnConfirm: false,
                        closeOnCancel: true,
                        html: true
                    },
                    function (isconfirm){
                        if(isconfirm){
                            swal.close();

                            obj.parents('.fancy_box_popup').find('.row_prop_vacant').css('visibility', 'visible');
                            obj.parents('.fancy_box_popup').find('.for_notes_row').css('visibility', 'hidden');
                            $('.row_urgent_job').css('display', 'none')
                            obj.parents('.fancy_box_popup').find('label.job_comments_label').html("Job Comments"); // changed job comments label
                            obj.parents('.fancy_box_popup').find('.lease_start_date_box').css('visibility','hidden');
                            reset(obj);

                        }else{
                            window.location.replace("/home");
                        }
                    }
                )
            }else if( prop_state=="QLD" ){

                var has_attended_in_30_days = obj.parents('.fancy_box_popup').find('.has_attended_in_30_days').val();

                if( has_attended_in_30_days == 1  ){

                    swal(
                        {
                            title: "",
                            text: "We have attended this property in the last 30 days, and may not be required to attend again. Please confirm that you'd like us to attend?",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-success",
                            confirmButtonText: "Yes, Confirm!",
                            cancelButtonClass: "btn-danger",
                            cancelButtonText: "No, Cancel!",
                            closeOnConfirm: false,
                            closeOnCancel: true,
                            html: true
                        },
                        function (isconfirm){
                            if(isconfirm){
                                swal.close();

                                obj.parents('.fancy_box_popup').find('.row_prop_vacant').css('visibility', 'visible');
                                obj.parents('.fancy_box_popup').find('.for_notes_row').css('visibility', 'hidden');
                                $('.row_urgent_job').css('display', 'none')
                                obj.parents('.fancy_box_popup').find('label.job_comments_label').html("Job Comments"); // changed job comments label
                                obj.parents('.fancy_box_popup').find('.lease_start_date_box').css('visibility','hidden');
                                reset(obj);

                            }else{
                                window.location.replace("/home");
                            }
                        }
                    );

                }else{ // default

                    obj.parents('.fancy_box_popup').find('.row_prop_vacant').css('visibility', 'visible');
                    obj.parents('.fancy_box_popup').find('.for_notes_row').css('visibility', 'hidden');
                    $('.row_urgent_job').css('display', 'none')
                    obj.parents('.fancy_box_popup').find('label.job_comments_label').html("Job Comments"); // changed job comments label
                    obj.parents('.fancy_box_popup').find('.lease_start_date_box').css('visibility','hidden');
                    reset(obj);

                }                                            

            }else{
                obj.parents('.fancy_box_popup').find('.row_prop_vacant').css('visibility', 'visible');
                obj.parents('.fancy_box_popup').find('.for_notes_row').css('visibility', 'hidden');
                $('.row_urgent_job').css('display', 'none')
                obj.parents('.fancy_box_popup').find('label.job_comments_label').html("Job Comments"); // changed job comments label
                obj.parents('.fancy_box_popup').find('.lease_start_date_box').css('visibility','hidden');
                reset(obj);
            }
            

            

            
		});
		
        $('.lease_renewal_button').on('click', function(e) { //lease renewal button
			e.preventDefault();
			var obj = $(this);           

            var prop_state = obj.parents('.fancy_box_popup').find('#hid_prop_state').val();

            if( prop_state=="QLD" ){

                var has_attended_in_30_days = obj.parents('.fancy_box_popup').find('.has_attended_in_30_days').val();

                if( has_attended_in_30_days == 1  ){

                    swal(
                        {
                            title: "",
                            text: "We have attended this property in the last 30 days, and may not be required to attend again. Please confirm that you'd like us to attend?",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-success",
                            confirmButtonText: "Yes, Confirm!",
                            cancelButtonClass: "btn-danger",
                            cancelButtonText: "No, Cancel!",
                            closeOnConfirm: false,
                            closeOnCancel: true,
                            html: true
                        },
                        function (isconfirm){
                            if(isconfirm){
                                swal.close();

                                obj.parents('.fancy_box_popup').find('.row_prop_vacant').css('visibility', 'visible');
                                obj.parents('.fancy_box_popup').find('.for_notes_row').css('visibility', 'hidden');
                                $('.row_urgent_job').css('display', 'none')
                                obj.parents('.fancy_box_popup').find('label.job_comments_label').html("Job Comments"); // changed job comments label
                                obj.parents('.fancy_box_popup').find('.lease_start_date_box').css('visibility','hidden');
                                reset(obj);

                            }else{
                                window.location.replace("/home");
                            }
                        }
                    );

                }else{ // default

                    obj.parents('.fancy_box_popup').find('.row_prop_vacant').css('visibility', 'visible');
                    obj.parents('.fancy_box_popup').find('.for_notes_row').css('visibility', 'hidden');
                    $('.row_urgent_job').css('display', 'none')
                    obj.parents('.fancy_box_popup').find('label.job_comments_label').html("Job Comments"); // changed job comments label
                    obj.parents('.fancy_box_popup').find('.lease_start_date_box').css('visibility','hidden');
                    reset(obj);

                }                                            

            }else{ // default

                obj.parents('.fancy_box_popup').find('.row_prop_vacant').css('visibility', 'visible');
                obj.parents('.fancy_box_popup').find('.for_notes_row').css('visibility', 'hidden');
                $('.row_urgent_job').css('display', 'none')
                obj.parents('.fancy_box_popup').find('label.job_comments_label').html("Job Comments"); // changed job comments label
                obj.parents('.fancy_box_popup').find('.lease_start_date_box').css('visibility','hidden');
                reset(obj);

            }

        });
		

		$('.prop_vacant').on('change',function(e){  //property vacant tweak
            var obj = $(this);
            var selectedJob = obj.parents('.fancy_box_popup').find('input[name="hid_job_type"]').val();

            if($.trim(obj.val().length)==0){
                obj.parents('.fancy_box_popup').find('.row_rel_prop_hidden').css('visibility','hidden');
                obj.parents('.fancy_box_popup').find('.lease_start_date_box').css('visibility','hidden');
            }else{
                if(obj.val()==1){
                    obj.parents('.fancy_box_popup').find('.row_rel_prop_hidden').css('visibility','visible');
                    obj.parents('.fancy_box_popup').find('.property_vacant_section').slideDown();
                    obj.parents('.fancy_box_popup').find('.lease_start_date_box').css('visibility','hidden');
                    obj.parents('.fancy_box_popup').find('input[name="lease_start_date"]').val(""); //clear
                }else if(obj.val()==0){
                    obj.parents('.fancy_box_popup').find('.row_rel_prop_hidden').css('visibility','visible');
                    obj.parents('.fancy_box_popup').find('.property_vacant_section').slideUp();
                    obj.parents('.fancy_box_popup').find('input[name="vacant_from"]').val(""); //clear field
                    obj.parents('.fancy_box_popup').find('input[name="vacant_to"]').val("");  //clear field
                    if(selectedJob!="Fix or Replace"){
                        obj.parents('.fancy_box_popup').find('.lease_start_date_box').css('visibility','visible');
                    }
                }
            }
     
		});


        $(document).on('click','#btn_create_new_job',function(){

            var obj = $(this);
            var job_type = obj.parents('.fancy_box_popup').find('input[name="hid_job_type"]').val();
            var ajt = obj.parents('.fancy_box_popup').find('input[name="hid_ajt"]').val();
            var prop_vacant = obj.parents('.fancy_box_popup').find('select[name="prop_vacant"]').val();
            var lease_date = obj.parents('.fancy_box_popup').find('input[name="lease_start_date"]').val();
            var job_comments = obj.parents('.fancy_box_popup').find('textarea[name="job_comments"]').val();

            var job_comments_faulty_alarm = obj.parents('.fancy_box_popup').find('#job_comments_faulty_alarm').val();
            var job_comments_wrong_with_the_alarm = obj.parents('.fancy_box_popup').find('#job_comments_wrong_with_the_alarm').val();
            var job_comments_faulty_brand = obj.parents('.fancy_box_popup').find('#job_comments_faulty_brand').val();

            var form = "#form_create_job"+ajt;
            var swal_error_txt = '';

            if(job_type=="Lease Renewal" && prop_vacant==0){
                if($.trim(lease_date).length ==0){
                    swal('','Lease Start Date must not be empty.','error');
                return false;
                }  
            }
            if(job_type == "Fix or Replace"){                 

                if( job_comments_faulty_alarm == '' ){
                    swal_error_txt += "'Faulty alarm' is required\n";
                }
                
                if( job_comments_wrong_with_the_alarm == '' ){
                    swal_error_txt += "'Wrong with the alarm' is required\n";
                }

                if( job_comments_faulty_brand == '' ){
                    swal_error_txt += "'Faulty brand' is required\n";
                }

                if(job_comments == '' ){
                    swal_error_txt += "'Describe the Problem and/or Provide Additional Information' is required.\n";
                } 
                
                if( swal_error_txt != '' ){

                    swal('',swal_error_txt,'error');
                    return false;

                }

            }
            
            if(job_type == "Fix or Replace"){
                var customSweetMsg = "Create Repair Job?";
            }else if(job_type== "Change of Tenancy"){
                var customSweetMsg = "Create Change of Tenancy Job?";
            }else if(job_type== "Lease Renewal"){
                var customSweetMsg = "Create Lease Renewal Job?";
            }else{
                var customSweetMsg = "Are you sure you want to create a job?";
            }
            
            var submitCount = 0;

            swal(
                    {
                        title: "",
                        text: customSweetMsg,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes, Create",
                        cancelButtonClass: "btn-danger",
                        cancelButtonText: "No, Cancel!",
                        closeOnConfirm: false,
                        closeOnCancel: true,
                    },
                    function(isConfirm){
                        if(isConfirm){
                            if(submitCount==0){
                                submitCount++;
                                $(form).submit();
                                return false;
                            }else{
                                swal('','Form submission is in progress.','error');
                                return false;
                            }
                            
                        }
                        
                    }
            );
            
            
        });




    }); //document ready end


   
</script>
