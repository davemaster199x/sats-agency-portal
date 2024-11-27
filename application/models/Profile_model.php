<?php
class Profile_model extends CI_Model {

        public function __construct(){
            parent::__construct();
            $this->load->database();      
        }
        
        public function get_agent_name_by_id($id){
            
            $query =  $this->db->get_where('agency', array('agency_id' => $id));
            $row  = $query->row();
            return $row->agency_name;

        }

        public function get_agency($agency_id){
        
            $this->db->select('*, a.country_id, a.login_id as aUsername, a.password as aPassword, sa.FirstName as saFirstname, sa.LastName as saLastname, sa.Email as saEmail, sa.ContactNumber as saContactNumber, am.maintenance_id, m.name as mName', false);
            $this->db->from('agency as a');
            $this->db->join('staff_accounts as sa', 'sa.StaffID = a.salesrep', 'left');
            $this->db->join('agency_maintenance as am', 'am.agency_id = a.agency_id', 'left');
            $this->db->join('maintenance as m', 'm.maintenance_id = am.maintenance_id', 'left');
            $this->db->where('a.agency_id',$agency_id);
            $query = $this->db->get();
            if($query->num_rows()>0){
                return $query->row();
            }else{
                return false;
            }

        }

        public function del_property_manager($hid_pm_id, $agency_id){

            $this->db->where('agency_user_account_id',$hid_pm_id);
            $this->db->where('agency_id', $agency_id);
            $this->db->delete('agency_user_accounts');
            if($this->db->affected_rows()>0){
                return true;
            }else{
                return false;
            }

        }

        public function update_profile($id,$data){

            $this->db->where('agency_id', $id);
            $this->db->update('agency', $data);
            if($this->db->affected_rows()>0){
                return true;
            }else{
                return false;
            }
            
        }

        public function update_property_batch(){

            $hid_pm_id = $this->input->post('hid_pm_id');
    
            for($i = 0; $i < count($hid_pm_id); $i++){
                $post_array[] = array(
                    'property_managers_id' => $this->input->post('hid_pm_id')[$i],
                    'name' => $this->input->post('update_pm_name')[$i],
                    'pm_email' => $this->input->post('update_pm_email')[$i]
                );
            }
          
            $this->db->update_batch('property_managers', $post_array, 'property_managers_id');
            if($this->db->affected_rows()>0){
                return true;
            }else{
                return false;
            }
          
        }

        public function add_property_batch(){

            $pm_name = $this->input->post('add_pm_name');

                for($i = 0;$i < count($pm_name); $i++){
                    $post_array[] = array(
                        'name' => $this->input->post('add_pm_name')[$i],
                        'pm_email' => $this->input->post('add_pm_email')[$i],
                        'agency_id' => $this->session->agency_id
                    );
                }
            
                $this->db->insert_batch('property_managers', $post_array);
                if($this->db->affected_rows()>0){
                    return true;
                }else{
                    return false;
                }
            
        }

      

        
    
}
