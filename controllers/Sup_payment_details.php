<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sup_payment_details extends CI_Controller {

	function __construct(){
        parent::__construct();
		$this->load->library('Emailtemplate');
        if(!$this->session->userdata['logged_in']['user_id']){
            redirect('login');
        }else{
			$user_id = $this->session->userdata['logged_in']['user_id'];
		}
		 if($this->session->userdata['logged_in']['user_type'] != "admin" && $this->session->userdata['logged_in']['user_type'] != "accountant"){
            redirect('error_page');
        }
    }

	public function index()
	{
		$this->load->model('sup_payment_details_m');
		$order_id = $this->uri->segment(2);
		$data['fetch_order'] = $this->sup_payment_details_m->get_order_details($order_id);
	
		$this->load->view('sup_payment_details',$data);
	}

	public function update_status(){
	    
	    //print_r($_POST);die;
		$this->load->model('sup_payment_details_m');
		$serv_name = $this->input->post('serv_name');
		$sl_id = $this->input->post('sl_id');
		$pay_date = $this->input->post('pay_date');
		$pay_amnt = $this->input->post('pay_amnt');
		$status = $this->input->post('status');
		$key = $this->input->post('key');
		$get_sup_info = $this->sup_payment_details_m->get_sup_info($sl_id);
		$sup_fullname = ucfirst($get_sup_info->firstname).' '.ucfirst($get_sup_info->lastname);
		$sup_email = $get_sup_info->email;
		$serive_id = $get_sup_info->supplier_service_id;
		$order_id = $get_sup_info->order_id;

		if($status == 'Confirm'){
			$this->tw_profile($sup_fullname,$order_id,$sl_id);
		}
	
		//order table update
		$get_order_details = $this->sup_payment_details_m->get_details($order_id);
		$supplier_payment_details = explode(",",$get_order_details->sup_payment_status);
		$supplier_payment_details[$key] = $status;
		$new_sup_pay_status = implode(',',$supplier_payment_details);
		$update_array = array('sup_payment_status' => $new_sup_pay_status);
		$update_order_trans = $this->sup_payment_details_m->update_order($order_id,$update_array);
		
		//supplier transeaction table update
		$update_array_trans = array('seller_amount' => $pay_amnt ,'pay_date' => $pay_date,'pay_status' => $status);
		$update_trans = $this->sup_payment_details_m->update_trans($update_array_trans,$sl_id);

		//supplier email starts

		$subject_sup = "Order (No. ".$order_id.", ".$currency." ".$total_price.") Payment Update-ProtectBox";

		$sup_message = $this->emailtemplate->payment_status_update_supplier($sup_fullname,$status,$order_id,$serv_name);
		$this->load->library('email');
		$this->email->set_mailtype("html");

		$this->email->from('noreply@protectbox.com', 'ProtectBox');
		$this->email->to('sweezit92@gmail.com'); 

		$this->email->subject($subject_sup);
		$this->email->message($sup_message);
		$sup_okay = $this->email->send();
		//supplier email starts

		//admin email starts
		$this->load->model('signup_m');
		$get_admins = $this->signup_m->get_admins();
		foreach($get_admins as $fetch_admin){
			$admin_fullname = ucfirst($fetch_admin->firstname).' '.ucfirst($fetch_admin->lastname);
			$admin_email = $fetch_admin->email;

			$subject_admin = "Order (No. ".$order_id.", ".$currency." ".$total_price.") Supplier Payment Update";

			$admin_message = $this->emailtemplate->payment_status_update_admin($admin_fullname,$status,$order_id,$serv_name);
			
			$this->load->library('email');
			$this->email->set_mailtype("html");

			$this->email->from('noreply@protectbox.com', 'ProtectBox');
			$this->email->to($admin_email); 

			$this->email->subject($subject_admin);
			$this->email->message($admin_message);
			$admin_okay = $this->email->send();
		}
		//admin email ends
	}
	
	public function tw_profile($sup_fullname,$order_id,$sl_id){
		/*$sup_fullname = 'Tech Data'; //
		$order_id = '1';
		$sl_id = '2';*/

		// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
		$ch = curl_init();
		$api_token = '9f9900f1-6dfc-44bd-8dba-67f92db76d73';			//9f9900f1-6dfc-44bd-8dba-67f92db76d73

		curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.transferwise.tech/v1/profiles');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		
	


		$headers = array();
		$headers[] = 'Authorization: Bearer '.$api_token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		//	print_r($result); die;
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		
		$decode_json = json_decode($result,TRUE);
		$this->tw_quotes($sup_fullname,$order_id,$sl_id,$decode_json[0]['id'],$api_token);

		curl_close($ch);
	}
	
//		public function tw_quotes('Tech Data',4,4,883,'9f9900f1-6dfc-44bd-8dba-67f92db76d73){

	public function tw_quotes($sup_fullname,$order_id,$sl_id,$profile_id,$api_token){

		$this->load->model('sup_payment_details_m');
		$supplier_account_info = $this->sup_payment_details_m->supplier_details($order_id);
		$amount_paid = $supplier_account_info->seller_amount;
		

		// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.transferwise.tech/v1/quotes');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{ \n          \"profile\":$profile_id,\n          \"source\": \"GBP\",\n          \"target\": \"".$supplier_account_info->currency."\",\n          \"rateType\": \"FIXED\",\n          \"targetAmount\": $amount_paid,\n          \"type\": \"BALANCE_PAYOUT\"\n        }");
		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = 'Authorization: Bearer '.$api_token;
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
	//	print_r($result); die;
		$decode_json = json_decode($result,TRUE);
		$this->tw_account($sup_fullname,$sl_id,$supplier_account_info->currency,$supplier_account_info->sort_code_gbp,$supplier_account_info->account_number_gbp,$supplier_account_info->iban_eur,$supplier_account_info->routing_number_usd,$supplier_account_info->account_number_usd,$supplier_account_info->account_type_usd,$profile_id,$api_token,$decode_json['id']);
		curl_close($ch);
	}

	public function tw_account($sup_fullname,$sl_id,$currency,$sort_code_gbp,$account_number_gbp,$iban_eur,$routing_number_usd,$account_number_usd,$account_type_usd,$profile_id,$api_token,$quote_id){
		
		// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.transferwise.tech/v1/accounts');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// Dynamic account as per currency
		if($currency == 'GBP'){
			curl_setopt($ch, CURLOPT_POSTFIELDS, "{ \n
						\"currency\": \"GBP\", \n
						\"type\": \"sort_code\", \n
						\"profile\": \"".$profile_id."\", \n
						\"accountHolderName\": \"".$sup_fullname."\",\n
						\"legalType\": \"PRIVATE\",\n
						\"details\": { \n
						\"sortCode\": \"".$sort_code_gbp."\", \n
						\"accountNumber\": \"".$account_number_gbp."\" \n
						} \n         }");

		}else if($currency == 'EUR'){
			curl_setopt($ch, CURLOPT_POSTFIELDS, "{ \n
						\"currency\": \"EUR\", \n
						\"type\": \"sort_code\", \n
						\"profile\": \"".$profile_id."\", \n
						\"accountHolderName\": \"".$sup_fullname."\",\n
						\"legalType\": \"PRIVATE\",\n
						\"details\": { \n
						\"IBAN\": \"".$iban_eur."\" \n
						} \n         }");
		}else if($currency == 'USD'){
			curl_setopt($ch, CURLOPT_POSTFIELDS, "{ \n
						\"currency\": \"USD\", \n
						\"type\": \"sort_code\", \n
						\"profile\": \"".$profile_id."\", \n
						\"accountHolderName\": \"".$sup_fullname."\",\n
						\"legalType\": \"PRIVATE\",\n
						\"details\": { \n
						\"routingNumber\": \"".$routing_number_usd."\", \n
						\"accountNumber\": \"".$account_number_usd."\", \n
						\"accountType\": \"".$account_type_usd."\" \n	
						} \n         }");
		}

		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = 'Authorization: Bearer '.$api_token;
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		
		curl_close($ch);
		$decode_json = json_decode($result,TRUE);
		//print_r($result);
		$recipient_account_id = $decode_json['id'];
		$this->tw_transfers($profile_id,$sl_id,$api_token,$recipient_account_id,$quote_id);
	}


	public function tw_transfers($profile_id,$sl_id,$api_token,$recipient_account_id,$quote_id){
		
		$this->load->model('sup_payment_details_m');
		$pay_date = date('Y-m-d');

		// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
		$ch = curl_init();
		$uuid = $this->gen_uuid();			//Format: '71bc9946-b9c5-11e9-a2a3-2a2ae2dbcce4'
		
		curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.transferwise.tech/v1/transfers');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{ \n          \"targetAccount\": $recipient_account_id,   \n          \"quote\": $quote_id,\n          \"customerTransactionId\": \"".$uuid."\",\n          \"details\" : {\n              \"reference\" : \"to my friend\",\n              \"transferPurpose\": \"verification.transfers.purpose.pay.bills\",\n              \"sourceOfFunds\": \"verification.source.of.funds.other\"\n            } \n         }");
		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = 'Authorization: Bearer '.$api_token;
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		$decode_json = json_decode($result,TRUE);
		//print_r($decode_json);
		curl_close($ch);
		
		$update_tw_array = array('pay_date' => $pay_date,'transaction_id' =>$uuid,'pay_method'=>'TransferWise','pay_status_respose'=>$decode_json['status'],'requested_by'=>'1','last_modified'=>$decode_json['created']);
		$update_trans = $this->sup_payment_details_m->update_trans($update_tw_array,$sl_id);

		$this->tw_fund_transfer($sl_id,$api_token,$decode_json['id']);
	}

	public function tw_fund_transfer($sl_id,$api_token,$transfer_id){

		$this->load->model('sup_payment_details_m');

		// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.transferwise.tech/v1/transfers/'.$transfer_id.'/payments');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{ \n          \"type\": \"BALANCE\"   \n         }");
		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = 'Authorization: Bearer '.$api_token;
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		$decode_json = json_decode($result);
		curl_close($ch);
		//print_r($decode_json);

		$update_tw_array = array('pay_status_respose'=>$decode_json->status);
		$update_trans = $this->sup_payment_details_m->update_trans($update_tw_array,$sl_id);

		$this->tw_transfer_status($api_token,$transfer_id);
	}

	public function tw_transfer_status($api_token,$transfer_id){
		// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.transferwise.tech/v1/transfers/'.$transfer_id);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


		$headers = array();
		$headers[] = 'Authorization: Bearer '.$api_token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		print_r($result);
	}


	/* GENERATES UUID FOR EACH TRANSACTION STARTS */
	public function gen_uuid() {

		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}
	/* GENERATES UUID FOR EACH TRANSACTION ENDS */

}

/* End of file Sup_payment_details.php */
/* Location: ./application/controllers/Sup_payment_details.php */