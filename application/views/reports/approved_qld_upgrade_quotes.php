<section class="box-typical box-typical-padding">

	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/reports">Reports</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/reports/approved_qld_upgrade_quotes"><?php echo $title; ?></a></li>
	  </ol>
	</nav>


	<h5 class="m-t-lg with-border"><?php echo $title; ?>

		<div class="float-right">
			<div class="col-sm-12">
				<section class="proj-page-section">
					<div class="proj-page-attach">
						<i class="font-icon font-icon-pdf"></i>
						<p class="name"><?php echo $title; ?></p>
						<p>
							<a href="
								/reports/approved_qld_upgrade_quotes/?pdf=1
								&output_type=I
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&from=<?php echo $this->input->get_post('from'); ?>
								&to=<?php echo $this->input->get_post('to'); ?>"

								target="blank"
							>
								View
							</a>

							<a href="
								/reports/approved_qld_upgrade_quotes/?pdf=1
								&output_type=D
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&from=<?php echo $this->input->get_post('from'); ?>
								&to=<?php echo $this->input->get_post('to'); ?>"
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
				'pm_id' => $this->input->post('pm_id'),
				'show_status' => $this->input->post('show_status'),
				'search' => $this->input->post('search'),
				'from' =>$this->input->get_post('from'),
				'to' => $this->input->get_post('to')
			);
			$export_link = '/reports/approved_qld_upgrade_quotes/?export=1&'.http_build_query($export_link_params);

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
		echo form_open('/reports/approved_qld_upgrade_quotes',$form_attr);
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
					<label class="col-sm-12 form-control-label">Show</label>
					<div class="col-sm-12">
					<select class="form-control" id="show_status" name="show_status">
						<option value="">All</option>
						<option <?php echo ($this->input->get_post(show_status)=="Completed") ? 'selected="true"' : NULL ?> value="Completed">Completed</option>
						<option <?php echo ($this->input->get_post(show_status)=="Incomplete") ? 'selected="true"' : NULL ?> value="Incomplete">Incomplete</option>
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
		<div class="table-responsive">
			<table class="table table-hover main-table">
				<thead>
					<tr>
						<th>Address</th>
						<th>Property Manager</th>
						<th>Quote Valid Until</th>
						<th>Quote Amount</th>
						<th>View Quote</th>
						<th>Approved Date</th>
						<th>Approved Alarm</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($list as $row){

					$qld_approved = ( isset($row->qld_upgrade_quote_approved_ts) && $this->jcclass->isDateNotEmpty($row->qld_upgrade_quote_approved_ts) )?true:false;
					$plus90_days_ts = strtotime($row->j_date." +90 days");

					?>
						<tr>
							<td>
								<a href="/properties/property_detail/<?php echo $row->property_id ?>" target="blank">
									<?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}"; ?>
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
							<td class="<?php echo ( date('Y-m-d') > date('Y-m-d',$plus90_days_ts) )?'colorItRed':null; ?>">
								<?php echo ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',$plus90_days_ts):null; ?>
							</td>
							<td>
								<?php
								echo "$".number_format($row->qld_upgrade_quote_amount,2);
								?>
							</td>
							<td>
								<?php

								$qt = null;
								$approve_alarm_text = null;
								switch( $row->preferred_alarm_id ){

									case 10:
										$qt = 'brooks';
										$approve_alarm_text = 'Brooks';
									break;
									case 14:
										$qt = 'cavius';
										$approve_alarm_text = 'Cavius';
									break;
									case 22:
										$qt = 'emerald';
										$approve_alarm_text = $this->system_model->get_quotes_new_name(22);
									break;

								}

								$quote_ci_link = "{$this->config->item('crmci_link')}/pdf/view_quote/?job_id={$row->j_id}&qt=combined";

								?>
								<a data-jobid="<?php echo $row->j_id; ?>" data-toggle="tooltip" title='View Upgrade Quote' href="<?php echo $quote_ci_link; ?>" class="quq_link" target="_blank">
									<i style="font-size:26px;" class="font-icon font-icon-pdf"></i>
								</a>
							</td>
							<td>
								<!--<?php echo ( $qld_approved == true )?date('d/m/Y H:i',strtotime($row->qld_upgrade_quote_approved_ts)):null; ?>	-->
								<?php echo ( $this->jcclass->isDateNotEmpty($row->j_created) )?$this->jcclass->formatDate($row->j_created, 'd/m/Y'):null; ?>
							</td>
							<td><?php echo $approve_alarm_text; ?></td>
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


});
</script>