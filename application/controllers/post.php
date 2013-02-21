<?php
class Post extends Controller {
  function __construct() {
    parent::Controller();
    $this->load->library('cache');
  }

  function create()
  {
    parse_str(substr(strstr($_SERVER['REQUEST_URI'], '?'), 1), $_GET);
    $data['page_title'] = 'New thread';
    $data['boardID'] = (int) $_GET["board"];

    $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

    /*if(!empty($_FILES['postAttachment']['tmp_name'])) {
        $img = $this->_uploadImage($_FILES['postAttachment']['tmp_name']);
        if(!$img) {
      $this->form_validation->set_message('_uploadImage', 'Image uploading failed.');
        } else {
      $_POST['postContent'] = $img . "\n\n" . $_POST['postContent'];
        }
    }*/

    $this->form_validation->set_rules('postContent', 'content', 'trim|required|min_length[2]|callback__attemptPost');

    if($this->input->post('postParentID')) {

    } else {
        $this->form_validation->set_rules('postTitle', 'thread title', 'trim|required|min_length[2]|max_length[60]');
    }

    if($this->form_validation->run()) {
      //Success!
      $insert = array('postCreatorID'=>$this->user->id, 'postContent'=>$this->input->post('postContent'), 'postBoard'=>$this->input->post('postBoard'));

      if($this->input->post('postParentID')) {
        $data['page_title'] = 'New post in thread #'.$this->input->post('postParentID');
        $insert['postParentID'] = $this->input->post('postParentID');
        $threadExists = $this->chan->getThread($insert['postParentID']);
        if(!$threadExists) {
          show_error("The thread you wanted to post in does not (longer) exist. Here is again your input so that you won't lose it:<br /><br />".$insert['postContent']);
        }
      } else {
        $insert['postTitle'] = $this->input->post('postTitle');
      }

      if($this->user->isQuestionable()) {
        show_error("Your e-mail is unconfirmed. Here is what you wrote so you won't lose it:<br /><br />".$insert['postContent']);
      }

      $new_id = $this->chan->createPost($insert, $this->input->post('postTags'));

      if($new_id) {
        $parent_id = $this->chan->findParentID($new_id);
        $insert['postID'] = $new_id;
        $insert['postContent'] = $this->_preview($insert['postContent']);
        $insert['userName'] = $this->user->name;
        $insert['userHash'] = md5(strtolower($this->user->email));
        $insert['postCreatedAt'] = date("Y-m-d H:i:s");
        $insert['userRole'] = $this->user->role;
        $da_post = json_encode($insert);
        //$this->push("http://127.0.0.1/thread/push?id=".$parent_id, "text/json", "{$da_post}");
        $this->cache->delete_all('threads/'.$parent_id.'/');
        $upload_ids = $this->input->post('postUploadIDs');
        $upload_ids = explode(",", $upload_ids);
        $upload_ids = array_filter($upload_ids);
        if(!empty($upload_ids)) {
          $this->db->set('uploadPostID', $new_id);
          $this->db->set('uploadThreadID', ($this->input->post('postParentID') ? $this->input->post('postParentID') : $new_id));
          $this->db->where_in('uploadID', $upload_ids);
          $this->db->update('uploads');
        }

        $this->chan->attachPoll($new_id, $this->input->post('pollQuestion'),
                                         $this->input->post('pollAnswers'),
                                         $this->input->post('pollStatus'));

        $this->chan->attachEvent($new_id, $this->input->post('eventName'),
                                          $this->input->post('eventLocation'),
                                          $this->input->post('eventStart'),
                                          $this->input->post('eventEnd'),
                                          $this->input->post('eventStatus'));

        redirect('thread/'.$parent_id.$this->chan->getLastPage('', $new_id).'#p'.$new_id);
      }
      $this->form_validation->set_message('_attemptPost', 'Something went wrong.');
    }

    $this->load->view('new-thread', $data);
  }

