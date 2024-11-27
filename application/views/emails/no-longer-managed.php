<?php $this->load->view('emails/template/email_header.php') ?>



<p><strong>Property</strong>
<br/>
<?php echo $property_address; ?>
</p>
<hr/>
<h3>Services Cancelled</h3>

<?php if(!empty($serv_list)){
    echo "<ul>";
        foreach($serv_list as $new_serv_list){
            switch($new_serv_list->alarm_job_type_id){
                case 2:
                    $s = "Smoke Alarms";
                break;
                case 5:
                    $s = "Safety Switch";
                break;
                case 6:
                    $s = "Corded Window";
                break;
                case 7:
                    $s = "Pool Barriers";
                break;
            }
        }
        echo "<li>".$s."</li>";
} 
    echo "</ul>";
?>
<hr/>
<h3>Agency</h3>
<?php echo $agency_name; ?>






<?php $this->load->view('emails/template/email_footer.php');