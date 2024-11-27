<style>
.ttmo{
	white-space: normal!important;
}
.get_2fa_code_class,
#enable_2fa_btn,
#resend_2fa_btn{
	display: none;
}
.user_2fa_tbl td, .user_2fa_tbl th{
	border: none;
}
.jfadeout{
	opacity: 0.5;
}
#twofa_code{
	float: left;
}
</style>
<div class="box-typical box-typical-padding">

	<?php
	if( isset($aua_id) && $aua_id > 0 ){ ?>
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="/user_accounts">User Accounts</a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="/user_accounts/my_profile/<?php echo $this->uri->segment(3) ?>"><?php echo $title; ?></a></li>
		  </ol>
		</nav>
	<?php	
	}
	?>	

	<h2 class="text-center"><?php echo $dynamic_user_txt; ?> Profile</h2>
	
	<?php 
	if( isset($upload_error) ){ ?>
		<div class="alert alert-danger">
		<?php echo $upload_error; ?>
		</div>
	<?php
	}	
	?>
	
	<?php 
	if( isset($upload_image_error) ){ ?>
		<div class="alert alert-danger">
		<?php echo $upload_error; ?>
		</div>
	<?php
	}	
	?>
	
	<?php 
	if( validation_errors() ){ ?>
		<div class="alert alert-danger">
		<?php echo validation_errors(); ?>
		</div>
	<?php
	}	
	?>
	
	
	
	<section class="box-typical">
	
	
	<?php 
	$form_attr = array('id' => 'jform');
	echo form_open_multipart("user_accounts/edit/{$user->agency_user_account_id}",$form_attr); 
	?>
	
	
		<div class="profile-card">
			
			<div class="form-group row">
			<div class="text-center" style="margin:auto;">
				<div class="form-control-static crop_preview_div">
					<div class="upload-demo-wrap">
						<div id="upload-demo"></div>
					</div>
				</div>
				<div class="container-fluid crop_result_div">	
					<label>
						<img id="crop_result" class="photo_pic border border-info" />
					</label>						
				</div>	
				
				<button type="button" class="btn btn-inline btn_crop">Crop</button>
				<button type="button" class="btn btn-inline btn-danger btn_crop_cancel">Cancel</button>
			
				<input type="hidden" id="crop_image_base64" name="crop_image_base64" />
				
				<div class="form-control-static current_photo_div" >
					
				
					<div class="container-fluid editable_box">	
						<label>
							<img class="photo_pic border border-info" src="<?php echo (!empty($user->photo))?"{$photo_path}/{$user->photo}":$this->config->item('photo_empty') ?>" />
						</label>
						<label style="display:none;" data-toggle="tooltip" class="font-icon font-icon-pencil photo_lbl editable_input" for="photo" title="Edit"></label>
						<label style="display:none;" data-toggle="tooltip" class="font-icon font-icon-del float-left delete_link editable_input" title="Delete"></label>
					</div>	
						
				</div>
				
					<div class="form-control-static photo_orig_browse hidden">
						<input value="<?php echo $user->photo ?>"  type="file" class="form-control-file" name="photo" id="photo" accept="image/*" />
					</div>
				
			</div>
		</div>
		
			<div class="profile-card-name editable_box">
			<div class="editable_text"><?php echo "{$user->fname} {$user->lname}"; ?></div>
                    <div class="row editable_input" style="width:400px;margin:auto;display:none;">
                            <div class="col-lg-6 columns">
                                <div class="form-group form-group-req">
                                    <input data-validation="[NOTEMPTY]" name="fname" id="fname" placeholder="First Name" class="form-control" type="text" value="<?php echo $user->fname ?>">
                                </div>
                            </div>
                            <div class="col-lg-6 columns">
                                <div class="form-group form-group-req">	
                                    <input data-validation="[NOTEMPTY]" name="lname" id="lname" placeholder="Last Name" class="form-control" type="text" value="<?php echo $user->lname ?>">
                                </div>
                            </div>
                    </div>
			</div>
            
            
			<div class="editable_box">
                <div class="editable_text">   <?php echo ( isset($user->job_title) && $user->job_title != '' )?$user->job_title:'' ?> </div>

                    <div class="row editable_input" style="width:300px;margin:auto;display:none;">
                        <div class="col-lg-12 columns">
                            <div class="form-group">
                                <input name="job_title" id="job_title" class="form-control" type="text" placeholder="Position/Job Title" value="<?php echo $user->job_title ?>">
                            </div>
                        </div>
                    </div>
		
			</div>
			<div class="profile-card-location"><?php echo $user->agency_name; ?></div>
			
			
		
		</div><!--.profile-card-->

		<div class="profile-statistic tbl">
			<div class="tbl-row">
				<div class="tbl-cell">
					<b><?php echo $prop_count; ?></b>
					<a class="edit_disable" href="/properties/index/<?php echo $aua_id; ?>">Properties</a>
				</div>
				<div class="tbl-cell">
					<b><?php echo $active_jobs; ?></b>
					<a class="edit_disable" href="/jobs/index/<?php echo $aua_id; ?>">Active Jobs</a>
				</div>
			</div>
		</div>
		
		
		<ul class="profile-links-list">
			<li class="nowrap editable_box">
				<div class="editable_text">
					<a href="javascript:void(0);">
						<i class="font-icon font-icon-phone"></i>
					</a>
					<span class="profile-link-span">
						<?php echo ( isset($user->phone) && $user->phone != '' )?$user->phone:'' ?> 
					</span>
				</div>
				<div class="editable_input pos_rel" style="display:none;">
				    <input name="phone" id="phone" class="form-control col-lg-3" placeholder="Phone Number" type="text" value="<?php echo (isset($user->phone))?$user->phone:'' ?>">
				</div>
			</li>
			<li class="nowrap editable_box">
				<div class="editable_text">
					<a href="javascript:void(0);">
						<i class="font-icon font-icon-mail"></i>
					</a>
					<span class="profile-link-span"><?php echo $user->email; ?></span>
				</div>
				<div class="editable_input pos_rel form-group-req" style="display:none">
					<input data-validation="[EMAIL]" name="email" id="email" class="form-control col-lg-3" placeholder="Email" type="text" value="<?php echo $user->email ?>">
				</div>
			</li>
			<li class="nowrap">
				<a href="javascript:void(0);">
					<i class="font-icon font-icon-lock"></i>
				</a>
				<span class="profile-link-span">Last Password Change: <?php echo ( isset($last_pass_update) && $this->jcclass->isDateNotEmpty($last_pass_update) )?date('d/m/Y H:i',strtotime($last_pass_update)):''; ?></span>
			</li>
			<li class="nowrap">
				<a href="javascript:void(0);">
					<i class="fa fa-sign-in"></i>
				</a>
				<span class="profile-link-span">Last Login: <?php echo ( isset($last_login) && $this->jcclass->isDateNotEmpty($last_login) )?date('d/m/Y H:i',strtotime($last_login)):''; ?></span>
				<input type="hidden" name="user_type" id="user_type" value="<?php echo $user->user_type ?>">
				<input type="hidden" name="aua_id" id="aua_id" value="<?php echo $user->agency_user_account_id; ?>">
			</li>
			<li class="divider"></li>
			<li style="text-align: center;">
			
				<?php
				// only you and admin can edit
				if( $aua_id == $this->session->aua_id || $logged_user_ut == 1 ){ ?>
				
					<button class="p_edit_button btn" type="button" value="Edit">Edit Profile</button>
					<button style="display:none;" class="p_edit_button_submit btn" type="submit" value="Submit">Save</button>
					<button style="display:none;" class="p_cancel_button btn btn-danger" type="button" value="Cancel">Cancel</button>
				
				<?php	
				}
				?>
								
				<a class="edit_disable reset_pass_btn" href="javascript:void(0);" data-href-val="/user_accounts/reset_password/<?php echo $user->agency_user_account_id; ?>">
					<button type="button" class="btn">Reset Password</button>
				</a>
				<a class="edit_disable" href="/user_accounts/logins/<?php echo $aua_id; ?>">
					<button type="button" class="btn"><?php echo $dynamic_user_txt; ?> Logins</button>
				</a>
				<a class="edit_disable" href="/logs/activity/<?php echo $aua_id; ?>">
					<button type="button" class="btn"><?php echo $dynamic_user_txt; ?> Activity</button>
				</a>
				<button type="button" id="2fa_btn" class="btn">Two-Factor Authentication (2FA)</button>
				
				<?php
				// only you and admin can activate/deactivate
				if( $aua_id == $this->session->aua_id || $logged_user_ut == 1 ){ ?>
				
					<?php 
					if( $user->active == 1 ){ ?>
						<button type="button" class="btn btn-inline btn-danger-outline edit_disable" id="btn_deactivate">Deactivate User</button>
					<?php
					}else{ ?>
						<button type="button" class="btn btn-inline btn-success-outline edit_disable" id="btn_reactive">Restore User</button>
					<?php
					}
					?>	
				
				<?php	
				}
				?>

				
								
			</li>
		</ul>

		
		
		</form>
		
		
	</section>

