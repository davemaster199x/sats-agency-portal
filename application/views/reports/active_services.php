<section class="box-typical box-typical-padding">

	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/reports">Reports</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/reports/active_services"><?php echo $title; ?></a></li>
	  </ol>
	</nav>

	<h5 class="m-t-lg with-border"><?php echo $title; ?>


		<div class="float-right">
			<div class="col-sm-12">
				<section class="proj-page-section">
					<div class="proj-page-attach">
						<i class="font-icon font-icon-pdf"></i>
						<p class="name">Active Services</p>
						<p>
							<a href="
								/reports/active_services/?pdf=1
								&output_type=I
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&service_type=<?php echo $this->input->get_post('service_type'); ?>
								&search=<?php echo $this->input->get_post('search'); ?>
								"
								
								target="blank"
							>
								View
							</a>
							
							<a href="
								/reports/active_services/?pdf=1
								&output_type=D
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&service_type=<?php echo $this->input->get_post('service_type'); ?>
								&search=<?php echo $this->input->get_post('search'); ?>
								"
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
				'service_type' => $this->input->get_post('service_type'),
				'search' => $this->input->get_post('search')
			);
			$export_link = '/reports/active_services/?export=1&'.http_build_query($export_link_params);

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

	<header class="box-typical-header">
            <div class="box-typical box-typical-padding">
			
                <?php
				$form_attr = array(
					'id' => 'jform'
				);
				echo form_open('/reports/active_services',$form_attr);
				?>
                    <div class="form-groupss row">
                        
						<div class="float-left">
							<label for="exampleSelect" class="col-sm-12 form-control-label">Property Manager</label>
							<div class="col-sm-12" style="width:250px;">
								<select name="pm_id" class="form-control field_g2 select2-photo">
									<option value="">---</option>
									<option <?php  echo ( $this->input->get_post('pm_id') == '0' )?'selected="selected"':''; ?> value="0" data-photo="<?php echo $this->config->item('photo_empty'); ?>">No PM assigned</option>
									<?php
									foreach( $pm_filter->result() as $row ){ ?>
										<option data-photo="<?php echo $this->jcclass->displayUserImage($row->photo); ?>" value="<?php echo $row->pm_id_new; ?>" <?php echo ( $row->pm_id_new == $this->input->get_post('pm_id') )?'selected="selected"':''; ?>><?php echo "{$row->fname} {$row->lname}"; ?></option>
									<?php
									}
									?>										
								</select>
							</div>
						</div>
						
						
						<div class="float-left">
							<label for="exampleSelect" class="col-sm-12 form-control-label">Service Type</label>
							<div class="col-sm-12">
								<select name="service_type" class="form-control">
									<option value="">---</option>
									<?php
									foreach( $serv_type_filter->result() as $row ){ ?>
										<option value="<?php echo $row->id; ?>" <?php echo ( $row->id == $this->input->get_post('service_type') )?'selected="selected"':''; ?>><?php echo $row->type; ?></option>
									<?php
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
							<?php
							// only show on Hume Agency(1598), AU only 
							if( $this->system_model->is_hume_housing_agency() == true ){ ?>
								<th>Property Code</th>
							<?php
							}
							?>	
                            <th>Property Manager</th>
                            <th>Service Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $ps->result() as $row ): ?>
                            <tr>
                                <td>
                                    <a href="/properties/property_detail/<?= $row->property_id ?>" target="blank"><?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}"; ?></a>
                                </td>

								<?php
								// only show on Hume Agency(1598), AU only
								if( $this->system_model->is_hume_housing_agency() == true ): ?>
									<td><?= $row->compass_index_num; ?></td>
								<?php endif; ?>
								
                                <td>
									<?php if( isset($row->pm_id_new) && $row->pm_id_new != 0 ): ?>
										<img class='profile_pic_small border-0 border-info' src="<?= profileAvatar($row->photo) ?>" >
										<span><?= $row->pm_fname?> <?=$row->pm_lname?></span>
									<?php endif;?>                                
								</td>
                                <td>
                                    <?= Alarm_job_type_model::icons($row->ajt_id); ?>
									<span class="d-none"><?= $row->ajt_type ?></span>
                                </td>
                            </tr>
						<?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation example" style="text-align:center">

                <?php echo $pagination; ?>

            </nav>
            <div class="pagi_count"><?php echo $pagi_count; ?></div> 

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
})
</script>

