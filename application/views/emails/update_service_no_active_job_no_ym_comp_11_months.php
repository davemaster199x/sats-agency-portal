<?php $this->load->view('emails/template/email_header.php') ?>

<p>Dear Info,</p>

<p>The property service for <a href="https:<?php echo $this->config->item('crm_link')."/view_property_details.php?id={$property_id}"; ?>"><?php echo $property_address; ?></a> was updated. However, there was no active job updated, and this property may not renew in the future. Please go to the property and ensure a YM job exists so that the property rolls correctly.</p>

<p>
  Regards, <?=$this->config->item('COMPANY_NAME_SHORT')?> Team
</p>


<?php $this->load->view('emails/template/email_footer.php');