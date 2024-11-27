<section class="box-typical box-typical-padding">

	<h5 class="m-t-lg with-border"><?php echo $title; ?></h5>

	
	<!-- list -->
	<div class="box-typical-body">
	
		<div class="table-responsive">
			<table id="jtable" class="table table-hover">
				<thead>
					<tr>
						<th>Date</th>
						<th>&nbsp;</th>
						<th>User</th>
						<th>Action</th>
						<th>Details</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $users  as $index => $row ){ 					
					?>
						<tr>
							<td><?php echo date('d/m/Y H:i',strtotime($row->date_created)); ?></td>
							<td><img class="profile_pic_small border border-info" src="<?php echo ( isset($row->photo) && $row->photo != '' )?"{$user_photo_upload_path}/{$row->photo}":$default_avatar; ?>" /></td>							
							<td><?php echo $this->jcclass->formatStaffName($row->fname,$row->lname); ?></td>
							<td><?php echo $row->title ?></td>							
							<td><?php echo $row->details; ?></td>
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
		
	</div><!--.box-typical-body-->
	
	
	
	
	
</section><!--.box-typical-->
<style>
.btn_save{
	margin-top: 10px;
}
.new_pass_div{
	display: none;
}
.radio {
    margin: 0;
}
.font-icon.font-icon-pencil {
    margin-right: 8px;   
}
.a_link{
	border-bottom: none !important;
}
.btn_add_user{
	position: relative;
    bottom: 8px;
    margin: 0 !important;
}
</style>