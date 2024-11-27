<style type="text/css">
    .btn_add_property_next{
        background: #46c35f;
        color:#fff!important;
    }
    #btnAddProperty{
        display: block;
        border: none;
        width: 100%;
        height: 100%;
        border-radius: 0!Important;
        margin: 0;
        padding: 13px 0px;
        color: #fff !important;
    }
    .services_confirmation_box,
    #service_garage_div,
    .price_break_down{
        display: none;
    }
</style>
	<?php
	if( validation_errors() ){ ?>
		<div class="alert alert-danger">
		<?php echo validation_errors(); ?>
		</div>
	<?php
	}
	?>

<div class="box-typical-r box-typical-padding-r">
    <section class="box-typical steps-numeric-block col-lg-12s">

        <div class="steps-numeric-header">
            <div class="steps-numeric-header-in">
                <ul class="steps_ul">
                    <li class="step" id="step1">
                        <div class="item"><span class="num">1</span><span class="title-text">Property Address</span><span class="font-icon font-icon-ok step-icon-finish"></span></div>
                    </li>
                    <li class="step" id="step2">
                        <div class="item"><span class="num">2</span><span class="title-text">Services</span><span class="font-icon font-icon-ok step-icon-finish"></span></div>
                    </li>
                    <li class="step" id="step3">
                        <div class="item"><span class="num">3</span><span class="title-text">Additional Info</span><span class="font-icon font-icon-ok step-icon-finish"></span></div>
                    </li>
                    <li class="step" id="step4">
                        <div class="item"><span class="num">4</span><span class="title-text">Tenant Details</span><span class="font-icon font-icon-ok step-icon-finish"></span></div>
                    </li>
                    <li class="step" id="step5">
                        <div class="item"><span class="num">5</span><span class="title-text">Comments</span><span class="font-icon font-icon-ok step-icon-finish"></span></div>
                    </li>
                </ul>
            </div>
        </div>
        <?php echo form_open('properties/add',array('id'=>'add_property_form')); ?>
       <div id="loader_block" style="display:none;"> <div id="div_loader"></div></div>
        <div class="steps-numeric-inner">

            <!-- ---------  GROUP 1  --------->
            <div class="ptabs" id="group_1" style="display:block;">



                <header class="steps-numeric-title">Property Address</header>

                <div class="row">

                    <div class="col-sm-10 col-lg-6">
                    <div class="form-group">
                        <input id="fullAdd" type="text" class="form-control field_g1" id="inputPassword" placeholder="Type in the address: e.g. 500 George st" autocomplete="off">
                        <input type="hidden" name="prop_lat" id="prop_lat">
                        <input type="hidden" name="prop_lng" id="prop_lng">
                    </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="form-group ">
                                    <input data-validation="[NOTEMPTY]" data-validation-label="Street NO." id="address_1" name="address_1" type="text" class="form-control field_g1 requiredV2" autocomplete="off" placeholder="Street No. *">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group ">
                                    <input data-validation="[NOTEMPTY]" data-validation-label="Street Name"  id="address_2" name="address_2" type="text" class="form-control field_g1 requiredV2" autocomplete="off" placeholder="Street Name *">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group ">
                                    <input data-validation="[NOTEMPTY]" data-validation-label="Suburb"  id="address_3" name="address_3" type="text" class="form-control field_g1 requiredV2" autocomplete="off" placeholder="Suburb *">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group ">
                                    <?php if($this->session->country_id == 1){ ?>

                                        <select data-validation="[NOTEMPTY]" data-validation-label="State" id="state" name="state" class="form-control select2-arrow manual select2-no-search-arrow requiredV2">
                                        <option value="NSW">NSW</option>
                                        <option value="VIC">VIC</option>
                                        <option value="QLD">QLD</option>
                                        <option value="ACT">ACT</option>
                                        <option value="TAS">TAS</option>
                                        <option value="SA">SA</option>
                                        <option value="WA">WA</option>
                                        <option value="NT">NT</option>
                                        </select>

                                   <?php  }else{ ?>

                                    <input placeholder="Region" class="form-control" type="text" id="state" name="state" >

                                    <?php } ?>



                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group ">
                                    <input data-validation="[NOTEMPTY]" data-validation-label="Postcode"  id="postcode" name="postcode" type="text" class="form-control field_g1 requiredV2" autocomplete="off" placeholder="Postcode *">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!----------GROUP 2------------->
            <div class="ptabs" id="group_2" style="display:none;">

                <header class="steps-numeric-title">Services</header>

                <div class="row">
                    <div class="col-lg-12 col-md-12 tbl_service">

                        <?php
                            $index = 0;
                            foreach($agency_services as $row) {
                                $is_bundle = $row['is_bundle'];
                                $bundle_ids = $row['bundle_ids'];
                        ?>

                        <?php // if($row['service_id']!=12 && $row['service_id']!=13 && $row['service_id']!=14){ //exclude IC services > removed as of Oct 24 2022 as per Ben's request ?>

                        <div class="row options_wrapper services_tr <?php echo ($is_bundle==1)?'bundle_tr':'non_bundle_tr' ?> mb-3">

                        <div style="display:none;">
                            <input type="hidden" class="services_id" value="<?php echo ($is_bundle==1)?$bundle_ids:$row['id']; ?>" />
                            <input type="hidden" value="<?php echo $row['service_id']; ?>" name="alarm_job_type_id[]" class="ajt_id">
                            <input type="hidden" value="<?php echo $row['price']; ?>" name="price[]">
                            <input type="hidden" value="<?php echo $is_bundle; ?>" class="isbundle" name="isbundle[]">
                            <input type="hidden" value="<?php echo $bundle_ids; ?>" class="bundle_ids" name="bundle_ids[]">
                            <input type="hidden" value="<?php echo $row['excluded_bundle_ids']?>" name="excluded_bundle_ids" class="excluded_bundle_ids" >
						</div>
                            <div class="col-md-3">
                                <label><?php
                                /*
                                if($row['id']==14){
                                    echo "Bundle Smoke Alarm, Corded Window, Safety Switch (Interconnected) $".$row['price'];
                                }else if($row['id']==13){
                                    echo "Smoke Alarm & Safety Switch (Interconnected) $".$row['price'];
                                }else if($row['id']==12){
                                    echo "Smoke Alarms (Interconnected) $".$row['price'];
                                }else if($row['id']==9){
                                    echo "Bundle Smoke Alarm, Corded Window, Safety Switch $".$row['price'];
                                }else{
                                    echo $row['type']." $".$row['price'];
                                }
                                */

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
                                    'agency_id' => $this->session->agency_id,
                                    'new_line' => 1,
                                    'display_reason' => 1
                                );
                                $price_var_arr = $this->system_model->get_agency_price_variation($price_var_params);
                                $dynamic_price = ( $piea_sql->num_rows() > 0 )?$row['price']:$price_var_arr['dynamic_price_total'];

                                if($row['id']==14){
                                    echo "Bundle Smoke Alarm, Corded Window, Safety Switch (Interconnected) $".number_format($dynamic_price,2);
                                }else if($row['id']==13){
                                    echo "Smoke Alarm & Safety Switch (Interconnected) $".number_format($dynamic_price,2);
                                }else if($row['id']==12){
                                    echo "Smoke Alarms (Interconnected) $".number_format($dynamic_price,2);
                                }else if($row['id']==9){
                                    echo "Bundle Smoke Alarm, Corded Window, Safety Switch $".number_format($dynamic_price,2);
                                }else{
                                    echo $row['full_name']." $".number_format($dynamic_price,2) ;
                                }
                                ?>
                                <div class="price_break_down"><?php echo $price_var_arr['price_breakdown_text']; ?></div>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group services_chkbox_wrapper">
                                   <!-- <div class="btn-group" data-toggle="buttons">-->
                                    <div class="btn-group">
                                        <label class="btn">
                                            <input name="service<?php echo $index ?>" class="serv_sats css-checkbox service_status_radio serv_status<?php echo $index ?>" id="main1radio<?php echo $index ?>" value="1" type="radio"> <span class="uppercase"><?= $this->config->item('theme') ?></span> 
                                        </label>
                                        <label class="btn">
                                            <input name="service<?php echo $index ?>" class="serv_not_sats css-checkbox red-box service_status_radio serv_status<?php echo $index ?>" id="main1radio<?php echo $index ?>" value="0" type="radio"> DIY
                                        </label>
                                        <label class="btn active">
                                            <input name="service<?php echo $index ?>" class="serv_not_sats css-checkbox red-box service_status_radio serv_status<?php echo $index ?>" id="main1radio<?php echo $index ?>" checked="checked" value="2" type="radio"> No Response
                                        </label>
                                        <label class="btn">
                                            <input name="service<?php echo $index ?>" class="serv_not_sats css-checkbox red-box service_status_radio serv_status<?php echo $index ?>" id="main1radio<?php echo $index ?>" value="3" type="radio"> Other Provider
                                        </label>

                                    </div>
                                    <span class='txt-info'></span>
                                </div>
                            </div>
                        </div>

                        <?php $index++; 
                        //} 
                    } 
                    ?>

                    <input type="hidden" id="sats_info" name="sats_info" class="sats_info" value="0" />
                    </div>

                </div>

                <div>
                    <p>&nbsp;</p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-info service_explain">
                            <h2>Service Explanation</h2>
                            <p><strong class="uppercase"><?=$this->config->item('theme') ?></strong> – The Owner would like <span class="uppercase"><?=$this->config->item('theme') ?></span> to service the Smoke Alarms on their behalf
                                <br/>
                                <strong>DIY</strong> – The Owner has advised Your Agency they will service the Smoke Alarms themselves
                                <br/>
                                <strong>No Response</strong> – The Owner has not responded as to who will service the Smoke Alarms<br/>
                                <strong>Other Provider</strong> – The Owner has nominated another provider to service the Smoke Alarms</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info services_confirmation_box alert-icon">
                            <i class="font-icon txt-red font-icon-warning"></i>
                            This will create a property with an active service, would you like to proceed?<br/>
                            Click 'Next' to continue
                        </div>
                    </div>
                </div>

            </div>


            <!----------GROUP 3------------->
            <div class="ptabs" id="group_3" style="display:none">

                <div class="hr_div">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3"><label style="hover: pointer;">Is this a holiday rental<i class="fa fa-question-circle" title="Please note: A holiday rental is not for 6 month or 12 month tenancies only for short stays" style="cursor: pointer;"></i></label></div>
                                <div class="col-md-9">

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select disabled="" data-validation="[NOTEMPTY]" data-validation-label="Short Term Rental" name="holiday_rental" id="holiday_rental" class="form-control requiredV2">
                                                    <option value="">Please select</option>
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                                <span class="font-icon font-icon-ok check-input-ok"></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-2" id="service_garage_div">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3"><label>IF property has an attached garage that is NOT part of the tenancy please tick here</label></div>
                                <div class="col-md-9">

                                    <div class="row">
                                        <div class="col-md-2">

                                            <div class="checkbox">
                                                <input type="checkbox" id="service_garage" name="service_garage" value="1" />
                                                <label for="service_garage"></label>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!--------------------- Currently Vacant -->
                    <div class="row current_vacant" style="display: none;">
                        <div class="col-md-12">
                            <div class="row">

                                <div class="col-md-3"><label>Is the Property Currently Vacant?</label></div>
                                <div class="col-md-9">
                                        <div class="row">

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <select disabled="" data-validation="[NOTEMPTY]" data-validation-label="Property Currently Vacant" name="prop_vacant" id="prop_vacant" class="form-control">
                                                            <option value="">Please select</option>
                                                            <option value="0">No</option>
                                                            <option value="1">Yes</option>
                                                    </select>
                                                            <span class="font-icon font-icon-ok check-input-ok"></span>
                                                </div>
                                            </div>


                                             <div class="col-md-7">
                                                <div class="row vacant_from_to" style="display: none;">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <div class="input-group date flatpickr" data-wrap="true" >
                                                                <input disabled="" data-validation="[NOTEMPTY]" data-validation-label="Vacant From" data-input  type="text" class="form-control" name="vacant_from" id="datepicker_vacantF" placeholder="Vacant From" data-date-format="d-m-Y">
                                                                <span class="input-group-append" data-toggle >
                                                                        <span class="input-group-text"><i class="font-icon font-icon-calend"></i></span>
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">

                                                        <div class="form-group">

                                                            <div class="input-group date flatpickr" data-wrap="true">
                                                                <input disabled=""  data-input  type="text" class="form-control" name="vacant_to" id="datepicker_vacantT" placeholder="Vacant To" data-date-format="d-m-Y">
                                                                <span class="input-group-append" data-toggle>
                                                                            <span class="input-group-text"><i class="font-icon font-icon-calend"></i></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                    <!-------------------- NEW TENANCY -->
                    <div class="row new_tenancy" style="display: none;">
                        <div class="col-md-12">

                            <div class="row">

                                <div class="col-md-3"><label>Is this a New Tenancy?</label></div>
                                <div class="col-md-9">

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group ">
                                                <select disabled="" data-validation="[NOTEMPTY]" data-validation-label="New Tenancy"  name="is_new_tent" id="is_new_tent" class="form-control">
                                                    <option value="">Please select</option>
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                                <span class="font-icon font-icon-ok check-input-ok"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="row new_tenant_start" style="display: none;">
                                                <div class="col-md-9">
                                                    <div class="form-group>">
                                                        <div class="input-group date flatpickr" data-wrap="true">
                                                            <input disabled="" data-validation="[NOTEMPTY]" data-validation-label="New Tenancy Starts" data-input type="text" class="form-control" name="new_ten_start" id="new_ten_start" placeholder="New Tenancy Starts" data-date-format="d-m-Y">
                                                            <span class="input-group-append" data-toggle>
                                                                <span class="input-group-text"><i class="font-icon font-icon-calend"></i></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                    </div>

                </div>


                <div class="awawaw_the_awaw" style="display:none;">
                    <!----- MORE DETAILS ---->
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="row">
                                <div class="col-md-7"> <label>More Details</label></div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="form-group ">
                                        <input type="text" class="form-control" id="key_number" name="key_number" placeholder="Key #">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group ">
                                        <input type="text" class="form-control" id="workorder_num" name="workorder_num" placeholder="Work Order #">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group ">
                                        <input type="text" class="form-control" id="lockbox_code" name="lockbox_code" placeholder="Lockbox Code" />
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group ">
                                        <input type="text" class="form-control" id="alarm_code" name="alarm_code" placeholder="House Alarm Code">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!----- MORE DETAILS END ---->

                    <!-- Landlord end start -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-3"><label>Landlord (optional)</label></div>
                                <div class="col-lg-9">

                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="form-group ">
                                                <input type="text" class="form-control field_g2" name="landlord_firstname" id="landlord_firstname" placeholder="First Name">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group ">
                                                <input type="text" class="form-control field_g2" name="landlord_lastname" id="landlord_lastname" placeholder="Last Name">
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group ">
                                                <input type="text" class="form-control field_g2 tenant_mobile" name="landlord_mobile" id="landlord_mobile" placeholder="Mobile">
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group ">
                                                <input type="text" class="form-control field_g2 phone-with-code-area-mask-input" name="landlord_landline" id="landlord_landline" placeholder="Landline">
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group ">
                                                <input type="text" class="form-control field_g2" name="landlord_email" id="landlord_email" placeholder="Email Address">
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Landlord end -->


                </div>
            </div>

            <!---------- GROUP 4 ------------->
            <div class="ptabs row" id="group_4" style="display:none;">
                <div class="col-lg-12">
                    <header class="steps-numeric-title tenant-title">New Tenants</header>
                </div>
                <div class="card-block">
                <div id="tenants-block">
                    <div class="form-group row">
                                <div class="col-lg-2"><label class="bold">First Name</label></div>
                                <div class="col-lg-2"><label class="bold">Last Name</label></div>
                                <div class="col-lg-2"><label class="bold">Mobile</label> </div>
                                <div class="col-lg-2"><label class="bold">Phone</label></div>
                                <div class="col-lg-4"><label class="bold">Email Address</label></div>
                    </div>

                    <!-- Tenant 1 -->
                    <div class="row tenants-row">
                        <div class="col-sm-10 col-lg-12">
                            <div class="row tenants_div">
                                <div class="tenant_count tenantBox1"></div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="tenant_firstname1" name="tenant_firstname[]" placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="tenant_lastname1" name="tenant_lastname[]" placeholder="Last Name">
                                    </div>
                                </div>
                                    <div class="col-lg-2">
                                    <div class="form-group">
                                         <input type="text" class="form-control tenant_mobile" id="tenant_mob1" name="tenant_mob[]" >
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control phone-with-code-area-mask-input" id="tenant_ph1" name="tenant_ph[]" >
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="tenant_email1" name="tenant_email[]" placeholder="Email Address" >
                                    </div>
                                </div>
                                 <div class="col-lg-1">
                                  &nbsp;
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                    <button id="add_tenant_row" class="btn btn-inline btn-primary-outline" type="button"><apan class="glyphicon glyphicon-plus"></apan> Tenant</button>

                </div>
            </div>

            <!----------GROUP 5------------->
            <div class="ptabs row" id="group_5" style="display:none;">
                    <div class="col-lg-12">
                            <div class="row">
                                    <div class="col-lg-12">
                                        <header class="steps-numeric-title">Property Manager for this property</header>
                                        <div class="row">
                                            <div class="col-lg-3" id="pm_div">
                                                <div class="form-group">
                                                        <select disabled="" data-validation="[NOTEMPTY]" data-validation-label="Property Manager" id="pm" name="pm" class="form-control field_g2 select2-photo requiredV2">

                                                            <option value="">Please select</option>
                                                            <option value="0"  data-photo="<?php echo $this->config->item('photo_empty'); ?>">No PM Assigned</option>
                                                            <?php
                                                            foreach($property_manager_list as $row){?>
                                                                
                                                                <option <?php echo ( $this->session->aua_id == $row->agency_user_account_id )?'selected':'' ?> data-photo="<?php echo ($row->photo!="")?$user_photo_path.$row->photo:'/images/avatar-2-64.png' ?>" value="<?php echo $row->agency_user_account_id; ?>"><?php echo $row->fname." ".$row->lname ?></option>
                                                            <?php
                                                            }
                                                            ?>
                                                        </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 pt-2">
                                                <div class="checkbox">
                                                    <input class="no_pm_chk" name="no_pm_chk" type="checkbox" id="no_pm_chk" value="1" />
                                                    <label for="no_pm_chk">No Property Manager</label>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-1">
                                                <a target="_blank" href="/user_accounts/add" class="btn btn-inline btn-danger-outline"><span class="glyphicon glyphicon-plus"></span> Property Manager</a>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <div class="row">
                                <p>&nbsp;</p>
                                <div class="col-lg-12">
                                    <header class="steps-numeric-title">Comments</header>
                                </div>
                                <div class="form-group col-lg-7">
                                    <textarea class="form-control" rows="5" id="job_comments" name="job_comments" placeholder="Property or Job Comments"></textarea>
                                </div>
                            </div>
                    </div>
            </div>
        </div>
        <div class="tbl steps-numeric-footer">
            <div class="tbl-row">
                <a onclick="nextPrev(-1)" id="prevBtn" href="javascript:void(0);" class="tbl-cell">← PREVIOUS</a>
                <a id="nextBtn" href="javascript:void(0);" class="tbl-cell btn_add_property_next">NEXT→</a>
                <input class="btn btn-inline btn-success color-green" style="display:none;" type="submit" name="btnAddProperty" id="btnAddProperty" value="ADD PROPERTY">
            </div>
        </div>
        </form>
    </section>
