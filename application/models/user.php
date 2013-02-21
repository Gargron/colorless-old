<?php

/**
 * User
 * @package Users
 */

class User extends Model {
	var $redis;

	var $name;
	var $id;
	var $password;
	var $email;
	var $role;
	var $status;
	var $blob;
	var $colorcode;

	var $session_secret = "AnnuhMaduk87";

	function User() {
		parent::Model();

		$this->redis = new Redis();
		$this->redis->connect('127.0.0.1', 6379);

		if($this->input->cookie("super_session")) {
			$user_data = $this->redis->hGetAll("user:".$this->input->cookie("super_session"));

			if($user_data) {

				$this->name   = $user_data["name"];
				$this->id     = $user_data["id"];
				$this->email  = $user_data["email"];
				$this->role   = $user_data["role"];
				$this->status = $user_data["status"];
				$this->blob   = $user_data["blob"];

				$this->colorcode = isset($user_data["colorcode"]) ? $user_data["colorcode"] : NULL;

				if($this->status == 'inactive') {
					$this->load->view('banned');
				}

			} else {
				setcookie("super_session", FALSE, time()-3600, "/");
			}
		}

		if($this->redis->sContains('banned:ips', isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"])) {
			$this->load->view('banned');
		}
	}

	/**
	 * addUser method creates a user
	 *
	 * Option: Values
	 * --------------
	 * userName
	 * userEmail
	 * userPassword
	 * userBlob
	 * userStatus
	 * userRole
	 *
	 * @param array $options
	 * @return int insert_id()
	 */

	function addUser($options = array()) {
		if(!$this->_required(array('userName', 'userEmail', 'userPassword'), $options))
			return false;

		$options = $this->_default(array('userBlob' => json_encode(new stdClass()), 'userStatus' => 'active', 'userRole' => 0, 'userIP'=>$_SERVER["REMOTE_ADDR"]), $options);

		$this->db->insert('users', $options);

		return $this->db->insert_id();
	}

	/**
	 * updateUser method updates an exisitng user's information
	 *
	 * Option: Values
	 * --------------
	 * userID required
	 * userName
	 * userEmail
	 * userPassword
	 * userBlob
	 * userStatus
	 * userRole
	 *
	 * @param array $options
	 * @return int affected_rows()
	 */

	function updateUser($options = array()) {
		if(!$this->_required(array('userID'), $options))
			return false;

		if(isset($options['userName']))
			$this->db->set('userName', $options['userName']);

		if(isset($options['userEmail']))
			$this->db->set('userEmail', $options['userEmail']);

		if(isset($options['userPassword']))
			$this->db->set('userPassword', $this->password($options['userPassword']));

		if(isset($options['userBlob']))
			$this->db->set('userBlob', json_encode($options['userBlob']));

		if(isset($options['userStatus']))
			$this->db->set('userStatus', $options['userStatus']);

		if(isset($options['userRole']))
			$this->db->set('userRole', $options['userRole']);

		$this->db->where('userID', $options['userID']);
		$this->db->update('users');

		$user = $this->getUsers(array('userID' => $options['userID']));

		$userdata = array(
			'name' => $user->userName,
			'email' => $user->userEmail,
			'id' => $user->userID,
			'blob' => $user->userBlob,
			'role' => $user->userRole,
			'status' => $user->userStatus,
			'colorcode' => $user->userColor
		);
		$hash = md5($this->session_secret . $user->userEmail);

		$this->redis->hMset("user:" . $hash, $userdata);
		$this->redis->expireAt("user:" . $hash, time()+(3600*24*14));

		return $this->db->affected_rows();
	}

	/**
	 *
	 */

	function randomPass() {
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;

		while ($i <= 7) {
		    $num = rand() % 33;
		    $tmp = substr($chars, $num, 1);
		    $pass = $pass . $tmp;
		    $i++;
		}

		return $pass;
	}

	/**
	 *
	 */

	function getUsersNum($options = array()) {
		if(isset($options['userID']))
			$this->db->where('userID', $options['userID']);

		if(isset($options['userName']))
			$this->db->where('userName', $options['userName']);

		if(isset($options['userEmail']))
			$this->db->where('userEmail', $options['userEmail']);

		$query = $this->db->get('users');
		return $query->num_rows();
	}

	/**
	 * getUsers method returns user(s) from the database
	 *
	 * Option: Values
	 * --------------
	 * userID
	 * userName
	 * userEmail
	 * userPassword
	 * userStatus
	 * userRole
	 * limit
	 * offset
	 * sortBy
	 * sortDirection
	 *
	 * Returned Object (array of)
	 * --------------------------
	 * userID
	 * userName
	 * userEmail
	 * userPassword
	 * userBlob
	 * userStatus
	 * userRole
	 * userCreatedAt
	 * userUpdatedAt
	 *
	 * @param array $options
	 * @return array
	 */

	function getUsers($options = array()) {
		if(isset($options['userID']))
			$this->db->where('userID', $options['userID']);

		if(isset($options['userName']))
			$this->db->where('userName', $options['userName']);

		if(isset($options['userEmail']))
			$this->db->where('userEmail', $options['userEmail']);

		if(isset($options['userPassword']))
			$this->db->where('userPassword', $options['userPassword']);

		if(isset($options['userStatus']))
			$this->db->where('userStatus', $options['userStatus']);

		if(isset($options['userRole']))
			$this->db->where('userRole', $options['userRole']);

		if(isset($options['limit']) && isset($options['offset']))
			$this->db->limit($options['limit'], $options['offset']);
		elseif(isset($options['limit']))
			$this->db->limit($options['limit']);

		if(isset($options['sortBy']) && isset($options['sortDirection']))
			$this->db->order_by($options['sortBy'], $options['sortDirection']);

		$query = $this->db->get('users');

		if(isset($options['userID']) || isset($options['userName']) || isset($options['userEmail']))
			return $query->row(0);

		return $query->result();
	}

	/**
	 * numActiveUsers method counts active users
	 *
	 * @return int num_rows()
	 */

	function numActiveUsers() {
	        $this->db->select('count(*) as numUsers');
		$this->db->where('userStatus', 'active');

		$query = $this->db->get('users');

		return $query->row()->numUsers;
	}

	function updateRelationship($userID, $user2ID, $status = "friend") {
		$this->db->query("insert into users_box (boxOwnerID, boxUserID, boxModel) values ('{$userID}', '{$user2ID}', '{$status}') on duplicate key update boxCreatedAt = CURRENT_TIMESTAMP, boxModel = '{$status}'");
		if ($status == "friend" && $this->db->affected_rows() > 0 && $this->isFriend($userID, $user2ID) == 1) {
            $this->sendFriendRequestEmail($userID, $user2ID);
        }
		return $this->db->affected_rows();
	}

	function getRelationships($userID, $offset = NULL, $limit = NULL) {
		$this->db->from('users_box');
		$this->db->where('boxModel', 'friend');
		$query = $this->db->get();

		return $query->result();
	}

	function numRelationships($userID) {
		$this->db->from('users_box');
		$this->db->where('boxModel', 'friend');
		$query = $this->db->get();

		return $query->num_rows();
	}

	function getBadges($userID = 0, $status = "given") {
		$query = $this->db->query('select *, count(badgeID) as badgeAmount from badges_box inner join badges on badgeID = boxBadgeID where (boxUserID = 0'.$userID.' and badgeStatus = "'.$status.'")'. ($status == "pending" ? ' or (boxUserID = 0'.$userID.' and badgeStatus = "given" and badgeModel = "viral") or (badgeModel = "public") or (badgeOwnerID = 0'.$userID.')' : '') .' group by badgeID ' . ($status == "given" ? 'having badgeAmount >= badgeThreshold' : ''));

		return $query->result();
	}

	function giveBadge($userID, $receiverID, $badgeID) {

        // verifies if the user has a badge
		$this->db->where(array("boxUserID" => $userID, "boxBadgeID" => $badgeID, "badgeStatus" => "pending"));
		$query = $this->db->get("badges_box");
		$pending_badge = $query->row()->boxID;

        // verifies if the sender has a given badge
		$this->db->where(array("boxUserID" => $userID, "boxBadgeID" => $badgeID, "badgeStatus" => "given"));
		$query = $this->db->get("badges_box");
		$received_badge = $query->row()->boxID;

		// verifies if the giver already gave it to receiver
		$this->db->where(array("boxUserID" => $receiverID, "boxGiverID" => $userID, "boxBadgeID" => $badgeID, "badgeStatus" => "given"));
		$query = $this->db->get("badges_box");
		$given_badge = $query->row()->boxID;

		// verifies if the receiver has it already, given by anyone
		$this->db->where(array("boxUserID" => $receiverID, "boxBadgeID" => $badgeID, "badgeStatus" => "given"));
		$query = $this->db->get("badges_box");
		$has_badge = $query->row()->boxID;

		// verifies how many the receiver has
		$this->db->select("count(*) as cnt");
		$this->db->where(array("boxUserID" => $receiverID, "boxBadgeID" => $badgeID, "badgeStatus" => "given"));
		$query = $this->db->get("badges_box");
		$has_amount = $query->row()->cnt;

		// verifies if it's a public badge
		$this->db->where(array("badgeID" => $badgeID));
		$query = $this->db->get('badges');
		$badge = $query->row();


	    /* The conditions are:
	        1. The user is an admin giving a badge
	        2. It is a public badge and the giver didn't give it to the receiver yet
	        3. It is a viral badge + the user received it + the receiver doesn't have it yet
	        4. The user "owns" the badge + the receiver doesn't have it yet
	        5. The user has a "pending" badge + the receiver doesn't have it yet
	    */

        if ($this->isAdmin()
                || ($badge->badgeModel == "public" && !$given_badge && $amount < $badge->badgeThreshold)
                || ($badge->badgeModel == "viral" && $received_badge && !$has_badge)
                || ($badge->badgeOwnerID == $userID && !$has_badge)) {

			$this->db->set("boxUserID", $receiverID);
			$this->db->set("boxBadgeID", $badgeID);
			$this->db->set("boxGiverID", $userID);
			$this->db->set("badgeStatus", "given");
			$this->db->insert("badges_box");

            if ($has_amount > $badge->badgeThreshold) {
                $this->sendBadgeNotificationEmail($userID, $receiverID, $badgeID);
            }
		} else if ($pending_badge && !$has_badge) {
		    $this->db->where('boxID', $pending_badge);
			$this->db->set("boxUserID", $receiverID);
			$this->db->set("boxBadgeID", $badgeID);
			$this->db->set("boxGiverID", $userID);
			$this->db->set("badgeStatus", "given");
			$this->db->update("badges_box");

			$this->sendBadgeNotificationEmail($userID, $receiverID, $badgeID);
		}
	}

	function getFriends($userID) {
		$this->db->join("users_box as box1", "users.userID = box1.boxUserID and box1.boxModel = 'friend'", "left");
		$this->db->join("users_box as box2", "users.userID = box2.boxOwnerID and box2.boxModel = 'friend' and box2.boxUserID = box1.boxOwnerID", "left");
		$this->db->where(array("box1.boxOwnerID" => $userID,
					"box2.boxUserID" => $userID));
		$query = $this->db->get('users');
		return $query->result();
	}

	function getRequests($userID) {
		$this->db->join("users_box as box1", "users.userID = box1.boxOwnerID and box1.boxModel = 'friend'", "left");
		$this->db->join("users_box as box2", "users.userID = box2.boxUserID and box2.boxModel = 'friend' and box1.boxUserID = box2.boxOwnerID", "left");
		$this->db->where(array("box1.boxUserID" => $userID,
					"box2.boxOwnerID" => NULL));
		$query = $this->db->get('users');
		return $query->result();
	}

	function getPending($userID) {
		$this->db->join("users_box as box1", "users.userID = box1.boxUserID and box1.boxModel = 'friend'", "left");
		$this->db->join("users_box as box2", "users.userID = box2.boxOwnerID and box2.boxModel = 'friend' and box2.boxUserID = box1.boxOwnerID", "left");
		$this->db->where(array("box1.boxOwnerID" => $userID,
					"box2.boxUserID" => NULL));
		$query = $this->db->get('users');
		return $query->result();
	}

	function getTimeline($userID, $offset) {
		$this->db->select('posts.*, thread.postTitle as threadTitle, users.userName, users.userEmail, users.userRole');
		$this->db->join('users', 'posts.postCreatorID = users.userID', 'inner');
		$this->db->join("posts as thread", "posts.postParentID = thread.postID", "inner");
		// $this->db->join("users_box", "posts.postCreatorID = users_box.boxUserID", "inner");
		$this->db->where("posts.postStatus <>", "deleted");
		$this->db->where("userID in (select boxUserID from `users_box` where boxOwnerID = 9914)", null, false);
		$this->db->order_by('postCreatedAt', 'DESC');
		$this->db->limit(40, $offset);

		$query = $this->db->get('posts');
		return $query->result();
	}

	function getMentions($userID, $offset) {
		$this->db->select('posts.*, thread.postTitle as threadTitle, users.userName, users.userEmail, users.userRole');
		$this->db->join('users', 'posts.postCreatorID = users.userID', 'left');
		$this->db->join("posts as thread", "posts.postParentID = thread.postID", "inner");
		$this->db->join('mentions', 'posts.postID = mentions.mentionPostID', 'left');
		$this->db->where(array("mentions.mentionUserID" => $userID,
				     "posts.postStatus <>" => "deleted"));
		$this->db->order_by('postCreatedAt', 'DESC');
		$this->db->limit(100, $offset);

		$query = $this->db->get('posts');
		return $query->result();
	}

	function getPersonalStats($userID = 0) {
		$this->db->select('count(*) as cnt');
		$this->db->where('postCreatorID', $userID);
		$this->db->where('postStatus <>', 'deleted');
		$query = $this->db->get('posts');
		$post_count = $query->row()->cnt;

		$this->db->select('count(*) as cnt');
		$this->db->where('boxOwnerID', $userID);
		$this->db->where('boxModel', 'friend');
		$query = $this->db->get('users_box');
		$friend_count = $query->row()->cnt;

		$this->db->select('count(*) as cnt');
		$this->db->where('boxOwnerID', $userID);
		$this->db->where('boxModel', 'follow');
		$query = $this->db->get('threads_box');
		$heart_count = $query->row()->cnt;

                $this->db->select('count(*) as cnt');
                $this->db->where('uploadUserID', $userID);
                $query = $this->db->get('uploads');
                $uploads_count = $query->row()->cnt;

		$this->db->select('count(*) as cnt');
		$this->db->join('threads_views', 'mentionthreadid = viewthreadid and viewuserid = mentionuserid and viewlastpostid < mentionpostid', 'inner join');
		$this->db->where('mentionUserID', $userID);
		$query = $this->db->get('mentions');
		$mention_count = $query->row()->cnt;

		$this->db->select('count(*) as cnt');
		$this->db->where('boxOwnerID', $userID);
		$this->db->where('boxModel', 'sender');
		$this->db->where('boxStatus', 'unread');
		$query = $this->db->get('pms_box');
		$pm_count = $query->row()->cnt;

		return array('posts' => $post_count, 'friends' => $friend_count, 'hearts' => $heart_count, 'mentions' => $mention_count, 'pms' => $pm_count, 'uploads' => $uploads_count);
	}

	function getFriendsInChat($userID) {
		$this->db->select('chat_sessions.*, users.userEmail');
		$this->db->join('users', 'chatUser = userName', 'inner');
		$this->db->join('users_box', 'userID = boxUserID', 'inner');
		$this->db->where(array('boxOwnerID' => $userID, 'boxModel' => 'friend'));

		$query = $this->db->get('chat_sessions');
		return $query->result();
	}

	function getStatistics($table, $granularity, $start, $end) {
		if (strpos($table, 'box'))
		    $singular = 'box';
		else if (strpos($table, 'votes'))
		    $singular = 'vote';
		else if (strpos($table, 'view'))
		    $singular = 'view';
		else
		    $singular = substr($table, 0, -1);

		$this->db->select("year(".$singular."createdat) as y, month(".$singular."createdat) as m, ".($granularity == "daily" ? "day(".$singular."createdat) as d, " : "(0) as d, ")." count(*) as v");
		$this->db->group_by("year(".$singular."createdat), month(".$singular."createdat)".($granularity == "daily" ? ", day(".$singular."createdat), " : ""));
		$this->db->where(array($singular."createdat >" => $start,
				       $singular."createdat <" => $end));
		$query = $this->db->get($table);
		return $query->result();
	}

	function numFriends($userID) {
		$this->db->select("user1.*");
		$this->db->from("users_box as user1");
		$this->db->join("users_box as user2", "user2.boxOwnerID = user1.boxUserID", "left");
		$this->db->where(array(
				       "user1.boxModel !="=>"fiend",
				       "user2.boxModel !="=>"fiend",
				       "user1.boxOwnerID"=>$userID,
				       "user2.boxUserID"=>$userID
				       ));
		$query = $this->db->get();

		return $query->num_rows();
	}

	function getCountries() {
		$query = $this->db->get('countries');
		$result = $query->result();

		foreach($result as $country)
		{
			$countries[$country->countryName] = $country->countryName;
		}
		return $countries;
	}

	function isFriend($userID1, $userID2) {
		$query1 = $this->db->query("select * from users_box where boxModel != 'fiend' && boxOwnerID = '{$userID1}' && boxUserID = '{$userID2}'");
		$side1  = ($query1->num_rows() > 0 ? true : false);
		$query2 = $this->db->query("select * from users_box where boxModel != 'fiend' && boxOwnerID = '{$userID2}' && boxUserID = '{$userID1}'");
		$side2  = ($query2->num_rows() > 0 ? true : false);

		if(!$side1 && !$side2) {
			return 0; //No relation
		} elseif ($side1 && !$side2) {
			return 1; //Request sent
		} elseif (!$side1 && $side2) {
			return 2; //Request received
		} elseif ($side1 && $side2) {
			return 3; //Friends
		} else {
			return 0;
		}
	}

	function fuckupRelationship($userID1, $userID2) {
		$this->db->simple_query("delete from users_box where boxOwnerID in('{$userID1}', '{$userID2}') && boxUserID in('{$userID1}', '{$userID2}')");
	}

	/**
	 * isLoggedIn method checks if the user is authentificated
	 *
	 * @return bool
	 */

	function isLoggedIn() {
		if(!empty($this->id)) {
			return true;
		}

		return false;
	}

	/**
	 * isAdmin method checks user for administrative rights
	 *
	 * Roles
	 * -----
	 * 3 Admin
	 * 2 Mod
	 * 1 Watchman
	 * 0 Member
	 *
	 * @param int
	 * @return bool
	 */

	function getRole($userID) {
		$this->db->where('userID', $userID);
		$query = $this->db->get('users');
		$result = $query->row();

		return $result->userRole;
	}

	function isAdmin($userID = 0) {
		if(!$this->isLoggedIn())
			return false;

		if($userID == 0) {
			return ($this->role >= 3);
		} else {
			$user = $this->db->select('userID, userRole')->where('userID', $userID)->get('users')->row();
			return ($user->userRole >= 3);
		}

		return false;
	}

	function isMod($userID = 0) {
		if(!$this->isLoggedIn())
			return false;

		if($userID == 0) {
			return ($this->role >= 2);
		} else {
			$user = $this->db->select('userID, userRole')->where('userID', $userID)->get('users')->row();
			return ($user->userRole >= 2);
		}

		return false;
	}

	function isWatchman($userID = 0) {
		if(!$this->isLoggedIn())
			return false;

		if($userID == 0) {
			return ($this->role >= 1);
		} else {
			$user = $this->db->select('userID, userRole')->where('userID', $userID)->get('users')->row();
			return ($user->userRole >= 1);
		}

		return false;
	}

	/**
	 * isBanned method checks whether the user has been banned
	 *
	 * @param int
	 * @return bool
	 */

	function isBanned($userID = 0) {
		if(!$this->isLoggedIn())
			return false;

		if($userID == 0) {
			return ($this->status == "inactive");
		} else {
			$user = $this->db->select('userID, userStatus')->where('userID', $userID)->get('users')->row();
			return ($user->userStatus == "inactive");
		}

		return false;
	}

	function isQuestionable($userID = 0) {
		if(!$this->isLoggedIn())
			return false;

		if($userID == 0) {
			return ($this->status == "questionable");
		} else {
			$user = $this->db->select('userID, userStatus')->where('userID', $userID)->get('users')->row();
			return ($user->userStatus == "questionable");
		}

		return false;
	}

	/**
	 *
	 */

	function canEdit($postCreatorID) {
		if(!$this->isLoggedIn())
			return false;

		if($postCreatorID == $this->id)
			return true;

		if($this->isAdmin())
			return true;


		return false;
	}

	/**
	 *
	 */

	function canMove($postCreatorID) {
		if(!$this->isLoggedIn())
			return false;

		if($postCreatorID == $this->id)
			return true;

		if($this->isAdmin() || $this->isMod() || $this->isWatchman())
			return true;


		return false;
	}

	/**
	 *
	 */

	function canDelete($postCreatorID) {
		if(!$this->isLoggedIn())
			return false;

		if($postCreatorID == $this->id)
			return true;

		if($this->isAdmin() || $this->isMod())
			return true;


		return false;
	}

	/**
	 *
	 */

	function canBan($userRole = 0) {
		if(!$this->isAdmin() && !$this->isMod())
			return false;

		if($userRole > 0)
			return false;

		return true;
	}

	/**
	 *
	 */

	function getClones($userIP) {
		return $this->db->select('userName, userLastIP')->where(array('userLastIP' => $userIP))->get('users')->result();
	}

	/**
	 *
	 */

	function doesWant($userID, $setting) {
		$result = $this->db->select('userID, userBlob')->where('userID', $userID)->get('users')->row();
		$settings = json_decode($result->userBlob);
		if(isset($settings->$setting) && $settings->$setting == true)
			return true;

		return false;
	}

	function forceWant($userID, $setting) {
		$result = $this->db->select('userID, userBlob')->where('userID', $userID)->get('users')->row();
		$settings = json_decode($result->userBlob);
		if(!isset($settings->$setting) || $settings->$setting !== false)
			return true;

		return false;
	}

	function settingArray($userID, $setting) {
		$result = $this->db->select('userID, userBlob')->where('userID', $userID)->get('users')->row();
		$settings = json_decode($result->userBlob);
		$array = (array) $settings->$setting;
		return $array;
	}

	/**
	 *
	 */

	function hisRole($role) {
		switch($role) {
			case 0:
				return "Proud mortal";
			break;
			case 1:
				return "Watchman";
			break;
			case 2:
				return "Demigod";
			break;
			case 3:
				return "God";
			break;
			default:
				return "Proud mortal";
		}
	}

	var $email_template = "<html><body leftmargin=\"0\" marginwidth=\"0\" topmargin=\"0\" marginheight=\"0\" offset=\"0\" bgcolor=\"#2E2E2E\" ><STYLE>a { color:#2a586f; }</STYLE><table width=\"100%\" cellpadding=\"10\" cellspacing=\"0\" class=\"backgroundTable\" bgcolor=\"#2E2E2E\" ><tr><td valign=\"top\" align=\"center\"><table width=\"550\" cellpadding=\"0\" cellspacing=\"0\" background=\"http://thecolorless.net/images/em_bg.jpg\" bgcolor=\"#858585\" style=\"text-shadow:#aaaaaa 0px 1px 1px;\"><tr bgcolor=\"#2E2E2E\"><a href=\"http://thecolorless.net\"><IMG SRC=\"http://thecolorless.net/images/em_head.jpg\" BORDER=\"0\" title=\"The Colorless\"  alt=\"The Colorless\" align=\"center\"></a></tr><tr><td valign=\"top\" background=\"http://thecolorless.net/images/em_bg.jpg\" style=\"padding:20px;font-size:13px;color:#333333;line-height:150%;font-family:arial;\"><p><span style=\"font-size:32px;font-weight:bold;letter-spacing:-2px;color:#555555;font-family:arial;line-height:110%;\">%headline%</span><br><br>%body%</p></td></tr><tr background=\"http://thecolorless.net/images/em_foot.jpg\" bgcolor=\"#2E2E2E\" height=\"129\"><td valign=\"top\" style=\"padding:20px;\"><center><span style=\"font-size:11px;color:#5f5f5f;line-height:100%;font-family:verdana;\"><a href=\"http://thecolorless.net\">The Colorless</a> | <a href=\"http://facebook.com/thecolorless\">On Facebook</a> | <a href=\"http://twitter.com/TheColorless\">On Twitter</a> | <a href=\"http://thecolorless.net/profile/settings\">Account settings</a></span></center></td></tr></table></td></tr></table></body></html>";

	/**
	 * sendWelcomeEmail
	 *
	 * @param string $userID, $userName, $userEmail, $userPassword
	 * @return bool
	 */

	function sendWelcomeEmail($userName, $userEmail, $userPassword) {
		$welcome = <<<EOT
Hello for the first time, $userName. Welcome to the Colorless.

You just signed up with this e-mail address, and we needed to test if it was real. Because nobody likes spammers. Not even spammers themselves. Anyways, you are now part of the community. Here're some things you could do to get started:

1. Get an avatar.
------------------
For that, go to http://gravatar.com and create an account with the same e-mail address. Once you did that and logged in into Gravatar, you can upload a picture and set it as your primary avatar. That picture will be your avatar everywhere that supports Gravatar (globally recognizable avatars), including the Colorless.

2. Fill in your profile info
------------------
Once you start posting, people will want to know more about you. Prepare some basic info for your profile page, for that just look into the settings.

3. Chat
------------------
If you are new, it's good to get to know more people. That can be achieved quickest by chatting in the chat. People will welcome you and answer your questions, should you have any.

4. Start posting!
------------------
Just remember that you agreed to our forum rules. No spamming, no NSFW, no racism, and no duplicate threads. If you have a thread idea, try searching for keywords first: perhaps someone already created a thread like that?

5. Have fun :)

Log in: http://thecolorless.net/login?ref=email&type=welcome
Settings: http://thecolorless.net/settings?ref=email&type=welcome
Chat: http://thecolorless.net/chat?ref=email&type=welcome

========================================

This e-mail was intended for $reEmail.
You can manage your notification settings here: http://thecolorless.net/settings?ref=email&type=welcome

--
With utter love from everywhere and nowhere,
The Colorless' Herald
EOT;

		$this->load->library('postmark');
		$this->postmark->to($userEmail, $userName);

		$this->postmark->subject('Welcome to the Colorless');
		$this->postmark->message_plain($welcome);
		$this->postmark->send();
		return true;
	}

