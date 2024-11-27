<style>
	.btn-success-outline .font-icon{
		color:#46c35f;
	}
	.btn-success-outline:hover .font-icon{
		color:#fff;
	}
	#move_prop_manager_sel, #move_prop_manager_agency_sel, input.transfer_all_prop_attached{
		display: block!important;
	}
	.transfer_all_prop_attached{
		width: auto;
		float:left;
		margin-right:10px;
	}

	.btn_move_prop_manager:focus, btn_move_prop_manager:active{
		color:#46c35f!important;
	}
	#cant_switch_warning_text{
		margin-top: 10px;;
	}

	div.pac-container {
		z-index: 99999999999 !important;
	}
	
</style>
<div class="box-typical box-typical-padding form-profile vpd_box">
    <!-- <h1 style="text-align:center;"> <?php echo $agency_name; ?>'s Profile</h1> -->
	<h5 class="m-t-lg with-border"><a href="/agency/profile"><?php echo $agency_name; ?>'s Profile</a></h5>

    <!----AGENCY DETAILS---->
	<header class="box-typical-header">
		<div class="tbl-row">
			<div class="tbl-cell tbl-cell-title">
				<h3>AGENCY DETAILS</h3>
			</div>
		</div>
	</header>
	<section class="box-typical-123">

		<div class="box-typical-body">
			<div class="table-responsive">
				<table class="table table-hover main-table">
					<thead>
						<tr>
							<th>Address</th>
							<th>Phone</th>
							<th class="text-center">Office Hours</th>
							<th>Maintenance Program</th>
							<th>Website</th>
							<th>ABN</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<a href="javascript:void(0);"
									data-auto-focus="false"
									data-fancybox data-src="#fancybox_agency_address"
								><?= $agency_info->complete_address ?: 'No Data' ?></a>
							
							</td>
							<td>
								<a href="javascript:void(0);"
									data-auto-focus="false"
									data-fancybox data-src="#fancybox_agency_phone"
								>
									<?= $agency_info->phone ?: 'No Data' ?>
								</a>
							</td>
							<!-- office hours -->
							<td class="text-center">
								<a href="javascript:void(0);"
									data-auto-focus="false"
									data-fancybox data-src="#fancybox_agency_office_hours"
								>
									<?= $agency_info->agency_hours ?: 'No Data' ?>
								</a>
							</td>
							<!-- maintenance program -->
							<td>
								<a href="javascript:void(0);"
									data-auto-focus="false"
									data-fancybox data-src="#fancybox_maintenance_program"
								>
									<?= $agency_info->mName ?: 'No Data' ?>
								</a>
							</td>
						
							<!-- Website -->
							<td>
								<a href="javascript:void(0);"
									data-auto-focus="false"
									data-fancybox data-src="#fancybox_agency_website"
								>
									<?= $agency_info->website ?: 'Add Website' ?>
								</a>
							</td>
							<!-- ABN -->
							<td>
								<a href="javascript:void(0);"
									data-auto-focus="false"
									data-fancybox data-src="#fancybox_agency_abn"
								>
									<?= $agency_info->abn ?: 'Add ABN' ?>
								</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<!-- Agency address fancybox Start -->
		<div id="fancybox_agency_address" style="display:none;">
			<form method="POST" name="address_form" id="address_form">
				<section class="card card-blue-fill">
					<header class="card-header">
						<div class="row">
							<div class="col-md-9"> <span >Address </span> </div>
						</div>
					</header>
					<div class="card-block">
						<div class="form-group">
							<label class="form-label">Google Address Bar</label>
							<input type='text' name='fullAdd' id='fullAdd' class='form-control'  value="<?= $agency_info->complete_address ?>" autocomplete="off">
							<input type='hidden' name='og_fullAdd' id='og_fullAdd' class='form-control vw-pro-dtl-tnt short-fld'  value="<?= $agency_info->complete_address ?>" />
						</div>
						

						<div class="form-row">
							<div class="form-group col-md-2">
									<label for="address_1" class="form-label">No.</label>
									<input type='text' name='address_1' id='address_1' value="<?=$agency_info->address_1?>" class='form-control vw-pro-dtl-tnt short-fld' />
							</div>
							<div class="form-group col-md-4">
									<label for="address_2" class="form-label">Street</label>
									<input type='text' name='address_2' id='address_2' value="<?= $agency_info->address_2 ?>" class='form-control vw-pro-dtl-tnt long-fld streetinput'>
							</div>
							<div class="form-group col-md-2">						
								<label for="address_3" class="form-label">Suburb</label>
								<input type='text'  name='address_3' id='address_3' value="<?= $agency_info->address_3 ?>" class='form-control vw-pro-dtl-tnt big-fld'>					
							</div>

							<div class="form-group col-md-2">
								<div class="form-group">
									<?php if($this->config->item('country') == 1): ?>
										<label class="form-label">State</label>
										<select class="form-control" id="state" name="state">
											<option value="">---Select State---</option>
											<?php
											foreach($country_states as $state){ ?>
												<option value='<?= $state['state']; ?>' 
													<?= ($state['state']== $agency_info->state) ? 'selected="selected"':''; ?>>
													<?= $state['state']; ?>
												</option>
											<?php	  
											}
											?>
										</select>
									<?php else:?>
										<label class="form-label">Region</label>
										<input class="form-control" type="text" name="state" id="state" value="<?= $agency_info->state ?>">
									<?php endif; ?>
										<input type="hidden" name="og_state" id="og_state" value="<?= $agency_info->state ?>">
								</div>
							</div>
						
							<div class="form-group col-md-2">
								<label for="postcode" class="form-label">Poscode</label>
								<input class="form-control" name='postcode' id='postcode' type="text" value="<?= $agency_info->postcode ?>">
							</div>
						</div>

						<!-- hide as requested by peter -->
						<!-- <div class="form-row">
							<div class="form-group col-md-6">
								<label for="postcode_region_name" class="form-label">
									<?= $this->config->item('country') == 2 ? 'District' : 'Region' ?>
								</label>
								<?php if( $agency_info->postcode_region_id!="" ): ?>
									<input class="form-control" readonly="readonly" name='postcode_region_name' id='postcode_region_name' type="text" value="<?= $agency_info->postcode_region_name ?>">
									<input class="form-control" name='og_postcode_region_name' id='og_postcode_region_name' type="hidden" value="<?= $agency_info->postcode_region_name ?>">
								<?php else: ?>
									<p><span class="text-danger">NO</span> region set up for this postcode</p>
								<?php endif; ?>
							
							</div>

						</div> -->
					</div>
				</section>
				<div class="text-right">
					<button type="submit" class="btn btn-primmary" id="btn_update_agency_address">Update</button>                        
				</div>
			</form>
		</div>
		<!-- Agency address fancybox End-->

		<!-- Agency Phone fancybox start -->
		<div id="fancybox_agency_phone" style="display:none; min-width: 500px;">
			<form method="POST" name="phone_form" id="phone_form">
				<section class="card card-blue-fill">
					<header class="card-header">
						<div class="row">
							<div class="col-md-9"> <span >Phone </span> </div>
						</div>
					</header>
					<div class="card-block">
						<div class="form-group">
							<input type='text' name='phone' id='phone' class='form-control vw-pro-dtl-tnt short-fld'  value="<?= $agency_info->phone ?>" />
						</div>

					</div>
				</section>
				<div class="text-right">
					<button type="submit" class="btn btn-primmary" id="btn_update_agency_phone">Update</button>                        
				</div>
			</form>
		</div>
		<!-- Agency Phone fancybox end -->

		<!-- Agency Office Hours fancybox start -->
		<div id="fancybox_agency_office_hours" style="display:none; min-width: 500px;">
			<form method="POST" name="agency_hours_form" id="agency_hours_form">
				<section class="card card-blue-fill">
					<header class="card-header">
						<div class="row">
							<div class="col-md-9"> <span >Office Hours </span> </div>
						</div>
					</header>
					<div class="card-block">
						<div class="form-group">
							<input type='text' name='agency_hours' id='agency_hours' class='form-control'  value="<?= $agency_info->agency_hours ?>" />
						</div>

					</div>
				</section>
				<div class="text-right">
					<button type="submit" class="btn btn-primmary">Update</button>                        
				</div>
			</form>
		</div>
		<!-- Agency Office Hours fancybox end -->

		<!-- Agency Maintenance Program fancybox start -->
		<div id="fancybox_maintenance_program" style="display:none; min-width: 500px;">
			<form method="POST" name="agency_maintenance_form" id="agency_maintenance_form">
				<section class="card card-blue-fill">
					<header class="card-header">
						<div class="row">
							<div class="col-md-9" style="margin-top:9px;"> <span >Maintenance Program </span> </div>
						</div>
						<input type="hidden" name="agency_maintenance_id" value="<?=$agency_maintenance->agency_maintenance_id?>">
					</header>
					<div class="card-block">
						<div class="form-group-row">
							<label for="maintenance_provider" class="form-control-label">Maintenance Provider</label>
							<select name="maintenance_provider" id="maintenance_provider" class="form-control">
								<option value="">None</option>
                                <option value="1" <?= $agency_maintenance->surcharge == 1 ? 'selected': ''?>>Yes</option>
                                <option value="0" <?= $agency_maintenance->surcharge == 0 ? 'selected': ''?>>No</option>
							</select>
						</div>
						<div id="maintenance_div_form" class="<?= $agency_maintenance  ? 'd-block' : 'd-none'?>">

						
							<div class="form-group mt-3">
								<label for="">Apply Surcharge to all Invoices?</label>
								<select name="surcharge" id="surcharge" class="form-control">
									<option value="1" <?= $agency_maintenance->surcharge == 1 ? 'selected': ''?>>Yes</option>
									<option value="0" <?= $agency_maintenance->surcharge == 0 ? 'selected': ''?>>No</option>
								</select>
							</div>
							<div class="form-group mt-3">
								<label for="">Display message on all Invoices?</label>
								<select name="display_surcharge" id="display_surcharge" class="form-control">
									<option value="1" <?= $agency_maintenance->display_surcharge == 1 ? 'selected': ''?>>Yes</option>
									<option value="0" <?= $agency_maintenance->display_surcharge == 0 ? 'selected': ''?>>No</option>
								</select>
							</div>
							<div class="form-group mt-3">
								<label for="">Surcharge</label>
								<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" id="surcharge-addon">$</span>
								</div>
								<input type="text" class="form-control" aria-label="Username" aria-describedby="surcharge-addon" name="surcharge_price" id="surcharge_price" placeholder="0.00" value="<?=$agency_maintenance->price?>" />
								</div>
							</div>

							<div class="form-group mt-3">
								<label for="">Invoice Message</label>
								<textarea name="surcharge_msg" id="surcharge_msg" class="form-control" rows="3"><?=$agency_maintenance->surcharge_msg?></textarea>
								<p class="text-danger font-italic mt-2">All invoices will divert to platform invoicing to process via <span id="mp_dynamic_name"><?= $agency_info->mName ?></span></p>
							</div>
						</div>
					</div>
				</section>
				<div class="text-right">
					<button type="submit" class="btn btn-primmary">Update</button>                        
				</div>
			</form>
		</div>
		<!-- Agency Maintenance Program fancybox end -->

		
		<!-- Agency Website fancybox start -->
		<div id="fancybox_agency_website" style="display:none; min-width: 500px;">
			<form method="POST" name="agency_website_form" id="agency_website_form">
				<section class="card card-blue-fill">
					<header class="card-header">
						<div class="row">
							<div class="col-md-9"> <span >Agency Website </span> </div>
						</div>
					</header>
					<div class="card-block">
						<div class="form-group">
							<input type='text' name='website' id='website' class='form-control'  value="<?= $agency_info->website ?>" />
						</div>

					</div>
				</section>
				<div class="text-right">
					<button type="submit" class="btn btn-primmary">Update</button>                        
				</div>
			</form>
		</div>
		<!-- Agency Website fancybox end -->

		<!-- Agency ABN fancybox start -->
		<div id="fancybox_agency_abn" style="display:none; min-width: 500px;">
			<form method="POST" name="agency_abn_form" id="agency_abn_form">
				<section class="card card-blue-fill">
					<header class="card-header">
						<div class="row">
							<div class="col-md-9"> <span >ABN </span> </div>
						</div>
					</header>
					<div class="card-block">
						<div class="form-group">
							<input type='text' name='abn' id='abn' class='form-control'  value="<?= $agency_info->abn ?>" />
						</div>
					</div>
				</section>
				<div class="text-right">
					<button type="submit" class="btn btn-primmary">Update</button>                        
				</div>
			</form>
		</div>
		<!-- Agency ABN fancybox end -->


		<div class="box-typical-body">
			<div class="table-responsive">
				<table class="table table-bordered main-table">
					<thead>
						<tr>
							<th class=" text-center w-25">BPAY Displayed on all Invoices?</th>
							<th class=" text-center w-25">Receive a copy of all Entry Notices?</th>
							<th class=" text-center w-25">Receive an additional key list email at 48 hours?(Standard 24 hours)</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<div class="form-group">
                                    <a href="javascript:void(0)"
                                       data-auto-focus="false"
                                       data-fancybox data-src="#agency_bpay_display_invoices"><?php echo yes_no($agency_info->display_bpay); ?></a>
								</div>
							</td>
							<td>
								<div class="form-group">
                                    <a href="#javascript:void(0)"
                                       data-auto-focus="false"
                                       data-fancybox data-src="#send_entry_notice_to_agency"><?php echo yes_no($agency_info->send_en_to_agency); ?></a>
								</div>
							</td>
							<td>
								<div class="form-group">
                                    <a href="#javascript:void(0)"
                                       data-auto-focus="false"
                                       data-fancybox data-src="#send_48_hr_key_to_agency"><?php echo yes_no($agency_info->send_48_hr_key); ?></a>
								</div>
							</td>
						
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="box-typical-body">
			<div class="table-responsive">
				<table class="table table-hover main-table">
					<thead>
						<tr>
							<th>Properties Under Management  </th>
							<th>Email to send Reports and Keys to be collected</th>
							<th>Email to send Invoices, Certificates and Statements</th>
							<!-- <th>Trust Account Software</th>
							<th>Edit</th> -->
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<a href="javascript:void(0)"
									data-auto-focus="false"
									data-fancybox data-src="#fancybox_total_properties"
									class="font-weight-bold"
								>
									<?= $agency_info->tot_properties ?: 'No Data' ?>
								</a>
								
								<!-- <span class="agency_field_lbl"><?php echo $agency_info->tot_properties ?></span>
								<input class="form-control agency_field_hid" type="text" id="tot_prop" name="tot_prop" placeholder="Total Properties Managed" value="<?php echo $agency_info->tot_properties ?>"> -->
							</td>
							<td>
								<a href="javascript:void(0)"
									data-auto-focus="false"
									data-fancybox data-src="#fancybox_agency_emails"
								>
									<?php if($agency_info->agency_emails): ?>
										<?= nl2br($agency_info->agency_emails); ?>
									<?php else: ?>
										<span>No Data</span>
									<?php endif; ?>
								</a>
								<!-- <span class="agency_field_lbl"><?php echo nl2br($agency_info->agency_emails); ?></span>
								<textarea placeholder="Agency Emails" name="agency_email" id="agency_email" class="form-control agency_field_hid"><?php echo $agency_info->agency_emails; ?></textarea> -->
							</td>
							<td>
								<a href="javascript:void(0)"
									data-auto-focus="false"
									data-fancybox data-src="#fancy_account_emails"
								>
									<?php if($agency_info->account_emails): ?>
										<?= nl2br($agency_info->account_emails); ?>
									<?php else: ?>
										<span>No Data</span>
									<?php endif; ?>
								</a>
								<!-- <span class="agency_field_lbl"><?php echo nl2br($agency_info->account_emails); ?></span>
								<textarea placeholder="Accounts Emails" name="acc_email" id="acc_email" class="form-control agency_field_hid"><?php echo $agency_info->account_emails; ?></textarea> -->
							</td>
							<!-- <td>
								<span class="agency_field_lbl"><?php echo $agency_info->tsa_name; ?></span>
								<select name="trust_account_software" id="tsa" class="form-control trust_account_software agency_field_hid">
									<option value="">----</option>
									<option value="1" <?php echo ($agency_info->trust_account_software==1)?'selected="selected"':''; ?>>REST</option>
									<option value="2" <?php echo ($agency_info->trust_account_software==2)?'selected="selected"':''; ?>>Property Tree</option>
									<option value="3" <?php echo ($agency_info->trust_account_software==3)?'selected="selected"':''; ?>>Console</option>
									<option value="4" <?php echo ($agency_info->trust_account_software==4)?'selected="selected"':''; ?>>Palace</option>
									<option value="5" <?php echo ($agency_info->trust_account_software==5)?'selected="selected"':''; ?>>Sherlock</option>
									<option value="6" <?php echo ($agency_info->trust_account_software==6)?'selected="selected"':''; ?>>Palace Liquid</option>
									<option value="7" <?php echo ($agency_info->trust_account_software==7)?'selected="selected"':''; ?>>PropertyMe</option>
									<option value="-1" <?php echo ($agency_info->trust_account_software==-1)?'selected="selected"':''; ?>>Other</option>
								</select>
							</td>
							<td>
								<a href="javascript:void(0);" class="edit_icon_link">
									<span class="font-icon font-icon-pencil"></span>
								</a>
								<button type="button" class="btn btn-primary update_icon_link" id="update_agency_btn">Update</button>
								<button type="button" class="btn btn-danger cancel_icon_link">Cancel</button>
							</td> -->
						</tr>
					</tbody>
				</table>

				<?php
				$delivered_to_txt = null;
				$api_integrated = ( $ageny_pref_row->api_name != '' )?$ageny_pref_row->api_name:'API';
				
				if( $ageny_pref_row->is_invoice == 1 && $ageny_pref_row->is_certificate == 1 ){
					$delivered_to_txt = "Invoices and Certificates will be delivered to {$api_integrated}";
				}else if( $ageny_pref_row->is_invoice == 1 ){
					$delivered_to_txt = "Invoices will be delivered to {$api_integrated}";
				}else if( $ageny_pref_row->is_certificate == 1 ){
					$delivered_to_txt = "Certificates will be delivered to {$api_integrated}";
				} 
				?>

				<span class="text-danger font-italic"><?php echo $delivered_to_txt; ?></span>

			</div>
		</div>

	</section>

	<!-- Agency total portfolio fancybox start -->
	<div id="fancybox_total_properties" style="display:none; min-width: 500px;">
		<form method="POST" name="total_properties_form" id="total_properties_form">
			<section class="card card-blue-fill">
				<header class="card-header">
					<div class="row">
						<div class="col-md-9"> <span >Properties under Management </span> </div>
					</div>
				</header>
				<div class="card-block">
					<div class="form-group">
						<!-- <label for="phone" class="form-label">Contact Phone</label> -->
						<input type='number' name='tot_properties' id='tot_properties' class='form-control'  value="<?= $agency_info->tot_properties ?>" />
					</div>

				</div>
			</section>
			<div class="text-right">
				<button class="btn btn-primmary" id="btn_update_agency_address">Update</button>                        
			</div>
		</form>
	</div>
	<!-- Agency total portfolio fancybox end -->
	<!-- Agency emails fancybox start -->
	<div id="fancybox_agency_emails" style="display:none; min-width: 600px;">
		<form method="POST" name="agency_emails_form" id="agency_emails_form">
			<section class="card card-blue-fill">
				<header class="card-header">
					<div class="row">
						<div class="col-md-9"> <span >Email to send Reports and Keys to be collected </span> </div>
					</div>
				</header>
				<div class="card-block">
					<div class="form-group">
						<label for="agency_emails">Agency emails</label>
						<textarea rows="5" name='agency_emails' id='agency_emails' class='form-control'><?= $agency_info->agency_emails ?></textarea>
						<small><strong>(Reports, Key Sheet)</strong>&nbsp;<small style="color: red;">(one email address per line)</small></small>
					</div>

				</div>
		
			</section>
			<div class="text-right">
				<button class="btn btn-primmary" id="btn_update_agency_address">Update</button>                        
			</div>
		</form>
	</div>
	<!-- Agency emails fancybox end -->

	<!-- Agency account emails fancybox start -->
	<div id="fancy_account_emails" style="display:none; min-width: 600px;">
		<form method="POST" name="account_emails_form" id="account_emails_form">
			<section class="card card-blue-fill">
				<header class="card-header">
					<div class="row">
						<div class="col-md-9"> <span >Email to send Invoices, Certificates and Statements</span> </div>
					</div>
				</header>
				<div class="card-block">
					<div class="form-group">
						<label for="account_emails">Account emails</label>
						<textarea rows="5" name='account_emails' id='account_emails' class='form-control'><?= $agency_info->account_emails ?></textarea>
						<small><strong>(Invoices, Certificates)</strong> <small style="color: red;">(one email address per line)</small></small>
					</div>

				</div>
		
			</section>
			<div class="text-right">
				<button class="btn btn-primmary" id="btn_update_agency_address">Update</button>                        
			</div>
		</form>
	</div>
	<!-- Agency account emails fancybox end -->

	<!-- AGENCY CONTACTS -->
	<div class="row">
		<div class="col-12">
			<header class="box-typical-header" style="margin-top: 15px;">
				<div class="tbl-row">
					<div class="tbl-cell tbl-cell-title">
						<h3>AGENCY CONTACTS</h3>
					</div>
				</div>
			</header>

			<section class="box-typical-123 agency_contact_div">
				<div class="box-typical-body-123">
					<div class="table-responsive">
						<table class="table table-hover main-table">
							<thead>
								<!-- Main -->
								<tr>
									<th>&nbsp;</th>
									<th>Name</th>
									<th>Phone</th>
									<th>Email</th>
									<!-- <th>Edit</th> -->
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<header class="box-typical-header">
											<div class="tbl-row">
												<div class="tbl-cell tbl-cell-title">
													<h3 class="text-dark">Main Contact for <?= $this->config->item('COMPANY_NAME_SHORT') ?></h3>
												</div>
											</div>
										</header>
									</td>
									<td>
										<a href="javascript:void(0);"
											data-auto-focus="false"
											data-fancybox data-src="#fancybox_agency_contact"
											class="text-dark"
										>
											<?php if($agency_info->contact_first_name || $agency_info->contact_last_name) :?>
												<span class="agency_field_lbl"><?= $agency_info->contact_first_name; ?></span>
												<span class="agency_field_lbl"><?= $agency_info->contact_last_name; ?></span>
											<?php else: ?>
												<span>No Data</span>
											<?php endif; ?>
										</a>
										
										<!-- <div class="row">
											<div class="col-sm-6">
												<input type="text" name="ac_fname" id="ac_fname" value="<?php echo $agency_info->contact_first_name; ?>" placeholder="First Name" class="form-control agency_field_hid">
											</div>
											<div class="col-sm-6">
												<input type="text" name="ac_lname" id="ac_lname" value="<?php echo $agency_info->contact_last_name; ?>" placeholder="Last Name" class="form-control agency_field_hid">
											</div>
										</div> -->
									</td>
									<td>
										<a href="javascript:void(0);"
											data-auto-focus="false"
											data-fancybox data-src="#fancybox_agency_contact"
											class="text-dark"
										>
											<span class="agency_field_lbl"><?= $agency_info->contact_phone ?: 'No Data'; ?></span>
										</a>

										<!-- <input type="text" name="ac_contact" id="ac_contact" value="<?php echo $agency_info->contact_phone; ?>" placeholder="Phone" class="form-control agency_field_hid"> -->
									</td>
									<td>
										<a href="javascript:void(0);"
											data-auto-focus="false"
											data-fancybox data-src="#fancybox_agency_contact"
											class="text-dark"
										>
											<span class="agency_field_lbl"><?= $agency_info->contact_email ?: 'No Data'; ?></span>
										</a>
										<!-- <input type="text" name="ac_email" id="ac_email" value="<?php echo $agency_info->contact_email; ?>" placeholder="Email" class="form-control agency_field_hid"> -->
									</td>
									<!-- <td class="table-photo">
										<a href="javascript:void(0);" class="edit_icon_link">
											<span class="font-icon font-icon-pencil"></span>
										</a>
										<div class="edit_btn_div">
											<button type="button" class="btn btn-primary update_icon_link" id="main_ac_update_btn">Update</button>
											<button type="button" class="btn btn-danger cancel_icon_link">Cancel</button>
										</div>
									</td> -->
								</tr>

								<!-- Accounts -->
								<tr>
									<td>
										<header class="box-typical-header">
											<div class="tbl-row">
												<div class="tbl-cell tbl-cell-title">
													<h3 class="text-dark">Accounts Contact</h3>
												</div>
											</div>
										</header>
									</td>
									<td>
										<a href="javascript:void(0);"
											data-auto-focus="false"
											data-fancybox data-src="#fancybox_accounts"
											class="text-dark"
										>
											<span class="agency_field_lbl"><?= $agency_info->accounts_name ?: 'No Data' ?></span>
										</a>
										<!-- <input type="text" name="acc_contact_name" id="acc_contact_name" value="<?php echo $agency_info->accounts_name; ?>" placeholder="Name" class="form-control agency_field_hid"> -->
									</td>
									<td>
										<a href="javascript:void(0);"
											data-auto-focus="false"
											data-fancybox data-src="#fancybox_accounts"
											class="text-dark"
										>
											<span class="agency_field_lbl"><?= $agency_info->accounts_phone ?: 'No Data' ?></span>
										</a>
										<!-- <input type="text" name="acc_phone" id="acc_phone" value="<?php echo $agency_info->accounts_phone; ?>" placeholder="Phone" class="form-control agency_field_hid"> -->
									</td>
									<td>&nbsp;</td>
									<!-- <td class="table-photo">
										<a href="javascript:void(0);" class="edit_icon_link">
											<span class="font-icon font-icon-pencil"></span>
										</a>
										<button type="button" class="btn btn-primary update_icon_link" id="accounts_ac_update_btn">Update</button>
										<button type="button" class="btn btn-danger cancel_icon_link">Cancel</button>
									</td> -->
								</tr>

								<!-- Office Start -->
								<tr>
									<td>
										<header class="box-typical-header">
											<div class="tbl-row">
												<div class="tbl-cell tbl-cell-title">
													<h3 class="text-dark">Tenant Details</h3>
												</div>
											</div>
										</header>
									</td>
									<td>
										<a href="javascript:void(0);"
											data-auto-focus="false"
											data-fancybox data-src="#fancybox_tenants_contact"
											class="text-dark"
										>
											<span class="agency_field_lbl"><?= $agency_info->tenant_details_contact_name ?: 'No Data'; ?></span>
										</a>
									</td>
									<td>
										<a href="javascript:void(0);"
											data-auto-focus="false"
											data-fancybox data-src="#fancybox_tenants_contact"
											class="text-dark"
										>
											<span class="agency_field_lbl"><?= $agency_info->tenant_details_contact_phone ?: 'No Data'; ?></span>
										</a>
									</td>
									<td>&nbsp;</td>
								</tr>
								<!-- Office End -->
							</tbody>
						</table>
					</div>
				</div>
			</section>
			<!-- Agency Main Contact name fancybox start -->
			<div id="fancybox_agency_contact" style="display:none; min-width: 500px;">
				<form method="POST" name="agency_contact_form" id="agency_contact_form">
					<section class="card card-blue-fill">
						<header class="card-header">
							<div class="row">
								<div class="col-md-9" style="margin-top:9px;"> <span >Agency Contact </span> </div>
							</div>
						</header>
						<div class="card-block">
							<div class="form-group">
								<label for="contact_first_name" class="form-label">First Name</label>
								<input type='text' name='contact_first_name' id='contact_first_name' class='form-control'  value="<?= $agency_info->contact_first_name ?>" />
							</div>
							<div class="form-group">
								<label for="contact_last_name" class="form-label">Last Name</label>
								<input type='text' name='contact_last_name' id='contact_last_name' class='form-control'  value="<?= $agency_info->contact_last_name ?>" />
							</div>
							<div class="form-group">
								<label for="contact_phone" class="form-label">Phone</label>
								<input type='text' 
									name='contact_phone' 
									id='contact_phone' 
									class='form-control'  
									value="<?= $agency_info->contact_phone ?>" 
								/>
							</div>
							<div class="form-group">
								<label for="contact_email" class="form-label">Email</label>
								<input type='email' 
									name='contact_email' 
									id='contact_email' 
									class='form-control'  
									value="<?= $agency_info->contact_email ?>" 
								/>
							</div>
							
						</div>
					</section>
					<div class="text-right">
						<button class="btn btn-primmary">Update</button>                        
					</div>
				</form>
			</div>
			<!-- Agency Main Contact name fancybox end -->
						
			<!-- Accounts Contact name fancybox start -->
			<div id="fancybox_accounts" style="display:none; min-width: 500px;">
				<form method="POST" name="accounts_contact_form" id="accounts_contact_form">
					<section class="card card-blue-fill">
						<header class="card-header">
							<div class="row">
								<div class="col-md-9" style="margin-top:9px;"> <span >Account Contact </span> </div>
							</div>
						</header>
						<div class="card-block">
							<div class="form-group">
								<label for="accounts_name" class="form-label">Email</label>
								<input type='text' 
									name='accounts_name' 
									id='accounts_name' 
									class='form-control'  
									value="<?= $agency_info->accounts_name ?>" 
								/>
							</div>
							<div class="form-group">
								<label for="accounts_phone" class="form-label">Phone</label>
								<input type='text' 
									name='accounts_phone' 
									id='accounts_phone' 
									class='form-control'  
									value="<?= $agency_info->accounts_phone ?>" 
								/>
							</div>
						</div>
					</section>
					<div class="text-right">
						<button class="btn btn-primmary">Update</button>                        
					</div>
				</form>
			</div>
			<!-- Accounts Contact Name fancybox end -->

				
			<!-- Tenants Contact name fancybox start -->
			<div id="fancybox_tenants_contact" style="display:none; min-width: 500px;">
				<form method="POST" name="tenants_contact_form" id="tenants_contact_form">
					<section class="card card-blue-fill">
						<header class="card-header">
							<div class="row">
								<div class="col-md-9" style="margin-top:9px;"> <span >Tenants Contact</span> </div>
							</div>
						</header>
						<div class="card-block">
							<div class="form-group">
								<label for="tenant_details_contact_name" class="form-label">Name</label>
								<input type='text' 
									name='tenant_details_contact_name' 
									id='tenant_details_contact_name' 
									class='form-control'  
									value="<?= $agency_info->tenant_details_contact_name ?>" 
								/>
							</div>
							<div class="form-group">
								<label for="tenant_details_contact_phone" class="form-label">Phone</label>
								<input type='text' 
									name='tenant_details_contact_phone' 
									id='tenant_details_contact_phone' 
									class='form-control'  
									value="<?= $agency_info->tenant_details_contact_phone ?>" 
								/>
							</div>
						</div>
					</section>
					<div class="text-right">
						<button class="btn btn-primmary">Update</button>                        
					</div>
				</form>
			</div>
			<!-- Tenants Contact Name fancybox end -->
            
            <!-- Display BPAY invoices -->
            <div id="agency_bpay_display_invoices" style="display:none; min-width: 500px;">
                <form method="POST" name="agency_bpay_display_invoices" id="agency_bpay_display_invoices_form">
                    <section class="card card-blue-fill">
                        <header class="card-header">
                            <div class="row">
                                <div class="col-md-9"> <span >BPAY Displayed on all Invoices? </span> </div>
                            </div>
                        </header>
                        <div class="card-block">
                            <div class="form-group-row">
                                <select class="form-control" id="display_bpay" name="display_bpay">
                                    <option value="1" <?= $agency_info->display_bpay == 1 ? "selected" : ""?>>YES</option>
                                    <option value="0" <?= $agency_info->display_bpay == 0 ? "selected" : ""?>>NO</option>
                                </select>
                            </div>
                        </div>
                    </section>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primmary">Update</button>
                    </div>
                </form>
            </div>
            <!-- Display BPAY invoices END -->
            
            <!-- Receive an additional key list email at 48 hours?(Standard 24 hours) -->
            <div id="send_entry_notice_to_agency" style="display:none; min-width: 500px;">
                <form method="POST" name="send_en_to_agency_form" id="send_en_to_agency_form">
                    <section class="card card-blue-fill">
                        <header class="card-header">
                            <div class="row">
                                <div class="col-md-9"> <span>Receive a copy of all Entry Notices? </span> </div>
                            </div>
                        </header>
                        <div class="card-block">
                            <div class="form-group-row">
                                <select class="form-control" id="send_en_to_agency" name="send_en_to_agency">
                                    <option value="1" <?= $agency_info->send_en_to_agency == 1 ? "selected" : ""?>>YES</option>
                                    <option value="0" <?= $agency_info->send_en_to_agency == 0 ? "selected" : ""?>>NO</option>
                                </select>
                            </div>
                        </div>
                    </section>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primmary">Update</button>
                    </div>
                </form>
            </div>
            <!-- Receive an additional key list email at 48 hours?(Standard 24 hours) END -->

            <!-- Display BPAY invoices -->
            <div id="send_48_hr_key_to_agency" style="display:none; min-width: 500px;">
                <form method="POST" name="agency_bpay_display_invoices" id="agency_bpay_display_invoices_form">
                    <section class="card card-blue-fill">
                        <header class="card-header">
                            <div class="row">
                                <div class="col-md-9"> <span >Receive an additional key list email at 48 hours?(Standard 24 hours) </span> </div>
                            </div>
                        </header>
                        <div class="card-block">
                            <div class="form-group-row">
                                <select class="form-control" id="send_48_hr_key" name="send_48_hr_key">
                                    <option value="1" <?=$agency_info->send_48_hr_key == 1 ? "selected" : ""?>>YES</option>
                                    <option value="0" <?=$agency_info->send_48_hr_key == 0 ? "selected" : ""?>>NO</option>
                                </select>
                            </div>
                        </div>
                    </section>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primmary">Update</button>
                    </div>
                </form>
            </div>
            <!-- Display BPAY invoices END -->

		</div>
	</div>


	<!-- PROPERTY MANAGERS -->
	<div class="row">

		<div class="col-lg-6">
			<header class="box-typical-header">
				<div class="tbl-row">
					<div class="tbl-cell tbl-cell-title">
						<h3>PROPERTY MANAGERS</h3>
					</div>
				</div>
			</header>
			<section class="box-typical-123">
				<div class="box-typical-body">
					<div class="table-responsive">
						<table class="table table-hover main-table">
							<thead>
								<tr>
									<th>Name</th>
									<th>Email</th>
									<th>View</th>
								</tr>
							</thead>
							<tbody>
							<?php if($agency_info->allow_indiv_pm == 1){
							foreach($propeties_pm as $row){
							?>
								<tr>
									<td>
										<?php
											echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
											echo "{$row->fname} {$row->lname}";
										?>
									</td>
									<td>
										<?php echo $row->email; ?>
									</td>
									<td class="table-photo">
										<a data-toggle="tooltip" title="View" class="a_link" href="/user_accounts/my_profile/<?php echo $row->agency_user_account_id; ?>">
											<span class="font-icon font-icon-eye"></span>
										</a>
									</td>
								</tr>
							<?php
								}
							}
							?>

							</tbody>
						</table>
					</div>
				</div>
			</section>

			<a href="/properties/edit_pm_properties">
				<button type="button" class="btn btn-inline btn-danger-outline">
					<span class="fa fa-pencil"></span>
					Assign PMs to Properties
				</button>
			</a>
			<a href="/user_accounts/add">
				<button type="button" class="btn btn-inline btn-primary-outline">
					<li class="fa fa-plus"></li>
					Property Manager
				</button>
			</a>
			
			<?php if( ( !in_array($this->session->agency_id,$this->config->item('harris_agencies')) && $user->user_type==1 && $user->alt_agencies!="" ) || ( in_array($this->session->agency_id,$this->config->item('harris_agencies')) && $user->user_type==1 )  ){ //dont show if Harries agency not admin ?>
			<a href="javascript:;" data-fancybox data-src="#move_prop_manager_box" class="btn btn-inline btn-success-outline btn_move_prop_manager">
				<span class="font-icon font-icon-revers"></span>
				Move Property Manager
			</a>
			<?php } ?>
		
			<div style="display:none;margin-top:20px;width:600px;" id="move_prop_manager_box" class="row">
				<div class="col-md-12">
				<div class="form-group"><h3>Move Property Manager</h3></div>
					<div class="form-group">
						<label class="form-control-label">Property Manager</label>
						<select class="form-control" id="move_prop_manager_sel">
							<option value="">Please Select</option>
							<?php 
								foreach($propeties_pm as $row_opt){ 
								if( $row_opt->alt_agencies=="" && !in_array($this->session->agency_id,$this->config->item('harris_agencies')) ){
									$has_no_alt_text = "(Contact ".$this->config->item('COMPANY_NAME_SHORT')." to move)";
								}else{
									$has_no_alt_text = "";
								}
							?>
								<option data-text='<?php echo "{$row_opt->fname} {$row_opt->lname}" ?>' value="<?php echo $row_opt->agency_user_account_id ?>"><?php echo "{$row_opt->fname} {$row_opt->lname} {$has_no_alt_text}" ?></option>
							<?php } ?>
						</select>
						<div style="display:none;" id="cant_switch_warning_text"></div>
					</div>
					<div id="move_prop_manager_agency_sel_box" style="display:none;">
						<div class="form-group">
							<label class="form-control-label">New Agency</label>
							<select class="form-control" id="move_prop_manager_agency_sel"></select>
						</div>
						
						<div class="form-group transfer_radio_box" style="display:none;">
							<label style="margin-bottom:10px;" class="form-control-label">Would you like to transfer ALL of the properties attached to <span id='transfer_user'></span> to <span id='transfer_agency'></span> also?</label>
							<div class="left"><input style="margin-top:3px;" type="radio" class="transfer_all_prop_attached form-control" name="transfer_all_prop_attached" value="1">&nbsp;Yes&nbsp;<span style="display:none;" class="txt-red move_user_and_properties"></span></div>
							<div class="left"><input style="margin-top:3px;" type="radio" class="transfer_all_prop_attached form-control" name="transfer_all_prop_attached" value="0">&nbsp;No&nbsp;<span style="display:none;" class="txt-red move_only_user_red_text"></span></div>
						</div>

						<div class="form-group">
							<input type="hidden" id="orig_agency_name" value="">
							<input type="hidden" id="orig_agency_id" value="">
							<button style="display:none;" type="button" id="btn_change_to_this_agency" class="btn">Change to this Agency</button>
						</div>
					</div>
				</div>
			</div>

		</div>

		



		<!-- ACCOUNT MANAGER -->

		<div class="col-lg-6">
			<header class="box-typical-header">
				<div class="tbl-row">
					<div class="tbl-cell tbl-cell-title">
						<h3>ACCOUNT MANAGER</h3>
					</div>
				</div>
			</header>
			<section class="box-typical-123">
				<div class="box-typical-body">
					<div class="table-responsive">
						<table class="table table-hover main-table">
							<tbody>
								<tr>
									<td class=" text-center" colspan='2'>
										<?php
										if( isset($agency_info->saProfilePic) && $agency_info->saProfilePic != '' ){ ?>
											<img class="profile_pic" src="<?php echo $this->config->item('crmci_link'); ?>/images/staff_profile/<?php echo $agency_info->saProfilePic; ?>" alt="profile picture">
										<?php
										}else{ ?>
											<img class="profile_pic" src="/images/avatar-2-64.png" alt="profile picture">
										<?php
										}
										?>
										&nbsp;&nbsp;
									</td>

								</tr>

									<tr><th>Name</th><td><?php echo "{$agency_info->saFirstname} {$agency_info->saLastname}"; ?></td>
									<tr><th>Email</th><td><?php echo $agency_info->saEmail; ?></td></tr>
									<tr><th>Phone</th><td><?php echo $agency_info->saContactNumber; ?></td></tr>

							</tbody>
						</table>
					</div>
				</div>
			</section>
		</div>
	</div>

	<!--
    <div class="height40"></div>
    <div class="form-group row">

        <div class="col-md-7">
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" id="agency_id" name="agency_id" value="<?php echo $this->session->agency_id; ?>" />
                    <button type="submit" id="btn_update_profile" class="btn_update_property btn btn-inline">Update Profile</button>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            &nbsp;
        </div>

    </div>
	-->

