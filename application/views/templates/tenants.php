
<div id="tenants_ajax_container">

<div class="tabs-section-nav tabs-section-nav-icons">
    <div class="tbl">
        <ul class="nav" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#tabs-1-tab-1" role="tab" data-toggle="tab">
                                            <span class="nav-link-in">
                                                <i class="font-icon font-icon-check-square"></i>
                                                Current Tenants
                                            </span>
                                        </a>
            </li>
            <li class="nav-item inactive_tenants_menu">
                <a class="nav-link" href="#tabs-1-tab-2" role="tab" data-toggle="tab">
                                            <span class="nav-link-in">
                                                <i class="font-icon font-icon-del"></i>
                                                Past Tenants
                                            </span>
                                        </a>
            </li>
        </ul>
    </div>
</div>
<div class="tab-content">
    <!-- tab 1/current tenant --->
    <div role="tabpanel" class="tab-pane fade in active show" id="tabs-1-tab-1">
        <div class="table-responsive">
            <table class="table table-hover tenant_table">
                <thead>
                    <tr style="background:#f6f8fa;">
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Mobile</th>
                        <th>Landline</th>
                        <th>Email</th>
                        <th class="tbl-last-col">Edit/Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($active_tenants)){ ?>
                    <?php foreach($active_tenants as $active_tenants_row): ?>
                    <tr class="tenant_row" style="position:relative;">
                    
                         <td>
                                        <?php echo $active_tenants_row->tenant_firstname ?>
                                        <div style="display:none;"><input type="text" class="tenant_input form-control" name="tenant_fname" value="<?php echo $active_tenants_row->tenant_firstname ?>"></div>
                                    </td>
                                    <td>
                                        <?php echo $active_tenants_row->tenant_lastname ?>
                                        <div style="display:none;"><input type="text" class="tenant_input form-control" name="tenant_lname" value="<?php echo $active_tenants_row->tenant_lastname ?>"></div>
                                    </td>
                                    <td>
                                        <?php echo $active_tenants_row->tenant_mobile ?>
                                        <div style="display:none;"><input type="text" class="tenant_input form-control" name="tenant_mobile" value="<?php echo $active_tenants_row->tenant_mobile ?>"></div>
                                    </td>
                                    <td>
                                        <?php echo $active_tenants_row->tenant_landline ?>
                                        <div style="display:none;"><input type="text" class="tenant_input form-control" name="tenant_landline" value="<?php echo $active_tenants_row->tenant_landline ?>"></div>
                                    </td>
                                    <td>
                                        <?php echo $active_tenants_row->tenant_email ?>
                                        <div style="display:none;"><input type="text" class="tenant_input form-control" name="tenant_email" value="<?php echo $active_tenants_row->tenant_email ?>"></div>
                                    </td>
                                    <td class="tbl-last-col">
                                    <a class="del edit_tenant" data-tenant_id="<?php echo $active_tenants_row->property_tenant_id ?>" href="#" data-toggle="tooltip" title="Edit"><span class="font-icon font-icon-pencil"></span></a>
                                    <a data-prop_id="<?php echo $active_tenants_row->property_id?>" data-tenant_id="<?php echo $active_tenants_row->property_tenant_id ?>" class="del deactivate_tenant" data-toggle="tooltip" title="Remove" href="#"><span class="font-icon font-icon-trash"></span></a>
                                    </td>
                        
                    </tr>
                    <tr class="edit_tenant_field_box" style="display:none;">
                         <td>
                             <div class="form-group">
                                <input placeholder="First Name" data-validation="[NOTEMPTY]" data-validation-label="First Name" class="form-control" type="text" name="edit_tenant_fname" value="<?php echo $active_tenants_row->tenant_firstname ?>">
                            </div>
                         </td>
                         <td>  <div class="form-group"><input placeholder="Last Name" class="form-control" type="text" name="edit_tenant_lname" value="<?php echo $active_tenants_row->tenant_lastname ?>"></div></td>
                         <td> <div class="form-group"> <input class="form-control tenant_mobile" type="text" name="edit_tenant_mobile" value="<?php echo $active_tenants_row->tenant_mobile ?>"></div></td>
                         <td>  <div class="form-group"><input class="form-control phone-with-code-area-mask-input" type="text" name="edit_tenant_landline" value="<?php echo $active_tenants_row->tenant_landline ?>"></div></td>
                         <td> <div class="form-group"> <input placeholder="Email" class="form-control" type="text" name="edit_tenant_email" value="<?php echo $active_tenants_row->tenant_email ?>"></div></td>
                         <td>
                             <div class="form-group">
                             <a data-tenant_id="<?php echo $active_tenants_row->property_tenant_id ?>"  class="update_tenant btn btn-sm " href="$">Update</a>&nbsp;&nbsp;<a class="cancel_tenant btn btn-sm btn-danger " href="#">Cancel</a>
                    </div>
                            </td>
                    </tr>
                    
                
                   <!-- <tr class="tenant_row" style="position:relative;">
                    <td colspan="6" style="padding:0px;"> 
                    <table style="width:100%;" class="table_no_border">
                        <tr class="tenant_row_inner">
                                    <td>
                                        <?php echo $active_tenants_row->tenant_firstname ?>
                                        <div style="display:none;"><input type="text" class="tenant_input form-control" name="tenant_fname" value="<?php echo $active_tenants_row->tenant_firstname ?>"></div>
                                    </td>
                                    <td>
                                        <?php echo $active_tenants_row->tenant_lastname ?>
                                        <div style="display:none;"><input type="text" class="tenant_input form-control" name="tenant_lname" value="<?php echo $active_tenants_row->tenant_lastname ?>"></div>
                                    </td>
                                    <td>
                                        <?php echo $active_tenants_row->tenant_mobile ?>
                                        <div style="display:none;"><input type="text" class="tenant_input form-control" name="tenant_mobile" value="<?php echo $active_tenants_row->tenant_mobile ?>"></div>
                                    </td>
                                    <td>
                                        <?php echo $active_tenants_row->tenant_landline ?>
                                        <div style="display:none;"><input type="text" class="tenant_input form-control" name="tenant_landline" value="<?php echo $active_tenants_row->tenant_landline ?>"></div>
                                    </td>
                                    <td>
                                        <?php echo $active_tenants_row->tenant_email ?>
                                        <div style="display:none;"><input type="text" class="tenant_input form-control" name="tenant_email" value="<?php echo $active_tenants_row->tenant_email ?>"></div>
                                    </td>
                                    <td class="tbl-last-col">
                                    <a class="del edit_tenant" data-tenant_id="<?php echo $active_tenants_row->property_tenant_id ?>" href="#" data-toggle="tooltip" title="Edit"><span class="font-icon font-icon-pencil"></span></a>
                                    <a data-prop_id="<?php echo $active_tenants_row->property_id?>" data-tenant_id="<?php echo $active_tenants_row->property_tenant_id ?>" class="del deactivate_tenant" data-toggle="tooltip" title="Remove" href="#"><span class="font-icon font-icon-trash"></span></a>
                                    </td>
                        </tr>
                        <tr class="edit_tenant_field_box" style="display:none;">
                         <td>
                             <div class="form-group">
                                <input class="form-control" type="text" name="edit_tenant_fname" value="<?php echo $active_tenants_row->tenant_firstname ?>">
                            </div>
                         </td>
                         <td>  <div class="form-group"><input class="form-control" type="text" name="edit_tenant_lname" value="<?php echo $active_tenants_row->tenant_lastname ?>"></div></td>
                         <td> <div class="form-group"> <input class="form-control tenant_mobile" type="text" name="edit_tenant_mobile" value="<?php echo $active_tenants_row->tenant_mobile ?>"></div></td>
                         <td>  <div class="form-group"><input class="form-control phone-with-code-area-mask-input" type="text" name="edit_tenant_landline" value="<?php echo $active_tenants_row->tenant_landline ?>"></div></td>
                         <td> <div class="form-group"> <input class="form-control" type="text" name="edit_tenant_email" alue="<?php echo $active_tenants_row->tenant_email ?>"></div></td>
                         <td><a data-tenant_id="<?php echo $active_tenants_row->property_tenant_id ?>"  class="update_tenant btn btn-sm btn-inline" href="$">Update</a>&nbsp;&nbsp;<a class="cancel_tenant btn btn-sm btn-danger btn-inline" href="#">Cancel</a></td>
                    </tr>
                    </table>
                    </td>
                    </tr> -->

                    <?php endforeach; ?>
                    <?php }else{ ?>
                                           <tr><td colspan="6"><span class="font-icon font-icon-warning red"></span> Property Vacant or No tenants on file</td>
                                            </tr>
                                      <?php   } ?>
                   
                </tbody>
            </table>
        </div>
    </div>

    <!-- tab 2/Past tenant --->
    <div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-2">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Mobile</th>
                        <th>Landline</th>
                        <th>Email</th>
                        <th>Reactivate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($in_active_tenants) && $in_active_tenants){
                                            foreach($in_active_tenants as $in_active_tenants_row):
                                        ?>
                    <tr class="tenant_row">
                        <td>
                            <?php echo $in_active_tenants_row->tenant_firstname ?>
                        </td>
                        <td>
                            <?php echo $in_active_tenants_row->tenant_lastname ?>
                        </td>
                        <td>
                            <?php echo $in_active_tenants_row->tenant_mobile ?>
                        </td>
                        <td>
                            <?php echo $in_active_tenants_row->tenant_landline ?>
                        </td>
                        <td>
                            <?php echo $in_active_tenants_row->tenant_email ?>
                        </td>
                        <td><a data-prop_id="<?php echo $in_active_tenants_row->property_id?>" data-tenant_id="<?php echo $in_active_tenants_row->property_tenant_id ?>" class="refresh reactivate_tenant" data-toggle="tooltip" title="Marked as active" href="#"><span class="font-icon font-icon-refresh"></span></a></td>
                    </tr>

                    <?php 
                            endforeach;
                        }else{
                            echo '<tr><td colspan="6"><span class="font-icon font-icon-warning red"></span> No Inactive Tenants Found</td></tr>';
                    } ?>


                </tbody>
            </table>
        </div>
    </div>
    <!--.tab-pane-->
