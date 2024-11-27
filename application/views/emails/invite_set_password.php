<?php $this->load->view('emails/template/email_header.php') ?>

<p>Hi <?php echo $user_fname ?>,</p>

<p>

	<?php echo $admin_full_name; ?> invited you to start using the <?=$this->config->item('COMPANY_NAME_SHORT')?> Portal. 
	For security reasons this invitation will expire in 48hrs.
    You can create your own login so all you need to do is press the button below and in less than 30 seconds you will be up and running.

</p>


<!-- bulletproof button for emails --->

<div style="text-align: center;"><!--[if mso]>
  <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo base_url("user_accounts/set_password?sp=" . $id_hash); ?>" style="height:40px;v-text-anchor:middle;width:200px;" arcsize="8%" strokecolor="#007bff" fillcolor="#007bff">
    <w:anchorlock/>
    <center style="color:#ffffff;font-family:sans-serif;font-size:13px;font-weight:bold;">Set My Password</center>
  </v:roundrect>
<![endif]--><a href="<?php echo base_url("user_accounts/set_password?sp=" . $id_hash); ?>"
style="background-color: <?= $this->config->item('theme') == 'sas' ? '#00607f' : '#007bff'?>;border-radius:3px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;mso-hide:all;">Set My Password</a></div>

<!-- temporarily remove while waiting for the video tutorial for SAS -->
<!-- <p><a href="https://youtu.be/HmlPIXgxYKQ">CLICK HERE</a> to watch a tutorial on how to get started.</p> -->
<?php if($this->config->item('theme') === 'sas'): ?>
<p>
    <a href="https://youtu.be/Y3FRIQHD9OA?si=b0w7xMxjz1MiY25e">CLICK HERE</a> <span>to watch a tutorial on how to get started.</span>
</p>
<?php endif; ?>

<p>	
	For Security reasons, we are letting you know this request was made by <?php echo $admin_full_name; ?> from a <?php echo $this->jcclass->getOS(); ?> device using <?php echo $this->jcclass->getBrowser(); ?> and IP address <?php echo $this->jcclass->getIPaddress(); ?>. 
	If you have any doubts just give them a call or <a href="<?= $this->config->item('theme') == 'sas' ? 'https://smokealarmsolutions.com.au/contact/' : (($this->config->item('country') == 1 ) ? 'https://www.sats.com.au/contact/':'https://sats.co.nz/contact/'); ?>" target="_blank">contact us</a>.
</p>


<p>
	Thanks<br />
	The <?=$this->config->item('COMPANY_NAME_SHORT')?> Team.
</p>

<?php $this->load->view('emails/template/email_footer.php');