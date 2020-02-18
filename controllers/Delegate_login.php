<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delegate_login extends CI_Controller {

	public function index()
	{
		$this->load->view('delegate_login');
	}

	public function check_login()
	{
		$this->load->model('delicate_login_m');
		$email = $this->input->post('email');
		$password = $this->input->post('password');

		$check_delegate = $this->delicate_login_m->delicate_check($email);
		if(sizeof($check_delegate) > 0)
		{
			$check_user = $this->delicate_login_m->user_check($email,$password);
			if(sizeof($check_user) > 0)
			{
				$fullname = $check_user->firstname . " " . $check_user->lastname; 
				$session_data = array(
									  'user_id' => $check_user->user_id,
									  //'user_type' => $check_user->user_type,
									  'user_type' => 'delegate',
									  'name' =>$fullname,
									  'email'=>$check_user->email
								   );
				if($check_delegate->access != ''){
					$array = explode(',',$check_delegate->access);
					if(in_array('basic',$array))
					{
						$this->session->set_userdata('logged_in',$session_data);
						redirect('delegate_questionaire');
					}else{
						if(in_array('tech',$array))
						{
							$this->session->set_userdata('logged_in',$session_data);
							redirect('delegate_questionaire_tech_info');
						}else{
							if(in_array('non_tech',$array))
							{
								$this->session->set_userdata('logged_in',$session_data);
								redirect('delegate_questionaire_nontech_info');
							}else{
								if(in_array('budget',$array))
								{
									$this->session->set_userdata('logged_in',$session_data);
									redirect('delegate_questionaire_budget');
								}
							}
						}
					}
				}else{
					$this->session->set_userdata('logged_in',$session_data);
					redirect('delegate_questionaire');
				}
				

			}else{
				$this->session->set_flashdata("failed", "Invalid email or password");
				redirect('delegate_login');
			}
		}else{
			$this->session->set_flashdata("failed", "Invalid email or password");
			redirect('delegate_login');
		}
	}

}

/* End of file Delicate_login.php */
/* Location: ./application/controllers/Delicate_login.php */