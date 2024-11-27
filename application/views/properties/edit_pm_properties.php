<section class="box-typical box-typical-padding">

<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/agency/profile">Agency Profile</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/properties/edit_pm_properties"><?php echo $title; ?></a></li>
	  </ol>
	</nav>

	<h5 class="m-t-lg with-border">
	
		<?php echo $title; ?>	
	
	</h5>

    <header class="box-typical-header">
		<div class="box-typical box-typical-padding">
		<?php
		$form_attr = array(
			'id' => 'jform'
		);
		echo form_open('properties/edit_pm_properties',$form_attr);
		?>
			<div class="form-groupsss row">
                
			<div class="col-md-8 columns">
                <div class="row">
				<div class="col-md-3 columns">
					<label for="exampleSelect" class="form-control-label">Property Manager</label>
					
						<select name="pm_id" class="form-control field_g2 select2-photo">
							<option value="">---</option>
                            <option <?php  echo ( $this->input->get_post('pm_id') == '0' )?'selected="selected"':''; ?> value="0" data-photo="<?php echo $this->config->item('photo_empty'); ?>">No PM assigned</option>
							<?php
							foreach( $property_manager_list as $row ){ ?>
								<option data-photo="<?php echo $this->jcclass->displayUserImage($row->photo); ?>" value="<?php echo $row->agency_user_account_id; ?>" <?php echo ( $row->agency_user_account_id == $this->input->get_post('pm_id') )?'selected="selected"':''; ?>><?php echo "{$row->fname} {$row->lname}"; ?></option>
							<?php
							}
							?>										
						</select>
					
				</div>
				
				
				
				<div class="col-md-3 columns">
					<label class="form-control-label">Search</label>
					
					
							<input type="text" name="search" class="form-control" placeholder="Text" value="<?php echo $this->input->get_post('search'); ?>" />
					
					
				</div>
				
				<div class="col-md-3 columns">
					<label class="col-sm-12 form-control-label">&nbsp;</label>
					
						<button type="submit" class="btn btn-inline">Search</button>
					
				</div>
        
        </div>
        </div>
        
			</div>
		</form>			
		</div>
	</header>
	
	<!-- list -->
	<div class="box-typical-body">
		<div class="table-responsive">
			<table class="table table-hover main-table">
				<thead>
					<tr>
				
						<th style="width:40%">Address</th>
						<th style="width:20%">Property Manager</th>

                        <?php 
                         $tt_api_row = $tt_api->row();
                         if( $tt_api->num_rows() > 0 ){  ##Show PME Prop Pmanage for PME only  
                         ?>
                        <th style="width:20%;color:#0082c6;">
                        <?php if( $tt_api_row->connected_service == 1 ){ //PME ?>
                            Property Manager (PropertyMe)
                        <?php } elseif( $tt_api_row->connected_service == 3 ){ //PTREE ?>
                            Property Manager (PropertyTree)
                        <?php } elseif( $tt_api_row->connected_service == 4 ){ //Palace ?>
                            Property Manager (PALACE)
                        <?php } ?>
                        </th>
                        <?php } ?>

						<th style="width:10%">Edit PM</th>
						<th style="width:10%">
                        
                        <div class="checkbox" style="margin:0;">
                            <input name="chk_all" type="checkbox" id="check-all">
                            <label for="check-all">Edit Multiple PM'S</label>
                        </div>
                        </th>
					</tr>
				</thead>
				<tbody>
                    <?php
                    if(!empty($prop_list)){
					foreach ( $prop_list  as $index => $row ){ 
					
					?>
						<tr class="prop_tr">

                       <td>
                       <?php echo "{$row->address_1} {$row->address_2}, {$row->address_3} {$row->state} {$row->postcode}"; ?>
                            </td>
                            
                        <td>
                            <div class="pm_edit_orig_box">

                            <?php  
                                if( isset($row->pm_id_new) && $row->pm_id_new != 0 ){
                                    echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
                                    echo "{$row->fname} {$row->lname}";
                                }
                            ?>    

                            </div>

                                    <div class="edit_single_pm_dropdown_box pm_edit_box row" style="display:none;">
                                        <div class="col-md-12 columns">
                                             <select name="pm_dropdown" class="form-control field_g2 select2-photo" >
                                                    <option value="">Property Manager</option>
                                                            <?php
                                                                foreach($property_manager_list as $row2){
                                                                    ?>
                                                                    
                                                                    <option <?php echo ($row->properties_model_id_new==$row2->agency_user_account_id)?'selected':'' ?> option data-photo="<?php echo $this->jcclass->displayUserImage($row2->photo); ?>" value="<?php echo $row2->agency_user_account_id; ?>"><?php echo $row2->fname." ".$row2->lname ?></option>

                                                                    <?php
                                                                } 
                                                            ?>

                                                </select>
                                        </div>
                                       
                                        </div>
                        </td>

                        <?php if( $tt_api->num_rows() > 0 ){ ?>
                        <td style="font-style:italic;">

                        <?php
                            
                            if( $row->api_prop_id!="" ){

                                if( $tt_api_row->connected_service == 1 ){ //PME PM
                                    $params = array(
                                        'prop_id' => $row->api_prop_id,
                                        'agency_id' => $this->session->agency_id
                                    );
                                    $pme_PM = $this->pme_model->get_pme_prop_pm($params);
                                    $pme_pm_row = json_decode($pme_PM);
        
                                    echo "{$pme_pm_row->FirstName} {$pme_pm_row->LastName}";
                                }elseif( $tt_api_row->connected_service == 3 ){ //PTREE PM

                                    $ptree_req = $this->property_tree_model->get_property($row->p_property_id);
                                    $ptree_obj = $ptree_req[0];
                                    
                                    if( !empty($ptree_obj->agents) ){

                                        foreach( $ptree_obj->agents as $index=>$value ){
                                            if( $index == 0 ){ //get first PM only
                                                
                                                $agent_params = array(
                                                    'agent_id' => $value,
                                                    'property_id' => $row->p_property_id
                                                );
                                                $agenty_req_obj = $this->property_tree_model->get_property_tree_agent_by_id($agent_params);

                                                if( $agenty_req_obj['http_status_code'] == 200 ){
                                                    $agenty_req_obj_res = $agenty_req_obj['json_decoded_response'];
                                                    $pme_prop_pm =  "{$agenty_req_obj_res->first_name} {$agenty_req_obj_res->last_name}";
                                                    echo $pme_prop_pm;
                                                }

                                            }
                                        }

                                    }

                                }elseif( $tt_api_row->connected_service == 4 ){ //PALACE PM

                                    $palace_params = array(
                                        'agency_id' => $this->session->agency_id,
                                        'palace_prop_id' => $row->api_prop_id
                                    );
                                    $palace_api_req = $this->palace_model->get_property($palace_params);

                                    $palace_obj_dec = json_decode($palace_api_req);
                                    
                                    echo $palace_obj_dec->PropertyAgentFullName;

                                }
                            }else{
                                echo "";
                            }
                           

                        ?>
                          <?php } ?>

                        </td>
                        <td>
                        <div class="pm_edit_orig_box">
                            <a data-toggle="tooltip" title="Edit PM" class="a_link edit_pm" href="#" >
									<span class="font-icon font-icon-pencil"></span>
                                </a>
                                                            </div>

                                <div class="pm_edit_box" style="display:none;">
                                <button data-propid="<?php echo $row->p_property_id; ?>" type="button" class="btn btn-sm edit_pm_single_update_btn">Update PM</button>
                                             <button type="button" class="btn btn-sm btn-danger edit_pm_single_cancel_btn">Cancel</button>
                                                            </div>
                            
                        </td>

						
							<td>
                            <div class="checkbox sd_checkbox" style="margin-bottom:0;">
								<input value="<?php echo $row->p_property_id; ?>"  type="checkbox" class="chkbox" name="chkbox[]" id="check<?php echo $row->p_property_id; ?>">
								<label for="check<?php echo $row->p_property_id; ?>">&nbsp;</label>
							</div>


							</td>
						</tr>
					
						
					<?php
                    }
                }
					?>		
											
				</tbody>
			</table>
            
			<div class="edit_pm_button txt-right" style="margin-bottom:1rem;display:none;">
                <?php echo form_open('/user_accounts/edit_multiple_pm','id=edit_multiple_pm_form'); ?>
							<div class="edit_multiple_pm_dropdown_box">
							<select name="pm_dropdown_for_multiple" class="form-control field_g2 select2-photo" >
								<option value="">Property Manager</option>
										<?php
											foreach($property_manager_list as $row){
												?>
												
												 <option option data-photo="<?php echo $this->jcclass->displayUserImage($row->photo); ?>" value="<?php echo $row->agency_user_account_id; ?>"><?php echo $row->fname." ".$row->lname ?></option>

												<?php
											} 
										?>

							</select>
							</div>
