<?php 
$form_attr = array(
	'id' => 'jform',
	'class'=>'sign-box'
);
echo form_open('login/submit_2fa_code',$form_attr); 
?>
	<div class="text-center">
		<img src="<?= theme('/images/login_logo.png') ?>" class="<?= $this->config->item('theme') ?> logo login_logo" />
	</div>
	
	<div>
	
		<header class="sign-title">Two-factor Authentication</header>
				
		<?php 
		if( $error == 1 ){ ?>
			<div class="alert alert-danger validation_errors text-center">Code Incorrect</div>
		<?php
		}else if( $error == 2 ){ ?>
			<div class="alert alert-danger validation_errors text-center">Code Expired</div>
		<?php
		}	
		?>
		
		<p>Please enter security code sent to your <?php echo ( $user_2fa_type == 1 )?'mobile':'email'; ?> to proceed and login</p>

		
		<div class="form-group">
			<input type="text" name="user_2fa_code" id="user_2fa_code" class="form-control" placeholder="Enter code here" />
		</div>

		<div id="user_2fa_code_timer" style="display:<?php echo ( $error == '' )?'block':'none'; ?>;"></div>

	
		<div class="text-center">
			<input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id ?>" />
			<input type="hidden" id="user_2fa_type" value="<?php echo $user_2fa_type ?>" />
			<input type="hidden" id="user_2fa_send_to" value="<?php echo $user_2fa_send_to ?>" />

			<button type="button" id="resend_2fa_btn" class="btn" style="display:<?php echo ( $error > 0 )?'inline':'none'; ?>;">Resend Code</button>	
			<button type="submit" id="submit_code_btn" class="btn">Submit Code</button>
		</div>
		
	</div>
	
<?php echo form_close(); ?>

<style>
#resend_2fa_btn{
	display: none;
}
#submit_code_btn{
	display: inline;
}
</style>
<script>
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
		jQuery("#user_2fa_code_timer").show();
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

jQuery(document).ready(function(){

	<?php
	if( $error == '' ){  ?>
		run_2fa_code_timer();
	<?php
	}
	?>
	
	
	jQuery("#jform").submit(function(){

		var user_2fa_code = jQuery("#user_2fa_code").val();
		var error = '';

		if( user_2fa_code == '' ){
			error += "2FA code is required\n";
		}

		if( error != '' ){ // error

			alert(error);
			return false;

		}else{
			return true;
		}

	});

	
	// send code
	jQuery("#resend_2fa_btn").click(function(e){

		var user_id = jQuery("#user_id").val();
		var user_2fa_type = jQuery("#user_2fa_type").val();
		var user_2fa_send_to = jQuery("#user_2fa_send_to").val();
		var error = '';

		if( user_id > 0 && user_2fa_type > 0 && user_2fa_send_to != '' ){

			jQuery.ajax({
				type: "POST",
				url: "/user_accounts/send_2fa_code",
				data: { 
					aua_id: user_id,
					twofa_type: user_2fa_type,
					twofa_send_to: user_2fa_send_to	
				}
			}).done(function( ret ) {

				run_2fa_code_timer();
				//location.reload();

			});							

		}			


	});
	
	
});
</script>