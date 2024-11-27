<section class="box-typical box-typical-padding">

	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/reports">Reports</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/reports/qld_upgrade"><?php echo $title; ?></a></li>
	  </ol>
	</nav>

	<h5 class="m-t-lg with-border"><?php echo $title; ?>


			<div class="float-right">
				<div class="col-sm-12">
					<section class="proj-page-section">
						<div class="proj-page-attach">
							<i class="font-icon font-icon-pdf"></i>
							<p class="name">QLD Upgrade</p>
							<p>
								<a href="
									/reports/qld_upgrade/?pdf=1
									&output_type=I
									&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
									&search=<?php echo $this->input->get_post('search'); ?>"
									
									target="blank"
								>
									View
								</a>
								
								<a href="
									/reports/qld_upgrade/?pdf=1
									&output_type=D
									&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
									&search=<?php echo $this->input->get_post('search'); ?>"
								>
									Download
								</a>
							</p>
						</div>
					</section>
			</div>
		</div>

		<?php 
			$export_link_params = array(
				'pm_id' => $this->input->get_post('pm_id'),
				'search' => $this->input->get_post('search')
			);
			$export_link = '/reports/qld_upgrade/?export=1&'.http_build_query($export_link_params);

		?>
	
		<div class="float-right">
			<div class="col-sm-12">
				<section class="proj-page-section">
					<div class="proj-page-attach">
						<i class="font-icon font-icon-post"></i>
						<p class="name"> <?php echo $title; ?> CSV</p>
						<p>
							<a href="<?php echo $export_link; ?>">
								Download
							</a>
						</p>
					</div>
				</section>
			</div>
		</div>

