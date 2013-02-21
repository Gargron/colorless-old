<?php

class Thread extends Controller {
    function __construct() {
        parent::Controller();
    }

    function index() {
        redirect('');
    }

    function view($threadID)
    {
        $threadExists = FALSE;
        $thread = $this->db->select('posts.*, threads.*, users.userName, users.userEmail, users.userRole')->join('threads', 'posts.postID = threads.threadID', 'left')->join('users', 'users.userID = posts.postCreatorID', 'left')->where('posts.postID', $threadID)->where('threads.threadID', $threadID)->get('posts')->row();
        if($thread) {
            $threadExists = $thread->threadStatus;
        }
        
        $this->load->library('cache');
        $this->load->library('pagination');
        $config['base_url'] = base_url().'thread/'.$threadID.'/offset/';
        $config['total_rows'] = $thread->threadItems;
        $config['per_page'] = '20';
        $config['uri_segment'] = '4';
        $config['full_tag_open'] = '<div class="pagination">';
        $config['full_tag_close'] = '</div>';

        $this->pagination->initialize($config);

        $offset = $this->uri->segment(4);
        $offset = (empty($offset) ? 0 : $offset);

        //Get cache for the thread+page
        //$cached_posts = $this->cache->get('threads/'.$threadID.'/'.$offset);
        
        //if(!$cached_posts) {
        //If wasn't cached before, query DB and cache
        $posts = $this->chan->retrievePosts(array('postParentID' => $threadID, 'limit'=>$config['per_page'], 'offset' => $offset, 'sortBy'=>'posts.postID', 'sortDirection'=>'asc'));
        //$this->cache->write($posts, 'threads/'.$threadID.'/'.$offset, 3600);
        //} else {
        //If it was cached, output the cache
        //$posts = $cached_posts;
        //}

        if(!$posts || !$threadExists) show_error('Whoever lead you to this thread was wrong, like seriously. There IS no such thread!', 404);

        if($threadExists == "deleted") show_error('This thread is gone.', 410);

        if($threadExists == "merged") {
          redirect('/thread/'.$postParentID.'/offset/'.$this->chan->getPageItsOn(0, $threadID).'#p'.$threadID);
        }

        $data['threadID'] = $threadID;
        $data['thread'] = $thread;
        $data['posts'] = $posts;
        $data['threadStatus'] = $threadExists;
        $data['page_title'] = $thread->postTitle . ' (Thread)';
        /*$data['myLastPost'] = $this->chan->getUserLastPost($threadID, $this->user->id);
        $data['opLastPost'] = $this->chan->getUserLastPost($threadID, 0);*/

        if ($this->pagination->total_rows <= $this->pagination->per_page || $this->uri->segment(4) == ((ceil($this->pagination->total_rows / $this->pagination->per_page) * $this->pagination->per_page) - $this->pagination->per_page)) {
            $data['is_last'] = TRUE;
        }

        if($this->uri->segment(4) && $this->uri->segment(4) !== 0)
            $data['page_number'] = ceil($this->uri->segment(4) / 20) + 1;

        if ($this->user->isLoggedIn()) {
            $this->chan->updateViews($threadID, $this->user->id);
        }

        $this->load->view('thread', $data);
    }

    function popular_widget() {
        $this->load->view('common/popular-widget.php');
    }

    function flag() {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        if($_POST) {
            if($this->user->isLoggedIn()) {
                $this->chan->flag($this->user->id, $this->input->post('flagPostID'), $this->input->post('flagReason'));
                redirect('thread/'.$this->input->post('flagPostID'));
            }
        }
    }

    function unflag($threadID) {
        if($this->user->role >= 2) {
            $this->chan->unflagThread($threadID);
            redirect('admin/flags');
        }
    }

    function toggle_follow($threadID = NULL) {
        header('Content-type: text/plain');
        if(empty($threadID)) die(json_encode(array('code'=>400, 'error'=>'You did not specify what thread you want to follow/unfollow.')));

        $userID = $this->user->id;
        if(empty($threadID)) die(json_encode(array('code'=>403, 'error'=>'You are not authorized to do this.')));

        $this->db->where('boxOwnerID', $userID);
        $this->db->where('boxThreadID', $threadID);
        $query = $this->db->get('threads_box');
        $status = $query->row(0)->boxModel;
        $to_do = ($status == "block" || !$status ? "follow" : "unfollow");
        switch($to_do) {
            case "follow":
                $this->chan->followThread($userID, $threadID);
                echo json_encode(array('code'=>200, 'status'=>'following', 'count'=>$this->chan->getThreadBookmarks($threadID)));
            break;
            case "unfollow":
                $this->chan->followThread($userID, $threadID, 'block');
                echo json_encode(array('code'=>200, 'status'=>'blocking', 'count'=>$this->chan->getThreadBookmarks($threadID)));
            break;
        }
    }

    function post($postID) {
        $post = $this->db->select('postID, postParentID')->where('postID', $postID)->get('posts')->row();
        $this->gotopost($post->postParentID, $post->postID);
    }

    function gotopost($postParentID, $postID) {
        redirect('/thread/'.$postParentID.'/offset/'.$this->chan->getPageItsOn($postParentID, $postID).'#p'.$postID);
    }
}
?>
