<section class="box-typical box-typical-padding">


<?php
	if($agencyIsAutoRenew == true){
		if(!empty($list)){
			?>
		<div class="row" >
			<div class="col-lg-12">
				<div style="margin-bottom:0;" class="alert alert-success alert-close alert-dismissible fade show" role="alert">
				<i class="font-icon font-icon-inline font-icon-warning"></i> All jobs on this screen will be Auto-Renewed in <span id="auto_renew_d"></span> days
				</div>
			</div>
		</div>
		<?php
		}
	}else{
?>
		<div class="row" >
			<div class="col-lg-12">
			<div style="margin-bottom:0;" class="alert alert-success alert-icon alert-close alert-dismissible fade show" role="alert">
							<i class="font-icon font-icon-warning"></i>
							All properties that eclipse 12 months service and <?=$this->config->item('COMPANY_NAME_SHORT')?> have not been advised to proceed with our services by your office, to fulfil our obligation to your Landlord/s, will be deemed non-compliant by <?=$this->config->item('COMPANY_NAME_SHORT')?>. <br/>
							<span class="txt-red"><?=$this->config->item('COMPANY_NAME_SHORT')?> will NOT be held responsible for any incidents or claims relating to the properties that are no longer under an agreement with <?=$this->config->item('COMPANY_NAME_SHORT')?>.</span>
						</div>
			</div>
		</div>
<?php
	}
?>


