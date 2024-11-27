<section class="box-typical box-typical-padding">

	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/reports">Reports</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="<?php echo $uri; ?>"><?php echo $title; ?></a></li>
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
								<?php echo $uri; ?>/?pdf=1
								&output_type=I
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&date_from_filter=<?php echo $this->input->get_post('date_from_filter'); ?>
								&date_to_filter=<?php echo $this->input->get_post('date_to_filter'); ?>"

								target="blank"
							>
								View
							</a>

							<a href="
								<?php echo $uri; ?>/?pdf=1
								&output_type=D
								&pm_id=<?php echo $this->input->get_post('pm_id'); ?>
								&date_from_filter=<?php echo $this->input->get_post('date_from_filter'); ?>
								&date_to_filter=<?php echo $this->input->get_post('date_to_filter'); ?>"
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
				'date_from_filter' => $this->input->get_post('date_from_filter'),
				'date_to_filter' =>$this->input->get_post('date_to_filter')
			);
			$export_link = '/reports/subscription_dates/?export=1&'.http_build_query($export_link_params);
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
				'id' => 'jform',
				'method' => 'get',
			);
			echo form_open($uri,$form_attr);
			?>
			<input type="hidden" name="date_from_filter" value="<?= $this->input->get_post('date_from_filter') ?>" />
			<input type="hidden" name="date_to_filter" value="<?= $this->input->get_post('date_to_filter') ?>" />
			<div class="form-groupsss row">

				<div class="col-md-8 columns">
					<div class="row">

						<div class="col-md-3 columns">
							<label for="exampleSelect" class="form-control-label">Property Manager</label>

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



						<div class="col-md-3 columns">
							<label class="col-sm-12 form-control-label">&nbsp;</label>

								<button type="submit" class="btn btn-inline">Search</button>

						</div>

				</div>
			</div>

				</div>
			</form>
		</div>
	</header>


	<div class="for-groupss row quickLinksDiv">

		<!-- PREV link -->
		<?php
			$prev_link_from = date("Y-m-01",strtotime("{$date_from_filter} -1 month"));
			$prev_link_to = date("Y-m-t",strtotime("{$date_from_filter} -1 month"));
		?>
		<div class="text-left col-md-3 columns">
			<!--<a href="<?php echo $uri; ?>?date_from_filter=<?php echo $prev_link_from; ?>&date_to_filter=<?php echo $prev_link_to; ?>"><em class="fa fa-arrow-circle-left"></em> Previous Month </a>-->
		</div>

		<!-- PREV link -->
        <div class="text-center col-md-6 columns">

           Quick Links&nbsp;

		    <?php
			for( $i=0; $i<4; $i++ ){
				$m = date("F",strtotime("{$date_from_filter} +{$i} month"));
				$from_link = date("Y-m-01",strtotime("{$date_from_filter} +{$i} month"));
				$to_link = date("Y-m-t",strtotime("{$date_from_filter} +{$i} month"));
			?>

				| &nbsp; <a href="<?php echo $uri; ?>?date_from_filter=<?php echo $from_link; ?>&date_to_filter=<?php echo $to_link; ?>" <?php echo ( $from_link == $date_from_filter )?'style="font-weight: bold;"':''; ?>><?php echo $m; ?></a>&nbsp;

			<?php
			}
			?>

        </div>

		<!-- NEXT link -->
		<?php
			$next_link_from = date("Y-m-01",strtotime("{$date_from_filter} +1 month"));
			$next_link_to = date("Y-m-t",strtotime("{$date_from_filter} +1 month"));
		?>
        <div class="text-right col-md-3 columns">
			<a href="<?php echo $uri; ?>?date_from_filter=<?php echo $next_link_from; ?>&date_to_filter=<?php echo $next_link_to; ?>" style="float: right;">Next Month <em class="fa fa-arrow-circle-right"></em> </a>
        </div>

    </div>


        <!-- list -->
        <div class="box-typical-body">

            <div class="table-responsive">
                <table class="table table-hover main-table">
                    <thead>
                        <tr>
							<th>Property Address</th>
							<th>Service Type</th>
							<th>Month of Invoice</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php

                        if(!empty($list->result())){
                            foreach($list->result() as $row){
                                ?>
                                    <tr>
										<td>
											<a href="/properties/property_detail/<?php echo $row->property_id ?>" target="blank"><?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}"; ?></a>
										</td>
										<td>
											<?= Alarm_job_type_model::icons($row->j_service); ?>
										</td>
										<td>
											<?php echo date("F Y",strtotime($row->j_date.' +12 months')); ?>
										</td>
                                    </tr>
                                <?php
                            }
						}else{
							echo "<tr><td colspan='3'>No Active Jobs</td></tr>";
						}

                        ?>

                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation example" style="text-align:center">

              <?php echo $pagination ?>

            </nav>
            <div class="pagi_count"><?php echo $pagi_count ?></div>

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
