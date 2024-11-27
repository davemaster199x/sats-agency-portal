<section class="box-typical box-typical-padding">
	<h5 class="m-t-lg with-border"><?php echo $title; ?>


</h5>


        <!-- Header -->
        <header class="box-typical-header">
            <div class="box-typical box-typical-padding">

                <?php
				$form_attr = array(
					'id' => 'jform'
				);
				echo form_open('/sms/job_feedback',$form_attr);
				?>
                    <div class="form-groupss row">




                    <div class="float-left">
					<label class="col-sm-12 form-control-label">From</label>
					<div class="col-sm-12">
						<div class="input-group flatpickr" data-wrap="true" data-default-date="<?php echo $date_from; ?>">
							<input type="text" class="form-control" name="from" id="from" data-input />
							<span class="input-group-append" data-toggle>
								<span class="input-group-text">
									<i class="font-icon font-icon-calend"></i>
								</span>
							</span>
						</div>
					</div>
				</div>

				<div class="float-left">
					<label class="col-sm-12 form-control-label">To</label>
					<div class="col-sm-12">
						<div class="input-group flatpickr" data-wrap="true" data-default-date="<?php echo $date_to; ?>">
							<input type="text" class="form-control" name="to" id="to" data-input />
							<span class="input-group-append" data-toggle>
								<span class="input-group-text">
									<i class="font-icon font-icon-calend"></i>
								</span>
							</span>
						</div>
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

        <!-- list -->
        <div class="box-typical-body">

            <div class="table-responsive">
                <table class="table table-hover main-table">
                    <thead>
                        <tr>
                            <th>Address</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>From</th>
                            <th>Tenant</th>
                            <th>Message</th>
                            <th>Technician</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if( !empty($feedback_list) ){
                        foreach($feedback_list as $row){
                            $sent_by = ($row->sent_by == -3)?'CRM':$row->FirstName." ".$row->LastName;
                            ?>

                    <tr>

                        <td><?php echo '<a href="/properties/property_detail/'.$row->property_id.'">'.$row->address_1." ".$row->address_2." ".$row->address_3.'</a>'?></td>
                        <td><?php echo date('d/m/Y', strtotime($row->created_date)) ?></td>
                        <td><?php echo date('H:i', strtotime($row->created_date)) ?></td>
                        <td>
                        <?php echo  $mob_num = '0'.substr($row->mobile,2); ?>
                        </td>
                        <td>
                        <?php
                        $active_tenants = $row->new_tenants;
                        if(!empty($active_tenants)){
                            $aw = array();
                            foreach ($active_tenants as $tenants_row) {

                                $aw[] = $this->jcclass->formatStaffName($tenants_row->tenant_firstname, $tenants_row->tenant_lastname);

                            }
                            echo implode(" | ", $aw);
                        }
                        ?>

                        </td>
                        <td><?php echo $row->response; ?></td>
                        <td><?php echo $this->jcclass->formatStaffName($row->tech_fname,$row->tech_lname); ?></td>
                    </tr>

                            <?php
                        }
                    }else{ ?>
                        <tr>
                            <td colspan="7">There is no Tenant feedback in the last 30 days</td>
                        </tr>
                    <?php
                    }
                    ?>


                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation example" style="text-align:center">

             <?php echo $pagination; ?>

            </nav>
            <div class="pagi_count"><?php echo $pagi_count; ?></div>

        </div>
        <!--.box-typical-body-->


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