<h5 class="m-t-lg with-border"><a href="/jobs/service_due"><?php echo $title; ?></a></h5>
	<!-- Header -->
	<header class="box-typical-header">
		<div class="box-typical box-typical-padding">
		<?php
		$export_links_params_arr = array(
			'pm_id' => $this->input->get_post('pm_id'),
			'search' => $this->input->get_post('search')
		);
		$export_link_params = '/jobs/service_due/?export=1&'.http_build_query($export_links_params_arr);

		$form_attr = array(
			'id' => 'jform'
		);
		echo form_open('jobs/service_due',$form_attr);
		?>
				<div class="form-groupss">
					<div class="row">
						<div class="col columns">
							<label for="exampleSelect" class=" form-control-label">Property Manager</label>
							<div>
								<select name="pm_id" class="form-control field_g2 select2-photo">
									<option value="">---</option>
									<option <?php  echo ( $this->input->get_post('pm_id') == '0' )?'selected="selected"':''; ?> value="0" data-photo="<?php echo $this->config->item('photo_empty'); ?>">No PM assigned</option>
									<?php
									foreach( $pm_filter->result() as $row ){ ?>
										<option data-photo="<?php echo ($row->photo!="")?$this->config->item('user_photo')."/".$row->photo:$this->config->item('photo_empty'); ?>" value="<?php echo $row->pm_id_new; ?>" <?php echo ( $row->pm_id_new == $this->input->get_post('pm_id') )?'selected="selected"':''; ?>><?php echo "{$row->fname} {$row->lname}"; ?></option>
									<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col columns">
							<label class="form-control-label">Search</label>
							<div>

									<input type="text" class="form-control" name="search" placeholder="Text" value="<?php echo $this->input->get_post('search'); ?>" />

							</div>
						</div>

						<div class="col columns">
							<label class="form-control-label">&nbsp;</label>
							<div>
								<button type="submit" class="btn btn-inline">Search</button>
							</div>
						</div>
						<div class="col columns">
							<div class="float-right">
								<section class="proj-page-section mt-4">
									<div class="proj-page-attach">
										<i class="font-icon font-icon-post"></i>
										<p class="name"><?php echo $title; ?> CSV</p>
										<p>
											<a href="<?php echo $export_link_params; ?>" target="blank">
												Download
											</a>
										</p>
									</div>
								</section>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</header>

	<!-- list -->
	<div class="box-typical-body">


		<?php

			if( $this->session->flashdata('nlm_chk_flag')==1){

				echo "<div class='text-center'><strong style='color:#fa424a;'>These Properties cannot be NLM because it has active jobs:</strong><ul style='margin-bottom:10px;'>";
					foreach( $this->session->flashdata('propArray') as $prop_data ){
						echo "<li>{$prop_data['prop_address']}</li>";
					}
				echo "</ul></div>";
			}

		?>


		<div class="table-responsive">
            <?php echo form_open(base_url('jobs/service_due_create_job'),'id=service_due_form') ?>
			<table class="table table-hover main-table">
				<thead>
					<tr>
						<th>Address</th>
						<th>Property Manager</th>
						<th>Service Type</th>
						<th>Bookable from</th>
						<th>Active Tenants</th>
						<th style="text-align:center;">View/Edit Tenant Details</th>
						<th class="check_all_td">
								<div class="checkbox" style="margin:0;">
									<input name="chk_all" type="checkbox" id="check-all">
									<label for="check-all">&nbsp;</label>
								</div>
							</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(!empty($list)){
					foreach ($list as $row){ ?>
						<tr class="list-main-tr">
							<td>
								<a href="/properties/property_detail/<?php echo $row->property_id ?>" target="blank">
									<?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}"; ?>
								</a>
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
							<td>
								<?php
								if (!is_null($row->start_date)){
									echo $this->jcclass->isDateNotEmpty($row->start_date)?date('d/m/Y',strtotime($row->start_date)):'';
								}
								?>
							</td>
							<td class="text-right">
								<?php
								echo ($row->new_tenants_count != 0) ? $row->new_tenants_count : '&nbsp;';
								?>
							</td>
							<td style="width:60px;text-align:center;" class="edit_sd_td">


								<input name="property_id" class="property_id" value="<?php echo $row->property_id; ?>" type="hidden">

							<a style="border-bottom:0px;" class="btn_edit_sd" data-toggle="tooltip" title="Edit" href="#"><span class="font-icon font-icon-pencil"></span></a>


							<!-- Fancybox trigger button -->
							<a href="javascript:;" class="fb_trigger" style="display:none;"  data-fancybox data-src="#hidden-content-<?php echo $row->j_id; ?>">Trigger the fancybox</a>


							<!-- Fancy box Start -->
							<div style="display: none;" class="fancy_box_popup" data-ajtID="<?php echo $row->j_id; ?>" id="hidden-content-<?php echo $row->j_id; ?>">
								<div class="row">
                                    <div class="col-lg-12 columns">
                                        <h2>Edit Tenant/Property </h2>
                                        <hr class="gherx_hr" />
								    </div>
								</div>

								<!-- VACANT INFO -->
								<div class="row mb-3">
									<div class="col-lg-12">
										<span style="font-weight: 700; padding-right: 10px;">Is this property currently vacant?</span>
										<div class="form-check form-check-inline">
										<label style="padding-right: 5px;" class="form-check-label" for="inlineRadio1">Yes</label>
										<input class="form-check-input job_vacant" type="radio" name="job_vacant" value="1"/>
										</div>
										<div class="form-check form-check-inline">
										<label style="padding-right: 5px;" class="form-check-label" for="inlineRadio2">No</label>
										<input class="form-check-input job_vacant" type="radio" name="job_vacant" value="0"/>
										<span class="no_job_vacant d-none text-danger font-weight-bold">Please ensure tenant details are correct before proceeding</span>
										</div>
										<div class="job_vacant_div d-none" style="padding-top: 10px;">
											<div class="d-inline-block">
												<input type="text" class="form-control vacant_from_date flatpickr" name="vacant_from_date" placeholder="Vacant From" />
											</div>
											<div class="d-inline-block">
												<input type="text" class="form-control vacant_to_date flatpickr" name="vacant_to_date" placeholder="Vacant To" />
											</div>
											<div class="d-inline-block">
												<div class="checkbox sd_checkbox">								
													<input type="checkbox" class="clear_tenants_chk" name="clear_tenants_chk" id="clear_tenants_chk_<?php echo $row->property_id ?>" value="1" /> 
													<label for="clear_tenants_chk_<?php echo $row->property_id ?>">Clear Old Tenants</label>
												</div>            
											</div>
											<input type="hidden" class="property_vacant" name="property_vacant" />
										</div>
									</div>
								</div>


											<!-- VACANCY -->
											<!-- <div class="row">
												<div class="col-lg-12">
												<h3 class="head_label"><span class="glyphicon glyphicon-map-marker"></span> VACANCY</h3>
												</div>
											</div>										 -->

                                            <!-- TENANTS -->
                                            <div id="tenant-group" class="tenant-group d-none">
												<div class="row">
													<div class="col-lg-12">
													<h3 class="head_label"><span class="font-icon font-icon-users"></span> TENANT DETAILS</h3>
													</div>
												</div>
												<table class="table">
													<tr>
														<td colspan="100%" style="padding: 0px;">
															<div class="tenant_section loader_wrapper_pos_rel">
														<div class="loader_block_v2" style="display: none;"> <div id="div_loader"></div></div>
																<div class="tenants_ajax_box"></div>
															</div>
														</td>
													</tr>
												</table>
											</div>

											<div class="more-information-group d-none">
												<!-- MORE INFO -->
												<div class="row" style="margin-top: 15px;">
													<div class="col-lg-12">
													<h3 class="head_label"><span class="glyphicon glyphicon-map-marker"></span> MORE INFORMATION</h3>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-12 columns">
														<table class="table awts">
															<tr>
																<th>Work Order</th>
																<th>House Alarm Code</th>
																<th>Key Number</th>
															</tr>
															<tr>
																<td>
																	<input name="work_order" class="form-control" type="text" value="<?php echo $row->work_order; ?>">
																</td>
																<td>
																	<input name="house_alarm" class="form-control" type="text"  value="<?php echo $row->alarm_code; ?>">
																</td>
																<td>
																	<input name="key_number" class="form-control" type="text" value="<?php echo $row->key_number; ?>">
																</td>
															</tr>
														</table>
													</div>
												</div>
											</div>


												<!-- ONHOLD INFO -->
												<div class="row on-hold-group d-none" style="margin-top: 15px;">
													<div class="col-lg-12">
														<span style="font-weight: 700; padding-right: 10px;">Does this job need to be placed on hold?</span>
														
														<div class="form-check form-check-inline">
														<label style="padding-right: 5px;" class="onhold form-check-label" for="inlineRadio1">Yes</label>
														<input class="form-check-input" type="radio" name="job_onhold" value="1"/>
														</div>

														<div class="form-check form-check-inline">
														<label style="padding-right: 5px;" class="onhold form-check-label" for="inlineRadio2">No</label>
														<input class="form-check-input" type="radio" name="job_onhold" value="0"/>
														</div>

														<div class="onhold_div d-none" style="padding-top: 10px;">
															<div class="d-inline-block">
																<p>From</p>
															</div>
															<div class="d-inline-block">
																<input type="text" class="form-control onhold_from_date flatpickr" name="onhold_from_date" placeholder="Onhold From" />
															</div>

															<div class="d-inline-block">
																<p>To</p>
															</div>
															<div class="d-inline-block">
																<input type="text" class="form-control onhold_to_date flatpickr" name="onhold_to_date" placeholder="Onhold To" />
															</div>
														</div>
														
													</div>
												</div>




                                                <!-- UPDATE DETAILS BUTTON -->
                                                <div class="row">
                                                    <div class="col-lg-12 columns txt-right">

													<input type='hidden' name='hid_prop_id'  value='<?php echo $row->j_property_id; ?>' />
													<input type='hidden' name='hid_job_id'  value='<?php echo $row->j_id ?>' />



                                                        <button type="button" class="btn update_details_btn" id="btn_submit_sd_update_details">Update Details</button>
                                                    </div>
                                                </div>



							</div>
							<!-- Fancy box END -->


							</td>
							<td class="nlm_chkbox_td">	<div class="checkbox sd_checkbox">
								<input type='hidden' name='j_id[]' class='j_id' value='<?php echo $row->j_id ?>' />
								<input type='hidden' name='prop_id[]' class='prop_id' value='<?php echo $row->j_property_id ?>' />
								<input type='hidden' name='serv_id[]' class='serv_id' value='<?php echo $row->j_service; ?>' />
								<input type='hidden' name='sel_job[]' class="sel_job"  value="0" />
								<input type='hidden' name='prop_state[]' class="prop_state"  value="<?php echo $row->p_state ?>" />
								<input type='hidden' name='retest_date[]' class="retest_date"  value="<?php echo $row->retest_date ?>" />
								<input value="<?php echo $row->j_property_id; ?>" data-propid="<?php echo $row->j_property_id; ?>" type="checkbox" class="chkbox" name="chkbox[]" id="check-<?php echo $row->j_id; ?>"> <label for="check-<?php echo $row->j_id; ?>">&nbsp;</label></div>
							</td>
						</tr>
                                    <tr class="abudakar">


                                        <td colspan="7" style="padding:0!important;margin:0;height:0!important;border:0px!important;padding-left:15px!important;padding-right:15px!important;">

                                        <!-- PROPERTY VACANT MORE INFO -->

                                               <!-- DISABLE FOR NOW
												   <div class="row sd_prop_vacant_more_info" style="display:none; border-top:1px solid #fff;">
                                                     <div class="col-lg-2 columns">
                                                         <label>Currently Vacant?</label>
                                                               <select id="prop_vacant" class="form-control" name="prop_vacant[]" style="width: 100%; margin: 0;">
                                                                    <option value="0" >No</option>
                                                                    <option value="1">Yes</option>
                                                                </select>
                                                     </div>
                                                     <div class="col-lg-10 columns prop_vacant_more_info" style="display:none;">
                                                            <div class="row">
                                                                <div class="col-lg-3 columns">
                                                                                                                                            <label>Vacant from</label>
                                                                    <div class="input-group date flatpickr" data-wrap="true">

                                                                        <input class="form-control flatpickr-input vacant_from" name="vacant_from[]" id="vacant_from"  data-input="" readonly="readonly" type="text" >
                                                                        <span class="input-group-append" data-toggle="">
                                                                                                                                                    <span class="input-group-text">
                                                                                                                                                        <i class="font-icon font-icon-calend"></i>
                                                                                                                                                    </span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 columns">
                                                                    <label>Vacant to</label>
                                                                        <div class="input-group date flatpickr" data-wrap="true">
                                                                        <input class="form-control flatpickr-input" name="vacant_to[]" id="vacant_to" data-input="" readonly="readonly" type="text" >
                                                                        <span class="input-group-append" data-toggle="">
                                                                                                                                                    <span class="input-group-text">
                                                                                                                                                        <i class="font-icon font-icon-calend"></i>
                                                                                                                                                    </span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 columns">
                                                                        <label>Comments</label>
                                                                        <input id="job_comments" type="text" name="job_comments[]" class="form-control">

                                                                </div>

                                                         </div>
                                                     </div>
                                                </div>
												-->



                               </td>

                    </tr>
					<?php
					}
				}else{
					echo "<tr><td colspan='7'>There are no properties due for service</td></tr>";
				}
					?>
					<?php if(!empty($list)){ ?>
							<tr>
								<td colspan="7">
								<div class="sd_buttons" style="display:none;" >
									<input type="hidden" name="sd_create_job_flag" value="0">
									<input type="hidden" name="sd_nlm_flag" value="0">
									<!-- <button type="button" class="btn btn-inline btn-danger" id="btn_nlm">NO LONGER MANAGE</button> -->
									<button data-val="0" id="btn_no_longer_managed" type="button" class="btn btn-inline btn-danger">No Longer Manage?</button>
									<button type="button" class="btn btn-inline btn-success" id="btn_create_job"><?php echo ( $agency_row->allow_upfront_billing == 1 )?'Create Subscription Renewal':'CREATE JOB'; ?></button>
								</div>
								</td>
							</tr>
					<?php } ?>
					<tr>
                        <td id="nlm_td_box" colspan="8" style="padding: 0px;height:0px!important;">

                         <div id="nlm_td_box_toggle" style="display: none;">
                             <table class="no-border" id="nlm_box" style="width:100%">
                            <tr>
                              <td style="padding-top:15px;padding-bottom: 15px;">
                                  <table class="no-border nlm_box_warning" style="width:100%">
                          <tr>
                            <td style="padding:0px">
