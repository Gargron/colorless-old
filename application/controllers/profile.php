<?php

class Profile extends Controller {

	function __construct() {
		parent::Controller();
	}

        function index() {
            $this->my();
        }

	function pubp($input = 1)
	{
            $user = $this->user->getUsers(array('userName' => urldecode($input)));
	    $this->_profile($user);
	}

	function givebadge($userID, $badgeID) {
	    if(!$this->user->isLoggedIn()) redirect('login');

	    if ($userID != $this->user->id)
	    $this->user->giveBadge($this->user->id, $userID, $badgeID);

		$user = $this->user->getUsers(array('userID' => $userID));
	    redirect('/user/' . $user->userName);
	}

	function _social_array($user) {
	    $social = array();
	    $social[] = (!empty($user->userBlob->socialTwitter) ? (object) array('slug'=>'twitter', 'name'=>'Twitter', 'title'=>'@'.$user->userBlob->socialTwitter, 'address'=>'http://twitter.com/'.$user->userBlob->socialTwitter) : false);
	    $social[] = (!empty($user->userBlob->socialTumblr) ? (object) array('slug'=>'tumblr', 'name'=>'Tumblr', 'title'=>$user->userBlob->socialTumblr.'.tumblr.com', 'address'=>'http://'.$user->userBlob->socialTumblr.'.tumblr.com') : false);
	    $social[] = (!empty($user->userBlob->socialLastfm) ? (object) array('slug'=>'lastfm', 'name'=>'Last.fm', 'title'=>$user->userBlob->socialLastfm, 'address'=>'http://last.fm/user/'.$user->userBlob->socialLastfm) : false);
	    $social[] = (!empty($user->userBlob->socialFacebook) ? (object) array('slug'=>'facebook', 'name'=>'Facebook', 'title'=>$user->userBlob->socialFacebook, 'address'=>'http://facebook.com/'.$user->userBlob->socialFacebook) : false);
	    $social[] = (!empty($user->userBlob->socialYoutube) ? (object) array('slug'=>'youtube', 'name'=>'YouTube', 'title'=>$user->userBlob->socialYoutube.'\'s Channel', 'address'=>'http://youtube.com/user/'.$user->userBlob->socialYoutube) : false);
	    $social[] = (!empty($user->userBlob->socialSkype) ? (object) array('slug'=>'skype', 'name'=>'Skype', 'title'=>$user->userBlob->socialSkype, 'address'=>'skype://'.$user->userBlob->socialSkype) : false);
	    $social[] = (!empty($user->userBlob->socialSteam) ? (object) array('slug'=>'steam', 'name'=>'Steam', 'title'=>$user->userBlob->socialSteam, 'address'=>'http://steamcommunity.com/id/'.$user->userBlob->socialSteam) : false);
	    $social[] = (!empty($user->userBlob->socialForrst) ? (object) array('slug'=>'forrst', 'name'=>'Forrst', 'title'=>'@'.$user->userBlob->socialForrst, 'address'=>'http://forrst.com/people/'.$user->userBlob->socialForrst) : false);
	    $social[] = (!empty($user->userBlob->socialDeviantart) ? (object) array('slug'=>'deviantart', 'name'=>'deviantArt', 'title'=>'~'.$user->userBlob->socialDeviantart, 'address'=>'http://'.$user->userBlob->socialDeviantart.'.deviantart.com') : false);
	    $social[] = (!empty($user->userBlob->socialFlickr) ? (object) array('slug'=>'flickr', 'name'=>'Flickr', 'title'=>$user->userBlob->socialFlickr.'\'s Photos', 'address'=>'http://flickr.com/photos/'.$user->userBlob->socialFlickr) : false);
	    $social = array_filter($social);
	    return $social;
	}

	function id($userID = NULL) {
            if(empty($userID)) {
		$this->pubp("id");
            } else {
		$user = $this->user->getUsers(array('userID' => $userID));
		$this->_profile($user);
	    }
	}

