<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct() {
      parent::__construct();	
	 // $this->load->database('db4_india', TRUE);

	}
	public function index()
	{
		$this->load->library('recaptcha');
        $recaptcha = $this->input->post('g-recaptcha-response');
        if (!empty($recaptcha)) {
            $response = $this->recaptcha->verifyResponse($recaptcha);
            if (isset($response['success']) and $response['success'] === true) {
                echo "You got it!";
            }
        }

        $data = array(
            'widget' => $this->recaptcha->getWidget(),
            'script' => $this->recaptcha->getScriptTag(),
        );
		if(isset($_GET['email_segment']))
		{
			$data['email'] = $_GET['email_segment'];
		}else{
			$data['email'] = "";
		}
		$this->load->view('login',$data);
	}
	public function check_login()
	{
		$this->load->model('login_m');
		$this->load->model('questionaire_m');
		$this->load->model("questionniare_results_m");
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$records=array('email'=>$email,'password'=>$password);
		
		/*Fetching currency from location starts 
			$client  = $_SERVER['HTTP_CLIENT_IP'];
			$forward = $_SERVER['HTTP_X_FORWARDED_FOR'];
			$remote  = $_SERVER['REMOTE_ADDR'];
			$result  = array('country'=>'', 'city'=>'','currencyCode'=>'');
			if(filter_var($client, FILTER_VALIDATE_IP)){
				$ip = $client;
			}else if(filter_var($forward, FILTER_VALIDATE_IP)){
				$ip = $forward;
			}else{
				$ip = $remote;
			}
			$ip_data = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));    
			if($ip_data && $ip_data->geoplugin_countryName != null){
				$result['country'] = $ip_data->geoplugin_countryName;
				$result['city'] = $ip_data->geoplugin_city;
				$result['currencyCode'] = $ip_data->geoplugin_currencyCode;
			}
			//$currencyCode = $result['currencyCode'];*/
			$currencyCode = 'GBP';
	   /*Fetching currency from location Ends*/

		$result = $this->login_m->login_function($email,$password);

		//print_r($result);die;
		
		if($result > 0){
			$last_id = $this->login_m->get_id($records);
			$session_data = array();
			foreach($last_id as $row)
			{
			  $fullname = $row->firstname . " " . $row->lastname;
			  $session_data = array(
									  'user_id' => $row->user_id,
									  'user_type' => $row->user_type,
									  'name' =>$fullname,
									  'email'=>$row->email,
				  					  'currency' =>$currencyCode
								   );
			}
			
			$this->session->set_userdata('logged_in',$session_data);
			if(isset($this->session->userdata['logged_in']['user_type']) && $this->session->userdata['logged_in']['user_type'] == "Small and medium business"){

				$user_id = $this->session->userdata['logged_in']['user_id'];
				$get_basic = $this->questionaire_m->row_check($user_id);
				$get_tech = $this->questionaire_m->tech_row($user_id);
				$get_non_tech = $this->questionaire_m->tech_non_tech($user_id);
				$get_budget = $this->questionaire_m->tech_budget($user_id);
				$get_bundle = $this->login_m->smb_bundle($user_id);
				$get_orders = $this->login_m->smb_orders($user_id);
				
			/*	if(!empty($last_id)){
			    $fetch_subscription = $this->questionniare_results_m->fetch_subscr($last_id[0]->user_id);
			    
			   $date = time();
			    if(isset($fetch_subscription->subscription_end_date)){
			       
		        if($date > $fetch_subscription->subscription_end_date){
		             
		            $threeMonthsLater = strtotime("+3 months", $date);
		            	$check_cpn_code = $this->questionniare_results_m->get_cpn($fetch_subscription->payment_processor);
		            		if(!empty($check_cpn_code)){
                			foreach($check_cpn_code As $val_cpn){
                				$coupon = array(
                				'coupon_code' => $check_cpn_code->coupon_code
                				);
                			}
                	
                			$coupon_data = array(
                				'smb_id' => $user_id,
                				'payment_processor' =>$fetch_subscription->payment_processor,
                				'payment_status' => '1',
                				'subscription_status' => '1',
                				'date' => $date,
                				'subscription_end_date' => $threeMonthsLater
                			);
                			$subscribe = $this->questionniare_results_m->smb_subscribe($coupon_data);
		            		}
		        }
				}
				}*/
				
				if($get_basic < 1)
				{
					redirect('questionaire');
				}
				else if($get_basic > 0 && $get_tech < 1)
				{
					redirect('questionaire');
				}
				else if($get_basic > 0 && $get_tech > 0 && $get_non_tech < 1)
				{
					redirect('questionaire_tech_info');
				}
				else if($get_basic > 0 && $get_tech > 0 && $get_non_tech > 0 && $get_budget < 1)
				{
					redirect('questionaire_nontech_info');
				}
				else if($get_basic > 0 && $get_tech > 0 && $get_non_tech > 0 && $get_budget > 0 && $get_bundle < 1)
				{
					redirect('bundle_json');
				}
				else if($get_basic > 0 && $get_tech > 0 && $get_non_tech > 0 && $get_budget > 0 && $get_bundle > 0 && $get_orders < 1)
				{
					redirect('results');
				}
				else if($get_basic > 0 && $get_tech > 0 && $get_non_tech > 0 && $get_budget > 0 && $get_orders > 0)
				{
					redirect('questionniare_results');
				}
				
			}else if(isset($this->session->userdata['logged_in']['user_type']) && $this->session->userdata['logged_in']['user_type'] == "Supplier"){
				redirect('edit_solution');
			}else if(isset($this->session->userdata['logged_in']['user_type']) && $this->session->userdata['logged_in']['user_type'] == "admin"){
				redirect('admin_dashboard');
			}else if(isset($this->session->userdata['logged_in']['user_type']) && $this->session->userdata['logged_in']['user_type'] == "delegate"){
				$this->session->set_flashdata("delegate", "It seems you are a delegate user! Please use <a href='delegate_login'>delegate login</a>.");
				redirect('login');
			}else if(isset($this->session->userdata['logged_in']['user_type']) && $this->session->userdata['logged_in']['user_type'] == "accountant"){
				redirect('pending_refund_request');
			}
		}else{
			$this->session->set_flashdata("failed", "Invalid email or password");
			redirect('login');
		}
	}

}
?>