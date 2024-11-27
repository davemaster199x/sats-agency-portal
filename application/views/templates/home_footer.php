<?php
$data = $this->templatedatahandler->getData();
?>
</div><!--.container-fluid-->
	</div><!--.page-content-->

<!-- Switch Agency lightbox -->
<a data-fancybox data-src="#swith_agency_fb" id="swith_agency_fb_link" style="display:none;" href="javascript:void(0);">click me</a>
<div style="display: none;" id="swith_agency_fb" >
	<h2>Switch Agencies</h2>
	<ul id="switch_agency_ul">
		<?php
		foreach( $data['agencies'] as $row ){ ?>
			<li>
				<?php if( $this->session->agency_id == $row->agency_id ){ ## current agency logged in > add tickbox and remove link ?>

					<a href="#" style="position: relative;">
						<?php echo $row->agency_name; ?> &nbsp;<span class="fa fa-check-circle check-input-ok" style="display: inline;margin-top:-6px;font-size:18px;"></span>
					</a>
				
				<?php }else{ ?>

					<a href="/sys/switch_agency/<?php echo $row->agency_id; ?>">
						<?php echo $row->agency_name; ?>
					</a>

				<?php } ?>
				
			</li>
		<?php
		}
		?>
	</ul>
</div>

<div class="modal bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
      	<h7>You've been idle for 30 Minutes <i class="fa fa-clock-o"></i></h7>
      </div>
      <div class="modal-body">
		<p>You will be logout in <span id="counter">15</span> second(s) unless you press 'Extend Session'.</p>
      	<i class="fa fa-question-circle"></i> Do you want to extend?
	  </div>
      <div class="modal-footer"><a href="javascript:;" id="extendBtn" class="btn btn-primary btn-block">Extend Session</a></div>
    </div>
  </div>
</div>

<div id="console_terms_view_only" class="fancybox"> 

	<h3>Terms and Conditions</h3>
	<?php echo $this->system_model->console_terms_and_conditions(); ?>
    
</div>

<div id="pt_select_settings" class="fancybox" style="display:none;">

	<h3>Invoice Configuration Settings</h3>

	<div id="pt_preference_tbl_div"></div>

	<div class="text-right mt-2">
		<button type="button" id="pt_select_preference_btn" class="btn">Save</button>
	</div>

</div>
<!-- used on google address autocomplete -->
<style>
#pt_preference_tbl_div table td{
	border: none;
}
#pt_select_settings{
	border: 3px solid #16b4fc;
}
.ur_connected_api_logo{
	width: 250px !important;
}
#preloader{
	z-index: 99999;
}
</style>
<script>
	function initPlaces() {
		try {
			initAutocomplete();
		}
		catch(ex) {}
	}
</script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= config_item('gmap_api_key'); ?>&callback=initPlaces&libraries=places" async defer></script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="/inc/js/lib/input-mask/jquery.mask.min.js"></script>

<?php if($this->config->item('theme') === "sas"): ?>
    <?php if (in_array($this->uri->uri_string(), array("/", "home", "sys/search"))): ?>
        <script src="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.7/dist/autoComplete.min.js"></script>
        <script>
            $(document).ready(function () {
                // Auto Complete
                const autoCompleteJS = new autoComplete({
                    placeHolder: "Search for Address...",
                    data: {
                        src: <?= json_encode($address_tags) ?>,
                    },
                    submit: true,
                    events: {
                        input: {
                            selection: (event) => {
                                const selection = event.detail.selection.value;
                                autoCompleteJS.input.value = selection;
                                document.getElementById('autoComplete').value = selection;
                                document.getElementById('search_address').value = selection;
                            }
                        }
                    }
                });
            });
        </script>
    <?php endif; ?>
<?php endif; ?>

<script> 

$(document).idle({
  onIdle: function(){
  	$('.bs-example-modal-sm').modal('show');
    var cTimer = setInterval(function(){ countdown(); },1000);
	jQuery("#extendBtn").click(function(){
        jQuery.ajax({
        	type: "POST",
        	url: "<?php echo base_url('/sys/check_agency_session') ?>",
        }).done(function(data){
        	if (data == "true") {
        		$('#counter').html(15)
		    	clearInterval(cTimer)
		  		$('.bs-example-modal-sm').modal('hide');
        	}else {
        		location.href = '<?=base_url()?>user_accounts/logout';
        	}
        });
	});
  },
  // extend to 30 minutes
  idle: 60000*30
})

function countdown() {
    var i = document.getElementById('counter');
    if (parseInt(i.innerHTML)<=1) {
  		$('.bs-example-modal-sm').modal('hide');
        location.href = '<?=base_url()?>user_accounts/logout';
    }
    i.innerHTML = parseInt(i.innerHTML)-1;
}

jQuery(document).ready(function(){

	// switch agency
	jQuery("#switch_agency_icon").click(function(){
		jQuery("#swith_agency_fb_link").click();
	});

	// loader
	jQuery("#preloader").delay(200).fadeOut("slow");

	jQuery(".view_console_terms").click(function(){

		// launch fancybox: console terms and conditions
		$.fancybox.open({
			src  : '#console_terms_view_only'
		});

	});

	<?php
	//if( $_ENV['APP_ENV'] != 'production' ){ ?>

		jQuery("#pt_yes_update_now, #pt_pref_link_popup").click(function(){

			var agency_id = <?php echo $this->session->agency_id; ?>;

			$.fancybox.close();

			if( agency_id > 0 ){

				jQuery('#preloader').show();
				jQuery.ajax({
					url: "/property_tree/display_agency_preference",
					type: 'POST',
					data: {
						'agency_id': agency_id
					}
				}).done(function( ret ){

					jQuery("#pt_preference_tbl_div").html(ret);

					// launch fancybox
					$.fancybox.open({
						src  : '#pt_select_settings'
					});

					jQuery('#preloader').hide();

				});

			}

		});

		// save propertytree agency preference
        jQuery("#pt_select_preference_btn").click(function(){

			var pt_select_settings_fb = jQuery("#pt_select_settings");

			var agency_id = <?php echo $this->session->agency_id; ?>;
			var creditor = pt_select_settings_fb.find("#pt_creditor").val();
			var account = pt_select_settings_fb.find("#pt_account").val();
			var prop_comp_cat = pt_select_settings_fb.find("#pt_prop_comp_cat").val();

			var error = '';

			if( agency_id > 0 ){

				if( creditor == '' ){
					error += 'Creditor is Required\n';
				}

				if( account == '' ){
					error += 'Account is Required\n';
				}

				if( prop_comp_cat == '' ){
					error += 'Property Compliance Category is Required\n';
				}

				if( error != '' ){ // error
					swal('',error,'error');
				}else{

					jQuery("#preloader").delay(200).fadeIn("slow");
					jQuery.ajax({
						url: "/property_tree/save_agency_preference",
						type: 'POST',
						data: {
							'agency_id': agency_id,
							'creditor': creditor,
							'account': account,
							'prop_comp_cat': prop_comp_cat,
						}
					}).done(function( ret ){

						jQuery("#preloader").delay(200).fadeOut("slow");  
						swal({
							title: "Success!",
							text: "Invoice Configuration Settings Saved!",
							type: "success",
                            confirmButtonClass: "btn-success"
						},
						function(){
							window.location='/api/select_agency_preference/?api=3'
						});

					});

				}

			}

		});
		
	<?php
	//}
	?> 

});
</script>
</body>
</html>