</h5>
   

        <!-- Header -->
        <header class="box-typical-header">
            <div class="box-typical box-typical-padding">
			
                <?php
				$form_attr = array(
					'id' => 'jform'
				);
				echo form_open('/reports/qld_upgrade',$form_attr);
				?>
                    <div class="form-groupss row">
                        
						<div class="float-left">
							<label for="exampleSelect" class="col-sm-12 form-control-label">Property Manager</label>
							<div class="col-sm-12" style="width:250px;">
								<select name="pm_id" class="form-control field_g2 select2-photo">
									<option value="">---</option>
									<option <?php  echo ( $this->input->get_post('pm_id') == '0' )?'selected="selected"':''; ?> value="0" data-photo="<?php echo $this->config->item('photo_empty'); ?>">No PM assigned</option>				
												
									<?php

									foreach($pm_filter->result() as $pm_row){
										if($pm_row->properties_model_id_new){
										?>
									
										<option data-photo="<?php echo $this->jcclass->displayUserImage($pm_row->photo); ?>" value="<?php echo $pm_row->properties_model_id_new; ?>" <?php echo ( $pm_row->properties_model_id_new == $this->input->get_post('pm_id') )?'selected="selected"':''; ?>><?php echo "{$pm_row->fname} {$pm_row->lname}"; ?></option>
										
										<?php
									}
								}
								
								
									?>
								</select>
							</div>
						</div>
						
						
					

                        <div class="float-left">
					<label class="col-sm-12 form-control-label">Search</label>
					<div class="col-sm-12">
					<input type="text" name="search" class="form-control" placeholder="Text" value="<?php echo $this->input->get_post('search'); ?>" />
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
							<th>&nbsp;</th>
							<th style="width: 270px;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php
                        if(!empty($qld_services_list->result())){
                            foreach($qld_services_list->result() as $row){

								//get/check IC Upgrade job
								/*$status_not_in = array('Booked','Pre Completion','Merged Certificates','Completed','Cancelled');
								$this->db->select('id');
								$this->db->from('jobs');
								$this->db->where('property_id', $row->property_id);
								$this->db->where('job_type', "IC Upgrade");
								$this->db->where_not_in('status',$status_not_in);
								$this->db->where('del_job', 0);
								$ic_q = $this->db->get();
								$ic_row = $ic_q->row();

								if( $ic_q->num_rows()>0 ){
									$has_id_job = 1;
									$ic_job_id = $ic_row->id;
								}else{
									$has_id_job = 0;
									$ic_job_id = NULL;
								}*/

								/*
								//get job by prop_id
								$this->db->select('id as j_id,service as j_service,date as j_date');
								$this->db->from('jobs');
								$this->db->where('property_id', $row->property_id);
								$this->db->where('status', 'Completed');
								$this->db->where('del_job', 0);
								$this->db->order_by('date','desc');
								$this->db->limit(1);
								$j_q = $this->db->get();
								$job_row = $j_q->row();
								*/

								// get IC job per property > if ic exist - dont show property
								/*$this->db->select('COUNT(id) as ic_count');
								$this->db->from('jobs');
								$this->db->where('del_job',0);
								$this->db->where('property_id',$row->property_id);
								$this->db->where('job_type','IC Upgrade');
								$this->db->where('status!=','Cancelled');
								$j_q_tt = $this->db->get();
								$count_row = $j_q_tt->row()->ic_count;
								*/

							

                                ?>
                                    <tr>

                                        <td>
										<a href="/properties/property_detail/<?php echo $row->property_id ?>" target="blank"><?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3}"; ?></a>
                        
										</td>
                                        <td>
										<?php  
									if( isset($row->properties_model_id_new) && $row->properties_model_id_new != 0 && $row->properties_model_fname!="" ){
										echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
										echo "{$row->properties_model_fname} {$row->properties_model_lname}";
									}

									?>    

                                        </td>
                                        <td data-jobid="<?php echo $row->j_id; ?>"><?= Alarm_job_type_model::icons($row->ajt_id); ?></td>
                                        <td>Not Compliant with NEW Legislation</td>
										<td>
											<a data-jobid="<?php echo $row->j_id; ?>" data-fancybox="" data-src="#view_quote_fancybox_<?php echo $row->property_id; ?>" class="btn btn-sm btn_view_quotes" href="#">View Quotes</a> &nbsp;&nbsp; <a data-jobID="<?php echo $row->j_id;  ?>" data-propid="<?php echo $row->property_id ?>" class="btn btn-sm btn_advise_upgrade" href="#">Advise Upgraded</a> 
											
											<!-- FANCY BOX -->
											<div id="view_quote_fancybox_<?php echo $row->property_id; ?>" style="display:none;">
												<h4>
                                                    <?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3}"; ?>
												    &nbsp;&nbsp;
                                                    <?= Alarm_job_type_model::icons($row->ajt_id); ?>
                                                </h4>

												<?php 	
												$comb_quote_ci_link = "{$this->config->item('crmci_link')}/pdf/view_quote/?job_id={$row->j_id}&qt=combined";
												?>

												<table class="table table-hover main-table">
													<thead>
														<th>
														<?php if($row->j_id>0){ ?>
															PDF Quote
														<?php } ?>
														</th>
														<th>&nbsp;</th>
														<th>Brooks Alarms</th>
														<?php if( $this->config->item('disable_cavius_option') == 0 ){ ?>
														<th>&nbsp;</th>
														<th>Cavius Alarms</th>
														<?php } ?>
														<th>&nbsp;</th>
														<th><?php echo $this->system_model->get_quotes_new_name(22); ?> Alarms</th>
													</thead>
													<tbody>
														<tr>
															<td>
																<?php if($row->j_id>0){ ?>
																	<a style="margin-right:10px;;" data-toggle="tooltip" title='View Upgrade Quote' href="<?php echo $comb_quote_ci_link; ?>" class="quq_link" target="_blank">
																		<i style="font-size:26px;" class="font-icon font-icon-pdf"></i>
																	</a>
																<?php } ?>

																<!-- copy to clipboard -->
																<a data-toggle='tooltip' title='Copy to Clipboard' href='javascript:void(0);'>
																	<span style='font-size:16px;' class='font-icon font-icon-share upgrade_quote_ctcb'>
																		<input type='hidden' class="upgrade_quote_link" value='<?php echo $comb_quote_ci_link; ?>' />
																	</span>                                                                                    									
																</a>
															</td>

															<!-- 240v RF brooks --->														
															<?php												
															if( $has_240v_rf_brooks == true ){								
																?>
																<!-- Quote Amount -->
																<td>
																	<?php							
																	$quote_amount_brooks = ( $agency_price_240v_rf_brooks > 0 )?( $agency_price_240v_rf_brooks * $row->qld_new_leg_alarm_num ):($this->config->item('default_qld_upgrade_quote_price') * $row->qld_new_leg_alarm_num); ;
																	echo "$".number_format($quote_amount_brooks,2);
																	?>
																</td>								

																<!-- Action -->
																<td>
																	<button 
																		type="button" 
																		class="btn btn-inline btn-success btn_proceed_with_quote" 
																		data-preferred_alarm_id="<?php echo $alarm_pwr_id_240v_rf_brooks; ?>" 
																		data-preferred_alarm_make="Brooks" <?php echo ($qld_approved)?'disabled':'' ?>
																		data-preferred_alarm_price="<?php echo $quote_amount_brooks; ?>"
																	>
																		Proceed with Brooks
																	</button>								
																</td>
																<?php	
															}																		
															?>	

															<!-- 240v RF cavius --->												
															<?php			
															if( $this->config->item('disable_cavius_option') == 0 ){													
															if( $has_240v_rf_cavius == true ){							
															?>
																<!-- Quote Amount -->
																<td>
																	<?php
																	$quote_amount_cavius = ( $agency_price_240v_rf_cavius > 0 )?( $agency_price_240v_rf_cavius * $row->qld_new_leg_alarm_num ):($this->config->item('default_qld_upgrade_quote_price') * $row->qld_new_leg_alarm_num); ;
																	echo "$".number_format($quote_amount_cavius,2);
																	?>
																</td>
																
																<!-- Action -->									
																<td>
																	<button 
																		type="button" 
																		class="btn btn-inline btn-success btn_proceed_with_quote"  
																		data-preferred_alarm_id="<?php echo $alarm_pwr_id_240v_rf_cavius; ?>" 
																		data-preferred_alarm_make="Cavius" <?php echo ($qld_approved)?'disabled':'' ?>
																		data-preferred_alarm_price="<?php echo $quote_amount_cavius; ?>"
																	>
																		Proceed with Cavius
																	</button>								
																</td>
															<?php								
															}									
															}									
															?>	

															<!-- 240v RF emerald --->														
															<?php												
															if( $has_240v_rf_emerald == true ){								
																?>
																<!-- Quote Amount -->
																<td>
																	<?php								
																	$quote_amount_240v_rf_emerald = ( $agency_price_240v_rf_emerald > 0 )?( $agency_price_240v_rf_emerald * $row->qld_new_leg_alarm_num ):($this->config->item('default_qld_upgrade_quote_price') * $row->qld_new_leg_alarm_num); ;
																	echo "$".number_format($quote_amount_240v_rf_emerald,2);
																	?>
																</td>								

																<!-- Action -->
																<td>
																	<button 
																		type="button" 
																		class="btn btn-inline btn-success btn_proceed_with_quote" 
																		data-preferred_alarm_id="<?php echo $alarm_pwr_id_240v_rf_emerald; ?>" 
																		data-preferred_alarm_make="<?php echo $this->system_model->get_quotes_new_name(22); ?>" <?php echo ($qld_approved)?'disabled':'' ?>
																		data-preferred_alarm_price="<?php echo $quote_amount_240v_rf_emerald; ?>"
																	>
																		Proceed with <?php echo $this->system_model->get_quotes_new_name(22); ?>
																	</button>								
																</td>
																<?php	
															}																		
															?>

															<input type="hidden" name="job_id" value="<?php echo $j_id->j_id; ?>" />
															<input type="hidden" class="j_service" value="<?php echo $j_id->j_service; ?>" />
															<input type="hidden" class="j_date" value="<?php echo $j_id->j_date; ?>" />								
															<input type="hidden" class="property_id" value="<?php echo $row->property_id; ?>" />

														</tr>
													</tbody>
												</table>
											</div>
											<!-- FANCY BOX END -->

										</td>
                                    </tr>
                                <?php
                            
						}
                        }
                        ?>
                     

                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation example" style="text-align:center">

              <?php echo $pagination ?>

            </nav>
            <div class="pagi_count"><?php echo $pagi_count ?></div>

        </div>
        <!--.box-typical-body-->


