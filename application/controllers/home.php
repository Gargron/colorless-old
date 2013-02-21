<?php
class Home extends Controller {

	function __construct() {
		parent::Controller();
		$this->load->library('pagination');
	}

	function index()
	{
		$config['base_url'] = base_url() . '/offset/';
		$config['total_rows'] = $this->chan->numThreads();
		$config['per_page'] = '20';
		$config['uri_segment'] = '2';
		$config['full_tag_open'] = '<div class="pagination">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		$set = array('sortBy' => 'threads.threadUpdatedAt', 'sortDirection' => 'desc', 'offset'=>$this->uri->segment(2), 'limit'=>$config['per_page']);
		$set['excludeBoards'] = array(8, 9);

		//Sticky comes first
		$set['sortBy'] = '(threads.threadRole = "sticky") desc, threadUpdatedAt';

		$posts = $this->chan->retrieveThreads($set);
		$data['posts'] = $posts;

		if($this->uri->segment(2) && $this->uri->segment(2) !== 0)
			$data['page_number'] = ceil($this->uri->segment(2) / 20)+1;

		$this->load->view('frontpage', $data);
	}

	function fetch($board = "all", $offset = 0) {
	    parse_str($_SERVER['QUERY_STRING'],$_GET);

	    $offset = ($_GET['page'] - 1) * 20;

	    $set = array('sortBy' => 'threads.threadUpdatedAt', 'sortDirection' => 'desc', 'offset'=>$offset, 'limit'=>20);
		$excl = $this->user->settingArray($this->user->id, 'settingExcludeBoards');

		if ($board != "all") {
		    $set['threadBoard'] = $this->chan->slugBoard($board);
	    } else {
	        $set['excludeBoards'] = array(8, 9);
	    }

		//Sticky comes first
		$set['sortBy'] = '(threads.threadRole = "sticky") desc, threadUpdatedAt';

		$posts = $this->chan->retrieveThreads($set);
		if (count($posts) == 0)
	        return;

        $this->load->view('common/threads-loop', array('posts'=>$posts));
	}

	function board($boardSlug) {
		$boardID = $this->chan->slugBoard(strtolower(str_replace("_", " ", $boardSlug)));
		$boardSlug = ucwords(str_replace("_", " ", $boardSlug));
		$boardDesc = "";

		$data['boardName'] = $boardSlug;
		$data['boardDesc'] = $boardDesc;
		$data['boardID'] = $boardID;

		$config['base_url'] = base_url() . '/board/'.$boardSlug.'/offset/';
		$config['total_rows'] = $this->chan->numThreads($boardID);
		$config['per_page'] = '20';
		$config['uri_segment'] = '4';
		$config['full_tag_open'] = '<div class="pagination">';
		$config['full_tag_close'] = '</div>';

		$this->pagination->initialize($config);

		$set = array('threadBoard' => $boardID, 'sortBy' => 'threads.threadUpdatedAt', 'sortDirection' => 'desc', 'offset'=>$this->uri->segment(4), 'limit'=>$config['per_page']);

		//Sticky comes first
		$set['sortBy'] = '(threads.threadRole = "sticky" or threadrole = "stickyboard") desc, threadUpdatedAt';

		$posts = $this->chan->retrieveThreads($set);
		$data['posts'] = $posts;
		if(!$posts) show_error('That board, sir, is empty.', 404);

		$data['page_title'] = 'Board: '.$boardSlug;
		if($this->uri->segment(4) && $this->uri->segment(4) !== 0)
			$data['page_number'] = ceil($this->uri->segment(4) / 20)+1;

		$this->load->view('board', $data);
	}

	function posts($criteria) {
		$loggedin = $this->user->isLoggedIn();
		$userID = $this->user->id;

		switch($criteria) {
			case "bookmarks":
				if(!$loggedin) redirect('login');

				$posts = $this->chan->getFollowedThreads($userID, 'follow', 'threads.threadUpdatedAt', 'desc', $this->uri->segment(4), 40);
				$posts_num = $this->chan->numFollowedThreads($userID, 'follow');
				$desc = "Browse every thread you ever bookmarked";
			break;
			case "own":
				if(!$loggedin) redirect('login');

				$posts = $this->chan->retrieveThreads(array('threadCreatorID'=>$userID, 'sortBy'=>'threadCreatedAt', 'sortDirection'=>'desc', 'offset'=>$this->uri->segment(4), 'limit'=>40));
				$posts_num = $this->chan->numUserThreads($userID);
				$desc = "Browse your own threads";
			break;
			default:
				$user = $this->user->getUsers(array('userName'=>$criteria));

				if(!$user->userID) show_error('I don\'t show posts, therefore I don\'t exist.', 404);

				$posts = $this->chan->retrieveThreads(array('threadCreatorID'=>$user->userID, 'sortBy'=>'threadCreatedAt', 'sortDirection'=>'desc', 'offset'=>$this->uri->segment(4), 'limit'=>40));
				$posts_num = $this->chan->numUserThreads($user->userID);
				$desc = "Browse all threads ".$user->userName." created";
			break;
		}

		$config['base_url'] = base_url() . '/posts/'.$criteria.'/offset/';
		$config['total_rows'] = $posts_num;
		$config['per_page'] = '20';
		$config['uri_segment'] = '4';
		$config['full_tag_open'] = '<div class="pagination">';
		$config['full_tag_close'] = '</div>';

		$this->pagination->initialize($config);

		$data['posts'] = $posts;
		if(!$posts) show_error('I don\'t show posts, therefore I don\'t exist.', 404);

		$data['page_title'] = 'Collection: '.$criteria;
		$data['page_desc'] = $desc;

		$this->load->view('posts', $data);
	}

	function tags($tagName) {
		$tag = $this->chan->getTagBySlug($tagName);

		if (!$tag) show_error('Thou shalt noth be hereth.', 404);

		$posts = $this->chan->retrieveThreads(array('postTags'=>$tag->tagID, 'sortBy'=>'threadCreatedAt', 'sortDirection'=>'desc', 'offset'=>$this->uri->segment(4), 'limit'=>40));
		$posts_num = 20;
		$desc = "Posts marked with " . $tagName;

		$config['base_url'] = base_url() . '/tags/'.$tagName.'/offset/';
		$config['total_rows'] = $posts_num;
		$config['per_page'] = '20';
		$config['uri_segment'] = '4';
		$config['full_tag_open'] = '<div class="pagination">';
		$config['full_tag_close'] = '</div>';

		$this->pagination->initialize($config);

		$data['posts'] = $posts;
		if(!$posts) show_error('I don\'t show posts, therefore I don\'t exist.', 404);

		$data['page_title'] = 'Posts marked with "' . $tagName . '"';
		$data['page_desc'] = $desc;
		$data['tag'] = $tag;

		$this->load->view('posts', $data);
	}

	function list_of_mods() {
		$users = $this->user->getUsers(array('userRole'=>2));
		header('Content-type: text/plain');
		foreach($users as $user) {
			echo $user->userName."\n";
		}
	}
}
?>
