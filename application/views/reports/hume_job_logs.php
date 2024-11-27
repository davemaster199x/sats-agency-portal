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
						<i class="font-icon font-icon-post"></i>
                        <p class="name"><?php echo $title; ?></p>
                            <p>
								<a href="<?php echo $export_link; ?>" target="blank">
									Export
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
					</tr>
				</thead>
				<tbody>
                    <?php foreach($list as $row){
                    ?>
                         <tr>
                            <td>
								<a href="/properties/property_detail/<?php echo $row->property_id ?>" target="blank">
									<?php echo "{$row->address_1} {$row->address_2}, {$row->address_3} {$row->state} {$row->postcode}"; ?>
								</a>								
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