</div>


<style>
.a_link{
	margin-left: 3px;
}
.font-icon-pencil::before {
    font-size: 14px;
}
.row input,
.row select,
.row textarea,
.cancel_icon_link,
.update_icon_link,
.agency_field_hid
{
	display: none;
}
.edit_icon_link{
	margin-left: 5px;
}
.agency_contact_div .tbl-cell-title{
	padding: 0 !important;
}

.profile_pic{
	/* border-radius: 50%;
	width: 200px;
	height: 200px;
	object-fit: contain;
	border-color: #00a1f3!important; */
	max-width: 200px;
}
td.edit_btn_col_fixed_width{
	width:188px!important;
}
.header_update_th{
	width: 30%;
}
</style>
<script type="text/javascript">


jQuery(document).ready(function(){






	<?php
	if( $this->session->flashdata('agency_profile_update_success') == 1 ){ ?>
		// update success
		swal({
			title: "Success!",
			text: "Your Profile has been successfully updated",
			type: "success",
			confirmButtonClass: "btn-success"
		});
	<?php
	}
	?>


	// update Main Agency Contact
	jQuery("#update_agency_btn").click(function(e){

		var tot_prop = jQuery(this).parents("tr:first").find("#tot_prop").val();
		var agency_email = jQuery(this).parents("tr:first").find("#agency_email").val();
		var acc_email = jQuery(this).parents("tr:first").find("#acc_email").val();
		var tsa = jQuery(this).parents("tr:first").find("#tsa").val();

		var err = "";
		if(agency_email==""){
			err += "Agency Emails must not be empty \n";
		}
		if(acc_email==""){
			err += "Account Emails must not be empty \n";
		}

		if(err!=""){
			swal('',err,'error');
			return false;
		}

		jQuery("#preloader").delay(200).fadeIn("slow");
		jQuery.ajax({
			type: "POST",
			url: "/agency/update_profile",
			data: {
				tot_prop: tot_prop,
				agency_email: agency_email,
				acc_email: acc_email,
				tsa: tsa
			}
		}).done(function( ret ) {

			jQuery("#preloader").delay(200).fadeOut("slow");
			if( parseInt(ret) == 1 ){
				location.reload();
			}else{

				swal({
					title: "Error!",
					text: "Error: Please try again!",
					type: "error",
					confirmButtonClass: "btn-success"
				});

			}


		});


	});


	// update Main Agency Contact
	jQuery("#main_ac_update_btn").click(function(e){

		var ac_fname = jQuery(this).parents("tr:first").find("#ac_fname").val();
		var ac_lname = jQuery(this).parents("tr:first").find("#ac_lname").val();
		var ac_contact = jQuery(this).parents("tr:first").find("#ac_contact").val();
		var ac_email = jQuery(this).parents("tr:first").find("#ac_email").val();

		jQuery("#preloader").delay(200).fadeIn("slow");
		jQuery.ajax({
			type: "POST",
			url: "/agency/update_main_agency_contact",
			data: {
				ac_fname: ac_fname,
				ac_lname: ac_lname,
				ac_contact: ac_contact,
				ac_email: ac_email
			}
		}).done(function( ret ) {

			jQuery("#preloader").delay(200).fadeOut("slow");
			if( parseInt(ret) == 1 ){
				location.reload();
			}else{

				swal({
					title: "Error!",
					text: "Error: Please try again!",
					type: "error",
					confirmButtonClass: "btn-success"
				});

			}


		});


	});


	// update Accounts Agency Contact
	jQuery("#accounts_ac_update_btn").click(function(e){

		var acc_contact_name = jQuery(this).parents("tr:first").find("#acc_contact_name").val();
		var acc_phone = jQuery(this).parents("tr:first").find("#acc_phone").val();

		jQuery("#preloader").delay(200).fadeIn("slow");
		jQuery.ajax({
			type: "POST",
			url: "/agency/update_accounts_agency_contact",
			data: {
				acc_contact_name: acc_contact_name,
				acc_phone: acc_phone
			}
		}).done(function( ret ) {


			jQuery("#preloader").delay(200).fadeOut("slow");
			if( parseInt(ret) == 1 ){
				location.reload();
			}else{

				swal({
					title: "Error!",
					text: "Error: Please try again!",
					type: "error",
					confirmButtonClass: "btn-success"
				});

			}



		});


	});


	jQuery(".edit_icon_link").click(function(){
		jQuery(this).parents("tr:first").find(".agency_field_hid").show();
		jQuery(this).parents("tr:first").find(".agency_field_lbl").hide();
		jQuery(this).parents("tr:first").find(".cancel_icon_link").show();
		jQuery(this).parents("tr:first").find(".update_icon_link").show();
		jQuery(this).parents("tr:first").find(".edit_icon_link").hide();

		//add class for button col width
		jQuery(this).parent("td").addClass('edit_btn_col_fixed_width');

	});


	jQuery(document).on('click','.cancel_icon_link',function(){

		jQuery(this).parents("tr:first").find(".agency_field_hid").hide();
		jQuery(this).parents("tr:first").find(".agency_field_lbl").show();
		jQuery(this).parents("tr:first").find(".cancel_icon_link").hide();
		jQuery(this).parents("tr:first").find(".update_icon_link").hide();
		jQuery(this).parents("tr:first").find(".edit_icon_link").show();

		//add class for button col width
		jQuery(this).parents("td").removeClass('edit_btn_col_fixed_width');

	});

	jQuery(".update_icon_link").click(function(){

		jQuery(this).parents("tr:first").find(".agency_field_hid").hide();
		jQuery(this).parents("tr:first").find(".agency_field_lbl").show();
		jQuery(this).parents("tr:first").find(".update_icon_link").hide();
		jQuery(this).parents("tr:first").find(".cancel_icon_link").hide();
		jQuery(this).parents("tr:first").find(".edit_icon_link").show();

	});


	jQuery('#move_prop_manager_sel').change(function(e){

		var prop_manager_id = $(this).val();
		var prop_manager_text = $('#move_prop_manager_sel option:selected').attr('data-text');

		if( prop_manager_id!="" ){

			jQuery.ajax({
                    type: "POST",
                    url: '/agency/ajax_get_agency_by_pm_including_alt',
                    data: {
						pm_id: prop_manager_id,
						logged_in_agency : <?php echo $this->session->agency_id; ?>
                    }
                }).done(function( ret ) {
				if( ret!="" ){
					$('#move_prop_manager_agency_sel').html('');
					$('#move_prop_manager_agency_sel').append(ret);
					jQuery('#move_prop_manager_agency_sel_box').show();
					$('#transfer_user').html(prop_manager_text);
					$('.transfer_radio_box').hide();
					$('#cant_switch_warning_text').hide().html("");
					$('#btn_change_to_this_agency').hide();
				}else{
					$('#cant_switch_warning_text').show().html(prop_manager_text+" has not been setup to be able to switch between Portfolios. Please call <?=$this->config->item('COMPANY_NAME_SHORT')?> on <?php echo $country->agent_number; ?> or <?php echo $country->outgoing_email ?> so that we can activate the switch for "+prop_manager_text+".");
					jQuery('#move_prop_manager_agency_sel_box').hide();
					$('#transfer_user').html("user");
				}
				
			});

			
		}else{
			jQuery('#move_prop_manager_agency_sel_box').hide();
		}
		
	})

	$('#move_prop_manager_agency_sel').change(function(){
		var agency_sel = $(this).val();
		var agency_text = $('#move_prop_manager_agency_sel option:selected').text();
		var prop_manager_id = $('#move_prop_manager_sel').val();

		

		if(agency_sel!=""){

			if(prop_manager_id!=""){

				jQuery.ajax({
						type: "POST",
						url: '/agency/ajax_check_pm_if_has_prop_attached',
						dataType: 'json',
						data: {
							pm_id: prop_manager_id
						}
					}).done(function( ret ) {
					if( ret.status ){
						
						$('.transfer_radio_box').show();
						$('#transfer_agency').html(agency_text);
						$('#btn_change_to_this_agency').show().html('Change to '+agency_text);

						//Add required marker to radiobox
						$('.transfer_all_prop_attached').addClass('is_req');

					}else{
						$('.transfer_radio_box').hide();
						$('#btn_change_to_this_agency').show().html('Change to '+agency_text);

						//remove required marker
						$('.transfer_all_prop_attached').removeClass('is_req');
					}

					$('#orig_agency_name').val(ret.old_agency_name);
					$('#orig_agency_id').val(ret.old_agency_id);
					
				});

			}

			
		}else{
			$('.transfer_radio_box').hide();
			$('#transfer_agency').html("agency");
			$('#btn_change_to_this_agency').hide().html('Change to this Agency');
		}
	})

	$('#btn_change_to_this_agency').click(function(e){
		var agency = $('#move_prop_manager_agency_sel').val();
		var agency_name = $('#move_prop_manager_agency_sel option:selected').text();
		var pm = $('#move_prop_manager_sel').val();
		var pm_name = $('#move_prop_manager_sel option:selected').attr('data-text');
		var transfer = $('input:radio[name=transfer_all_prop_attached]:checked').val();
		var transfer_all_prop_attached = $('input:radio[name=transfer_all_prop_attached]:checked');
		var orig_agency_name = $('#orig_agency_name').val();
		var orig_agency_id = $('#orig_agency_id').val();

		var err = "";
		if( agency=="" ){
			err +="Please select agency\n";
		}

		if( transfer_all_prop_attached.length <= 0 && agency!="" && $('.transfer_all_prop_attached').hasClass('is_req') ){
			err +="Please select Would you like to transfer ALL of the properties attached to "+pm_name+" to "+agency_name+" also?\n";
		}

		if(err!=""){
			swal('Error',err,'error');
			return false;
		}

		jQuery("#preloader").show();
		jQuery.ajax({
			type: "POST",
			url: '/agency/ajax_move_agency_pm',
			data: {
				agency: agency,
				pm:pm,
				transfer: transfer,
				orig_agency_name: orig_agency_name,
				orig_agency_id: orig_agency_id
			}
		}).done(function( ret ) {
			jQuery("#preloader").hide();
			$.fancybox.close();
			swal({
				title:"Success!",
				text: "Update Successful",
				type: "success",
				showCancelButton: false,
				confirmButtonText: "OK",
				closeOnConfirm: false,

			},function(isConfirm){
				if(isConfirm){ 
					location.reload();
					}
			});
			});

	})

	$('input:radio[name=transfer_all_prop_attached]').change(function(){
		var thisval = $(this).val();
		var agency_text = $('#move_prop_manager_agency_sel option:selected').text();
		var old_agency = $('#orig_agency_name').val();
		var prop_manager_text = $('#move_prop_manager_sel option:selected').attr('data-text');

		if( thisval == 1 ){
			$('.move_user_and_properties').show().html("This will move all attached properties from "+old_agency+" to "+agency_text);
			$('.move_only_user_red_text').hide();
		}else{
			$('.move_only_user_red_text').show().html("Move only "+prop_manager_text+" to "+agency_text);
			$('.move_user_and_properties').hide();
		}
	})
	
	// submit Agency Address Form for update
	$('body').on('submit', '#address_form', function(e){
		e.preventDefault();

		let data = $('#address_form').serialize();
		let url = "/ajax/agency_ajax/update_agency_address";

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Error',res.message,'error');
			}
		});
	})

	// submit Agency Phone Form for update
	$('body').on('submit', '#phone_form', function(e){
		e.preventDefault();

		let data = $('#phone_form').serialize();
		let url = "/ajax/agency_ajax/update_agency_profile";

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		});
	})

	// submit Agency Office hours Form for update
	$('body').on('submit', '#agency_hours_form', function(e){
		e.preventDefault();

		let data = $('#agency_hours_form').serialize();
		let url = "/ajax/agency_ajax/update_agency_profile";

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		});
	})

	// submit Agency maintenance form for update
	$('body').on('submit', '#agency_maintenance_form', function(e){
		e.preventDefault();

		let data = $('#agency_maintenance_form').serialize();
		let url = "/ajax/agency_ajax/update_agency_maintenance";

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		});
	})

	// submit Agency website form for update
	$('body').on('submit', '#agency_website_form', function(e){
		e.preventDefault();

		let data = $('#agency_website_form').serialize();
		let url = "/ajax/agency_ajax/update_agency_profile";

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		});
	})

	// submit Agency ABN form for update
	$('body').on('submit', '#agency_abn_form', function(e){
		e.preventDefault();

		let data = $('#agency_abn_form').serialize();
		let url = "/ajax/agency_ajax/update_agency_profile";

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		});
	})

	// submit Agency total portfolio Form for update
	$('body').on('submit', '#total_properties_form', function(e){
		e.preventDefault();

		let data = $('#total_properties_form').serialize();
		let url = "/ajax/agency_ajax/update_agency_profile";

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		});
	})

	// submit Agency emails Form for update
	$('body').on('submit', '#agency_emails_form', function(e){
		e.preventDefault();

		let data = $('#agency_emails_form').serialize();
		let url = "/ajax/agency_ajax/update_agency_profile";

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		});
	})
	
	// submit Account emails Form for update
	$('body').on('submit', '#account_emails_form', function(e){
		e.preventDefault();

		let data = $('#account_emails_form').serialize();
		let url = "/ajax/agency_ajax/update_agency_profile";

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		});
	})

	// submit Agency Main Contact name Form for update
	$('body').on('submit', '#agency_contact_form', function(e){
		e.preventDefault();

		let data = $('#agency_contact_form').serialize();
		let url = "/ajax/agency_ajax/update_agency_profile";

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		});
	})
		
	
	// submit Account Contact Name Form for update
	$('body').on('submit', '#accounts_contact_form', function(e){
		e.preventDefault();

		let data = $('#accounts_contact_form').serialize();
		let url = "/ajax/agency_ajax/update_agency_profile";

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		});
	})


	// submit Tenants Contact Name for update
	$('body').on('submit', '#tenants_contact_form', function(e){
		e.preventDefault();

		let data = $('#tenants_contact_form').serialize();
		let url = "/ajax/agency_ajax/update_agency_profile";

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		});
	})


	//toggle to show form when select a maintenance provider
	$('#maintenance_provider').on('change', function(){
		var selectedOption = $(this).val();
		
		if(selectedOption !== ""){
			$('#maintenance_div_form').removeClass("d-none").addClass('d-block');
		} else {
			$('#maintenance_div_form').removeClass("d-block").addClass('d-none');
		}
	})
	
	//toggle display_bpay select input
    //update to lightbox
	$('#agency_bpay_display_invoices_form').on('click', function(e){
        // e.preventDefault();
		var selectedOption = $(this).find('#display_bpay').val();

		let url = "/ajax/agency_ajax/update_agency_profile";
		let data = {display_bpay: selectedOption};

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
            if(res.status){
                swal({
                    title: "Success.",
                    text: res.message,
                    type: "success",
                    // timer: 2000,
                    showConfirmButton: false
                },function(isConfirm){
                    location.reload();
                });
            }else{
                swal('Warning',res.message,'warning');
            }
		})
	})

	//toggle send_en_to_agency select input
	$('#send_en_to_agency').on('change', function(){
		var selectedOption = $(this).val();
		
		let url = "/ajax/agency_ajax/update_agency_profile";
		let data = {send_en_to_agency: selectedOption};

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		})
	})

	//toggle send_48_hr_key select input
	$('#send_48_hr_key').on('change', function(){
		var selectedOption = $(this).val();
		
		let url = "/ajax/agency_ajax/update_agency_profile";
		let data = {send_48_hr_key: selectedOption};

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data
		}).done(function(res){
		
			if(res.status){
				swal({
					title: "Success.",
					text: res.message,
					type: "success",
					timer: 2000,
					showConfirmButton: false
				},function(isConfirm){
					location.reload();
				});
			}else{
				swal('Warning',res.message,'warning');
			}
		})
	})

 });