</div>

<!-- fancybox start -->
<div id="2fa_fb" class="fancybox" style="display:none;">  

    <h3>Two-Factor Authentication (2FA)</h3>
    <p class="user_2fa_choose_type_class">Please select type of 2FA to send code to:</p>

	<table class="table user_2fa_tbl">
		<tr class="user_2fa_tbl_tr <?php echo ( $user_2fa_row->type == 1 )?null:'jfadeout'; ?> user_2fa_choose_type_class">
			<th>Mobile: </th>
			<td><input name="twofa_send_to_mobile" id="twofa_send_to_mobile"class="form-control twofa_send_to_mobile" type="text" value="<?php echo ( $user_2fa_row->type == 1 )?$user_2fa_row->send_to:$user->phone; ?>" /></td>
			<td><input type="radio" class="form-control twofa_type" name="twofa_type" <?php echo ( $user_2fa_row->type == 1 )?'checked':null; ?> value="1" /></td>
		</tr>
		<tr class="user_2fa_tbl_tr <?php echo ( $user_2fa_row->type == 2 )?null:'jfadeout'; ?> user_2fa_choose_type_class">
			<th>Email: </th>
			<td><input name="twofa_send_to_email" id="twofa_send_to_email" class="form-control twofa_send_to_email" type="text" value="<?php echo ( $user_2fa_row->type == 2 )?$user_2fa_row->send_to:$user->email; ?>" /></td>
			<td><input type="radio" class="form-control twofa_type" name="twofa_type" <?php echo ( $user_2fa_row->type == 2 )?'checked':null; ?> value="2" /></td>
		</tr>
		<tr class="user_2fa_choose_type_class send_2fa_code_tr">
			<td></td>
			<td>
				<?php
				if( $user_2fa_row->id > 0 ){ ?>
					<button type="button" id="2fa_disable_btn" class="btn btn-danger float-right ml-3">Disable 2FA</button>
				<?php
				}
				?>	
				<button type="button" id="enable_2fa_btn" class="btn float-right">Send Code</button>								
			</td>
			<td></td>
		</tr>
		<tr class="get_2fa_code_class">
			<th>Code</th>
			<td>
				<div>Please enter the code that was sent to your <span id="send_code_to_span"></span></div>
				<input name="twofa_code" id="twofa_code" placeholder="" class="form-control twofa_code" type="text" />
				<div id="user_2fa_code_timer"></div>						
			</td>
			<td></td>
		</tr>	
		<tr>
			<td>
				<button type="button" id="resend_2fa_btn" class="btn float-right">Resend Code</button>	
			</td>
			<td>
				<button type="button" id="conf_and_save_2fa_btn" class="btn btn-success get_2fa_code_class float-right">Confirm Code and Proceed</button>							
			</td>
			<td></td>
		</tr>	
	</table>
        
    
