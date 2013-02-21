<?php
class Login extends Controller {

	function __construct() {
		parent::Controller();
	}

	function index()
	{
		if($this->user->isLoggedIn()) redirect('');

		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules('userName', 'username', 'trim|required|callback__check_login');
		$this->form_validation->set_rules('userPassword', 'password', 'trim|required');

		if($this->form_validation->run()) {
			//die('Success desu');
			if($this->user->authorize(array('userName' => $_POST['userName'], 'userPassword' => $_POST['userPassword']))) {
				$goback = urldecode($_POST['ref']);
				if(strpos($goback, "thecolorless.net") !== false) {
					if(strpos($goback, "register") !== false) {
						redirect('');
					}
					redirect($goback);
				}
				redirect('');
			}
		}
		$data['page_title'] = "Login";
		$data['page_desc'] = "We're glad to see you again!";
		$data['referrer'] = urlencode($_SERVER['HTTP_REFERER']);
		$this->load->view('login', $data);
	}

	function register() {
		if($this->user->isLoggedIn()) redirect('');

		$this->load->library('form_validation');
		$this->load->helper('recaptcha');

		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules('userName', 'username', 'trim|required|max_length[24]|alpha_dash|callback__check_username');
		$this->form_validation->set_rules('userEmail', 'email', 'trim|required|valid_email|callback__check_email');
		$this->form_validation->set_rules('userPassword', 'password', 'trim|required');

		if($this->form_validation->run() && empty($_POST["url"])) {
			//Yeahooooooo
			$insert = array('userName'=>$this->input->post('userName'), 'userEmail'=>$this->input->post('userEmail'), 'userPassword'=>md5($this->input->post('userPassword')));

            $new_id = $this->user->addUser($insert);
			if($new_id) {

				$this->user->sendWelcomeEmail($this->input->post('userName'), $this->input->post('userEmail'), $this->input->post('userPassword'));
				redirect('login/registered');
			}
		}

		$data['page_title'] = "Join";
		$data['page_desc'] = "Here you can become one of us, free and anonymous";
		$this->load->view('sign-up', $data);
	}

	function registered() {
		$data['page_title'] = "Success!";
		$this->load->view('sign-up-success', $data);
	}

	function recover() {
		if($_POST) {
			$usere = $this->user->getUsers(array('userEmail'=>$this->input->post('userEmail')));
			if($usere) {
				$new_pass = $this->user->randomPass();

				$update = array('userID'=>$usere->userID, 'userPassword'=>$new_pass);
				if($this->user->updateUser($update)) {
					$this->user->sendNewPassword($usere->userName, $usere->userEmail, $new_pass);
					$data['fulfilled'] = "true";
				}
			}
		}

		$data['page_title'] = "Password recovery";
		$this->load->view('password-recovery', $data);
	}

	function logout() {
		$this->user->unauthorize();
		setcookie("nickname", "", time()-3600, "/");
		setcookie("kjhdf", false, (time()-3600), "/");
		redirect('');
	}

	function _check_login($userName) {
		if($_POST['userPassword']) {
			$is_there = $this->user->getUsers(array('userName' => $_POST['userName'], 'userPassword' => $this->user->password($_POST['userPassword'])));
			if($is_there) return true;
		}

		$this->form_validation->set_message('_check_login', 'Either your username or password (or both) is invalid.');
		return false;
	}

	function _check_username($userName) {
		$is_there = $this->user->getUsersNum(array('userName'=>$userName));
		if($is_there == 0)
			return true;

		$this->form_validation->set_message('_check_username', 'That username is already in use. Sorry :(');
		return false;
	}

	function _check_email($userEmail) {
		$is_there = $this->user->getUsersNum(array('userEmail'=>$userEmail));
		if($is_there == 0)
			return true;

		$this->form_validation->set_message('_check_email', 'That e-mail is already assigned to an account. We don\'t tolerate multiple accounts.');
		return false;
	}

}
?>
