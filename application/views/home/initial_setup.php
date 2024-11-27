<div class="col-sm-8">

	<?php 
	if( validation_errors() ){ ?>
		<div class="alert alert-danger">
		<?php echo validation_errors(); ?>
		</div>
	<?php
	}	
	?>

	<?php echo form_open('home/initial_setup',array('id'=>'initial_setup_form')); ?>

		<section class="box-typical steps-numeric-block">
		
		
			<div class="steps-numeric-inner">
				Hi <span class="colorItBlue"><?php echo $agency_info->agency_name; ?></span><br /><br />
				This is your first time in the new <?=$this->config->item('COMPANY_NAME_SHORT')?> portal. It will take us 1 minute to get you on your way. 
				We just need to ask a few questions and invite the rest of your team.<br /><br />					
			</div>
		
		
			<div class="col-sm">
				<section class="box-typical steps-numeric-block">
					<div class="steps-numeric-header">
						<div class="steps-numeric-header-in">
							<ul>
								<li class="active"><div class="item"><span class="num">1</span>Select an Email Address</div></li>
								<li><div class="item"><span class="num">2</span>A little bit about you</div></li>
								<li><div class="item"><span class="num">3</span>Invite Your Team</div></li>
							</ul>
						</div>
					</div>

					<div id="form_step1" class="steps-numeric-inner form_steps" data-steps_num="1">
						
						<p>
							Your username needs to change from <span class="redFontUnderLine"><?php echo $agency_info->login_id; ?></span> to an email address. 
							This user will become the main user for your account. <span class="redItalics">(Don’t worry, we can always change this again later).</span> 
							Please type the email address below that you would like to use as your main user account.
							
						</p>
		
					
						
						<div class="form-group">
							<input type="text" name="email" class="form-control" id="email" placeholder="Email" data-validation="[NOTEMPTY,EMAIL]" /> 
							<span id="email_available_check" class="font-icon font-icon-ok step-icon-finish" data-toggle="tooltip" title="Email is available to use"></span>
							<span id="email_already_exist_check" class="font-icon font-icon-close step-icon-finish" data-toggle="tooltip" title="Email already exist"></span>
						</div>
						
				
						
					</div>
					
					<div id="form_step2" class="steps-numeric-inner form_steps" data-steps_num="2">
						
						<p>
							Tell us a little bit about you (or the main account holder). We need your/their name but the rest is up to you. 
							<span class="redItalics">(Don’t worry you can always change it later)</span>
						</p>
						
						<div class="form-group">
							<input type="text" name="fname" class="form-control" id="fname" placeholder="First Name (*required)" data-validation="[NOTEMPTY]" />
						</div>
						<div class="form-group">
							<input type="text" name="lname" class="form-control" id="lname" placeholder="Last Name (*required)" data-validation="[NOTEMPTY]" />
						</div>
						<div class="form-group">
							<input type="text" name="job_title" class="form-control" id="job_title" placeholder="Job Title" />
						</div>
						<div class="form-group">
							<input type="text" name="phone" class="form-control" id="phone" placeholder="Phone Number" />
						</div>
						
			
						
					</div>
					
					<div id="form_step3" class="steps-numeric-inner form_steps" data-steps_num="3">
						
						<p>
							Invite your Team. Your team will all get their own user access now to better protect your account. 
							If some of your team below are no longer active please untick them otherwise press ‘Submit’. 
							<span class="redItalics">(Don’t worry you can add more users once we get you inside the portal)</span>
						</p>
					
						
						
						<table class="table table-hover main-table" id="pm_table">
							<thead>
								<tr>
						
									<th>User</th>
									<th>Email</th>		
									<th class="text-center">Invite</th>
									
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ( $users  as $index => $row ){ 
								
									$aua_id = $row->agency_user_account_id;
								
								?>
									<tr class="<?php echo ( $row->active == 0 )?'opa-down':''; ?>">
										
										<td class="fname_td"><?php echo "{$row->fname} {$row->lname}"; ?></td>
										<td><?php echo $row->email; ?></td>
							
										<td class="text-center">
											<input type="checkbox" name="pm_id[]" class="pm_id" value="<?php echo $aua_id; ?>" checked="checked" />
										</td>
									</tr>
		
								<?php
								}
								?>		
														
							</tbody>
						</table>
						
					</div>

					<div class="tbl steps-numeric-footer">
						<div class="tbl-row">
							<input type="hidden" name="delete_admin" id="delete_admin" value="0" />
							<input type="hidden" name="selected_pm" id="selected_pm" value="0" />
							<a href="javascript:void(0);" id="prev_btn" class="tbl-cell">← Previous</a>
							<a href="javascript:void(0);" id="next_btn" class="tbl-cell color-green">Next →</a>
						</div>
					</div>
					
					<input type="hidden" class="go_next" value="0" />
					
				</section><!--.steps-numeric-block-->
			</div>
			

			
			
			
			
			

		</section>
		
	
	</form>
	