	function format_email($headline, $body) {
		$str = $this->email_template;
		$str = str_replace("%headline%", $headline, $str);
		$str = str_replace("%body%", $body, $str);
		return $str;
	}

	function sendNewPassword($userName, $userEmail, $userPassword) {
		$npass = <<<EOT
Good day, $userName.

You (or someone who knows your e-mail address) have requested your old password to be reset, and a new one to be sent to you. Well, here it is:

$userPassword

Login here: http://thecolorless.net/login?ref=email&type=password
Change the password here: http://thecolorless.net/settings?ref=email&type=password

========================================

This e-mail was intended for $reEmail.
You can manage your notification settings here: http://thecolorless.net/settings?ref=email&type=password

--
With utter love from everywhere and nowhere,
The Colorless' Herald
EOT;

		$this->load->library('postmark');
		$this->postmark->to($userEmail, $userName);

		$this->postmark->subject('Password recovery');
		$this->postmark->message_plain($npass);
		$this->postmark->send();
		return true;
	}

	function sendBadgeNotificationEmail($userID, $receiverID, $badgeID) {
	    $this->db->select("userName");
        $this->db->where(array("userID" => $userID));
        $query = $this->db->get('users');
        $senderName = $query->row()->userName;

	    $this->db->select("userName, userEmail");
        $this->db->where(array("userID" => $receiverID));
        $query = $this->db->get('users');
        $reName = $query->row()->userName;
        $reEmail = $query->row()->userEmail;

        $this->db->where('badgeID', $badgeID);
        $query = $this->db->get('badges');
        $badgeTitle = $query->row()->badgeTitle;
        $badgeContent = $query->row()->badgeContent;

    	$request = <<<EOT
O hai $reName,

You've just earned a badge on the Colorless, given to you by $senderName. The badge goes like this:

"$badgeContent"

You can check it out on your profile right now: http://thecolorless.net/user/$reName?ref=email&type=badge

========================================

This e-mail was intended for $reEmail.
You can manage your notification settings here: http://thecolorless.net/settings?ref=email&type=friend

========================================

E-mail notifications kindly sponsored by J-List: http://moe.jlist.com/click/3341

--
With utter love from everywhere and nowhere,
The Colorless' Herald
EOT;

		$this->load->library('postmark');
		$this->postmark->to($reEmail, $reName);

		$this->postmark->subject('You\'ve earned a badge!');
		$this->postmark->message_plain($request);
		$this->postmark->send();
		return true;
	}

