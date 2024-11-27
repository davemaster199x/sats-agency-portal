<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property       User_account_model $user_account_model
 * @property-read  string $myReadOnlyProperty
 * @property-write string $myWriteOnlyProperty
 */
class User_accounts extends CI_Controller {
	protected $user_photo_upload_path = '/uploads/user_accounts/photo';
	protected $default_avatar = '/images/avatar-2-64.png';

	public function __construct(){
		parent::__construct();
		$this->load->model('user_accounts_model');
		$this->load->model('jobs_model');
		$this->load->library('pagination');
		$this->load->model('properties_model');
		$this->load->model('mixed_db_model');
		$this->load->helper('url');
		$this->load->model('logs_model');
    }

	public function index(){

		$data['title'] = 'User Accounts';

		// property manager dropdown list
		$data['property_manager_list'] = $this->properties_model->get_property_manager_by_agency($this->session->agency_id);

		$agency_id = $this->session->userdata('agency_id');
		$view = $this->input->get('view');

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		// paginated results
		$params = array(
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`,
				aua.`email`,
				auat.`user_type_name`,
				auat.`agency_user_account_type_id`,
				aua.`photo`,
				aua.`active`
			',
			'agency_id' => $agency_id,
			'limit' => $per_page,
			'offset' => $offset
		);

		// active
		if( $view != 'all' ){
			$params['active'] = 1;
		}

		$query = $this->user_accounts_model->get_user_accounts($params);
		$data['users'] = $query->result();

		// all rows
		$params = array(
			'sel_query' => 'aua.`agency_user_account_id`',
			'agency_id' => $agency_id
		);

		// active
		if( $view != 'all' ){
			$params['active'] = 1;
		}

		$query = $this->user_accounts_model->get_user_accounts($params);
		$total_rows = $query->num_rows();

		// pagination settings
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'offset';
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $per_page;
		$config['base_url'] = "/user_accounts/index/?view={$view}";
		$data['user_photo_upload_path'] = $this->user_photo_upload_path;
		$data['default_avatar'] = $this->default_avatar;

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();

		$pc_params = array(
			'total_rows' => $total_rows,
			'offset' => $offset,
			'per_page' => $per_page
		);
		$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);
		$data['typeOptions'] = $this->db->select('*')->from('agency_user_account_types')->get()->result();

		$params =array('aua_id'=>$this->session->aua_id);
		$data['userType'] = $this->user_accounts_model->get_user_accounts($params)->row()->user_type ;

		$this->load->view('templates/home_header',$data);
        $this->load->view('user_accounts/index',$data);
		$this->load->view('templates/home_footer');
	}

	public function edit_multiple_pm (){

	}



	public function logins($aua_id=null){

		$agency_id = $this->session->agency_id;

		if($aua_id && $aua_id!=""){
			if($this->session->aua_id==$aua_id){
				$data['title'] =  "Your Account Logins";
			}else{
				$params = array(
					'sel_query' => '
						aua.`fname`,
						aua.`lname`,
					',
					'aua_id' => $aua_id,
					'agency_id' => $this->session->agency_id
				);
				$user_sql = $this->user_accounts_model->get_user_accounts($params)->row_array();
				$user_name = $user_sql['fname'];
				//$ttname = $user_name.'\''.($user_name[strlen($user_name) - 1] != 's' ? 's' : ''); //reserved
				$data['title'] = ucfirst($user_name)."'s Logins";
			}
		}else{
			$data['title'] = "Logins";
		}
		

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get('offset');

		// paginated results
		$params = array(
			'sel_query' => '
				aul.`date_created`,
				aul.`ip`,
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`,
				aua.`photo`
			',
			'agency_id' => $agency_id,
			'limit' => $per_page,
			'offset' => $offset,
			'aua_id' => $aua_id
		);
		$query = $this->user_accounts_model->get_all_user_logs($params);
		$data['users'] = $query->result();

		// all rows
		$params = array(
			'sel_query' => '
				aul.`agency_user_login_id`
			',
			'agency_id' => $agency_id,
			'aua_id' => $aua_id
		);
		$query = $this->user_accounts_model->get_all_user_logs($params);
		$total_rows = $query->num_rows();

		// pagination
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $per_page;
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'offset';
		$config['base_url'] = "/user_accounts/logins/{$aua_id}";
		$data['user_photo_upload_path'] = $this->user_photo_upload_path;
		$data['default_avatar'] = $this->default_avatar;

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();

		$pc_params = array(
			'total_rows' => $total_rows,
			'offset' => $offset,
			'per_page' => $per_page
		);
		$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);