</script>

<script type="text/javascript">
	
	//------------GOOGLE ADDRESS AUTOCOMPLETE START
	var placeSearch, autocomplete;
    var componentForm2 = {
        route: {
            'type': 'long_name',
            'field': 'address_2'
        },
        administrative_area_level_1: {
            'type': 'short_name',
            'field': 'state'
        },
        postal_code: {
            'type': 'short_name',
            'field': 'postcode'
        }
    };

    function initAutocomplete() {
		
		// fullAdd = document.querySelector("#fullAdd");
		fullAdd = document.getElementById('fullAdd');

		//alert('element found: ' + !!fullAdd)

        <?php if( $this->config->item('country') == 1 ){ ?>
            var cntry = 'au';
        <?php }else{ ?>
            var cntry = 'nz';
        <?php } ?>

        var options = {
            types: ['geocode'],
            componentRestrictions: {country: cntry}
        };

        autocomplete = new google.maps.places.Autocomplete(fullAdd,options);

        autocomplete.addListener('place_changed', fillInAddress);

    }

    function fillInAddress() {

        var place = autocomplete.getPlace();

        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm2[addressType]) {
                var val = place.address_components[i][componentForm2[addressType].type];
                document.getElementById(componentForm2[addressType].field).value = val;
            }
        }

        // street name
        var ac = jQuery("#fullAdd").val();
        var ac2 = ac.split(" ");
        var street_number = ac2[0];
        console.log(street_number);
        jQuery("#address_1").val(street_number);

        // suburb
        jQuery("#address_3").val(place.vicinity);

        console.log(place);
    }
    //------------GOOGLE ADDRESS AUTOCOMPLETE END
	
</script>