  function push( $url, $content_type, $raw_data ) {
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
    $fp = fsockopen("tcp://" . $domain, 80, $errno, $errstr, 10);

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

  function edit($postID = 0) {
    if($postID == 0) show_error('Woah, that post totally does not exist.', 404);

    $data['post'] = $this->chan->retrievePosts(array('postID'=>$postID));
    if(!$data['post'])
      show_error('Woah, that post totally does not exist.', 404);

    if(!$this->user->canEdit($data['post']->postCreatorID))
      show_error('You cannot do that, that post is not yours!', 403);

    if($data['post']->postID !== $data['post']->postParentID)
      $data['threadID'] = $data['post']->postID;

    $data['blockBoardChange'] = true;
    if($data['post']->postID == $data['post']->postParentID) {
      $data['reps'] = $this->chan->getThreadImages($postID, 0, 10);
    }
    $data['attachments'] = $this->chan->getPostImages($postID);
    $data['postTags'] = $this->chan->getTags($postID);

    $poll = $this->chan->getThreadPoll($postID);
    if ($poll) {
      $data['has_poll'] = ($poll->pollStatus == "enabled");
      $data['pollQuestion'] = $poll->pollQuestion;

      $answers = $this->chan->getAnswers($poll->pollID);
      foreach($answers as $answer)
        $answer_array[] = $answer->answerContent;
        $data['pollAnswers'] = implode(',', $answer_array);
    }

    $event = $this->chan->getEvent($postID);
    if ($event) {
      $data['has_event'] = ($event->eventStatus == "enabled");
      $data['eventName'] = $event->eventName;
      $data['eventLocation'] = $event->eventLocation;
      $data['eventStart'] = date("d/m/Y", strtotime($event->eventStart));
      $data['eventEnd'] = date("d/m/Y", strtotime($event->eventEnd));
    }

    $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
    $this->form_validation->set_rules('postContent', 'content', 'trim|required|min_length[2]|callback__attemptPost');

    if($this->form_validation->run()) {
      //Woot
      $update = array('postID'=>$postID, 'postContent'=>$this->input->post('postContent'), 'postBoard'=>$this->input->post('postBoard'));
      if($data['post']->postID == $data['post']->postParentID) {
        $update['postTitle'] = $this->input->post('postTitle');
        $this->db->set('threadBoard', $this->input->post('postBoard'));
        $this->db->where('threadID', $data['post']->postID);
        $this->db->update('threads');
      }
      $threadID = $this->chan->findParentID($postID);

      if($this->chan->updatePost($update, $this->input->post('postTags'))) {
        $this->cache->delete_all('threads/'.$threadID);

        // Catch all uploads
        $upload_ids = $this->input->post('postUploadIDs');
        $upload_ids = explode(",", $upload_ids);
        $upload_ids = array_filter($upload_ids);
        if(!empty($upload_ids)) {
          $this->db->set('uploadPostID', $postID);
          $this->db->set('uploadThreadID', $threadID);
          $this->db->where_in('uploadID', $upload_ids);
          $this->db->update('uploads');
        }

        $this->chan->attachPoll($postID, $this->input->post('pollQuestion'),
                                         $this->input->post('pollAnswers'),
                                         $this->input->post('pollStatus'));

        $this->chan->attachEvent($postID, $this->input->post('eventName'),
                                          $this->input->post('eventLocation'),
                                          $this->input->post('eventStart'),
                                          $this->input->post('eventEnd'),
                                          $this->input->post('eventStatus'));

        redirect('thread/'.$threadID.'/offset/'.$this->chan->getPageItsOn($threadID, $postID).'#p'.$postID);
      }
    }
    $data['page_title'] = 'Editing #'.$postID;
    $this->load->view('edit-thread', $data);
  }

  function tags($postID = 0) {
    if(!$this->user->isLoggedIn())
      redirect('login');

    if($postID == 0)
      show_error('Woah, that post totally does not exist.', 404);

    $data['post'] = $this->chan->retrievePosts(array('postID'=>$postID));

    if(!$data['post'])
      show_error('Woah, that post totally does not exist.', 404);

    if($this->input->post('postTags')) {
	$update = array('postID'=>$postID);
	if($this->chan->updatePost($update, $this->input->post('postTags'))) {
		redirect('thread/'.$postID);
	}
    }

    $data['postTags'] = $this->chan->getTags($postID);
    $data['page_title'] = "Editing tags for thread #".$postID;
    $this->load->view('edit-thread-tags', $data);
  }

  function move($postID = 0) {
    if($postID == 0)
      show_error('Woah, that post totally does not exist.', 404);

    $data['post'] = $this->chan->retrievePosts(array('postID'=>$postID));

    if(!$data['post'])
      show_error('Woah, that post totally does not exist.', 404);

    if(!$this->user->canMove($data['post']->postCreatorID))
      show_error('You cannot do that, that post is not yours!', 403);

    if($postID !== $data['post']->postParentID)
      show_error('You can only move threads.');

    if($_POST) {
      $update = array("postID"=>$postID, "postBoard"=>$this->input->post('postBoard'));
      if($this->chan->updatePost($update)) {
        $this->db->set('threadBoard', $this->input->post('postBoard'));
        $this->db->where('threadID', $postID);
        $this->db->update('threads');

        // Mark this on the Action database table.
        $this->chan->doPostAction($postID, 'move', 'Post moved.');

        redirect('thread/'.$postID);
      }
    }
    $data['page_title'] = "Moving thread #".$postID;
    $this->load->view('move-thread', $data);
  }

  function delete($postID) {
    if(!$postID)
      show_error('Woah, that post totally does not exist.', 404);;

    $post = $this->chan->retrievePosts(array('postID'=>$postID));
    if(!$post)
      show_error('Woah, that post totally does not exist.', 404);

    if(!$this->user->canDelete($post->postCreatorID))
      show_error('You cannot do that, that post is not yours!', 403);

    if($_POST) {
      $delete = array('postID'=>$postID, 'postStatus'=>'deleted');

      $cuid = $this->user->id;

      if($cuid !== $post->postCreatorID) {
        $cuip = $this->input->ip_address();
        $this->db->simple_query("insert into actions (actionObjectID, actionUserID, actionType, actionVariant, actionIP) values ('{$postID}', '{$cuid}', 'post', 'do', '{$cuip}')");
      }

      if($post->postID == $post->postParentID) {
        //$this->db->set('postStatus', 'deleted');
        //$this->db->where('postParentID', $postID);
        //$this->db->update('posts');

        $this->db->set('threadStatus', 'deleted');
        $this->db->where('threadID', $postID);
        $this->db->update('threads');

        redirect('');
      } else {
        $this->chan->updatePost($delete);
        $this->db->set('threadItems', 'threadItems -1', FALSE);
        $this->db->where('threadID', $post->postParentID);
        $this->db->update('threads');
        $this->cache->delete_all('threads/'.$post->postParentID);

        redirect('thread/'.$post->postParentID);
      }
    }

    $data['post'] = $post;
    $data['page_title'] = 'Delete #'.$postID;
    $this->load->view('delete-thread', $data);
  }

  function lock($postID) {
    if($postID == 0) show_error('Woah, that post totally does not exist.', 404);

    $data['post'] = $this->chan->retrievePosts(array('postID'=>$postID));
    if(!$data['post'])
      show_error('Woah, that post totally does not exist.', 404);

    if(!$this->user->isAdmin() && !$this->user->isMod()) show_error('Who do you think you are?! Whatever, you are not somebody with the license to lock!', 403);

    if($postID !== $data['post']->postParentID) show_error('You can only move threads.');

    if($_POST) {
      $update = array("postID"=>$postID);
      if($this->chan->updatePost($update)) {
        $this->db->set('threadStatus', "closed");
        $this->db->where('threadID', $postID);
        $this->db->update('threads');

        $this->chan->doPostAction($postID, 'lock', 'Post locked.');
        redirect('thread/'.$postID);
      }
    }
    $data['page_title'] = "Locking thread #".$postID;
    $this->load->view('lock-thread', $data);
  }

  function unlock($postID) {
    if($postID == 0) show_error('Woah, that post totally does not exist.', 404);

    $data['post'] = $this->chan->retrievePosts(array('postID'=>$postID));
    if(!$data['post'])
      show_error('Woah, that post totally does not exist.', 404);

    if(!$this->user->isAdmin())
      show_error('Who do you think you are?! Whatever, you are not somebody with the license to UNlock!', 403);

    if($postID !== $data['post']->postParentID) show_error('You can only move threads.');

    if($_POST) {
      $update = array("postID"=>$postID);
      if($this->chan->updatePost($update)) {
        $this->db->set('threadStatus', "visible");
        $this->db->where('threadID', $postID);
        $this->db->update('threads');
        $this->chan->doPostAction($postID, 'unlock', 'Post unlocked.');
        redirect('thread/'.$postID);
      }
    }
    $data['page_title'] = "Unlocking thread #".$postID;
    $this->load->view('unlock-thread', $data);
  }

  function merge($postID = 0) {
    if($postID == 0)
      show_error('Woah, that post totally does not exist.', 404);

    $data['post'] = $this->chan->retrievePosts(array('postID'=>$postID));
    if(!$data['post'])
      show_error('Woah, that post totally does not exist.', 404);

    if(!$this->user->isAdmin() && !$this->user->isMod())
      show_error('Who do you think you are?! Whatever, you are not somebody with the license to merge!', 403);

    if($postID !== $data['post']->postParentID)
      show_error('You can only merge threads.');

    $data['page_title'] = "Merging thread #".$postID;

    if($_POST) {
      $data['postList'] = $this->chan->getThreadsByName($this->input->post('postName'));
      $data['postName'] = $this->input->post('postName');

      $data['page_title'] = "Merging thread #".$postID." - step 2";

      if($this->input->post('threadID')) {
        $older = min($this->input->post('threadID'), $postID);
        $newer = max($this->input->post('threadID'), $postID);

        if ($older > 0 && $newer > 0) {
          // Actually Merge
          $this->db->set('postParentID', $older);
          $this->db->where('postParentID', $newer);
          $this->db->update('posts');

          // Set old thread as merged
          $this->db->set('threadStatus', 'merged');
          $this->db->where('threadID', $newer);
          $this->db->update('threads');

          // Update Thread
          $this->db->where(array('postParentID' => $older, 'postStatus' => 'visible'));
          $this->db->order_by('postcreatedat', 'desc');
          $this->db->limit(10);
          $query = $this->db->get('posts');
          $result = $query->row();

          $this->db->select('count(*) numPosts');
          $this->db->where(array('postParentID' => $older, 'postStatus' => 'visible'));
          $query = $this->db->get('posts');
          $postCount = $query->row()->numPosts;

          $this->db->set('threadStatus', 'visible');
          $this->db->set('threadLastEntry', $result->postID);
          $this->db->set('threadLastUserID', $result->postCreatorID);
          $this->db->set('threadUpdatedAt', date("Y-m-d H:i:s"));
          $this->db->set('threadItems', $postCount);
          $this->db->where('threadID', $older);
          $this->db->update('threads');

          $this->chan->doPostAction($older, 'merge_with', 'Post merged into some other post.');
          $this->chan->doPostAction($newer, 'merge', 'Other post was merged into this one.');

          redirect('/thread/'.$older);
        }
      }
    }
    $this->load->view('merge-thread', $data);
  }

  function preview() {
    echo $this->chan->format($_POST["raw"]);
  }

  function _preview($d) {
    return $this->chan->format($d);
  }

  function get_images($threadID) {
    $images = $this->chan->getThreadImages($threadID);
    foreach($images as $i) {
      echo '<img src="/uploads/'.$i->uploadFilename.'" width="100" alt="" />';
    }
  }

  function search() {

  }

  function vote() {
    error_reporting(E_ALL);

    $model = $this->input->post('model');
    $postID = $this->input->post('id');
    $cuid = $this->user->id;
    if(!$model || !$postID) {
      die("Reading your mind failed");
    }

    if(!$cuid)
      die("Not authorized");

    if($model == "up") {
      $model = "up";
    } else {
      $model = "down";
    }

    $post = $this->chan->retrievePosts(array('postID'=>$postID));
    if($post->postCreatorID == $cuid) {
      die("Self-egoboosting disabled");
    }

    $this->db->where(array(
               'voteUserID'=>$cuid,
               'votePostID'=>$postID
               ));
    $query1 = $this->db->get('posts_votes');

    $res = new stdClass;
    $res->model = NULL;
    $res->count = NULL;

    if($query1->num_rows() > 0 && $query1->row(0)->voteModel == $model) {
      //Do nothing
      $res->model = $model;
      $res->count = $this->chan->countVotes($postID);
      die(json_encode($res));
    } else if($query1->num_rows() > 0 && $query1->row(0)->voteModel !== $model) {
      //Update record
      $this->db->set('voteModel', $model);
      $this->db->where(array(
               'voteUserID'=>$cuid,
               'votePostID'=>$postID
               ));
      $this->db->update('posts_votes');

      if($model == "up") {
        $this->db->set("voteModelUp", "voteModelUp+1", false);
        $this->db->set("voteModelDown", "voteModelUp-1", false);
      } else {
        $this->db->set("voteModelUp", "voteModelUp-1", false);
        $this->db->set("voteModelDown", "voteModelUp+1", false);
      }
      $this->db->where("votePostID", $postID);
      $this->db->update("posts_votes_buffer");

      $res->model = $model;
      $res->count = $this->chan->countVotes($postID);
      die(json_encode($res));
    } else {
      //Insert record
      $this->db->insert('posts_votes', array(
            'votePostID'=>$postID,
            'voteUserID'=>$cuid,
            'voteModel'=>$model
            ));

      $this->db->where("votePostID", $postID);
      $check = $this->db->get("posts_votes_buffer");
      if($check->num_rows() > 0) {
        if($model == "up") {
          $this->db->set("voteModelUp", "voteModelUp+1", false);
        } else {
          $this->db->set("voteModelDown", "voteModelDown+1", false);
        }
        $this->db->where("votePostID", $postID);
        $this->db->update("posts_votes_buffer");
      } else {
        if($model == "up") {
          $this->db->set("voteModelUp", "1");
        } else {
          $this->db->set("voteModelDown", "1");
        }
        $this->db->set("votePostID", $postID);
        $this->db->insert("posts_votes_buffer");
      }

      $res->model = $model;
      $res->count = $this->chan->countVotes($postID);
      die(json_encode($res));
    }
  }

  function upload() {
    if(!$this->user->isLoggedIn())
      die(json_encode(array("error"=>"You must be logged in to upload.")));

    $config['upload_path']   = '/home/www/thecolorless.net/www/uploads/';
    $config['allowed_types'] = 'gif|jpg|png';
    $config['encrypt_name']  = TRUE;
    $config['max_size']   = '700';

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload("postUpload")) {
      $error = array('error' => $this->upload->display_errors());
      die(json_encode($error));
    } else {
      $data = $this->upload->data();
      //Check for duplicates
      $new_hash = md5_file($data['full_path']);
      $this->db->where('uploadHash', $new_hash);
      $oh = $this->db->get('uploads');
      $ohr = $oh->result();
      if(!empty($ohr)) {
        unlink($data['full_path']);
        $return = array('filename'=>$ohr[0]->uploadFilename);
        die(json_encode($return));
      }
      //End check
      //$this->_toCDN($data["full_path"], "uploads/".$data["file_name"]);

      $this->db->set('uploadUserID', $this->user->id);
      $this->db->set('uploadFiletype', $data['file_type']);
      $this->db->set('uploadFilename', $data['file_name']);
      $this->db->set('uploadHash', $new_hash);
      $this->db->set('uploadIP', $this->input->ip_address());
      $this->db->set('uploadLocation', 'amazon');
      $this->db->insert('uploads');
      $new_id = $this->db->insert_id();
      die(json_encode(array('filename'=>$data['file_name'], 'id'=>$new_id)));
    }
  }

