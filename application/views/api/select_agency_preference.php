<div class="box-typical-body">
    <div class="container">
        <form id="jform" action="/api/pme_save_preference">

            <h1>Lets personalise your Preferences</h1>   

            <table class="table">
                <tr>
                    <th>How would you like to receive your Statement of Compliances?</th>
                    <td>
                        <select class="form-control" name="recieve_compliance" id="recieve_compliance">
                            <option value="1">Directly into <?php echo $agency_row->api_name; ?></option>
                            <option value="0">Via Email</option>       
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>How would you like to receive your Invoices?</th>
                    <td>
                        <select class="form-control" name="recieve_invoice" id="recieve_invoice">
                            <option value="1">Directly into <?php echo $agency_row->api_name; ?></option>
                            <option value="0">Via Email</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>What would you like to do with $0 Invoices?</th>
                    <td>
                        <select class="form-control" name="free_invoice" id="free_invoice">
                            <option value="1">Ignore them</option>
                            <option value="0">Send them</option>                            
                        </select>
                    </td>
                </tr>
            </table> 

            <div class="row text-right mt-3">
                <div class="col">
                    <input type="hidden" name="api" value="<?php echo $agency_row->agency_api_id; ?>" />
                    <button type="submit" class="btn">Update</button>
                </div>
            </div>            

        </form>
    </div>
</div>
<script>
jQuery(document).ready(function(){

    jQuery("#jform").submit(function(){

        var recieve_compliance = jQuery("#recieve_compliance").val();
        var recieve_invoice = jQuery("#recieve_invoice").val();
        var free_invoice = jQuery("#free_invoice").val();
        var error = '';

        if( recieve_compliance == '' ){
            error += "'Statement of Compliances' preference is required\n";
        }

        if( recieve_invoice == '' ){
            error += "'Invoices' preference is required\n";
        }

        if( recieve_compliance == '' ){
            error += "'$0 Invoices' preference is required\n";
        }

        if( error != '' ){ // error

            swal('',error,'error');
            return false;

        }else{

            return true;

        }

    });
    
});
</script>