</div>
<div class="new_tenant_inline_section">
    <form id="new_tenants_form">
        <button id="plus_new_tenant_btn" class="btn btn-inline btn-danger-outline" type="button">
                                                    <span class="glyphicon glyphicon-plus"></span> <span class="btn_inline_text">Tenant</span>
                                                </button>
        <div class="new_tenant_fields_box" style="display:none;">
            <table class="table vpd_table">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Mobile</th>
                        <th>Landline</th>
                        <th>Email</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="form-group"><input placeholder="First Name" data-validation="[NOTEMPTY]" data-validation-label="First Name" type="text" class="form-control" name="new_tenant_fname"></div>
                        </td>
                        <td>
                            <div class="form-group"><input placeholder="Last Name" type="text" class="form-control" name="new_tenant_lname"></div>
                        </td>
                        <td>
                            <div class="form-group"><input  type="text" class="form-control tenant_mobile" name="new_tenant_mobile"></div>
                        </td>
                        <td>
                            <div class="form-group"><input type="text" class="form-control phone-with-code-area-mask-input" name="new_tenant_landline"></div>
                        </td>
                        <td>
                            <div class="form-group"><input placeholder="Email"  type="text" class="form-control" name="new_tenant_email"></div>
                        </td>
                        <td>
                            <div class="form-group"><button style="margin:0;" id="add_new_tenant_btn" type="button" class="btn btn-sm btn-inline">Add Tenant</button></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>

