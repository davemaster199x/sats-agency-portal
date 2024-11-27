<div class="box-typical box-typical-padding">

	
	<h5 class="m-t-lg with-border"><a href="/user_accounts/add"><?php echo $title; ?></a></h5>
	
	<?php 
	if( validation_errors() ){ ?>
		<div class="alert alert-danger">
		<?php echo validation_errors(); ?>
		</div>
	<?php
	}	
	?>
	

	<?php 
	$form_attr = array('id' => 'jform');
	echo form_open('user_accounts/add',$form_attr); ?>
		<div class="form-group row">
			<label class="col-sm-2 form-control-label">Email <span class="color-red">*</span></label>
			<div class="col-sm-4">
				<p class="form-control-static">
					<input type="text" name="email" class="form-control" id="inputPassword" placeholder="Email" 					
					data-validation="[EMAIL]"
					/>
				</p>
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-2 form-control-label">First Name <span class="color-red">*</span></label>
			<div class="col-sm-4">
				<p class="form-control-static">
					<input type="text" name="fname" class="form-control" id="inputPassword" placeholder="First Name"
					data-validation="[NOTEMPTY]" 
					/>
				</p>
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-2 form-control-label">Last Name <span class="color-red">*</span></label>
			<div class="col-sm-4">
				<p class="form-control-static">
					<input type="text" name="lname" class="form-control" id="inputPassword" placeholder="Last Name"
					data-validation="[NOTEMPTY]"
					/>
				</p>
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-2 form-control-label" for="us-phone-mask-input">Phone</label>
			<div class="col-sm-4">
				<p class="form-control-static">
					<input type="text" name="phone" id="us-phone-mask-input" class="form-control" placeholder="Phone" />
				</p>
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-2 form-control-label">Job Title</label>
			<div class="col-sm-4">
				<p class="form-control-static">
					<input type="text" name="job_title" class="form-control" placeholder="Job Title" />
				</p>
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-2 form-control-label">User Type <span class="color-red">*</span></label>
			<div class="col-sm-4">
				<p class="form-control-static">
					<select name="user_type" class="form-control user_types" data-validation="[NOTEMPTY]">
						<?php 
						foreach ( $user_types  as $user_type ){ ?>
							<option value="<?php echo $user_type->agency_user_account_type_id; ?>">
							<?php echo $user_type->user_type_name; ?>
							</option>
						<?php
						}
						?>
					</select>
				</p>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputPassword" class="col-sm-2 form-control-label">&nbsp;</label>
			<div class="col-sm-4">
				<button type="submit" class="btn btn-inline">Submit</button>
			</div>
		</div>		
	</form>
	
	
	
	
				
</div>
<script>
jQuery(function() {
	
	
	
	<?php 
	
	if( $this->session->flashdata('new_user_added') == 1 ){ ?>
	
		swal({
			title: "Success!",
			text: "New User Account Added.\nAn Invitation Email has been sent.",
			type: "success",
			confirmButtonClass: "btn-success"
		},function(){
			
			//location.reload();
			
		});
	
	<?php
	}
	
	?>
	
	
	// jquery form validation
	jQuery('#jform').validate({
		submit: {
			settings: {
				inputContainer: '.form-group',
				errorListClass: 'form-tooltip-error'
			}
		},
		labels: {
			'fname': 'First Name',
			'lname': 'Last Name',
			'user_type': 'User Type'
		}
	});

});
</script>