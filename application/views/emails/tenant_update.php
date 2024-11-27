<?php $this->load->view('emails/template/email_header.php') ?>


<p><strong>Tenant updated for <?php echo $property_address; ?></strong></p>

<?php if($orig_fname!=$fname){ ?>
<p>
    <strong>First Name</strong><br/>
    From: <?php echo $orig_fname; ?>
    <br/>
    To: <?php echo $fname; ?>
</p>
<?php } ?>

<?php if($orig_lname!=$lname){ ?>
<p>
    <strong>Last Name</strong><br/>
    From: <?php echo $orig_lname; ?>
    <br/>
    To: <?php echo $lname; ?>
</p>
<?php } ?>

<?php if($mobile!=$orig_mobile){ ?>
<p>
    <strong>Mobile</strong><br/>
    From: <?php echo $orig_mobile; ?>
    <br/>
    To: <?php echo $mobile; ?>
</p>
<?php } ?>

<?php if($landline!=$orig_landline){ ?>
<p>
    <strong>Landline</strong><br/>
    From: <?php echo $orig_landline; ?>
    <br/>
    To: <?php echo $landline; ?>
</p>
<?php } ?>

<?php if($email!=$orig_email){ ?>
<p>
    <strong>Email</strong><br/>
    From: <?php echo $orig_email; ?>
    <br/>
    To: <?php echo $email; ?>
</p>
<?php } ?>





<?php $this->load->view('emails/template/email_footer.php');