	function sendFriendRequestEmail($userID, $receiverID) {
		$this->db->select("userName");
		$this->db->where(array("userID" => $userID));
		$query = $this->db->get('users');
		$senderName = $query->row()->userName;

		$this->db->select("userName, userEmail");
		$this->db->where(array("userID" => $receiverID));
		$query = $this->db->get('users');
		$reName = $query->row()->userName;
		$reEmail = $query->row()->userEmail;

		$request = <<<EOT
Hey $reName,

The user $senderName has just added you as a friend.

You can return the friendship by going to his/her profile, here: http://thecolorless.net/user/$senderName?ref=email&type=friend

========================================

This e-mail was intended for $reEmail.
You can manage your notification settings here: http://thecolorless.net/settings?ref=email&type=friend

========================================

E-mail notifications kindly sponsored by J-List: http://moe.jlist.com/click/3341

--
With utter love from everywhere and nowhere,
The Colorless' Herald
EOT;

		$this->load->library('postmark');
		$this->postmark->to($reEmail, $reName);

		$this->postmark->subject('New friendship request');
		$this->postmark->message_plain($request);
		$this->postmark->send();
		return true;
	}


	/**
	 * authorize method sets user as logged in
	 *
	 * Option: Values
	 * --------------
	 * userName
	 * userPassword
	 *
	 * @param array $options
	 * @return object
	 */

