<section class="box-typical box-typical-padding">

	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/reports">Reports</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/reports/not_serviced"><?php echo $title; ?></a></li>
	  </ol>
	</nav>

	<h5 class="m-t-lg with-border"><?php echo $title; ?>
			<div class="float-right">
				<div class="col-sm-12">
				<section class="proj-page-section">
					<div class="proj-page-attach">
						<i class="font-icon font-icon-pdf"></i>
						<p class="name">Not Serviced</p>
						<p>
							<a href="
								/reports/not_serviced/?pdf=1
								&output_type=I
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&search=<?php echo $this->input->get_post('search'); ?>
								&offset=<?php echo $this->input->get_post('offset'); ?>"
								
								target="blank"
							>
								View
							</a>
							
							<a href="
								/reports/not_serviced/?pdf=1
								&output_type=D
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&search=<?php echo $this->input->get_post('search'); ?>
								&offset=<?php echo $this->input->get_post('offset'); ?>"
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
			$export_link = '/reports/not_serviced/?export=1&'.http_build_query($export_link_params);

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
				echo form_open('/reports/not_serviced',$form_attr);
				?>
                    <div class="form-groupss row">
                        
						<div class="float-left">
							<label for="exampleSelect" class="col-sm-12 form-control-label">Property Manager</label>
							<div class="col-sm-12" style="width:250px;">
								<select name="pm_id" class="form-control field_g2 select2-photo">
									<option value="">---</option>
									<option <?php  echo ( $this->input->get_post('pm_id') == '0' )?'selected="selected"':''; ?> value="0" data-photo="<?php echo $this->config->item('photo_empty'); ?>">No PM assigned</option>				
									<?php

									foreach($pm_filter as $pm_row){
										if($pm_row->properties_model_id_new){
										?>
									
										<option data-photo="<?php echo $this->jcclass->displayUserImage($pm_row->photo); ?>" value="<?php echo $pm_row->properties_model_id_new; ?>" <?php echo ( $pm_row->properties_model_id_new == $this->input->get_post('pm_id') )?'selected="selected"':''; ?>><?php echo "{$pm_row->properties_model_fname} {$pm_row->properties_model_lname}"; ?></option>
										
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
                            <th>Service Status</th>
                        </tr>
                    </thead>
                    <tbody>
				<?php
				if(!empty($prop_list_not)){

					foreach($prop_list_not as $row){ ?>

									<tr>
										<td><?php echo "<a href='/properties/property_detail/".$row->property_id."'>" .$row->address_1." ".$row->address_2.", ".$row->address_3. "</a>"  ?></td>
										<td>
										<?php  
													if( isset($row->properties_model_id_new) && $row->properties_model_id_new != 0 && $row->properties_model_fname!="" ){
														echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
														echo "{$row->properties_model_fname} {$row->properties_model_lname}";
													}

													?>    
										</td>
										<!--<td><?php echo $this->gherxlib->selected_service_label($row->service); ?></td>-->
										<td>Not serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?></td>
									</tr>

					<?php
					}

				}
				?>
                    
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation example" style="text-align:center">

              <?php echo $pagi_links_non_sats; ?>

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