</div>


<!-- LOAD SCRIPTS HERE -->
<script type="text/javascript" src="/inc/js/lib/moment/moment-with-locales.min.js"></script>
<script type="text/javascript" src="/inc/js/lib/flatpickr/flatpickr.min.js"></script>
<script src="/inc/js/lib/clockpicker/bootstrap-clockpicker.min.js"></script>
<script src="/inc/js/lib/clockpicker/bootstrap-clockpicker-init.js"></script>
<script src="/inc/js/lib/daterangepicker/daterangepicker.js"></script>
<script src="/inc/js/lib/jquery-tag-editor/jquery.caret.min.js"></script>
<script src="/inc/js/lib/jquery-tag-editor/jquery.tag-editor.min.js"></script>
<script src="/inc/js/lib/input-mask/jquery.mask.min.js"></script>
<script src="/inc/js/lib/prism/prism.js"></script>


<script type="text/javascript">

jQuery('document').ready(function(){


         //success/error message sweel alert pop  start
         <?php if( $this->session->flashdata('status') &&  $this->session->flashdata('status') == 'success' ){?>
            swal({
                title: "Success!",
                text: "<?php echo $this->session->flashdata('success_msg') ?>",
                type: "success",
                confirmButtonClass: "btn-success"
            });
        <?php }else if(  $this->session->flashdata('status') &&  $this->session->flashdata('status') == 'error'  ){ ?>
            swal({
                title: "Error!",
                text: "<?php echo $this->session->flashdata('error_msg') ?>",
                type: "error",
                confirmButtonClass: "btn-danger"
            });
        <?php } ?>
        //success/error message sweel alert pop  end


        // delete error from full address on focus
        $("#fullAdd").focus(function(){
            $(this).next('.form-tooltip-error').remove();
        })

        // radion buttons custom toggle script
        jQuery('.btn-group label.btn').click(function(){
            jQuery(this).parent('.btn-group').find('label').removeClass('active')
            jQuery(this).addClass('active');
        })

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


        // sats radio buttons
        jQuery(".serv_sats").change(function(){

               /* var isBundle = jQuery(this).parents(".options_wrapper").find(".isbundle").val();

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
                           case "1": radioMsg = "<span class='txt-green'><span class='font-icon font-icon-warning'></span> The Property is to be serviced by SATS.</span>"; break;
                           case "0": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner assumes full responsibility and will service the "+label_type+"."; break;
                           case "2": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner has not responded as to whom will service the "+label_type+"."; break;
                           case "3": radioMsg = "<span class='font-icon font-icon-warning red'></span> The property is serviced by an alternative smoke alarm provider than SATS.";
                       };
                       jQuery(this).find('.txt-info').html(radioMsg);
                       show_hide_confirmation_box();
                    });

                }else{
                    var ajt_id = jQuery(this).parents(".options_wrapper").find(".ajt_id").val();
                    jQuery(".isbundle").each(function(){
                        if(jQuery(this).val()==1){
                            var bndl = jQuery(this).parents(".options_wrapper").find(".bundle_ids").val();
                            if(bndl.indexOf(ajt_id)>=0){
                                jQuery(this).parents(".options_wrapper").fadeTo(300,0.3).addClass('events_none');
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
                            case "1": radioMsg = "<span class='txt-green'><span class='font-icon font-icon-warning'></span> The Property is to be serviced by SATS.</span>"; break;
                            case "0": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner assumes full responsibility and will service the "+label_type+"."; break;
                            case "2": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner has not responded as to whom will service the "+label_type+"."; break;
                            case "3": radioMsg = "<span class='font-icon font-icon-warning red'></span> The property is serviced by an alternative smoke alarm provider than SATS.";
                       };
                       jQuery(this).find('.txt-info').html(radioMsg);
                       show_hide_confirmation_box();
                    });

                } */

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
                        case "1": radioMsg = "<span class='txt-green'><span class='font-icon font-icon-warning'></span> This Property is serviced by <span class='uppercase'><?=$this->config->item('theme') ?></span>.</span>"; break;
                        case "0": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner assumes full responsibility and will service the "+label_type+"."; break;
                        case "2": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner has not responded as to whom will service the "+label_type+"."; break;
                        case "3": radioMsg = "<span class='font-icon font-icon-warning red'></span> The property is serviced by an alternative smoke alarm provider than <span class='uppercase'><?=$this->config->item('theme') ?></span>.";
                    };
                    jQuery(this).find('.txt-info').html(radioMsg);
                });

         });

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
                            case "1": radioMsg = "<span class='txt-green'><span class='font-icon font-icon-warning'></span> The Property is to be serviced by SATS.</span>"; break;
                            case "0": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner assumes full responsibility and will service the "+label_type+"."; break;
                            case "2": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner has not responded as to whom will service the "+label_type+"."; break;
                            case "3": radioMsg = "<span class='font-icon font-icon-warning red'></span> The property is serviced by an alternative smoke alarm provider than SATS.";
                       };
                       jQuery(this).find('.txt-info').html(radioMsg);
                       show_hide_confirmation_box();
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
                           case "1": radioMsg = "<span class='txt-green'><span class='font-icon font-icon-warning'></span> The Property is to be serviced by SATS.</span>"; break;
                           case "0": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner assumes full responsibility and will service the "+label_type+"."; break;
                           case "2": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner has not responded as to whom will service the "+label_type+"."; break;
                           case "3": radioMsg = "<span class='font-icon font-icon-warning red'></span> The property is serviced by an alternative smoke alarm provider than SATS.";
                       };
                       jQuery(this).find('.txt-info').html(radioMsg);
                       show_hide_confirmation_box();
                    });

            }

            });*/

             jQuery(".serv_not_sats").change(function(){
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
                        case "1": radioMsg = "<span class='txt-green'><span class='font-icon font-icon-warning'></span> This Property is serviced by <span class='uppercase'><?=$this->config->item('theme') ?></span>.</span>"; break;
                            case "0": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner assumes full responsibility and will service the "+label_type+"."; break;
                        case "2": radioMsg = "<span class='font-icon font-icon-warning red'></span> The owner has not responded as to whom will service the "+label_type+"."; break;
                        case "3": radioMsg = "<span class='font-icon font-icon-warning red'></span> The property is serviced by an alternative smoke alarm provider than <span class='uppercase'><?=$this->config->item('theme') ?></span>.";
                    };
                    jQuery(this).find('.txt-info').html(radioMsg);
                });

             })

    // show hide SATS info container tweak
    jQuery(".serv_sats").change(function(){
            jQuery(".sats_info").val(1);
    });

    // show hide SATS info container tweak
    jQuery(".service_status_radio").change(function(){

        var dom = jQuery(this);
        var service_status = dom.val();
        var parent_row = dom.parents("div.services_tr:first");

        if( service_status == 1 ){ // SATS
            parent_row.find('.price_break_down').show();
        }else{
            parent_row.find('.price_break_down').hide();
        }

    });

    jQuery(".serv_not_sats").change(function(){
        if( jQuery(".serv_sats:checked").length==0 ){
            jQuery(".sats_info").val(0);
        }
    });

    jQuery('#is_new_tent').change(function(){
        if(jQuery(this).val() == 0){
            jQuery('.tenant-title').text('Current Tenants');
        }else{
            jQuery('.tenant-title').text('New Tenants');
        }

        show_gherx_awaw();
    })

    jQuery('#prop_vacant').change(function(){
        if(jQuery(this).val() == 1){
            jQuery('.tenant-title').text('New Tenants');
        }else{
            jQuery('.tenant-title').text('Current Tenants');
        }
        show_gherx_awaw();
    })


    // ADDITIONAL INFORMATION TWEAK HERE>>>>>
    //Short Term Rental tweak
    jQuery('#holiday_rental').change(function(){
        var thisVal = jQuery(this).val();
        if(thisVal == '0' && thisVal != ""){
            jQuery('.current_vacant').show().find('#prop_vacant').removeAttr('disabled');
            jQuery(this).next('.check-input-ok').show();

           // $('.awawaw_the_awaw').show();
        }else{
            if(thisVal==""){
                jQuery(this).next('.check-input-ok').hide();
               // $('.awawaw_the_awaw').hide();
            }else{
                jQuery(this).next('.check-input-ok').show();
            }
            jQuery('.current_vacant').hide().find("#prop_vacant").attr('disabled',"").val("");
            jQuery('.vacant_from_to').hide().find(".flatpickr-input").attr('disabled','').val("");

            jQuery('.new_tenancy').hide().find("#is_new_tent").attr('disabled',"").val("");

           //hide tick for vacant and new tenancy
           $('#prop_vacant').next('.check-input-ok').hide();
           $('#is_new_tent').next('.check-input-ok').hide();

        }
        show_gherx_awaw();
    })
    //currently vacant
     jQuery('#prop_vacant').change(function(){
        var thisVal = jQuery(this).val();
        if(thisVal == '1'){
            jQuery('.vacant_from_to').show().find(".flatpickr-input").removeAttr('disabled');
            jQuery('.new_tenancy').hide().find("#is_new_tent").attr('disabled','').val("");
            jQuery('.new_tenancy').find("#new_ten_start").attr('disabled','').val("");
            jQuery(this).next('.check-input-ok').show();
        }else if(thisVal == '0'){
            jQuery('.vacant_from_to').hide().find(".flatpickr-input").attr('disabled','').val("");
            jQuery('.new_tenancy').show().find("#is_new_tent").removeAttr('disabled');
            jQuery(this).next('.check-input-ok').show();
        }else if(thisVal == ""){
           jQuery(this).next('.check-input-ok').hide();
           jQuery('.vacant_from_to').hide().find(".flatpickr-input").attr('disabled','').val("");
           jQuery('.new_tenancy').hide().find("#is_new_tent").attr('disabled','').val("");
           jQuery('.new_tenancy').find("#new_ten_start").attr('disabled','').val("");
        }
    })
    //new tenancy dropdown tweak
    jQuery('#is_new_tent').change(function(){
        var thisVal = jQuery(this).val();
        if(thisVal == '1'){
            jQuery('.new_tenant_start').show().find("#new_ten_start").removeAttr('disabled');
            jQuery(this).next('.check-input-ok').show();
        }else if(thisVal == '0'){
            jQuery('.new_tenant_start').hide().find("#new_ten_start").attr('disabled',"");
            jQuery(this).next('.check-input-ok').show();
        }else{
            jQuery(this).next('.check-input-ok').hide();
        }
    })


    // add tenant buttons
    jQuery('#add_tenant_row').on('click',function(e){

       e.preventDefault();
       /*
        var rowLength = $('#tenants-block').find('.tenants-row:visible');
        $('#tenants-block').find('.tenants-row:visible').next('.tenants-row:hidden').show();
       if(rowLength.length == 3){
           $('#add_tenant_row').hide();
       }*/
       var htm_content = '<div class="row tenants-row">'+
                        '<div class="col-sm-10 col-lg-12">'+
                        '<div class="row tenants_div">'+
                        '<div class="col-lg-2"><div class="form-group"><input type="text" class="form-control" name="tenant_firstname[]" placeholder="First Name"></div></div>'+
                        '<div class="col-lg-2"><div class="form-group"><input type="text" class="form-control"  name="tenant_lastname[]" placeholder="Last Name" ></div></div>'+
                        '<div class="col-lg-2"><div class="form-group"><input type="text" class="form-control tenant_mobile"  name="tenant_mob[]" ></div></div>'+
                        '<div class="col-lg-2"><div class="form-group"><input type="text" class="form-control phone-with-code-area-mask-input"  name="tenant_ph[]" ></div></div>'+
                        '<div class="col-lg-3"><div class="form-group"><input type="text" class="form-control"  name="tenant_email[]" placeholder="Email Address"></div></div>'+
                        '<div class="col-lg-1"> <a data-toggle="tooltip" title="Remove" class="del_tenant_row" href="#"><span class="font-icon font-icon-trash"></span></a> </div>'+
                        '</div>'+
                        '</div>'+
                        '</div>';
        $('#tenants-block').append(htm_content);
        phone_mobile_mask();
        mobile_validation();
        phone_validation();
    });



    // DELETE tenants row
    jQuery(document).on('click','.del_tenant_row',function(e){

        e.preventDefault();
       var obj = $(this);
       obj.parents('.tenants-row').remove();

    });


    //  validation script.......
    var options = {
        submit: {
            settings: {
                form: '#add_property_form',
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',
                button: '#nextBtn',
            },
            callback:{
                onSubmit: function(node,formData){
                    nextPrev(1);
                    //console.log(formData);
                }
            }

        }
    }
    $.validate(options);

    //trigger for add property button
    $('#add_property_form').validate({
                    submit: {
                        settings: {
                            inputContainer: '.form-group',
                            errorListClass: 'form-tooltip-error'
                        }
                    }
    });
    // validation script end.......


    jQuery("#holiday_rental").change(function(){

        var holiday_rental_dom = jQuery(this);
        var holiday_rental = holiday_rental_dom.val();

        // only show on NSW
        if( jQuery("#state").val() == 'NSW' ){

            if( holiday_rental == 1 ){
                jQuery("#service_garage_div").show();
            }else{
                jQuery("#service_garage").prop("checked",false);
                jQuery("#service_garage_div").hide();
            }

        }        

    });


});

