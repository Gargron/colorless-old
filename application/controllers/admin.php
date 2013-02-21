<?php

class Admin extends Controller {
        function __construct() {
                parent::Controller();
        }

        function index() {
                if(!$this->user->isAdmin() && !$this->user->isMod()) show_error('Move along, nothing to see here!', 403);

                $this->load->view('admin', $data);
        }

	function user($id) {
		$id = (int) $id;
		if(!$this->user->isAdmin()) show_error('Move along, nothing to see here!', 403);

		if($_POST) {
			$this->user->updateUser(array('userID' => $id, 'userName' => $this->input->post('username'), 'userEmail' => $this->input->post('email'), 'userRole' => $this->input->post('role'), 'userStatus' => $this->input->post('status')));
		}

		$user = $this->user->getUsers(array('userID'=>$id));

		$data['page_title'] = "Administrate user";
		$data['user'] = $user;

		$this->load->view('admin-user', $data);
	}

	function thread($id) {
		if(!$this->user->isAdmin()) show_error('Move along, nothing to see here!', 403);

		$id = (int) $id;
		if($_POST) {
			$role = $this->input->post('role');
			$str = $this->db->update_string('threads', array('threadRole' => $role), "threadID = {$id}");
			$query = $this->db->query($str);
			if ($this->db->affected_rows()) {
				redirect("/thread/".$id);
			} else {
				die("error");
			}
		}
		$thread = $this->chan->retrieveThreads(array('threadID'=>$id));

		$data['page_title'] = "Administrate thread";
		$data['thread'] = $thread[0];

		$this->load->view('admin-thread', $data);
	}

        function flags($offset = 0) {
                if(!$this->user->isAdmin() && !$this->user->isMod() && ($this->user->name !== "Dave")) show_error('Move along, nothing to see here!', 403);

                $data['page_title'] = "Flagged Threads";
                $data['threads'] = $this->chan->getLatestFlags($offset);
                $data['offset'] = $offset;

                $this->load->view('admin-flags', $data);
        }

        function actions($offset = 0) {
                if(!$this->user->isAdmin() && !$this->user->isMod()) show_error('Move along, nothing to see here!', 403);

                $data['page_title'] = "Actions Watcher";
                $data['actions'] = $this->chan->getActions($offset);
                $data['offset'] = $offset;

                $this->load->view('admin-actions', $data);
        }

        function statistics($table = "users", $granularity = "monthly", $start = NULL, $end = NULL) {
                if(!$this->user->isAdmin() && !$this->user->isMod()) show_error('Move along, nothing to see here!', 403);

                if($start == NULL || $end == NULL) {
                    $end = date("Y-m-d");
                    $start = date("Y-m-d", time()-(3600*24*30*6));
                }
                $data['page_title'] = "Statistics";
                $data['table'] = $table;
                $data['granularity'] = $granularity;
                $data['start'] = $start;
                $data['end'] = $end;
                $data['statistics'] = $this->user->getStatistics($table, $granularity, $start, $end);

                $this->load->view('admin-statistics', $data);
        }

        function export() {
                header("Content-Type: text/plain; charset=UTF-8");

                ini_set('display_errors', 1);
                error_reporting(E_ALL);

                if(!$this->user->isAdmin() && !$this->user->isMod()) show_error('Move along, nothing to see here!', 403);

                $query = $this->db->get('users');
                $results = $query->result();

                foreach ($results as $user) {
                    $alias = $user->userName;

            		$blob = json_decode($user->userBlob);
            		if ($blob)
            		    if (isset($blob->userAlias))
            		        $alias = $blob->userAlias;

                    $output = array($user->userEmail,
                        $user->userName,
                        $user->userLastIP,
                        $alias,
                        "\"" . $user->userUpdatedAt . "\"");

                    echo implode(",", $output) . "\n";
                }
        }
}
?>