	function authorize($options = array()) {
		error_reporting(E_ALL);
		if(!$this->_required(array('userName', 'userPassword'), $options))
			return false;

		$user = $this->getUsers(array('userName' => $options['userName'], 'userPassword' => $this->password($options['userPassword'])));

		if(!$user) return false;

		$userdata = array(
			'name' => $user->userName,
			'email' => $user->userEmail,
			'id' => $user->userID,
			'blob' => $user->userBlob,
			'role' => $user->userRole,
			'status' => $user->userStatus,
			'colorcode' => $user->userColor
		);
		$hash = md5($this->session_secret . $user->userEmail);
		setcookie('super_session', $hash, time()+(3600*24*14), "/");
		$this->redis->hMset("user:" . $hash, $userdata);
		$this->redis->expireAt("user:" . $hash, time()+(3600*24*14));

		$this->updateActivity($user->userID);
		return true;
	}

	function unauthorize() {
		$this->redis->delete("user:".$this->input->cookie("super_session"));
		$this->name = null;
		$this->id = null;
		$this->email = null;
		$this->blob = null;
		$this->role = null;
		$this->status = null;
		$this->colorcode = null;
	}

	function updateActivity($userID) {
		$this->db->set('userUpdatedAt', date("Y-m-d H:i:s"));
		$this->db->set('userLastIP', isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"]);
		$this->db->where('userID', $userID);
		$this->db->update('users');
		return true;
	}

	/**
	 * _required checks for required fields in an array
	 *
	 * @param array $required The required fields
	 * @param array $data The array to check in
	 * @return bool
	 */

	function _required($required, $data) {
		foreach($required as $field)
			if(!isset($data[$field])) return false;

		return true;
	}

	/**
	 * _password method encrypts the password
	 *
	 * @param string $password
	 * @return string md5()
	 */

	function password($password) {
		return md5($password);
	}

	/**
	 * _default method defaults values in an options array
	 *
	 * @param array $default
	 * @param array $options
	 * @return array
	 */

	function _default($default, $options) {
		return array_merge($default, $options);
	}
}
?>