</script>


<script type="text/javascript">

    // google map autocomplete
    var placeSearch, autocomplete;

    // test
    var componentForm2 = {
        route: {
            'type': 'long_name',
            'field': 'address_2'
        },
        administrative_area_level_1: {
            'type': 'short_name',
            'field': 'state'
        },
        postal_code: {
            'type': 'short_name',
            'field': 'postcode'
        }
    };

    function initAutocomplete() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.

        <?php if( $this->session->country_id ==1 ){ ?>
            var cntry = 'au';
        <?php }else{ ?>
            var cntry = 'nz';
        <?php } ?>


        var options = {
            types: ['geocode'],
            componentRestrictions: {
                country: cntry
            }
        };

        var input = document.getElementById('fullAdd');

        autocomplete = new google.maps.places.Autocomplete(input, options);

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);

    }


    // [START region_fillform]
    function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        var lat = place.geometry.location.lat(),
            lng = place.geometry.location.lng();

        // test
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm2[addressType]) {
                var val = place.address_components[i][componentForm2[addressType].type];
                document.getElementById(componentForm2[addressType].field).value = val;
            }
        }

        //lat lng
        jQuery("#prop_lat").val(lat);
        jQuery("#prop_lng").val(lng);

        // street name
        var ac = jQuery("#fullAdd").val();
        var ac2 = ac.split(" ");
        var street_number = ac2[0];
        //	console.log(street_number);
        jQuery("#address_1").val(street_number);

        // suburb
        jQuery("#address_3").val(place.vicinity);

        // duplicate property tweak
        var complete_address = '';
        var street_num = jQuery("#address_1").val();
        var street_name = jQuery("#address_2").val();
        var suburb = jQuery("#address_3").val();
        var state = jQuery("#state").val();
        var postcode = jQuery("#postcode").val();
        var dup_msg = '';
        var allow_add = 0;

        complete_address = street_num+' '+street_name+' '+suburb+' '+state+' '+postcode;

       jQuery.ajax({
            type: "POST",
            url: "/properties/check_property_duplicate",
            data: {complete_address:complete_address},
            dataType: 'json'
	    }).done(function(data){
            if(data.match == 1){
                if(data.agency_id == <?php echo $this->session->agency_id ?>){
                   // swal("Error","This property already exists with your agency. <a>View Property Here</a>","error");
				   $('#fullAdd').parent('.form-group').append('<div class="form-tooltip-error" data-error-list=""><ul><li>This property already exist with this agency.</li></ul></div>');
                //    $('#fullAdd').parent('.form-group').append('<div class="form-tooltip-error" data-error-list=""><ul><li>This property is marked as "No Longer Managed", please contact us to reactivate it. <a target="_blank" class="txt_underline" href="/properties/property_detail/'+data.property_id+'">View Property Here</a></li></ul></div>');
                    jQuery('#address_1').val("");
                    jQuery('#address_2').val("");
                    jQuery('#address_3').val("");
                    jQuery('#state').val("");
                    jQuery('#postcode').val("");
                }else{
                    //swal("Error","This property already exist with another agency","error");
                    $('#fullAdd').parent('.form-group').append('<div class="form-tooltip-error" data-error-list=""><ul><li>This property already exist with another agency</li></ul></div>');
                    jQuery('#address_1').val("");
                    jQuery('#address_2').val("");
                    jQuery('#address_3').val("");
                    jQuery('#state').val("");
                    jQuery('#postcode').val("");
                }
            }

        });


    }