<div class="txt-center alert alert-danger alert-close alert-dismissible fade show ">
    <i class="font-icon font-icon-warning red"></i>&nbsp;&nbsp;Warning - If you proceed, All Jobs and Services will be Cancelled, and this property will be Archived </div>
                          </td>
                        </tr>
                        </table>

                                  <?php echo form_open(base_url('/properties/no_longer_managed'),'id=nlm_form'); ?>
                            <table class="nlm_box_fields nlm_div"  style="width:100%">
                            <thead>
                                 <tr>

                        <th>"No Longer Managed" From *</th>
                        <th>"No Longer Managed" Reason *</th>
						<th class="other_reason_elem">Other Reason</th>
                        <th>&nbsp;</th>
                    </tr>
                                </thead>
                                <tbody>
                                <tr>

                                    <td>
                                    <div class="form-group">
                                        <div class="input-group date flatpickr" data-wrap="true" >
                                            <input data-validation="[NOTEMPTY]" data-validation-label="No Longer Managed From" data-input  type="text" class="form-control" name="nlm_from" id="nlm_from">
                                            <span class="input-group-append" data-toggle >
                                                    <span class="input-group-text"><i class="font-icon font-icon-calend"></i></span>
                                            </span>
                                        </div>
                                    </div>
                                    </td>
                                    <td><div class="form-group">										
										<select name="reason_they_left" class="form-control reason_they_left">
                                            <option value="">---Select Reason---</option>       
                                            <?php
                                            // get leaving reason                                                
                                            $lr_sql = $this->db->query("
                                            SELECT *
                                            FROM `leaving_reason`
                                            WHERE `active` = 1
                                            AND `display_on` IN(3,5)
                                            ORDER BY `reason` ASC
                                            ");   
                                            foreach( $lr_sql->result() as $lr_row ){ ?>
                                                <option value="<?php echo $lr_row->id; ?>"><?php echo $lr_row->reason; ?></option> 
                                            <?php
                                            }                                         
                                            ?> 
                                            <option value="-1">Other</option> 
                                        </select>
									</div></td>
									<td class="other_reason_elem">
                                        <div class="form-group">                                        
                                            <textarea class="form-control addtextarea other_reason" name="other_reason" placeholder="Other Reason"></textarea>
                                        </div>
                                    </td>
                                    <td><div class="form-group"><button type="button" style="margin: 0;" id="btn_nlm" class="btn btn-sm btn-inline">Proceed</button></div></td>
                                    <!-- <td><div class="form-group"><button type="button" style="margin: 0;" id="btn_no_longer_managed_go" class="btn btn-sm btn-inline">Proceed</button></div></td> -->
                                    </tr>
                                </tbody>
                            </table>
                                <?php echo form_close(); ?>
                                </td>
                              </tr>
                            </table>

                            </div>


                        </td>
                    </tr>
				</tbody>
			</table>
            </form>

		</div>

		<nav aria-label="Page navigation example" style="text-align:center">
			<?php echo $pagination; ?>
		</nav>

		<div class="pagi_count"><?php echo $pagi_count; ?></div>


	</div><!--.box-typical-body-->


</section><!--.box-typical-->

<style>
.other_reason_elem{
	display: none;
}
</style>
<!-- JAVASCRIPT START HERE.... -->
<script type="text/javascript">

	 // no longer manager toggle tweak
	 $('#btn_no_longer_managed').on('click',function(e){
            e.preventDefault();
            var dataVal = $(this).data('val');
            var btnVal = $(this).html();
            $('#nlm_td_box_toggle').slideToggle(function(){
                if(btnVal=="No Longer Manage?"){
                    $('#btn_no_longer_managed').html('Cancel').attr('data-val',1);
                }else{
                    $('#btn_no_longer_managed').html('No Longer Manage?').attr('data-val',0);
                }
            });
        });


	 jQuery(document).ready(function() {

		// datepicker
		jQuery('.flatpickr').flatpickr({
			dateFormat: "d/m/Y"
		});

		$("input[name='job_onhold']").click(function(){
			//alert('You clicked radio111!');
			//$("onhold_div").removeClass("intro");
			var job_vacant = $("input[name='job_onhold']:checked").val();
			if(job_vacant == 1){
				$(".onhold_div").removeClass("d-none");
			}else{
				$(".onhold_div").addClass("d-none");
			}
		});

		$("input[name='job_vacant']").click(function(){
			var job_vacant = $("input[name='job_vacant']:checked").val();
			if(job_vacant == 1){
				$(".no_job_vacant").addClass("d-none");
				$(".tenant-group").addClass("d-none");
				$(".more-information-group").removeClass("d-none");
				$(".on-hold-group").addClass("d-none");
				$(".job_vacant_div").removeClass("d-none");
			}else{
				$(".no_job_vacant").removeClass("d-none");
				$(".tenant-group").removeClass("d-none");
				$(".more-information-group").removeClass("d-none");
				$(".on-hold-group").removeClass("d-none");
				$(".job_vacant_div").addClass("d-none");
			}
		});


		// vacant toggle
		jQuery(".currently_vacant_toggle").click(function(){

			var vacant_toggle_dom = jQuery(this);
			var fancy_box_popup = vacant_toggle_dom.parents(".fancy_box_popup");
			var vacant_toggle_btn_inner_dom = vacant_toggle_dom.find(".btn_inline_text");
			var vacant_toggle_btn_txt = vacant_toggle_btn_inner_dom.text();
			var orig_btn_txt = 'Currently Vacant';			

			if( vacant_toggle_btn_txt == orig_btn_txt ){ // show

				vacant_toggle_btn_inner_dom.text("Cancel"); // update button text to cancel
				fancy_box_popup.find(".property_vacant").val(1);
				
				fancy_box_popup.find(".vacant_div").removeClass('d-none');
				fancy_box_popup.find(".vacant_div").addClass('d-inline-block');

			}else{

				vacant_toggle_btn_inner_dom.text(orig_btn_txt); // update button text to back to orig
				fancy_box_popup.find(".property_vacant").val(0);

				// clear field
				fancy_box_popup.find(".vacant_from_date").val('');
				fancy_box_popup.find(".vacant_to_date").val('');
				fancy_box_popup.find(".clear_tenants_chk").prop("checked",false);

				fancy_box_popup.find(".vacant_div").addClass('d-none');
				fancy_box_popup.find(".vacant_div").removeClass('d-inline-block');

			}


		});

		 //autorenew timer
		 AutoRenewCountdownTimer();

		 //select2
		 $(".select2-photo").not('.manual').select2({
			templateSelection: select2Photos,
			templateResult: select2Photos
		 })


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

			//click all tick box
			$('#check-all').on('change',function(){
				var obj = $(this);
				var isChecked = obj.is(':checked');
				var divbutton = $('.sd_buttons');
				if(isChecked){
					divbutton.show();
					$('.chkbox').prop('checked',true);
					$('.list-main-tr').addClass('selected');
					$('.list-main-tr').find('.sel_job').val(1);
				}else{
					divbutton.hide();
					$('.chkbox').prop('checked',false);
					$('.list-main-tr').removeClass('selected');
					$('.list-main-tr').find('.sel_job').val(0);
				}
			})

		 	//SERVICE DUE CHECKBOX TWEAK
			$('.chkbox').on('change',function(){

				var obj = $(this);
				var prop_id = obj.data('propid');

				var prop_vacant = obj.parents('.list-main-tr').next('.abudakar').find('#prop_vacant');
				var vacant_from = obj.parents('.list-main-tr').next('.abudakar').find('#vacant_from');
				var vacant_to = obj.parents('.list-main-tr').next('.abudakar').find('#vacant_to');
				var comments = obj.parents('.list-main-tr').next('.abudakar').find('#job_comments');


				if(obj.is(':checked')){
					obj.parents('.list-main-tr').addClass('selected');
					obj.parents('.list-main-tr').next('.abudakar').find('.sd_prop_vacant_more_info').slideDown();
					$('.sd_buttons').show();
					obj.parents('.list-main-tr').find('.sel_job').val(1);
				}else{
					obj.parents('.list-main-tr').removeClass('selected');
					obj.parents('.list-main-tr').next('.abudakar').find('.sd_prop_vacant_more_info').slideUp();
					obj.parents('.list-main-tr').find('.sel_job').val(0);

					if($('[name="chkbox[]"]:checked').length==0){
						$('.sd_buttons').hide();
					}

					// clear fields
					//prop_vacant.val("0");
					vacant_from.val("");
					vacant_to.val("");
					comments.val("");
				}

			});



			//Prop Vacant Toggle Tweak
			$(document).on('change','#prop_vacant',function(){
				var obj = $(this);

				if(obj.val()==1){
					obj.parents('.sd_prop_vacant_more_info').find('.prop_vacant_more_info').show();
				}else{
					obj.parents('.sd_prop_vacant_more_info').find('.prop_vacant_more_info').hide();
				}
			});



		 	//EDIT SERVICE DUE
			$('.btn_edit_sd').on('click',function(e){

				e.preventDefault();
				var obj = $(this);
				var fb_trigger = obj.parents("td.edit_sd_td:first").find(".fb_trigger");
				var tenants_block = obj.parents("td.edit_sd_td:first").find(".tenants_ajax_box");
				var property_id = obj.parents("td.edit_sd_td:first").find(".property_id").val();
				var loader = obj.parents("td.edit_sd_td:first").find(".loader_block_v2");

				// clear all tenants div
				$('.tenants_ajax_box').empty();

				loader.show();

				//load tenants ajax box (via ajax)
				tenants_block.load('/properties/tenants_ajax', {
						prop_id: property_id
					}, function(response, status, xhr) {

						loader.hide();
						$('[data-toggle="tooltip"]').tooltip(); //init tooltip
						phone_mobile_mask(); //init phone/mobile mask
						mobile_validation(); //init mobile validation
						phone_validation(); //init phone validation
						add_validate_tenant(); //init new tenant validation

						//fb_trigger.click(); // trigger fancybox popup
					}

				);

				fb_trigger.click(); // trigger fancybox popup

			})

			


			//SERVICE DUE UPDATE TENANTS/PROPERTY DETAILS
			$(document).on('click','.update_details_btn',function(e){
				var count_err = 0;
				var obj = $(this);
				var propId = obj.parents('.fancy_box_popup').find('input[name="hid_prop_id"]').val();
				var job_id = obj.parents('.fancy_box_popup').find('input[name="hid_job_id"]').val();
				var work_order = obj.parents('.fancy_box_popup').find('input[name="work_order"]');
				var job_vacant = $("input[name='job_vacant']:checked").val();
				var key_number = obj.parents('.fancy_box_popup').find('input[name="key_number"]');
				var alarm_code = obj.parents('.fancy_box_popup').find('input[name="house_alarm"]');

				var property_vacant_hid = obj.parents('.fancy_box_popup').find('input[name="property_vacant"]').val();
				var vacant_from_date = obj.parents('.fancy_box_popup').find('input[name="vacant_from_date"]').val();
				var vacant_to_date = obj.parents('.fancy_box_popup').find('input[name="vacant_to_date"]').val();

				var job_onhold = $("input[name='job_onhold']:checked").val();
				var onhold_from_date = obj.parents('.fancy_box_popup').find('input[name="onhold_from_date"]').val();
				var onhold_to_date = obj.parents('.fancy_box_popup').find('input[name="onhold_to_date"]').val();

				var clear_tenants_dom = obj.parents('.fancy_box_popup').find('input[name="clear_tenants_chk"]');	
				var clear_tenants = ( clear_tenants_dom.prop("checked") == true )?1:0;			

				//var prop_vacant = ( property_vacant_hid == 1 && vacant_from_date != "" )?1:0;

				if( job_vacant == 1 && vacant_from_date == '' && vacant_to_date == '' ){
					count_err = 1;
					swal('Error','Vacant from and to date is required','error');
				}

				if( job_onhold == 1 && onhold_from_date == '' && onhold_to_date == '' ){
					count_err = 1;
					swal('Error','Onhold from and to date is required','error');
				}

				if(count_err == 0){
					swal(
						{
							title: "",
							text: "Are you sure all details are correct?",
							type: "warning",
							showCancelButton: true,
							confirmButtonClass: "btn-success",
							confirmButtonText: "Yes, Update",
							cancelButtonClass: "btn-danger",
							cancelButtonText: "No, Cancel!",
							closeOnConfirm: false,
							closeOnCancel: true,
						},
						function(isConfirm){
							if(isConfirm){
								
								jQuery.ajax({
								type: "POST",
								url: "<?php echo base_url('/jobs/job_ajax_update_details') ?>",
								dataType: 'json',
								data: {
									prop_id: propId,
									job_id:job_id,
									agency_id: <?php echo $this->session->agency_id ?>,
									alarm_code: alarm_code.val(),
									key_number: key_number.val(),
									work_order: work_order.val(),
									job_vacant: job_vacant,
									prop_vacant: property_vacant_hid,
									start_date: vacant_from_date,
									due_date: vacant_to_date,
									job_onhold: job_onhold,
									onhold_start_date: onhold_from_date,
									onhold_end_date: onhold_to_date,
									page_type: 'service_due',
									clear_tenants: clear_tenants
								}
								}).done(function(data){
									if(data.status){
										swal({
											title:"Success!",
											text: "Details Updated",
											type: "success",
											showCancelButton: false,
											confirmButtonText: "OK",
											closeOnConfirm: false,

										},function(isConfirm){

											if(isConfirm){

												alarm_code.val(data.alarm_code);
												key_number.val(data.key_number);
												work_order.val(data.work_order);
												swal.close();
												$.fancybox.close();

												// added redirect requested by ben
												window.location='/jobs/service_due';

											}

										});
									}else{
									// swal('Error','Details already updated','error');
									swal.close();
										$.fancybox.close();
									}
								});
							}

						}
					);
				}
			});



			//CREATE JOB

			function createJobConfirm(){
				swal(
					{
						title: "",
						text: "This will create a job for selected properties, are you sure you want to proceed?",
						type: "warning",
						showCancelButton: true,
						confirmButtonClass: "btn-success",
						confirmButtonText: "Yes, Proceed",
						cancelButtonClass: "btn-danger",
						cancelButtonText: "No, Cancel!",
						closeOnConfirm: false,
						closeOnCancel: true,
						},function(isConfirm){
							if(isConfirm){

								//set relevant flag to capture post
								$('input[name="sd_create_job_flag"]').val(1);
								$('input[name="sd_nlm_flag"]').val(0);

								$('#service_due_form').submit();

							}
						}
					);
			}

			$('#btn_create_job').on('click',function(){
				var obj = $(this);
				var ischecked = $('.chkbox:checked');

				if(ischecked.length>0){
					createJobConfirm();

					/*$('.chkbox').each(function(index, value){
						var check = $(this).is(':checked');
						if(check){
							var prop_vacant = $(this).parents('.list-main-tr').next('.abudakar').find('#prop_vacant').val();
							var vacant_from = $(this).parents('.list-main-tr').next('.abudakar').find('.vacant_from').val();
							if(prop_vacant==1){
								if(vacant_from==""){
									swal('','Vacant from must not be empty','error');
									return false;
								}else{
									createJobConfirm();
									return false;
								}
							}else{
								createJobConfirm();
								return false;
							}
						}


					});*/

				}else{
					swal('','No Items Selected','error');
				}

			})



			//NO LONGER MANAGE
			$('#btn_nlm').on('click',function(){
					var obj = $(this);
					var ischecked = $('.chkbox:checked');

					var nlm_div = obj.parents(".nlm_div");
					var reason_they_left = nlm_div.find(".reason_they_left").val();
					var other_reason = nlm_div.find(".other_reason").val();
					var error = '';

					// validation
					if( reason_they_left == '' ){
						error += "'Reason They Left' is required\n";
					}else{
						if( reason_they_left == -1 && other_reason == '' ){
							error += "'Other Reason' is required\n";
						}
					}

					if( error != "" ){ // error

						swal('', error, 'error'); 

					}else{
						
						if(ischecked.length>0){
							swal(
									{
										title: "",
										text: "Are you sure you want to remove this property from servicing?",
										type: "warning",
										showCancelButton: true,
										confirmButtonClass: "btn-success",
										confirmButtonText: "Yes, Remove",
										cancelButtonClass: "btn-danger",
										cancelButtonText: "No, Cancel!",
										closeOnConfirm: false,
										closeOnCancel: true,
									},function(isConfirm){
										if(isConfirm){

											//set relevant flag to capture post
											$('input[name="sd_nlm_flag"]').val(1);
											$('input[name="sd_create_job_flag"]').val(0);

											$('#service_due_form').submit();

										}
									}
							);
						}else{
							swal('','No Items Selected','error');
						}						

					}
					
			})

			
			// reason show/hide script			
			jQuery(".reason_they_left").change(function(){

				var reason_they_left_dom = jQuery(this);
				var nlm_div = reason_they_left_dom.parents(".nlm_div");
				var reason_they_left =  reason_they_left_dom.find("option:checked").val();

				if( reason_they_left == -1 ){
					nlm_div.find(".other_reason_elem").show();
				}else{
					nlm_div.find(".other_reason_elem").hide();
				}            

			});


	}); //doc ready end here...

	function AutoRenewCountdownTimer(){

	var d = new Date();

	// auto renew on 1st of month
	var auto_renew_day = 15;
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

</script>
