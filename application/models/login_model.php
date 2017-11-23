<?php

class Login_model extends CI_Model {
	
	private $mysqli = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->persons = $this->load->database('persons', true);
	}

	public function check_user($username, $password){
		
		$params = array($username,$password);
	
		$sql = "SELECT uat.id, uat.person_id, pt.first_name, pt.middle_name, pt.last_name, pt.person_status, pit.employee_no
				FROM user_account_tab uat
				LEFT JOIN person_tab pt
				ON uat.person_id = pt.id
				LEFT JOIN person_info_tab pit
				ON pt.id = pit.person_id
				WHERE uat.username = ? AND uat.password = ? LIMIT 1";
		$rows = $this->persons->query($sql,$params);
		
		if($rows->num_rows() > 0){
			$data = $rows->result();
			$data = $data[0];
			$user_data = array(
				'user_id' => $data->person_id,
				'firstname' => $data->first_name,
				'lastname' => $data->last_name,
				'fullname' => $data->first_name . ' ' . $data->last_name,
				'employee_number' => $data->employee_no
			);
			$this->session->set_userdata($user_data);
			return true;
		}
		else {
			return false;
		}
	}
	
	public function check_access($person_id, $system){
			
		$params = array($person_id, $system);
		
		$sql = "SELECT uat.id, utt.user_type
				FROM user_account_tab uat
				LEFT JOIN user_access_tab uat2
				ON uat.id = uat2.user_account_id
				LEFT JOIN system_tab st
				ON uat2.system_id = st.id
				LEFT JOIN user_type_tab utt
				ON uat2.user_type_id = utt.id
				WHERE uat.person_id = ?
				AND st.system = ?
				LIMIT 1";
				
		$rows = $this->persons->query($sql,$params);
		
		if($rows->num_rows() > 0){
			$data = $rows->result();
			$data = $data[0];
			$user_data = array(
				'user_type' => $data->user_type
			);
			$this->session->set_userdata($user_data);
			return true;
		}
		else {
			return false;
		}
	}
}