  function poll($threadID) {
      if(!$this->user->isLoggedIn()) show_error('Hey, you are not logged on!.', 404);

      $poll = $this->chan->getThreadPoll($threadID);

      $this->chan->answerPoll($poll->pollID, $this->user->id, $this->input->post('answer'));

      redirect('/thread/'.$threadID);
  }

  function event($threadID) {
      if(!$this->user->isLoggedIn()) show_error('Hey, you are not logged on!.', 404);

      $event = $this->chan->getEvent($threadID);

      $this->chan->answerEvent($event->eventID, $this->user->id, $this->input->post('answer'));

      redirect('/thread/'.$threadID);
  }

  function findtag() {
    $results = $this->db->query("select tagName from tags where tagModel = 'approved' and tagName like '" . strtolower($_REQUEST['q']) . "%'")->result();
    foreach($results as $result)
      echo $result->tagName . "\n";
  }

  function _attemptPost($postContent) {
    if(!$this->user->isLoggedIn()) {
        $this->form_validation->set_message('_attemptPost', 'You must be logged in to post.');
        return false;
    }

    return true;
  }

  function _uploadImage($postAttachment) {
    $this->load->library('curl');

    $filename = $_FILES['postAttachment']['tmp_name'];
    if(!$filename)
      return false;

    $handle = fopen($filename, "r");
    $file = fread($handle, filesize($filename));

    $pvars = array('image' => base64_encode($file), 'key' => "73beab2e7532f5f808dff68241bc351a");
    $resp = $this->curl->simple_post('http://imgur.com/api/upload.json', $pvars);

    $json = json_decode($resp);
    $image = $json->rsp;

    if($image->stat == "ok") return $image->image->original_image;

    return false;
  }

  function test_node() {
    $this->chan->test_redis();
  }

}
?>
