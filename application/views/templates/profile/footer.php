</div>
<!--.container-fluid-->
</div>
<!--.page-content-->
<!-- JS start -->
<script src="/inc/js/lib/jquery/jquery-3.2.1.min.js"></script>
<script src="/inc/js/lib/popper/popper.min.js"></script>
<script src="/inc/js/lib/tether/tether.min.js"></script>
<script src="/inc/js/lib/bootstrap/bootstrap.min.js"></script>
<script src="/inc/js/plugins.js"></script>
<script src="/inc/js/jc.js"></script>
<script src="/inc/js/gherx.js"></script>

<script type="text/javascript" src="/inc/js/lib/jqueryui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/inc/js/lib/lobipanel/lobipanel.min.js"></script>
<script type="text/javascript" src="/inc/js/lib/match-height/jquery.matchHeight.min.js"></script>
<script type="text/javascript" src="/inc/js/lib/bootstrap-select/bootstrap-select.min.js"></script>
<script type="text/javascript" src="/inc/js/lib/select2/select2.full.min.js"></script>
<script src="/inc/js/lib/bootstrap-sweetalert/sweetalert.min.js"></script>

<script src="/inc/js/lib/bootstrap-maxlength/bootstrap-maxlength.js"></script>
<script src="/inc/js/lib/bootstrap-maxlength/bootstrap-maxlength-init.js"></script>
<script src="/inc/js/lib/hide-show-password/bootstrap-show-password.min.js"></script>
<script src="/inc/js/lib/hide-show-password/bootstrap-show-password-init.js"></script>


<script src="/inc/js/app.js"></script>
<!-- JS end -->



<script type="text/javascript">


jQuery(document).ready(function(){
  
    // remove additional property box
    jQuery(document).on('click','.btn_new_prop_delete', function(e){
        e.preventDefault();
        jQuery(this).closest('.row').remove();
    });
  
    // add property box
    jQuery('#btn_add_new_box').on('click',function(e){

        e.preventDefault();
        var htm ='<div class="row form-group">'+'<div class="col-md-5">'+
                    '<input type="text" name="add_pm_name[]" placeholder="Name" class="form-control">'+
                    '</div>'+
                    '<div class="col-md-5">'+
                    '<input type="text" name="add_pm_email[]" placeholder="Email"  class="form-control">'+
                    '</div>'+
                    '<div class="col-md-2">'+
                    '<button type="button" class="btn btn-inline btn-danger btn_new_prop_delete"><span class="del glyphicon glyphicon-remove"></span></button>'+
                    '</div>'+
                    '</div>';

        jQuery('#pm-fields').append(htm);

    });

    // delete property manager from database table via ajax

    jQuery(document).on('click','.btn_prop_delete', function(e){
        
        e.preventDefault();
        var obj = jQuery(this);
        var hidden_pm_id = obj.closest('.row').find('.hid_pm_id').val();
        
        swal({
                title: "",
                text: "Are you sure you want to delete this property manager?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {

                    // ajax remove property here...
                    jQuery.ajax({
                        type: "post",
                        url: "profile/delete_property_manager",
                        data: { hid_pm_id: hidden_pm_id, agency_id: <?php echo $this->session->agency_id; ?>},
                        dataType: "json",
                        success: function(response){
                            if(response.success){
                                // removed property manager box in the list
                                obj.closest('.row').remove();
                                // deletion success message
                                swal("Deleted!", "Property manager has been deleted", "success");
                            }else{
                                swal("Error", "Error deleting please try again!", "error");
                            }
                        },
                        error: function(){
                            swal("Cancelled", "Error deleting please try again!", "error");
                        }
                    })

                } else {
                    swal("Cancelled", "Deleting property manager has been cancelled", "error");
                }
             });

         });


    // total   property mangege tweak
    jQuery("#tot_prop").change(function(){
		jQuery("#tot_prop_changed").val(1);
	});

    //success message 
    jQuery('.p_success_msg').fadeOut(9000);



 });
    
    


</script>


</body>

</html>