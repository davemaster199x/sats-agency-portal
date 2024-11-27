<?php $this->load->view('emails/template/email_header.php') ?>

<!-- CONTENT START HERE -->

<p><strong> <?php echo $job_type; ?> - Smoke Alarms</strong></p>
<p><strong style="color:#b4151b">From: </strong> <?php echo $agency_name; ?></p>
<p><strong style="color:#b4151b">Date: </strong> <?php echo date('d/m/Y \@ H:i:s') ?></p>
<p><strong style="color:#b4151b">Address: </strong> <?php echo $property_address; ?></p>
<hr/>

<b style="color:#b4151b">Tenants:</b>
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
   echo " No Tenants Found";
 } ?>

 <hr/>
 <b style="color:#b4151b">More Details:</b>
 <p>
 New Tenancy Starts: <?php echo $new_tenancy_start; ?> <br/>
Vacant From: <?php echo $vacant_from; ?><br/>
Vacant To: <?php echo $vacant_to; ?><br/>
Work Order: <?php echo $work_order; ?> <br/>
Comment: <?php echo $comment; ?>
</p>
<hr/>
<p><b style="color:#b4151b">Entered by:</b> <?php echo $agent_full_name; ?></p>


<!-- CONTENT END HERE -->


<?php $this->load->view('emails/template/email_footer.php');