</div>



<style>
.steps-numeric-title{
	color: #00a8ff;
}
.form_steps{
	display: none;
}
#form_step1{
	display: block;
}
.step-icon-finish {
    margin-left: 7px;
   
	display: none;
}
.font-icon-ok{
	 color: #46c35f !important;
}
.font-icon-close{
	 color: #fa424a !important;
}
#email{
	max-width: 95%;
	display: inline;
}
.colorItBlue{
	color: #00a8ff;
}
.redFontUnderLine{
	color: #fa424a;
	text-decoration: underline;
}
.redItalics{
	font-weight: bold;
	font-style: italic;
}
#next_btn{
	background-color: #46c35f !important;
	color: white !important;
}
</style>
<script>
function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function validateAdminName(){
	
	//console.log('validate email');
	
	var fname = jQuery("#fname").val();
	var lname = jQuery("#lname").val();
	var ret = true;
	
	if( fname == '' && lname == '' ){
		
		//jQuery(".go_next").val(0);
		ret = false;
		swal({
			title: "Required",
			text: "First Name and Last Name are required",
			type: "warning",
			confirmButtonClass: "btn-success"
		});
		
	}else if( fname == '' ){
		
		//jQuery(".go_next").val(0);
		ret = false;
		swal({
			title: "Required",
			text: "First Name is required",
			type: "warning",
			confirmButtonClass: "btn-success"
		});
		
	}else if( lname == '' ){
		
		//jQuery(".go_next").val(0);
		ret = false;
		swal({
			title: "Required",
			text: "Last Name are required",
			type: "warning",
			confirmButtonClass: "btn-success"
		});
		
	}
	
	return ret;
	
	
}