	function _profile($user) {
		if(!$user || $user->userStatus == 'deleted') show_error('That.. user... that user... doesn\'t exist! ;-;', 404);

		if($this->uri->segment(3) == "feed") {
			$this->_feed($user);
		} else {
			$user->userBlob = (object) json_decode($user->userBlob);
			$data['user'] = $user;
			$data['user_threads'] = $this->chan->retrieveThreads(array('threadCreatorID'=>$user->userID, 'sortBy'=>'threadCreatedAt', 'sortDirection'=>'desc', 'limit'=>'5'));
			$data['rpthreads'] = $this->chan->getUsersThreads($user->userID, 0, 5);
			$data['social'] = $this->_social_array($user);
			$data['friends_count'] = $this->user->numFriends($user->userID);
			$data['page_title'] = $user->userName . "'s profile";
			$data['page_desc'] = "User #".$user->userID;
			$data['badges'] = $this->user->getBadges($user->userID);
			$data['badges2'] = $this->user->isAdmin() ? $this->db->get("badges")->result() : $this->user->getBadges($this->user->id, 'pending');
			$this->load->view('profile', $data);
		}
	}

	function my() {
		redirect('user/'.$this->user->name);
	}

	function settings() {
		if(!$this->user->isLoggedIn()) redirect('login');

		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules('userEmail', 'email', 'trim|required|valid_email');
		$this->form_validation->set_rules('userHP', 'homepage', 'trim|callback_valid_url');
		$this->form_validation->set_rules('userLocation', 'location', 'trim|max_length[50]');
		$this->form_validation->set_rules('socialTwitter', 'Twitter username', 'trim|alpha_dash|max_length[50]');
		$this->form_validation->set_rules('socialTumblr', 'Tumblr username', 'trim|alpha_dash|max_length[50]');
		$this->form_validation->set_rules('socialFacebook', 'Facebook handle', 'trim|max_length[50]');
		$this->form_validation->set_rules('socialLastfm', 'Last.fm username', 'trim|alpha_dash|max_length[50]');
		$this->form_validation->set_rules('userAbout', 'bio', 'trim|max_length[160]');
		$this->form_validation->set_rules('userYear', 'date of birth (dd.mm.yyy)', 'trim');

		if($this->form_validation->run()) {
			$user = $this->user->getUsers(array('userID' => $this->user->id));

			//Huzzah
			$meta = new stdClass();
			$meta->userAlias = $this->input->post('userAlias');
			$meta->userHP = $this->input->post('userHP');
			$meta->userYear = $this->input->post('userYear');
			$meta->userCountry = $this->input->post('userCountry');
			$meta->userAbout = $this->input->post('userAbout');
			$meta->userGender = $this->input->post('userGender');
			$meta->settingReceivePMs = $this->input->post('settingReceivePMs');
			$meta->settingReceiveMNs = $this->input->post('settingReceiveMNs');
			$meta->settingReceiveFRs = $this->input->post('settingReceiveFRs');
                        $meta->settingShowSpam = $this->input->post('settingShowSpam');
			$meta->socialTwitter = $this->input->post('socialTwitter');
			$meta->socialTumblr = $this->input->post('socialTumblr');
			$meta->socialFacebook = $this->input->post('socialFacebook');
			$meta->socialLastfm = $this->input->post('socialLastfm');
			$meta->socialYoutube = $this->input->post('socialYoutube');
			$meta->socialSkype = $this->input->post('socialSkype');
			$meta->socialSteam = $this->input->post('socialSteam');
			$meta->socialDeviantart = $this->input->post('socialDeviantart');
			$meta->socialForrst = $this->input->post('socialForrst');
			$meta->socialFlickr = $this->input->post('socialFlickr');
			$meta->settingExcludeBoards = array();
			$exclude = $this->input->post('settingExcludeBoards');
			if($exclude) {
				$exclude = explode(' ', $exclude);
				foreach($exclude as $board) {
					$key = $this->chan->slugBoard(strtolower($board));
					$meta->settingExcludeBoards[] = $key;
				}
				array_unique($meta->settingExcludeBoards);
			}
			$meta = (object) array_merge((array) json_decode($user->userBlob), (array) $meta);
			$update = array('userID' => $this->user->id, 'userBlob' => $meta);
			$email = $this->input->post('userEmail');
			if($email) {
				$update['userEmail'] = $email;
			}

			if(($this->input->post('userPassword') && $this->input->post('userPasswordConfirm')) && ($this->input->post('userPassword') == $this->input->post('userPasswordConfirm'))) {
				$update['userPassword'] = $this->input->post('userPassword');
			}

			$update['userPrivacy'] = $this->input->post('userPrivacy');

			if($this->user->updateUser($update)) {
				//Saved
				$this->form_validation->set_message('updateUser', 'Changes saved successfully =D');
			} else {
				//Not saved
				$this->form_validation->set_message('updateUser', 'Saving failed D= D= D=');
			}
		}

		$user = $this->user->getUsers(array('userID' => $this->user->id));
		if(!$user) show_error('You do not exist!', 404);

		$oldObject = $user->userBlob;
		$jsonObject = json_decode($oldObject);
		if(is_object($jsonObject)) {
			//It's the newly stored metablob, no import needed
			$user->userBlob = $jsonObject;
		} else {
			//Unjsonification failed, needs unserialization
			$serObject = unserialize(stripslashes($oldObject));
			if(is_array($serObject)) {
				//We got it! Now convert data
				$newObject = new stdClass;
				$newObject->userAlias = $serObject['alias'];
				$newObject->userHP = $serObject['url'];
				$newObject->userYear = substr($serObject['birthyear'], 0, 4);
				$newObject->userLocation = $serObject['location'];

				$user->userBlob = $newObject;
			}
		}

		$data['user'] = $user;
		$data['page_title'] = 'Settings';
		$this->load->view('settings', $data);
	}

