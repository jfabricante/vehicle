<?php 
class Login extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('form');
	}
	
	public function index(){
		$data['message'] = '';
		$this->load->view('login_view', $data);
	}
	
	public function check_login(){
		$this->load->model('login_model');
		$this->load->library('form_validation');
		$config = array(
					array(
						'field' => 'username',
						'label' => 'Username',
						'rules' => 'trim|required',
						'errors' => array(
										'required' => '* Please enter your username',
									),
						),
					array(
						'field' => 'password',
						'label' => 'Password',
						'rules' => 'trim|required',
						'errors' => array(
										'required' => '* Please enter your password',
									)
						)
					);
		$this->form_validation->set_rules($config);
		if($this->form_validation->run() == TRUE){
			$is_user = $this->login_model->check_user($this->input->post('username'),$this->input->post('password'));
			if($is_user){
				$is_having_access = $this->login_model->check_access($this->session->userdata('user_id'), 'IVS');
				if($is_having_access){
					if($this->session->userdata('user_type') == 'sales 1'){
						redirect('forsale/summary');
					}
					else if($this->session->userdata('user_type') == 'nyk 1'){
						redirect('cbu/pullout_entry');
					}
					else{
						redirect('buyoff/list_');
					}
					//print_r($this->session->userdata());
				}
				else {
					$data['message'] = '<span class="col-sm-12 alert alert-warning">You have no rights to access this system.</span>';
					$this->load->view('login_view.php',$data);
				}
			}
			else {
				$data['message'] = '<span class="col-sm-12 alert alert-warning">You have entered an incorrect username or password, please try again.</span>';
				$this->load->view('login_view.php',$data);
				
			}
		}
		else {
			$this->index();
		}
	}
	
	public function logout(){
		
		$user_data = $this->session->get_userdata();
		foreach($user_data as $key => $value){
			$this->session->unset_userdata($key);
		}
		redirect('login');
	}
}
