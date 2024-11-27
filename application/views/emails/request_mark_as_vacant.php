<?php $this->load->view('emails/template/email_header.php') ?>


<p>Dear Customer Service Team,</p>
<p>
    <?php echo "{$user_row->fname} {$user_row->lname}"; ?> 
    from <?php echo $prop_row->agency_name; ?> has marked 
    <a href="<?php echo $this->config->item('crm_link'); ?>/view_property_details.php?id=<?php echo $prop_row->property_id; ?>">
        <?php echo "{$prop_row->p_address_1} {$prop_row->p_address_2} {$prop_row->p_address_3}"; ?>
    </a> as
    <?php
    if( $vacant_from_date != '' && $vacant_to_date != '' ){ // vacant start and end date exist ?>
        vacant from <?php echo $vacant_from_date; ?> till <?php echo $vacant_to_date; ?>.
    <?php
    }else if( $vacant_from_date != '' && $vacant_to_date == '' ){ // only vacant start exist ?>
        vacant from <?php echo $vacant_from_date; ?>, no end date was provided.
    <?php
    }else if( $vacant_from_date == '' && $vacant_to_date != '' ){ // only end date exist ?>
        vacant until <?php echo $vacant_to_date; ?>, no initial date was provided.
    <?php
    }
    ?>
</p>

<p>Please ensure the appropriate job is labelled with these vacant dates so that we can service the property in a timely manner.</p>
    
<p>&nbsp;</p>
<p>
    Kind Regards<br/>
    <?php echo config_item('company_full_name') ?>
</p>




<?php $this->load->view('emails/template/email_footer.php');