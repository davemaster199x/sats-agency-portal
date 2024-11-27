<style>
.icon_check{
    color:#46c35f;
}
.icon_cross{
    color:#fa424a;
}
span.fa{
    font-size:20px;
}
.fancy_div{
    width: 800px;
}
.fancy_list{
    margin-left:17px;
}
.fancy_list li{
    list-style: none;
    margin-bottom:10px;
}
.fancy_list li.tick{

}
.fancy_list li.un_tick{

}
.fancy_tickbox{
    margin-top: 15px;
}
.fancy_create_job_box{
    margin-top: 15px;
    padding-bottom:20px;
}
.fancy_list li span{
    width:20px;
    margin-left: -23px;
}

#search{
    width:450px;
    border:1px solid #00a8ff;
}
#create_job_search_btn, .search_span {
    color:white !important;
    background-color:#00a8ff;
    border: 1px solid #00a8ff;
}
#create_job_search_btn{
    border-top-right-radius: .25rem;
    border-bottom-right-radius: .25rem;
}
.font-12{
    font-size:12px;
}
.checkbox{
    float:left;
    margin-right:9px;
}
.checkbox label{
    padding-left:20px!important;
}
.chk_no_box label::before, .chk_no_box label::after{
    color:#fa424a;
    border-color: #fa424a!important;
}
.chk_yes_box label::before, .chk_yes_box label::after{
    color:#46c35f;
    border-color: #46c35f!important;
}
.no_border,.no_border tr, .no_border td{
    border:0px!important;
    padding:0px;
}
</style>
<section class="box-typical box-typical-padding">

	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/reports">Reports</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/compliance/nsw_inspection_details"><?php echo $title; ?></a></li>
	  </ol>
	</nav>

    <!-- EXPORT START -->
    <h5 class="m-t-lg with-border"><?php echo $title; ?>
        <?php if($btn_search){ ?>
		<div class="float-right">
			<div class="col-sm-12">
			    <section class="proj-page-section">
				    <div class="proj-page-attach">
                        <i class="font-icon font-icon-pdf"></i>
                        <p class="name"><?php echo $title; ?></p>
                        <p>
                            <a href="/compliance/nsw_inspection_details?pdf=1&output_type=I&pm_id=<?php echo $this->input->get_post('pm_id'); ?>&search=<?php echo $this->input->get_post('search'); ?>"target="blank">
                                View
                            </a>

                            <a href="/compliance/nsw_inspection_details?pdf=1&output_type=D&pm_id=<?php echo $this->input->get_post('pm_id'); ?>&search=<?php echo $this->input->get_post('search'); ?>">
                                Download
                            </a>
                        </p>
				    </div>
			    </section>
		    </div>
        </div>
        <?php } ?>
    </h5>
    <!-- EXPORT END -->

        <!-- Header -->
        <header class="box-typical-header">
            <div class="box-typical box-typical-padding">

                <?php
				$form_attr = array(
					'id' => 'jform'
				);
				echo form_open("/compliance/nsw_inspection_details",$form_attr);
				?>
                    <div class="form-groupss row">
						<!--<div class="float-left">
							<label for="exampleSelect" class="col-sm-12 form-control-label">Property Manager</label>
							<div class="col-sm-12" style="width:250px;">
								<select name="pm_id" class="form-control field_g2 select2-photo">
									<option value="">---</option>
									<?php
                                        foreach($pm->result() as $row){
                                            if($row->properties_model_id_new!=""){
                                                $sel = ($row->properties_model_id_new==$this->input->get_post('pm_id')) ? 'selected="true"' : NULL;
                                    ?>
                                            <option <?php echo $sel; ?> value='<?php echo $row->properties_model_id_new ?>'><?php echo "{$row->fname} {$row->lname}" ?></option>
                                    <?php
                                            }
                                        }
									?>
								</select>
							</div>
						</div> -->
                        <div class="float-left">
                         <label class="col-sm-12 form-control-label">Search the property here to find information required for your Ingoing Condition Report</label>
                            <div class="col-sm-12">
                          <!--  <input type="text" name="search" class="form-control" placeholder="Text" value="<?php echo $this->input->get_post('search'); ?>" /> -->

                            <div class="form-group">
                                <div class="input-group">
                                    <input name="search" type="text" id="search" class="form-control" placeholder="Search Property" value="<?php echo $this->input->get_post('search'); ?>">
                                    <div class="input-group-append">
                                        <button type="submit" name="btn_search" value="Search" id="create_job_search_btn">
                                        <span class="input-group-text search_span">
                                            <span class="glyphicon glyphicon-search"></span>
                                        </span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            </div>
                        </div>
                       <!-- <div class="float-left">
                            <label class="col-sm-12 form-control-label">&nbsp;</label>
                            <div class="col-sm-12">
								<input type="submit" class="btn btn-inline" id="create_job_search_btn" name="btn_search" value="Search">
                            </div>
                        </div> -->
                    </div>
                </form>
            </div>
        </header>

        <!-- list -->
        <div class="box-typical-body">

            <div class="table-responsive">
                <?php
                    $search_field = $this->input->get("search");
                    if( $btn_search || isset($search_field) ){
                    ?>
                <table class="table table-hover main-table">
                    <thead>
                        <tr>
                            <th>Address</th>
                            <th>Property Manager</th>
                            <th>Service Type</th>
                           <!-- <th class="txt-center">Serviced in Last 12 months</th>
                            <th>Last Battery Change </th>
                            <th>Next Scheduled </th> -->
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($propertyServices as $row){

                            //last service
                            $last_service = $row->last_service;

                            $tt_na = "";
                            $tt_yes="";
                            $tt_no="";
                            if($last_service!=""){
                                $now = date('Y-m');
                                $last_service_date = date('Y-m', strtotime($last_service->date));
                                $last_12_month = date('Y-m', strtotime($now . " -12 month"));
                                //$tt = (strtotime($last_service_date)>=strtotime($last_12_month)) ? '<span class="fa fa-check txt-green"></span>' :'<span class="fa fa-close txt-red"></span>';

                                if(strtotime($last_service_date)>=strtotime($last_12_month)){
                                    $tt_yes = 'checked';
                                    $tt_yes_check_a = 'chk_yes_box';
                                }else{
                                    $tt_no = 'checked';
                                    $tt_yes_check_b = 'chk_no_box';
                                }
                                

                                $tt_na = NULL;
                            }else{
                                //$tt = "No inspection performed";
                                $tt_na = 'checked';
                            }

                            //next sched
                            $next_schedule = $row->next_schedule;
                            $last_sched_date = $next_schedule->date;
                            $next_sched_date_plus_monts = date('Y-m-d', strtotime($last_sched_date . " +12 month"));
                            if($next_schedule->status == "Booked"){
                                $nex_sced_date = date('d/m/Y', strtotime($next_schedule->date));
                            }else if($next_schedule->status =="Completed" && $next_schedule->job_type == "Yearly Maintenance"){
                                $nex_sced_date = date('M, Y', strtotime($next_sched_date_plus_monts));
                            }else{
                                $nex_sced_date = "<em class='txt-red'>Booking in Progress</em>";
                            }

                            $last_bat_changed = (!empty($last_service->date)) ? date('d/m/Y', strtotime($last_service->date)) : 'N/A';

                            ?>
                            <tr>
                                <td><?php echo "<a href='/properties/property_detail/".$row->property_id."'>" .$row->p_address_1." ".$row->p_address_2.", ".$row->p_address_3."&nbsp;".$row->p_state."&nbsp;".$row->p_postcode. "</a>"  ?></td>
                                <td>
                                    <?php
                                    if( isset($row->properties_model_id_new) && $row->properties_model_id_new != 0 ){
                                        echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
                                        echo "{$row->properties_model_fname} {$row->properties_model_lname}";
                                    }
                                    ?>
                                </td>
                                <td>
	                                <?= Alarm_job_type_model::icons($row->ajt_id); ?>
                                </td>
                                <td>
                                    <a data-fancybox href="javascript:;" data-src="#fancy<?php echo $row->property_id ?>" class="btn btn-sm btn-success">Show Details</a>

                                    <div class="fancy_div" style="display: none;" id="fancy<?php echo $row->property_id ?>">
                                        <?php
                                            $item3 = ($last_service->date!="") ? date('d/m/Y', strtotime($last_service->date)) : 'No inspection performed' ;

                                            #if(!empty($row->last_service)){
                                            $chk_yes = "";
                                            $chk_no = "";
                                            if( $last_service->date!="" ){
                                                $chk_yes = 'checked';
                                                $check_class_a = 'chk_yes_box';
                                            }else{
                                                $chk_no = 'checked';
                                                $check_class_b = 'chk_no_box';
                                            }
                                        ?>
                                        <div><p>The landlord must indicate the following:</p></div>

                                        <table class="table-bordered table table-hover main-table table_aw">
                                            <tr>
                                                <td>1.</td>
                                                <td>Have smoke alarms been installed in the residential premises in accordance with the Environmental Planning and Assessment Act 1979 (including any regulations made under that Act)?</td>
                                                <td class="txt-center" style="width:180px;">
                                                    <div class="checkbox <?php echo $check_class_a; ?>">
                                                        <input <?php echo $chk_yes; ?> class="chk_yes" type="checkbox" readonly="readonly" id="check_y_<?php echo $row->property_id ?>" >
                                                        <label for="check_y_<?php echo $row->property_id ?>">Yes</label>
                                                    </div>
                                                    <div class="checkbox <?php echo $check_class_b; ?>">
                                                        <input <?php echo $chk_no; ?> class="chk_no" type="checkbox" readonly="readonly" id="check_n_<?php echo $row->property_id ?>">
                                                        <label for="check_n_<?php echo $row->property_id ?>">No</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2.</td>
                                                <td>
                                                    <table class="no_border">
                                                        <tr>
                                                            <td>Have all the smoke alarms installed on the residential premises been checked and found to be in working order?</td>
                                                            
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Date last checked</strong></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td>
                                                <table class="no_border">
                                                        <tr>
                                                            <td>
                                                                <div class="checkbox <?php echo $check_class_a; ?>">
                                                                    <input <?php echo $chk_yes; ?> class="chk_yes" type="checkbox" readonly="readonly" >
                                                                    <label for="check-1">Yes</label>
                                                                </div>
                                                                <div class="checkbox  <?php echo $check_class_b; ?>">
                                                                    <input <?php echo $chk_no; ?> class="chk_no" type="checkbox" readonly="readonly">
                                                                    <label for="check-1">No</label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $item3; ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <!--<td class="txt-center"><?php echo $tt; ?></td>-->
                                                <td>3.</td>
                                                <td>
                                                    <table class="no_border">
                                                        <tr>
                                                            <td>Have the removable batteries in all the smoke alarms been replaced within the last 12 months, except for removable lithium batteries?</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Date batteries were last changed</strong></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td>
                                                    <table class="no_border">
                                                        <tr>
                                                        <td>
                                                    <div class="checkbox <?php echo $tt_yes_check_a; ?>">
                                                        <input <?php echo $tt_yes ?> class="chk_yes" type="checkbox" readonly="readonly" > 
                                                        <label for="check-1">Yes</label>
                                                    </div>
                                                    <div class="checkbox <?php echo $tt_yes_check_b; ?>">
                                                        <input <?php echo $tt_no ?> class="chk_no" type="checkbox" readonly="readonly">
                                                        <label for="check-1">No</label>
                                                    </div>
                                                    <div class="checkbox">
                                                    <input <?php echo $tt_na ?> class="chk_na" type="checkbox" readonly="readonly">
                                                        <label for="check-1">N/A</label>
                                                    </div>
                                                </td>
                                                        </tr>
                                                        <tr>   <td><?php echo $item3; ?></td></tr>
                                                    </table>
                                                </td>
                                               
                                              
                                            </tr>
                                          
                                            <tr>
                                                <!--<td class="txt-center">N/A</td>-->
                                                <td>4.</td>
                                                <td>Have the batteries in all the smoke alarms that have a removable lithium battery been replaced in the period specified by the manufacturer of the smoke alarm?</td>
                                                <td>
                                                     <div class="checkbox chk_yes_box">
                                                        <input class="chk_yes" type="checkbox" readonly="readonly" >
                                                        <label for="check-1">Yes</label>
                                                    </div>
                                                    <div class="checkbox chk_no_box">
                                                        <input class="chk_no" type="checkbox" readonly="readonly">
                                                        <label for="check-1">No</label>
                                                    </div>
                                                    <div class="checkbox">
                                                        <input checked="checked" class="chk_na" type="checkbox" readonly="readonly">
                                                        <label for="check-1">N/A</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>Next Scheduled Inspection</td>
                                                <td class="txt-center"><?php echo $nex_sced_date; ?></td>
                                            </tr>
                                            <tr>
                                                <!--<td>If the Strata Schemes Management Act 2015 applies to the residential premises, is the owners corporation of the strata scheme responsible for the repair and replacement of smoke alarms in the residential premises?</td>
                                                <td>See Below</td>
                                                -->
                                                <td colspan="3">Note: Section 64A of the Residential Tenancies Act 2010 provided that repairs to a smoke alarm (which includeds a heat alarm) includeds maintenance of a smoke alarm in working order by installing or replacing a battery in the smoke alarm.</td>
                                            </tr>
                                        </table>



                                        <!-- ALARM DETAILS TABLE -->
                                        <?php

                                            $alarm_det = $row->alarm_details;
                                        ?>
                                        <table class="table-bordered table table-hover main-table">
                                            <thead>
                                                <tr>
                                                    <th>Alarm Location</th>
                                                    <th>Alarm Power</th>
                                                    <th>Battery Type</th>
                                                    <th>Battery Replaceable?</th>
                                                </tr>
                                                <tbody>
                                                    <?php
                                                        if( !empty($alarm_det) ){
                                                        foreach($alarm_det as $row_alarm_det){
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $row_alarm_det->ts_position ?></td>
                                                        <td class="text-center"><?php echo $row_alarm_det->alarm_pwr_source ?></td>
                                                        <td class="text-center"><?php echo $row_alarm_det->battery_type ?></td>
                                                        <td class="text-center">
                                                            <?php
                                                                if($row_alarm_det->is_replaceable!=NULL){
                                                                    if($row_alarm_det->is_replaceable==1){
                                                                        echo "Yes";
                                                                    }else{
                                                                        echo "No";
                                                                    }
                                                                }else{
                                                                    echo "&nbsp;";
                                                                }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <?php }}else{
                                                        echo "<tr><td colspan='4'>No Alarm Data on File</td></tr>";
                                                    } ?>
                                                </tbody>
                                            </thead>
                                        </table>
                                        <!-- ALARM DETAILS TABLE END -->

                                        <div class="font-12">
                                            <p><em>We would recommend to answer “No” to this question and have <?=$this->config->item('COMPANY_NAME_SHORT')?> attend the property unless the property has a serviced fire panel (Newer, multi level buildings often have these) and in the case of a fire panel in a building <?=$this->config->item('COMPANY_NAME_SHORT')?> will advise you and you will not be charged for the visit.</em></p>
                                            <p><em>To answer “Yes’ you would need to seek instruction from each individual strata scheme and in the event where the Strata Scheme takes responsibility (very unlikely) you will not be able to answer the questions required in the New ingoing condition report as Strata will not share this information readily OR you may have to hold up a lease waiting for them to respond.</em></p>
                                        </div>

                                        <div class="fancy_create_job_box">
                                        <button data-ajt_id="<?php echo $row->ajt_id ?>" data-propid="<?php echo $row->property_id ?>" data-last_job_id="<?php echo $next_schedule->id ?>" data-last_service_job_id="<?php echo $last_service->id ?>" type="button" class="btn btn_create_job">Create Job</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                    <?php
                    }
                    ?>
            </div>

            <?php if($btn_search){ ?>
            <nav aria-label="Page navigation example" style="text-align:center">

              <?php echo $pagi_links_non_sats; ?>

            </nav>
            <div class="pagi_count"><?php echo $pagi_count; ?></div>
            <?php } ?>

        </div>
        <!--.box-typical-body-->


</section>
    <!--.box-typical-->

<script>
jQuery(document).ready(function(){

    $('.btn_create_job').click(function(e){
        e.preventDefault();
        var prop_id = $(this).attr('data-propid');
        var ajt_id = $(this).attr('data-ajt_id');

        swal(
            {
                title: "",
                text: 'Are you sure you want to create a job?',
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
                        url: "<?php echo base_url('/compliance/ajax_compliance_create_job') ?>",
                        dataType: 'json',
                        data: {
                            prop_id: prop_id,
                            ajt_id: ajt_id
                        }
                        }).done(function(data){
                            if(data.status){
                                swal({
                                    title:"Success!",
                                    text: "Job Successfully Created",
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
                            }else{
                                if(data.err_msg != ""){
                                    swal('',data.err_msg,'error');
                                }
                            }
                        });
                }

            }
        );
    })

})
</script>
