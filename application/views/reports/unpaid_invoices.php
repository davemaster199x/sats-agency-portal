<section class="box-typical box-typical-padding">

	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/reports">Reports</a></li>
		<li class="breadcrumb-item active" aria-current="page"><a href="/reports/unpaid_invoices"><?php echo $title; ?></a></li>
	  </ol>
	</nav>

    <!-- list -->
    <div class="box-typical-body">

        <div class="table-responsive">
            <table class="table table-hover main-table">
                <thead>
                    <tr>
                        <th>Address</th>
                        <th>Property Manager</th>
                        <th>Job Type</th>
                        <th>Invoice Date</th>
                        <th>Invoice Amount</th>
                        <th>Invoice Due Date</th>
                        <th>Invoice #</th>
                        <th>View Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $invoice_balance_tot = 0;
                        foreach($list->result_array() as $row){ 
                            $check_digit = $this->jobs_model->getCheckDigit(trim($row['j_id']));
                            $bpay_ref_code = "{$row['j_id']}{$check_digit}"; 
                    ?>
                    <tr data-j_id = "<?php echo $row['j_id'] ?>">
                        <td><a target="blank" href="/properties/property_detail/<?php echo $row['property_id'] ?>"><?php echo $row['p_address_1']." ".$row['p_address_2'].", ".$row['p_address_3'] ?></a></td>
                        <td>
							  <?php  
                                if( isset($row['pm_id_new']) && $row['pm_id_new'] != 0 ){
                                    echo $this->gherxlib->avatarv2($row['photo'])."&nbsp;&nbsp;";
                                    echo "{$row['pm_fname']} {$row['pm_lname']}";
                                }
                             ?>
						</td>
                        <td><?php echo $row['job_type'] ?></td>
                        <td><?php echo $this->jcclass->isDateNotEmpty($row['j_date'])?date('d/m/Y',strtotime($row['j_date'])):''; ?></td>
                        <td><?php echo '$'.$row['invoice_balance'] ?></td>
                        <td><?php echo $this->jcclass->isDateNotEmpty($row['due_date'])?date('d/m/Y',strtotime($row['due_date'])):''; ?></td>
                        <td><?php echo $bpay_ref_code; ?></td>
                        <td>
                            <?php
	                            $hashIds = new HashEncryption($this->config->item('hash_salt'), 6);
                                $encrypted_job_id = rawurlencode($hashIds->encodeString($row['j_id']));
                                $invoice_url =  $this->config->item('crmci_link')."/pdf/view_invoice/?job_id=".$encrypted_job_id; // invoice url
                            ?>
                            <a target="_blank" href="<?php echo $invoice_url; ?>"><i style="font-size:26px;" class="font-icon font-icon-pdf"></i></a>
                        </td>
                    </tr>
                    
                    <?php 
                        $invoice_balance_tot += $row['invoice_balance'];     
                        } 
                    ?>
                    <tr><td colspan="4"><strong>Total</strong></td><td colspan="4"><strong><?php echo '$'.number_format($invoice_balance_tot,2) ?></strong></td></tr>
                </tbody>
            </table>
        </div>

        <nav aria-label="Page navigation example" style="text-align:center">

            <?php echo $pagination ?>

        </nav>
        <div class="pagi_count"><?php echo $pagi_count ?></div>

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

