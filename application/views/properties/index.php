<section class="box-typical box-typical-padding">
<div style="display:none;">{elapsed_time} </div>
<h5 class="m-t-lg with-border"><a href="/properties"><?php echo $title; ?></a></h5>
	
	<!-- Tabs -->
	<section class="tabs-section">
		<div class="tabs-section-nav tabs-section-nav-icons">
			<div class="tbl">
				<ul class="nav" role="tablist">
					<li class="nav-item">

						<a data-url="<?= $this->config->item('theme') ?>" 
							class="nav-link <?= ($this->input->get('type')) ? ($this->input->get('type')=='sats' || $this->input->get('type')=='sas')?'active':'not-active' : 'active' ?> " 
							href="/properties/?type=<?=$this->config->item('theme')?>"
						>
						<span class="nav-link-in">
							<i class="fa fa-calendar-check-o"></i>
							Annual Service
						</span>
						</a>

					</li>
					<?php if( $this->session->country_id == 1 ): // AU ?>

						<li class="nav-item">

							<a data-url="<?= $type_not_compliant ?>" 
								class="nav-link <?= ($this->input->get('type')=='not_compliant')?'active':'not-active' ?> " 
								href="/properties/?type=not_compliant"
							>
								<span class="nav-link-in">
									<i class="fa fa-calendar-times-o"></i>
									Not Compliant
									&nbsp;<span id="not_compliant_count" class="label label-pill label-danger jTabBubble"><?= $this->properties_model->get_no_compliant_prop_for_properties_page($parms_tt)->num_rows() ?></span>
								</span>
							</a>

						</li>

					<?php endif; ?>					
					<li class="nav-item red">
						<a data-url="<?= $type_nonSats ?>" 
							class="nav-link <?= ($this->input->get('type')=='nonsats' || $this->input->get('type')=='nonsas')?'active':'not-active' ?>"
							href="/properties/?type=non<?=$this->config->item('theme')?>"
						>
							<span class="nav-link-in">
								<span class="fa fa-hourglass-end"></span>
								Not Serviced by <span class="uppercase"><?= $this->config->item('theme')  ?></span>
							</span>
						</a>
					</li>

					<li class="nav-item">
						<a data-url="<?= $type_onceOff ?>" 
							class="nav-link <?= ($this->input->get('type')=='onceOff')?'active':'not-active' ?>" 
							href="/properties/?type=onceOff"
						>
							<span class="nav-link-in">
								<i class="fa fa-calendar-times-o"></i>
								<span class="uppercase"><?= $this->config->item('theme')  ?></span> Once-off Service
							</span>
						</a>
					</li>
					<?php if($agency_state == 'QLD'): ?>
					<li class="nav-item">
						<a data-url="<?= $type_sales_prop ?>" 
							class="nav-link <?= ($this->input->get('type')=='sales_prop')?'active':'not-active' ?>" 
							href="/properties/?type=sales_prop"
						>
							<span class="nav-link-in">
								<i class="fa fa-calendar-check-o"></i>
								Sales Upgrade
							</span>
						</a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		<!--.tabs-section-nav-->


		<!------ TAB 1 START ------->
		<div class="tab-content">
			<div role="tabpanel" 
				class="tab-pane fade in <?= ($this->input->get('type')) ? (($this->input->get('type')=='sats' || $this->input->get('type')=='sas'))?'active show':'not-active' : 'active show' ?> " 
				id="tabs-1-tab-1"
			>

				<?php if(($this->input->get('type')=='sats' || $this->input->get('type')=='sas') || $this->input->get('type')=='') : ?>
				<!-- list -->
				<div class="box-typical-body">
					<div class="table-responsive">
						<table class="table table-hover main-table" id="datatable">
							<thead>
								<tr>
									<th>Address</th>
									<!-- // only show on Hume Agency(1598), AU only  -->
									<?php if( $this->system_model->is_hume_housing_agency() == true ): ?>
										<th>Property Code</th>
									<?php endif; ?>									
									<th>Property Manager</th>
									<th>Service Type</th>
									<th>Last Service</th>
								</tr>
							</thead>
							<tbody>
								<?php if(!empty($prop_list)): ?>
									<?php foreach($prop_list as $row): ?>

										<tr <?= ($row['is_nlm']==1)?'class="opa-down"':'' ?> 
											data-nlm="<?= $row['nlm_display'] ?>" 
											data-jobid="<?= $row['j_id'] ?>" 
											data-propid="<? $row['property_id'] ?>" 
										>
											<td>
												<?php if($row['is_nlm']==1): ?>
													<?= $row['address_1']." ".$row['address_2'].", ".$row['address_3'] ?>
												<?php else: ?>
													<a target="_blank" href="<?= base_url()."properties/property_detail/".$row['property_id']; ?>">
														<?= $row['address_1']." ".$row['address_2'].", ".$row['address_3'] ?>
													</a>
												<?php endif; ?>
												<input type="hidden" class="property_id" value="<?= $row['property_id']; ?>" />
											</td>
									
											<!-- // only show on Hume Agency(1598), AU only -->
											<?php if( $this->system_model->is_hume_housing_agency() == true ): ?>
												<td>
													<input type ="text" 
														class="form-control compass_index_num w-75 float-left mr-3" 
														value="<?= $row['compass_index_num']; ?>" />
													<span class="font-icon font-icon-ok compass_index_num_check align-middle" style="color: #46c35f; display:none;"></span>
												</td>
											<?php endif; ?>		

											<td data-propID="<?= $row['property_id'] ?>" id="pm_td<?= $row['property_id'] ?>">
												<?php if( isset($row['agency_user_account_id']) && $row['agency_user_account_id'] > 0 ): ?>
											
													<img class='profile_pic_small border-0 border-info' src="<?= profileAvatar($row['photo']) ?>" >
													<a id='edit_pm_fb_link<?=$row['property_id']?>' 
														class='edit_pm_fb_link' 
														href='javascript:void(0);' 
														data-pm_id='<?= $row["agency_user_account_id"] ?>'
													>
														<?= $row['pm_fname'] ?> <?= $row['pm_lname'] ?>
													</a>										

												<?php else: ?>
													<button id="add_pm_btn<?= $row['property_id'] ?>" class="btn btn-inline add_pm_btn" type="button">Add Property Manager</button>

												<?php endif; ?>
											</td>
											<td>
                                                <?php foreach($row['property_services'] as $prop_service): ?>
                                                    <?php if ( $prop_service->agency_service_count > 0 ): ?>
                                                        <?= Alarm_job_type_model::icons($prop_service->ajt_id); ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                
											</td>
							
											<td>
												<?php if($this->jcclass->isDateNotEmpty($row['last_service_date'])): ?>
													<span>
														<?= date("d/m/Y", strtotime($row['last_service_date'])) ?> 
														<?= ($row['assigned_tech'] == 1) ? "&nbsp;(Not by sats)" : "" ?>
													</span>
													<input type="hidden" name="assigned_tech_id" value="<?= $row['assigned_tech_id'] ?>">
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>

							</tbody>
						</table>
					</div>
				</div>
			
				<?php endif; ?>

			</div>
			<!------ TAB 1 END ------->

			<!------ TAB 2 START ------->
			<div role="tabpanel" class="tab-pane fade <?= ($this->input->get('type')=='not_compliant')?'active show':'' ?>" id="tabs-1-tab-2_a">
			
				<?php if($this->input->get('type')=='not_compliant'): ?>
				<!-- list -->
				<div class="box-typical-body">
					<div class="table-responsive">
						<table class="table table-hover main-table" id="datatable">
							<thead>
								<tr>
									<th>Address</th>

									<th>Property Manager</th>
									<th>Service Type</th>
									<th>Comment</th>
									<th>Last Service</th>
								</tr>
							</thead>
							<tbody>

								<?php if(count($list_not_compliant) > 0): ?>
									<?php foreach($list_not_compliant as $row): ?>
										<tr class="<?= ($row['is_nlm']==1) ? 'opa-down' : '' ?>"  data-jobid="<?= $row['j_id'] ?>">
											<td>
												<div class="d-none"><?= $row['agency_id'] ?></div>
												<?php if($row['is_nlm']==1): ?>
													<?= $row['address_1']." ".$row['address_2'].", ".$row['address_3'] ?>
												<?php else: ?>
													<a target="_blank" href="<?= base_url()."properties/property_detail/".$row['property_id']; ?>">
														<?= $row['address_1']." ".$row['address_2'].", ".$row['address_3'] ?>
													</a>
												<?php endif; ?>
											</td>

											<td>
												<?php if( isset($row['agency_user_account_id']) && $row['agency_user_account_id'] > 0 ): ?>
													<img class='profile_pic_small border-0 border-info' src="<?= profileAvatar($row['photo']) ?>" >
													<span><?= $row['pm_fname']?> <?=$row['pm_lname']?></span>
												<?php endif; ?>
											</td>
							
											<td>
												<?php if(!empty($row['property_services'])): ?>
													<?php foreach($row['property_services'] as $prop_service): ?>
                                                        <?= Alarm_job_type_model::icons($prop_service->ajt_id); ?>
													<?php endforeach; ?>
												<?php else: ?>
													<span>-----</span>
												<?php endif; ?>
											</td>

											<td>
												<?= $row['not_compliant_notes'] ?>
											</td>

											<td>
												<?= ($this->jcclass->isDateNotEmpty($row['last_service_date'])) ? date("d/m/Y", strtotime($row['last_service_date'])) : '' ?>
											</td>

										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php endif; ?>
			
            </div>

			<!------ TAB 3 START ------->
			<div role="tabpanel" class="tab-pane fade <?= ($this->input->get('type')=='nonsats' || $this->input->get('type')=='nonsas') ? 'active show' : '' ?>" id="tabs-1-tab-2">

			<?php if($this->input->get('type')=='nonsats' || $this->input->get('type')=='nonsas'): ?>
				<div class="box-typical-body">
					<div class="table-responsive">
						<table class="table table-hover main-table" id="datatable">
							<thead>
								<tr>
									<th>Address</th>

									<th>Property Manager</th>
									<th>Service Type</th>
									<th>Last Service</th>
								</tr>
							</thead>
							<tbody>

								<?php if($this->input->get_post('type')=="nonsats" || $this->input->get('type')=='nonsas'): ?>
									<?php if(count($properties) > 0): ?>
										<?php foreach($properties as $row): ?>

											<tr class= "<?= ($row['is_nlm']==1) ? 'opa-down' : '' ?>" >
												<td>
													<div style="display:none;"><?= $row['agency_id'] ?></div>
													<?php if($row['is_nlm']==1): ?>
														<?= $row['address_1']." ".$row['address_2'].", ".$row['address_3'] ?>
													<?php else: ?>
														<a target="_blank" href="<?= base_url()."properties/property_detail/".$row['property_id']; ?>">
														<?= $row['address_1']." ".$row['address_2'].", ".$row['address_3'] ?></a>
													<?php endif; ?>
												</td>
												<td>
													<?php if( isset($row['agency_user_account_id']) && $row['agency_user_account_id'] > 0 ): ?>
														<img class='profile_pic_small border-0 border-info' src="<?= profileAvatar($row['photo']) ?>" >
														<span><?= $row['pm_fname']?> <?=$row['pm_lname']?></span>
													<?php endif; ?>
												</td>
												<td>

													<?php if(!empty($row['property_services'])): ?>
														<?php foreach($tetes as $newtetes): ?>
                                                            <?= Alarm_job_type_model::icons($prop_service->ajt_id); ?>
														<?php endforeach; ?>
													<?php else: ?>
														<span>-----</span>
													<?php endif; ?>

												</td>
												<td>
                                                    <?php if($this->jcclass->isDateNotEmpty($row['last_service_date'])): ?>
                                                        <span> <?php echo date("d/m/Y", strtotime($row['last_service_date'])); echo ($row['assigned_tech'] == 1) ? "&nbsp;(Not by sats)" : ""; ?></span>
                                                    <?php endif; ?>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php endif; ?>
								<?php endif; ?>

							</tbody>
						</table>
					</div>
				</div>

			<?php endif; ?>
            </div>

			<!------ TAB 4 START ------->
			<div role="tabpanel" class="tab-pane fade <?= ($this->input->get('type')=='onceOff') ? 'active show' : '' ?>" id="tabs-1-tab-3">
				<?php if($this->input->get('type')=='onceOff'): ?>
				<div class="table-responsive">
					<table class="table table-hover main-table" id="datatable">
						<thead>
							<tr>
								<th>Address</th>
								<th>Property Manager</th>
								<th>Service Type</th>
								<th>Last Service</th>
							</tr>
						</thead>
						<tbody>
							<?php if(!empty($properties)): ?>
								<?php foreach($properties as $row): ?>
								<tr class="<?= ($row['is_nlm']==1)? 'opa-down' : '' ?>" >
									<td>
										<?php if($row['is_nlm']==1): ?>
											<?= $row['prop_address']; ?>
										<?php else: ?>
											<a target="_blank" href="<?= base_url()."properties/property_detail/".$row['property_id']; ?>">  
												<?= $row['prop_address']; ?>  
											</a>
										<?php endif; ?>
									</td>
									<td>
										<?php if( isset($row['aua_id']) && $row['aua_id'] > 0 ): ?>
											<img class='profile_pic_small border-0 border-info' src="<?= profileAvatar($row['photo']) ?>" >
											<span><?= $row['pm_fname']?> <?=$row['pm_lname']?></span>
										<?php endif; ?>
									</td>
									<td>
										<?php if(!empty($row['property_services'])): ?>
											<?php foreach($row['property_services'] as $prop_service): ?>
                                                <?= Alarm_job_type_model::icons($prop_service->ajt_id); ?>
											<?php endforeach; ?>
										<?php else: ?>
											<span>-----</span>
										<?php endif; ?>
									</td>
									<td>
										<?php if(!empty($row['last_service_date'])): ?>
												<?= ($this->jcclass->isDateNotEmpty($row['last_service_date'])) ? date("d/m/Y", strtotime($row['last_service_date'])) : '' ?>
										<?php endif; ?>
									</td>
								</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
				<?php endif; ?>
			</div>

			<!------ TAB 5 START ------->
			<div role="tabpanel" class="tab-pane fade <?= ($this->input->get('type')=='sales_prop') ? 'active show' : '' ?>" id="tabs-1-tab-4">
				<?php if($this->input->get('type')=='sales_prop'): ?>
				<div class="table-responsive">
					<table class="table table-hover main-table" id="datatable">
						<thead>
							<tr>
								<th>Address</th>
								<!-- // only show on Hume Agency(1598), AU only  -->
								<?php if( $this->system_model->is_hume_housing_agency() == true ): ?>
									<th>Property Code</th>
								<?php endif ?>		

								<th>Property Manager</th>
								<th>Service Type</th>
								<th>Last Service</th>
							</tr>
						</thead>
						<tbody>
							<?php if( !$this->input->get('type') || $this->input->get('type')=='sales_prop' ): ?>
								<?php if(!empty($prop_list_sales_prop)): ?>
									<?php foreach($prop_list_sales_prop as $row): ?>
										<tr class="<?= ($row['is_nlm']==1) ? 'opa-down' : '' ?>" >
											<td>
												<?php if($row['is_nlm']==1): ?>
													<?= $row['address_1']." ".$row['address_2'].", ".$row['address_3'] ?>
												<?php else: ?>
													<a target="_blank" href="<?= base_url()."properties/property_detail/".$row['property_id']; ?>">
														<?= $row['address_1']." ".$row['address_2'].", ".$row['address_3'] ?>
													</a>
												<?php endif; ?>
												<input type="hidden" class="property_id" value="<?= $row['property_id']; ?>" />
											</td>
											
											<!-- // only show on Hume Agency(1598), AU only -->
											<?php if( $this->system_model->is_hume_housing_agency() == true ): ?>
												<td>
													<input type ="text" class="form-control compass_index_num w-75 float-left mr-3" value="<?= $row['compass_index_num']; ?>" />
													<span class="font-icon font-icon-ok compass_index_num_check align-middle" style="color: #46c35f; display:none;"></span>
												</td>
											<?php endif; ?>		

											<td data-propID="<?= $row['property_id'] ?>">
												<?php if( isset($row['agency_user_account_id']) && $row['agency_user_account_id'] > 0 ): ?>
													<img class='profile_pic_small border-0 border-info' src="<?= profileAvatar($row['photo']) ?>" >
													<span><?= $row['pm_fname']?> <?=$row['pm_lname']?></span>
												<?php endif; ?>

											</td>
											<td>
												<?php foreach($row['property_services'] as $prop_service): ?>
													<?php if ( $prop_service->agency_service_count > 0 ): ?>
                                                        <?= Alarm_job_type_model::icons($prop_service->ajt_id); ?>
													<?php endif; ?>
												<?php endforeach; ?>
											</td>
											<td>
												<?= ($this->jcclass->isDateNotEmpty($row['last_service_date'])) ? date("d/m/Y", strtotime($row['last_service_date'])) : '' ?>
											</td>

										</tr>
									<?php endforeach ?>
								<?php endif ?>
							<?php endif ?>
						</tbody>
					</table>
				</div>
				<?php endif; ?>
			</div>

		</div>
	</section>
