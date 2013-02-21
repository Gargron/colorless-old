<?php

class Dashboard extends Controller {
	function __construct() {
		parent::Controller();
	}

	function index($offset = 0) {
		if(!$this->user->isLoggedIn())
			redirect('login');
//		if(!$this->user->isAdmin())
//			redirect('/');

		$this->load->library('pagination');
		$config['base_url'] = base_url().'dashboard/'.$threadID.'/offset/';
		$config['total_rows'] = $offset + 41;
		$config['per_page'] = 40;
		$config['uri_segment'] = '3';
		$config['full_tag_open'] = '<div class="pagination">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		$user = $this->user->getUsers(array('userID' => $this->user->id));
		$data['user'] = $user;
		$data['page_title'] = "Your Timeline";
		$data['page_desc'] = "User #".$user->userID;
		$data['offset'] = empty($offset) ? 0 : $offset;
		$data['timeline'] = array();
		$data['userlist'] = array();

		foreach($this->user->getTimeline($user->userID, $offset) as $post) {
			if (!array_key_exists($post->postParentID, $data['timeline'])) {
				$data['timeline'][$post->postParentID] = $post;
				$data['userlist'][$post->postParentID] = array();
			}
			$data['userlist'][$post->postParentID][$post->userName] = array('userName' => $post->userName, 'userEmail' => $post->userEmail);
		}

		$this->load->view('dashboard', $data);
	}

	function gotopost($postParentID, $postID) {
		redirect('/thread/'.$postParentID.'/offset/'.$this->chan->getPageItsOn($postParentID, $postID).'#p'.$postID);
	}

	function mentions() {
		if(!$this->user->isLoggedIn())
			redirect('login');
//		if(!$this->user->isAdmin())
//			redirect('/');
		if($this->user->isBanned())
			$this->load->view('banned');
	    
		$offset = $this->uri->segment(4);
	    
		$this->load->library('pagination');
		$config['base_url'] = base_url().'dashboard/mentions/'.$threadID.'/offset/';
		$config['total_rows'] = $offset + 101;
		$config['per_page'] = 100;
		$config['uri_segment'] = '4';
		$config['full_tag_open'] = '<div class="pagination">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		$user = $this->user->getUsers(array('userID' => $this->user->id));
		$data['user'] = $user;
		$data['page_title'] = $user->userName . "'s Mentions";
		$data['page_desc'] = "User #".$user->userID;
		$data['offset'] = empty($offset) ? 0 : $offset;
		$data['timeline'] = array();
		$data['userlist'] = array();
	    
		foreach($this->user->getMentions($user->userID, $offset) as $post) {
			if (!array_key_exists($post->postParentID, $data['timeline'])) {
				$data['timeline'][$post->postParentID] = $post;
				$data['userlist'][$post->postParentID] = array();
			}
			$data['userlist'][$post->postParentID][$post->userName] = array('userName' => $post->userName, 'userEmail' => $post->userEmail);
		}

		$this->load->view('dashboard', $data);
	}	

	function friends() {
		if(!$this->user->isLoggedIn())
			redirect('login');
		if($this->user->isBanned())
			$this->load->view('banned');
	
		$user = $this->user->getUsers(array('userID' => $this->user->id));
		$data['user'] = $user;
		$data['page_title'] = "Your friends";
		$data['page_desc'] = "User #".$user->userID;
		$data['friends'] = $this->user->getFriends($user->userID);
		$this->load->view('friends', $data);
	}
	
	function pending() {
		if(!$this->user->isLoggedIn())
			redirect('login');
		if($this->user->isBanned())
			$this->load->view('banned');
	
		$user = $this->user->getUsers(array('userID' => $this->user->id));
		$data['user'] = $user;
		$data['page_title'] = "Your sent requests";
		$data['page_desc'] = "User #".$user->userID;
		$data['friends'] = $this->user->getPending($user->userID);
		$this->load->view('friends', $data);
	}
	
	function requests() {
		if(!$this->user->isLoggedIn())
			redirect('login');
		if($this->user->isBanned())
			$this->load->view('banned');
	
		$user = $this->user->getUsers(array('userID' => $this->user->id));
		$data['user'] = $user;
		$data['page_title'] = "Your incoming friend requests";
		$data['page_desc'] = "User #".$user->userID;
		$data['friends'] = $this->user->getRequests($user->userID);
		$this->load->view('friends', $data);
	}
}
?>
