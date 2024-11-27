<section class="box-typical box-typical-padding">


<h5 class="m-t-lg with-border"><a href="/api/api_properties"><?php echo $title; ?></a></h5>
<p>Properties listed on this page have an active connection with <?php echo $this->system_model->integrated_api(); ?></p>

	<!-- Header -->
	<!--
    <header class="box-typical-header">
        <?php 
        $sel1 = ( $this->input->get_post('linked')=='yes' || $this->input->get_post('linked')=="" ) ? 'checked="checked"' : NULL;
        ?>
		<div class="box-typical box-typical-padding">
        <label>Linked</label>

		<div class="checkbox-toggle">
			<input type="checkbox" id="api_toggle" name="api_toggle" value="yes" <?php echo $sel1; ?> />
			<label for="api_toggle"></label>
		</div>

		</div>
	</header>
	-->

	<header class="box-typical-header">

		<div class="box-typical box-typical-padding">
		<form action="<?php echo $uri ?>" method="POST">
			<div class="form-groupss row">

                <div class="col-lg-12">

					<div class="row">

						<div class="col-lg-3">
							<label for="exampleSelect" class="form-control-label">Property Manager</label>
							<select id="pm_filter" name="pm_filter" class="form-control field_g2 select2-photo">
								<option value="">All</option>
								<option <?php echo ($this->input->get_post('pm_filter')=='0')?'selected="selected"':'' ?> value="0" data-photo='/images/avatar-2-64.png'>No PM assigned</option>
								<?php			
								foreach( $pm_filter_sql->result() as $pm_filter_row ){?>
									<option 
										value="<?php echo $pm_filter_row->agency_user_account_id; ?>"
										data-photo="<?php echo ( $pm_filter_row->photo != '' )?"/uploads/user_accounts/photo/{$pm_filter_row->photo}":'/images/avatar-2-64.png' ?>" 										
										<?php echo $pm_filter_row->agency_user_account_id == ($this->input->get_post('pm_filter') )?'selected':null ?> 
									>
										<?php echo "{$pm_filter_row->fname} {$pm_filter_row->lname}"; ?>
									</option>
								<?php
								}
								?>
							</select>
						</div>

						<div class="col-lg-3">
							<label for="exampleSelect" class="form-control-label">Service Type</label>
							<select id="service_type_filter" name="service_type_filter" class="form-control">
								<option value="">All</option>
								<?php
								foreach( $serv_type_filter_sql->result() as $serv_type_filter_row ){
								?>
								<option 
									<?php echo ( $serv_type_filter_row->id == $this->input->get_post('service_type_filter') )?'selected':null; ?>  
									value="<?php echo $serv_type_filter_row->id; ?>"
								>
									<?php echo $serv_type_filter_row->type; ?>
								</option>
								<?php
								}
								?>
							</select>
						</div>

						<div class="col-lg-3">
							<label for="exampleSelect" class="form-control-label">Service Status</label>
							<select id="service_status_filter" name="service_status_filter" class="form-control">						
								<option value="1" <?php echo ( $service_status_filter == 1  )?'selected':null; ?>>Serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?></option>
								<option value="2" <?php echo ( $service_status_filter == 2  )?'selected':null; ?>>Not serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?></option>
							</select>
						</div>

						<div class="col-lg-2">
							<label class="form-control-label">Search</label>
							<input type="text" class="form-control" id="search_phrase" name="search_phrase" value="<?php echo $this->input->get_post('search_phrase') ?>" placeholder="Text">
						</div>

						<div class="col-lg-1">
							<div class="float-left">
								<label class="col-sm-12 form-control-label">&nbsp;</label>
								<input class="btn btn-inline" type="submit" id="btn_sats_search" name="btn_sats_search" value="Search">
							</div>
						</div>

					</div>

                </div>

				<!--
				<div class="col-lg-4">	

					<label id="active_toggle_lbl"><?php echo ( $this->input->get_post('active_toggle') == 1 || $this->input->get_post('active_toggle') == '' )?'Serviced by '.$this->config->item('COMPANY_NAME_SHORT'):'Not serviced by '.$this->config->item('COMPANY_NAME_SHORT');  ?></label>
					<div class="checkbox-toggle">
						<input 
							type="checkbox" 
							id="active_toggle" 
							name="active_toggle" 
							value="1"
							<?php 
							echo ( $this->input->get_post('active_toggle') == 1 || $this->input->get_post('active_toggle') == '' )?'checked':null; 
							?> 
						/>
						<label for="active_toggle"></label>
					</div>					

				</div>
				-->

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
						<th><?php echo $this->system_model->integrated_api(); ?> Property ID</th>
						<th>Property Manager</th>
						<th>Service Type</th>
						<th>Active Service</th>
					</tr>
				</thead>
				<tbody>
					<?php 
                    if($list->num_rows() > 0){

						foreach( $list->result() as $row ){
						?>
							<tr>
								<td>
									<a href ="/properties/property_detail/<?php echo $row->property_id ?>"><?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3}"; ?></a>
									<?php 
									/*
									$api_prop_data = $this->api_model->get_property_data($row['property_id']);   
									if( $api_prop_data['api_prop_id'] != '' ){
									?>
										&nbsp;<img style="height:25px;" src="/images/api/<?php echo $api_prop_data['agency_api_icon']; ?>" data-toggle="tooltip" title="Connected to: <?php echo  $api_prop_data['api_prop_address']; ?>" class="api_icon" />
									<?php 
									}
									*/
									?>
								</td>
								<td>
									<?php
									if( $this->system_model->integrated_api() == 'Console' ){ // console
										echo $row->console_prop_id;
									}else{ // other API
										echo $row->api_prop_id;
									}
									?>
								</td>
								<td>
									<?php
									if( $row->agency_user_account_id > 0 ){
										echo $this->gherxlib->avatarv2($row->properties_model_photo)." {$row->properties_model_fname} {$row->properties_model_lname}";
									}
									?>
								</td>
								<td>		
									<?php
									if( $row->ajt_id > 0 ){ ?>
										<?= Alarm_job_type_model::icons($row->ajt_id); ?>
									<?php
									}else{
										echo "No service";
									}
									?>																	
								</td>
								<td>
									<?php
									if( $row->ps_service == 1 ){ // serviced by SATS ?>
										<span class=" fa fa-check text-success"></span>
									<?php
									}else{ // add new ?>
										<a href="/properties/property_detail/<?php echo $row->property_id ?>/?scroll_to_bottom=1">
											<button type="button" class="btn">Add new service</button>
										</a>										
									<?php
									}
									?>
								</td>
							</tr>
						<?php
						}

					}else{
                        echo "<td>No Data<td>";
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


<!-- JAVASCRIPT START HERE.... -->
<style>
.vacant_from_date,
.vacant_to_date{
    width: 117px;
}
.profile_pic_small{
	margin-right: 5px;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function() {

	// PM dropdown script	
	$(".select2-photo").not('.manual').select2({
		templateSelection: select2Photos,
		templateResult: select2Photos
	});

	// service to SATS toggle script
	jQuery("#active_toggle").change(function(){

		var active_toggle_dom = jQuery(this); // DOM element
		if( active_toggle_dom.prop("checked") == true ){
			jQuery("#active_toggle_lbl").text('Serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?>');
			location.href = '/api/api_properties/?active_toggle=1';
		}else{
			jQuery("#active_toggle_lbl").text('Not serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?>');
			location.href = '/api/api_properties/?active_toggle=0';
		}

	});

}); 
</script>