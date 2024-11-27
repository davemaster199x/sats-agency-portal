<?php 
	$this->load->view('/templates/themes/'. $this->config->item('theme') .'/dashboard.php');
?> 
<div id="pt_fully_connect_invite_fb">
    <div class="text-center mb-3"><img src="/images/api/prop_tree_normal.png" /></div>
    <p><?=config_item('COMPANY_NAME_SHORT');?> are thrilled to announce that an updated version of our Property Tree Integration is now available! </p>
    
    <div>
        <p><b>What's New?</b></p>
        
        <p>
        <ul>
            <li>Invoices can now be uploaded directly as a 'Creditor Invoice' located in the Accounting section</li>
            <li>Statements of Compliance can now be uploaded directly into the 'Compliance' tab</li>
            <li>Smoke alarm compliance information auto-completed in the 'Compliance Register' </li>            
			<li>All documents uploaded by <?=config_item('COMPANY_NAME_SHORT');?> are also available in the 'Documents' tab in the Creditor Portfolio</li>
        </ul>
        </p>
        
        <p><b>Ready to Proceed?</b></p>
    </div>
    <div class="text-center mt-5">
        
		<button id="pt_yes_update_now" class="btn btn-success mr-2">Yes - Update Now</button>
        <button id="pt_remind_later_btn" class="btn btn mr-2">Remind Me Later</button>
        <button id="pt_keep_curren_ver_btn" class="btn btn-danger mr-2">No - Keep My Current Version</button>
    </div>
</div>
<style>
#pt_fully_connect_invite_fb ul {
	list-style: disc !important;
}
#pt_fully_connect_invite_fb{
	display: none;
}
</style>
<script>
// propertytree snooze function
function pt_snooze(snooze_days){
	var agency_id = <?php echo $this->session->agency_id ?>;
	var popup_id = 'pt_fully_connect_invite_fb';
	jQuery.ajax({
		type: "POST",
		url: "/home/pt_popup_snooze",
		data: {
			agency_id: agency_id,
			popup_id: popup_id,
			snooze_days: snooze_days
		}
	}).done(function( ret ) {
		location.reload();
	});
}
jQuery(document).ready(function(){

	<?php
	
	// propertytree final step
	if(
		( ( $pt_has_tokens == true && $pt_has_preference == false ) && $has_set_pt_popup_settings == false ) ||
		(
			( $pt_has_tokens == true && $pt_has_preference == false ) &&
			(
				( $pt_popup_row->show_again_in == '' ) ||
				( $pt_popup_row->show_again_in != '' &&  date('Y-m-d') >= $pt_popup_row->show_again_in )
			)
		)
	){ ?>
		$.fancybox.open({
			src  : '#pt_fully_connect_invite_fb'
		});
	<?php
	}
	
	?>

	// PropertyTree popup - remind me later
	jQuery("#pt_remind_later_btn").click(function(){
		var agency_id = <?php echo $this->session->agency_id ?>;
		var popup_id = 'pt_fully_connect_invite_fb';
		var snooze_days = 30;
		pt_snooze(snooze_days);
	});

	// PropertyTree popup - remind me later
	jQuery("#pt_keep_curren_ver_btn").click(function(){
		var agency_id = <?php echo $this->session->agency_id ?>;
		var popup_id = 'pt_fully_connect_invite_fb';
		var snooze_days = 90;
		pt_snooze(snooze_days);
	});

});
</script>
