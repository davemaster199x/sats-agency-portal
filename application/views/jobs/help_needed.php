

<style>
	.service_sec{

	}
	.nlm_reason_div,
	.other_reason{
		display: none;
	}
</style>
<section class="box-typical box-typical-padding">
<h5 class="m-t-lg with-border"><a href="/jobs/help_needed"><?php echo $title; ?></a></h5>
	<!-- Header -->
	<header class="box-typical-header">
		<div class="box-typical box-typical-padding">
		<?php
		$form_attr = array(
			'id' => 'jform'
		);
		echo form_open('jobs/help_needed',$form_attr);
		?>
			<div class="form-groupsss row">

                <div class="col-md-8">
                <div class="row">
				<div class="col-md-3 columns">
					<label for="exampleSelect" class="form-control-label">Property Manager</label>

						<select name="pm_id" class="form-control field_g2 select2-photo">
							<option value="">---</option>
							<option <?php  echo ( $this->input->get_post('pm_id') == '0' )?'selected="selected"':''; ?> value="0" data-photo="<?php echo $this->config->item('photo_empty'); ?>">No PM assigned</option>
							<?php
							foreach( $pm_filter->result() as $row ){ ?>
								<option data-photo="<?php echo ($row->photo!="")?$this->config->item('user_photo')."/".$row->photo:$this->config->item('photo_empty'); ?>" value="<?php echo $row->properties_model_id_new; ?>" <?php echo ( $row->properties_model_id_new == $this->input->get_post('pm_id') )?'selected="selected"':''; ?>><?php echo "{$row->fname} {$row->lname}"; ?></option>
							<?php
							}
							?>
						</select>

				</div>
				<div class="col-md-3 columns">
					<label class="form-control-label">Search</label>
					<div>

							<input type="text" class="form-control" name="search" placeholder="Text" value="<?php echo $this->input->get_post('search'); ?>" />

					</div>
				</div>

				<div class="col-md-3 columns">
					<label class="form-control-label">&nbsp;</label>
					<div>
						<button type="submit" class="btn btn-inline">Search</button>
					</div>
				</div>
                    <div class="col-md-3 columns">&nbsp;</div>
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
						<th>Reason</th>
						<th>Edit</th>
						<!--<th>Update</th>	-->
					</tr>
				</thead>
				<tbody>
					<?php
					if(!empty($escalatedJobs)){
					foreach ($escalatedJobs as $row){
						 $fullAddress = "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
						?>
						<tr>
							<td>

								<a href="/properties/property_detail/<?php echo $row->property_id ?>" target="blank">
									<?php echo $fullAddress; ?>
								</a>
							</td>
							<td>
							<?php
							if( isset($row->properties_model_id_new) && $row->properties_model_id_new != 0 ){
								echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
								echo "{$row->properties_model_fname} {$row->properties_model_lname}";
							}
							?>
							</td>
							<td>
								<?= Alarm_job_type_model::icons($row->ajt_id); ?>
							</td>
							<td>
								<?php echo $row->reason; ?>
							</td>
							<td class="edit_sd_td">

								<!-- button tweak -->
								<?php

								$is_verify_ten = 0;
								$is_en = 0;
								$is_response = 0;
								$btn_txt = '';
								$job_reason_id = $row->escalate_job_reasons_id;

								if($job_reason_id==1){
									$is_verify_ten = 1;
									$is_response = 1;
									$btn_txt = 'Update Property';
								}else if($job_reason_id == 2){
									$is_response = 2;
									$btn_txt = 'Response Required';
								}else if($job_reason_id == 10){
									$is_response = 10;
									$btn_txt = 'Response Required';
								}else if($job_reason_id == 8){ //unresponsive
									$is_response = 8;
									$btn_txt = 'Update Property';
								}else if($job_reason_id == 3){ //other
									$is_response = 3;
									$btn_txt = 'Update Property';
								}else if($job_reason_id == 6){ //Verify NLM
									$is_response = 6;
									$btn_txt = 'Update Property';
								}else if( $job_reason_id ==7){
									$is_response = 7;
									$btn_txt = 'Update Property';
								}else if($job_reason_id == 5){ //Short Term Rental
									$is_response = 5;
									$btn_txt = 'Response Required';
								}else if($job_reason_id == 9){ //needs agent to verify
									$is_response = 9;
									$btn_txt = 'Response Required';
								}else if($job_reason_id == 11){ 
									$is_response = 11;
									$btn_txt = 'Update Property';
								}
								?>


								<input type="hidden" name="job_id" value="<?php echo $row->j_id; ?>" />
								<input name="property_id" class="property_id" value="<?php echo $row->property_id; ?>" type="hidden">
								<input type="hidden" class="is_response" value="<?php echo $is_response ?>">


								<?php if($job_reason_id == 6){ ?>
										<button type="button" class="btn btn-sm btn-escalate-update-now" data-nlm_val="yes">Yes</button>
										<button type="button" class="btn btn-sm btn-danger verify_nlm_btn" data-nlm_val="no">No</button>
								<?php }else if($job_reason_id == 5){ ?>
										<button data-fancybox data-src="#holiday-rental-hidden-content-<?php echo $row->j_id; ?>" type="button" class="btn btn-sm btn_holiday_rental_yes" value="1">Yes</button>
										<button type="button" class="btn btn-sm btn-escalate-update-now" value="0">No</button>
								<?php }else{
									    // do not show button on "Other - See Notes" reason										
										if($btn_txt!="" && $job_reason_id != 3 ){ ?>
											<button type="button" class="btn-escalate-update-now btn btn-sm" ><?php echo $btn_txt; ?></button>
										<?php } ?>
								<?php } ?>



							<!-- Fancybox trigger button -->
							<a href="javascript:;" class="fb_trigger" style="display:none;" data-options='{"modal":"true"}' data-fancybox data-src="#hidden-content-<?php echo $row->j_id; ?>">Trigger the fancybox</a>


							<!-- Fancy box Start -->

							<?php if($is_response == 0){ ?>
									<div id="hidden-content-<?php echo $row->j_id; ?>" class="escalate_response_div fancy_box_popup" style="display:none;">
										<div class="row">

											<div class="col-lg-12 columns">
												<h2>Response</h2>
												<hr class="gherx_hr" />
								            </div>

										</div>
										<div class="row">
											<div class="col-lg-12">
											<textarea name="escalate_response_txt" class="form-control escalate_response_txt" ></textarea>
											</div>
											<div class="col-lg-12 txt-right">
												<input type='hidden' name='hid_prop_id'  value='<?php echo $row->property_id; ?>' />
												<input type='hidden' name='hid_job_id'  value='<?php echo $row->j_id ?>' />
												<input type="hidden" class="is_response" value="<?php echo $is_response ?>">
												<button style="margin-top:20px;" type="button" class="btn response-btn">Submit</button>

											</div>
										</div>
									</div>
							<?php }else if($is_response==2){ ?>
									<!-- OLD JOBS FANCYBOX -->

									<div id="hidden-content-<?php echo $row->j_id; ?>" class="escalate_response_div fancy_box_popup" style="display:none;">
										<div class="row">

											<div class="col-lg-12 columns">
												<h2>Response</h2>
												<hr class="gherx_hr" />
								            </div>

										</div>
										<div class="row">
											<div class="col-lg-12 columns">
												<label style="margin-bottom:10px;">To service this old job…</label>
								            </div>
											<div class="col-lg-12">
												<div class="radio">
													<input class="form-control old_job_radio" type="radio" name="old-job" id="old-job1-<?php echo $row->j_id; ?>" value="EN Property">
													<label for="old-job1-<?php echo $row->j_id; ?>">Entry Notice Property </label>
												</div>
												<div class="radio">
													<input class="form-control old_job_radio" type="radio" name="old-job" id="old-job2-<?php echo $row->j_id; ?>" value="Agent will contact">
													<label for="old-job2-<?php echo $row->j_id; ?>">Agent will contact </label>
												</div>
												<div class="radio">
													<input class="form-control old_job_radio" type="radio" name="old-job" id="old-job3-<?php echo $row->j_id; ?>" value="Keep contacting">
													<label for="old-job3-<?php echo $row->j_id; ?>">Keep contacting </label>
												</div>
												<div class="radio">
													<input class="form-control old_job_radio" type="radio" name="old-job" id="old-job4-<?php echo $row->j_id; ?>" value="NLM">
													<label for="old-job4-<?php echo $row->j_id; ?>">We no longer manage this property </label>
												</div>
												<!--<div class="radio">
													<input class="form-control old_job_radio" type="radio" name="old-job" id="old-job4" value="Other">
													<label for="old-job4">Other </label>
												</div>
												<div><textarea style="display:none;" name="old-job-other" class="form-control old-job-other"></textarea></div> -->
											</div>
											<div class="col-lg-12 txt-right">
												<input type='hidden' name='hid_prop_id'  value='<?php echo $row->property_id; ?>' />
												<input type='hidden' name='hid_job_id'  value='<?php echo $row->j_id ?>' />
												<input type="hidden" class="is_response" value="<?php echo $is_response ?>">
												<button data-fancybox-close="" style="margin-top:20px;" type="button" class="btn btn-danger btn_close_aaa">Cancel</button>
												<button style="margin-top:20px;" type="button" class="btn response-btn-old-job">Submit</button>

											</div>
										</div>
									</div>

							<?php }else if($is_response==8 || $is_response==3 || $is_response==6 || $is_response==5 || $is_response == 1 || $is_response == 11){ //Unresponsive|Other|NLM | Holida Rental| verify tenant details ?>

								<!-- UPDATE PROPERTY FANCY BOX -->
								<div style="display: none;" class="fancy_box_popup" data-ajtID="<?php echo $row->j_id; ?>" id="hidden-content-<?php echo $row->j_id; ?>">

									<div class="row">
										<div class="col-lg-12 columns">
											<h2>Edit Tenant/Property Details </h2>
											<hr class="gherx_hr" />
										</div>
									</div>

									<!-- TENANTS -->
									<div class="row">
										<div class="col-lg-12">
										<h3 class="head_label"><span class="font-icon font-icon-users"></span> TENANT DETAILS</h3>
										</div>
									</div>
									<table class="table">

										<tr>
											<td colspan="100%" style="padding: 0px;">
												<div class="tenant_section loader_wrapper_pos_rel">
											<div class="loader_block_v2_v2" style="display: none;"> <div id="div_loader"></div></div>
													<div class="tenants_ajax_box"></div>

													<!-- inline add tenant field for help_needed only -->
													<div class="new_tenant_inline_section">
														<form id="new_tenants_form">
															<button id="plus_new_tenant_btn" class="btn btn-inline btn-danger-outline" type="button">
																<span class="glyphicon glyphicon-plus"></span> <span class="btn_inline_text">Tenant</span>
															</button>
															<div class="new_tenant_fields_box" style="display:none;">
																<table class="table vpd_table">
																	<thead>
																		<tr>
																			<th>First Name</th>
																			<th>Last Name</th>
																			<th>Mobile</th>
																			<th>Landline</th>
																			<th>Email</th>
																			<th>&nbsp;</th>
																		</tr>
																	</thead>
																	<tbody>
																		<tr class="tenants-row">
																			<td>
																				<div class="form-group"><input placeholder="First Name" data-validation="[NOTEMPTY]" data-validation-label="First Name" type="text" class="form-control new_tenant_fname" name="new_tenant_fname[]"></div>
																			</td>
																			<td>
																				<div class="form-group"><input placeholder="Last Name" type="text" class="form-control new_tenant_lname" name="new_tenant_lname[]"></div>
																			</td>
																			<td>
																				<div class="form-group"><input  type="text" class="form-control tenant_mobile new_tenant_mobile" name="new_tenant_mobile[]"></div>
																			</td>
																			<td>
																				<div class="form-group"><input type="text" class="form-control phone-with-code-area-mask-input new_tenant_landline" name="new_tenant_landline[]"></div>
																			</td>
																			<td>
																				<div class="form-group"><input placeholder="Email"  type="text" class="form-control new_tenant_email" name="new_tenant_email[]"></div>
																			</td>
																			<td>&nbsp;</td>
																		</tr>
																		<tr class="add_new_tenant_plus_btn">
																			<td colspan="6">
																			<div class="add_tenan_new_row_section">
																				<a class="add_tenan_new_row_btn btn btn-sm" href="#" class="btn btn-sm"><span class="glyphicon glyphicon-plus"></span> Tenant</a>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
															</div>
														</form>
													</div>
													<!-- inline add tenant field for help_needed only -->

												</div>
											</td>
										</tr>

									</table>

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

									<!-- Vaccant Section -->
									<div class="row" style="margin-top: 15px;">
										<div class="col-lg-12">
											<h3 class="head_label"><span class="glyphicon glyphicon-map-marker"></span> PROPERTY VACANT</h3>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-6 columns">
											<div class="row">
												<div class="col-lg-4 columns">
													<input name="vacant_from" type="text" class="form-control flatpickr flatpickr-input vacant_from" placeholder="Vacant From *" data-date-format="d/m/Y">
												</div>
												<div class="col-lg-4 columns">
													<input name="vacantl_till" type="text" class="form-control flatpickr flatpickr-input vacantl_till" placeholder="Vacant Till" data-date-format="d/m/Y">
												</div>
											</div>
										</div>
									</div>

									<!-- Current Service Section -->
									<div class="row" style="margin-top: 20px;">
										<div class="col-lg-12">
											<h3 class="head_label"><span class="glyphicon glyphicon-map-marker"></span> CURRENT SERVICE</h3>
										</div>
									</div>
									<div class="row service_sec">
										<div class="col-lg-4 columns">
											<?php echo $row->ajt_type; ?> &nbsp;&nbsp;&nbsp;&nbsp; <?= Alarm_job_type_model::icons($row->ajt_id); ?>
											&nbsp;&nbsp;&nbsp;&nbsp;
										</div>
										<div class="col-lg-8 columns"> <a class="btn btn-sm" href="/properties/property_detail/<?php echo $row->property_id; ?>">Update</a> </div>
									</div>
									<!-- Current Service Section end -->

									<!-- NLM Section -->
									<div class="row" style="margin-top: 15px;">
										<div class="col-lg-12">
											<h3 class="head_label"><span class="glyphicon glyphicon-map-marker"></span> NO LONGER MANAGE?</h3>
										</div>
									</div>
									<div class="row nlm_div">
										<div class="col-lg-4">We no longer manage this property.&nbsp;&nbsp;</div>
										<div class="col-lg-8 columns">
										
										<button class="btn btn-sm btn-warning mb-2 nlm_process_btn">Proceed</button>

										<div class="nlm_reason_div">

											<select name="reason_they_left" class="form-control mb-2 reason_they_left">
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

											<textarea class="form-control addtextarea mb-2 other_reason" name="other_reason" placeholder="Other Reason"></textarea>

											
											<button class="btn btn-sm btn-success verify_nlm_btn_v2" data-nlm_val="no">Submit</button>&nbsp;
										

										</div>										

										</div>
									</div>

									<!-- UPDATE DETAILS BUTTON -->
									<div class="row">

										<div class="col-lg-12 columns txt-right">

											<input type='hidden' name='hid_prop_id'  value='<?php echo $row->property_id; ?>' />
											<input type='hidden' name='hid_job_id'  value='<?php echo $row->j_id ?>' />
											<input type="hidden" name="is_verify_ten" class="is_verify_ten" value="<?php echo $is_verify_ten; ?>" />
											<input type="hidden" name="prop_vacant" value="">
											<input type="hidden" class="is_response" value="<?php echo $is_response ?>">

											<?php

											if($row->new_tenants_count != 0){
												echo "<input type='hidden' name='have_dontHave_tenants' value='1'>";
											}else{
												echo "<input type='hidden' name='have_dontHave_tenants' value='0'>";
											}

											?>

											<button data-fancybox-close="" style="margin-top:20px;" type="button" class="btn btn-danger btn_close_aaa">Cancel</button>
											<button data-full_address="<?php echo $fullAddress; ?>" style="margin-top:20px;" type="button" class="btn btn-success btn_submit_escalate_update_details_v2">Complete</button>
										</div>
									</div>

								</div>


									<!-- FOR Short Term Rental YES BUTTON -->
									<?php if($is_response == 5){
									?>

										<div id="holiday-rental-hidden-content-<?php echo $row->j_id; ?>" class="escalate_response_div fancy_box_popup_2" style="display:none;">
											<div class="row">

												<div class="col-lg-12 columns">
													<h2>Response</h2>
													<hr class="gherx_hr" />
												</div>

											</div>
											<div class="row">
												<div class="col-lg-12">
												Please indicate vacant dates so that we can service this property.
												<input type="hidden" name="holiday_rental_input" value="1">
												</div>
												<div class="col-lg-12">
													<div class="row">
														<div class="col-md-2"><input data-date-format="d/m/Y" type="text" class="form-control flatpickr flatpickr-input holiday_vacant_from" name="holiday_vacant_from" placeholder="Vacant From"></div>
														<div class="col-md-2"><input data-date-format="d/m/Y" type="text" class="form-control flatpickr flatpickr-input holiday_vacant_till" name="holiday_vacant_till" placeholder="Vacant Till"></div>
													</div>
												</div>
												<div class="col-lg-12 columns txt-right">
													<input type='hidden' name='hid_prop_id'  value='<?php echo $row->property_id; ?>' />
													<input type='hidden' name='hid_job_id'  value='<?php echo $row->j_id ?>' />
													<input type="hidden" class="is_response" value="<?php echo $is_response ?>">
													<button data-fancybox-close="" style="margin-top:20px;" type="button" class="btn btn-danger btn_close_aaa">Cancel</button>
													<button style="margin-top:20px;" type="button" class="btn btn-success submit_holiday_rental_yes">Submit</button>
												</div>
											</div>

										</div>

									<?php } ?>
									<!-- FOR Short Term Rental YES BUTTON END -->


								<!-- UPDATE PROPERTY FANCY BOX END -->

							<?php }else if($is_response == 9){ //need agent to verify ?>

									<div id="hidden-content-<?php echo $row->j_id; ?>" class="escalate_response_div fancy_box_popup" style="display:none;">
										<div class="row">

											<div class="col-lg-12 columns">
												<h2>Response</h2>
												<hr class="gherx_hr" />
								            </div>

										</div>
										<div class="row">
											<div class="col-lg-12">
											<input type="hidden" value="I will contact the tenant to confirm this service." name="need_agency_ver_res_text">
											I will contact the tenant to confirm this service.
											</div>
											<div class="col-lg-12 columns txt-right">
												<input type='hidden' name='hid_prop_id'  value='<?php echo $row->property_id; ?>' />
												<input type='hidden' name='hid_job_id'  value='<?php echo $row->j_id ?>' />
												<input type="hidden" class="is_response" value="<?php echo $is_response ?>">
												<button data-fancybox-close="" style="margin-top:20px;" type="button" class="btn btn-danger btn_close_aaa">Cancel</button>
												<button style="margin-top:20px;" type="button" class="btn btn-success btn_need_agent_to_verify">Submit</button>
											</div>
										</div>

									</div>

							<?php }else if($is_response==10){ //TO BOOK WITH AGENT ?>

								<div id="hidden-content-<?php echo $row->j_id; ?>" class="escalate_response_div fancy_box_popup" style="display:none;">
										<div class="row">

											<div class="col-lg-12 columns">
												<h2>Response</h2>
												<hr class="gherx_hr" />
								            </div>

										</div>
										<div class="row">
											<div class="col-lg-12">
											<span class="to_be_book_with_agent_text">This tenant has advised that an agent from <?php echo $row->agency_name; ?> is required to attend with our technician.</span><br/>
											Please advise us of a suitable date and time.<br/>
											</div>
											<div class="col-lg-12">
												<div class="row" style="margin-top:15px;">
													<div class="col-lg-2"><input data-date-format="d/m/Y H:i" name="to_be_book_with_agent_time" class="flatpickr_2 form-control flatpickr-input" id="flatpickr" type="text" placeholder="Select Date/Time" readonly="readonly" data-enable-time="true"></div>
												</div>
											</div>
											<div class="col-lg-12 columns txt-right">
												<input type='hidden' name='hid_prop_id'  value='<?php echo $row->property_id; ?>' />
												<input type='hidden' name='hid_job_id'  value='<?php echo $row->j_id ?>' />
												<input type="hidden" class="is_response" value="<?php echo $is_response ?>">
												<button data-fancybox-close="" style="margin-top:20px;" type="button" class="btn btn-danger btn_close_aaa">Cancel</button>
												<button style="margin-top:20px;" type="button" class="btn btn-success btn_to_book_with_agent">Submit</button>
											</div>
										</div>

									</div>

							<?php } ?>

							<!-- Fancy box END -->


							</td>

							<!--<td class="edit_sd_td2">
								<input class="property_id2" value="<?php echo $row->property_id; ?>" type="hidden">
								<button type="button" class="btn btn_update_property btn-sm">Update Property</button>
								-->
								<!-- Fancybox trigger button -->
								<!--<a href="javascript:;" class="fb_trigger2" style="display:none;" data-options='{"modal":"true"}' data-fancybox data-src="#hidden-content2-<?php echo $row->j_id; ?>">Trigger the fancybox</a> -->



							<!--</td>-->

					<?php
					}
				}else{
					echo "<tr><td colspan='5'>There are no properties that require your help</td></tr>";
				}
					?>
						<!--</tr>
							<tr><td colspan="5">&nbsp;</td>
						</tr>	-->
				</tbody>
			</table>
		</div>

		<nav aria-label="Page navigation example" style="text-align:center">
			<?php echo $pagination; ?>
		</nav>

		<div class="pagi_count"><?php echo $pagi_count; ?></div>

	</div><!--.box-typical-body-->