jQuery(document).ready(function(){
	
	/*
	// disable all link redirects
	jQuery("a").click(function(e){
		e.preventDefault();
	});
	*/
	
	
	// Next Button
	jQuery("#next_btn").click(function(){
		
		// current div
		var current_div = jQuery(".form_steps:visible");
		var steps = parseInt(current_div.attr("data-steps_num"));
		var go_next = parseInt(jQuery('.go_next').val());
		
		console.log("steps:"+steps);
		
			
		if( steps <= 3 ){
		
			
			if( steps == 1 ){ // step 1: validate email
				
				var email = jQuery("#email").val();
				
				if( email == '' ){
					jQuery(".go_next").val(0);
					swal({
						title: "Required",
						text: "Email address is required",
						type: "warning",
						confirmButtonClass: "btn-success"
					});
				}else{
					
					if( validateEmail(email) == false ){	
					
						jQuery(".go_next").val(0);
						swal({
							title: "Invalid Email",
							text: "Please enter a valid email address",
							type: "warning",
							confirmButtonClass: "btn-success"
						});
						
					}
					
				}
				
			}else if( steps == 2 ){	// step 2: validate admin required name
			
				if( validateAdminName() == true ){
					jQuery(".go_next").val(1);
					jQuery(this).html('Finish →');	
				}else{
					jQuery(".go_next").val(0);
				} 
				
				
			}else if( steps == 3 ){	// submit
			
				jQuery(".go_next").val(0);
				jQuery("#initial_setup_form").submit();	
				
			}
			
			var go_next = parseInt(jQuery('.go_next').val());
			if( go_next == 1 ){
				
				// hide current div, show next div
				current_div.hide();	
				current_div.next().fadeIn();
				
				// hide current tab header, show next tab header
				var curren_active_header = jQuery(".steps-numeric-header-in .active");
				curren_active_header.removeClass('active');
				curren_active_header.next().addClass('active');
				
			}
			
			
		}
		
	});
	
	// previous button
	jQuery("#prev_btn").click(function(){
		
		// current div
		var current_div = jQuery(".form_steps:visible");
		var steps = parseInt(current_div.attr("data-steps_num"));
		
		if( steps > 1 ){
			
			if( steps != 2 ){
				jQuery("#next_btn").html('Next →');
			}
			
			// hide current div, show next div
			current_div.hide();	
			current_div.prev().fadeIn();
			
			// hide current tab header, show next tab header
			var curren_active_header = jQuery(".steps-numeric-header-in .active");
			curren_active_header.removeClass('active');
			curren_active_header.prev().addClass('active');
			
		}
		
	});
	
	
	// validate email on the fly
	jQuery("#email").change(function(){
		
		var email = jQuery(this).val();
		
		if( email != '' ){
			
			if( validateEmail(email) == true ){
			
				jQuery.ajax({
					type: "POST",
					url: "/home/email_check_json",
					data: { 
						email: email
					},
					dataType: 'json'
				}).done(function( ret ) {
					
					
					console.log(ret);
					var aua_id = parseInt(ret.aua_id);
					
					if( aua_id > 0 ){ // email exist
						
						jQuery('.go_next').val(0);
						jQuery('#email_available_check').hide();
						jQuery('#email_already_exist_check').fadeIn();
						
						var user_fullname = ret.fname+" "+ret.lname
						
						swal({
							title: "Email Already Exists!",
							text: "Hi "+user_fullname+", we see you are already in the system. Shall we continue?",
							type: "warning",
							showCancelButton: true,
							confirmButtonClass: "btn-primary",
							confirmButtonText: "Yes",
							cancelButtonText: "No",
							closeOnConfirm: true
						},
						function(isConfirm) {
							
							if (isConfirm) { // yes

								// delete admin on submit
								jQuery("#delete_admin").val(1);
								jQuery("#selected_pm").val(aua_id);
								
								
								// step 2 repopulate user name
								jQuery("#fname").val(ret.fname);
								jQuery("#lname").val(ret.lname);
								
								// step 3
								// remove PM row that is email has been selected, so it get sent invite
								jQuery("#pm_table .pm_id").each(function(){
						
									var pm_id = jQuery(this).val();
									
									if( pm_id == aua_id ){
										jQuery(this).parents("tr:first").remove();
									}
									
								});
								
								// proceeds to step 2
								jQuery('.go_next').val(1);
								jQuery('#email_available_check').fadeIn();
								jQuery('#email_already_exist_check').hide();
								jQuery("#next_btn").click();
								
							}
							
						});
						
					}else{ // email available
						

						jQuery('.go_next').val(1);
						jQuery('#email_available_check').fadeIn();
						jQuery('#email_already_exist_check').hide();
						
					}
					
					
					/*
					if( parseInt(ret) == 1 ){ // email exist
						
						jQuery('.go_next').val(0);
						jQuery('#email_available_check').hide();
						jQuery('#email_already_exist_check').fadeIn();
												
					}else{ // email available to use				
						
						jQuery('.go_next').val(1);
						jQuery('#email_available_check').fadeIn();
						jQuery('#email_already_exist_check').hide();
					}
					*/
					
					
				});
				
			}	
			
		}
						
		
	});
	
	
	
	
});
</script>