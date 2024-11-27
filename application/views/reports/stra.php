<section class="box-typical box-typical-padding">

	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/reports">Reports</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/reports/stra/?compliant=<?php echo $this->input->get_post('compliant'); ?>"><?php echo $title; ?></a></li>
	  </ol>
	</nav>

	<h5 class="m-t-lg with-border"><?php echo $title; ?>

         <!--   
		<div class="float-right">
			<div class="col-sm-12">
                <section class="proj-page-section">
                    <div class="proj-page-attach">
                        <i class="font-icon font-icon-pdf"></i>
                        <p class="name">QLD Upgrade</p>
                        <p>
                            <a href="
                                /reports/qld_upgrade/?pdf=1
                                &output_type=I
                                &pm_id=<?php echo $this->input->get_post('pm_id'); ?>
                                &search=<?php echo $this->input->get_post('search'); ?>"
                                
                                target="blank"
                            >
                                View
                            </a>
                            
                            <a href="
                                /reports/qld_upgrade/?pdf=1
                                &output_type=D
                                &pm_id=<?php echo $this->input->get_post('pm_id'); ?>
                                &search=<?php echo $this->input->get_post('search'); ?>"
                            >
                                Download
                            </a>
                        </p>
                    </div>
                </section>
		    </div>
        </div>
        -->

        <?php 
			$export_link_params = array(
				'compliant' => $this->input->get_post('compliant'),
				'pm_id' => $this->input->get_post('pm_id'),
				'search' => $this->input->get_post('search')
			);
			$export_link = '/reports/stra/?export=1&'.http_build_query($export_link_params);

		?>
	
		<div class="float-right">
			<div class="col-sm-12">
				<section class="proj-page-section">
					<div class="proj-page-attach">
						<i class="font-icon font-icon-post"></i>
						<p class="name"> <?php echo $title; ?> CSV</p>
						<p>
							<a href="<?php echo $export_link; ?>">
								Download
							</a>
						</p>
					</div>
				</section>
			</div>
		</div>
        
</h5>
   

        <!-- Header -->
        <header class="box-typical-header">
            <div class="box-typical box-typical-padding">
			
                <?php
				$form_attr = array(
					'id' => 'jform'
				);
				echo form_open("/reports/stra/?compliant={$this->input->get_post('compliant')}",$form_attr);
				?>
                    <div class="form-groupss row">
                        
						<div class="float-left">
							<label for="exampleSelect" class="col-sm-12 form-control-label">Property Manager</label>
							<div class="col-sm-12" style="width:250px;">
								<select name="pm_id" class="form-control field_g2 select2-photo">
									<option value="">---</option>
                                    <option <?php  echo ( $this->input->get_post('pm_id') == '0' )?'selected="selected"':''; ?> value="0" data-photo="<?php echo $this->config->item('photo_empty'); ?>">No PM assigned</option>				
													
									<?php

									foreach($pm_filter->result() as $pm_row){
										if($pm_row->properties_model_id_new){
										?>
									
										<option data-photo="<?php echo $this->jcclass->displayUserImage($pm_row->photo); ?>" value="<?php echo $pm_row->properties_model_id_new; ?>" <?php echo ( $pm_row->properties_model_id_new == $this->input->get_post('pm_id') )?'selected="selected"':''; ?>><?php echo "{$pm_row->fname} {$pm_row->lname}"; ?></option>
										
										<?php
									}
								}
								
								
									?>
								</select>
							</div>
						</div>
						
						
					

                        <div class="float-left">
					<label class="col-sm-12 form-control-label">Search</label>
					<div class="col-sm-12">
					<input type="text" name="search" class="form-control" placeholder="Text" value="<?php echo $this->input->get_post('search'); ?>" />
					</div>
				</div>

                        <div class="float-left">
                            <label class="col-sm-12 form-control-label">&nbsp;</label>
                            <div class="col-sm-12">
								<input type="submit" class="btn btn-inline" id="create_job_search_btn" value="Search">
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </form>
            </div>
        </header>

  
        <section class="tabs-section">

            <div class="tabs-section-nav tabs-section-nav-icons">
                <div class="tbl">
                    <ul class="nav" role="tablist">
                        <li class="nav-item">

                            <a class="nav-link <?php echo ( $this->input->get_post('compliant') == 1 )?'active':null; ?>" href="/reports/stra/?compliant=1">
                                <span class="nav-link-in">
                                    <!--<i class="fa fa-calendar-check-o"></i>-->
                                    STRA Compliant
                                </span>
                            </a>

                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ( is_numeric($this->input->get_post('compliant')) && $this->input->get_post('compliant') == 0 )?'active':null; ?>" href="/reports/stra/?compliant=0">
                                <span class="nav-link-in">
                                    <!--<span class="fa fa-hourglass-end"></span>-->
                                    STRA Non-Compliant
                                </span>
                            </a>
                        </li>
                    
                    </ul>
                </div>
            </div>

       
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active show" id="tabs1">
                                                   
                        <div class="box-typical-body">

                            <div class="table-responsive">
                                <table class="table table-hover main-table">
                                    <thead>
                                        <tr>
                                            <th>Address</th>
                                            <th>Property Manager</th>                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <?php
                                        if(!empty($list->result())){
                                            foreach($list->result() as $row){
                                                ?>
                                                    <tr>

                                                        <td>
                                                            <a href="/properties/property_detail/<?php echo $row->property_id ?>" target="blank">
                                                                <?php echo "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}"; ?>
                                                            </a>                        
                                                        </td>
                                                        <td>
                                                            <?php  
                                                            if( isset($row->properties_model_id_new) && $row->properties_model_id_new != 0 && $row->properties_model_fname!="" ){
                                                                echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
                                                                echo "{$row->properties_model_fname} {$row->properties_model_lname}";
                                                            }
                                                            ?>    
                                                        </td>                                        
                                                    </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    

                                    </tbody>
                                </table>
                            </div>

                            <nav aria-label="Page navigation example" style="text-align:center">

                            <?php echo $pagination ?>

                            </nav>
                            <div class="pagi_count"><?php echo $pagi_count ?></div>

                        </div>                             
                
                </div>                
            </div>

        </section>

        


</section>
    <!--.box-typical-->

<script>
jQuery(document).ready(function(){
    //select2
	$(".select2-photo").not('.manual').select2({
			templateSelection: select2Photos,
			templateResult: select2Photos
		});
})
</script>