	function _date_check($str, $format="%m/%d/%Y") {
		$date_array = strptime($str, $format);
		if($date_array == false || $date_array['unparsed'] !== "") {
			$this->form_validation->set_message('_date_check', 'Please enter a correct date of birth in a mm/dd/yyyy format.');
			return false;
		} else {
			return true;
		}
	}

	function name_lookup($name = NULL) {
		if(!isset($name))
			die("false");

		$user = $this->user->getUsers(array('userName'=>$name));
		if(!$user)
			die("false");

		die("true");
	}

	function email_lookup() {
		$email = $this->input->post("email");

		if(!$email)
			die("false");

		$user = $this->user->getUsers(array('userEmail'=>$email));
		if(!$user)
			die("false");

		die("true");
	}

	function user() {
		if(!$this->user->isLoggedIn()) redirect('login');

		$user = $this->user->getUsers(array('userID' => $this->user->id));
		if(!$user) show_error('You do not exist!', 404);

		$user->userBlob = json_decode($user->userBlob);

		$data['user'] = $user;
		$data['page_title'] = 'Profile settings';
		$this->load->view('settings-profile', $data);
	}

	function following($uID = null) {
		redirect('posts/bookmarks', 'location', 301);
	}

        function ban($userID = 0) {
            if($userID == 0) show_error('That.. user... that user... doesn\'t exist! ;-;', 404);

            if(!$this->user->isLoggedIn()) redirect('login');

            if(!$this->user->isAdmin() && !$this->user->isMod()) show_error('Who do you think you are?! Whatever, you are not somebody with the license to ban!', 403);

            if($this->user->isAdmin($userID) || $this->user->isMod($userID) || $this->user->isWatchman($userID)) redirect('403');

	    $data['user'] = $this->user->getUsers(array('userID'=>$userID));
            if(!$data['user']) show_error('That.. user... that user... doesn\'t exist! ;-;', 404);

            if($_POST) {
		$cuid = $this->user->id;
		$cuip = $this->input->ip_address();
		$this->db->simple_query("insert into actions (actionObjectID, actionUserID, actionType, actionVariant, actionIP) values ('{$userID}', '{$cuid}', 'user', 'do', '{$cuip}')");
                $this->user->updateUser(array('userID'=>$userID, 'userStatus'=>'inactive'));

                $this->user->redis->sAdd("banned:ips", $data['user']->userLastIP);
                $this->user->redis->sAdd("banned:ips", $data['user']->userIP);

                redirect('user/'.$data['user']->userName);
            }

            $this->load->view('ban', $data);
        }

