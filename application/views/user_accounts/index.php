<section class="box-typical box-typical-padding">

	<h5 class="m-t-lg with-border">

		<a href="/user_accounts"><?php echo $title; ?></a>



		<div class="float-right">
			<a href="/user_accounts/add">
				<button type="button" class="btn btn-inline btn-primary-outline btn_add_user">
					<i class="fa fa-plus"></i>
					Add User
				</button>
			</a>
		</div>

		<div class="float-right">
			<a href="/user_accounts/index/?view=all">
				<button type="submit" class="btn btn-inline btn_show_all">Show ALL Users</button>
			</a>
		</div>

	</h5>

	<?php
	if( $this->input->get('del') == 1 ){ ?>
		<div class="alert alert-success">
		User Deactivated
		</div>
	<?php
	}
	?>


	<!-- list -->
	<div class="box-typical-body">
		<div class="table-responsive">
			<table class="table table-hover main-table">
				<thead>
					<tr>

						<th>User</th>
						<th>Email</th>
						<th>User Type</th>
						<th class="text-center">View/Edit</th>

					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $users  as $index => $row ){

					$aua_id = $row->agency_user_account_id;

					?>
						<tr class="<?php echo ( $row->active == 0 )?'opa-down':''; ?>">

							<td class="fname_td">
								<?php
									echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
									echo "{$row->fname} {$row->lname}";
								?>
							</td>
							<td><?php echo $row->email; ?></td>
							<td><?php

								if($userType=="1"){ //admin
									if($aua_id != $this->session->aua_id){
								?>
									<select data-aua_id="<?php echo $aua_id; ?>" class="form-control user_type" name="user_type"  style="width:200px;">
										<?php
											foreach($typeOptions as $row2){
												?>
													<option <?php echo ($row->agency_user_account_type_id == $row2->agency_user_account_type_id)?'selected="selected"':'' ?> value="<?php echo $row2->agency_user_account_type_id; ?>"><?php echo $row2->user_type_name; ?></option>
												<?php
											}
										?>
									</select>
								<?php
									}else{
										echo $row->user_type_name;
									}
								}else{
									echo $row->user_type_name;
								}

							?></td>
							<td class="text-center">
								<input  type="hidden" class="aua_id" value="<?php echo $aua_id; ?>" />
								<a data-toggle="tooltip" title="View" class="a_link" href="/user_accounts/my_profile/<?php echo $aua_id; ?>">
									<span class="font-icon font-icon-eye"></span>
								</a>
							</td>




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
.a_link {
    margin-right: 8px;
}
.a_link{
	border-bottom: none !important;
}
.btn_add_user,
.btn_show_all{
	position: relative;
    bottom: 8px;
    margin: 0 !important;
	margin-left: 10px !important;
}
.font-icon{
	color:#adb7be
}
.font-icon:hover{
	color:#00a8ff;
}
.font-icon-del:hover{
	color:#fa424a
}
.sweet-alert{
    width:auto !important;
}
</style>
<script>
jQuery(document).ready(function(){

	//select2
	$(".select2-photo").not('.manual').select2({
		templateSelection: select2Photos,
		templateResult: select2Photos
	 })

	 $('.user_type').on('change',function(){
		 var thisVal = $(this).val();
		 var aua_id = $(this).attr('data-aua_id');

		 swal(
                    {
                        title: "",
                        text: 'Are you sure you want to update user type?',
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes, Update",
						cancelButtonClass: "btn-danger",
                        cancelButtonText: "No, Cancel!",
                        closeOnConfirm: false,
                        closeOnCancel: true,
                    },
                    function(isConfirm){
                        if(isConfirm){

							jQuery.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('/user_accounts/updateUserType') ?>",
                                    dataType: 'json',
                                    data: {
                                        aua_id: aua_id,
										user_type: thisVal,
                                    }
                                    }).done(function(data){
                                        if(data.status){
                                            swal({
                                                title:"Success!",
                                                text: "User Type successfully updated",
                                                type: "success",
                                                showCancelButton: false,
                                                confirmButtonText: "OK",
                                                closeOnConfirm: false,

                                            },function(isConfirm){
                                               if(isConfirm){
													swal.close();
													location.reload();
												}
                                            });
                                        }
                                    });

                        }else{
							location.reload();
						}

                    }
            );

	 })


});
</script>