<?php $this->load->view('emails/template/email_header.php') ?>

<p>Hi Team,</p>

<p>
	Please schedule Agency Portal training for <?= $fullname ?> from <?= $agency_name ?>. Please contact <a href="mailto:<?= $user_email ?>"><?= $user_email ?></a> to arrange a time for the portal training.
</p>
<br/>
<p>This is an automated email. Please do not reply.</p>



<?php $this->load->view('emails/template/email_footer.php');