</script>

<script type="text/javascript">


    var swalErrorConfig = {
        title: "Error!",
        text: "Fields cannot be empty"
    }
    var swalSuccessConfig = {
        title: "Good job!",
        text: "Add function is still in progress",
        type: "success",
        confirmButtonClass: "btn-success",
        confirmButtonText: "Success"
    }


    var currentTab = 0; // Current tab is set to be the first tab (0)
    showTab(currentTab); // Display the crurrent tab


    function showTab(n) {

        var satsVal = document.getElementById('sats_info').value;
        var hr = document.getElementById('holiday_rental').value;

        // This function will display the specified tab of the form...
        var x = document.getElementsByClassName("ptabs");
        x[n].style.display = "block";

        //... and fix the Previous/Next buttons:
        if (n == 0) {
            // document.getElementById("prevBtn").style.display = "none";
            $('#prevBtn').css('pointer-events','none');
        } else {

           $('#prevBtn').css('pointer-events','');
        }

        if (n == (x.length - 1)) {
            //end of step code here
            document.getElementById("nextBtn").style.display = "none";
            document.getElementById("btnAddProperty").style.display = 'block';

        } else {
            document.getElementById("nextBtn").setAttribute('style','display:block');
            document.getElementById("btnAddProperty").style.display = 'none';
        }

        // sats no response tweak
         if(n == 2 && satsVal == 0){
             // go directly to step 5 escape 3 and 4
            document.getElementById("nextBtn").style.display = 'none';
            document.getElementById("btnAddProperty").style.display = 'block';
            document.getElementById('group_5').style.display = 'block';
            document.getElementById('group_3').style.display = 'none';
            document.getElementById('group_4').style.display = 'none';

            // removed current tab disabled inputs for jquery validations
            $('#group_5').find('.requiredV2').removeAttr('disabled');
         }else{
            document.getElementById('group_5').style.display = 'none';

            // add disabled attribute when back button press to exclude for current tab validations
            $('#group_5').find('.requiredV2').attr('disabled',"");
         }

         // sats response yes
         if(n == 3 && hr == 1){
             //go diretly to step 5
            document.getElementById("nextBtn").style.display = "none";
            document.getElementById("btnAddProperty").style.display = 'block';
            document.getElementById('group_5').style.display = 'block';
            document.getElementById('group_4').style.display = 'none';

            //removed disabled input attr for validations
            $('#group_5').find('.requiredV2').removeAttr('disabled');

        }else if(n==4 && hr == 0){
            document.getElementById('group_5').style.display = 'block';
            //removed disabled input attr for validations
            $('#group_5').find('.requiredV2').removeAttr('disabled');
        }

        // enable disable tweak each tab for validation purposes
        if(n == 1){
            $('#group_2').find('.requiredV2').attr('disabled',"");
            $('#group_3').find('.requiredV2').attr('disabled',"");
            $('#group_4').find('.requiredV2').attr('disabled',"");
        }

        if(n == 2 && satsVal != 0){
            $('#group_3').find('.requiredV2').removeAttr('disabled');
        }

        //... and run a function that will display the correct step indicator:
        fixStepIndicator(n)

    }


    function nextPrev(n) {
        var satsVal = document.getElementById('sats_info').value;
        var hr = document.getElementById('holiday_rental').value;
        // sats value
        var satsVal = document.getElementById('sats_info').value;
        // This function will figure out which tab to display
        var x = document.getElementsByClassName("ptabs");
        // Exit the function if any field in the current tab is invalid:

        if (n == 1 && !validateForm2(currentTab)) return false;

        // Hide the current tab:
        x[currentTab].style.display = "none";
        // Increase or decrease the current tab by 1:
        currentTab = currentTab + n;
        // if you have reached the end of the form...
        if (currentTab >= x.length) {
            // ... the form gets submitted:
            //form submit code here
            return false;
        }

        // loader tweak
        jQuery('#loader_block').show();
        setTimeout(function(){
            jQuery('#loader_block').hide();
            showTab(currentTab);
        }, 300);

    }

     function validateForm2(n) {
        var satsVal = document.getElementById('sats_info').value;
        var hr = document.getElementById('holiday_rental').value;
        // This function deals with validation of the form fields
        var x, y, i, o, valid = true;
        x = document.getElementsByClassName("ptabs");
        y = x[currentTab].getElementsByClassName("requiredV2");

        for (i = 0; i < y.length; i++) {
            // If a field is empty...
            if (y[i].value == "") {
                // add an "invalid" class to the field:
                y[i].className += " invalid";
                  //  alert('empty');
               // swal(swalErrorConfig);
                // and set the current valid status to false
                valid = false;
            }
        }

        // If the valid status is true, mark the step as finished and valid:
        if (valid) {

            if(n == 1 && satsVal ==0){
                document.getElementsByClassName("step")[2].className += " finish";
                document.getElementsByClassName("step")[3].className += " finish";
                ///alert(n);
            }

            if(n == 2 && hr == 1 && satsVal == 1){
               document.getElementsByClassName("step")[3].className += " finish";
            }

            document.getElementsByClassName("step")[currentTab].className += " finish";

        }

        return valid; // return the valid status
    }

    function validateForm() {
        // This function deals with validation of the form fields
        var x, y, i, o, valid = true;
        x = document.getElementsByClassName("ptabs");
        y = x[currentTab].getElementsByTagName("input");
        o = x[currentTab].getElementsByTagName("select");
        // A loop that checks every input field in the current tab:
        for (i = 0; i < y.length; i++) {
            // If a field is empty...
            if (y[i].value == "") {
                // add an "invalid" class to the field:
                y[i].className += " invalid";
                //  alert('empty');
                // swal(swalErrorConfig);
                // and set the current valid status to false
                valid = false;
            }
        }
        // If the valid status is true, mark the step as finished and valid:
        if (valid) {
            document.getElementsByClassName("step")[currentTab].className += "finish";
        }
        return valid; // return the valid status
    }

    function fixStepIndicator(n) {
        var satsVal = document.getElementById('sats_info').value;
        var hr = document.getElementById('holiday_rental').value;
        // This function removes the "active" class of all steps...
        var i, x = document.getElementsByClassName("step");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }

        if(n == 2 && satsVal == 0){
            //end index become 2
            x[4].className += " active";
        }else if(n == 3 && hr == 1){
            //index become 2
            x[4].className += " active";
        }else{
            x[n].className += " active";
        }

        //remove finish class
        if(n==1){
            jQuery('.steps_ul li:nth-child(3)').removeClass('finish');
            jQuery('.steps_ul li:nth-child(4)').removeClass('finish');
        }else if(n == 0){
            jQuery('.steps_ul li:nth-child(2)').removeClass('finish');
        }

        if(n==2 && satsVal ==1){
            jQuery('.steps_ul li:nth-child(4)').removeClass('finish');
        }

        //... and adds the "active" class on the current step:
       // x[n].className += " active";

    }

    function show_gherx_awaw(){

        var a = $('#holiday_rental').val();
        var b = $('#prop_vacant').val();
        var c = $('#is_new_tent').val();

        if(a==1){
            $('.awawaw_the_awaw').show();
        }else if(a==0 && b==1){
            $('.awawaw_the_awaw').show();
        }else if(a!="" && b!="" && c!=""){
            $('.awawaw_the_awaw').show();
        }else{
            $('.awawaw_the_awaw').hide();
        }

    }

    function show_hide_confirmation_box(){
             if( $('.serv_sats:checked').length >0 ){
                 $('.services_confirmation_box').show();
             }else{
                $('.services_confirmation_box').hide();
             }
    }


</script>

<?php
    if($this->session->country_id == 1){ //AU
        $satsApi = 'AIzaSyAUHcKVPXD_kJQyPCC-bvTNEPsxC8LAUmA';
    }else{ //NZ
        $satsApi = 'AIzaSyBSCcImRAb-7OggYHpIhuHuFeLujZwscAo';
    }
?>
<script>
jQuery(document).ready(function(){

    jQuery('#no_pm_chk').click(function(){

        var no_pm_chk_dom = jQuery(this);

        if( no_pm_chk_dom.prop('checked') == true ){
            jQuery("#pm_div").hide();
        }else{
            jQuery("#pm_div").show();
        }        
        
    });

});
</script>
