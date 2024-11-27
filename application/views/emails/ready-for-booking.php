<?php $this->load->view('emails/template/email_header.php') ?>


<p>Dear <?php echo $agency_name; ?>,</p>
<p><?php echo $agent_name; ?> has added a new property through the <?=$this->config->item('COMPANY_NAME_SHORT')?> agency portal</p>
<p style="color:#b4151b"><strong>Property Address:</strong></p>
<p><?php echo $mail_prop_address; ?></p>
<hr/>
<h3 style="color:#b4151b">Tenants:</h3>
<?php if(!empty($active_tenants)){ ?>
    <table style="width:100%; border:1px solid #ccc;text-align: left;">
    <thead>
        <tr>
            <th style="padding:5px;text-align:left;">First Name</th>
            <th style="padding:5px;text-align:left;">Last Name</th>
            <th style="padding:5px;text-align:left;">Mobile</th>
            <th style="padding:5px;text-align:left;">Landline</th>
            <th style="padding:5px;text-align:left;">Email</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($active_tenants as $new_active_tenants): ?>
        <tr>
            <td style="padding:5px;"><?php echo $new_active_tenants->tenant_firstname ?></td>
            <td style="padding:5px;"><?php echo $new_active_tenants->tenant_lastname ?></td>
            <td style="padding:5px;"><?php echo $new_active_tenants->tenant_mobile ?></td>
            <td style="padding:5px;"><?php echo $new_active_tenants->tenant_landline ?></td>
            <td style="padding:5px;"><?php echo $new_active_tenants->tenant_email ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php }else{
   echo " No Tenants entered";
 } ?>

   <h3 style="color:#b4151b">Services:</h3>
    <ul style="margin: 0;padding-left:10px;">
        <?php if(!empty($get_property_services)){
            foreach($get_property_services as $new_get_property_services){
                switch($new_get_property_services->service){
                            case 0:
                                $service = 'DIY';
                                $color="#8F8C8C";
							break;
							case 1:
                                $service = '<span style="color:#b4151b;">'.$this->config->item('COMPANY_NAME_SHORT').'</span>';
                                $color="#000";
							break;
							case 2:
                                $service = 'No Response';
                                $color="#8F8C8C";
							break;
							case 3:
                                $service = 'Other Provider';
                                $color="#8F8C8C";
							break;
                }
            ?>
                <li style="color:<?php echo $color; ?>"><?php echo $new_get_property_services->type." ".$new_get_property_services->price." - <b>".$service."</b>" ?></li>
        <?php } }?>
</ul>
    
<h3 style="color:#b4151b">Comments:</h3>
Job Comments: <?php echo $job_comments; ?>

<h3 style="color:#b4151b">Landlord:</h3>
<table style="width:100%; border:1px solid #ccc;text-align: left;">
    <thead>
       <th style="padding:5px;text-align:left;">Full Name</th>
        <th style="padding:5px;text-align:left;">Mobile</th>
       <th style="padding:5px;text-align:left;">Landline</th>
       <th style="padding:5px;text-align:left;">Email</th>
    </thead>
    <tbody>
    <tr>
      <td style="padding:5px;"><?php echo $landlord_firstname." ".$landlord_lastname ?></td>
      <td style="padding:5px;"><?php echo $landlord_mobile; ?></td>
      <td style="padding:5px;"><?php echo $landlord_landline; ?></td>
      <td style="padding:5px;"><?php echo $landlord_email; ?></td>
    </tr>
    
    </tbody>
</table>
<p><strong style="color:#b4151b">Short Term Rental: </strong><?php echo ($holiday_rental==0)?'No':'Yes' ?></p>
<?php if($holiday_rental!=1){ ?>
    <p><strong style="color:#b4151b">Currently Vacant: </strong><?php echo ($prop_vacant==0)?'No':'Yes' ?></p>
    <?php if($prop_vacant==1){ ?>
        <p><strong style="color:#b4151b">Vacant Dates: </strong>From <?php echo $vacant_from; ?> To <?php echo $vacant_to; ?></p>
    <?php }else{ ?>
        <p><strong style="color:#b4151b">New Tenancy: </strong><?php echo ($is_new_tent==1)?'Yes':'No' ?></p>
        <?php if($is_new_tent==1){ ?>
            <p><strong style="color:#b4151b">New Tenancy Starts: </strong> <?php echo $new_ten_start; ?></p>
        <?php } ?>
    <?php } ?>
<?php } ?>

<?php
if( $holiday_rental == 1 ){ ?>
    <p>This property is marked as Short Term Rental. Please update the property to the appropriate IC service.</p>
<?php
}
?>
    
<p>&nbsp;</p>
<p>
    Kind Regards<br/>
    <?php echo config_item('company_full_name') ?>
</p>




<?php $this->load->view('emails/template/email_footer.php');