<div style="clear:both;"></div>
						<div>	<button class="btn" id="edit_multiple_pm_btn" type="button">Edit PM for Multiple Properties</button></div>
                
                </form>
							</div>
            
            
            
		</div>

		<nav aria-label="Page navigation example" style="text-align:center">
	
			<?php echo $pagination; ?>
		
		</nav>
		
		<div class="pagi_count"><?php echo $pagi_count; ?></div>
		
	</div><!--.box-typical-body-->
	
	
	
	
	
</section><!--.box-typical-->
<style>
.btn_save{
	margin-top: 10px;
}
.new_pass_div{
	display: none;
}
.radio {
    margin: 0;
}
.a_link {
    margin-right: 8px;   
}
.a_link{
	border-bottom: none !important;
}
.btn_add_user, 
.btn_show_all{
	position: relative;
    bottom: 8px;
    margin: 0 !important;
	margin-left: 10px !important;
}
.font-icon{
	color:#adb7be
}
.font-icon:hover{
	color:#00a8ff;
}
.font-icon-del:hover{
	color:#fa424a
}
</style>
<script>
jQuery(document).ready(function(){

    //select all checkbox tweak
    $('#check-all').on('change',function(){
        var obj = $(this);
        var isChecked = obj.is(':checked');
        if(isChecked){
            $('.edit_pm_button').slideDown();
            $('.chkbox').prop('checked',true);
        }else{
            $('.edit_pm_button').slideUp();
            $('.chkbox').prop('checked',false);
        }
    })
	
	//select2
	$(".select2-photo").not('.manual').select2({
			templateSelection: select2Photos,
			templateResult: select2Photos
		 })


    $('.edit_pm').on('click',function(e){
        e.preventDefault();
        var obj = $(this);
        
        //set default
        $('.pm_edit_box').hide();
        $('.pm_edit_orig_box').show();

        obj.parents('.prop_tr').find('.pm_edit_box').show();
        obj.parents('.prop_tr').find('.pm_edit_orig_box').hide();
       
    })


    $('.edit_pm_single_update_btn').on('click',function(e){
                e.preventDefault();
                var obj = $(this);
                 var pm_id = obj.parents('.prop_tr').find('select[name="pm_dropdown"]');
                 var propId = obj.data('propid');

                 if(pm_id.val()==""){
                     swal('Error','Property Manager must not be empty','error');
                     return false;
                 }


                 swal(
                    {
                        title: "",
                        text: "Update Property Manager?",
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
                                    url: "<?php echo base_url('/properties/update_property_manager') ?>",
                                    dataType: 'json',
                                    data: {
                                        prop_id: propId,
										pm_id:pm_id.val()
                                    }
                                    }).done(function(data){
                                        if(data.status){
                                            swal({
                                                title:"Success!",
                                                text: "Property Manager Updated",
                                                type: "success",
                                                showCancelButton: false,
                                                confirmButtonText: "OK",
                                                closeOnConfirm: false,

                                            },function(isConfirm){
                                               if(isConfirm){ 
												  location.reload();
												   }
                                            });
                                        }else{
										   location.reload();
                                        }
                                    });
                                }
                        
                    }
            	);

    })


    $('.edit_pm_single_cancel_btn').on('click',function(e){
        e.preventDefault();
        var obj = $(this);

        obj.parents('.prop_tr').find('.pm_edit_box').hide();
        obj.parents('.prop_tr').find('.pm_edit_orig_box').show();

    })



	$('.chkbox').on('change',function(){

			var obj = $(this);

				if(obj.is(':checked')){
						$('.edit_pm_button').slideDown();
                        obj.parents('.prop_tr').addClass('selected');
				}else{

					if($('[name="chkbox[]"]:checked').length==0){
						$('.edit_pm_button').slideUp();
					}
                    obj.parents('.prop_tr').removeClass('selected');

				}
				
	});



	$('#edit_multiple_pm_btn').on('click',function(){

			var pm_id = $('select[name="pm_dropdown_for_multiple"]').val();

			var prop_id = [];
			$('input[name="chkbox[]"]').each(function(){
				if($(this).is(':checked')){
					prop_id.push($(this).val());
				}
			});

			if($('[name="chkbox[]"]:checked').length==0){
				swal('Error','Property tick box must not be empty','error');
				return false;
			}
			if(pm_id==""){
				swal('Error','Property Manager must not be empty','error');
				return false;
			}

			//$('#edit_multiple_pm_form').submit();

            swal(
                    {
                        title: "",
                        text: "Update Property Manager for Selected Properties?",
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
                            $.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('/properties/update_property_manager/') ?>",
                                    dataType: 'json',
                                    data: {
                                        insert_type: 1, // 1 = multiple/batch
                                        prop_id: prop_id,
                                        pm_id: pm_id
                                    }
                                }).done(function(data){

                                        if(data.status){
                                            swal({
                                                title:"Success!",
                                                text: "Property Manager Updated",
                                                type: "success",
                                                showCancelButton: false,
                                                confirmButtonText: "OK",
                                                closeOnConfirm: false,

                                            },function(isConfirm){
                                                if(isConfirm){ 
                                                    location.reload();
                                                    }
                                            });
                                        }else{
                                            location.reload();
                                        }

                                });
                        }
                        
                    }
            	);

			

	});
	
	
});
</script>   