</div>
<!-- fancybox end -->

<style>
.fa-sign-in {
    vertical-align: middle;
    margin: 0 5px 0 0;
    font-size: 1.2rem;
    position: relative;
    top: -1px;
}
.profile-links-list a{
	color:#adb7be
}
.profile-links-list .font-icon{
	color: unset;
}
.profile_pic {
    border-radius: 50%;
    width: 200px;
	height: 200px;
}
.btn-danger-outline{
	margin: 0 !important;
}


/*new*/
.btn_save_pw{
	margin-top: 10px;
}
.new_pass_div{
	display: none;
}
.photo_pic{
	border: none !important;
	width:200px;
	height:200px;
}
.pw_lbl{
	display: none;
}
.photo_lbl{
	cursor: pointer;
	position: absolute;
	top: 0;
	right: 0;
}
.font-icon-del{
	cursor: pointer;
	position: absolute;
	top: 30px;
	right: 0;
}

/* Croppie */
.upload-demo .upload-demo-wrap,
.upload-demo .btn_crop,
.upload-demo .btn_crop_cancel,
.upload-demo.ready .upload-msg {
    display: none;
}
.upload-demo.ready .upload-demo-wrap {
    display: block;
	margin:auto;
}
.upload-demo.ready .btn_crop,
.upload-demo.ready .btn_crop_cancel {
    display: inline-block;    
}
.upload-demo-wrap {
    width: 230px;
    height: 230px;
}