</div>

<script type="text/javascript">

$(document).ready(function(){


        //delete/deactivate tenant
    $(document).on('click','.deactivate_tenant',function(e){
            e.preventDefault();
            var obj = $(this);
            var tenant_id = $(this).data('tenant_id');
                    swal({
                        title: "",
                        text: "Remove Tenant?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes, Remove",
                        cancelButtonText: "No, Cancel!",
                        closeOnConfirm: false,
                        closeOnCancel: true,
                    },
                    function(isConfirm){
                        if(isConfirm){
                                jQuery.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('/properties/update_tenant/') ?>",
                                    dataType: 'json',
                                    data: {
                                        action: 'deactivate',
                                        prop_id: <?php echo $prop_id; ?>,
                                        tenant_id: tenant_id,
                                        is_details: 1,
                                    }
                                    }).done(function(data){
                                        if(data.status===true){
                                            if(data.error){
                                                swal('Error',data.error,'error');
                                            } else {
                                                swal({
                                                    title:"Success!",
                                                    text: "Tenant Removed",
                                                    type: "success",
                                                    showCancelButton: false,
                                                    confirmButtonText: "OK",
                                                    closeOnConfirm: false,

                                                },function(isConfirm){
                                                    swal.close();
                                                    obj.parents('.loader_wrapper_pos_rel').find('.loader_block_v2').show(); //show loader
                                                    obj.parents('.loader_wrapper_pos_rel').find('.tenants_ajax_box').load('/properties/tenants_ajax #tenants_ajax_container',{prop_id:<?php echo $prop_id ?>}, function(response, status, xhr){
                                                        $('.loader_wrapper_pos_rel').find('.loader_block_v2').hide(); //hide loader
                                                        $('[data-toggle="tooltip"]').tooltip(); //init tooltip
                                                        add_validate_tenant(); //init tenant validation
                                                        //phone/mobile mask
                                                        phone_mobile_mask();
                                                        mobile_validation();
                                                        phone_validation();
                                                    });
                                                });
                                            }
                                            
                                        }else{
                                            swal('Error','Tenant error: Please try again','error');
                                    }
                                });
                        }
                    })
    });

    // reactivate tenant
    $(document).on('click','.reactivate_tenant',function(e){
        e.preventDefault();
        var obj = $(this);
        var tenant_id = $(this).data('tenant_id');
                    swal({
                        title: "",
                        text: "Reactivate Tenant?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes, Reactivate",
                        cancelButtonClass: "btn-danger",
                        cancelButtonText: "No, Cancel!",
                        closeOnConfirm: false,
                        closeOnCancel: true,
                    },
                    function(isConfirm){
                        if(isConfirm){
                                jQuery.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('/properties/update_tenant/') ?>",
                                    dataType: 'json',
                                    data: {
                                        action: 'reactivate',
                                        prop_id: <?php echo $prop_id; ?>,
                                        tenant_id: tenant_id,
                                        is_details: 1,
                                    }
                                    }).done(function(data){
                                        if(data.status===true){
                                            if(data.error){
                                                swal('Error',data.error,'error');
                                            } else {
                                                swal({
                                                    title:"Success!",
                                                    text: "Tenant Reactivated",
                                                    type: "success",
                                                    showCancelButton: false,
                                                    confirmButtonText: "OK",
                                                    closeOnConfirm: false,

                                                },function(isConfirm){
                                                    swal.close();
                                                    obj.parents('.loader_wrapper_pos_rel').find('.loader_block_v2').show(); //show loader
                                                    obj.parents('.loader_wrapper_pos_rel').find('.tenants_ajax_box').load('/properties/tenants_ajax #tenants_ajax_container',{prop_id:<?php echo $prop_id ?>}, function(response, status, xhr){
                                                        $('.loader_wrapper_pos_rel').find('.loader_block_v2').hide(); //hide loader
                                                        $('[data-toggle="tooltip"]').tooltip(); //init tooltip
                                                        add_validate_tenant(); // init tenant validation
                                                        //phone/mobile mask
                                                        phone_mobile_mask();
                                                        mobile_validation();
                                                        phone_validation();
                                                    });
                                                });
                                            }

                                            
                                        }else{
                                            swal('Error','Tenant error: Please try again','error');
                                    }
                                });
                        }
                    })
    });
    

    // toogle new tenant div/fields
    $(document).on('click','#plus_new_tenant_btn',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        var obj = $(this);
        var btnName = obj.find('.btn_inline_text');
        var btnIcon = obj.find('.glyphicon');
        $('.new_tenant_fields_box').slideToggle(function(){
                if(btnName.html()=="Tenant"){
                    btnName.html("Cancel");
                    btnIcon.removeClass('glyphicon-plus').addClass('glyphicon-minus');
                }else{
                    btnName.html("Tenant");
                    btnIcon.removeClass('glyphicon-minus').addClass('glyphicon-plus');
                }
        });
    });


    //edit tenant toggle
    $(document).on('click','.edit_tenant',function(e){
        e.preventDefault();
        var obj = $(this);
        
        $('.tenant_row').show();
        $('.edit_tenant_field_box').hide();
        obj.parents('.tenant_row').hide();
        obj.parents('.tenant_row:first').next('.edit_tenant_field_box').show();
    });
    
    //cancel tenant toggle
    $(document).on('click','.cancel_tenant',function(e){
        e.preventDefault();
        obj = $(this);
        obj.parents('.edit_tenant_field_box').hide();
        obj.parents('.edit_tenant_field_box:first').prev('.tenant_row').show();
    });
    
    //update tenant details/info
    $(document).on('click','.update_tenant',function(e){
        e.preventDefault();
        obj = $(this);
        var tenant_id = $(this).data('tenant_id');
        var tenant_fname = obj.parents('.edit_tenant_field_box').find('input[name="edit_tenant_fname"]').val();
        var tenant_lname = obj.parents('.edit_tenant_field_box').find('input[name="edit_tenant_lname"]').val();
        var tenant_mobile = obj.parents('.edit_tenant_field_box').find('input[name="edit_tenant_mobile"]').val();
        var tenant_landline = obj.parents('.edit_tenant_field_box').find('input[name="edit_tenant_landline"]').val();
        var tenant_email = obj.parents('.edit_tenant_field_box').find('input[name="edit_tenant_email"]').val();
                    swal({
                        title: "",
                        text: "Update Tenant?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes, Update",
                        cancelButtonClass: "btn-danger",
                        cancelButtonText: "No, Cancel!",
                        closeOnConfirm: false,
                        closeOnCancel: true,
                    }, 
                    function(isConfirm_t){
                        if(isConfirm_t){
                            jQuery.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('/properties/update_tenant_details/') ?>",
                                    dataType: 'json',
                                    data: {
                                        prop_id: <?php echo $prop_id; ?>,
                                        tenant_id: tenant_id,
                                        tenant_fname: tenant_fname,
                                        tenant_lname: tenant_lname,
                                        tenant_mobile: tenant_mobile,
                                        tenant_landline: tenant_landline,
                                        tenant_email: tenant_email,
                                        is_details: 1,
                                    }
                                    }).done(function(data){
                                        if(data.status===true){
                                            if(data.error){
                                                swal('Error',data.error,'error');
                                            } else {
                                                swal({
                                                    title:"Success!",
                                                    text: "Tenant Updated",
                                                    type: "success",
                                                    showCancelButton: false,
                                                    confirmButtonText: "OK",
                                                    closeOnConfirm: false,

                                                },function(isConfirm){
                                                    swal.close();
                                                    obj.parents('.loader_wrapper_pos_rel').find('.loader_block_v2').show(); //show loader
                                                    obj.parents('.loader_wrapper_pos_rel').find('.tenants_ajax_box').load('/properties/tenants_ajax #tenants_ajax_container',{prop_id:<?php echo $prop_id ?>}, function(response, status, xhr){
                                                        $('.loader_wrapper_pos_rel').find('.loader_block_v2').hide(); //hide loader
                                                        $('[data-toggle="tooltip"]').tooltip(); //init tooltip
                                                        add_validate_tenant(); // init tenant validation
                                                        phone_mobile_mask(); //init ph/mobile mask
                                                        mobile_validation(); //init mobile validation
                                                        phone_validation(); //init phone validation
                                                    });
                                                });
                                            }
                                            
                                        }else{
                                           location.reload();
                                    }
                                });
                        }
                    }
                    );
    });


}); //document ready end


