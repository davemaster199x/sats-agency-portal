<?php 
$form_attr = array(
	'id' => 'jform',
	'class'=>'sign-box'
);
echo form_open('/user_accounts/save_password',$form_attr); 
?>
	<div style="text-align: center;">
		<img src="<?= theme('images/login_logo.png')?>" style="width: 260px; margin-bottom: 10px" />
	</div>
	<header class="sign-title">New Password</header>
	
	
	
	<?php
	if( isset($set_pass_success) && $set_pass_success == true ){ ?>
		<div class="alert alert-success">
			Your new password is now saved. <a href="<?php echo site_url(); ?>">Go to homepage</a>
		</div>
	<?php
	}
	?>

	<?php
	if( isset($_GET['msg']) && $_GET['msg'] == true ){ ?>
		<div class="alert alert-success">
			Your new password is now saved. <a href="<?php echo site_url(); ?>">Go to homepage</a>
		</div>
	<?php
	}
	?>

	
	
	<div class="form-group">
		<input type="password" name="new-pass" class="form-control" placeholder="New Password"
		data-validation="[NOTEMPTY]"
		/>
	</div>
	<div class="form-group">
		<input type="password" name="confirmpass" class="form-control" placeholder="Confirm Password" 
		data-validation="[V==new-pass]"
		data-validation-message="Passwords Don't Match"
		/>
	</div> 
	<div>
		<button type="submit" class="btn">
			<span>Set New Password</span>
		</button>
	</div>

	<input type="hidden" name="enc" value="<?php echo ( isset($_GET['sp']) )?$_GET['sp']:''; ?>" />
	<input type="hidden" name="user_id" value="<?php echo ( isset($user_id) )?$user_id:''; ?>" />
	
</form>

<script>
	$(function() {
		
		
		// jquery form validation
		jQuery('#jform').validate({
			submit: {
				settings: {
					inputContainer: '.form-group',
					errorListClass: 'form-tooltip-error'
				}					
			},
			labels: {
				'new-pass': 'New Password',
				'confirmpass': 'Confirm Password'
			}
		});
		
		
	});
</script>