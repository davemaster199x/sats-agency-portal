
<style>
#datatable{
	border: 1px solid #dee2e6 !important;
}
</style>
<section class="box-typical box-typical-padding">
<h5 class="m-t-lg with-border"><a href="/jobs"><?php echo $title; ?></a></h5>
	
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
						<th>Status</th>
						<th>Booked Date</th>
						<th style="width:110px;">Entry Notice</th>
						<th style="width:125px;">Short Term Rental</th>	
						<th>Active From</th>	
					</tr>
				</thead>
				<tbody>
							
					<?php foreach ($list as $row): ?>
						<tr>
							<td>
								<a href="/properties/property_detail/<?= $row->property_id ?>" target="blank">
									<?= "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3}" ?>
								</a>
								<input type="hidden" name="job_id" value="<?= $row->j_id ?>" />
							</td>
							<!-- // only show on Hume Agency(1598), AU only -->
							<?php if( $this->system_model->is_hume_housing_agency() == true ): ?>
								<td><?= $row->compass_index_num ?></td>
							<?php endif; ?>

							<td>
								<?php if( isset($row->pm_id_new) && $row->pm_id_new != 0 ): ?>
									
									<img class='profile_pic_small border-0 border-info' src="<?= profileAvatar($row->photo) ?>" >
									<span><?= $row->pm_fname?> <?=$row->pm_lname?></span>

								<?php endif ?>
							</td>
							<td>
                                <?= Alarm_job_type_model::icons($row->j_service); ?>
								<span class="d-none"><?= $row->ajt_type ?></span>
							</td>
							<td data-toggle="tooltip" title="<?= $this->gherxlib->jobStatusNewNameMouseHover($row->j_status) ?>">
								<span><?= $this->gherxlib->jobStatusNewName_v2($row->j_status) ?></span>
							</td>									
							<td>
								<!-- // display date only to the following statuses -->
								<?php $allowed_status_arr = array('Booked','Pre Completion','Merged Certificates','Completed'); ?>
								<?php if( in_array($row->j_status, $allowed_status_arr) ): ?>
									<?= ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) ) ? date('d/m/Y',strtotime($row->j_date)) : null ?>
								<?php endif; ?>
							</td>
							<td style="padding-top:0;padding-bottom:0;padding-left:20px;">
								<?php if( $row->job_entry_notice==1 ): ?>
                                    <a data-toggle="tooltip" title="Entry Notice" href="<?= $this->config->item('crm_link') ?>/pdf/entry_notice/<?= $row->encrypted_job_id ?>" class="en_link" target="_blank">
										<i style="font-size:26px;" class="font-icon font-icon-pdf"></i>
									</a>
								<?php else: ?>
									<span>N/A</span>
								<?php endif; ?>								
							</td>
							<td>
								<?php if( $row->holiday_rental == 1 ): ?>
									<img data-toggle='tooltip'  title='Short Term Rental' src='/images/holiday_rental.png' />
								<?php else: ?>
									<span>N/A</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if( isset($row->j_created) && $this->jcclass->isDateNotEmpty($row->j_created) ): ?>
									<?= ( $row->j_created > date('Y-m-d') ) ? '<span style="color:red">'.date('d/m/Y',strtotime($row->j_created)).'</span>':date('d/m/Y',strtotime($row->j_created))  ?>
								<?php else: ?>
									<span>---</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					
				</tbody>
			</table>
		</div>
		
	</div><!--.box-typical-body-->
	
	
</section><!--.box-typical-->

			

<style>
.en_link{
	border-bottom: unset !important;
}
</style>

<script>
jQuery(document).ready(function(){
	
	//select2
	$(".select2-photo").not('.manual').select2({
			templateSelection: select2Photos,
			templateResult: select2Photos
		    });

})
</script>