function add_validate_tenant(){
     // insert and new tenant (inline)
     $('#new_tenants_form').validate({
                submit:{
                        settings: {
                            inputContainer: '.form-group',
                            errorListClass: 'form-tooltip-error',
                            button: '#add_new_tenant_btn'
                        },
                        callback: {
                            onBeforeSubmit: function(node){
                                node.parents('.loader_wrapper_pos_rel').find('.loader_block_v2').show();
                            },
                            onSubmit: function(node,formData){       
                                $.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('/properties/add_tenant/') ?>",
                                    dataType: 'json',
                                    data: {
                                        prop_id: <?php echo $prop_id; ?>,
                                        tenant_fname: node[0][1].value,
                                        tenant_lname: node[0][2].value,
                                        tenant_mobile: node[0][3].value,
                                        tenant_landline: node[0][4].value,
                                        tenant_email: node[0][5].value,
                                        is_details: 1,
                                    }
                                 }).done(function(ret){
                                     if(ret.status===true){
                                        if(ret.error){
                                            $('.loader_wrapper_pos_rel').find('.loader_block_v2').hide();
                                            swal('Error',ret.error,'error');
                                        } else {
                                            node.parents('.loader_wrapper_pos_rel').find('.loader_block_v2').hide();
                                            swal({
                                                    title:"Success!",
                                                    text: "Tenant Added",
                                                    type: "success",
                                                    showCancelButton: false,
                                                    confirmButtonText: "OK",
                                                    closeOnConfirm: false,

                                                },function(isConfirm){
                                                    swal.close();
                                                    node.parents('.loader_wrapper_pos_rel').find('.loader_block_v2').show();
                                                    node.parents('.loader_wrapper_pos_rel').find('.tenants_ajax_box').load('/properties/tenants_ajax #tenants_ajax_container',{prop_id:<?php echo $prop_id ?>}, function(response, status, xhr){
                                                        $('.loader_wrapper_pos_rel').find('.loader_block_v2').hide();
                                                        $('[data-toggle="tooltip"]').tooltip(); //init tooltip
                                                        add_validate_tenant();
                                                        //phone/mobile mask
                                                        phone_mobile_mask();
                                                        mobile_validation();
                                                        phone_validation();
                                                    });
                                                });
                                        }
                                     }
                                 });
                            }
                        }
                }


        })
}

</script>