<?php

    $spec_agency = array(1328); //client don't want to see key number
    $property_id = $this->uri->segment(3);
	$hashIds = new HashEncryption($this->config->item('hash_salt'), 6);
?>
<style>
.property_code_col{
    width: 10% !important;
}
.change_service_div,
#add_new_service_type_div,
#non_active_service_div{
	display: none;
}
.api_icon{
    width:100px;
    cursor: pointer;
}
.change_or_add_content_div,
#disable_service_can_nlm_div,
#disable_service_cannot_nlm_div,
.process_buttons,
#service_to_sats_q1_div,
#service_to_sats_q2_div,
#service_to_sats_q3_div,
#qld_regular_service_div,
#qld_regular_service_type,
#qld_IC_service_type,
#service_types_,
#service_types_div,
.other_reason_elem,
.other_reason2{
    display: none;
}

#permission_error{
    clear: both;
    margin: 0 auto;
    padding: 15px 60px;
    text-align: center;
    background-color: #b4151b;
    color: #ffffff;
    font-weight: bold;
}
.mailto_link{
    position: unset !important; 
}
</style>
<div class="box-typical box-typical-padding">
    <?php if($is_nlm == 1): ?>
        <div id='permission_error'>This property has been marked as No Longer Managed!</div>        
    <?php endif;?>

    <div class="vpd_box">

            <!-- ADDRESS -->

            <table id="vpd_address_table" class="table vpd_table table-sm">
                <thead>
                    <tr>
                        <td class="f_heading">
                            <header class="box-typical-header">                
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                        <h3><span class="glyphicon glyphicon-map-marker"></span> PROPERTY ADDRESS</h3>
                                    </div>
                                </div>
                             </header>
                        </td>
                        <td class="f_heading" colspan="4">
                            <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                        <h3><span class="glyphicon glyphicon-info-sign"></span> ADDITIONAL INFORMATION</h3>
                                    </div>
                                </div>
                            </header>
                        </td>
                        <?php //if( $user->alt_agencies!="" ){ ##show only if has alt agencies ?>

                        <?php if( ( !in_array($this->session->agency_id,$this->config->item('harris_agencies')) && $user->alt_agencies!="" ) || ( in_array($this->session->agency_id,$this->config->item('harris_agencies')) && $user->user_type==1 )  ){ //dont show if Harries agency not admin ?>
                        <td class="f_heading">
                            <button style="margin-left:10px;" data-fancybox="" data-src="#move_prop_fancybox" type="button" class="btn btn-success">Move Property</button>
                            <div style="display:none;width:500px;" id="move_prop_fancybox">
                                <h3>Move Property</h3>
                                <div class="form-group">
                                    <label>New Agency</label>
                                    <select class="form-control" id="sel_new_agency">
                                    </select>
                                </div>
                                <div style="display:none;" id="move_prop_manager_div">
                                    <div class="form-group">
                                        <label>Property Manager</label>
                                        <select class="form-control" id="sel_new_pm">
                                        
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" id="old_agecy_id" value="<?php echo $this->session->agency_id ?>">
                                        <button type="button" class="btn btn_move_property">Move Property to <span class="new_agency_name"></span></button>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <?php } ?>
                        <?php //} ?>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <?php
                        // only on Hume Housing agency(1598) AU
                        if( $this->system_model->is_hume_housing_agency() == true ){ ?>
                             <th class="property_code_col">Property Code</th>
                        <?php
                        }
                        ?>                       
                        <th>Short Term Rental</th>
                        <th> Key Number</th>
                        <th> Lockbox Code</th>
                        <th> Property Manager</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                           <div class="form-group">

                               <?php 
                                echo $p_address = "{$prop_det->p_address_1}  {$prop_det->p_address_2} {$prop_det->p_address_3} {$prop_det->p_state} {$prop_det->p_postcode}";                                 
                              
                                if( $property_id > 0 ){

                                    // get API property data dynamically depends on the API integrated
                                    $api_prop_data = $this->api_model->get_property_data($property_id);   
                                   
                                    if( $api_prop_data['api_prop_id'] != '' ){ // if API property connected found
                                    ?>
                                        <img src="/images/api/<?php echo $api_prop_data['agency_api_icon']; ?>" data-toggle="tooltip" title="Connected to: <?php echo  $api_prop_data['api_prop_address']; ?>" class="api_icon" />
                                    <?php    
                                    }

                                }                                                  
                               ?>                                
                           </div>
                        </td> 

                        <?php
                        // only on Hume Housing agency(1598) AU
                        if( $this->system_model->is_hume_housing_agency() == true ){ ?>
                        <td>
                            <div class="form-group">
                                <div class="form-row align-items-center">
                                    <input type ="text" id="compass_index_num" class="form-control float-left w-75 mr-3" value="<?php echo $prop_det->compass_index_num ?>" />
                                    <span class="font-icon font-icon-ok compass_index_num_check align-middle" style="display: none; color: #46c35f"></span>                      
                                </div>
                            </div>                            
                        </td>
                        <?php
                        }
                        ?>                                         
                                  
                        <td>  <div class="form-group">                            
                                <!-- ben said to remove "Please select" for nulls -->
                                <select id="holiday_rental" name="holiday_rental" class="form-control">  
                                    <option value="0" <?php echo ( $prop_det->holiday_rental == 0 && is_numeric($prop_det->holiday_rental) )?'selected':null; ?>>No</option>                                  
                                    <option value="1" <?php echo ( $prop_det->holiday_rental == 1 )?'selected':null; ?>>Yes</option>                                    
                                </select>
                                <span class="font-icon font-icon-ok check-input-ok holiday_rental_check" style="display: none;"></span>
                            </div>
                        </td>
                        <td style="width: 150px;">
                            <div class="form-group">
                                <div class="form-row align-items-center">
                                    <div class="col-xs-7">
                                        <input class="form-control" type ="<?php echo (in_array($this->session->agency_id, $spec_agency))?'password':'text' ?>" value="<?php echo $prop_det->key_number ?>" id="key_number_input">
                                    </div>
                                    <div class="col-xs-3 text-left">
                                        <span class="font-icon font-icon-ok key_number_check align-middle" style="display: none; color: #46c35f"></span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <?php
                        // get lockbox data
                        $lockbox_sql_str = "
                        SELECT `code`
                        FROM `property_lockbox`
                        WHERE `property_id` = {$property_id}
                        ";
                        $lockbox_sql = $this->db->query($lockbox_sql_str);
                        $lockbox_sql_row = $lockbox_sql->row();
                        ?>
                        <td style="width: 150px;">
                            <div class="form-group">
                                <div class="form-row align-items-center">
                                    <div class="col-xs-12">
                                        <input class="form-control" type ="text" id="lockbox_code" value="<?php echo $lockbox_sql_row->code; ?>" />
                                    </div>   
                                    <div class="col-xs-3 text-left">
                                        <span class="font-icon font-icon-ok lockbox_code_check align-middle" style="display: none; color: #46c35f"></span>
                                    </div>                              
                                </div>
                            </div>
                        </td>

                        <td>

                        <div class="form-group">
                        <div style="position:relative;padding-right:10px;">
                        
                            <select id="prop_pm" name="prop_pm" class="form-control field_g2 select2-photo">

                                <option value="">Please select</option>
                                <option  <?php echo ($prop_det->pm_id_new=='0')?'selected="selected"':'' ?> value="0" data-photo='/images/avatar-2-64.png'>No PM assigned</option>
                                <?php
                                foreach($property_manager_list as $row){?>
                                    <option  <?php echo ( $row->agency_user_account_id == $prop_det->pm_id_new )?'selected':'' ?> data-photo="<?php echo ($row->photo!="")?$user_photo_path.$row->photo:'/images/avatar-2-64.png' ?>" value="<?php echo $row->agency_user_account_id; ?>"><?php echo $row->fname." ".$row->lname ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <span class="font-icon font-icon-ok check-input-ok pm_check" style="display: none;"></span>
                        </div>
                    </div>

                        </td>
                          <td>

                            <?php
                            if($job_status === TRUE){
                                echo "This property has an active job and <br />cannot be marked 'No Longer Managed'";
                            }else{ ?>
                                <div class="form-group"><button data-val="0" id="btn_no_longer_managed" style="margin: 0;" type="button" class="btn btn-danger btn-inline">No Longer Manage?</button></div>
                                <?php
                            } ?>

                          </td>
                    </tr>
                    <tr>
                        <td id="nlm_td_box" colspan="8" style="padding: 0px;height:0px!important;">

                         <div id="nlm_td_box_toggle" style="display: none;">
                             <table class="no-border" id="nlm_box" style="width:100%">
                            <tr>
                              <td style="padding-top:15px;padding-bottom: 15px;">
                                  <table class="no-border nlm_box_warning" style="width:100%">
                          <tr>
                            <td style="padding:0px">
<div class="txt-center alert alert-danger alert-close alert-dismissible fade show ">
    <i class="font-icon font-icon-warning red"></i>&nbsp;&nbsp;Warning - If you proceed, All Jobs and Services will be Cancelled, and this property will be Archived </div>
                          </td>
                        </tr>
                        </table>

                                  <?php echo form_open(base_url('/properties/no_longer_managed'),'id=nlm_form'); ?>
                            <table class="nlm_box_fields"  style="width:100%">
                            <thead>
                                 <tr>

                        <th>"No Longer Managed" From</th>
                        <th>"No Longer Managed" Reason</th>
                        <th class="other_reason_elem">Other Reason</th>
                        <th>&nbsp;</th>
                    </tr>
                                </thead>
                                <tbody>
                                <tr>

                                    <td>
                                    <div class="form-group">
                                        <div class="input-group date flatpickr" data-wrap="true" >
                                            <input data-validation-label="No Longer Managed From" data-input  type="text" class="form-control" name="nlm_from" id="nlm_from">
                                            <span class="input-group-append" data-toggle >
                                                    <span class="input-group-text"><i class="font-icon font-icon-calend"></i></span>
                                            </span>
                                        </div>
                                    </div>
                                    </td>
                                    <td><div class="form-group">
                                        <select id="reason_they_left" name="reason_they_left" class="form-control">
                                            <option value="">---Select Reason---</option>       
                                            <?php
                                            // get leaving reason                                                
                                            $lr_sql = $this->db->query("
                                            SELECT *
                                            FROM `leaving_reason`
                                            WHERE `active` = 1
                                            AND `display_on` IN(3,5)
                                            ORDER BY `reason` ASC
                                            ");   
                                            foreach( $lr_sql->result() as $lr_row ){ ?>
                                                <option value="<?php echo $lr_row->id; ?>"><?php echo $lr_row->reason; ?></option> 
                                            <?php
                                            }                                         
                                            ?> 
                                            <option value="-1">Other</option> 
                                        </select>
                                    </div></td>
                                    <td class="other_reason_elem">
                                        <div class="form-group">                                        
                                            <textarea class="form-control addtextarea" id="other_reason" name="other_reason" placeholder="Other Reason"></textarea>
                                        </div>
                                    </td>
                                    <!-- <td><div class="form-group"><input data-validation="[NOTEMPTY]" data-validation-label="NO Longer Managed Reason" class="form-control requiredV2" type="text" id="nlm_reason" name="nlm_reason"></div></td> -->
                                    <td><div class="form-group"><button type="button" style="margin: 0;" id="btn_no_longer_managed_go" class="btn btn-sm btn-inline">Proceed</button></div></td>
                                    </tr>
                                </tbody>
                            </table>
                                <?php echo form_close(); ?>
                                </td>
                              </tr>
                            </table>

                            </div>


                        </td>
                    </tr>
                </tbody>

            </table>

            <!-- VACANCY -->
            <header class="box-typical-header" style="margin-top: 15px;">
                <div class="tbl-row">
                    <div class="tbl-cell tbl-cell-title">
                        <h3><span class="glyphicon glyphicon-map-marker"></span> VACANCY</h3>
                    </div>
                </div>
            </header>

            <div class="row mb-3 currently_vacant_div">
                <div class="col-lg-12">

                    <button class="btn btn-inline currently_vacant_toggle" type="button">
                        <span class="btn_inline_text">Currently Vacant</span>
                    </button>

                    <div class="vacant_div d-none">

                        <div class="d-inline-block">
                            <input type="text" class="form-control vacant_from_date flatpickr" name="vacant_from_date" placeholder="Vacant From" />
                        </div>

                        <div class="d-inline-block">
                            <input type="text" class="form-control vacant_to_date flatpickr" name="vacant_to_date" placeholder="Vacant To" />
                        </div>

                        <div class="d-inline-block">
                            <div class="checkbox sd_checkbox">								
                                <input type="checkbox" class="clear_tenants_chk" name="clear_tenants_chk" id="clear_tenants_chk_<?php echo $row->property_id ?>" value="1" /> 
                                <label for="clear_tenants_chk_<?php echo $row->property_id ?>">Clear Old Tenants</label>
                            </div>            
                        </div>

                        <input type="hidden" class="property_vacant" name="property_vacant" />

                        <button class="btn btn-inline currently_vacant_submit ml-3" type="button">
                            <span class="btn_inline_text">Submit</span>
                        </button>

                    </div>

                </div>
            </div>	

            <!------- TENANTS -------->
            <header class="box-typical-header" style="margin-top: 15px;">
                <div class="tbl-row">
                    <div class="tbl-cell tbl-cell-title">
                        <h3><span class="font-icon font-icon-users"></span> TENANT DETAILS</h3>
                    </div>
                </div>
            </header>

      <!-- TENANT SECTION START HERE -->
	<section class="tabs-section loader_wrapper_pos_rel tenant_section" style="margin-bottom: 20px;">

                    <div class="loader_block_v2" style="display: none;"> <div id="div_loader"></div></div>

<div class="tenants_ajax_box"></div>



      </section>
      <!-- LANDLORD SECTION START HERE -->

            <!---- Landlord ---->
            <header class="box-typical-header" style="margin-top: 15px;">
                <div class="tbl-row">
                    <div class="tbl-cell tbl-cell-title">
                        <h3><span class="font-icon font-icon-user"></span> LANDLORD DETAILS</h3>
                    </div>
                </div>
            </header>
            <?php echo form_open(base_url('/properties/update_landlord'),'id=update_landlord_form'); ?>
            <table class="table vpd_table table-sm">

                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <!--
                        <th>Mobile</th>
                        <th>Landline</th>
                        <th>Email</th>
                        -->
                        <th>
                        <?php
                        if(!empty($prop_det->landlord_firstname) || !empty($prop_det->landlord_lastname)){
                            echo "Edit";
                        }else{
                            echo "Add";
                        }
                        ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="editable_box">

                        <td>

                        <!-- Message if no firstname or lastname --->
                        <?php if(empty($prop_det->landlord_firstname) && empty($prop_det->landlord_lastname)){
                            echo '<div class="form-group editable_text"><span class="font-icon font-icon-warning red"></span> No Landlord on file</div>';
                        } ?>

                        <div class="editable_text"> <?php echo $prop_det->landlord_firstname; ?> </div>
                        <div class="form-group editable_input hiddenv3"><input placeholder="First Name"  name="landlord_firstname" id="landlord_firstname" type="text" class="form-control" value="<?php echo $prop_det->landlord_firstname ?>"></div></td>

                        <td>
                        <div class="editable_text">  <?php echo $prop_det->landlord_lastname ?> </div>
                        <div class="form-group editable_input hiddenv3"><input placeholder="Last Name"  name="landlord_lastname" id="landlord_lastname" type="text" class="form-control" value="<?php echo $prop_det->landlord_lastname ?>"></div></td>

                        <!--
                        <td>
                        <div class="editable_text">  <?php echo $prop_det->landlord_mob ?> </div>
                        <div class="form-group editable_input hiddenv3"><input  name="landlord_mob" id="landlord_mob" type="text" class="form-control tenant_mobile" value="<?php echo $prop_det->landlord_mob ?>"></div></td>

                        <td>
                        <div class="editable_text">  <?php echo $prop_det->landlord_ph ?> </div>
                        <div class="form-group editable_input hiddenv3"><input  name="landlord_ph" id="landlord_ph" type="text" class="form-control phone-with-code-area-mask-input" value="<?php echo $prop_det->landlord_ph ?>"></div></td>

                        <td>
                        <div class="editable_text"> <?php echo $prop_det->landlord_email ?></div>
                        <div class="form-group editable_input hiddenv3">
                        <input placeholder="Email" name="landlord_email" id="landlord_email" type="text" class="form-control" value="<?php echo $prop_det->landlord_email ?>"></div></td>
                        -->

                        <td>
                        <div class="form-group editable_text">

                       <?php if(!empty($prop_det->landlord_firstname) || !empty($prop_det->landlord_lastname)){ ?>

                             <a class="btn_update_landlord_toggle" data-tenant_id="23" href="#" data-toggle="tooltip" title="" data-original-title="Edit"><span class="font-icon font-icon-pencil"></span></a>

                       <?php }else{ ?>

                            <button class="btn btn-sm btn-danger-outline btn_update_landlord_toggle" type="button">
                                                    <span class="glyphicon glyphicon-plus"></span> <span class="btn_inline_text">Landlord</span>
                            </button>

                       <?php } ?>
                       </div>

                       <div class="form-group editable_input hiddenv3" style="width:128px;">
                            <button id="btn_update_landlord" style="margin: 0;" type="button" class="btn btn-sm">Submit</button>
                            <button id="btn_cancel_landlord" type="button" class="btn btn-sm btn-danger">Cancel</button>
                       </div>
                        </td>

                    </tr>
                </tbody>

            </table>
</form>


         <!---- SERVICES ---->
        <section class="services_section">
                <header class="box-typical-header" style="margin-top: 15px;">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3><span class="font-icon font-icon-cogwheel"></span> SERVICES</h3>
                        </div>
                    </div>
                </header>
                <div class="tabs-section-nav tabs-section-nav-icons">
                    <div class="tbl">
                        <ul class="nav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#tabs-1-tab-1v2" role="tab" data-toggle="tab">
                                    <span class="nav-link-in">
                                        <i class="font-icon font-icon-cogwheel"></i>
                                        Services
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item inactive_tenants_menu">
                                <a class="nav-link" href="#tabs-1-tab-2v2" role="tab" data-toggle="tab">
                                    <span class="nav-link-in">
                                        <i class="font-icon font-icon-build"></i>
                                        Property Details
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                        <!-- tab 1/current tenant --->
                        <div role="tabpanel" class="tab-pane fade in active show" id="tabs-1-tab-1v2">
                            <div class="table-responsivesss">
                                     <?php echo form_open(base_url('properties/vpd_update_services'), 'id=vpd_update_services'); ?>
             <table class="table vpd_table table-sm tbl_service">
                <tbody>
                    <tr>
                      <td>
                     <p>&nbsp;</p>
                    <?php
                    if(!empty($agency_services)){

                        $index = 0;
                        $has_service_to_sats = false;
                        foreach($agency_services as $row) {
                            $is_bundle = ($row['is_bundle'])?$row['is_bundle']:false;
                            $bundle_ids = $row['bundle_ids'];

                            $newPrice = $row['new_price'];
                            $psi_val = $row['psi_val'];

                            //IC tweak > show IC if service = 1
                            if($row['service_id']==12 || $row['service_id']==13 || $row['service_id']==14){

                                if($psi_val!=1){
                                    $ic_aw_class = 'hidden';
                                }else{
                                    $ic_aw_class = NULL;
                                }

                            }else{
                                $ic_aw_class = NULL;
                            }

                        if($psi_val==1){

                            $has_service_to_sats = true;

                        ?>

                        <div class="row options_wrapper services_tr <?php echo ($is_bundle==1)?'bundle_tr':'non_bundle_tr';?> <?php echo $ic_aw_class; ?> mt-3">

                            <div style="display:none;">

                                <input type="hidden" class="services_id" value="<?php echo ($is_bundle==1)?$bundle_ids:$row['job_type_id']; ?>" />
                                <input type="hidden" value="<?php echo $row['service_id']; ?>" name="alarm_job_type_id[]" class="ajt_id">
                                <input type="hidden" value="<?php echo $newPrice; ?>" name="price[]">
                                <input type="hidden" value="<?php echo $is_bundle; ?>" class="isbundle" name="isbundle[]">
                                <input type="hidden" value="<?php echo $bundle_ids; ?>" class="bundle_ids" name="bundle_ids[]">
                                <input type="hidden" value="<?php echo $row['type']?>" name="services_name[]" >
                                <input type="hidden" value="<?php echo $psi_val?>" name="psi_val[]" class="input_psi_val" >
                                <input type="hidden" value="<?php echo $row['excluded_bundle_ids']?>" name="excluded_bundle_ids" class="excluded_bundle_ids" >

                                <?php

                                    if($psi_val==1){ ?>

                                    <?php if($row['service_id']==6){ ?>
                                        <input type='hidden' class='hidden_servicesVal_corded' value="<?php echo $row['service_id']?>">
                                        <?php }else{ ?>
                                            <input type='hidden' class='hidden_servicesVal' value="<?php echo $row['service_id']?>">
                                    <?php } ?>

                                <?php  }else{ ?>
                                    <input type='hidden' class='no_selected_serv' value="<?php echo $row['service_id']?>">
                                <?php }
                                ?>

                            </div>
                            
                            <div class="col-md-4">
                                <label class="d-inline-block mr-2">
                                    <?php
                                // get price increase excluded agency
                                $piea_sql = $this->db->query("
                                SELECT *
                                FROM `price_increase_excluded_agency`
                                WHERE `agency_id` = {$this->session->agency_id}                  
                                AND (
                                    `exclude_until` >= '".date('Y-m-d')."' OR
                                    `exclude_until` IS NULL
                                )
                                ");  

                                $price_var_params = array(
                                    'service_type' => $row['service_id'],                                    
                                    'property_id' => $this->uri->segment(3),
                                    'new_line' => 1,
                                    'display_reason' => 1
                                );
                                $price_var_arr = $this->system_model->get_property_price_variation($price_var_params);
                                $dynamic_price = ( $piea_sql->num_rows() > 0 )?$row['price']:$price_var_arr['dynamic_price_total'];

                                if($row['job_type_id']==14){
                                    echo "Bundle Smoke Alarm, Corded Window, Safety Switch (Interconnected) $".number_format($dynamic_price,2);
                                }else if($row['job_type_id']==13){
                                    echo "Smoke Alarm & Safety Switch (Interconnected) $".number_format($dynamic_price,2);
                                }else if($row['job_type_id']==12){
                                    echo "Smoke Alarms (Interconnected) $".number_format($dynamic_price,2);
                                }else if($row['job_type_id']==9){
                                    echo "Bundle Smoke Alarm, Corded Window, Safety Switch $".number_format($dynamic_price,2);
                                }else{
                                    echo $row['full_name']." $".number_format($dynamic_price,2);
                                }
                                ?></label>
                                <div class="price_break_down"><?php echo $price_var_arr['price_breakdown_text']; ?></div>
	                            <?= Alarm_job_type_model::icons($row['service_id']); ?>
                            </div>

                            <div class="col-md-4">
                                <button type="button" class="btn change_or_add_new_service_fb_btn" data-from_service_type="<?php echo $row['service_id']; ?>">Change or Add New Service</button>
                            </div>

                            <div class="col-md-4"></div>
                        </div>

                        <?php $index++;

                        }

                        }
                    }else{
                        echo "Error: No Services found!";
                    }

                    echo ( $has_service_to_sats == false )?'There is currently no active service for this property.':null;

                        ?>
                      </td>

                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <!--<tr>
                        <td>If you’d like to change the service on this property, please first mark your current service as 'No Response'.</td>
                    </tr>-->
                </tbody>

            </table>

                                <div class="form-group">

                                    <input type="hidden" name="hid_btn_update_services" value="1" />
                                    <input type="hidden" id="default_no_services" name="default_no_services" value="<?php echo (($checkCurrentPropertyServices===false)?'Yes':'No') ?>">
                                    <input type="hidden" name="serv_prop_id" value="<?php echo $prop_id; ?>">
                                    <input type="hidden" name="property_address" value="<?php echo $prop_det->p_address_1." ".$prop_det->p_address_2." ".$prop_det->p_address_3 ?>" >

                                    <div class="row">

                                    <!--
                                        <div class="col">
                                            <input id="vpd_update_services_btn" name="vpd_update_services_btn" style="margin-top: 20px;" type="submit" class="btn btn-inline" value="Update Services">
                                        </div>
                                         
                                        <div class="col">
                                            
                                           
                                            <button type="button" id='show_non_active_services' class="btn btn-inline" style="margin-top: 20px;">Show Non-Active Services</button>
                                            <div id="non_active_service_div">
                                                <table id="non_active_service_tbl" class="table">
                                                    <tr>
                                                        <th>Service</th>
                                                        <th>Status</th>
                                                    </tr>

                                                    <?php
                                                    $ps_sql = $this->db->query("
                                                    SELECT 
                                                        ps.`property_services_id`,
                                                        ps.`price`,
                                                        ps.`service` AS serv_status,

                                                        ajt.`id` AS ajt_id,
                                                        ajt.`type` AS ajt_type
                                                    FROM `property_services` AS ps
                                                    LEFT JOIN `alarm_job_type` AS ajt ON ps.`alarm_job_type_id` = ajt.`id`
                                                    WHERE ps.`property_id` = {$this->uri->segment(3)}
                                                    AND ps.`service` != 1
                                                    ");
                                                    foreach( $ps_sql->result() as $ps_row ){ ?>

                                                        <tr>
                                                            <td><?php echo "{$ps_row->ajt_type} - \${$ps_row->price}"; ?></td>
                                                            <td>
                                                                <input type="hidden" class="non_active_ps_id" value="<?php echo $ps_row->property_services_id; ?>" /> 													
                                                                <input type="radio"  class="non_active_service_status mr-1 non_active_service_<?php echo $ps_row->property_services_id; ?>" name="non_active_service_<?php echo $ps_row->property_services_id; ?>" value="0" <?php echo ( is_numeric($ps_row->serv_status) && $ps_row->serv_status == 0 )?'checked':null; ?> /><span class="mr-2">DIY</span> 													
                                                                <input type="radio"  class="non_active_service_status mr-1 non_active_service_<?php echo $ps_row->property_services_id; ?>" name="non_active_service_<?php echo $ps_row->property_services_id; ?>" value="3" <?php echo ( $ps_row->serv_status == 3 )?'checked':null; ?> /><span class="mr-2">Other Provider</span>
                                                            </td>
                                                        </tr>

                                                    <?php
                                                    }
                                                    ?>									
                                                </table>

                                                <button type='button' class='btn' id='non_active_service_update_btn'>Update</button>
                                            </div>
                                            
                                        </div>
                                        -->

                                        <!-- Add New Service -->
                                        <?php
                                        if( $has_service_to_sats == false ){ ?>

                                            <div class="col-6">

                                            <button type="button" id="add_new_service_btn" class="btn btn-inline" style="margin-top: 20px;" <?php echo isset($is_nlm) && $is_nlm == 1 ? 'disabled'  : ''?> >Add New Service</button>
                                            <div id="add_new_service_type_div">                                                	


                                                <?php
                                                // QLD only
                                                if( $prop_det->p_state == 'QLD' ){ ?>

                                                    <div id="service_to_sats_q1_div">
                                                        <p>Has this property been upgraded to Interconnected Smoke Alarms?</p>
                                                        <p>                                                    												
                                                            <input type="radio" id="service_to_sats_q1_dp_yes" class="service_to_sats_q1_dp" name="service_to_sats_q1_dp" value="1" /><span class="ml-1 mr-2">Yes</span> 													
                                                            <input type="radio" id="service_to_sats_q1_dp_no" class="service_to_sats_q1_dp" name="service_to_sats_q1_dp" value="0" /><span class="ml-1 mr-2">No</span>
                                                        </p>
                                                    </div>


                                                    <div id="service_to_sats_q2_div">
                                                        <p>Do you give permission for <?=$this->config->item('COMPANY_NAME_SHORT')?> to upgrade this property on attendance?</p>
                                                        <p>                                                    												
                                                            <input type="radio" id="service_to_sats_q2_dp_yes" class="upgrage_to_ic" name="service_to_sats_q2_dp" value="1" /><span class="ml-1 mr-2">Yes</span> 													
                                                            <input type="radio" id="service_to_sats_q2_dp_no" class="upgrage_to_ic"  name="service_to_sats_q2_dp" value="0" /><span class="ml-1 mr-2">No</span>
                                                        </p>
                                                    </div>                                                         


                                                    <div id="service_types_div">
                                                        <p id="service_types_p">Select Service Type:</p>
                                                        <div>
                                                            <select id="qld_regular_service_type" class="form-control my-2 qld_sel_serv_type" style="width:auto;">
                                                                <option value="">---</option>
                                                                <?php
                                                                $agency_id = $this->session->agency_id;

                                                                // regular service types
                                                                $agen_serv_sql = $this->db->query("
                                                                SELECT 
                                                                    agen_serv.`price`,

                                                                    ajt.`id` AS ajt_id,
                                                                    ajt.`type` AS ajt_type
                                                                FROM `agency_services` AS agen_serv
                                                                LEFT JOIN `alarm_job_type` AS ajt ON agen_serv.`service_id` = ajt.`id`
                                                                WHERE agen_serv.`agency_id` = {$this->session->agency_id}	
                                                                AND agen_serv.`service_id` NOT IN(
                                                                    SELECT `alarm_job_type_id`
                                                                    FROM `property_services` 
                                                                    WHERE `property_id` = {$this->uri->segment(3)}
                                                                    AND `service` = 1
                                                                )	
                                                                ORDER BY ajt.`type` ASC					
                                                                ");
                                                                
                                                                foreach( $agen_serv_sql->result() as $agen_serv_row ){ ?>													
                                                                    <!-- <option value="<?php //echo $agen_serv_row->ajt_id; ?>"><?php //echo "{$agen_serv_row->ajt_type} -{$agen_serv_row->price}"; ?></option> -->
                                                                    
                                                                    <option value="<?php echo $agen_serv_row->ajt_id; ?>">
                                                                    <?php 
                                                                    
                                                                    echo "{$agen_serv_row->ajt_type} - ";
                                                                    $price_var_params = array(
                                                                        'service_type' =>$agen_serv_row->ajt_id,
                                                                        'agency_id'  => $agency_id
                                                                    );
                                                                    $price_var_arr = $this->system_model->get_agency_price_variation($price_var_params);
                                                                    echo $price_var_arr['price_breakdown_text'];
                                                                    
                                                                    ?></option>										
                                                                <?php
                                                                }
                                                                

                                                                //foreach( $agen_serv_sql->result() as $agen_serv_row ){ ?>													
                                                                    <!--<option value="<?php //echo $agen_serv_row->ajt_id; ?>"><?php //echo "{$agen_serv_row->ajt_type} - \${$agen_serv_row->price}"; ?></option>	-->												
                                                                <?php
                                                                //}
                                                                ?>

                                                                
                                                                
                                                            </select>
                                                        </div>

                                                        <div>
                                                            <select id="qld_IC_service_type" class="form-control my-2 qld_sel_serv_type" style="width:auto;">
                                                                <option value="">---</option>
                                                                <?php
                                                                $agency_id = $this->session->agency_id;

                                                                // IC service types
                                                                $agen_serv_sql = $this->db->query("
                                                                SELECT 
                                                                    agen_serv.`price`,

                                                                    ajt.`id` AS ajt_id,
                                                                    ajt.`type` AS ajt_type
                                                                FROM `agency_services` AS agen_serv
                                                                LEFT JOIN `alarm_job_type` AS ajt ON agen_serv.`service_id` = ajt.`id`
                                                                WHERE agen_serv.`agency_id` = {$this->session->agency_id}	
                                                                AND ajt.`is_ic` = 1
                                                                AND agen_serv.`service_id` NOT IN(
                                                                    SELECT `alarm_job_type_id`
                                                                    FROM `property_services` 
                                                                    WHERE `property_id` = {$this->uri->segment(3)}
                                                                    AND `service` = 1
                                                                )	
                                                                ORDER BY ajt.`type` ASC						
                                                                ");
                                                                
                                                                foreach( $agen_serv_sql->result() as $agen_serv_row ){ ?>													
                                                                    <!-- <option value="<?php //echo $agen_serv_row->ajt_id; ?>"><?php //echo "{$agen_serv_row->ajt_type} -{$agen_serv_row->price}"; ?></option> -->
                                                                    
                                                                    <option value="<?php echo $agen_serv_row->ajt_id; ?>">
                                                                    <?php 
                                                                    
                                                                    echo "{$agen_serv_row->ajt_type} - ";
                                                                    $price_var_params = array(
                                                                        'service_type' =>$agen_serv_row->ajt_id,
                                                                        'agency_id'  => $agency_id
                                                                    );
                                                                    $price_var_arr = $this->system_model->get_agency_price_variation($price_var_params);
                                                                    echo $price_var_arr['price_breakdown_text'];
                                                                    
                                                                    ?></option>										
                                                                <?php
                                                                }
                                                                

                                                                //foreach( $agen_serv_sql->result() as $agen_serv_row ){ ?>													
                                                                    <!--<option value="<?php //echo $agen_serv_row->ajt_id; ?>"><?php //echo "{$agen_serv_row->ajt_type} - \${$agen_serv_row->price}"; ?></option> -->											
                                                                <?php
                                                                //}
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div id="service_to_sats_q3_div">
                                                        <p>Select Alarm: </p>
                                                        <p>
                                                            <select id="preferred_alarm" class="form-control my-2" style="width:auto;">
                                                                <option value="">---</option>
                                                                <?php    
                                                                // 10 - 240v RF
                                                                // 14 - 240v RF(cav)
                                                                // 22 - 240V RF(EP)                                                    
                                                                $agency_alarms_sql = $this->db->query("
                                                                SELECT *
                                                                FROM `agency_alarms` AS agen_al
                                                                LEFT JOIN `alarm_pwr` AS al_p ON agen_al.`alarm_pwr_id` = al_p.`alarm_pwr_id`                                                        
                                                                WHERE agen_al.`agency_id` = {$this->session->agency_id}	 
                                                                AND agen_al.`alarm_pwr_id` IN(10,22)                                                  							
                                                                ");
                                                                foreach( $agency_alarms_sql->result() as $agency_alarms_row ){ ?>													
                                                                    <option value="<?php echo $agency_alarms_row->alarm_pwr_id; ?>"><?php echo $agency_alarms_row->alarm_make; ?></option>													
                                                                <?php
                                                                }                                                        
                                                                ?>
                                                            </select>
                                                        </p>
                                                    </div> 
                                                                                                  

                                                <?php
                                                }else{ // default ?>

                                                    <div>
                                                        <select id="new_service_type" class="form-control my-2" style="width:auto;">
                                                            <option value="">---</option>
                                                            <?php
                                                            $agency_id = $this->session->agency_id;

                                                            $agen_serv_sql = $this->db->query("
                                                            SELECT 
                                                                agen_serv.`price`,

                                                                ajt.`id` AS ajt_id,
                                                                ajt.`type` AS ajt_type
                                                            FROM `agency_services` AS agen_serv
                                                            LEFT JOIN `alarm_job_type` AS ajt ON agen_serv.`service_id` = ajt.`id`
                                                            WHERE agen_serv.`agency_id` = {$this->session->agency_id}	
                                                            AND agen_serv.`service_id` NOT IN(
                                                                SELECT `alarm_job_type_id`
                                                                FROM `property_services` 
                                                                WHERE `property_id` = {$this->uri->segment(3)}
                                                                AND `service` = 1
                                                            )							
                                                            ");
                                                            
                                                            foreach( $agen_serv_sql->result() as $agen_serv_row ){ ?>													
                                                                <!-- <option value="<?php //echo $agen_serv_row->ajt_id; ?>"><?php //echo "{$agen_serv_row->ajt_type} -{$agen_serv_row->price}"; ?></option> -->
                                                                
                                                                <option value="<?php echo $agen_serv_row->ajt_id; ?>">
                                                                <?php 
                                                                
                                                                echo "{$agen_serv_row->ajt_type} - ";
                                                                $price_var_params = array(
                                                                    'service_type' =>$agen_serv_row->ajt_id,
                                                                    'agency_id'  => $agency_id
                                                                );
                                                                $price_var_arr = $this->system_model->get_agency_price_variation($price_var_params);
                                                                echo $price_var_arr['price_breakdown_text'];
                                                                
                                                                ?></option>										
                                                            <?php
                                                            }
                                                            

                                                            //foreach( $agen_serv_sql->result() as $agen_serv_row ){ ?>													
                                                                <!--<option value="<?php //echo $agen_serv_row->ajt_id; ?>"><?php //echo "{$agen_serv_row->ajt_type} - \${$agen_serv_row->price}"; ?></option>	-->												
                                                            <?php
                                                            //}
                                                            ?>
                                                        </select>
                                                    </div>

                                                <?php
                                                }
                                                ?>                                                                                                

                                                <button type='button' class='btn' id='add_new_service_type_submit_btn' style="display:<?php echo ( $prop_det->p_state == 'QLD' )?'none;':'block'; ?>">Submit</button>

                                            </div>                                            

                                            </div>

                                        <?php
                                        }
                                        ?>                                      
                                        
                                    </div>
                                
                                </div>
</form>
                            </div>
                        </div>
            <!-- PROPERTY DETAILS TAB HERE -->

            <div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-2v2">


                 <table class="table vpd_table table-sm">
                <tbody>
                    <tr>
                        <td>


                    <?php
                    foreach($agency_services as $row){
                        $is_bundle = $row['is_bundle'];
                        $bundle_ids = $row['bundle_ids'];

                        $newPrice = $row['new_price'];
                        $psi_val = $row['psi_val'];
                    ?>


                    <!-- MORE INFORMATON TOGGLE FOR SERVICES SELECTED START HERE -->
                        <?php
                        //if($psi_val==1){

                            //check active property services = 1
                            $gio_ah_ah_service = $row['ps_service'];

                            ?>  <!-- if service == sats display view details button -->
                            <?php if($gio_ah_ah_service==1){ ?>
                            <div data-jobtypeid="<?php echo $row['job_type_id']; ?>" class="serv_more_info_box prop_det_box_tab">

                            <?php
                                $property_id = $this->uri->segment(3);
                                
                                $price_var_params = array(
                                    'service_type' =>$row['service_id'],
                                    'property_id'  => $property_id
                                );
                                $price_var_arr = $this->system_model->get_property_price_variation($price_var_params);
                                //echo $price_var_arr['price_text'];
                                echo "<h2>".$row['full_name']." ".$price_var_arr['price_breakdown_text']."</h2>";  // TITLE/HEADING
                                
                                //echo "<h2>".$row['type']." $".$newPrice."</h2>";  // TITLE/HEADING

                                $bundle_more_det = array();
                                if($is_bundle==1){
                                    $bundle_more_det = explode(',',trim($bundle_ids));
                                }else if($is_bundle == 0){
                                    $bundle_more_det[] = $row['job_type_id'];
                                }

                               foreach($bundle_more_det as $bundle_more_det_row){

                            ?>


                            <?php if($bundle_more_det_row == 2){ ?> <!------------Smoke Alarms Details-------- -->
                                <div class="box-group">
                                   <?php
                                       /* if($is_bundle==0){
                                            echo "<h2>".$row['type']." $".$newPrice."</h2>";
                                        }*/
                                    ?>
                                    <h3>Alarm Details</h3>
                                    <table class="serv_more_info_table main-table">
                                    <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th>Power</th>
                                            <th>Type</th>
                                            <th>Make</th>
                                            <th>Model</th>
                                            <th>Expiry</th>
                                            <th>RFC</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                            <?php if(!empty($alarm_det)){ ?>
                                                <?php foreach($alarm_det as $row_alarm_det): ?>
                                                    <tr>
                                                            <td><?php echo $row_alarm_det->ts_position ?></td>
                                                            <td><?php echo $row_alarm_det->alarm_pwr ?></td>
                                                            <td><?php echo $row_alarm_det->alarm_type ?></td>
                                                            <td><?php echo $row_alarm_det->make ?></td>
                                                            <td><?php echo $row_alarm_det->model ?></td>
                                                            <td><?php echo $row_alarm_det->expiry ?></td>
                                                            <td><?php echo (($row_alarm_det->ts_required_compliance==1)?'Yes':'No') ?></td>
                                                    </tr>
                                                 <?php endforeach; ?>
                                            <?php }else{ ?>
                                                <tr>
                                                             <td><span class="font-icon font-icon-warning red"></span>&nbsp;&nbsp;No Alarm Data on File</td>
                                                             <td>N/A</td>
                                                             <td>N/A</td>
                                                             <td>N/A</td>
                                                             <td>N/A</td>
                                                             <td>N/A</td>
                                                             <td>N/A</td>
                                                </tr>
                                            <?php } ?>


                                        <tr>
                                            <td colspan="7"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;&nbsp;RFC = Required for Compliance</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } ?> <!------------Smoke Alarms Details End-------- -->


                            <?php if($bundle_more_det_row == 5){ ?> <!------------Safety Switch-------- -->
                                <div class="box-group">
                                   <?php
                                      /*  if($is_bundle==0){
                                            echo "<h2>".$row['type']." $".$newPrice ."</h2>";
                                        }*/
                                    ?>
                                    <h3>Safety Switch</h3>
                                    <table class="serv_more_info_table main-table">
                                    <thead>
                                        <tr>
                                            <th>Fusebox Location</th>
                                            <th>Switch Quantity</th>
                                            <th>Switch Board Image</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td> <?php echo ($safety_switch->ss_location!="")?$safety_switch->ss_location:'<span class="font-icon font-icon-warning red"></span> No Fusebox location on file' ?> </td>
                                            <td> <?php echo ($safety_switch->ss_quantity!="")?$safety_switch->ss_quantity:'<span class="font-icon font-icon-warning red"></span> No Safety Switch Quantity on file' ?> </td>
                                            <td>
                                                    <?php
                                                        if($safety_switch->ss_image!=""){
                                                            
                                                            // crm CI upload path
                                                            $switchboard_full_path = "{$this->config->item('crmci_link')}/uploads/switchboard_image/{$safety_switch->ss_image}";                                                            
                                                            ?>
                                                            <a data-toggle="tooltip" title="View Switch Board image" target="_blank" href="<?php echo $switchboard_full_path; ?>">
                                                                 <span class="fa fa-camera" style="font-size:18px;"></span>
                                                            </a>
                                                        <?php }else{
                                                            echo "No image available";
                                                        }
                                                    ?>
                                            </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="box-group">
                                    <h3>Switch Details</h3>
                                    <table class="serv_more_info_table main-table">
                                    <thead>
                                        <tr>
                                            <th>Make</th>
                                            <th>Model</th>
                                           <!-- <th>Test Result</th> -->
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($safety_switch_details)){ ?>
                                                <?php foreach($safety_switch_details as $newTT): ?>
                                                    <tr>
                                                        <td>
                                                        <div style="display:none;"><?php echo $newTT->job_id; ?></div>
                                                        <?php

                                                        echo $newTT->make;

                                                        ?></td>
                                                        <td><?php echo $newTT->model; ?></td>

                                                        <!--<td> -->
                                                            <?php
/*
                                                            if($newTT->test==0){
                                                                echo "Failed";
                                                            }else if($newTT->test==1){
                                                                echo "Passed";
                                                            }else if($newTT->test==2){
                                                                echo "No Power";
                                                            }

*/

                                                            ?>

                                                      <!--  </td> -->

                                                    </tr>
                                                <?php endforeach; ?>
                                           <?php }else{ ?>
                                                    <tr>
                                                    <td><span class="font-icon font-icon-warning red"></span> This property has no Safety Switch data on file</td>
                                                    <td>N/A</td>
                                                  <!--  <td>N/A</td> -->
                                                    </tr>
                                            <?php }

                                            ?>

                                        </tbody>
                                    </table>
                                </div>
                            <?php } ?>


                            <?php if($bundle_more_det_row == 6){ ?> <!------------Corded Window-------- -->
                                <div class="box-group">
                                    <?php
                                       /* if($is_bundle==0){
                                            echo "<h2>".$row['type']." $".$newPrice ."</h2>";
                                        }*/
                                    ?>
                                    <h3>Window Details</h3>
                                    <table class="serv_more_info_table main-table">
                                    <thead>
                                        <tr>
                                            <th>Location</th>
                                            <th>Number of windows</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                            <?php if(!empty($corded_windows)){ ?>

                                                <?php foreach($corded_windows as $new_corded_windows): ?>
                                                    <tr>
                                                        <td><?php echo $new_corded_windows->location ?></td>
                                                        <td><?php echo $new_corded_windows->num_of_windows ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            <?php }else{ ?>
                                                    <tr>
                                                        <td><span class="font-icon font-icon-warning red"></span>&nbsp;&nbsp;This property has no Corded Window data on file</td>
                                                        <td>N/A</td>
                                                    </tr>
                                            <?php } ?>


                                        </tbody>
                                    </table>
                                </div>
                            <?php } ?>

                            <?php if($bundle_more_det_row == 7){ ?> <!------------Water Meter-------- -->
                                <div class="box-group">
                                   <?php
                                       /* if($is_bundle==0){
                                            echo "<h2>".$row['type']." $".$newPrice ."</h2>";
                                        }*/
                                    ?>
                                    <h3>Water Meter Details</h3>
                                    <table class="serv_more_info_table main-table">
                                    <thead>
                                        <tr>
                                            <th>Location</th>
                                            <th>Reading</th>
                                            <th>Meter Image</th>
                                            <th>Meter Reading Image</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td><?php echo (!empty($water_meter_details->location))?$water_meter_details->location:'&nbsp;' ?></td>
                                            <td><?php echo (!empty($water_meter_details->reading))?$water_meter_details->reading:'&nbsp;' ?></td>
                                            <td><?php if(!empty($water_meter_details->meter_image)){
                                                    echo "<img width='80' src=".$this->config->item('crm_link')."/".$water_meter_details->meter_image.">";
                                            }else{
                                                echo "No image available";
                                            } ?>
                                            </td>
                                            <td><?php
                                                if(!empty($water_meter_details->meter_reading_image)){
                                                    echo "<img width='80' src=".$this->config->item('crm_link')."/".$water_meter_details->meter_reading_image.">";
                                                }else{
                                                    echo "No image available";
                                                }
                                            ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } ?>

                                <?php } ?>

                          </div>
                            <?php } ?>
                          <!-- MORE INFORMATON TOGGLE FOR SERVICES SELECTED END HERE -->

                        <?php

                        //}  //end psi_val

                            ?>
         <?php
         } //END LOOP
         ?>

         <div class="serv_more_info_box">
                                    <!-- LAST SERVICE START -->
                                    <div class="box-group_ss">
                                                <h3>Last Service</h3>

                                                <table class="serv_more_info_table main-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Job Type</th>
                                                            <th>Invoice</th>
                                                            <th>Certificate</th>
                                                            <th>Combined</th>
                                                            <?php
                                                            if(!empty($last_service)){
                                                                if( $last_service->state=='QLD' && $last_service->qld_new_leg_alarm_num>0 && $last_service->assigned_tech!=1 && $last_service->assigned_tech!=3 && $last_service->prop_upgraded_to_ic_sa!=1 ){
                                                                    echo "<th>Upgrade Quote</th>";
                                                                }
                                                            }
                                                            ?>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                    <?php if(!empty($last_service) && $last_service->assigned_tech!=NULL && $last_service->assigned_tech!=1 && $last_service->assigned_tech!=3){ ?>
                                                        <td><?php echo date('d/m/Y', strtotime($last_service->date)) ?></td>
                                                        <td>
                                                                <?php echo $last_service->job_type. (($last_service->assigned_tech == 1)?'(Other Supplier)':'')  ?>
                                                        </td>
                                                        <td>
                                                                <a data-toggle="tooltip" title="Download Invoice" href="<?php echo "{$invoice_url}/D" ?>"><span style="font-size:16px;" class="font-icon font-icon-cloud-download"></span></a>
                                                                <a style="margin-left:5px;" data-toggle="tooltip" title="View Invoice" target="_blank" href="<?php echo $invoice_url ?>"><span style="font-size:16px;" class="font-icon font-icon-eye"></span></a>
                                                            </td>
                                                        <td>
                                                                <a data-toggle="tooltip" title="Download Certificate" href="<?php echo "{$certificate_url}/D" ?>"><span style="font-size:16px;" class="font-icon font-icon-cloud-download"></span></a>
                                                                <a style="margin-left:5px;" data-toggle="tooltip" title="View Certificate" target="_blank" href="<?php echo $certificate_url ?>"><span style="font-size:16px;" class="font-icon font-icon-eye"></span></a>
                                                            </td>
                                                        <td>
                                                                <a data-toggle="tooltip" title="Download Combined" href="<?php echo "{$combined_url}/D" ?>"><span style="font-size:16px;" class="font-icon font-icon-cloud-download"></span></a>
                                                                <a data-toggle="tooltip" title="View Combined" target="_blank" href="<?php echo $combined_url ?>"><span style="font-size:16px;" class="font-icon font-icon-eye"></span></a>
                                                            </td>

                                                            <?php
                                                                if( $last_service->state=='QLD' && $last_service->qld_new_leg_alarm_num>0 && $last_service->assigned_tech!=1 && $last_service->prop_upgraded_to_ic_sa!=1 ){
                                                                    echo "<td>";
                                                                        if( $last_service->qld_new_leg_alarm_num>0 ){
                                                                            $encrypted_job_id = $hashIds->encodeString($last_service->id);
								                                            $quote_ci_link = "{$this->config->item('crmci_link')}/pdf/view_quote/?job_id={$encrypted_job_id}&qt=combined";
                                                                            echo "<a data-toggle='tooltip' title='Download Upgrade Quote' href='{$quote_ci_link}&output_type=D'>
                                                                                    <span style='font-size:16px;' class='font-icon font-icon-cloud-download'></span>
                                                                                </a>
                                                                                <a data-toggle='tooltip' title='View Upgrade Quote' href='{$quote_ci_link}' target='_blank'>
                                                                                    <span style='font-size:16px;' class='font-icon font-icon-eye'></span>
                                                                                </a>
                                                                                <a data-toggle='tooltip' title='Copy to Clipboard' href='javascript:void(0);'>
                                                                                    <span style='font-size:16px;' class='font-icon font-icon-share upgrade_quote_ctcb'>
                                                                                        <input type='hidden' class='upgrade_quote_link' value='{$quote_ci_link}' />
                                                                                    </span>                                                                                                                                                                        
                                                                                </a>
                                                                                ";
                                                                        }
                                                                    echo "</td>";
                                                                }
                                                            ?>

                                                    <?php }else{ ?>
                                                            <td><span class="font-icon font-icon-warning red"></span>&nbsp;&nbsp;
                                                            <?php
                                                            // logged user
                                                            $aua_sql = $this->db->query("
                                                            SELECT `fname`, `lname`, `email`
                                                            FROM `agency_user_accounts`
                                                            WHERE `agency_user_account_id` = {$this->session->aua_id}
                                                            ");
                                                            $aua_row = $aua_sql->row();
                                                        
                                                            // mailto
                                                            $mailto_email = $_ENV['COMPANY_EMAIL'];
                                                            $mailto_subject = rawurlencode("Portal Request for {$p_address}");
                                                            $mailto_body = rawurlencode("{$aua_row->fname} {$aua_row->lname} from {$prop_det->agency_name} has requested a certificate/Report for {$p_address}. please email it to {$aua_row->fname} {$aua_row->lname} at {$aua_row->email}");
                                                            
                                                            if( config_item('theme') == 'sas' ){ // SAS only
                                                                echo 'Your property may be available on our legacy system. Please click <a class="mailto_link" href="mailto:'.$mailto_email.'?Subject='.$mailto_subject.'&body='.$mailto_body.'">HERE</a> to request your Certificate/Report';
                                                            }else{ // default, SATS
                                                                echo 'This property has no recent visit data on file';
                                                            }
                                                            ?>                                                            
                                                            </td>
                                                            <td>N/A</td>
                                                            <td>N/A</td>
                                                            <td>N/A</td>
                                                            <td>N/A</td>

                                                            <?php
                                                            if(!empty($last_service)){
                                                                if( $last_service->state=='QLD' && $last_service->qld_new_leg_alarm_num>0 && $last_service->assigned_tech!=1 && $last_service->prop_upgraded_to_ic_sa!=1 ){
                                                                    if( $last_service->qld_new_leg_alarm_num>0 ){
                                                                            echo "<td>No Quote Available</td>";
                                                                    }

                                                                }
                                                            }
                                                            ?>

                                                    <?php } ?>

                                                            </tr>
                                                        </tbody>
                                                </table>
                                            </div>
                                            <!-- LAST SERVICE END -->


                                            <!-- LAST YEARLY MAINTENANCE START -->
                                        <?php if(!empty($last_yearly_maintenance) && $last_yearly_maintenance->assigned_tech!=1){ ?>
                                            <div class="box-group_ss">
                                                <h3>Last Yearly Maintenance/Annual Invoice</h3>
                                                <table class="serv_more_info_table main-table">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Job Type</th>
                                                        <th>Invoice</th>
                                                        <th>Certificate</th>
                                                        <th>Combined</th>
                                                        <?php
                                                            if(!empty($last_yearly_maintenance)){
                                                                if( $last_yearly_maintenance->state=='QLD' && $last_yearly_maintenance->qld_new_leg_alarm_num>0 && $last_yearly_maintenance->assigned_tech!=1 && $last_yearly_maintenance->prop_upgraded_to_ic_sa!=1 ){
                                                                    echo "<th>Upgrade Quote</th>";
                                                                }
                                                            }
                                                            ?>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                    <?php if(!empty($last_yearly_maintenance) && $last_yearly_maintenance->assigned_tech!=1){ ?>
                                                        <td><?php echo date('d/m/Y', strtotime($last_yearly_maintenance->date)) ?></td>
                                                        <td><?php echo $last_yearly_maintenance->job_type. (($last_yearly_maintenance->assigned_tech == 1)?'(Other Supplier)':'')  ?></td>
                                                        <td>
                                                            <a data-toggle="tooltip" title="Download Invoice" href="<?php echo "{$lym_invoice_url}/D" ?>"><span style="font-size:16px;" class="font-icon font-icon-cloud-download"></span></a>
                                                            <a style="margin-left:5px;" data-toggle="tooltip" title="View Invoice" target="_blank" href="<?php echo $lym_invoice_url ?>"><span style="font-size:16px;" class="font-icon font-icon-eye"></span></a>
                                                        </td>
                                                        <td>
                                                            <?php if($last_yearly_maintenance->assigned_tech!=NULL && $last_yearly_maintenance->assigned_tech!=1 && $last_yearly_maintenance->assigned_tech!=2){ ?>
                                                            <a data-toggle="tooltip" title="Download Certificate" href="<?php echo "{$lym_certificate_url}/D" ?>"><span style="font-size:16px;" class="font-icon font-icon-cloud-download"></span></a>
                                                            <a style="margin-left:5px;" data-toggle="tooltip" title="View Certificate" target="_blank" href="<?php echo $lym_certificate_url ?>"><span style="font-size:16px;" class="font-icon font-icon-eye"></span></a>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php  if($last_yearly_maintenance->assigned_tech!=NULL && $last_yearly_maintenance->assigned_tech!=1 && $last_yearly_maintenance->assigned_tech!=2){ ?>
                                                            <a data-toggle="tooltip" title="Download Combined" href="<?php echo "{$lym_combined_url}/D" ?>"><span style="font-size:16px;" class="font-icon font-icon-cloud-download"></span></a>
                                                            <a style="margin-left:5px;" data-toggle="tooltip" title="View Combined" target="_blank" href="<?php echo $lym_combined_url ?>"><span style="font-size:16px;" class="font-icon font-icon-eye"></span></a>
                                                            <?php } ?>
                                                       </td>

                                                            <?php
                                                            if($last_yearly_maintenance->assigned_tech!=1 && $last_yearly_maintenance->assigned_tech!=NULL && $last_yearly_maintenance->assigned_tech!=2){
                                                                if( $last_yearly_maintenance->state=='QLD' && $last_yearly_maintenance->qld_new_leg_alarm_num>0 && $last_yearly_maintenance->prop_upgraded_to_ic_sa!=1 ){

                                                                    echo "<td>";
                                                                    if( $last_yearly_maintenance->qld_new_leg_alarm_num>0 ){
                                                                        $encrypted_job_id_last_ym = $hashIds->encodeString($last_yearly_maintenance->id);
                                                                        $quote_ci_link_last_ym = "{$this->config->item('crmci_link')}/pdf/view_quote/?job_id={$encrypted_job_id_last_ym}&qt=combined";
                                                                        echo "<a data-toggle='tooltip' title='Download Upgrade Quote' href='{$quote_ci_link_last_ym}/D'>
                                                                                <span style='font-size:16px;'' class='font-icon font-icon-cloud-download'></span>
                                                                            </a>
                                                                            <a data-toggle='tooltip' title='View Upgrade Quote' href='{$quote_ci_link_last_ym}' target='_blank'>
                                                                                <span style='font-size:16px;'' class='font-icon font-icon-eye'></span>
                                                                            </a>
                                                                            <a data-toggle='tooltip' title='Copy to Clipboard' href='javascript:void(0);'>
                                                                                <span style='font-size:16px;' class='font-icon font-icon-share upgrade_quote_ctcb'>
                                                                                    <input type='hidden' class='upgrade_quote_link' value='{$quote_ci_link_last_ym}' />
                                                                                </span>                                                                                                                                                                        
                                                                            </a>
                                                                            ";
                                                                    }
                                                                    echo "</td>";
                                                                }
                                                            }
                                                            ?>

                                                    <?php }else{?>
                                                        <td><span class="font-icon font-icon-warning red"></span>&nbsp;&nbsp;This Property has no Yearly Maintenance Visit data on file</td>
                                                        <td>N/A</td>
                                                            <td>N/A</td>
                                                            <td>N/A</td>
                                                            <td>N/A</td>
                                                            <?php
                                                            if(!empty($last_yearly_maintenance)){
                                                                if( $last_yearly_maintenance->state=='QLD' && $last_yearly_maintenance->qld_new_leg_alarm_num>0 && $last_yearly_maintenance->assigned_tech!=1 && $last_yearly_maintenance->prop_upgraded_to_ic_sa!=1 ){
                                                                    if( $last_yearly_maintenance->qld_new_leg_alarm_num>0 ){
                                                                            echo "<td>No Quote Available</td>";
                                                                    }

                                                                }
                                                            }
                                                            ?>
                                                    <?php } ?>

                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } ?>
                                                <!-- LAST YEARLY MAINTENANCE END -->

                                                 <!-- Property Notes -->
                                                 <div class="box-group_ss">
                                                  <?php echo form_open(base_url('/properties/update_comment'),'id=update_comment_form'); ?>
                                                    <div class="flex flex-row">
                                                            <div> <h3>Property Notes <a id="editComment" class="float-right ml-3"  data-toggle="tooltip" title="" data-original-title="Edit"><span class="font-icon font-icon-pencil"></span></a>
                                                        
                                                            <div id="commentActionBtn" hidden="false" class="float-right mb-2" style="width:128px;">
                                                                    <button id="updateCommentBtn" style="margin: 0;" type="button" class="btn btn-sm">Submit</button>
                                                                    <button id="editCommentCancel" type="button" class="btn btn-sm btn-danger ">Cancel</button>
                                                            </div>
                                                        
                                                        </h3> </div>
                                                          
                                                     </div>
                                                     <input id="oldComment" type="hidden" value="<?php echo $prop_det->comments; ?>">
                                                    <textarea id="editTxt" name="editTxt"  style="margin-bottom:20px;" class="form-control" readonly><?php echo $prop_det->comments; ?></textarea>
                                                </div>
                                                <!-- Property Notes end-->
                        </div>

                        </td>
                    </tr>
                     </tbody>
                </table>
            </div>

            </div>
        </section

    </div>
</div>

<!-- FANCYBOX START -->
<?php
if( $has_service_to_sats == true ){ ?>

    <div id="changer_service_fb" class="fancybox" style="display:none;">        

        <div class="row mt-3">

            <div class="col-lg-3 columns" style="padding-top: 10px;">
                <b>What would you like to do?</b>
            </div>    
            
            <div class="col-lg-9 columns">
                <button id="change_current_service_btn" class="btn btn-inline btn-warning-outline change_or_add_btn" type="button" data-btn_class="warning">Change Current Service</button>
                <button id="disable_current_service_btn" class="btn btn-inline btn-danger-outline change_or_add_btn" type="button" data-btn_class="danger">Disable Current Service</button>        
                <button id="add_new_service_btn" class="btn btn-inline btn-success-outline change_or_add_btn" type="button" data-btn_class="success">Add a New Service</button>        
            </div>        

        </div>

        <div id="change_current_service_content_div" class="row mt-3 change_or_add_content_div">
            
            <div class="col">

                <div class="row mt-3">

                    <div class="col-lg-3" style="padding-top: 10px;">
                        <b>Please select the new service you'd like to activate</b>
                    </div>    

                    <div class="col-lg-9">
                        <select id="to_service_type" class="form-control">
                            <option value="">---</option>               
                            <?php

                            $agency_id = $this->session->agency_id;
                            $agen_serv_sql_str = "
                            SELECT 
                                agen_serv.`price`,

                                ajt.`id` AS ajt_id,
                                ajt.`type` AS ajt_type
                            FROM `agency_services` AS agen_serv
                            LEFT JOIN `alarm_job_type` AS ajt ON agen_serv.`service_id` = ajt.`id`
                            WHERE agen_serv.`agency_id` = {$this->session->agency_id}
                            AND agen_serv.`service_id` NOT IN(
                                SELECT `alarm_job_type_id`
                                FROM `property_services` 
                                WHERE `property_id` = {$this->uri->segment(3)}
                                AND `service` = 1
                            )									
                            ";
                            $agen_serv_sql = $this->db->query($agen_serv_sql_str);
                            foreach( $agen_serv_sql->result() as $agen_serv_row ){ ?>													
                                <!-- <option value="<?php //echo $agen_serv_row->ajt_id; ?>"><?php //echo "{$agen_serv_row->ajt_type} -{$agen_serv_row->price}"; ?></option> -->
                                
                                <option value="<?php echo $agen_serv_row->ajt_id; ?>">
                                <?php 
                                
                                echo "{$agen_serv_row->ajt_type} - ";
                                $price_var_params = array(
                                    'service_type' =>$agen_serv_row->ajt_id,
                                    'agency_id'  => $agency_id
                                );
                                $price_var_arr = $this->system_model->get_agency_price_variation($price_var_params);
                                echo $price_var_arr['price_breakdown_text'];
                                
                                ?></option>										
                            <?php
                            }
                            ?>
                        </select>   
                    </div>  

                </div> 

                <div class="row mt-3 text-right">
                    <div class="col">
                        <span class="label_alert d-none text-danger font-weight-bold mr-3 text-left">Please select an '(IC)' service as this property has Interconnected Smoke Alarms.</span>                    
                        <button id="change_service_save_btn" class="btn btn-inline process_buttons" type="button">Update</button>
                    </div>
                </div>  

            </div>

        </div>

        <div id="disable_current_service_content_div" class="row mt-3 change_or_add_content_div">

            <div class="col">

                <?php
                if( $job_status == true ){ // cannot disable ?>

                    <div id="cannot_disable_service_div" class="row mt-3">
                        <div class="col">
                            This property has an active job, so you cannot deactivate the corresponding service. 
                            If you'd like to change to a different service, please use the 'Change Serivce' option. 
                            Otherwise, please contact our office.
                        </div>
                    </div>

                <?php
                }else{ // can disable ?>

                    <div class="row mt-3">

                        <div class="col-lg-3">
                            <b>Please tell us why you're disabling the service?</b>
                        </div>    

                        <div class="col-lg-9">
                            <select id="disable_current_service_dp" class="form-control">
                                <option value="">---</option>               
                                <option value="1">The owner would like to service the property himself</option>
                                <option value="2">The owner is changing to a different smoke alarm company</option>
                                <option value="3">We no longer manage the property</option>
                            </select>   
                        </div>  

                    </div>            

                    <div id="disable_service_can_nlm_div" class="row mt-3">

                        <div class="col-lg-6">                    
                            <input type="text" id="disable_service_nlm_date" class="form-control flatpickr flatpickr-input disable_service_nlm_date" placeholder="'No Longer Managed' Date*" data-date-format="d/m/Y" />
                        </div>    

                        <div class="col-lg-6">
                            <div class="form-group">
                                <!--<input class="form-control" type="text" id="disable_service_nlm_reason" name="disable_service_nlm_reason" placeholder="'No Longer Managed' Reason*" />-->
                                
                                <select id="reason_they_left2" class="form-control mb-2 reason_they_left2">
                                    <option value="">---Select Reason---</option>       
                                    <?php
                                    // get leaving reason                                                
                                    $lr_sql = $this->db->query("
                                    SELECT *
                                    FROM `leaving_reason`
                                    WHERE `active` = 1
                                    AND `display_on` IN(3,5)
                                    ORDER BY `reason` ASC
                                    ");   
                                    foreach( $lr_sql->result() as $lr_row ){ ?>
                                        <option value="<?php echo $lr_row->id; ?>"><?php echo $lr_row->reason; ?></option> 
                                    <?php
                                    }                                         
                                    ?> 
                                    <option value="-1">Other</option> 
                                </select>

                                <textarea class="form-control addtextarea mb-2 other_reason2" id="other_reason2" placeholder="Other Reason"></textarea>

                            </div> 
                        </div> 

                    </div>

                    <div class="row mt-3 text-right">
                        <div class="col">                    
                            <button id="disable_service_save_btn" class="btn btn-inline process_buttons" type="button">Update</button>
                        </div>

                    </div>   

                <?php
                }
                ?>                     

            </div>
                

        </div>

        <div id="add_new_service_content_div" class="row mt-3 change_or_add_content_div">

            <div class="col">            

                <?php
                $can_add_new_service =  false;

                // if property service is more than one or it has CW bundle
                if( $prop_ser_count > 1 || $has_cw_bundle_service == true ){  ?>

                    <div class="row">

                        <div class="col">
                            Sorry, we cannot find any services to add, please try the 'Change Current Service' option, or contact the office.
                        </div>

                    </div>

                <?php
                }else{ // has no CW bundle

                    if( $has_sa_wm_service == true ){ 

                        $can_add_new_service =  true; // show save button
                        
                        ?>

                        <div class="row">

                            <div class="col-lg-3">
                                <b>New Service</b>
                            </div>

                            <div class="col-lg-9">
                                <select id="new_service_type" class="form-control">
                                    <option value="">---</option>
                                    <?php
                                    $agency_id = $this->session->agency_id;

                                    $agen_serv_sql = $this->db->query("
                                    SELECT 
                                        agen_serv.`price`,

                                        ajt.`id` AS ajt_id,
                                        ajt.`type` AS ajt_type
                                    FROM `agency_services` AS agen_serv
                                    LEFT JOIN `alarm_job_type` AS ajt ON agen_serv.`service_id` = ajt.`id`
                                    WHERE agen_serv.`agency_id` = {$this->session->agency_id}	
                                    AND ajt.`id` IN(6)
                                    AND agen_serv.`service_id` NOT IN(
                                        SELECT `alarm_job_type_id`
                                        FROM `property_services` 
                                        WHERE `property_id` = {$this->uri->segment(3)}
                                        AND `service` = 1
                                    )							
                                    ");

                                    foreach( $agen_serv_sql->result() as $agen_serv_row ){ ?>													
                                        <!-- <option value="<?php //echo $agen_serv_row->ajt_id; ?>"><?php //echo "{$agen_serv_row->ajt_type} -{$agen_serv_row->price}"; ?></option> -->
                                        
                                        <option value="<?php echo $agen_serv_row->ajt_id; ?>">
                                        <?php 
                                        
                                        echo "{$agen_serv_row->ajt_type} - ";
                                        $price_var_params = array(
                                            'service_type' =>$agen_serv_row->ajt_id,
                                            'agency_id'  => $agency_id
                                        );
                                        $price_var_arr = $this->system_model->get_agency_price_variation($price_var_params);
                                        echo $price_var_arr['price_breakdown_text'];
                                        
                                        ?></option>										
                                    <?php
                                    }
                                    ?>
                                </select>   
                            </div>   

                        </div>   

                    <?php
                    }else if( $only_has_cw == true ){ 
                        
                        $can_add_new_service =  true; // show save button

                        ?>

                        <div class="row">

                            <div class="col-lg-3">
                                <b>New Service</b>
                            </div>

                            <div class="col-lg-9">
                                <select id="new_service_type" class="form-control">
                                    <option value="">---</option>
                                    <?php
                                    $agency_id = $this->session->agency_id;

                                    $agen_serv_sql = $this->db->query("
                                    SELECT 
                                        agen_serv.`price`,

                                        ajt.`id` AS ajt_id,
                                        ajt.`type` AS ajt_type
                                    FROM `agency_services` AS agen_serv
                                    LEFT JOIN `alarm_job_type` AS ajt ON agen_serv.`service_id` = ajt.`id`
                                    WHERE agen_serv.`agency_id` = {$this->session->agency_id}	
                                    AND ajt.`id` IN(2,7,11)
                                    AND agen_serv.`service_id` NOT IN(
                                        SELECT `alarm_job_type_id`
                                        FROM `property_services` 
                                        WHERE `property_id` = {$this->uri->segment(3)}
                                        AND `service` = 1
                                    )							
                                    ");

                                    foreach( $agen_serv_sql->result() as $agen_serv_row ){ ?>													
                                        <!-- <option value="<?php //echo $agen_serv_row->ajt_id; ?>"><?php //echo "{$agen_serv_row->ajt_type} -{$agen_serv_row->price}"; ?></option> -->
                                        
                                        <option value="<?php echo $agen_serv_row->ajt_id; ?>">
                                        <?php 
                                        
                                        echo "{$agen_serv_row->ajt_type} - ";
                                        $price_var_params = array(
                                            'service_type' =>$agen_serv_row->ajt_id,
                                            'agency_id'  => $agency_id
                                        );
                                        $price_var_arr = $this->system_model->get_agency_price_variation($price_var_params);
                                        echo $price_var_arr['price_breakdown_text'];
                                        
                                        ?></option>										
                                    <?php
                                    }
                                    ?>
                                </select>   
                            </div>   

                        </div> 

                    <?php
                    }else{ ?>

                        <div class="row">

                            <div class="col">
                                Sorry, we cannot find any services to add, please try the 'Change Current Service' option, or contact the office.
                            </div>

                        </div>

                    <?php
                    }   
                        
                }


                if( $can_add_new_service == true ){ ?>

                    <div class="row mt-3 text-right">

                        <div class="col">                    
                            <button id="add_new_service_type_submit_btn" class="btn btn-inline process_buttons" type="button">Update</button>
                        </div>

                    </div> 
                
                <?php
                }
                ?>              

            </div>
        
        

            

        </div>

        <input type="hidden" id="from_service_type" />
        

    </div>

<?php
}
?>
<!-- FANCYBOX END -->

<script type="text/javascript">

    jQuery(document).ready(function(){

        <?php
        // scroll to bottom script
        if( $this->input->get_post('scroll_to_bottom') == 1 ){ ?>
            $("html, body").animate({ scrollTop: $(document).height() }, 1000);
        <?php
        }
        ?>

        var current_user_logged_in_id = <?php echo $this->session->aua_id ?>;

        //edit comment / notes
        $("#editComment").click(function(){
        $('#commentActionBtn').prop('hidden', false);
        $('#editComment').prop('hidden', true);
        $('#editTxt').prop('readonly', false);
        $("#editTxt").addClass("bg-light shadow");
        });
        $("#editCommentCancel").click(function(){

        $('#commentActionBtn').prop('hidden', true);
        $('#editComment').prop('hidden', false);
        $('#editTxt').prop('readonly', true);
        $('#editTxt').val($('#oldComment').val())
        $("#editTxt").removeClass("bg-light shadow");

        });

        // end of edit comment notes
        jQuery(".currently_vacant_submit").click(function(){
            var vacant_from_date = jQuery(".vacant_from_date").val();
            var vacant_to_date = jQuery(".vacant_to_date").val();            
            var clear_tenants_dom = jQuery(".clear_tenants_chk");	
            var clear_tenants = ( clear_tenants_dom.prop("checked") == true )?1:0;	

            if( vacant_from_date == '' && vacant_to_date == '' ){
                swal('Error','Vacant from and to date is required','error');
            }else{
                $('#preloader').show(); //show loader
                jQuery.ajax({
                    type: "POST",
                    url: '/properties/check_active_jobs',
                    dataType: 'json',
                    data: {
                        prop_id: <?php echo $this->uri->segment(3) ?>,
                        agency_id: <?php echo $this->session->agency_id ?>
                    }
                }).done(function( ret ){
                    $('#preloader').hide(); //hide loader
                    if(ret.status == 1){
                        swal({
                                title: "Warning!",
                                text: "Are you sure you want to mark this property as vacant?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-success",
                                confirmButtonText: "Yes",
                                cancelButtonClass: "btn-danger",
                                cancelButtonText: "No, Cancel!",
                                closeOnConfirm: false,
                                closeOnCancel: true,
                            },
                            function(isConfirm){

                                if(isConfirm){

                                    $('#preloader').show(); //show loader

                                    // continue via ajax request
                                    jQuery.ajax({
                                        type: "POST",
                                        url: '/properties/request_mark_as_vacant',
                                        data: {                                
                                            prop_id: <?php echo $this->uri->segment(3) ?>,
                                            vacant_from_date: vacant_from_date,
                                            vacant_to_date: vacant_to_date,
                                            clear_tenants: clear_tenants
                                        }
                                    }).done(function( ret ){

                                        $('#preloader').hide(); //hide loader

                                        swal({
                                            title:"Success!",
                                            text: "Property marked as vacant",
                                            type: "success",
                                            showCancelButton: false,
                                            confirmButtonText: "OK",
                                            closeOnConfirm: false,

                                        },function(isConfirm){

                                            if(isConfirm){

                                                swal.close();
                                                location.reload();

                                            }

                                        });                                                       

                                    });


                                }else{
                                    return false;
                                }

                            }

                        );
                    } else {
                        swal({
                            title: "Please note!",
                            text: "We only require vacant dates for an active job. If you require <?=$this->config->item('COMPANY_NAME_SHORT')?> to attend, please create a job or submit a workorder.",
                            type: "info",
                            confirmButtonClass: "btn-success"
                        });
                    }
                });
                // Ajax End
            }                    

            });


        // vacant toggle
		jQuery(".currently_vacant_toggle").click(function(){

            var vacant_toggle_dom = jQuery(this);
            var currently_vacant_div = vacant_toggle_dom.parents(".currently_vacant_div");
            var vacant_toggle_btn_inner_dom = vacant_toggle_dom.find(".btn_inline_text");
            var vacant_toggle_btn_txt = vacant_toggle_btn_inner_dom.text();
            var orig_btn_txt = 'Currently Vacant';			

            if( vacant_toggle_btn_txt == orig_btn_txt ){ // show

                vacant_toggle_btn_inner_dom.text("Cancel"); // update button text to cancel
                currently_vacant_div.find(".property_vacant").val(1);
                
                currently_vacant_div.find(".vacant_div").removeClass('d-none');
                currently_vacant_div.find(".vacant_div").addClass('d-inline-block');

            }else{

                vacant_toggle_btn_inner_dom.text(orig_btn_txt); // update button text to back to orig
                currently_vacant_div.find(".property_vacant").val(0);

                // clear field
                currently_vacant_div.find(".vacant_from_date").val('');
                currently_vacant_div.find(".vacant_to_date").val('');
                currently_vacant_div.find(".clear_tenants_chk").prop("checked",false);

                currently_vacant_div.find(".vacant_div").addClass('d-none');
                currently_vacant_div.find(".vacant_div").removeClass('d-inline-block');

            }


        });


        //success/error message sweel alert pop  start
        <?php if( $this->session->flashdata('status') &&  $this->session->flashdata('status') == 'success' ){?>
            swal({
                title: "Success!",
                text: "<?php echo $this->session->flashdata('success_msg') ?>",
                type: "success",
                confirmButtonClass: "btn-success"
            });
        <? }else if(  $this->session->flashdata('status') &&  $this->session->flashdata('status') == 'error'  ){ ?>
            swal({
                title: "Error!",
                text: "<?php echo $this->session->flashdata('error_msg') ?>",
                type: "error",
                confirmButtonClass: "btn-danger"
            });
        <?php } ?>
        //success/error message sweel alert pop  end


        //select2
        $(".select2-photo").not('.manual').select2({
            templateSelection: select2Photos,
            templateResult: select2Photos
        });
        function select2Photos (state) {
            if (!state.id) { return state.text; }
            var $state = $(
                '<span class="user-item"><img src="' + state.element.getAttribute('data-photo') + '"/>' + state.text + '</span>'
            );
            return $state;
        }


        //PM
        $('#prop_pm').change(function(){
            var thisVal = $(this).val();

            swal(
			{
				title: "",
				text: "Are you sure you want to update Property Manager?",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn-success",
				confirmButtonText: "Yes",
                cancelButtonClass: "btn-danger",
				cancelButtonText: "No, Cancel!",
				closeOnConfirm: false,
				closeOnCancel: true,
			},
			function(isConfirm){
				if(isConfirm){

					//$('#load-screen').show(); //show loader

					// continue via ajax request
					jQuery.ajax({
						type: "POST",
						url: '/properties/update_vpd_pm',
						dataType: 'json',
						data: {
							pm_id: thisVal,
                            prop_id: <?php echo $this->uri->segment(3) ?>,
							agency_id: <?php echo $this->session->agency_id ?>
						}
					}).done(function( ret ){

							//$('#load-screen').hide(); //hide loader

							if(ret.status){
							    swal.close();
                                $(".pm_check").show();
							}


                        });


                    }else{
                        return false;
                    }

                }

            );

        })



        // radion buttons custom toggle script
        jQuery('.btn-group label.btn').click(function(){
            jQuery(this).parent('.btn-group').find('label').removeClass('active').addClass('no_bg');
            jQuery(this).addClass('active').removeClass('no_bg');
        })


        // services events none tweak on document ready START
        <?php if($checkCurrentPropertyServices===true){ ?>
            var hidden_services_val = $('.hidden_servicesVal').val();
            var hidden_servicesVal_corded = $('.hidden_servicesVal_corded').val();
            var isbundle = $('.hidden_servicesVal').parents('.options_wrapper').find('.isbundle').val();

            if(isbundle==1){

                        /*var bundles_str = jQuery('.hidden_servicesVal').parents(".options_wrapper").find(".bundle_ids").val();
                        var bundles = bundles_str.split(","); */
                        var this_serv = jQuery('.hidden_servicesVal').parents(".options_wrapper").find(".ajt_id");
                        var excluded_bundle_ids = jQuery('.hidden_servicesVal').parents(".options_wrapper").find(".excluded_bundle_ids").val();
                        var excluded_bundle_ids_arr = excluded_bundle_ids.split(",");

                        for(var i=0;i<excluded_bundle_ids_arr.length;i++){
                            jQuery(".tbl_service .options_wrapper .ajt_id").not(this_serv).each(function(){
                                if(jQuery(this).val().indexOf(excluded_bundle_ids_arr[i])>=0){
                                    jQuery(this).parents(".options_wrapper").addClass('events_none');
									 jQuery(this).parents(".options_wrapper").find('.txt-info').html('');
                                }
                            });
                        }

            }else{
                $('input.no_selected_serv').each(function(){
                    var obj = $(this);

                    //if( hidden_servicesVal_corded){
                        var bndl = obj.parents(".options_wrapper").find(".excluded_bundle_ids").val();
                        if(bndl.indexOf(hidden_services_val)>=0){
                            obj.parents(".options_wrapper").addClass('events_none');
                            jQuery(this).parents(".options_wrapper").find('.txt-info').html('');

                        }

                        if(bndl.indexOf(hidden_servicesVal_corded)>=0){
                            obj.parents(".options_wrapper").addClass('events_none');
                            jQuery(this).parents(".options_wrapper").find('.txt-info').html('');

                        }

                        if($(this).val()==15){
                            obj.parents(".options_wrapper").addClass('events_none');
                            jQuery(this).parents(".options_wrapper").find('.txt-info').html('');
                        }
                    //}
            });
            }




       <?php } ?>
    // services events none tweak on document ready END


        jQuery(".serv_sats").change(function(){

            var this_serv = jQuery(this).parents(".options_wrapper").find(".ajt_id");

            jQuery(".tbl_service .options_wrapper .ajt_id").not(this_serv).each(function(){
                var excluded_bundle_ids = jQuery(this).parents(".options_wrapper").find(".excluded_bundle_ids").val();
                var excluded_bundle_ids_arr = excluded_bundle_ids.split(",");
                if( jQuery.inArray(this_serv.val(),excluded_bundle_ids_arr) !== -1 ){
                    jQuery(this).parents(".options_wrapper").fadeTo(300,0.3).addClass('events_none');
                }
            })

            // tick info message tweak
            $('.events_none').each(function(){
                $(this).find('.txt-info').html("");
                });
            jQuery('.services_tr').not('.events_none').each(function(i){
                var radioVal = $(this).find('input[type="radio"]:checked').val();

                var ajt = $(this).find('.ajt_id').val();
                    var label_type ="";
                    if(ajt ==6){
                        var label_type =  "corded windows";
                    }else{
                        var label_type = "smoke alarms";
                    }

                var radioMsg = "";
                switch (radioVal){
                    case "1": radioMsg = "<span class='txt-green'><span class='font-icon font-icon-warning'></span> This Property is serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?>.</span>"; break;
                    case "0": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner assumes full responsibility and will service the "+label_type+"."; break;
                    case "2": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner has not responded as to whom will service the "+label_type+"."; break;
                    case "3": radioMsg = "<span class='font-icon font-icon-warning red'></span> The property is serviced by an alternative smoke alarm provider than <?=$this->config->item('COMPANY_NAME_SHORT')?>.";
                };
                jQuery(this).find('.txt-info').html(radioMsg);
            });

        })


        // sats radio buttons
        /*jQuery(".serv_sats").change(function(){

        var isBundle = jQuery(this).parents(".options_wrapper").find(".isbundle").val();

        if(isBundle == 1){
            var bundles_str = jQuery(this).parents(".options_wrapper").find(".bundle_ids").val();
            var bundles = bundles_str.split(",");
            var this_serv = jQuery(this).parents(".options_wrapper").find(".services_id");

            for(var i=0;i<bundles.length;i++){
                jQuery(".tbl_service .options_wrapper .services_id").not(this_serv).each(function(){
                    if(jQuery(this).val().indexOf(bundles[i])>=0){
                        jQuery(this).parents(".options_wrapper").fadeTo(300,0.3).addClass('events_none');
                    }
                });
            }

            // tick info message tweak
            $('.events_none').each(function(){
                $(this).find('.txt-info').html("");
                });
            jQuery('.services_tr').not('.events_none').each(function(i){
                var radioVal = $(this).find('input[type="radio"]:checked').val();

				 var ajt = $(this).find('.ajt_id').val();
                       var label_type ="";
                       if(ajt ==6){
                           var label_type =  "corded windows";
                       }else{
                           var label_type = "smoke alarms";
                       }

                var radioMsg = "";
                switch (radioVal){
                    case "1": radioMsg = "<span class='txt-green'><span class='font-icon font-icon-warning'></span> This Property is serviced by SATS.</span>"; break;
                     case "0": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner assumes full responsibility and will service the "+label_type+"."; break;
                    case "2": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner has not responded as to whom will service the "+label_type+"."; break;
                    case "3": radioMsg = "<span class='font-icon font-icon-warning red'></span> The property is serviced by an alternative smoke alarm provider than SATS.";
                };
                jQuery(this).find('.txt-info').html(radioMsg);
            });

        }else{
                var ajt_id = jQuery(this).parents(".options_wrapper").find(".ajt_id").val();
                jQuery(".isbundle").each(function(){
                    if(jQuery(this).val()==1){
                        var bndl = jQuery(this).parents(".options_wrapper").find(".bundle_ids").val();
                        if(bndl.indexOf(ajt_id)>=0){
                            jQuery(this).parents(".options_wrapper").fadeTo(300,0.3).addClass('events_none');
                        }
                    }else{ //add tweak for Water Efficiency
                        if(!jQuery(this).parents(".options_wrapper").find(".serv_sats").prop("checked")){
                            if(jQuery(this).parents(".options_wrapper").find(".ajt_id").val() == 15){
                                jQuery(this).parents(".options_wrapper").fadeTo(300,0.3).addClass('events_none');
                            }
                        }

                    }

                });

                // tick info message tweak
                $('.events_none').each(function(){
                    $(this).find('.txt-info').html("");
                    });
                jQuery('.services_tr').not('.events_none').each(function(i){
                    var radioVal = $(this).find('input[type="radio"]:checked').val();

					 var ajt = $(this).find('.ajt_id').val();
                       var label_type ="";
                       if(ajt ==6){
                           var label_type =  "corded windows";
                       }else{
                           var label_type = "smoke alarms";
                       }

                    var radioMsg = "";
                    switch (radioVal){
                        case "1": radioMsg = "<span class='txt-green'><span class='font-icon font-icon-warning'></span> This Property is serviced by SATS.</span>"; break;
                         case "0": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner assumes full responsibility and will service the "+label_type+"."; break;
                        case "2": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner has not responded as to whom will service the "+label_type+"."; break;
                        case "3": radioMsg = "<span class='font-icon font-icon-warning red'></span> The property is serviced by an alternative smoke alarm provider than SATS.";
                    };
                    jQuery(this).find('.txt-info').html(radioMsg);
                });
            }

        }); */

        // not sats radio buttons
        /*jQuery(".serv_not_sats").change(function(){

            var isBundle = jQuery(this).parents(".options_wrapper").find(".isbundle").val();

            if(isBundle==1){

                var bundles_str = jQuery(this).parents(".options_wrapper").find(".bundle_ids").val();
                var bundles = bundles_str.split(",");
                var this_serv = jQuery(this).parents(".options_wrapper").find(".services_id");

                for(var i=0;i<bundles.length;i++){
                    jQuery(".tbl_service .options_wrapper .services_id").not(this_serv).each(function(){
                        if(jQuery(this).val().indexOf(bundles[i])>=0){
                            jQuery(this).parents(".options_wrapper").fadeTo(300,1).removeClass('events_none');
                        }
                    });
                }

                // tick info message tweak
                $('.events_none').each(function(){
                    $(this).find('.txt-info').html("");
                });
                jQuery('.services_tr').not('.events_none').each(function(i){
                       var radioVal = $(this).find('input[type="radio"]:checked').val();

					   var ajt = $(this).find('.ajt_id').val();
                       var label_type ="";
                       if(ajt ==6){
                           var label_type =  "corded windows";
                       }else{
                           var label_type = "smoke alarms";
                       }

                       var radioMsg = "";
                       switch (radioVal){
                            case "1": radioMsg = "<span class='txt-green'><span class='font-icon font-icon-warning'></span> This Property is serviced by SATS.</span>"; break;
                             case "0": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner assumes full responsibility and will service the "+label_type+"."; break;
                            case "2": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner has not responded as to whom will service the "+label_type+"."; break;
                            case "3": radioMsg = "<span class='font-icon font-icon-warning red'></span> The property is serviced by an alternative smoke alarm provider than SATS.";
                       };
                       jQuery(this).find('.txt-info').html(radioMsg);
                    });

            }else{

                var is_sats =false;
                var ajt_id = jQuery(this).parents(".options_wrapper").find(".ajt_id").val();

                jQuery(".isbundle").each(function(){
                    // is bundle
                    if(jQuery(this).val()==1){
                        var bndl = jQuery(this).parents(".options_wrapper").find(".bundle_ids").val();
                        jQuery(".non_bundle_tr .ajt_id").each(function(){
                            if(bndl.indexOf(jQuery(this).val())>=0){
                                if(jQuery(this).parents(".options_wrapper").find(".serv_sats").prop("checked")==true){
                                    is_sats = true;
                                }
                            }
                        });
                        // if no active service to sats
                        if(is_sats==false){
                            jQuery(this).parents(".options_wrapper").fadeTo(300,1).removeClass('events_none');
                        }
                    }else{ //add tweak for Water Efficiency
                        if( $(this).parents('.options_wrapper').find('.ajt_id').val() == 15 ){
                            jQuery(this).parents(".options_wrapper").fadeTo(300,1).removeClass('events_none');
                        }
                    }
                });

                // tick info message tweak
                $('.events_none').each(function(){
                    $(this).find('.txt-info').html("");
                });
                jQuery('.services_tr').not('.events_none').each(function(i){
                       var radioVal = $(this).find('input[type="radio"]:checked').val();

					    var ajt = $(this).find('.ajt_id').val();
                       var label_type ="";
                       if(ajt ==6){
                           var label_type =  "corded windows";
                       }else{
                           var label_type = "smoke alarms";
                       }


                       var radioMsg = "";
                       switch (radioVal){
                           case "1": radioMsg = "<span class='txt-green'><span class='font-icon font-icon-warning'></span> This Property is serviced by SATS.</span>"; break;
                            case "0": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner assumes full responsibility and will service the "+label_type+"."; break;
                           case "2": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner has not responded as to whom will service the "+label_type+"."; break;
                           case "3": radioMsg = "<span class='font-icon font-icon-warning red'></span> The property is serviced by an alternative smoke alarm provider than SATS.";
                       };
                       jQuery(this).find('.txt-info').html(radioMsg);
                    });

            }

            }); */

            jQuery(".serv_not_sats").change(function(){

                var is_sats =false;
                var ajt_id = jQuery(this).parents(".options_wrapper").find(".ajt_id").val();

                /*jQuery(".isbundle").each(function(){
                    // is bundle
                    if(jQuery(this).val()==1){
                        var bndl = jQuery(this).parents(".options_wrapper").find(".bundle_ids").val();
                        jQuery(".non_bundle_tr .ajt_id").each(function(){
                            if(bndl.indexOf(jQuery(this).val())>=0){
                                if(jQuery(this).parents(".options_wrapper").find(".serv_sats").prop("checked")==true){
                                    is_sats = true;
                                }
                            }
                        });
                        // if no active service to sats
                        if(is_sats==false){
                            jQuery(this).parents(".options_wrapper").fadeTo(300,1).removeClass('events_none');
                        }
                    }
                }); */

                jQuery(".isbundle").each(function(){
                    // is bundle
                    if(jQuery(this).val()==1){
                        var bndl = jQuery(this).parents(".options_wrapper").find(".bundle_ids").val();
                        jQuery(".non_bundle_tr .ajt_id").each(function(){
                            if(bndl.indexOf(jQuery(this).val())>=0){
                                if(jQuery(this).parents(".options_wrapper").find(".serv_sats").prop("checked")==true){
                                    is_sats = true;
                                }
                            }
                        });

                    }

                    // if no active service to sats
                    if(is_sats==false){
                            jQuery(this).parents(".options_wrapper").fadeTo(300,1).removeClass('events_none');
                        }
                });



                // tick info message tweak
                $('.events_none').each(function(){
                    $(this).find('.txt-info').html("");
                });
                jQuery('.services_tr').not('.events_none').each(function(i){
                    var radioVal = $(this).find('input[type="radio"]:checked').val();

                    var ajt = $(this).find('.ajt_id').val();
                    var label_type ="";
                    if(ajt ==6){
                        var label_type =  "corded windows";
                    }else{
                        var label_type = "smoke alarms";
                    }

                    var radioMsg = "";
                    switch (radioVal){
                        case "1": radioMsg = "<span class='txt-green'><span class='font-icon font-icon-warning'></span> This Property is serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?>.</span>"; break;
                            case "0": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner assumes full responsibility and will service the "+label_type+"."; break;
                        case "2": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner has not responded as to whom will service the "+label_type+"."; break;
                        case "3": radioMsg = "<span class='font-icon font-icon-warning red'></span> The property is serviced by an alternative smoke alarm provider than <?=$this->config->item('COMPANY_NAME_SHORT')?>.";
                    };
                    jQuery(this).find('.txt-info').html(radioMsg);
                });

            })


        // no longer manager toggle tweak
        $('#btn_no_longer_managed').on('click',function(e){
            e.preventDefault();
            var dataVal = $(this).data('val');
            var btnVal = $(this).html();
            $('#nlm_td_box_toggle').slideToggle(function(){
                if(btnVal=="No Longer Manage?"){
                    $('#btn_no_longer_managed').html('Cancel').attr('data-val',1);
                }else{
                    $('#btn_no_longer_managed').html('No Longer Manage?').attr('data-val',0);
                }
            });
        });

        // proceed - no longer managed (validation/insert via ajax)
         $('#nlm_form').validate({
				submit: {
					settings: {
						inputContainer: '.form-group',
						errorListClass: 'form-tooltip-error',
                        button: '#btn_no_longer_managed_go'
					},
                    callback: {
                        onBeforeSubmit: function(node){
                            //loader here
                        },
                        onSubmit: function(node,formData){
                            //console.log(formData);

                            var reason_they_left = jQuery("#reason_they_left").val();
                            var other_reason = jQuery("#other_reason").val();
                            var error = '';   

                            // validation
                            /*if( reason_they_left == '' ){
                                error += "'Reason They Left' is required\n";
                            }else{
                                if( reason_they_left == -1 && other_reason == '' ){
                                    error += "'Other Reason' is required\n";
                                }
                            } */

                            if( error != "" ){ // error

                                swal('', error, 'error'); 

                            }else{

                                swal({
                                    title: "",
                                    text: "Are you sure you want to mark this Property 'No Longer Managed'?",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonClass: "btn-success",
                                    confirmButtonText: "Yes, Proceed",
                                    cancelButtonClass: "btn-danger",
                                    cancelButtonText: "No, Cancel!",
                                    closeOnConfirm: false,
                                    closeOnCancel: true,
                                },function(isConfirm){

                                    if(isConfirm){
                                        jQuery.ajax({
                                        type: "POST",
                                        url: "<?php echo base_url('/properties/no_longer_managed/') ?>",
                                        dataType: 'json',
                                        data: {
                                            prop_id: <?php echo $this->uri->segment(3) ?>,
                                            agency_id: <?php echo $this->session->agency_id ?>,
                                            agent_nlm_from: $('input[name="nlm_from"]').val(),
                                            agent_nlm_reason: $('#nlm_reason').val(),
                                            reason_they_left: reason_they_left,
                                            other_reason: other_reason
                                        }
                                        }).done(function(data){
                                            if(data.status){
                                                swal({
                                                    title:"",
                                                    text: "Property succesfully updated",
                                                    type: "success",
                                                    showCancelButton: false,
                                                    confirmButtonText: "OK",
                                                    closeOnConfirm: false,

                                                },function(isConfirm){
                                                    window.location.href = "<?php echo base_url('/properties/');?>";
                                                });
                                            }else{
                                                swal('Server error','Please contact admin','error');
                                            }
                                        });
                                    }

                                })

                            }                             

                        }
                    }
				}
        });


        //update landlord toggle tweak
        $('.btn_update_landlord_toggle').on('click',function(e){

            e.preventDefault();
            $(this).parents('.editable_box').find('.editable_input').show();
            $(this).parents('.editable_box').find('.editable_text').hide();

        })
        //update landlord cancel tweak
        $('#btn_cancel_landlord').on('click',function(e){

            e.preventDefault();
            $(this).parents('.editable_box').find('.editable_text').show();
            $(this).parents('.editable_box').find('.editable_input').hide();

        })


        // Update Lanlord
        $('#update_landlord_form').validate({
            submit:{
                settings: {
						inputContainer: '.form-group',
						errorListClass: 'form-tooltip-error',
                        button: '#btn_update_landlord'
                },
                callback:{
                    onBeforeSubmit: function(node){
                            //loader here
                    },
                    onSubmit: function(node,formData){
                        swal({
                                title: "",
                                text: "Do you want to update landlord details?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-success",
                                confirmButtonText: "Yes, Update",
                                cancelButtonClass: "btn-danger",
                                cancelButtonText: "No, Cancel!",
                                closeOnConfirm: false,
                                closeOnCancel: true,
                            },
                            function(isConfirm){
                                if(isConfirm){
                                    jQuery.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('/properties/update_landlord/') ?>",
                                    dataType: 'json',
                                    data: {
                                        prop_id: <?php echo $this->uri->segment(3) ?>,
                                        agency_id: <?php echo $this->session->agency_id ?>,
                                        landlord_firstname: $('input[name="landlord_firstname"]').val(),
                                        landlord_lastname: $('input[name="landlord_lastname"]').val(),
                                        landlord_mob: $('input[name="landlord_mob"]').val(),
                                        landlord_ph: $('input[name="landlord_ph"]').val(),
                                        landlord_email: $('input[name="landlord_email"]').val()
                                    }
                                    }).done(function(data){
                                        if(data.status){
                                            swal({
                                                title:"",
                                                text: "Landlord succesfully updated",
                                                type: "success",
                                                showCancelButton: false,
                                                confirmButtonText: "OK",
                                                closeOnConfirm: false,

                                            },function(isConfirm){
                                                $('input[name="landlord_firstname"]').val(data.landlord_firstname);
                                                $('input[name="landlord_lastname"]').val(data.landlord_lastname);
                                                $('input[name="landlord_mob"]').val(data.landlord_mob);
                                                $('input[name="landlord_ph"]').val(data.landlord_ph);
                                                $('input[name="landlord_email"]').val(data.landlord_email);

                                                location.reload();
                                            });
                                        }else{
                                            swal('Error','Landlord details already updated','error');
                                        }
                                    });
                                }
                            })
                    }

                }

            }
        });

         // Update Comment
         $('#update_comment_form').validate({
            submit:{
                settings: {
						inputContainer: '.form-group',
						errorListClass: 'form-tooltip-error',
                        button: '#updateCommentBtn'
                },
                callback:{
                    onBeforeSubmit: function(node){
                            //loader here
                    },
                    onSubmit: function(node,formData){
                        swal({
                                title: "",
                                text: "Do you want to update comment details?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-success",
                                confirmButtonText: "Yes, Update",
                                cancelButtonClass: "btn-danger",
                                cancelButtonText: "No, Cancel!",
                                closeOnConfirm: false,
                                closeOnCancel: true,
                            },
                            function(isConfirm){
                                if(isConfirm){
                                    jQuery.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('/properties/update_comment/') ?>",
                                    dataType: 'json',
                                    data: {
                                        prop_id: <?php echo $this->uri->segment(3) ?>,
                                        agency_id: <?php echo $this->session->agency_id ?>,
                                        comments: $('#editTxt').val(),
                                    }
                                    }).done(function(data){
                                        if(data.status){
                                            swal({
                                                title:"",
                                                text: "Agents Property Notes succesfully updated",
                                                type: "success",
                                                showCancelButton: false,
                                                confirmButtonText: "OK",
                                                closeOnConfirm: false,

                                            },function(isConfirm){
                                                $('#editTxt').val(data.comments);

                                                location.reload();
                                            });
                                        }else{
                                            swal('Error','Landlord details already updated','error');
                                        }
                                    });
                                }
                            })
                    }

                }

            }
        });

        //end of edit comment notes



        // view services deatils toggle (to be removed)
        $('.btn-serv-view_det').on('click',function(e){
            e.preventDefault();
            var thisbtn = $(this);
            var btnVal = $(this).html();
            var parentBox = $(this).parents('.services_tr').next();

            $(parentBox).slideToggle(function(){
                if(btnVal=="View Details"){
                    $(thisbtn).html("Hide Details");
                }else{
                    $(thisbtn).html("View Details");
                }

            });
        });


        //udpate services
        function ajax_update_services(){
            jQuery.ajax({
                type: "POST",
                url: "/properties/ajax_check_property_jobs_status",
                dataType: 'json',
                data: {
                    property_id: <?php echo $this->uri->segment(3); ?>
            }
            }).done(function(ret){
                if(ret.status){
                        swal({
                            title: "",
                            text: "This property has an outstanding job. Please contact our office on <?php echo $agent_phone; ?>",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonText: "OK",
                            closeOnConfirm: false,
                            closeOnCancel: true,
                        },function(isConfirm2){
                            if(isConfirm2){
                                location.reload();
                            }
                        })
                        return false;
                }else{
                    $('form#vpd_update_services').submit();
                }
            });
        }

        $('#vpd_update_services_btn').on('click',function(e){
                e.preventDefault();

                var serv_sats_radio = $('.serv_sats:checked');
                var default_no_services = $('#default_no_services').val();

                //swal confirmation msg tweak
                if(default_no_services == 'Yes'){
                    if(serv_sats_radio.length>0){
                        var isSwal = true;
                        var swal_msg = "This will create a job due for service, do you want to proceed?";
                    }else{
                        var isSwal = false;
                        var swal_msg = "This will cancel all active jobs, are you sure you want to proceed?";
                    }
                }else{
                    if(serv_sats_radio.length>0){
                        var isSwal = true;
                        var swal_msg = "This will create a job due for service, do you want to proceed?";
                    }else{
                        var isSwal = true;
                        var swal_msg = "This will cancel all active jobs, are you sure you want to proceed?";
                    }

                }

                    if(isSwal===true){
                        swal(
                            {
                                title: "",
                                text: swal_msg,
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-success",
                                confirmButtonText: "Yes, Submit",
                                cancelButtonClass: "btn-danger",
                                cancelButtonText: "No, Cancel!",
                                closeOnConfirm: false,
                                closeOnCancel: true,
                            },
                            function(isConfirm){
                                if(isConfirm){
                                    ajax_update_services();
                                }
                            }
                        );
                    }else{
                        ajax_update_services();
                    }


        });


         //load tenants ajax box (via ajax)
         $('.tenants_ajax_box').load('/properties/tenants_ajax',{prop_id:<?php echo $prop_id ?>}, function(response, status, xhr){
            $('.loader_block_v2').hide();
            $('[data-toggle="tooltip"]').tooltip(); //init tooltip
            phone_mobile_mask(); //init phone/mobile mask
            mobile_validation(); //init mobile validation
            phone_validation(); //init phone validation
            add_validate_tenant(); //init new tenant validation

        });


        $('#key_number_input').change(function(){

            var obj = $(this);
            var keyNumber = obj.val();
            jQuery.ajax({
                type: "POST",
                url: "/properties/vpd_ajax_update_key_number",
                dataType: 'json',
                data: {
                    property_id: <?php echo $this->uri->segment(3); ?>,
                    key_number: keyNumber
                }
            }).done(function(ret){
                if(ret.res){
                    obj.parents("td:first").find(".key_number_check").show();
                }
            });

        })


        jQuery('#lockbox_code').change(function(){

            var obj = jQuery(this);
            var lockbox_code = obj.val();
            
            jQuery.ajax({
                type: "POST",
                url: "/properties/update_lockbox_code",
                data: {
                    property_id: <?php echo $this->uri->segment(3); ?>,
                    lockbox_code: lockbox_code
                }
            }).done(function(ret){

                obj.parents("td:first").find(".lockbox_code_check").show();

            });

        });


        // property code inline ajax update
        jQuery('#compass_index_num').change(function(){

            var dom = jQuery(this);
            var compass_index_num = dom.val();

            $('#load-screen').show();
            jQuery.ajax({
                type: "POST",
                url: "/properties/vpd_ajax_update_compass_index_num",
                dataType: 'json',
                data: {
                    property_id: <?php echo $this->uri->segment(3); ?>,
                    compass_index_num: compass_index_num
                }
            }).done(function(ret){

                $('#load-screen').hide();

                if(ret.res){
                    dom.parents("td:first").find(".compass_index_num_check").show();
                }
                
            });

        });


        // short term rental ajaxy update, copied from PM update
        jQuery('#holiday_rental').change(function(){
            
            var holiday_rental = jQuery(this).val();

            swal(
			{
				title: "",
				text: "Are you sure you want to update the Short Term Rental status?",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn-success",
				confirmButtonText: "Yes",
                cancelButtonClass: "btn-danger",
				cancelButtonText: "No, Cancel!",
				closeOnConfirm: false,
				closeOnCancel: true,
			},
			function(isConfirm){
				if(isConfirm){

					//$('#load-screen').show(); //show loader

					// continue via ajax request
					jQuery.ajax({
						type: "POST",
						url: '/properties/update_short_term_rental',
						dataType: 'json',
						data: {
							holiday_rental: holiday_rental,
                            prop_id: <?php echo $this->uri->segment(3) ?>,
							agency_id: <?php echo $this->session->agency_id ?>
						}
					}).done(function( ret ){

							//$('#load-screen').hide(); //hide loader

							if(ret.status){
							    swal.close();
                                jQuery(".holiday_rental_check").show();
							}


                        });


                    }else{
                        return false;
                    }

                }

            );

        });

        // copy to clipboard
        jQuery(".upgrade_quote_ctcb").click(function(){

            var upgrade_quote_ctcb_dom = jQuery(this); // curren button DOM
            var upgrade_quote_link_dom = upgrade_quote_ctcb_dom.find('.upgrade_quote_link'); // link to be copied DOM		
            var upgrade_quote_link = upgrade_quote_link_dom.val(); // link to be copied		

            copy_to_clipboard(upgrade_quote_link) 	

        });


        // change service hide/show toggle script
        jQuery(".change_service_btn").click(function(){    

            var change_service_btn_dom = jQuery(this);
            var parents_tr = change_service_btn_dom.parents("div.services_tr:first");

            parents_tr.find(".change_service_div").toggle();

        });


        /*
        // change service script
        jQuery(".change_service_save_btn").click(function(){

            var change_service_save_btn_dom = jQuery(this);
            var parents_tr = change_service_save_btn_dom.parents("div.services_tr:first");

            var from_service_type = parents_tr.find(".from_service_type").val();
            var to_service_type = parents_tr.find(".to_service_type").val();

            if( from_service_type > 0 && to_service_type > 0 ){

                swal({
                    title: "Warning!",
                    text: "Are you sure you want to update service type?",
                    type: "warning",						
                    showCancelButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Yes, Continue",
                    cancelButtonClass: "btn-danger",
                    cancelButtonText: "No, Cancel!",
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm) {

                    if (isConfirm) {							  
                        
                        $('#load-screen').show(); 
                        jQuery.ajax({
                            url: '/properties/ajax_update_service_type',
                            type: 'POST',
                            data: {
                                property_id: <?php echo $this->uri->segment(3); ?>,
                                agency_id: <?php echo $this->session->agency_id; ?>,
                                from_service_type: from_service_type,
                                to_service_type: to_service_type
                            }
                        }).done(function( ret ){
                            
                            $('#load-screen').hide(); 
                            window.location="/properties/property_detail/<?php echo $this->uri->segment(3); ?>";                    
                            
                        });						

                    }

                });                                        

            }

        });
        */

        // show/hide update button
        jQuery("#to_service_type").change(function(){

            var dp_dom = jQuery(this);
            var dp_val = dp_dom.val();

            var ic_upgrade_exlude = [2, 8, 9, 11, 16, 17, 18];

            var result = checkValue(dp_val, ic_upgrade_exlude);

            var is_ic_upgrade = <?php echo ($prop_det->ic_upgrade ? $prop_det->ic_upgrade: 0); ?>;
            var state = "<?php echo $prop_det->p_state; ?>";

            

            if(result == 1 && is_ic_upgrade == 1 && state == 'QLD'){
                jQuery("#change_service_save_btn").hide();
                $(".label_alert").removeClass("d-none");
            } else {
                $(".label_alert").addClass("d-none");
                jQuery("#change_service_save_btn").attr('disabled', false);
                if( dp_val > 0 ){
                    jQuery("#change_service_save_btn").show();
                }else{
                    jQuery("#change_service_save_btn").hide();
                }
            }

            console.log("Value: " + dp_val + " Result: " + result + " State: " + state + " Upgrade: " + is_ic_upgrade);
        });

        function checkValue(value, arr) {
            var status = 0;
            for (var i = 0; i < arr.length; i++) {
                var name = arr[i];
                if (name == value) {
                    status = 1;
                    break;
                }
            }
            return status;
        }


        // change service script
        jQuery("#change_service_save_btn").click(function(){

            var from_service_type = jQuery("#from_service_type").val();            
            var to_service_type = jQuery("#to_service_type").val();
            
    

            if( from_service_type > 0 && to_service_type > 0 ){

                swal({
                    title: "Warning!",
                    text: "Are you sure you want to update the service type?",
                    type: "warning",						
                    showCancelButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Yes, Continue",
                    cancelButtonClass: "btn-danger",
                    cancelButtonText: "No, Cancel!",
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm) {

                    if (isConfirm) {							  
                        
                        $('#load-screen').show(); 
                        jQuery.ajax({
                            url: '/properties/ajax_update_service_type',
                            type: 'POST',
                            data: {
                                property_id: <?php echo $this->uri->segment(3); ?>,
                                agency_id: <?php echo $this->session->agency_id; ?>,
                                from_service_type: from_service_type,
                                to_service_type: to_service_type
                            }
                        }).done(function( ret ){
                            
                            $('#load-screen').hide(); 
                            window.location="/properties/property_detail/<?php echo $this->uri->segment(3); ?>";                    
                            
                        });						

                    }

                });                                        

            }

        });


        // non-active services hide/show toggle script
        jQuery("#show_non_active_services").click(function(){

            jQuery("#non_active_service_div").toggle();

        });

        jQuery("#non_active_service_update_btn").click(function(){
		
            // loop through all non active services
            var non_active_ps_id_arr = [];
            var non_active_service_status_arr = [];
            jQuery(".non_active_ps_id").each(function(){

                var non_active_ps_id_dom = jQuery(this);
                var parents = non_active_ps_id_dom.parents("tr:first");
                var non_active_ps_id = non_active_ps_id_dom.val();

                var non_active_service_status = parents.find(".non_active_service_status:checked").val();

                if( non_active_ps_id > 0 ){
                    non_active_ps_id_arr.push(non_active_ps_id);
                    non_active_service_status_arr.push(non_active_service_status);
                }

            });	                       

            swal({
                title: "Warning!",
                text: "Are you sure you want to update service status of non-active services?",
                type: "warning",						
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Yes, Continue",
                cancelButtonClass: "btn-danger",
                cancelButtonText: "No, Cancel!",
                closeOnConfirm: true,
                showLoaderOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {

                if (isConfirm) {							  
                    
                    $('#load-screen').show(); 
                    jQuery.ajax({
                        url: '/properties/ajax_non_active_service_update',
                        type: 'POST',
                        data: {
                            property_id: <?php echo $this->uri->segment(3); ?>,                        
                            non_active_ps_id_arr: non_active_ps_id_arr,
                            non_active_service_status_arr: non_active_service_status_arr
                        }
                    }).done(function( ret ){
                                    
                        $('#load-screen').hide(); 
                        window.location="/properties/property_detail/<?php echo $this->uri->segment(3); ?>";  
                        
                    });					

                }

            });

        });



        // show/hide update button
        jQuery("#new_service_type").change(function(){

            var dp_dom = jQuery(this);
            var dp_val = dp_dom.val();

            if( dp_val > 0 ){
                jQuery("#service_to_sats_q1_div").show();
                jQuery("#add_new_service_type_submit_btn").show();
            }else{
                jQuery("#service_to_sats_q1_div").hide();
                //jQuery("#add_new_service_type_submit_btn").hide();
            }

        });


        // add new service type hide/show toggle script
        jQuery("#add_new_service_btn").click(function(){

            <?php
            if( $prop_det->p_state == 'QLD' ){  ?>

                jQuery("#add_new_service_type_div").show();
                jQuery("#service_to_sats_q1_div").show();

            <?php
            }else{ ?>

                jQuery("#add_new_service_type_div").toggle();

            <?php
            }
            ?>            

        });

        jQuery("#add_new_service_type_submit_btn").click(function(){

            var service_to_sats_q1_dp = jQuery(".service_to_sats_q1_dp:checked").val();                    
            var upgrage_to_ic = jQuery(".upgrage_to_ic:checked").val();
            var preferred_alarm = jQuery("#preferred_alarm").val();
            var state = "<?php echo $prop_det->p_state; ?>";
            var error = '';

            if( state == 'QLD' ){ 

                var service_to_sats_q1_dp = jQuery(".service_to_sats_q1_dp:checked").val();

                /*
                var qld_IC_service_type = jQuery("#qld_IC_service_type").val();
                var qld_regular_service_type = jQuery("#qld_regular_service_type").val();

                var new_service_type = ( service_to_sats_q1_dp == 1 )?qld_IC_service_type:qld_regular_service_type;
                */

                var new_service_type = jQuery(".qld_sel_serv_type:visible").val();

            }else{ // default

                var new_service_type = jQuery("#new_service_type").val();	  

            }
            
            if( new_service_type > 0 ){

                if( error != '' ){
                    swal('',error,'error');
                }else{

                    swal({
                        title: "Warning!",
                        text: "Are you sure you want to add new service type?",
                        type: "warning",						
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes, Continue",
                        cancelButtonClass: "btn-danger",
                        cancelButtonText: "No, Cancel!",
                        closeOnConfirm: true,
                        showLoaderOnConfirm: true,
                        closeOnCancel: true
                    },
                    function(isConfirm) {

                        if (isConfirm) {							  
                            
                            $('#load-screen').show(); 
                            jQuery.ajax({
                                url: '/properties/ajax_add_new_service_type',
                                type: 'POST',
                                data: {
                                    property_id: <?php echo $this->uri->segment(3); ?>,
                                    agency_id: <?php echo $this->session->agency_id; ?>,
                                    new_service_type: new_service_type,
                                    upgrage_to_ic: upgrage_to_ic,
                                    preferred_alarm: preferred_alarm,
                                    state: state
                                }
                            }).done(function( ret ){
                                                            
                                $('#load-screen').hide(); 
                                window.location="/properties/property_detail/<?php echo $this->uri->segment(3); ?>";           
                                
                            });					

                        }

                    });

                }                
                
            }             

        });


        jQuery(".change_or_add_new_service_fb_btn").click(function(){

            var button_dom = jQuery(this);
            var from_service_type = button_dom.attr("data-from_service_type");

            // load from service ID to lightbox
            jQuery("#from_service_type").val(from_service_type);

            // trigger lightbox
            $.fancybox.open({
                src  : '#changer_service_fb'
            });

        });


        jQuery(".change_or_add_btn").click(function(){

            var button_dom = jQuery(this);
            var button_text = button_dom.text();   
            var btn_class = button_dom.attr("data-btn_class");         
            
            // hide content
            jQuery(".change_or_add_content_div").css("display","none");

            // back to default
            jQuery(".change_or_add_btn").each(function(){

                var btn_dom =  jQuery(this);
                var btn_class2 = btn_dom.attr("data-btn_class");

                btn_dom.removeClass("btn-"+btn_class2);
                btn_dom.addClass("btn-"+btn_class2+"-outline");

            });

            // set button to active
            button_dom.removeClass("btn-"+btn_class+"-outline");
            button_dom.addClass("btn-"+btn_class);

            if( button_text == "Change Current Service" ){
                jQuery("#change_current_service_content_div").css("display","flex");
            }else if( button_text == "Disable Current Service" ){
                jQuery("#disable_current_service_content_div").css("display","flex");
            }else if( button_text == "Add a New Service" ){
                jQuery("#add_new_service_content_div").css("display","flex");
            }            

        });



        // NLM div hide/show toggle
        jQuery("#disable_current_service_dp").change(function(){

            var dp_dom = jQuery(this);
            var dp_val = dp_dom.val();
            var nlm_reason = jQuery("#disable_service_nlm_reason").val();

            if( dp_val > 0 ){

                if( dp_val == 3 ){ // NLM
                
                    jQuery("#disable_service_can_nlm_div").css("display","flex"); // show   
                    
                    if( nlm_reason != '' ){
                        jQuery("#disable_service_save_btn").show();
                    }else{
                        jQuery("#disable_service_save_btn").hide();
                    }
                    

                }else{ // other

                    jQuery("#disable_service_can_nlm_div").css("display","none"); // hide

                    jQuery("#disable_service_save_btn").show();

                }  

            }else{
                jQuery("#disable_service_save_btn").hide();
            }                      

        });        


        // only show update button if reason exist
        jQuery("#disable_service_nlm_reason").keyup(function(){

            var nlm_reason_dom = jQuery(this);
            var nlm_reason_val = nlm_reason_dom.val();

            if( nlm_reason_val != '' ){
                jQuery("#disable_service_save_btn").show();
            }else{
                jQuery("#disable_service_save_btn").hide();
            }

        });


        // change service script
        jQuery("#disable_service_save_btn").click(function(){

            var disable_current_service_dp = jQuery("#disable_current_service_dp").val();                    
            var current_service_type = jQuery("#from_service_type").val();                                 
            
            if( disable_current_service_dp > 0 ){

                swal({
                    title: "Warning!",
                    text: 'Are you sure you want to confirm that <?=$this->config->item('COMPANY_NAME_SHORT')?> will no longer service this property? Proceeding here will cancel all active jobs.',
                    type: "warning",						
                    showCancelButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Yes, Continue",
                    cancelButtonClass: "btn-danger",
                    cancelButtonText: "No, Cancel!",
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm) {

                    if (isConfirm) {	
                        
                        // DIY or Other Provider
                        if( disable_current_service_dp == 1 || disable_current_service_dp == 2 ){
                            
                            if( disable_current_service_dp == 1 ){
                                var update_service_status_to = 0; // DIY
                            }else if( disable_current_service_dp == 2 ){
                                var update_service_status_to = 3; // Other Provider
                            }

                            
                            $('#load-screen').show(); 
                            jQuery.ajax({
                                url: '/properties/ajax_update_service_type_status',
                                type: 'POST',
                                data: {
                                    property_id: <?php echo $this->uri->segment(3); ?>,
                                    agency_id: <?php echo $this->session->agency_id; ?>,
                                    update_service_status_to: update_service_status_to,
                                    current_service_type: current_service_type
                                }
                            }).done(function( ret ){
                                
                                $('#load-screen').hide(); 
                                window.location="/properties/property_detail/<?php echo $this->uri->segment(3); ?>";                    
                                
                            });		
                            

                        }else if( disable_current_service_dp == 3 ){ // NLM

                            var nlm_date = jQuery("#disable_service_nlm_date").val();
                            var nlm_reason = jQuery("#disable_service_nlm_reason").val();
                            var reason_they_left = jQuery("#reason_they_left2").val();
                            var other_reason = jQuery("#other_reason2").val();

                            // NLM
                            $('#load-screen').show(); 
                            jQuery.ajax({
                                url: '/properties/no_longer_managed',
                                type: 'POST',
                                data: {
                                    prop_id: <?php echo $this->uri->segment(3); ?>,
                                    agency_id: <?php echo $this->session->agency_id; ?>,
                                    agent_nlm_from: nlm_date,
                                    agent_nlm_reason: nlm_reason,
                                    reason_they_left: reason_they_left,
                                    other_reason: other_reason
                                }
                            }).done(function( ret ){
                                
                                $('#load-screen').hide(); 
                                //window.location="/properties/property_detail/<?php echo $this->uri->segment(3); ?>";    
                                
                                swal({
                                    title:"Success!",
                                    text: "Property Marked as No Longer Managed",
                                    type: "success",
                                    showCancelButton: false,
                                    confirmButtonText: "OK",
                                    closeOnConfirm: false,

                                },function(isConfirm){

                                    if(isConfirm){

                                        swal.close();
                                        window.location="/properties";    

                                    }

                                });
                                
                            });
                            

                        }                                                                    			

                    }

                });                                        

            }            

        });


        // Has this property been upgraded to Interconnected Smoke Alarms?
        // yes
        jQuery("#service_to_sats_q1_dp_yes").change(function(){

            jQuery("#service_types_div").show();
            jQuery("#qld_regular_service_type").hide();
            jQuery("#qld_IC_service_type").show();

            jQuery("#service_to_sats_q2_div").hide();
            jQuery("#add_new_service_type_submit_btn").show();

            jQuery("#service_to_sats_q3_div").hide();

        });

        // no
        jQuery("#service_to_sats_q1_dp_no").change(function(){

            jQuery("#service_types_div").hide();

            if( jQuery(".upgrage_to_ic:checked").val() == 1 ){
                jQuery("#qld_regular_service_type").hide();
                jQuery("#qld_IC_service_type").show();
            }else{
                jQuery("#qld_regular_service_type").show();
                jQuery("#qld_IC_service_type").hide();
            }            

            jQuery("#service_to_sats_q2_div").show();
            jQuery("#add_new_service_type_submit_btn").hide();

        });

        // Do you give permission for SATS to upgrade this property on attendance?
        // yes
        jQuery("#service_to_sats_q2_dp_yes").change(function(){

            jQuery("#service_types_div").show();
            jQuery("#qld_regular_service_type").hide();
            jQuery("#qld_IC_service_type").show();

            jQuery("#service_to_sats_q3_div").show();
            jQuery("#add_new_service_type_submit_btn").hide();

        });

        // no
        jQuery("#service_to_sats_q2_dp_no").change(function(){

            jQuery("#service_types_div").show();
            jQuery("#qld_regular_service_type").show();
            jQuery("#qld_IC_service_type").hide();

            jQuery("#service_to_sats_q3_div").hide();
            jQuery("#add_new_service_type_submit_btn").show();

        });

        // show/hide update button
        jQuery("#preferred_alarm").change(function(){

            var dom = jQuery(this);
            var preferred_alarm = dom.val();

            if( preferred_alarm > 0 ){
                jQuery("#add_new_service_type_submit_btn").show();
            }else{                
                jQuery("#add_new_service_type_submit_btn").hide();
            }

        });

        // reason show/hide script
        jQuery("#reason_they_left").change(function(){

            var reason_they_left_dom = jQuery(this);
            var nlm_div = reason_they_left_dom.parents(".nlm_box_fields");
            var reason_they_left =  reason_they_left_dom.find("option:checked").val();

            if( reason_they_left == -1 ){
                nlm_div.find(".other_reason_elem").show();
            }else{
                nlm_div.find(".other_reason_elem").hide();
            }            

        });

        // reason show/hide script
        jQuery("#reason_they_left2").change(function(){

            var reason_they_left_dom = jQuery(this);
            var nlm_div = reason_they_left_dom.parents("#disable_service_can_nlm_div");
            var reason_they_left =  reason_they_left_dom.find("option:checked").val();

            if( reason_they_left == -1 ){
                nlm_div.find("#other_reason2").show();
            }else{
                nlm_div.find("#other_reason2").hide();
            }            

        });

        
        /** MOVE PROPERTY START */

        //on load load alt agencies
        $('#sel_new_agency').load('/agency/ajax_get_agency_by_pm_including_alt',{pm_id:current_user_logged_in_id},function(response, status, xhr){
            if( status == 'success' ){
                console.log(status);
            }
            if( status == 'error' ){
                console.log(status);
            }
        });

        //new agency drodown on change
        $('#sel_new_agency').change(function(){
            var new_agency_id = $(this).val();
            var new_agency_name = $('#sel_new_agency option:selected').text();

            if(new_agency_id!=""){
                jQuery.ajax({
                    type: "POST",
                    url: '/properties/ajax_get_pm_by_agency_id',
                    data: {
                        agency_id: new_agency_id
                    }
                }).done(function( ret ) {
                    $('#sel_new_pm').html(ret);
                    $('#move_prop_manager_div').show();
                    $('.new_agency_name').html(new_agency_name);
                });
            }else{
                $('#move_prop_manager_div').hide();
            }

        })

        $('.btn_move_property').click(function(){
            var err = ""
            var sel_new_agency = $('#sel_new_agency').val();
            var new_agency_name = $('#sel_new_agency option:selected').text();
            var sel_new_pm = $('#sel_new_pm').val();
            var old_agecy_id = $('#old_agecy_id').val();

            if( sel_new_agency=="" ){
                err +="Please Select New Agency\n";
            }
            if( sel_new_pm=="" ){
                err +="Please Select Property Manager\n";
            }
            if(err!=""){
                swal('Error',err,'error');
                return false;
            }

            swal({
                title: "Warning!",
                text: "Are you sure you want to move this property to "+new_agency_name+"?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Yes",
                cancelButtonClass: "btn-danger",
                cancelButtonText: "No, Cancel!",
                closeOnConfirm: false,
                closeOnCancel: true,
            },
            function(isConfirm){

                if(isConfirm){

                    $('#preloader').show(); //show loader
                    jQuery.ajax({
                        type: "POST",
                        url: '/properties/ajax_moved_property',
                        data: {
                            agency_id: sel_new_agency,
                            old_agecy_id: old_agecy_id,
                            pm_id: sel_new_pm,
                            property_id: <?php echo $property_id; ?>
                        }
                    }).done(function( ret ) {
                        $('#preloader').hide(); //hide loader
                        $.fancybox.close();
                        swal({
                            title: "Success!",
                            text: "This property has been moved",
                            type: "success",
                            confirmButtonClass: "btn-success"
                        },function(){
                            window.location.href = "<?php echo  base_url('/properties'); ?>";
                        });
                    });


                }else{
                    return false;
                }

            });
           
        })


        /** MOVE PROPERTY END */

        

    //end document ready
    });






</script>
