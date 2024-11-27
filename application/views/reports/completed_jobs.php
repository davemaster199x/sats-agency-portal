
<section class="box-typical box-typical-padding">

	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/reports">Reports</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/reports/completed_jobs"><?php echo $title; ?></a></li>
	  </ol>
	</nav>

	<h5 class="m-t-lg with-border"><?php echo $title; ?>

		<div class="float-right">
			<div class="col-sm-12">
				<section class="proj-page-section">
					<div class="proj-page-attach">
						<i class="font-icon font-icon-pdf"></i>
						<p class="name">Completed Jobs</p>
						<p>
							<a href="
								/reports/completed_jobs/?pdf=1
								&output_type=I
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&from=<?php echo $from; ?>
								&to=<?php echo $to; ?>"
								
								target="blank"
							>
								View
							</a>
							
							<a href="
								/reports/completed_jobs/?pdf=1
								&output_type=D
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&from=<?php echo $from; ?>
								&to=<?php echo $to; ?>"
							>
								Download
							</a>
						</p>
					</div>
				</section>
			</div>
		</div>

		<div class="float-right">
			<div class="col-sm-12">
				<section class="proj-page-section">
					<div class="proj-page-attach">
						<i class="font-icon font-icon-post"></i>
						<p class="name">Completed Jobs CSV</p>
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
		echo form_open('/reports/completed_jobs',$form_attr);	
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
					<label class="col-sm-12 form-control-label">From</label>
					<div class="col-sm-12">						
						<div class="input-group flatpickr" data-wrap="true" data-default-date="<?php echo ( $from !='' )?$from:date('01/m/Y'); ?>">
							<input type="text" class="form-control" name="from" id="from" data-input />
							<span class="input-group-append" data-toggle>
								<span class="input-group-text">
									<i class="font-icon font-icon-calend"></i>
								</span>
							</span>
						</div>
					</div>
				</div>	
				
				<div class="float-left">
					<label class="col-sm-12 form-control-label">To</label>
					<div class="col-sm-12">
						<div class="input-group flatpickr" data-wrap="true" data-default-date="<?php echo ( $to !='' )?$to:date('t/m/Y'); ?>">
							<input type="text" class="form-control" name="to" id="to" data-input />
							<span class="input-group-append" data-toggle>
								<span class="input-group-text">
									<i class="font-icon font-icon-calend"></i>
								</span>
							</span>
						</div>
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
	<?php
	// header filters
	$filter_params = "
	&from=".$this->input->get_post('from')."
	&to=".$this->input->get_post('to')."
	&pm_id=".$this->input->get_post('pm_id')."
	&search=".$this->input->get_post('search');
	
	// sort toggle
	$toggle_sort = ( $sort == 'asc' )?'desc':'asc';	
	?>
	<div class="box-typical-body">
		<div class="table-responsive">
			<table id="dataTable" class="table table-hover main-table datatable">
				<thead>
					<tr>
						<th>
							Address
							<a data-toggle="tooltip" class="a_link" href="<?php echo "/reports/completed_jobs/?order_by=p.address_2&sort={$toggle_sort}{$filter_params}"; ?>">
								<span class="fa fa-sort-<?php echo $sort; ?>"></span>
							</a>
						</th>
						<?php
						// only show on Hume Agency(1598), AU only 
						if( $this->system_model->is_hume_housing_agency() == true ){ ?>
							<th>Property Code</th>
						<?php
						}
						?>	
						<th>Property Manager</th>
						<th>Job Type</th>						
						<th>							
							Completed Date
							<a data-toggle="tooltip" class="a_link" href="<?php echo "/reports/completed_jobs/?order_by=j.date&sort={$toggle_sort}{$filter_params}"; ?>">
								<span class="fa fa-sort-<?php echo $sort; ?>"></span>
							</a>
						</th>
						<?php 
							if($this->gherxlib->isCompassFG( $this->session->agency_id )){
						?>
							<th>Compass Index</th>
						<?php
							}
						 ?>
						
					</tr>
				</thead>
				<tbody>
					<?php					
					foreach ($list->result() as $row){ ?>
						<tr>
							<td>
								<a href="/properties/property_detail/<?php echo $row->property_id ?>" target="blank">
									<?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}"; ?>
								</a>
								<input type="hidden" name="job_id" value="<?php echo $row->j_id; ?>" />
							</td>
							<?php
							// only show on Hume Agency(1598), AU only
							if( $this->system_model->is_hume_housing_agency() == true ){ ?>
								<td><?php echo $row->compass_index_num; ?></td>
							<?php
							}
							?>
							<td>
							<?php  
									if( isset($row->properties_model_id_new) && $row->properties_model_id_new != 0 ){
										echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
										echo "{$row->properties_model_fname} {$row->properties_model_lname}";
									}

									?>    
							</td>
							<td>
								<?php echo $row->job_type; ?>
							</td>								
							<td>
								<?php echo ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):null; ?>
							</td>
							<?php 
							if( $this->gherxlib->isCompassFG( $this->session->agency_id )){
							?>
								<td><?php echo $row->compass_index_num; ?></td>
							<?php
								}
							?>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
		
		<nav aria-label="Page navigation example" style="text-align:center">
			<?php echo $pagination; ?>
		</nav>
		
		<div class="pagi_count"><?php echo $pagi_count; ?></div>
		
	</div><!--.box-typical-body-->
	
	
</section><!--.box-typical-->

			

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
	
});
</script>