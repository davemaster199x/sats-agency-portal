<?php $this->load->view('emails/template/email_header.php') ?>

<p>Hi Sales,</p>

<p><?php echo $user; ?> from <?php echo $agency_name; ?> has requested an Audit on their portfolio.</p>
<p>Please contact them ASAP to show them how easy it is to Audit their portfolio</p>

<?php $this->load->view('emails/template/email_footer.php');