
<section class="box-typical box-typical-padding">

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/reports">Reports</a></li>
    <li class="breadcrumb-item active" aria-current="page"><a href="/reports/key_pick_up"><?php echo $title; ?></a></li>
  </ol>
</nav>

<h5 class="m-t-lg with-border"><?php echo $title; ?>

    <div class="float-right">
        <div class="col-sm-12">
            <section class="proj-page-section">
                <div class="proj-page-attach">
                    <i class="font-icon font-icon-pdf"></i>
                    <p class="name"><?php echo $title; ?></p>
                    <p>
                        <a href="/reports/key_pick_up/?pdf=1&output_type=I&date=<?php echo (!empty($this->input->get_post('date')))?$this->input->get_post('date'):date('Y-m-d'); ?>&tech_id=<?php echo $this->input->get_post('tech_id'); ?>"
                            target="blank"
                        >
                            View
                        </a>

                        <a href="
                            /reports/key_pick_up/?pdf=1&output_type=D&date=<?php echo (!empty($this->input->get_post('date')))?$this->input->get_post('date'):date('Y-m-d'); ?>&tech_id=<?php echo $this->input->get_post('tech_id'); ?>">
                            Download
                        </a>
                    </p>
                </div>
            </section>
        </div>
    </div>

        <?php 
			$export_link_params = array(
				'tech_id' => $this->input->get_post('tech_id'),
				'date' => $this->input->get_post('date')
			);
			$export_link = '/reports/key_pick_up/?export=1&'.http_build_query($export_link_params);

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
        'id' => 'jform',
        'method' => 'get',
    );
    echo form_open('/reports/key_pick_up',$form_attr);
    ?>
        <div class="form-groupss row">


            <div class="float-left">
                <label class="col-sm-12 form-control-label">Date</label>
                <div class="col-sm-12">
                    <div class="input-group flatpickr" data-wrap="true" data-default-date="<?php echo ( $this->input->get_post('date') !='' )?$this->input->get_post('date'):date('d/m/Y'); ?>">
                        <input type="text" class="form-control" name="date" id="date" data-input  />
                        <span class="input-group-append" data-toggle>
                            <span class="input-group-text">
                                <i class="font-icon font-icon-calend"></i>
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="float-left">
                <label class="col-sm-12 form-control-label">Tech</label>
                <div class="col-sm-12">
                    <select id="tech_id" name="tech_id" class="form-control field_g2">
                        <option value="">ALL</option>
                        <?php
                        foreach($tech_list as $row){
                            if (empty($row['staff_fName'])) continue;
                            $selected = ($this->input->get_post('tech_id')==$row['StaffID'])?'selected':'';
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $row['StaffID'] ?>"><?php echo $row['staff_fName']." ".$row['staff_lName'] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="float-left">
                <label class="col-sm-12 form-control-label">&nbsp;</label>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-inline">Search</button>
                </div>
            </div>

        </div>
    </form>
    </div>
</header>

<!-- list -->
<?php
// header filters
$filter_params = "
&from=".$this->input->get_post('from')."
&to=".$this->input->get_post('to')."
&pm_id=".$this->input->get_post('pm_id')."
&search=".$this->input->get_post('search');

// sort toggle
$toggle_sort = ( $sort == 'asc' )?'desc':'asc';
?>
<div class="box-typical-body">
    <div class="table-responsive">
        <table id="dataTable" class="table table-hover main-table datatable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Technician</th>
                    <th>Action</th>
                    <th>Time</th>
                    <th>Number of Keys</th>
                    <th>Agency Staff</th>
                    <th>Signature</th>
                </tr>
            </thead>
            <tbody>

                <?php
                if(!empty($keyRunRows)){
                    foreach($keyRunRows as $i => $row){
                ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($row['tech_date'])) ?></td>
                    <td><?php echo "{$row['staff_fName']} {$row['staff_lName']}" ?></td>
                    <td style="color:<?php echo ($row['action']=="Pick Up")?'green"':'red'; ?>"><?php echo $row['action']; ?></td>
                    <td><?php echo date('H:i', strtotime($row['completed_date'])) ?></td>
                    <td><?php echo $row['number_of_keys'] ?></td>
                    <td><?php echo $row['kr_agency_staff'] ?></td>
                    <td>
                        <?php
                        if( $row['signature_svg']!='' ){ ?>
                            <a data-toggle="tooltip" title="Show/View" href="#fancy_<?php echo $i ?>" class="inline_fancybox"><span class="fa fa-eye" style="font-size:20px;"></span></a>

                            <div style="display:none;" id="fancy_<?php echo $i; ?>">
                                <img  style="width:300px;" src="<?php echo $row['signature_svg'] ?>" />
                            </div>

                        <?php
                        }else{
                           echo "N/A";
                        }
                        ?>
                    </td>
                </tr>
                <?php
                    }}else{
                        echo "<tr><td colspan='6'>No Data</td></tr>";
                    }
                ?>

            </tbody>
        </table>
    </div>

    <nav aria-label="Page navigation example" style="text-align:center">
        <?php echo $pagination; ?>
    </nav>

    <div class="pagi_count"><?php echo $pagi_count; ?></div>

</div><!--.box-typical-body-->


</section><!--.box-typical-->



<script>
jQuery(document).ready(function(){

    //init datepicker
    jQuery('.flatpickr').flatpickr({
        dateFormat: "d/m/Y"
    });

    $("a.inline_fancybox").fancybox({});

});
</script>