<?php $this->load->view('emails/template/email_header.php') ?>

<p>Dear Info,</p>

<p>Property service for <?php echo $property_address; ?> was updated. However, the system found a YM that was in precom status. As the system cannot determine the end state of the job at this point, it was not updated. Please go to the property and ensure a YM job exists for the new service, so the property rolls correctly.</p>

<p>
  Regards, <?=$this->config->item('COMPANY_NAME_SHORT')?> Team
</p>


<?php $this->load->view('emails/template/email_footer.php');