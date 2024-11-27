<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Property_Tree extends CI_Controller {

    function __construct(){

        parent::__construct();
		$this->load->model('property_tree_model');
        
    }


    public function display_agency_preference(){

        $agency_id = $this->input->get_post('agency_id');
        $table = null;

        // get creditors
        $ret_obj = $this->property_tree_model->get_creditors($agency_id);
        if( $ret_obj->httpcode == 200 ){
            $creditors_json = $ret_obj->json_decoded_response;
        } 
        
        // get account
        $ret_obj = $this->property_tree_model->get_accounts($agency_id);
        if( $ret_obj->httpcode == 200 ){
            $accounts_json = $ret_obj->json_decoded_response;
        }

        // get property compliance categories
        $ret_obj = $this->property_tree_model->property_compliance_categories($agency_id);
        if( $ret_obj->httpcode == 200 ){
            $prop_comp_cat_json = $ret_obj->json_decoded_response;
        }

        // get agency preference
        $pt_ap_sql = $this->db->query("
        SELECT *
        FROM `propertytree_agency_preference`
        WHERE `agency_id` = {$agency_id}
        AND `active` = 1
        ");
        $pt_ap_row = $pt_ap_sql->row();

        $table .= "
        <table class='table table-borderless'>
            <tr>
                <td>Creditor ( Select {$this->config->item('COMPANY_FULL_NAME')} )</td>
                <td>
                    <select id='pt_creditor' class='form-control' required>
                        <option value=''>---</option>
                        ";
                        foreach( $creditors_json as $creditors ){

                            $table .= "<option value='{$creditors->creditor_id}' ".( ( $creditors->creditor_id == $pt_ap_row->creditor )?'selected':null ).">{$creditors->name}</option>";
                            
                        }
                        $table .= "
                    </select>
                </td>
            </tr>
            <tr>
                <td>Account ( Select Fire Protection )</td>
                <td>
                    <select id='pt_account' class='form-control' required>
                        <option value=''>---</option>
                        ";
                        foreach( $accounts_json as $accounts ){

                            $table .= "<option value='{$accounts->id}' ".( ( $accounts->id == $pt_ap_row->account )?'selected':null ).">{$accounts->name}</option>";
                            
                        }
                        $table .= "
                    </select>
                </td>
            </tr>
            <tr>
                <td>Property Compliance Category ( Select Smoke Alarms )</td>
                <td>
                    <select id='pt_prop_comp_cat' class='form-control' required>
                        <option value=''>---</option>
                        ";
                        foreach( $prop_comp_cat_json as $prop_comp_cat ){

                            $table .= "<option value='{$prop_comp_cat->category_id}' ".( ( $prop_comp_cat->category_id == $pt_ap_row->prop_comp_cat )?'selected':null ).">{$prop_comp_cat->category_name}</option>";
                            
                        }
                        $table .= "
                    </select>
                </td>
            </tr>
        </table>
        ";

        echo $table;

    }
    

    public function save_agency_preference(){

        $agency_id = $this->input->get_post('agency_id');
        $creditor = $this->input->get_post('creditor');
        $account = $this->input->get_post('account');
        $prop_comp_cat = $this->input->get_post('prop_comp_cat');

        if( $agency_id > 0 ){

            $sql = $this->db->query("
            SELECT COUNT(pt_ap_id) AS pt_ap_count
            FROM propertytree_agency_preference
            WHERE `agency_id` = {$agency_id}
            ");
            
            if( $sql->row()->pt_ap_count ){ // already exist, update

                $update_data = array(
                    'creditor' => $creditor,
                    'account' => $account,
                    'prop_comp_cat' => $prop_comp_cat
                );                
                $this->db->where('agency_id', $agency_id);
                $this->db->update('propertytree_agency_preference', $update_data);
                

            }else{ // new, insert

                // insert agency/auth token
                $insert_data = array(
                    'agency_id' => $agency_id,
                    'creditor' => $creditor,
                    'account' => $account,
                    'prop_comp_cat' => $prop_comp_cat
                );            
                $this->db->insert('propertytree_agency_preference', $insert_data);           

            }  
            
            // insert log
            $params = array(
                'title' => 91, // PropertyTree API
                'details' => 'Fully connected to Property Tree API',
                'display_in_vad' => 1,
                'agency_id' => $this->session->agency_id,
                'created_by' => $this->session->aua_id
            );    
            $this->jcclass->insert_log($params);

        }        

    }

    
}