<?php

class Direct extends Controller {

    function Direct() {
		parent::Controller();
		if(!$this->user->isLoggedIn()) redirect('login');
		$this->load->library('pagination');
    }

    var $recipients_users;

    function index() {
		redirect('direct/inbox');
    }

    function compose($rec = NULL) {
		parse_str(substr(strstr($_SERVER['REQUEST_URI'], '?'), 1), $_GET);

		if(array_key_exists("in_reply_to", $_GET)) {
		    $reply_to = $this->privatum->getPM(array('pmID' => $_GET["in_reply_to"]));
		    $reply_to->pmBlob = json_decode($reply_to->pmBlob);
		    $data["re"] = $reply_to->pmBlob->subject;
		}

		if($_POST && trim($_POST['pmContent']) !== "") {
		    $message = array(
			'subject' => $this->input->post('pmSubject'),
			'body' => $this->input->post('pmContent')
		    );
		    $message = (object) $message;
		    if(!empty($rec)) {
			$ids = intval($rec);
		    } else {
			$ids = $this->_rec_to_ids($this->input->post('pmRec'));
			if(empty($ids)) {
			    redirect('direct/compose');
			}
		    }
		    $new_id = $this->privatum->sendPM($this->user->id, $ids, $message, intval($this->input->post('pmFollowup')));
		    if($new_id) {
			if(is_array($this->recipients_users)) {
			    foreach($this->recipients_users as $r) {
				$this->privatum->sendInboxNotification($r->userName, $r->userEmail, $this->user->name, $message->subject, $message->body, $new_id);
			    }
			}
			redirect('direct/view/'.$new_id);
		    } else {
			redirect('direct/compose');
		    }
		}

		$data['page_title'] = 'Compose new direct message';
		$data['rec'] = $rec;
		$this->load->view('direct-new-new', $data);
    }

    function _rec_to_ids($string) {
		$names = explode(",", $string);
		$names = array_map('trim', $names);
		unset($names[$this->user->name]);
		$this->db->select('userID, userName, userEmail');
		$this->db->where_in('userName', $names);
		$users = $this->db->get('users');
		$users = $users->result();
		$ids   = array();
		foreach($users as $u) {
		    $ids[] = $u->userID;
		}
		$this->recipients_users = $users;

		return $ids;
    }

    function view($pmID = null) {
		if(empty($pmID)) show_404();

		$pm = $this->privatum->getPM(array('pmID'=>$pmID));
		$cur_u = $this->user->id;
		$pass = false;

		foreach($pm->pmReceivers as $rec) {
		    if($rec->boxOwnerID == $cur_u) {
			$pass = true;
		    }
		}

		if($pm->pmSender !== $cur_u && !$pass) show_error('Not for you!', 403);

		$this->read($pmID);
		$pm->pmBlob = json_decode($pm->pmBlob);
		$data['pm'] = $pm;

		$data['page_title'] = "Direct: ".(!empty($pm->pmBlob->subject) ? $pm->pmBlob->subject : "No subject");
		$this->load->view('direct-view', $data);
    }

    function inbox() {
		$cur_u = $this->user->id;

		$config['base_url'] = base_url() . '/direct/inbox/offset/';
	        $config['total_rows'] = $this->privatum->getPMCount($cur_u, 'receiver');
	        $config['per_page'] = '20';
	        $config['uri_segment'] = '4';
	        $config['full_tag_open'] = '<div class="pagination">';
	        $config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		$pm = $this->privatum->getPM(array('pmReceiver'=>$cur_u, 'sortBy'=>'pms.pmCreatedAt', 'sortDirection'=>'desc', 'offset'=>$this->uri->segment(4), 'limit'=>20));
		$data['page_title'] = "Inbox";

		if($this->unread > 0)
		    $data['page_title'] = "(".$this->unread.") " . $data['page_title'];

		$data['pms'] = $pm;

		$this->load->view('direct-box', $data);
    }

    function sent() {
		$cur_u = $this->user->id;

		$config['base_url'] = base_url() . '/direct/sent/offset/';
	        $config['total_rows'] = $this->privatum->getPMCount($cur_u, 'sender');
	        $config['per_page'] = '20';
	        $config['uri_segment'] = '4';
	        $config['full_tag_open'] = '<div class="pagination">';
	        $config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		$pm = $this->privatum->getPM(array('pmSender'=>$cur_u, 'sortBy'=>'pms.pmCreatedAt', 'sortDirection'=>'desc', 'offset'=>$this->uri->segment(4), 'limit'=>20));

		$data['page_title'] = "Outbox";
		$data['pms'] = $pm;

		$this->load->view('direct-box', $data);
    }

    function read($pmID = null) {
		if(empty($pmID)) return false;

		if($this->privatum->markPM($this->user->id, $pmID, 'read') > 0) {
		    return true;
		} else {
		    return false;
		}
    }

    function delete($pmID = null) {
		if(empty($pmID)) return false;

		if($this->privatum->markPM($this->user->id, $pmID, 'deleted') > 0) {
		    return true;
		} else {
		    return false;
		}
    }

    function get_unread() {
		echo $this->privatum->numUnreadPM($this->user->id);
    }
}
?>