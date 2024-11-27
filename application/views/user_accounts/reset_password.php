<?php 
$form_attr = array('class' => 'sign-box reset-password-box','id' => 'jform');
echo form_open('/user_accounts/reset_password_form',$form_attr); 
?>

	<?php
	if( $this->session->flashdata('reset_pass_success') == 1 ){ ?>
		<div class="alert alert-success">
			Your link for changing your password has been sent to your email.
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

	<?php 
	if( $this->session->flashdata('email_not_found') == 1 ){ ?>
		<div class="alert alert-danger">
		Email doesn't Exist
		</div>
	<?php
	}	
	?>

	<header class="sign-title">Reset Password</header>
	<div class="form-group">
		<input type="text" class="form-control" name="email" placeholder="E-Mail"
		data-validation="[EMAIL]"
		/>
	</div>
	<button type="submit" class="btn">Reset</button> or <a href="/">Sign in</a>
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
				'email': 'Email'
			}
		});
		

	});
</script>