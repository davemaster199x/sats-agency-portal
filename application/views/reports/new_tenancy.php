<section class="box-typical box-typical-padding">

	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/reports">Reports</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/reports/new_tenancy"><?php echo $title; ?></a></li>
	  </ol>
	</nav>

	<h5 class="m-t-lg with-border"><?php echo $title; ?>


		<div class="float-right">
			<div class="col-sm-12">
				<section class="proj-page-section">
					<div class="proj-page-attach">
						<i class="font-icon font-icon-pdf"></i>
						<p class="name">New Tenancy</p>
						<p>
							<a href="
								/reports/new_tenancy/?pdf=1
								&output_type=I
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&search=<?php echo $this->input->get_post('search'); ?>
								&offset=<?php echo $this->input->get_post('offset'); ?>"

								target="blank"
							>
								View
							</a>

							<a href="
								/reports/new_tenancy/?pdf=1
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
			$export_link = '/reports/new_tenancy/?export=1&'.http_build_query($export_link_params);

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
				echo form_open('/reports/new_tenancy',$form_attr);
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
								<option data-photo="<?php echo $this->jcclass->displayUserImage($row->photo); ?>" value="<?php echo $row->properties_model_id_new; ?>" <?php echo ( $row->properties_model_id_new == $this->input->get_post('pm_id') )?'selected="selected"':''; ?>><?php echo "{$row->fname} {$row->lname}"; ?></option>
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
							<th>Property Manager</th>
                            <th>Job Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Job Status</th>
                            <th>Job Date</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php

                        foreach($job_list->result() as $row){
                        ?>
 <tr>
                      <td>
					  <a href="/properties/property_detail/<?php echo $row->property_id ?>" target="blank"><?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}"; ?></a>

					  </td>
                      <td>
					  <?php
									if( isset($row->properties_model_id_new) && $row->properties_model_id_new != 0 && $row->properties_model_fname!="" ){
										echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
										echo "{$row->properties_model_fname} {$row->properties_model_lname}";
									}

									?>
                      </td>
                      <td>	<?php echo $row->job_type; ?></td>
                      <td><?php echo ($this->jcclass->isDateNotEmpty( $row->start_date ))?date('d/m/Y', strtotime($row->start_date)):'' ?></td>
                      <td><?php echo ($this->jcclass->isDateNotEmpty( $row->due_date ))?date('d/m/Y', strtotime($row->due_date)):'' ?></td>
                      <td data-toggle="tooltip" title="<?php echo $this->gherxlib->jobStatusNewNameMouseHover($row->j_status) ?>">
						<?php //echo $row->j_status; ?>
					 	 <span><?php echo $this->gherxlib->jobStatusNewName_v2($row->j_status); ?></span>
						</td>
                      <td>	<?php echo ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):'N/A'; ?></td>
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