		$this->load->view('templates/home_header',$data);
        $this->load->view('user_accounts/logins',$data);
		$this->load->view('templates/home_footer');
	}


	public function add(){

		// WIP:
		// - add CI repopulate

		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_if_email_exist');
		$this->form_validation->set_rules('fname', 'First Name', 'required');
		$this->form_validation->set_rules('lname', 'Last Name', 'required');
		$this->form_validation->set_rules('user_type', 'User Type', 'required');

		if ( $this->form_validation->run() == true ){
			if( $this->user_accounts_model->save_user_account() ){
				$data['save_success'] = true;
			}
		}

		$data['title'] = 'Add User Accounts';
		$data['user_types'] = $this->user_accounts_model->get_all_user_types();

		$this->load->view('templates/home_header',$data);
		$this->load->view('user_accounts/add',$data);
		$this->load->view('templates/home_footer');

	}

	public function edit($aua_id){

		// WIP:
		// - add a check if email exist, can't use the current one add user bec it will not accept the current email used. it will detect it as already exist

		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('fname', 'First Name', 'required');
		$this->form_validation->set_rules('lname', 'Last Name', 'required');

		if( $this->input->post('edit_pw_flag') == 1 ){
				$this->form_validation->set_rules('password', 'Password', 'required');
		}

		if ( $this->form_validation->run() == true ){
			$data['update_success'] = false;
			$update_success = false;
			$upload_success = false;

			$data_crop = $this->input->post('crop_image_base64');

			if($data_crop && $data_crop!=""){
				list($type, $data_crop) = explode(';', $data_crop);
				list(, $data_crop)      = explode(',', $data_crop);
				$data_crop = base64_decode($data_crop);
			}


			$image_name = 'crop_image'.rand().date('Ymdhi').'.png';
			$temp_crop_file = $_SERVER['DOCUMENT_ROOT']."/uploads/temp/{$image_name}";
			$upload_folder = $_SERVER['DOCUMENT_ROOT']."/uploads/user_accounts/photo/";
			$upload_path = "{$upload_folder}{$image_name}";

			// Allow certain file formats
			$imageFileType = strtolower(pathinfo($temp_crop_file,PATHINFO_EXTENSION));

			if(
			$imageFileType != "jpg" &&
			$imageFileType != "png" &&
			$imageFileType != "jpeg" &&
			$imageFileType != "gif"
			){
				$data['upload_image_error'] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
			}else{

				// upload
				if( file_put_contents($temp_crop_file, $data_crop) ){

					// move cropped photo from temp folder to user accounts folder
					if( rename($temp_crop_file,$upload_path) ){


						// get existing photo
						$this->db->select('photo');
						$this->db->from('agency_user_accounts');
						$this->db->where('agency_user_account_id', $aua_id);
						$user_sql = $this->db->get();
						$user_row = $user_sql->row();

						// delete previous photo if it exist
						if( isset($user_row->photo) && $user_row->photo != '' ){
							$this->jcclass->delete_image($upload_folder.$user_row->photo);
						}

						// delete temp cropped photo
						$this->jcclass->delete_image($temp_crop_file);

						// save photo file name to database
						$query_data = array(
								'photo' => $image_name
						);

						$this->db->where('agency_user_account_id', $aua_id);
						$this->db->update('agency_user_accounts', $query_data);


						//added by gherx start
						// logs for photo
						$title = 4; // User Account Updated
						$details = "{agency_user:{$aua_id}}'s photo updated";

						$params = array(
							'title' => $title,
							'details' => $details,
							'display_in_portal' => 1,
							'display_in_vad' => 1,
							'agency_id' => $this->session->agency_id,
							'created_by' => $this->session->aua_id
						);

						$this->jcclass->insert_log($params);
						//added by gherx end

						$upload_success = true;

					}
				}
			}

			// update user
			if( $this->user_accounts_model->update_user_account() ){
				$update_success = true;
			}

			if( $update_success == true || $upload_success == true ){
				$data['update_success'] = true;

				//added by gherx
				$this->session->set_flashdata(array('success_msg'=>'User Updated','status'=>'success'));
				redirect('user_accounts/my_profile/'.$aua_id);
				//added by gherx end
			}else{
				//added by gherx
				$this->session->set_flashdata(array('error_msg'=>'No changes made or crop picture first before submission','status'=>'error'));
				redirect('user_accounts/my_profile/'.$aua_id);
				//added by gherx end
			}

		}


	}

	public function update_password(){

		$aua_id = $this->input->post('aua_id');
		$password = $this->input->post('password');

		$password_hash = password_hash ( $password, PASSWORD_DEFAULT );

		$data = array(
				'password' => $password_hash
		);

		$this->db->where('agency_user_account_id', $aua_id);
		$this->db->update('agency_user_accounts', $data);

	}


	public function check_only_one_default(){

		$agency_id = $this->input->post('agency_id');
		$where_arr = array(
			'agency_id' => $agency_id,
			'user_type' => 1
		);
		$query = $this->db->get_where('agency_user_accounts', $where_arr);
		echo $query->num_rows();

	}


	public function pm_property_check(){

		$aua_id = $this->input->post('aua_id');
		$prop_found = 0;

		// search for properties managed by this PM
		$this->db->select('property_id');
		$this->db->from('property');
		$this->db->where('deleted', 0);
		$this->db->where('pm_id_new', $aua_id);
		$this->db->where('agency_id', $this->session->agency_id);
		$query = $this->db->get();

		echo $query->num_rows();

	}

	public function delete_user(){

		$aua_id = $this->input->post('aua_id');
		$success = 0;

		// delete user
		if( $this->user_accounts_model->delete_user_account($aua_id) == true ){
			$success = 1;
		}else{
			$success = 0;
		}

		echo $success;

	}


	public function delete_user_and_clear_pm_prop(){

		$aua_id = $this->input->post('aua_id');
		$error = 0;
		$success = 0;

		// delete user
		if( $this->user_accounts_model->delete_user_account($aua_id) == false ){
			$error = 1;
		}

		// clear all PM attached to Properties
		if( $this->properties_model->clear_pm_prop($aua_id) == false ){
			$error = 1;
		}

		if( $error == 0 ){
			$success = 1;
		}else{
			$success = 0;
		}

		echo $success;

	}

	public function my_profile($aua_id){

		//$aua_id = $this->session->aua_id;
		$data['photo_path'] = $this->user_photo_upload_path;
		$data['default_avatar'] = $this->default_avatar;

		$popup_2fa = $this->input->get_post('popup_2fa');

		$params = array(
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`,
				aua.`email`,
				aua.`job_title`,
				aua.`photo`,
				aua.`active`,
				aua.user_type,
				aua.phone,

				auat.`user_type_name`,

				a.`agency_name`
			',
			'aua_id' => $aua_id,
			'agency_id' => $this->session->agency_id
		);

		$user_sql = $this->user_accounts_model->get_user_accounts($params);

		if( $user_sql->num_rows() > 0 ){

			$user = $user_sql->row();
			$user_fname = ucfirst($user->fname)."'s";

			$dynamic_user_txt = ($aua_id==$this->session->aua_id)?'My':$user_fname;
			$data['title'] = $dynamic_user_txt.' Profile';
			$data['user'] = $user;

			// get active jobs
			$query_sql = $this->get_active_jobs($aua_id);
			$row = $query_sql->row();
			$data['active_jobs'] = $row->jcount;
			$data['aua_id'] = $aua_id;


			$data['dynamic_user_txt'] =  $dynamic_user_txt;

			//get property count
			$condi = array();
			$params = array( 'pm_id' => $aua_id );
			if(!empty($this->properties_model->get_property_list($this->session->agency_id,$condi,$params))){
				$data['prop_count'] = count($this->properties_model->get_property_list($this->session->agency_id,$condi,$params));
			}else{
				$data['prop_count'] = 0;
			}


			// last password save
			//$custom_where = "l.`details` LIKE '%password has been updated%'";
			$log_title = 24; // Password Updated
			$params = array(
				'sel_query' => '
					l.`created_date`
				',
				'deleted' => 0,
				'created_by' => $aua_id,
				'log_title' => $log_title,
				'display_query' => 0,
				'limit' => 1,
				'offset' => 0,
				'sort_list' => array(
					array(
						'order_by' => 'l.created_date',
						'sort' => 'DESC'
					)
				)
			);
			$logs_sql = $this->logs_model->get_logs($params);
			if( $logs_sql->num_rows() > 0 ){
				$logs_row = $logs_sql->row();
				$data['last_pass_update'] = $logs_row->created_date;
			}

			// get last login
			$params = array(
				'sel_query' => '
					aul.`date_created`
				',
				'limit' => 1,
				'offset' => 0,
				'aua_id' => $aua_id,
				'sort_list' => array(
					array(
						'order_by' => 'aul.date_created',
						'sort' => 'DESC'
					)
				)
			);
			$aul_sql = $this->user_accounts_model->get_all_user_logs($params);
			if( $aul_sql->num_rows() > 0 ){
				$aul_row = $aul_sql->row();
				$data['last_login'] = $aul_row->date_created;
			}

			// get logged in user, user type
			$logged_user_params = array(
				'sel_query' => '
					aua.user_type
				',
				'aua_id' => $this->session->aua_id
			);

			$logged_user_sql = $this->user_accounts_model->get_user_accounts($logged_user_params);
			$logged_user_row = $logged_user_sql->row();
			$data['logged_user_ut'] = $logged_user_row->user_type;

			// get 2fa settings
			$user_2fa_sql = $this->db->query("
			SELECT 
				`id`,
				`type`,
				`send_to`
			FROM `agency_user_2fa`
			WHERE `user_id` = {$this->session->aua_id}
			AND `active` = 1
			");
			$data['user_2fa_row'] = $user_2fa_sql->row();

			// offer uses 2FA
			$data['popup_2fa'] = $popup_2fa;

			$this->load->view('templates/home_header', $data);
			$this->load->view('user_accounts/my_profile', $data);
			$this->load->view('templates/home_footer');

		}else{
			redirect('/home');
		}

	}

	// get active jobs by aua_id
	public function get_active_jobs($aua_id){

		$sel_query = '
			COUNT(j.`id`) AS jcount
		';
		$query_params = array(
			'sel_query' => $sel_query,
			'del_job' => 0,
			'p_deleted' => 0,
			'a_status' => 'active',
			'pm_id' => $aua_id,
			'agency_id' => $this->session->agency_id,
			'custom_where' => "(
				j.`status` != 'Completed' AND
				j.`status` != 'Pending' AND
				j.`status` != 'Cancelled'
			)
			",
			'display_query' => 0
		);
		return $this->jobs_model->get_jobs($query_params);

	}

	public function getPhotoPath(){
		return $this->user_photo_upload_path;
	}


	public function check_if_email_exist($email){
		if ( $this->user_accounts_model->check_if_email_exist($email) == true ){
			$this->form_validation->set_message('check_if_email_exist', '{field} already exist');
			return false;
		}else{
			return true;
		}
	}





	public function reset_password($aua_id) {

		// send reset password email
		if( $this->user_accounts_model->send_reset_password_email($aua_id) ){
			$this->session->set_flashdata('reset_password', 1);
			redirect("/user_accounts/my_profile/{$aua_id}");
		}

	}

	public function reset_password_form(){

		$this->load->library('email');

		// header
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$data = [];

		if ( $this->form_validation->run() == true ){

			$email = $this->input->post('email');
			$user = $this->user_accounts_model->get_user_account_via_email($email);

			if( isset($user) ){

				$aua_id = $user->agency_user_account_id;
				// send reset password email
				if( $this->user_accounts_model->send_reset_password_email($aua_id) ){
					$this->session->set_flashdata('reset_pass_success', 1);
				}

			}else{
				$this->session->set_flashdata('email_not_found', 1);
			}

		}

		$data['title'] = 'Reset Password';
		$this->load->view('templates/main_header', $data);
		$this->load->view('user_accounts/reset_password',$data);
		$this->load->view('templates/main_footer');

	}

	public function set_password(){

		$this->load->helper('url');

		$reset_password_code = $this->input->get('sp');
		$no_expiry = $this->input->get('no_expiry');

		$data['title'] = "Set Password";

		// get user data
		$params = array(
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`reset_password_code`,
				aua.`reset_password_code_ts`
			',
			'reset_password_code' => $reset_password_code
		);
		$user_sql = $this->user_accounts_model->get_user_accounts($params);

		// user found
		if( $user_sql->num_rows() > 0 ){

			$user_row = $user_sql->row();

			if( $no_expiry == 1 ){ // no expiry

				$data['user_id'] = $user_row->agency_user_account_id;
				$this->load->view('templates/main_header', $data);
				$this->load->view('user_accounts/set_password',$data);
				$this->load->view('templates/main_footer');

			}else{ // has expiration
				
				// valid upto 2 hours
				//Extend validity up to 100 hours for SAS Onboarding purpose will revert back to 2 hours after
				$valid_until = date('Y-m-d H:i:s',strtotime($user_row->reset_password_code_ts.'+100 hour'));
				$now = date('Y-m-d H:i:s');

				// if reset code link is still valid
				if( ( isset($user_row->reset_password_code) && $user_row->reset_password_code != '' ) && $now <= $valid_until ){

					$data['user_id'] = $user_row->agency_user_account_id;
					$this->load->view('templates/main_header', $data);
					$this->load->view('user_accounts/set_password',$data);
					$this->load->view('templates/main_footer');

				}else{ // link expired

					$data['title'] = "Error";

					$data['error_header'] = "The password link you are trying to use has expired";

					// get agent number from countries table
					$countries_sql = $this->db->query("
					SELECT `agent_number`
					FROM `countries`
					WHERE `country_id` = {$_ENV['COMPANY_COUNTRY_ID']}			
					");
					$countries_row = $countries_sql->row();
					$agent_number = $countries_row->agent_number;

					$data['error_txt'] = "Please contact us at <a href='mailto:{$_ENV['COMPANY_EMAIL']}'>{$_ENV['COMPANY_EMAIL']}</a> or call us on {$agent_number}";

					$this->load->view('errors/template/error_header.php', $data);
					$this->load->view('generic/error_page', $data);
					$this->load->view('errors/template/error_footer.php', $data);

				}

			}





		}else{ // reset password link has already been used

			$data['title'] = "Error";

			$data['error_header'] = "ERROR";
			$data['error_txt'] = "Reset password code has been used. Please generate another one to reset password";

			$this->load->view('errors/template/error_header.php', $data);
			$this->load->view('generic/error_page', $data);
			$this->load->view('errors/template/error_footer.php', $data);

		}



	}

	public function save_password() {

		// update password
		$this->user_accounts_model->update_user_password();

		// user ID
		$user_id = $this->input->get_post('user_id');

		// get user data via user ID
		$user_account_params = array(
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`agency_id`,
				a.`country_id`
			',
			'aua_id' => $user_id,
			'active' => 1
		);

		// get user details
		$user_account_sql = $this->user_accounts_model->get_user_accounts($user_account_params);

		// user data
		$user_account = $user_account_sql->row();
		$agency_id = $user_account->agency_id;
		$country_id = $user_account->country_id;

		// create session array
		$sess_arr = array(
			'agency_id' => $agency_id,
			'country_id' => $country_id,
			'aua_id' => $user_id
		);

		// set session
		$this->session->set_userdata($sess_arr);

		// redirect to homepage
		redirect('home');


	}


	public function delete_user_photo(){

		$aua_id = $this->input->post('aua_id');
		$user_photo = $this->config->item('user_photo');
		$server_root = $_SERVER['DOCUMENT_ROOT'];

		if( isset($aua_id) && $aua_id > 0 ){

			$params = array(
				'sel_query' => '
					aua.`agency_user_account_id`,
					aua.`fname`,
					aua.`lname`,
					aua.`email`,
					auat.`user_type_name`,
					aua.`photo`
				',
				'aua_id' => $aua_id,
				'active' => 1
			);
			$query = $this->user_accounts_model->get_user_accounts($params);

			$row = $query->row();

			// delete photo in database
			if( $this->user_accounts_model->delete_user_photo($aua_id) ){

				// get full absolute path
				$photo_full = "{$server_root}{$user_photo}/{$row->photo}";
				// delete
				$this->jcclass->delete_image($photo_full);

			}





		}

	}



	public function reactivate_user(){

		$aua_id = $this->input->post('aua_id');

		// get user account
		$aua_params = array(
			'aua_id' => $aua_id,
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`
			'
		);
		$user_sql = $this->user_accounts_model->get_user_accounts($aua_params);
		$user = $user_sql->row();

		// user name
		$user_fullname = "{$user->fname} {$user->lname}";


		$title = 16; // Account Restored
		$details = "{agency_user:{$aua_id}}'s user account has been restored";

		$params = array(
			'title' => $title,
			'details' => $details,
			'display_in_portal' => 1,
			'display_in_vad' => 1,
			'agency_id' => $this->session->agency_id,
			'created_by' => $this->session->aua_id
		);

		$this->jcclass->insert_log($params);

		// reactivate
		$data = array(
			'active' => 1
		);
		$this->db->where('agency_user_account_id', $aua_id);
		$this->db->update('agency_user_accounts', $data);

	}


	public function logout(){
		$this->session->sess_destroy();
		redirect('/');
	}

	//edit profile inline (ajax)
	public function profile_inline_edit(){

		$data['status'] = false;

		$edit_profile_query = $this->user_accounts_model->update_user_account();

		if($edit_profile_query){
			$data['status'] = true;
		}

		echo json_encode($data);

	}


	public function hide_welcome_message(){

		$data = array(
				'hide_welcome_msg' => 1
		);
		$this->db->where('agency_user_account_id', $this->session->aua_id);
		$this->db->update('agency_user_accounts', $data);

	}

	public function updateUserType(){
		$data['status'] = false;
		$aua_id = $this->input->post('aua_id');
		$user_type = $this->input->post('user_type');

		$params = array('user_type'=>$user_type);
		$this->db->where('agency_user_account_id',$aua_id);
		$this->db->update('agency_user_accounts',$params);
		if($this->db->affected_rows()>0){

			//insert log
			$title = 28; // User Type Updated
			$details = "{agency_user:{$aua_id}}'s User Type updated";
			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'display_in_vad' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);
			$this->jcclass->insert_log($params);

			$data['status'] = TRUE;

		}else{
			$data['status'] = FALSE;
		}
		echo json_encode($data);
	}


	public function send_2fa_code(){

		$user_id = $this->input->post('aua_id');
		$twofa_type = $this->input->post('twofa_type');
		$twofa_send_to = str_replace(' ', '', $this->input->post('twofa_send_to')); // remove spaces

		// check if 2FA of user already exist
		$sql = $this->db->query("
		SELECT COUNT(`id`) AS au2_count
		FROM `agency_user_2fa`
		WHERE `user_id` = {$user_id}
		");
		$row = $sql->row();

		if( $row->au2_count > 0 ){ // exist, update

			// update count
            $update_data = array(
                'type' => $twofa_type,
				'send_to' => $twofa_send_to,
            );            
            $this->db->where('user_id', $user_id);
            $this->db->update('agency_user_2fa', $update_data);

		}else{ // new, insert

			$insert_data = array(
                'type' => $twofa_type,
                'send_to' => $twofa_send_to,
                'user_id' => $user_id
            );            
            $this->db->insert('agency_user_2fa', $insert_data);

		}

		if( $twofa_type == 1 ){ // 2FA type mobile

			// if user account(agency_user_accounts) has empty mobile(`phone`), update it with the one used here
			$aua_sql = $this->db->query("
			SELECT `phone`
			FROM `agency_user_accounts`
			WHERE `agency_user_account_id` = {$user_id}
			");
			$aua_row = $aua_sql->row();

			if( $aua_row->phone == '' ){ 

				// update 
				$update_data = array(
					'phone' => $twofa_send_to
				);            
				$this->db->where('agency_user_account_id ', $user_id);
				$this->db->update('agency_user_accounts', $update_data);

			}

		}		
		
		// send 2FA code
		$user_2fa_params =  array('aua_id' => $user_id);
		$this->system_model->send_2fa_code($user_2fa_params);

	}


	public function confirm_2fa_code(){

		$user_id = $this->input->post('user_id');
		$twofa_code = trim($this->input->post('twofa_code'));

		if( $user_id > 0 ){

			// confirm
			$confirm_2fa_params = array(
				'user_id' => $user_id,
				'user_2fa_code' => $twofa_code
			);
			$ret_arr = $this->system_model->confirm_2fa_code($confirm_2fa_params);

			if( $ret_arr['success'] == 1 ){ // success

				// enable
				$update_data = array(
					'active' => 1
				);            
				$this->db->where('user_id', $user_id);
				$this->db->update('agency_user_2fa', $update_data);

			}

			echo json_encode($ret_arr);
			

		}		

	}


	public function delete_2fa(){

		$user_id = $this->input->get_post('user_id');

		if( $user_id > 0 ){

			$this->db->where('user_id', $user_id);
			$this->db->delete('agency_user_2fa');

		}		
		
	}


}