</section>
    <!--.box-typical-->

<script>
jQuery(document).ready(function(){
    //select2
	$(".select2-photo").not('.manual').select2({
			templateSelection: select2Photos,
			templateResult: select2Photos
		});


		// copy to clipboard
	jQuery(".upgrade_quote_ctcb").click(function(){

		var upgrade_quote_ctcb_dom = jQuery(this); // curren button DOM
		var upgrade_quote_link_dom = upgrade_quote_ctcb_dom.find('.upgrade_quote_link'); // link to be copied DOM		
		var upgrade_quote_link = upgrade_quote_link_dom.val(); // link to be copied		

		copy_to_clipboard(upgrade_quote_link) 	

	});	


	jQuery(".btn_proceed_with_quote").click(function(e){

		var btn_proceed_with_quote_btn_node = jQuery(this);
		var this_row = btn_proceed_with_quote_btn_node.parents("tr:first");
		var property_id = this_row.find(".property_id").val();
		var j_service = this_row.find(".j_service").val();
		var quote_amount = parseFloat(btn_proceed_with_quote_btn_node.attr("data-preferred_alarm_price"));
		var preferred_alarm_id = btn_proceed_with_quote_btn_node.attr("data-preferred_alarm_id");
		var preferred_alarm_make = btn_proceed_with_quote_btn_node.attr("data-preferred_alarm_make");		
		var job_id = this_row.find(".job_id").val();
		
		swal({
			title: "Upgrade Quote",
			text: "You've chosen to upgrade using "+preferred_alarm_make+" alarms for $"+quote_amount.toFixed(2)+", are you sure you want to proceed?",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-success",
			confirmButtonText: "I approve this upgrade",
			cancelButtonClass: "btn-danger",
			cancelButtonText: "No, Cancel!",
			closeOnConfirm: false
		},
		function(isConfirm) {


			if (isConfirm) { // yes

				jQuery.ajax({
					type: "POST",
					url: "/reports/qld_upgrade_proceed_with_quote",
					data: {
						property_id: property_id,
						amount: quote_amount.toFixed(2),
						j_service: j_service,
						preferred_alarm_id: preferred_alarm_id,
						job_id : job_id
					}
				}).done(function( ret ) {

					swal({
						title: "Success!",
						text: "Update Success",
						type: "success",
						confirmButtonClass: "btn-success"
					},function(){

						window.location='/reports/qld_upgrade'

					});
				});

			} else { // no

			}


		});

	});

	//btn_advise_upgrade
	jQuery(".btn_advise_upgrade").click(function(e){

		var prop_id = $(this).attr('data-propid');
		var job_id = $(this).attr('data-jobID');
		/*var hasicjob = $(this).attr('data-hasicjob');
		var ic_job_id = $(this).attr('data-ic_job_id');*/
		
		swal({
			title: "Advise Upgrade",
			text: "Confirm advise upgrade?",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-success",
			confirmButtonText: "Yes, Proceed",
			cancelButtonClass: "btn-danger",
			cancelButtonText: "No, Cancel!",
			closeOnConfirm: false
		},
		function(isConfirm) {


			if (isConfirm) { // yes

				jQuery.ajax({
					type: "POST",
					url: "/reports/qld_upgrade_advise_upgrade",
					data: {
						property_id: prop_id,
						job_id: job_id
					}
				}).done(function( ret ) {

					swal({
						title: "Success!",
						text: "Advise Upgrade Success",
						type: "success",
						confirmButtonClass: "btn-success"
					},function(){

						window.location='/reports/qld_upgrade'

					});
				});

			} else { // no

			}


		});

		});

})
</script>