</section><!--.box-typical-->


<script type="text/javascript">

			$(document).ready(function(){



					 //select2
					$(".select2-photo").not('.manual').select2({
						templateSelection: select2Photos,
						templateResult: select2Photos
					})

					//BUTTONS  (Fancybox popup)
					$('.btn-escalate-update-now').on('click',function(e){
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
						//for help_neede page > add remove_add_tenant_felds request to remove add tenant field on tenant ajax and add new tenant filed manually on help_needed page directly
						tenants_block.load('/properties/tenants_ajax_for_help_needed', {
								prop_id: property_id,
							}, function(response, status, xhr) {

								loader.hide();
								$('[data-toggle="tooltip"]').tooltip(); //init tooltip
								phone_mobile_mask(); //init phone/mobile mask
								mobile_validation(); //init mobile validation
								phone_validation(); //init phone validation
								add_validate_tenant(); //init new tenant validation

							}

						);
						fb_trigger.click(); // trigger fancybox popup

					})



					//UPDATE TENANTS/PROPERTY DETAILS > didn't used for now > elements deleted and updated to btn_submit_escalate_update_details_v2
					$(document).on('click','.btn_submit_escalate_update_details',function(){
						var obj = $(this);
						var propId = obj.parents('.fancy_box_popup').find('input[name="hid_prop_id"]').val();
						var job_id = obj.parents('.fancy_box_popup').find('input[name="hid_job_id"]').val();

						var work_order = obj.parents('.fancy_box_popup').find('input[name="work_order"]');
						var alarm_code = obj.parents('.fancy_box_popup').find('input[name="house_alarm"]');
						var key_number = obj.parents('.fancy_box_popup').find('input[name="key_number"]');

						var is_verify_ten = obj.parents('.fancy_box_popup').find('input[name="is_verify_ten"]');

						var have_dontHave_tenants =  obj.parents('.fancy_box_popup').find('input[name="have_dontHave_tenants"]');

						var fullAddress = obj.data('full_address');


						jQuery.ajax({
										type: "POST",
										url: "<?php echo base_url('/jobs/get_tenant_count') ?>",
										dataType: 'json',
										data: {
											prop_id: propId
										}
										}).done(function(data){
											if(data.count>0){

													swal(
															{
																title: "",
																text: "Finished updating "+fullAddress,
																type: "warning",
																showCancelButton: true,
																confirmButtonClass: "btn-success",
																confirmButtonText: "Yes, Proceed",
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
																				is_verify_ten: is_verify_ten.val(),
																				prop_vacant: 0,
																				page_type: 'help_needed'
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
																						location.reload();
																						}
																					});
																				}else{
																					//swal('Error','Details already updated','error');
																					location.reload();
																				}
																			});
																}

															}
														);


											}else{

												swal(
															{
																title: "",
																text: "Is this property vacant?",
																type: "warning",
																showCancelButton: true,
																confirmButtonClass: "btn-success",
																confirmButtonText: "Yes, Continue",
																cancelButtonText: "No, Let me add tenants",
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
																				is_verify_ten: is_verify_ten.val(),
																				prop_vacant: 1,
																				page_type: 'help_needed'
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
																						location.reload();
																						}
																					});
																				}else{
																					//swal('Error','Details already updated','error');
																					location.reload();
																				}
																			});
																}

															}
														);

											}
										});



					});



					// RESPONSE
					$(document).on('click','.response-btn',function(){
						var obj = $(this);
						var propId = obj.parents('.fancy_box_popup').find('input[name="hid_prop_id"]').val();
						var job_id = obj.parents('.fancy_box_popup').find('input[name="hid_job_id"]').val();
						var response_text = obj.parents('.fancy_box_popup').find('textarea[name="escalate_response_txt"]').val();

						if($.trim(response_text).length!=0){

								swal(
									{
										title: "",
										text: "Submit Response?",
										type: "warning",
										showCancelButton: true,
										confirmButtonClass: "btn-success",
										confirmButtonText: "Yes, Submit",
										cancelButtonClass: "btn-danger",
										cancelButtonText: "No, Cancel!",
										closeOnConfirm: false,
										closeOnCancel: true,
									},
									function(res_confirm){
										if(res_confirm){

													jQuery.ajax({
														type: "POST",
														url: "<?php echo base_url('/jobs/ajax_escalate_capture_response') ?>",
														dataType: 'json',
														data: {
															job_id: job_id,
															prop_id: propId,
															response: response_text
														}
													}).done(function(res){
														if(res.status){
															swal({
																title:"Success!",
																text: "Response Submitted",
																type: "success",
																showConfirmButton: false,
																timer: 2000
															});
															setTimeout(function(){ window.location='/jobs/help_needed'; }, 2000);
														}else{
															swal('Error','Something went wrong try again later','error');

														}
													});

										}
									}
								)


						}else{
								swal('Error','Please Enter Response Text','error');
						}

					});

					//RESPONSE OLD JOB
					$(document).on('click','.response-btn-old-job',function(){
						var obj = $(this);
						var propId = obj.parents('.fancy_box_popup').find('input[name="hid_prop_id"]').val();
						var job_id = obj.parents('.fancy_box_popup').find('input[name="hid_job_id"]').val();
						var radio = obj.parents('.fancy_box_popup').find('input[name="old-job"]');
						var radio_selected = obj.parents('.fancy_box_popup').find('input[name="old-job"]:checked');
						var response_text = obj.parents('.fancy_box_popup').find('textarea[name="old-job-other"]');

						if(radio_selected.val()=='Other'){
							if(response_text.val()==""){
								var radio_val = radio_selected.val();
							}else{
								var radio_val = response_text.val();
							}
						}else{
							var radio_val = radio_selected.val();
						}

						if(radio.is(':checked')){ // Option selected

								if(radio_selected.val()=='NLM'){ // OPTION NLM SELECTED > Process NLM function > YES

									swal(
									{
										title: "",
										text: "Are you sure you want to mark this Property 'No Longer Managed'?",
										type: "warning",
										showCancelButton: true,
                                        confirmButtonClass: "btn-success",
                                        confirmButtonText: "Yes, Proceed",
                                        cancelButtonClass: "btn-danger",
                                        cancelButtonText: "No, Cancel!",
										closeOnConfirm: false,
										closeOnCancel: true,
									},
									function (isconfirm){
										if(isconfirm){
											$('#preloader').css({'opacity': '0.5','z-index':'1000000'}).show(); //show loader to git rid of button multi clicked
												jQuery.ajax({
													type: "POST",
													url: "<?php echo base_url('/jobs/ajax_escalate_verify_nlm') ?>",
													dataType: 'json',
													data: {
														job_id: job_id,
														prop_id: propId,
														verify_nlm_val: "no"
													}
												}).done(function(res){
													$('#preloader').hide(); //hide loader
													if(res.status){

														var resmsg = res.stat_msg;

														if(res.has_active_jobs){
															var sweet_title = "Error!";
															var sweet_type = "error";
														}else{
															var sweet_title = "Success!";
															var sweet_type = "success";
														}

														swal({
															title: sweet_title,
															text: resmsg,
															type: sweet_type,
															showConfirmButton: false,
															timer: 2000
														});
														setTimeout(function(){ window.location='/jobs/help_needed'; }, 2000);
													}else{

													swal('Error','Something went wrong try again later','error');

												}
											});

										}
									}
							)

								}else{

									swal(
									{
										title: "",
										text: "Submit Response?",
										type: "warning",
										showCancelButton: true,
										confirmButtonClass: "btn-success",
										confirmButtonText: "Yes, Submit",
										cancelButtonClass: "btn-danger",
										cancelButtonText: "No, Cancel!",
										closeOnConfirm: false,
										closeOnCancel: true,
									},
									function(res_confirm){
										if(res_confirm){
											$('#preloader').css({'opacity': '0.5','z-index':'1000000'}).show(); //show loader to git rid of button multi clicked
											jQuery.ajax({
												type: "POST",
												url: "<?php echo base_url('/jobs/ajax_escalate_capture_response') ?>",
												dataType: 'json',
												data: {
													job_id: job_id,
													prop_id: propId,
													response: radio_val,
													is_old_job_options: 1
												}
											}).done(function(res){
												$('#preloader').hide(); //hide loader
												if(res.status){
													swal({
														title:"Success!",
														text: "Response Submitted",
														type: "success",
														showConfirmButton: false,
														timer: 2000
													});
													setTimeout(function(){ window.location='/jobs/help_needed'; }, 2000);
												}else{
													swal('Error','Something went wrong try again later','error');
												}
											});

										}
									})

								}




						}else{
								swal('Error','Please Enter Response Text','error');
						}

					});

					//NEED AGENT TO VERIFY
					$(document).on('click','.btn_need_agent_to_verify',function(){
						var obj = $(this);
						var propId = obj.parents('.fancy_box_popup').find('input[name="hid_prop_id"]').val();
						var job_id = obj.parents('.fancy_box_popup').find('input[name="hid_job_id"]').val();
						var response_text = obj.parents('.fancy_box_popup').find('input[name="need_agency_ver_res_text"]').val();

						if(response_text!=""){

								swal(
									{
										title: "",
										text: "Submit Response?",
										type: "warning",
										showCancelButton: true,
										confirmButtonClass: "btn-success",
										confirmButtonText: "Yes, Submit",
										cancelButtonClass: "btn-danger",
										cancelButtonText: "No, Cancel!",
										closeOnConfirm: false,
										closeOnCancel: true,
									},
									function(res_confirm){
										if(res_confirm){

													jQuery.ajax({
														type: "POST",
														url: "<?php echo base_url('/jobs/ajax_escalate_capture_response') ?>",
														dataType: 'json',
														data: {
															job_id: job_id,
															prop_id: propId,
															response: response_text,
															is_agent_need_verify: 1
														}
													}).done(function(res){
														if(res.status){
															swal({
																title:"Success!",
																text: "Response Submitted",
																type: "success",
																showConfirmButton: false,
																timer: 2000
															});
															setTimeout(function(){ window.location='/jobs/help_needed'; }, 2000);
														}else{
															swal('Error','Something went wrong try again later','error');

														}
													});

										}
									}
								)


						}else{
								swal('Error','Please Enter Response Text','error');
						}

					});


					//TO BOOK WITH AGENT
					$(document).on('click','.btn_to_book_with_agent',function(){
						var obj = $(this);
						var propId = obj.parents('.fancy_box_popup').find('input[name="hid_prop_id"]').val();
						var job_id = obj.parents('.fancy_box_popup').find('input[name="hid_job_id"]').val();
						var response_text = obj.parents('.fancy_box_popup').find('span.to_be_book_with_agent_text').text();
						var response_date_time = obj.parents('.fancy_box_popup').find('input[name="to_be_book_with_agent_time"]').val();

						if(response_date_time!=""){

								swal(
									{
										title: "",
										text: "Submit Response?",
										type: "warning",
										showCancelButton: true,
										confirmButtonClass: "btn-success",
										confirmButtonText: "Yes, Submit",
										cancelButtonClass: "btn-danger",
										cancelButtonText: "No, Cancel!",
										closeOnConfirm: false,
										closeOnCancel: true,
									},
									function(res_confirm){
										if(res_confirm){

													jQuery.ajax({
														type: "POST",
														url: "<?php echo base_url('/jobs/ajax_escalate_capture_response') ?>",
														dataType: 'json',
														data: {
															job_id: job_id,
															prop_id: propId,
															response: response_text,
															response_date_time: response_date_time,
															is_to_be_book_with_agent: 1
														}
													}).done(function(res){
														if(res.status){
															swal({
																title:"Success!",
																text: "Response Submitted",
																type: "success",
																showConfirmButton: false,
																timer: 2000
															});
															setTimeout(function(){ window.location='/jobs/help_needed'; }, 2000);
														}else{
															swal('Error','Something went wrong try again later','error');
														}
													});

										}
									}
								)


						}else{
								swal('Error','Please Enter Date/Time','error');
						}

					});


					//Short Term Rental
					/*$(document).on('click','.btn_holiday_rental',function(){
						var obj = $(this);
						var propId = obj.parents('.fancy_box_popup').find('input[name="hid_prop_id"]').val();
						var job_id = obj.parents('.fancy_box_popup').find('input[name="hid_job_id"]').val();
						var response_text = obj.parents('.fancy_box_popup').find('input[name="need_agency_ver_res_text"]').val();

						swal(
							{
								title: "",
								text: "Submit Response?",
								type: "warning",
								showCancelButton: true,
								confirmButtonClass: "btn-success",
								confirmButtonText: "Yes, Submit",
								cancelButtonClass: "btn-danger",
								cancelButtonText: "No, Cancel!",
								closeOnConfirm: false,
								closeOnCancel: true,
							},
							function(res_confirm){
								if(res_confirm){

									jQuery.ajax({
										type: "POST",
										url: "<?php echo base_url('/jobs/ajax_escalate_capture_response') ?>",
										dataType: 'json',
										data: {
											job_id: job_id,
											prop_id: propId,
											response: obj.val(),
											is_holiday_rental: 1
										}
									}).done(function(res){
										if(res.status){
											swal({
												title:"Success!",
												text: "Response Submitted",
												type: "success",
												showCancelButton: false,
												confirmButtonText: "OK",
												closeOnConfirm: false,

											},function(isConfirm_suc){
												if(isConfirm_suc){
													//swal.close();
													//$.fancybox.close();
													location.reload();
												}
											});
										}else{
											swal('Error','Something went wrong try again later','error');

										}
									});

								}
							}
						)

					});	*/
					$('.submit_holiday_rental_yes').on('click',function(){
						var obj = $(this);
						var err = "";
						var propId = obj.parents('.fancy_box_popup_2').find('input[name="hid_prop_id"]').val();
						var job_id = obj.parents('.fancy_box_popup_2').find('input[name="hid_job_id"]').val();
						var holiday_vacant_from = obj.parents('.fancy_box_popup_2').find('input[name="holiday_vacant_from"]').val();
						var holiday_vacant_till = obj.parents('.fancy_box_popup_2').find('input[name="holiday_vacant_till"]').val();
						var holiday_rental_input = obj.parents('.fancy_box_popup_2').find('input[name="holiday_rental_input"]').val();
						var is_response = obj.parents('.fancy_box_popup_2').find('.is_response').val();

						if(holiday_vacant_from==""){
							err +="Vacant from must not be empty.\n";
						}
						if(holiday_vacant_till==""){
							err +="Vacant till must not be empty.\n";
						}
						if(err!=""){
							swal('',err,'error');
							return false;
						}

						jQuery.ajax({
							type: "POST",
							url: "<?php echo base_url('/jobs/ajax_escalate_capture_response') ?>",
							dataType: 'json',
							data: {
								job_id: job_id,
								prop_id: propId,
								response: holiday_rental_input,
								start_date: holiday_vacant_from,
								due_date: holiday_vacant_till,
								is_response: is_response,
								is_holiday_rental: 1
							}
						}).done(function(res){
							if(res.status){
								swal({
									title:"Success!",
									text: "Response Submitted",
									type: "success",
									showConfirmButton: false,
									timer: 2000
								});
								setTimeout(function(){ window.location='/jobs/help_needed'; }, 2000);
							}else{
								swal('Error','Something went wrong try again later','error');

							}
						});

					})

					//verify NLM button
					$('.verify_nlm_btn').on('click',function(){
						var obj = $(this);

						var verify_nlm_val = obj.data('nlm_val');
						var propId = obj.parents('.edit_sd_td').find('input[name="property_id"]').val();
						var job_id = obj.parents('.edit_sd_td').find('input[name="job_id"]').val();


						if(verify_nlm_val=="yes"){
							var swal_text = "Still Manage Property?";
							var confirm_but = "btn-success";
							var confirm_txt = "Yes, Manage"

						}else{
							var swal_text = "Mark this property as 'No Longer Managed'?";
							var confirm_but = "btn-success";
							var confirm_txt = "Yes, Submit";
						}

						swal(
								{
									title: "",
									text: swal_text,
									type: "warning",
									showCancelButton: true,
									confirmButtonClass: confirm_but,
									confirmButtonText: confirm_txt,
									cancelButtonClass: "btn-danger",
									cancelButtonText: "No, Cancel!",
									closeOnConfirm: false,
									closeOnCancel: true,
								},
								function (isconfirm){
									if(isconfirm){
										$('#preloader').css({'opacity': '0.5','z-index':'1000000'}).show(); //show loader to git rid of button multi clicked
											jQuery.ajax({
														type: "POST",
														url: "<?php echo base_url('/jobs/ajax_escalate_verify_nlm') ?>",
														dataType: 'json',
														data: {
															job_id: job_id,
															prop_id: propId,
															verify_nlm_val: verify_nlm_val
														}
													}).done(function(res){
														$('#preloader').hide(); //hide loader
														if(res.status){

															var resmsg = res.stat_msg;

															if(res.has_active_jobs){
																var sweet_title = "Error!";
																var sweet_type = "error";
															}else{
																var sweet_title = "Success!";
																var sweet_type = "success";
															}

															swal({
																title: sweet_title,
																text: resmsg,
																type: sweet_type,
																showConfirmButton: false,
																timer: 2000
															});
															setTimeout(function(){ window.location='/jobs/help_needed'; }, 2000);
														}else{

															swal('Error','Something went wrong try again later','error');

														}
													});

									}
								}
						)



					});

					//Update Property Button
					$('.btn_update_property').on('click',function(e){
						e.preventDefault();
						var obj = $(this);
						var fb_trigger = obj.parents("td.edit_sd_td2:first").find(".fb_trigger2");
						var tenants_block = obj.parents("td.edit_sd_td2:first").find(".tenants_ajax_box");
						var property_id = obj.parents("td.edit_sd_td2:first").find(".property_id2").val();
						var loader = obj.parents("td.edit_sd_td2:first").find(".loader_block_v2_v2");

						// clear all tenants div
						$('.tenants_ajax_box').empty();

						loader.show();

						//load tenants ajax box (via ajax)
						tenants_block.load('/properties/tenants_ajax_for_help_needed', {
								prop_id: property_id
							}, function(response, status, xhr) {

								loader.hide();
								$('[data-toggle="tooltip"]').tooltip(); //init tooltip
								phone_mobile_mask(); //init phone/mobile mask
								mobile_validation(); //init mobile validation
								phone_validation(); //init phone validation
								add_validate_tenant(); //init new tenant validation

							}

						);

						fb_trigger.click(); // trigger fancybox popup
					})

					//Update Property popup complete button
					$(document).on('click','.btn_submit_escalate_update_details_v2',function(){

						var obj = $(this);
						var tenants_arr = [];
						var propId = obj.parents('.fancy_box_popup').find('input[name="hid_prop_id"]').val();
						var job_id = obj.parents('.fancy_box_popup').find('input[name="hid_job_id"]').val();

						var work_order = obj.parents('.fancy_box_popup').find('input[name="work_order"]');
						var alarm_code = obj.parents('.fancy_box_popup').find('input[name="house_alarm"]');
						var key_number = obj.parents('.fancy_box_popup').find('input[name="key_number"]');
						var vacant_from = obj.parents('.fancy_box_popup').find('input[name="vacant_from"]');
						var vacantl_till = obj.parents('.fancy_box_popup').find('input[name="vacantl_till"]');
						var is_verify_ten = obj.parents('.fancy_box_popup').find('input[name="is_verify_ten"]');
						var have_dontHave_tenants =  obj.parents('.fancy_box_popup').find('input[name="have_dontHave_tenants"]');
						var fullAddress = obj.data('full_address');
						var prop_vacant = (vacant_from.val()!="")?1:0;
						var is_response = obj.parents('.fancy_box_popup').find('.is_response').val();

						obj.parents(".fancybox-content").find(".new_tenant_fname").each(function () {
							var obj2 = jQuery(this);
             				var row = obj2.parents(".tenants-row");

							var new_tenant_fname = row.find(".new_tenant_fname").val();
							var new_tenant_lname = row.find(".new_tenant_lname").val();
							var new_tenant_mobile = row.find(".new_tenant_mobile").val();
							var new_tenant_landline = row.find(".new_tenant_landline").val();
							var new_tenant_email = row.find(".new_tenant_email").val();

							if(new_tenant_fname!="" || new_tenant_lname!=""){
								var json_data = {
									new_tenant_fname: new_tenant_fname,
									new_tenant_lname: new_tenant_lname,
									new_tenant_mobile: new_tenant_mobile,
									new_tenant_landline: new_tenant_landline,
									new_tenant_email: new_tenant_email
								};

								var json_str = JSON.stringify(json_data);
								tenants_arr.push(json_str);
							}
						})

						var err = "";

						$('#preloader').css({'opacity': '0.5','z-index':'1000000'}).show(); //show loader to git rid of button multi clicked
						jQuery.ajax({
							type: "POST",
							url: "<?php echo base_url('/jobs/get_tenant_count') ?>",
							dataType: 'json',
							data: {
								prop_id: propId
							}
						}).done(function(data){
							if(data.count>0 || tenants_arr!=""){ //has tenants > Vacant not required
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
										is_verify_ten: is_verify_ten.val(),
										start_date: vacant_from.val(),
										due_date: vacantl_till.val(),
										prop_vacant: prop_vacant,
										is_response: is_response,
										page_type: 'help_needed',
										tenants_arr: tenants_arr
									}
								}).done(function(data){
									$('#preloader').hide(); //hide loader
									if(data.status){
										swal({
											title:"Success!",
											text: "Details Updated",
											type: "success",
											showConfirmButton: false,
											timer: 2000
										});
										setTimeout(function(){ window.location='/jobs/help_needed'; }, 2000);
									}else{
										//swal('Error','Details already updated','error');
										location.reload();
									}
								});
							}else{//has no tenants > Vacant required
								if(vacant_from.val()==""){
									err+="Please update some aspect of the property.\n";
								}
								if(err!=""){
									$('#preloader').hide();
									swal('',err,'error');
									return false;
								}

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
										is_verify_ten: is_verify_ten.val(),
										start_date: vacant_from.val(),
										due_date: vacantl_till.val(),
										prop_vacant: prop_vacant,
										is_response: is_response,
										page_type: 'help_needed',
										tenants_arr: tenants_arr
									}
								}).done(function(data){
									$('#preloader').hide(); //hide loader
									if(data.status){
										swal({
											title:"Success!",
											text: "Details Updated",
											type: "success",
											showConfirmButton: false,
											timer: 2000
										});
										setTimeout(function(){ window.location='/jobs/help_needed'; }, 2000);
									}else{
										//swal('Error','Details already updated','error');
										location.reload();
									}
								});

							}
						});



					});

					//verify NLM button for UPDATE NOW BUTTON/POPUP
					$('.verify_nlm_btn_v2').on('click',function(){
						var obj = $(this);
						var nlm_reason_div = obj.parents(".nlm_reason_div");

						var verify_nlm_val = obj.data('nlm_val');
						var propId = obj.parents('.fancy_box_popup').find('input[name="hid_prop_id"]').val();
						var job_id = obj.parents('.fancy_box_popup').find('input[name="hid_job_id"]').val();

						var reason_they_left = nlm_reason_div.find(".reason_they_left").val();
						var other_reason = nlm_reason_div.find(".other_reason").val();
						var error = '';

						if(verify_nlm_val=="yes"){ // Manage
							var swal_text = "Still Manage Property?";
							var confirm_but = "btn-success";
							var confirm_txt = "Yes, Manage"

						}else{ // mark NLM
							var swal_text = "Mark this property as 'No Longer Managed'?";
							var confirm_but = "btn-success";
							var confirm_txt = "Yes, Submit";
						}

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

							swal(
								{
									title: "",
									text: swal_text,
									type: "warning",
									showCancelButton: true,
									confirmButtonClass: confirm_but,
									confirmButtonText: confirm_txt,
									cancelButtonClass: "btn-danger",
									cancelButtonText: "No, Cancel!",
									closeOnConfirm: false,
									closeOnCancel: true,
								},
								function (isconfirm){
									if(isconfirm){
											$('#preloader').css({'opacity': '0.5','z-index':'1000000'}).show(); //show loader to git rid of button multi clicked
											jQuery.ajax({
												type: "POST",
												url: "<?php echo base_url('/jobs/ajax_escalate_verify_nlm') ?>",
												dataType: 'json',
												data: {
													job_id: job_id,
													prop_id: propId,
													verify_nlm_val: verify_nlm_val,
													reason_they_left: reason_they_left,
													other_reason: other_reason
												}
											}).done(function(res){
												$('#preloader').hide(); //hide loader
												if(res.status){

													var resmsg = res.stat_msg;

													if(res.has_active_jobs){
														var sweet_title = "Error!";
														var sweet_type = "error";
													}else{
														var sweet_title = "Success!";
														var sweet_type = "success";
													}

													swal({
														title: sweet_title,
														text: resmsg,
														type: sweet_type,
														showConfirmButton: false,
														timer: 2000
													});
													setTimeout(function(){ window.location='/jobs/help_needed'; }, 2000);
												}else{

												swal('Error','Something went wrong try again later','error');

											}
										});

									}
								}
							)
							
						}						


					});



				/*$('.old_job_radio').on('change', function() {
					var a = $(this).filter(':checked').val();
					if(a=='Other'){
						$('.old-job-other').show();
					}else{
						$('.old-job-other').hide();
					}
				});*/

				//custom flatpcker with time
				 //init datepicker
				$('.flatpickr_2').flatpickr();


				// toogle new tenant div/fields
				$(document).on('click','#plus_new_tenant_btn',function(e){
					e.preventDefault();
					e.stopImmediatePropagation();
					var obj = $(this);
					var btnName = obj.find('.btn_inline_text');
					var btnIcon = obj.find('.glyphicon');
					var new_tenant_fname = obj.parents('.fancy_box_popup').find('input[name="new_tenant_fname"]');
					var new_tenant_lname = obj.parents('.fancy_box_popup').find('input[name="new_tenant_lname"]');
					var new_tenant_mobile = obj.parents('.fancy_box_popup').find('input[name="new_tenant_mobile"]');
					var new_tenant_landline = obj.parents('.fancy_box_popup').find('input[name="new_tenant_landline"]');
					var new_tenant_email = obj.parents('.fancy_box_popup').find('input[name="new_tenant_email"]');
					obj.parents('.fancybox-content').find('.new_tenant_fields_box').slideToggle(function(){
							if(btnName.html()=="Tenant"){
								btnName.html("Cancel");
								btnIcon.removeClass('glyphicon-plus').addClass('glyphicon-minus');
							}else{
								btnName.html("Tenant");
								btnIcon.removeClass('glyphicon-minus').addClass('glyphicon-plus');
								//remove field values
								new_tenant_fname.val("");
								new_tenant_lname.val("");
								new_tenant_mobile.val("");
								new_tenant_landline.val("");
								new_tenant_email.val("");
							}
					});
				});

				//add new tenant row
				$('.add_tenan_new_row_btn').on('click',function(e){
					var obj = $(this);
					e.preventDefault();
					insertNewTenantRow(obj);
				})

				// DELETE tenants row
				jQuery(document).on('click','.del_tenant_row',function(e){
					e.preventDefault();
					var obj = $(this);
					obj.parents('.tenants-row').remove();
				});


				// NLM
				jQuery(document).on("click",".nlm_process_btn",function(){

					var nlm_process_btn_dom = jQuery(this);
					var nlm_div = nlm_process_btn_dom.parents(".nlm_div");

					nlm_div.find(".nlm_reason_div").toggle();

				});

				// reason show/hide script
				jQuery(".reason_they_left").change(function(){

					var reason_they_left_dom = jQuery(this);
					var nlm_div = reason_they_left_dom.parents(".nlm_div");
					var reason_they_left =  reason_they_left_dom.find("option:checked").val();

					if( reason_they_left == -1 ){
						nlm_div.find(".other_reason").show();
					}else{
						nlm_div.find(".other_reason").hide();
					}            

				});



			}); //doc ready end


			function insertNewTenantRow(obj){

			var htm_content = '<tr class="tenants-row">'+
			'<td>'+
			'<div class="form-group"><input placeholder="First Name" data-validation="[NOTEMPTY]" data-validation-label="First Name" type="text" class="form-control new_tenant_fname" name="new_tenant_fname[]"></div>' +
			'</td>'+
			'<td>'+
			'<div class="form-group"><input placeholder="Last Name" type="text" class="form-control new_tenant_lname" name="new_tenant_lname[]"></div>' +
			'</td>'+
			'<td>'+
			'<div class="form-group"><input  type="text" class="form-control tenant_mobile new_tenant_mobile" name="new_tenant_mobile[]"></div>' +
			'</td>'+
			'<td>'+
			'<div class="form-group"><input type="text" class="form-control phone-with-code-area-mask-input new_tenant_landline" name="new_tenant_landline[]"></div>' +
			'</td>'+
			'<td>'+
			'<div class="form-group"><input placeholder="Email"  type="text" class="form-control new_tenant_email" name="new_tenant_email[]"></div>' +
			'</td>'+
			'<td>'+
			'<a data-toggle="tooltip" title="Remove" class="del_tenant_row" href="#"><span class="font-icon font-icon-trash"></span></a>' +
			'</td>'+
			'</tr>';
			obj.parents('.fancybox-content').find('.add_new_tenant_plus_btn').before(htm_content);
			phone_mobile_mask();
			//mobile_validation();
			//phone_validation();

			}



</script>