<?php $this->load->view('emails/template/email_header.php') ?>

<p>Hi <?php echo $user_fname ?>,</p>

<p>
	Because you have an enabled Two-factor Authentication for <?=$this->config->item('theme_email_from')?>  Agency Portal. 
	You will need authentication code below to proceed and login. <strong>This code is only valid for 5 minutes.</strong>
</p>

<h1><?php echo $twofa_code; ?></h1>

<p>
	For Security, this request was received from a <?php echo $this->jcclass->getOS(); ?> device 
	using <?php echo $this->jcclass->getBrowser(); ?> 
	and IP address <?php echo $this->jcclass->getIPaddress(); ?>.
</p>
<p>
	If you did not request a password reset, 
	please ignore this email or <a href="https://www.sats.com.au/contact/" target="_blank">contact us</a> 
	if you have any questions.
</p>

<p>
	Thanks<br />
	The <?=$this->config->item('COMPANY_NAME_SHORT')?> Team.
</p>

<?php $this->load->view('emails/template/email_footer.php');