.upload-msg {
    text-align: center;
    padding: 50px;
    font-size: 22px;
    color: #aaa;
    width: 260px;
    margin: 50px auto;
    border: 1px solid #aaa;
}
.croppie_row{
	margin-bottom: 56px;
}

.btn_crop, 
.btn_crop_cancel{
	position: relative;
	left: 200px;
	bottom: 139px;
	display:none;
}

.crop_result_div,
.crop_preview_div{
	display: none;
}
.current_photo_div{
	position: relative;
	padding: 0 25px;
}





</style>


<script type="text/javascript">
function run_2fa_code_timer(){

	// set countdown, 5 minutes
	var min = 4;
	var sec = 60;

	var my_interval = setInterval(function () {
		
		sec -= 1; // decrement seconds
		
		// pad 0's to single digit number
		var min_str = min.toString().padStart(2, '0'); 
		var sec_str = sec.toString().padStart(2, '0'); 
		
		// display
		jQuery("#user_2fa_code_timer").text(display_clock = min_str+":"+sec_str);
		
		// stop clock
		if( min == 0 && sec == 0 ){
			clearInterval(my_interval); // clear when it hits 00:00

			jQuery("#user_2fa_code_timer").html('<span class="text-danger">Expired</span>');
			jQuery("#resend_2fa_btn").show();
		}
		
		// reset
		if( sec == 0 ){
			sec = 60; // reset back to 60 seconds
			min -= 1; // decrement minute
		}
		
	}, 1000);

}


function select_2fa_type_script(radio_dom){

	jQuery(".user_2fa_tbl_tr").addClass('jfadeout'); // fade both from the start

	var parent_tr = radio_dom.parents("tr:first");
	parent_tr.removeClass('jfadeout'); // fadeout only the selected
	var twofa_type_val = radio_dom.val();
	var send_code_to_span = jQuery("#send_code_to_span");
	var send_code_btn_txt = null;

	if( twofa_type_val == 1 ){
		send_code_btn_txt = 'Send SMS';
		send_code_to_span.text('Phone');
	}else{
		send_code_btn_txt = 'Send Email';
		send_code_to_span.text('Email');
	}

	jQuery("#enable_2fa_btn").text(send_code_btn_txt);
	jQuery("#enable_2fa_btn").show(); // show send code button

}