        function unban($userID = 0) {
            if($userID == 0) show_error('That.. user... that user... doesn\'t exist! ;-;', 404);

            if(!$this->user->isLoggedIn()) redirect('login');

            if(!$this->user->isAdmin() && !$this->user->isMod()) show_error('Who do you think you are?! Whatever, you are not somebody with the license to ban!', 403);

            $data['user'] = $this->user->getUsers(array('userID'=>$userID));
            if(!$data['user']) show_error('That.. user... that user... doesn\'t exist! ;-;', 404);

            if($_POST) {
		$cuid = $this->user->id;
		$cuip = $this->input->ip_address();
		$this->db->simple_query("insert into actions (actionObjectID, actionUserID, actionType, actionVariant, actionIP) values ('{$userID}', '{$cuid}', 'user', 'undo', '{$cuip}')");
                $this->user->updateUser(array('userID'=>$userID, 'userStatus'=>'active'));

                $this->user->redis->sRemove("banned:ips", $data['user']->userLastIP);
                $this->user->redis->sRemove("banned:ips", $data['user']->userIP);

                redirect('user/'.$data['user']->userName);
            }

            $this->load->view('unban', $data);
        }

        function valid_url($url)
        {
            $pattern = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
            if (!preg_match($pattern, $url) && !empty($url))
            {
                $this->form_validation->set_message('valid_url', 'Your homepage is meant to be a URL.');
                return FALSE;
            }

            return TRUE;
        }

	function _feed($user) {
		$this->load->helper('xml');
		$threads = $this->chan->retrieveThreads(array('threadCreatorID'=>$user->userID, 'sortBy'=>'threadCreatedAt', 'sortDirection'=>'desc'));

		if(empty($threads)) {
			header("HTTP/1.1 204 No Content");
			die();
		}

		header('Content-type: application/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/"><channel>';
		echo '
		<title>'.$user->userName.'\'s Threadfeed</title>
		<link>'.site_url('user/'.$user->userName.'/feed').'</link>
		<pubDate>'.date('r', human_to_unix($threads[0]->threadCreatedAt)).'</pubDate>
		<language>en</language>
		<description>Threads on TheColorless created by '.$user->userName.'</description>
		';
		foreach($threads as $t) {
			echo '
			<item>
			<title>'.xml_convert($t->threadTitle).'</title>
			<link>'.site_url('thread/'.$t->threadID).'</link>
			<dc:creator>'.$user->userName.'</dc:creator>
			<guid isPermaLink="false">'.site_url('thread/'.$t->threadID).'</guid>
			<pubDate>'.date('r', human_to_unix($t->threadCreatedAt)).'</pubDate>
			<description><![CDATA['.xml_convert($t->threadOP).']]></description>
			<content:encoded><![CDATA['.$this->chan->format($t->threadOP).']]></content:encoded>
			</item>
			';
		}
		echo '</channel></rss>';
	}

	function mark_questionable($pass = "") {
		if($pass !== "nc3m5i7k") {
			header("HTTP/1.1 401 Unauthorized");
			die();
		}

		$param = file_get_contents("php://input");
		$param = json_decode($param);

		if($param->Type !== "HardBounce") {
			header("HTTP/1.1 202 Accepted");
			die();
		}

		if(empty($param)) {
			header("HTTP/1.1 400 Bad Request");
			die();
		}
		$user = $this->user->getUsers(array('userEmail'=>$param->Email));
		if(!$user || $user->userRole >= 1) {
			header("HTTP/1.1 202 Accepted");
			die();
		}
		$update = array('userID'=>$user->userID, 'userEmail'=>$param->Email, 'userStatus'=>'questionable');
		if($this->user->updateUser($update)) {
			header("HTTP/1.1 201 Created");
			die();
		}
	}

	function relate($model, $user2ID) {
		$cuid = $this->user->id;
		if(!$cuid || !is_numeric($user2ID))
			die("failure");

		switch($model) {
			case "friend":
				$status = "friend";
				break;
			case "fiend":
				$status = "fiend";
				break;
			case "love":
				$status = "love";
				break;
			default:
				$status = "friend";

		}

		$success = $this->user->updateRelationship($cuid, $user2ID, $status);
		if($success > 0) {
			$relation = $this->user->isFriend($cuid, $user2ID);
			die(json_encode(array("status"=>"ok", "relation"=>$relation)));
		} else {
			die(json_encode(array("status"=>"error")));
		}
	}

	function friends($userID = NULL) {
		if(!isset($userID))
			$userID = $this->user->id;

		if(!$userID)
			redirect('login');

		$friends = $this->user->getRelationships($userID);
		header("Content-type: text/plain");
		print_r($friends);
	}

}
?>
