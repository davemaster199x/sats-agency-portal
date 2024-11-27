<?php $this->load->view('emails/template/email_header.php') ?>


<p><strong>Property</strong>
<br/>
<?php echo $property_address; ?>
</p>
<hr/>

<p><strong>Changes Made</strong>
<br/>
<?php
if(!empty($email_services_array)){
    foreach($email_services_array as $row){
      echo $row['service']." From <b>".$row['service_from']."</b> To <b>". $row['service_to']."</b><br/>";
    }
}

?>
</p>

<hr/>

<p><strong>Agency</strong>
<br/>
<?php echo $agency_name; ?>
</p>
<hr/>
<p><strong>Changed by</strong>
<br/>
<?php echo $agent_name; ?>
</p>


<?php $this->load->view('emails/template/email_footer.php');