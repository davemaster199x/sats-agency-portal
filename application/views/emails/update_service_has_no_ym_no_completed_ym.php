<?php $this->load->view('emails/template/email_header.php') ?>

<p>Dear Info,</p>

<p>Property service for <a href="<?php echo $this->config->item('crm_link')."/view_property_details.php?id={$property_id}"; ?>"><?php echo $property_address; ?></a> was updated. However, the system couldn't find a YM to replicate. Please go to the property and ensure a YM job exists so the property rolls correctly.</p>

<p>
  Regards, <?=$this->config->item('COMPANY_NAME_SHORT')?> Team
</p>


<?php $this->load->view('emails/template/email_footer.php');