</section>

<div id="add_pm_fb" class="fancybox" style="display:none;"> 

	<div class="row">
		<div class="col">
			<h3>Property Managers</h3>
			<div>
				<select id="add_edit_pm" class="form-control">
					<option value="">---</option>					
					<?php										
					foreach($property_manager_list as $pm_row){
					?>
					<option value="<?php echo $pm_row->agency_user_account_id; ?>">
						<?php echo $pm_row->fname." ".$pm_row->lname ?>
					</option>
					<?php
					}										
					?>
				</select>
			</div>
			<input type="hidden" id="property_id_hid_lb" />
			<div class="mt-3 text-center"><button id="save_pm_btn" class="btn btn-inline" type="button">Save</button></div>			
		</div>
	</div>
    
</div>

<!---- SCRIPT HERE -->

<script type="text/javascript">

	jQuery(document).ready(function(){
		
		$('.dtsp-searchPane:nth-child(3) .dataTables_scrollBody table tbody tr span').removeAttr('title');

		var base_url = "<?php echo base_url() ?>";


			//select2
			$(".select2-photo").not('.manual').select2({
			templateSelection: select2Photos,
			templateResult: select2Photos
		});


		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			var url = $(e.target).data('url');
			var newUrl = "/properties/?type="+url;
			window.history.pushState('','',newUrl );
			$('#sat_search_form').attr('action',base_url+'/properties/?type='+url);
			//location.reload();
		})


		
		// property code inline ajax update
        jQuery('.compass_index_num').change(function(){

			var dom = jQuery(this);
			var parents_tr = dom.parents("tr:first");

			var compass_index_num = dom.val();
			var property_id = parents_tr.find('.property_id').val();

			$('#load-screen').show();
			jQuery.ajax({
				type: "POST",
				url: "/properties/vpd_ajax_update_compass_index_num",
				dataType: 'json',
				data: {
					property_id: property_id,
					compass_index_num: compass_index_num
				}
			}).done(function(ret){

				$('#load-screen').hide(); 
				if(ret.res){
					dom.parents("td:first").find(".compass_index_num_check").show();
				}
				
			});

		});
		
		// edit/add PM
		jQuery(document).on('click','.edit_pm_fb_link, .add_pm_btn',function(){
			
			var dom = jQuery(this);
			var parent_td = dom.parents("td:first");
			var pm = dom.attr("data-pm_id");
			var property_id = parent_td.attr("data-propID");
			var fb_link_id = dom.attr("id");

			// clear
			jQuery("#add_edit_pm").val(''); 
			jQuery("#property_id_hid_lb").val('');
			
			jQuery("#property_id_hid_lb").val(property_id); // inject property ID on lightbox
			jQuery("#add_edit_pm").val(pm); // pre-select current PM			

			$.fancybox.open({
				src  : '#add_pm_fb'
			});

		});

		
		// save PM
		jQuery("#save_pm_btn").click(function(){

			var add_pm_fb = jQuery("#add_pm_fb");
			var property_id = jQuery("#property_id_hid_lb").val();
			var pm = jQuery("#add_edit_pm").val();

			if( property_id > 0 ){

				jQuery.ajax({
					type: "POST",
					url: "/properties/save_pm",
					data: {
						property_id: property_id,
						pm: pm
					},
					dataType: 'json'
				}).done(function(ret){

					$.fancybox.close();

					console.log(ret);

					// replace PM in PM inline (if it has it)
					jQuery('#edit_pm_fb_link'+property_id).text(ret.new_pm_name);	

					// remove add PM button (if it has it)
					jQuery('#add_pm_btn'+property_id).remove();
					
					// insert pm as inline edit
					var fb_link_html = ret.new_pm_pp+'&nbsp;&nbsp;<a id="edit_pm_fb_link'+property_id+'" class="edit_pm_fb_link" href="javascript:void(0);" data-pm_id="'+ret.new_pm_id+'">'+ret.new_pm_name+'</a>';
					jQuery('#pm_td'+property_id).html(fb_link_html);
					
				});
				
			}					

		});


	});



</script>
