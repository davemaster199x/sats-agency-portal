<section class="box-typical box-typical-padding">

	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/reports">Reports</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/reports/upgraded_properties"><?php echo $title; ?></a></li>
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
								/reports/upgraded_properties/?pdf=1
								&output_type=I"
								target="blank">
								View
							</a>
							
							<a href="
								/reports/upgraded_properties/?pdf=1
								&output_type=D">
								Download
							</a>
						</p>
					</div>
				</section>
			</div>
		</div>

		<?php 
			$export_link_params = array();
			$export_link = '/reports/upgraded_properties/?export=1&'.http_build_query($export_link_params);

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

	<!-- list -->
	<div class="box-typical-body">
		<div class="table-responsive">
			<table class="table table-hover main-table">
				<thead>
					<tr>
						<th>Address</th>
						<th>Property Manager</th>
						<th>Completed Date</th>
					</tr>
				</thead>
				<tbody>
                    <?php foreach($lists->result() as $row){
                    ?>
                         <tr>
                            <td data-jobid="<?php echo $row->j_id; ?>">
								<a href="/properties/property_detail/<?php echo $row->property_id ?>" target="blank">
									<?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}"; ?>
								</a>								
                            </td>
                            <td>
							<?php  
									if( isset($row->properties_model_id_new) && $row->properties_model_id_new != 0 && $row->properties_model_fname!="" ){
										echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
										echo "{$row->properties_model_fname} {$row->properties_model_lname}";
									}
									?>   
                            </td>
                            <td>
								<?php echo ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):null; ?>
							</td>
                        </tr>
                    <?php
                    } ?>
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