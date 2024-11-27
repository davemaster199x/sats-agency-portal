<section class="box-typical box-typical-padding">

	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/reports">Reports</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/reports/qld_upgrade_quotes"><?php echo $title; ?></a></li>
	  </ol>
	</nav>


	<h5 class="m-t-lg with-border"><?php echo $title; ?>

		<div class="float-right">
			<div class="col-sm-12">
				<section class="proj-page-section">
					<div class="proj-page-attach">
						<i class="font-icon font-icon-pdf"></i>
						<p class="name">QLD Upgrade Quotes</p>
						<p>
							<a href="
								/reports/qld_upgrade_quotes/?pdf=1
								&output_type=I
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&search=<?php echo $this->input->get_post('search'); ?>
								&status_filter=<?php echo $this->input->get_post('status_filter'); ?>"

								target="blank"
							>
								View
							</a>

							<a href="
								/reports/qld_upgrade_quotes/?pdf=1
								&output_type=D
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&search=<?php echo $this->input->get_post('search'); ?>
								&status_filter=<?php echo $this->input->get_post('status_filter'); ?>"
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
				'status_filter' => $this->input->get_post('status_filter'),
				'search' => $this->input->get_post('search')
			);
			$export_link = '/reports/qld_upgrade_quotes/?export=1&'.http_build_query($export_link_params);

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
		echo form_open('/reports/qld_upgrade_quotes',$form_attr);
		?>
			<div class="form-groupss row">

				<div class="float-left">
					<label for="exampleSelect" class="col-sm-12 form-control-label">Property Manager</label>
					<div class="col-sm-12" style="width:250px;">
						<select name="pm_id" class="form-control field_g2 select2-photo" >
							<option value="">---</option>
							<option <?php  echo ( $this->input->get_post('pm_id') == '0' )?'selected="selected"':''; ?> value="0" data-photo="<?php echo $this->config->item('photo_empty'); ?>">No PM assigned</option>				
										
							<?php
							foreach( $pm_filter->result() as $row ){ ?>
								<option data-photo="<?php echo $this->jcclass->displayUserImage($row->photo); ?>" value="<?php echo $row->properties_model_id_new; ?>" <?php echo ( $row->properties_model_id_new == $this->input->get_post('pm_id') )?'selected="selected"':''; ?>><?php echo "{$row->fname} {$row->lname}"; ?></option>
							<?php
							}
							?>
						</select>
					</div>
				</div>

				<div class="float-left">
					<label for="exampleSelect" class="col-sm-12 form-control-label">Status</label>
					<div class="col-sm-12" style="width:250px;">
						<select name="status_filter" class="form-control" >
							<option <?php echo ( $this->input->get_post('status_filter')=="" ) ? 'selected' : NULL; ?> value="">---</option>
							<option <?php echo ( $this->input->get_post('status_filter')==2 ) ? 'selected' : NULL; ?> value="2">Approved</option>
							<option <?php echo ( $this->input->get_post('status_filter')==1 ) ? 'selected' : NULL; ?> value="1">Pending</option>
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
						<button type="submit" class="btn btn-inline">Search</button>
					</div>
				</div>

			</div>
		</form>
		</div>
	</header>

	<!-- list -->
	<div class="box-typical-body">
		<?php echo form_open('/reports/qld_upgrade_quotes_bulk_download','id=dl_form') ?>
		<div class="table-responsive">
			<table class="table table-hover main-table">
				<thead>
					<tr>
						
						<th>Address</th>
						<th>Property Manager</th>
						<th>Quote Valid Until</th>
						<th>PDF Quote</th>	
						<?php
						if( $has_240v_rf_brooks == true ){ ?>
							<!-- 240v RF brooks --->	
							<th>&nbsp;</th>						
							<th colspan="1">Brooks Alarms</th>							
						<?php
						}
						?>

						<?php
						if( $this->config->item('disable_cavius_option') == 0 ){
						if( $has_240v_rf_cavius == true ){ ?>
							<!-- 240v RF cavius --->
							<th>&nbsp;</th>		
							<th colspan="1">Cavius Alarms</th>	
						<?php
						}
						}
						?>	
							
						<?php
						if( $has_240v_rf_emerald == true ){ ?>
							<!-- 240v RF brooks --->	
							<th>&nbsp;</th>								
							<th colspan="1"><?php echo $this->system_model->get_quotes_new_name(22); ?> Alarms</th>							
						<?php
						}
						?>

						<th>Approved</th>										
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($list as $row){										

					$qld_approved = ( isset($row->qld_upgrade_quote_approved_ts) && $this->jcclass->isDateNotEmpty($row->qld_upgrade_quote_approved_ts) )?true:false;
					
					// live
					// Hot Property Management Hendra
					$spec_agency_id = 3759;

					// dev
					// Adams Test Agency
					//$spec_agency_id = 1448;
					if( $row->agency_id == $spec_agency_id ){
						$plus90_days_ts = strtotime($row->j_date." +9 months");
					}else{
						//$plus90_days_ts = strtotime($row->j_date." +180 days"); ## disabled as per Bens request > changed from +180 days to static date(31/10/2021)
						//$plus90_days_ts = strtotime("2021-10-31"); ##disabled as per Ben request > change to today + 6 months
						$plus90_days_ts = strtotime( date( 'Y-m-d', strtotime("+6 months", strtotime(date('Y-m-d'))) ) );
					}
					
					$comb_quote_ci_link = "{$this->config->item('crmci_link')}/pdf/view_quote/?job_id={$row->j_id}&qt=combined";

					?>
						<tr class="tbl_list_tr">
							<td>
								<a href="/properties/property_detail/<?php echo $row->property_id ?>" target="blank">
									<?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3}"; ?>
								</a>
							</td>
							<td>
							<?php
									if( isset($row->properties_model_id_new) && $row->properties_model_id_new != 0 && $row->properties_model_fname!="" ){
										echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
										echo "{$row->properties_model_fname} {$row->properties_model_lname}";
									}
									?>
							</td>
							<td class="<?php echo ( date('Y-m-d') > date('Y-m-d',$plus90_days_ts) )?'colorItRed':null; ?>">
								<?php echo ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',$plus90_days_ts):null; ?>
							</td>

							<!-- PDF -->
							<td>

								<a data-toggle="tooltip" title='View Upgrade Quote' href="<?php echo $comb_quote_ci_link; ?>" class="quq_link" target="_blank">
									<i style="font-size:26px;" class="font-icon font-icon-pdf"></i>
								</a>

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
							if( $this->config->item('disable_cavius_option') == 0 )	{													
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
																					

							<td>
								<?php $pref_alarm_make = ( $row->quote_title!='' )? $row->quote_title : $row->pref_alarm_make ?>
								<?php echo ( $qld_approved == true )?"{$pref_alarm_make} quote approved at: ".date('d/m/Y H:i',strtotime($row->qld_upgrade_quote_approved_ts)).'&nbsp;<i class="font-icon font-icon-ok txt-green "></i>':null; ?>
								<input type="hidden" name="job_id" value="<?php echo $row->j_id; ?>" />
								<input type="hidden" class="j_service" value="<?php echo $row->j_service; ?>" />
								<input type="hidden" class="j_date" value="<?php echo $row->j_date; ?>" />								
								<input type="hidden" class="property_id" value="<?php echo $row->property_id; ?>" />
							</td>

						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>

		<div id="mbm_box" class="text-right" style="display:none;">
			<div class="gbox_main" >
				<div class="gbox">
					<button type="button" id="btn_bulk_download" type="button" class="btn"><span class="fa fa-download"></span> Download</button>
				</div>
			</div>
		</div>
				</form>

		<nav aria-label="Page navigation example" style="text-align:center">
			<?php echo $pagination; ?>
		</nav>

		<div class="pagi_count"><?php echo $pagi_count; ?></div>

	</div><!--.box-typical-body-->


</section><!--.box-typical-->



<style>
.colorItRed{
	color: #fa424a;
}
</style>
<script>
jQuery(document).ready(function(){

	//init datepicker
	jQuery('.flatpickr').flatpickr({
		dateFormat: "d/m/Y"
	});

	//select2
	$(".select2-photo").not('.manual').select2({
		templateSelection: select2Photos,
		templateResult: select2Photos
	});

	// set defaut user
	jQuery(".btn_proceed_with_quote").click(function(e){

		var btn_proceed_with_quote_btn_node = jQuery(this);
		var this_row = btn_proceed_with_quote_btn_node.parents("tr:first");
		var property_id = this_row.find(".property_id").val();
		var j_service = this_row.find(".j_service").val();
		var quote_amount = parseFloat(btn_proceed_with_quote_btn_node.attr("data-preferred_alarm_price"));
		var preferred_alarm_id = btn_proceed_with_quote_btn_node.attr("data-preferred_alarm_id");
		var preferred_alarm_make = btn_proceed_with_quote_btn_node.attr("data-preferred_alarm_make");		

		swal({
			title: "Upgrade Quote",
			text: "You've chosen to upgrade using "+preferred_alarm_make+" alarms for $"+quote_amount.toFixed(2)+", are you sure you want to proceed?",
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
					url: "/reports/proceed_with_quote",
					data: {
						property_id: property_id,
						amount: quote_amount.toFixed(2),
						j_service: j_service,
						preferred_alarm_id: preferred_alarm_id
					}
				}).done(function( ret ) {

					swal({
						title: "Success!",
						text: "Job Created",
						type: "success",
						confirmButtonClass: "btn-success"
					},function(){

						location.reload();

					});
				});

			} else { // no

			}


		});

	});

	// copy to clipboard
	jQuery(".upgrade_quote_ctcb").click(function(){

		var upgrade_quote_ctcb_dom = jQuery(this); // curren button DOM
		var upgrade_quote_link_dom = upgrade_quote_ctcb_dom.find('.upgrade_quote_link'); // link to be copied DOM		
		var upgrade_quote_link = upgrade_quote_link_dom.val(); // link to be copied		

		copy_to_clipboard(upgrade_quote_link) 	

	});

});
</script>