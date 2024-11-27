
<section class="box-typical box-typical-padding">

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/reports">Reports</a></li>
    <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo $uri; ?>"><?php echo $title; ?></a></li>
  </ol>
</nav>

<h5 class="m-t-lg with-border"><?php echo $title; ?>

    <?php
    if( $this->input->get_post('alarm_expiry') != '' ){ ?>

        <div class="float-right">
            <div class="col-sm-12">
            <section class="proj-page-section">
                <div class="proj-page-attach">
                    <i class="font-icon font-icon-pdf"></i>
                    <p class="name"><?php echo $title; ?></p>
                    <p>
                        <a href="
                            <?php echo $uri; ?>/?pdf=1&output_type=I&alarm_expiry=<?php echo $this->input->get_post('alarm_expiry'); ?>"
                            
                            target="blank"
                        >
                            View
                        </a>
                        
                        <a href="
                            <?php echo $uri; ?>/?pdf=1&output_type=D&alarm_expiry=<?php echo $this->input->get_post('alarm_expiry'); ?>"
                        >
                            Download
                        </a>
                    </p>
                </div>
            </section>
        </div>

    <?php
    }
    ?>    


</h5>

<!-- Header -->
<header class="box-typical-header">
    <div class="box-typical box-typical-padding">
    
    <?php
    $form_attr = array(
        'id' => 'jform'
    );
    echo form_open($uri,$form_attr);	
    ?>
        <div class="form-groupss row">
        
            <div class="float-left">
                <label for="exampleSelect" class="col-sm-12 form-control-label">Property Manager</label>
                <div class="col-sm-12" style="width:250px;">
                    <select class="form-control" id="pm_filter" name="pm_filter">
                        <option value="">---</option>
                        <?php                                    
                        foreach ( $pm_sql->result() as $pm_row ) {                                        
                        ?>
                            <option value="<?php echo $pm_row->aua_id ?>" <?php echo ( $pm_row->aua_id == $this->input->get_post('pm_filter') )?'selected':null; ?>>
                                <?php echo "{$pm_row->fname} {$pm_row->lname}"; ?>
                                <?php echo ( $pm_row->active !=1 )?'(Inactive)':null; ?>                                
                            </option>
                        <?php
                        }                           
                        ?>
                    </select>
                </div>
            </div>


            <div class="float-left">
                <label for="exampleSelect" class="col-sm-12 form-control-label">Expiry Year <span class="color-red">*</span></label>
                <div class="col-sm-12" style="width:250px;">
                    <select class="form-control" id="alarm_expiry" name="alarm_expiry">
                        <option value="">---</option>                                   
                        <?php                                    
                        $year_to = date('Y',strtotime('+10 years'));                                    
                        $year_from = date('Y');                                     
                        foreach ( range( $year_from, $year_to ) as $year ) { ?>
                            <option value="<?php echo $year; ?>" <?php echo ( $year == $this->input->get_post('alarm_expiry') )?'selected':null; ?>><?php echo $year; ?></option>                                    
                        <?php 
                        }
                        ?>
                    </select>
                </div>
            </div>
                                

            <div class="float-left">
                <label class="col-sm-12 form-control-label">&nbsp;</label>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-inline">Search</button>
                </div>
            </div>
            
        </div>
    </form>			
    </div>
</header>

<!-- list -->
<?php
// header filters
$filter_params = "
&from=".$this->input->get_post('from')."
&to=".$this->input->get_post('to')."
&pm_id=".$this->input->get_post('pm_id')."
&search=".$this->input->get_post('search');

// sort toggle
$toggle_sort = ( $sort == 'asc' )?'desc':'asc';	
?>
<div class="box-typical-body">
    <div class="table-responsive">
        <table id="dataTable" class="table table-hover main-table datatable">
            <thead>
                <tr>
                        <th>Address</th> 
                        <th>Property Manager</th>    
                        <th>Total Number of alarms in property</th>
                        <th>9v expiring</th>
                        <th>240v expiring</th>  
                </tr>
            </thead>
            <tbody>
                    <?php    
                    if( $this->input->get_post('alarm_expiry') !='' ){               
                        if( $list_sql->num_rows() > 0 ){                           
                            foreach( $list_sql->result() as $row ){ ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo $this->config->item('crm_link'); ?>/view_property_details.php?id=<?php echo $row->property_id; ?>">
                                            <?php echo "{$row->p_street_num} {$row->p_street_name} {$row->p_suburb} {$row->p_state}{$row->p_postcode} "; ?>
                                        </a>
                                    </td>
                                    <td><?php echo "{$row->properties_model_fname} {$row->properties_model_lname}"; ?></td>
                                    <td><?php echo $row->al_qty; ?></td>   
                                    <td><?php echo $row->al_9v_count; ?></td>
                                    <td><?php echo $row->al_240v_count; ?></td>                             
                                </tr>
                            <?php                    
                            } ?>                        
                        <?php
                        }else{ ?>
                            <tr><td colspan='5'>Empty</td></tr>
                        <?php
                        }
                    }else{ ?>
                        <tr><td colspan='5'>Please Filter by Expiry Year before Submitting</td></tr>
                    <?php
                    }               
                    ?>
                    <tr>
                        <td colspan="2"><b>TOTAL</b></td>
                        <td><b><?php echo $tot_exp_al; ?></b></td>   
                        <td><b><?php echo $tot_exp_9v; ?></b></td>  
                        <td><b><?php echo $tot_exp_240v; ?></b></td>                       
                    </tr>
                </tbody>
        </table>
    </div>
    
    <nav aria-label="Page navigation example" style="text-align:center">
        <?php echo $pagination; ?>
    </nav>
    
    <div class="pagi_count"><?php echo $pagi_count; ?></div>
    
</div><!--.box-typical-body-->


</section><!--.box-typical-->

        

<script>
jQuery(document).ready(function(){

    jQuery("#jform").submit(function(){

        var alarm_expiry = jQuery("#alarm_expiry").val();
        var error = '';

        if( alarm_expiry == '' ){
            error += 'Alarm Expiry field is required\n';
        }


        if( error != '' ){

            swal('',error,'error');
            return false;

        }else{
            return true;
        }        

    });

});
</script>