$(document).ready(function(){
		
	<?php
	if( $this->session->flashdata('reset_password') == 1 ){ ?>
				
			swal({
				title: "Success!",
				text: "Reset confirmation email has been sent",
				type: "success",
				confirmButtonClass: "btn-success"
			},function(){
				
				//location.reload();
				
			});
			
	<?php
	}
	?>

	 //success/error message sweet alert pop  start
	 <?php if( $this->session->flashdata('status') &&  $this->session->flashdata('status') == 'success' ){?>
		swal({
			title: "Success!",
			text: "<?php echo $this->session->flashdata('success_msg') ?>",
			type: "success",
			confirmButtonClass: "btn-success"
		});
	<?php }else if(  $this->session->flashdata('status') &&  $this->session->flashdata('status') == 'error'  ){ ?>
		swal({
			title: "Error!",
			text: "<?php echo $this->session->flashdata('error_msg') ?>",
			type: "error",
			confirmButtonClass: "btn-danger"
		});
	<?php } ?>
	//success/error message sweel alert pop  end
	
	
	
	
	
	
	// reset password confirm
	jQuery(".reset_pass_btn").click(function(e){
		
		var url = jQuery(this).attr("data-href-val");
		
		swal({
			title: "Reset Password",
			text: "Are you sure you want to reset password?",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Yes",
			cancelButtonText: "No",
			closeOnConfirm: true
		},
		function(isConfirm) {
			
			if (isConfirm) { // yes

				// continue with the link
				window.location = url;
				
			}
			
		});
		
	});





	function validateFileType(){

		var error = '';

		var fileName = document.getElementById("photo").value;
		var idxDot = fileName.lastIndexOf(".") + 1;
		var extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
		if (extFile=="jpg" || extFile=="jpeg" || extFile=="png"){
			//TO DO
		}else{
			error = 'Only images are allowed!';
		} 

		return error;

	}
	
	// croppie
	var $uploadCrop;

	// read file from input
	function readFile(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			
			reader.onload = function (e) {
				$('.upload-demo').addClass('ready');
				$uploadCrop.croppie('bind', {
					url: e.target.result
				}).then(function(){
					console.log('jQuery bind complete');
				});
				
			}
			
			reader.readAsDataURL(input.files[0]);
		}
		else {
			swal("Sorry - you're browser doesn't support the FileReader API");
		}
	}

	// croppie settings
	$uploadCrop = $('#upload-demo').croppie({
		viewport: {
			width: 200,
			height: 200,
			type: 'circle'
		},
		enableExif: true
	});

	// read file from input
	$('#photo').on('change', function () { 
	
		
		
		var input_err = validateFileType();
		if( input_err != '' ){
			// error
			swal({
				title: "Invalid File Type!",
				text: input_err,
				type: "warning",
				confirmButtonClass: "btn-primary"
			});
			
		}else{
			
			jQuery(".p_edit_button_submit").prop("disabled",true);
			jQuery(".current_photo_div").hide();
			jQuery(".crop_preview_div").css('display','flex');
			readFile(this);
			
			$('.btn_crop').show();
			$('.btn_crop_cancel').show();
			
		}
		 
		
	});

	// crop
	$('.btn_crop').on('click', function () {
		
		$uploadCrop.croppie('result', {
			type: 'base64'
		}).then(function (resp) {
				
			jQuery(".crop_preview_div").hide();
			jQuery("#crop_image_base64").val(resp);
			jQuery("#crop_result").attr("src",resp);
			jQuery(".crop_result_div").show();
			
			$('.btn_crop').hide();
			$('.btn_crop_cancel').hide();
			jQuery(".p_edit_button_submit").prop("disabled",false);
			
		});
		
	});
	
	
	
	// crop cancel
	$('.btn_crop_cancel').on('click', function () {
		
		jQuery(".p_edit_button_submit").prop("disabled",false);
		jQuery(".current_photo_div").show();
		jQuery(".crop_preview_div").hide();			
		$('.btn_crop').hide();
		$('.btn_crop_cancel').hide();
		
	});
	
	
	// delete photo
	jQuery(".delete_link").click(function(e){

		var aua_id = jQuery("#aua_id").val();
		
		swal({
			title: "Delete",
			text: "Are you sure you want to delete this photo?",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Yes",
			cancelButtonText: "No",
			closeOnConfirm: true
		},
		function(isConfirm) {
			
			if (isConfirm) { // yes

				jQuery.ajax({
					type: "POST",
					url: "/user_accounts/delete_user_photo",
					data: { 
						aua_id: aua_id	
					}
				}).done(function( ret ) {
					location.reload();
				});			
				
			} else { // no
				//location.reload();
				
			}
			
		});
		
		
	});
		
		//Edit profile button toggle tweak
		$('.p_edit_button').on('click',function(e){

			e.preventDefault();
			var obj = $(this);
			var editable_box = $('.editable_box');


			//post params
			var aua_id = $('#aua_id').val();
			var user_type = $('#user_type').val();
			var email = $('#p_email').val();
			var fname = $('#p_fname').val();
			var lname = $('#p_lname').val();
			var phone = $('#p_phone_number').val();
			var job_title = $('#p_position').val();


		
				editable_box.find('.editable_input').show();
				editable_box.find('.editable_text').hide();
				$('.edit_disable').addClass('events_none').css('opacity','0.3');
				$('.p_cancel_button').show();
				$('.p_edit_button_submit').show();
				$(this).hide();
			
		})

		$('.p_cancel_button').on('click',function(e){

			e.preventDefault();
			var obj = $(this);
			var editable_box = $('.editable_box');
			
			editable_box.find('.editable_input').hide();
			editable_box.find('.editable_text').show();
			$('.edit_disable').removeClass('events_none').css('opacity','1');
			$('.p_edit_button_submit').hide();
			$('.p_edit_button ').show();
			obj.hide();
			
			
			//hide crop result and crop preveiw and show current photo div
			$('.crop_result_div').hide();
			$('.crop_preview_div').hide();
			$('.btn_crop').hide();
			$('.btn_crop_cancel').hide();
			$('.current_photo_div').show();
			
			//location.reload(); //test
		})
		
		//Reactivate user
		jQuery("#btn_reactive").click(function(){
			
			var aua_id = jQuery("#aua_id").val();
			var fname = jQuery("#fname").val();
			var lname = jQuery("#lname").val();
			var full_name = fname+" "+lname;

			swal({
				title: "Restore User?",
				text: "Restore "+full_name+" to Active?",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn-success",
				confirmButtonText: "Yes, Restore",
				cancelButtonClass: "btn-danger",
				cancelButtonText: "No, Cancel!",
				closeOnConfirm: false
			},
			function(isConfirm) {
				
				if (isConfirm) { // yes
				
					jQuery.ajax({
						type: "POST",
						url: "/user_accounts/reactivate_user",
						data: { 
							aua_id: aua_id	
						}
					}).done(function( ret ) {
						
						//swal('Default User has been set');	
						swal({
							title: "Success!",
							text: full_name+" has been Restored",
							type: "success",
							confirmButtonClass: "btn-success"
						},function(){
							
							location.reload();
							
						});
						
					});			
					
				}
				
			});
			
		});
		
		//Deactivate user
		jQuery("#btn_deactivate").click(function(){

		var aua_id = jQuery("#aua_id").val();
		var fname = jQuery("#fname").val();
		var lname = jQuery("#lname").val();
		var full_name = fname+" "+lname;
		
		swal({
			title: "Deactivate",
			text: "Are you sure you want to deactivate "+full_name.trim()+"?",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Yes, Proceed",
			cancelButtonText: "No, Cancel!",
			closeOnConfirm: false
		},
		function(isConfirm) {
			
			if (isConfirm) { // yes

				// PM property check
				jQuery.ajax({
					type: "POST",
					url: "/user_accounts/pm_property_check",
					data: { 
						aua_id: aua_id	
					}
				}).done(function( ret ) {
					
					var pm_prop = parseInt(ret);
					
					if(  pm_prop > 0 ){
						
						// Property Found
						swal({
							title: "Property Found!",
							text: "Properties managed by "+full_name+" were found",
							type: "warning",
							confirmButtonClass: "btn-danger ttmo",
							confirmButtonText: "Yes, Please un-assign all properties and deactivate this user" ,
							cancelButtonText: "No, Cancel!",
							showCancelButton: true,
						},
						function(isConfirm) {
							
							if (isConfirm) { // yes

								jQuery.ajax({
									type: "POST",
									url: "/user_accounts/delete_user_and_clear_pm_prop",
									data: { 
										aua_id: aua_id	
									}
								}).done(function( ret ) {
									
									var success = parseInt(ret);
									
									if( success == 1 ){
										
										// success
										swal({
											title: "Success!",
											text: full_name+" has been unassigned to all its assigned properties and has been deactivated",
											type: "success",
											confirmButtonClass: "btn-success"
										},function(){
											window.location='/user_accounts/?del=1';
										});
										
									}else{
										
										// error
										swal({
											title: "Failed!",
											text: "Delete Failed",
											type: "error",
											confirmButtonClass: "btn-danger"
										});
										
									}
									
								});
								
							}
							
						});
						
					}else{
						
						// delete user
						jQuery.ajax({
							type: "POST",
							url: "/user_accounts/delete_user",
							data: { 
								aua_id: aua_id	
							}
						}).done(function( ret ) {
							
							var success = parseInt(ret);
							
							if( success == 1 ){
								
								// success
								swal({
									title: "Success!",
									text: full_name+" has been deactivated",
									type: "success",
									confirmButtonClass: "btn-success"
								},function(){
									window.location='/user_accounts/?del=1';
								});
								
							}else{
								
								// error
								swal({
									title: "Failed!",
									text: "Delete Failed",
									type: "error",
									confirmButtonClass: "btn-danger"
								});
								
							}								
							
						});
						
					}
					
				});
				
			}
			
		});
		
	});
		
		
		// jquery form validation
		jQuery('#jform').validate({
			submit: {
				settings: {
					inputContainer: '.form-group-req',
					errorListClass: 'form-tooltip-error'
				}
			},
			labels: {
				'fname': 'First Name',
				'lname': 'Last Name',
				'user_type': 'User Type'
			}
		});
		
		
		
		jQuery("#2fa_btn").click(function(){

			// launch fancybox
			$.fancybox.open({
				src  : '#2fa_fb',
				touch: false // disable panning/swiping
			});
			
		});


		// send code
		jQuery("#enable_2fa_btn, #resend_2fa_btn").click(function(e){

			jQuery(".user_2fa_choose_type_class").hide(); // hide SMS/Email section

			var aua_id = jQuery("#aua_id").val();
			var twofa_type = jQuery(".twofa_type:checked").val();
			var error = '';

			if( aua_id > 0 ){

				if( twofa_type == undefined ){
					error += "2FA type is required\n";
				}else{

					if( twofa_type == 1 ){ // mobile

						var twofa_send_to = jQuery("#twofa_send_to_mobile").val();
						if( twofa_send_to == '' ){
							error += "2FA mobile is required\n";

						}
					}else if( twofa_type == 2 ){ // email
						
						var twofa_send_to = jQuery("#twofa_send_to_email").val();
						if( twofa_send_to == '' ){
							error += "2FA email is required\n";
						}

					}
					
				}

				
				if( error != '' ){ // error

					swal('',error,'error');

				}else{ // success

					jQuery(".get_2fa_code_class").show();
					
					jQuery('#load-screen').show();
					jQuery.ajax({
						type: "POST",
						url: "/user_accounts/send_2fa_code",
						data: { 
							aua_id: aua_id,
							twofa_type: twofa_type,
							twofa_send_to: twofa_send_to	
						}
					}).done(function( ret ) {
						jQuery('#load-screen').hide();
						run_2fa_code_timer();
						//location.reload();
					});

				}							

			}			


		});


		jQuery("#conf_and_save_2fa_btn").click(function(){

			var user_id = jQuery("#aua_id").val();
			var twofa_code = jQuery("#twofa_code").val();

			if( user_id > 0 ){

				jQuery('#load-screen').show();
				jQuery.ajax({
					type: "POST",
					url: "/user_accounts/confirm_2fa_code",
					dataType: "json",
					data: { 
						user_id: user_id,
						twofa_code: twofa_code
					}
				}).done(function( ret ) {

					jQuery('#load-screen').hide();

					console.log(ret);

					var success = parseInt(ret.success);
					var error = parseInt(ret.error);

					if( success == 1 ){

						// success
						swal({
							title: "Success!",
							text: "Two-factor authentication (2FA) enabled!",
							type: "success",
							confirmButtonClass: "btn-success"
						},function(isConfirm) {
							
							if (isConfirm) { // yes

								//location.reload();
								window.location='/user_accounts/my_profile/<?php echo $this->session->aua_id; ?>';		
								
							}
							
						});

					}else{

						if( error == 1 ){ // code incorrect
							swal('','Code Incorrect','error');
						}else if( error == 2 ){ // code expired

							swal('','Code Expired','error');

						}

					}

				});

			}			

		});
		
		
		jQuery("#2fa_disable_btn").click(function(){

			var user_id = jQuery("#aua_id").val();

			if( user_id > 0 ){

				swal({
					title: "",
					text: "Are you sure you want to disable Two-factor Authentication?",
					type: "warning",
					showCancelButton: true,
					confirmButtonClass: "btn-success",
					confirmButtonText: "Yes",
					cancelButtonText: "No",
					closeOnConfirm: true
				},
				function(isConfirm) {
					
					if (isConfirm) { // yes

						jQuery('#load-screen').show();
						jQuery.ajax({
							type: "POST",
							url: "/user_accounts/delete_2fa",
							data: { 
								user_id: user_id
							}
						}).done(function( ret ) {
							jQuery('#load-screen').hide();
							//location.reload();
							window.location='/user_accounts/my_profile/<?php echo $this->session->aua_id; ?>';	
						});
						
					}
					
				});				

			}			

		});


		<?php
		// offer 2FA for user
		if( $popup_2fa == 1 ){ ?>
			jQuery("#2fa_btn").click();
		<?php
		}
		?>

		// select 2FA script
		// radio select
		jQuery(".twofa_type").change(function(){

			select_2fa_type_script(jQuery(this));

		});

		// text input select
		jQuery(".twofa_send_to_mobile").keyup(function(){

			var type_txt_dom = jQuery(this);
			var parent_tr = type_txt_dom.parents("tr:first");
			var twofa_type = parent_tr.find(".twofa_type");
			twofa_type.prop("checked",true); // select

			select_2fa_type_script(twofa_type);

		});


		// mobile format
		var mobile_mask = '<?php echo ( $this->config->item('country') == 1)?'9999 999 999':'999 9999 9999'; ?>';
		jQuery("#twofa_send_to_mobile").mask(mobile_mask);


	})
</script>

