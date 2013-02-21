<?php

error_reporting(E_ALL);

class Chat extends Controller {

	function __construct() {
	    parent::Controller();
	}

        function index($channel = 0) {
	    $data['req_anon'] = false;

            if(!$this->user->isLoggedIn())
                redirect('login');

            if($this->user->isBanned())
                $this->load->view('banned');

            if(array_key_exists("kkd", $_COOKIE)) {
				die("You have been kicked for 5 minutes");
            }

		    setcookie("nickname", $this->user->name, (time()+2592000), "/");
            setcookie("kjhdf", true, (time()+2592000), "/");

            $user = $this->user->getUsers(array('userID' => $this->user->id));
            setcookie("avatar", md5(strtolower($this->user->email)), (time()+2592000), "/");
            //setcookie("colorcode", $user->userColor, (time()+2592000), "/");

            $channel_req    = $channel;
            $channel_req    = str_replace("\"", "", $channel_req);
            $channel_req    = str_replace("'", "", $channel_req);
            $channel_req    = str_replace("\\", "", $channel_req);
            $channel_req    = str_replace("/", "", $channel_req);
            $channel_req    = strip_tags(htmlspecialchars($channel_req));
            $channel_req    = strtolower($channel_req);
            if( strlen($channel_req) && preg_match('/[0-9a-zA-Z\-]+/', $channel_req) )
            {}
            else
            {
                    $channel_req    = false;
            }
            $data['channel_req'] = $channel_req;
            $this->load->view('chat', $data);
        }

	function v3() {
		error_reporting(E_ALL);

		if($this->user->isLoggedIn()) {
			$s_id = $this->input->cookie("super_session");
		}

		$this->load->view('chat-v3', array('sid' => (isset($s_id) ? $s_id : false)));
	}

	function kicked($kickerName = "a mod") {
		setcookie("kkd", true, time()+(60*5), "/");
		show_error("You have been kicked by ".$kickerName . ". You can come back in a moment.");
	}

	function hit($action = NULL) {
		$ru = $this->user->name;
		$comet_id = $this->_channel($this->uri->segment(4));
		if(!empty($ru)) {
			$user = $ru;
			switch($action):
				case "online":
					$event = $this->_event("join", $user);
					$this->push("http://127.0.0.1/chat/comet_push?channel={$comet_id}", "text/json", "{$event}");
					$this->db->simple_query("insert into chat_sessions (chatUser) values('{$user}') on duplicate key update chatUpdatedAt=current_timestamp, chatChannel='{$comet_id}'");
				break;
				case "offline":
					$event = $this->_event("leave", $user);
					$this->push("http://127.0.0.1/chat/comet_push?channel={$comet_id}", "text/json", "{$event}");
					$this->db->simple_query("delete from chat_sessions where chatUser='{$user}'");
				break;
				default:
					//Nothing
					$this->db->simple_query("insert into chat_sessions (chatUser) values('{$user}') on duplicate key update chatUpdatedAt=current_timestamp, chatChannel='{$comet_id}'");
			endswitch;
			$timepoint = time()-400;
			$this->db->query("delete from chat_sessions where UNIX_TIMESTAMP(chatUpdatedAt) <= '{$timepoint}'");
			if($this->db->affected_rows() > 0) {
				$this->db->cache_delete('chat', 'get_online');
			}
			echo "success";
		} else {
			echo "failure";
		}
	}

	function kick($channel = 0) {
		$comet_id = $this->_channel($channel);
		$user = $this->user->name;
		if($this->user->canBan(0)) {
			$event = $this->_event("kick", $user, strip_tags($_POST["target"]));
			$this->push("http://127.0.0.1/chat/comet_push?channel={$comet_id}", "text/json", "{$event}");
			die("success");
		}
		die("failure");
	}

	function publish($channel = 0) {
		$comet_id = $this->_channel($channel);
		$last = $this->input->cookie('salm');
		$user = $this->user->name;
		if(!$user)
			die("failure");

		$message = trim($_POST["message"]);
		$message = auto_link(htmlspecialchars($message), 'url', TRUE);

		if(strlen($message) == 0 || strlen($message) > 160)
			die("failure");

		if($last !== false && $last == md5($message))
			die("failure");
		else
			setcookie("salm", md5($message), time()+3600, "/");

		$username = $user;
		$this->db->simple_query("insert into chat_sessions (chatUser) values('{$username}') on duplicate key update chatUpdatedAt=current_timestamp, chatChannel='{$comet_id}'");

		$event = $this->_message($user, $message, (array_key_exists("color", $_COOKIE) && !empty($_COOKIE["color"]) ? $this->input->cookie("color") : "black"), $_COOKIE["avatar"], isset($this->user->colorcode) ? $this->user->colorcode : NULL);
		$this->push("http://127.0.0.1/chat/comet_push?channel={$comet_id}", "text/json", "{$event}");
		die("success");
	}

	function _channel($channel) {
		$channel = str_replace("/", "", $channel);

		if(empty($channel))
			$comet_id = 0;

		if(!preg_match("/[0-9a-zA-Z\_]{1,}/", $channel))
			$comet_id = 0;
		else
			$comet_id = $channel;

		return $comet_id;
	}

	function _event($type, $name, $target = NULL, $verb = NULL) {
		$e           = new stdClass;
		$e->type     = $type;
		$e->nickname = $name;
		if(isset($target)) {
		$e->target   = $target;
		}
		if(isset($verb)) {
		$e->verb     = $verb;
		}
		return json_encode($e);
	}

	function _message($name, $message, $color = "black", $avatar = '', $colorcode = '') {
		$e           = new stdClass;
		$e->type     = "message";
		$e->nickname = $name;
		$e->text  = $message;
		$e->color    = $color;
		$e->timestamp = time();
		$e->avatar = $avatar;
		$e->colorcode = $colorcode;

		return json_encode($e);
	}

	function get_online() {
		$this->db->cache_on();
		$this->db->select('chatUser, chatChannel');
		$this->db->from('chat_sessions');

		$query = $this->db->get();

		$o = new stdClass;
		$o->count = $query->num_rows();
		$o->users = $query->result();
		$this->db->cache_off();
		echo json_encode($o);
	}

	public function push( $url, $content_type, $raw_data ) {
		$url = str_replace('http://','',$url);

		// generate domain/req/headers
		$slash  = strpos($url,'/');

		if( $slash ) {
		  $domain = substr($url,0, $slash);
		  $request= substr($url,$slash);
		} else {
		  $domain  = $url;
		  $request = '/';
		}

		// header write
		$header = "POST {$request} HTTP/1.1\r\n";
		$header.= "Host: {$domain}\r\n";
		$header.= "Content-Type: {$content_type}\r\n";
		$header.= "Content-Length: ". strlen($raw_data) ."\r\n";
		$header.= "Connection: Close\r\n\r\n";

		// create resource
		$fp = fsockopen("tcp://" . $domain, 90, $errno, $errstr, 10);

		if( !$fp ) {
		  return false;
		} else {
		  fputs( $fp, $header );
		  fputs( $fp, $raw_data );
		  fgets( $fp, 1024 );
		  fclose( $fp );
		}

		return true;
	}

}
?>