<?php $this->load->view('emails/template/email_header.php') ?>

<p>Dear Info,</p>
<br/>
<p><strong><?php echo $agency_user_fname; ?></strong> from <strong><?php echo $agency_name; ?></strong> has advised us that <strong><a href="<?php echo $this->config->item('crm_link') ?>/view_property_details.php?id=<?php echo $prop_id; ?>"><?php echo $prop_address; ?></a></strong> is already upgraded.</p>
<p>This property currently has a job with a status of either 'Precompletion' or 'Merged Certificates'. Please go to the property to confirm it has been correctly upgraded by the system.</p>
<br/>
<p>
Regards,<br/>
<?=$this->config->item('COMPANY_NAME_SHORT')?> Team
</p>

<?php $this->load->view('emails/template/email_footer.php');