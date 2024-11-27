<section class="box-typical box-typical-padding">


	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/user_accounts">User Accounts</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/user_accounts/logins/<?php echo ( $this->uri->segment(3) )?$this->uri->segment(3):NULL ?>"><?php echo $title; ?></a></li>
	  </ol>
	</nav>
	<?php 
		$userID = $this->uri->segment(3);
	?>
	<h5 class="m-t-lg with-border"><a href="/user_accounts/logins/<?php echo $userID ?>"><?php echo $title; ?></a></h5>

	<!-- list -->
	<div class="box-typical-body">
	
		<div class="table-responsive">
			<table id="jtable" class="table table-hover main-table">
				<thead>
					<tr>
						<th>Login</th>
						<th>User</th>
						<th>IP Address</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $users  as $index => $row ){ 
					
					$aua_id = $row->agency_user_account_id;
					
					?>
						<tr>
							<td><?php echo date('d/m/Y H:i',strtotime($row->date_created)); ?></td>
							<td>
								<?php  
										echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
										echo "{$row->fname} {$row->lname}";
								?>
							</td>
							<td><?php echo $row->ip; ?></td>
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