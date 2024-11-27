<?php $this->load->view('emails/template/email_header.php') ?>

<!-- CONTENT START HERE -->

<p>Your password has been reset to <strong><?php echo $random_pass; ?></strong>, please update your password after.</p>

<!-- CONTENT END HERE -->


<?php $this->load->view('emails/template/email_footer.php');