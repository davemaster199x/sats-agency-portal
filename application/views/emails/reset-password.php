<?php $this->load->view('emails/template/email_header.php') ?>

<p>Hi <?php echo $user_fname ?>,</p>

<!-- Extend Reset Password Validity for 100 hours for SAS Onboarding purpose will revert back to 2 hours after -->
<p>
	You recently requested to reset your password for <?= $this->config->item('COMPANY_FULL_NAME') ?> Agency Portal. 
	Use the button below to reset it. <strong>This password reset is only valid for the next 48 hours.</strong>
</p>


<!-- bulletproof button for emails --->

<div style="text-align: center;"><!--[if mso]>
  <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo base_url("user_accounts/set_password?sp=" . $id_hash); ?>" style="height:40px;v-text-anchor:middle;width:200px;" arcsize="8%" strokecolor="#007bff" fillcolor="#007bff">
    <w:anchorlock/>
    <center style="color:#ffffff;font-family:sans-serif;font-size:13px;font-weight:bold;">Reset Your Password</center>
  </v:roundrect>
<![endif]--><a href="<?php echo base_url("user_accounts/set_password?sp=" . $id_hash); ?>"
style="<?= $this->config->item('theme') == 'sas' ? 'background-color: #00607f;' : 'background-color:#007bff;'?> border-radius:3px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;mso-hide:all;">Reset Your Password</a></div>

<!-- temporarily remove while waiting for the video tutorial for SAS -->
<!-- <p><a href="https://youtu.be/HmlPIXgxYKQ">CLICK HERE</a> to watch a tutorial on how to get started.</p> -->

<p>
	For Security, this request was received from a <?php echo $this->jcclass->getOS(); ?> device using <?php echo $this->jcclass->getBrowser(); ?> and IP address <?php echo $this->jcclass->getIPaddress(); ?>.
	If you did not request a password reset, please ignore this email or <a href="<?= $this->config->item('theme') == 'sas' ? 'https://smokealarmsolutions.com.au/contact/' : (($this->config->item('country') == 1 ) ? 'https://www.sats.com.au/contact/':'https://sats.co.nz/contact/'); ?>" target="_blank">contact us</a> if you have any questions.
</p>
<p>

</p>

<p>
	Thanks<br />
	The <span style="text-transform: uppercase;"><?=$this->config->item('COMPANY_NAME_SHORT')?></span> Team.
</p>

<?php $this->load->view('emails/template/email_footer.php');