<?php

/**
 * Chan
 * @package Posts
 */

class Chan extends Model {
    var $redis;

    function Chan() {
        parent::Model();
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    /**
     * createPost method creates a post
     *
     * Option: Values
     * --------------
     * postParentID
     * postCreatorID    required
     * postTitle
     * postContent      required
     * postCreatedAt
     * postUpdatedAt
     * postStatus
     * postBoard
     *
     * @param array $options
     * @return int insert_id()
     */

    function createPost($options = array(), $tags = "", $parent = NULL) {
        if(!$this->_required(array('postCreatorID', 'postContent'), $options))
            return false;

        $options = $this->_default(array('postStatus' => 'visible', 'postBoard' => 0, 'postIP'=>isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"]), $options);
        $threadBoard = $options['postBoard'];
        $options['postTitle'] = strip_tags($options['postTitle']);
        $this->db->insert('posts', $options);
        $new_id = $this->db->insert_id();

        if(!isset($options['postParentID'])) {
            $this->db->set('postParentID', $new_id);
            $this->db->where('postID', $new_id);
            $this->db->update('posts');

            $new_thread = array('threadID' => $new_id, 'threadCreatorID' => $options['postCreatorID'], 'threadItems' => 1, 'threadLastEntry' => $new_id, 'threadLastUserID' => $options['postCreatorID'], 'threadBoard' => $threadBoard, 'threadUpdatedAt'=>date("Y-m-d H:i:s"));
            $this->db->insert('threads', $new_thread);

            $this->insertTags($new_id, $options['postCreatorID'], $tags);

            $this->redis->publish('threads:all', json_encode(array(
                "thread" => $new_id, "data" => array(
                    "title" => $options["postTitle"],
                    "board" => $this->slugBoard($options["postBoard"]),
                    "last_id" => $new_id,
                    "last_user_id" => $this->user->id,
                    "last_user_name" => $this->user->name,
                    "last_updated_at" => time()
                ), "new" => true
            )));
        } else {
            $this->db->set('threadLastEntry', $new_id);
            $this->db->set('threadLastUserID', $options['postCreatorID']);
            $this->db->set('threadUpdatedAt', date("Y-m-d H:i:s"));
            $this->db->set('threadItems', 'threadItems + 1', FALSE);
            $this->db->where('threadID', $options['postParentID']);
            $this->db->update('threads');

            //$thread = $this->retrievePosts(array('id' => $options['postParentID']));

            $this->redis->publish('threads:all', json_encode(array(
                "thread" => $options["postParentID"], "data" => array(
                    "last_id" => $new_id,
                    "last_user_id" => $this->user->id,
                    "last_user_name" => $this->user->name,
                    "last_updated_at" => time()
                )
            )));
            $this->redis->publish('threads:specific', json_encode(array(
                "thread" => $options["postParentID"], "data" => array(
                    "id" => $new_id,
                    "user_id" => $this->user->id,
                    "user" => array(
                        "name" => $this->user->name,
                        "hash" => md5(strtolower($this->user->email)),
                        "role" => $this->user->role
                    ),
                    "content" => $this->format($options["postContent"]),
                    "created_at" => time()
                )
            )));
        }

        $this->catchMentions($new_id, isset($options['postParentID']) ? $options['postParentID'] : $new_id, $options['postCreatorID'], $options['postContent']);

        return $new_id;
    }

    function insertTags($postID, $userID, $tags) {
        $this->db->delete('threads_tags_box', array('boxThreadID' => $postID));

        $tags = split(",", $tags);
        foreach($tags as $tag) {
            $tag = trim(strip_tags($tag));
            $tag = strtolower($tag);
            if (!empty($tag)) {
                    // Find tag on the database
                    $this->db->where('tagSlug', $tag);
                    $query = $this->db->get('tags');
                    $tag_id = $query->row()->tagID;

                // Insert tag if it doesn't exist
                    if ($query->num_rows() == 0) {
                    $slug = strtolower(trim($tag));
                    $slug = preg_replace('/[^a-z0-9_!&\(\)-]/', '_', $slug);
                    $slug = preg_replace('/\-\+/', "-", $slug);

                        $this->db->insert('tags', array('tagSlug' => $slug, 'tagCreatorID' => $userID));
                        $tag_id = $this->db->insert_id();
                    }

                // Append to thread
                    $this->db->insert('threads_tags_box', array('boxThreadID' => $postID, 'boxTagID' => $tag_id));
            }
        }
    }

    function catchMentions($postID, $threadID, $userID, $postContent) {
        $regex = '/(^|[^&;\w])@([^\s\.\´\`<>!\*\'\(\);:@&=\+\$\,\/\?%#\[\]]{3,})?/';
        $match_buffer = array();
        if (preg_match_all($regex, $postContent, $match)) {
            for($a=0; $a < count($match[0]); $a++) {
                $tmpUserName = str_replace("@", "", trim($match[0][$a]));

                if (!in_array($tmpUserName, $match_buffer)) {
                    $this->db->select("userID, userName, userEmail");
                    $this->db->where(array("userName" => $tmpUserName));
                    $query = $this->db->get('users');
                    $mentionedUserID = $query->row()->userID;
                    if ($query->num_rows()) {
                        $this->db->insert('mentions', array('mentionOwnerID' => $userID,
                                                            'mentionUserID' => $mentionedUserID,
                                                            'mentionPostID' => $postID,
                                                            'mentionThreadID' => $threadID));
                        $this->sendMentionNotification($mentionedUserID,
                                                        $userID,
                                                        $threadID,
                                                        $postID,
                                                        strip_tags($postContent));
                    }
                }
            }
        }
    }

    /**
     * findParentID method looks up the parent ID of a post
     *
     * @param int $postID
     * @return int threadID
     */

    function findParentID($postID) {
        $this->db->select('postID, postParentID');
        $this->db->from('posts');
        $this->db->where('postID', $postID);
        $query = $this->db->get();
        return $query->row()->postParentID;
    }

    /**
     * getLastPage method does what it says
     *
     */

    function getLastPage($itemsNumber, $postID = 0) {
        if($itemsNumber == '' && $postID !== 0) {
            $threadID = $this->findParentID($postID);
            $this->db->select('threadItems');
            $this->db->where('threadID', $threadID);
            $query = $this->db->get('threads');
            $result = $query->row();
            $itemsNumber = $result->threadItems;
        }

        $pageNum = (ceil($itemsNumber/20) * 20) - 20;

        if($pageNum == 0)
            return '';

        return '/offset/'.$pageNum;
    }

    /**
     * Get the page number of a specific post
     *
     */

    function getPageItsOn($threadID = 0, $postID = 0) {
        if($threadID == 0 && $postID !== 0) {
                $threadID = $this->findParentID($postID);
        }
        $this->db->select('count(postID) as postsUntilOurs');
        $this->db->where(array('postParentID' => $threadID, 'postStatus <>' => 'deleted'));
                $this->db->where('postID <=', $postID);
        $query = $this->db->get('posts');
        $result = $query->row();
        $itemsNumber = $result->postsUntilOurs;

        $pageNum = (ceil($itemsNumber/20) * 20) - 20;

        if($pageNum == 0)
            return 0;

        return $pageNum;
    }

    /**
    * Gets the last post on a thread made by a specific user, or of the OP of the thread if userID = 0
    *
    */

    function getUserLastPost($threadID, $userID = 0) {
        if ($userID == 0) {
            $this->db->where(array('postID' => $threadID, 'postParentID' => $threadID));
            $query = $this->db->get('posts');
            $result = $query->row();
            $userID = $result->postCreatorID;
        }
        $this->db->where(array('postParentID' => $threadID, 'postStatus <>' => 'deleted', 'postCreatorID' => $userID));
        $this->db->order_by('postCreatedAt desc');
        $this->db->limit(1);
        $query = $this->db->get('posts');

        if ($query->num_rows() == 0)
            return 0;

        $result = $query->row();

        return $result->postID;
    }

    /**
     * getActiveUsers method looks up which users are most active
     *
     * Option: Values
     * --------------
     * userStatus
     * userRole
     * limit
     * offset
     * sortBy
     * sortDirection
     */

    function getActiveUsers($options = array()) {
        $options = $this->_default(array('userStatus' => 'active'), $options);
        $this->db->select('count(posts.postID) as userPostsCount, users.userName, users.userEmail');
        $this->db->from('posts');
        $this->db->group_by('users.userID');
        $this->db->join('users', 'posts.postCreatorID=users.userID');

        if(isset($options['userStatus']))
            $this->db->where('users.userStatus', $options['userStatus']);

        if(isset($options['userRole']))
            $this->db->where('users.userRole', $options['userRole']);

        if(isset($options['limit']) && isset($options['offset']))
            $this->db->limit($options['limit'], $options['offset']);
        elseif(isset($options['limit']))
            $this->db->limit($options['limit']);

        if(isset($options['sortBy']) && isset($options['sortDirection']))
            $this->db->order_by($options['sortBy'], $options['sortDirection']);

        $query = $this->db->get();

        return $query->result();
    }

    function getNumActiveUsers($options = array()) {
        $options = $this->_default(array('userStatus' => 'active'), $options);
        $this->db->select('userName, userEmail');
        $this->db->from('users');
        $this->db->where('userUpdatedAt >=', 'DATE_SUB(NOW(), INTERVAL 30 DAY)', false);

        if(isset($options['userStatus']))
            $this->db->where('users.userStatus', $options['userStatus']);

        if(isset($options['userRole']))
            $this->db->where('users.userRole', $options['userRole']);

        if(isset($options['limit']) && isset($options['offset']))
            $this->db->limit($options['limit'], $options['offset']);
        elseif(isset($options['limit']))
            $this->db->limit($options['limit']);

        if(isset($options['sortBy']) && isset($options['sortDirection']))
            $this->db->order_by($options['sortBy'], $options['sortDirection']);

        $query = $this->db->get();

        return $query->num_rows();
    }

    /**
     * numPosts method counts posts in a thread/at all
     *
     * @param int $threadD/nothing
     * @return int count_all_results()
     */

    function numPosts($threadID = 0, $postStatus = 'visible') {
        if($threadID !== 0)
            $this->db->where('postParentID', $threadID);

        $this->db->where('postStatus', $postStatus);

        $this->db->from('posts');
        return $this->db->count_all_results();
    }

    function numThreads($boardID = 0, $threadStatus = 'visible') {
        if($boardID !== 0)
            $this->db->where('threadBoard', $boardID);

        $this->db->where('threadStatus', $threadStatus);

        $this->db->from('threads');
        return $this->db->count_all_results();
    }

    function numUserThreads($userID, $threadStatus = 'visible') {
        $this->db->where('threadCreatorID', $userID);
        $this->db->where('threadStatus', $threadStatus);

        $this->db->from('threads');
        return $this->db->count_all_results();
    }

    function numUserPosts($userID) {
        $this->db->select('count(postID) as userPostsCount');
        $this->db->where('postCreatorID', $userID);

        $this->db->from('posts');
        return $this->db->count_all_results();
    }

    /**
     * retrievePosts method retrieves one or more posts
     *
     * Option: Values
     * --------------
     * postID
     * postParentID
     * postCreatorID
     * postStatus
     * postBoard
     * limit
     * offset
     * sortBy
     * sortDirection
     *
     * Returned Object (array of):
     * ---------------------------
     * postID
     * postParentID
     * postCreatorID
     * postTitle
     * postContent
     * postCreatedAt
     * postUpdatedAt
     * postStatus
     * postBoard
     * userName
     * userEmail
     *
     * @param array $options
     * @return array of objects
     */

    function retrievePosts($options = array()) {
        $options = $this->_default(array('postStatus' => 'visible', 'sortBy'=>'posts.postCreatedAt', 'sortDirection'=>'asc'), $options);

        $this->db->select('posts.*, users.userName, users.userEmail, users.userRole');
        $this->db->from('posts');

        if(isset($options['postID']))
                $this->db->where('postID', $options['postID']);

        if(isset($options['postParentID']))
                $this->db->where('postParentID', $options['postParentID']);

        if(isset($options['postCreatorID']))
                $this->db->where('postCreatorID', $options['postCreatorID']);

        if(isset($options['postStatus']))
                $this->db->where('postStatus', $options['postStatus']);

        if(isset($options['postBoard']))
                $this->db->where('postBoard', $options['postBoard']);

        $this->db->join('users', 'posts.postCreatorID = users.userID', 'left');

        if(isset($options['limit']) && isset($options['offset']))
                $this->db->limit($options['limit'], $options['offset']);
        elseif(isset($options['limit']))
                $this->db->limit($options['limit']);

        if(isset($options['sortBy']) && isset($options['sortDirection']))
                $this->db->order_by($options['sortBy'], $options['sortDirection']);

        $query = $this->db->get();

        if(isset($options['postID']))
            return $query->row(0);

        return $query->result();
    }

    function retrieveUploads($options = array()) {
        $options = $this->_default(array('sortBy'=>'uploadCreatedAt', 'sortDirection'=>'desc', 'limit' => 20), $options);

        if(isset($options['userID']))
            $this->db->where('uploadUserID', $options['userID']);

        if(isset($options['fileName']))
            $this->db->where('uploadFilename RLIKE BINARY', $options['fileName']);

        if(isset($options['sortBy']) && isset($options['sortDirection']))
            $this->db->order_by($options['sortBy'], $options['sortDirection']);

        if(isset($options['limit']) && isset($options['offset']))
                $this->db->limit($options['limit'], $options['offset']);

        elseif(isset($options['limit']))
                $this->db->limit($options['limit']);

        if($options['count'] == TRUE) {
                $this->db->select('count(*) as cnt');
                $query = $this->db->get('uploads');
                return $query->row()->cnt;
        }

        $query = $this->db->get('uploads');

        if (isset($options['fileName']))
            return $query->row();

        return $query->result();
    }

    function deleteUpload($name) {
        $this->db->where('uploadFilename RLIKE BINARY', $name);
        $this->db->delete('uploads');
    }

    function getTagID($tagName) {
        $this->db->where(array('tagSlug' => $tagName));
        $query = $this->db->get('tags');

        return $query->row()->tagID;
    }

    function getTagBySlug($tagSlug) {
        $this->db->where(array('tagSlug' => $tagSlug));
        $query = $this->db->get('tags');

        return $query->row();
    }

    function getTags($threadID) {
        $this->db->where(array('boxThreadID' => $threadID, 'tagModel <>' => 'deleted'));
        $this->db->join('tags', 'tagID = boxTagID', 'inner');
        $query = $this->db->get('threads_tags_box');

        $result = $query->result();

        $tagsarr = array();
        foreach($result as $tag)
                $tagsarr[] = $tag->tagSlug;

        return implode(",", $tagsarr);
    }

    function getTagsB($threadID) {
        $this->db->where(array('boxThreadID' => $threadID));
        $this->db->join('tags', 'tagID = boxTagID', 'inner');
        $query = $this->db->get('threads_tags_box');

        return $query->result();
    }

    function getTagsAll($offset = null, $limit = null) {
        if($offset !== null && $limit !== null)
            $this->db->limit($limit, $offset);

        $this->db->select('tags.*, count(threads_tags_box.boxTagID) as tagCount');
        $this->db->group_by('tags.tagID');
        $this->db->order_by("tagCount", 'desc');
        $this->db->join('tags', 'tags.tagID = threads_tags_box.boxTagID', 'left');
        $tags = $this->db->get('threads_tags_box');
        return $tags->result();
    }

    function updateViews($threadID, $userID) {
        $this->db->where(array("threadID" => $threadID));
        $query = $this->db->get("threads");
        $lastPost = $query->row()->threadLastEntry;

        $this->db->select("viewThreadID");
        $this->db->where(array("viewThreadID" => $threadID, "viewUserID" => $userID));
        $query = $this->db->get("threads_views");
        $result = $query->row();

        if ($result) {
            $this->db->where(array("viewThreadID" => $threadID, "viewUserID" => $userID));
            $this->db->set("viewLastPostID", $lastPost);
            $this->db->set('viewCreatedAt', date("Y-m-d H:i:s"));
            $this->db->update("threads_views");
        }
        else {
            $this->db->insert("threads_views", array("viewThreadID" => $threadID, "viewUserID" => $userID, "viewLastPostID" => $lastPost, 'viewCreatedAt' => date("Y-m-d H:i:s")));
            $this->db->query("update threads set threadViews = threadViews + 1 where threadID = " . $threadID);
        }
    }

    function getThread($threadID) {
        $this->db->select('*');
        $this->db->from('threads');
        $this->db->where('threadID', $threadID);
        $query = $this->db->get();
        $result = $query->row(0);
        if($result->threadStatus) {
            return $result->threadStatus;
        }
        return false;
    }

    function getThreadsByName($threadName) {
        $query = $this->db->query("select posts.*, users.* from posts inner join users on postCreatorID = userID inner join threads on postID = threadID where postID = postParentID and postTitle like '%" . $threadName . "%' and threadStatus = 'visible' order by postCreatedAt desc limit 10");
        return $query->result();
    }

    function getThreadLastUserID($threadID) {
        $this->db->select('*');
        $this->db->from('threads');
        $this->db->where('threadID', $threadID);
        $query = $this->db->get();
        $result = $query->row(0);
        if($result->threadStatus) {
            return $result->threadLastUserID;
        }
        return false;
    }

    function getUsersThreads($userID, $offset = NULL, $limit = NULL) {
        $this->db->select('posts.postCreatorID, posts.postParentID, posts.postCreatedAt, rthread.threadStatus, thread.postID, thread.postTitle, thread.postCreatorID as threadCreatorID, user.userName as threadCreatorUserName, posts.postID as replyID');
        $this->db->from('posts');
        $this->db->join('posts as thread', 'thread.postID = posts.postParentID', 'left');
        $this->db->join('users as user', 'user.userID = thread.postCreatorID', 'left');
        $this->db->join('threads as rthread', 'rthread.threadID = thread.postID', 'left');
        $this->db->where('posts.postCreatorID', $userID);
        //$this->db->where('rthread.threadStatus', 'visible');
        //$this->db->group_by('thread.postID');
        $this->db->order_by('posts.postCreatedAt', 'desc');

        if(isset($offset) && isset($limit)) {
            $this->db->limit($limit * 10, $offset);
        }
        $query = $this->db->get();
        $result = $query->result();

        // Group, limit and filter in PHP because it takes ~ 4 to 12 seconds in MySQL.
        $final_result = array();
        $thread_buffer = array();
        foreach($result as $row) {
            if (count($final_result) < $limit && $row->threadStatus == 'visible' && !in_array($row->postParentID, $thread_buffer)) {
                $final_result[] = $row;
                $thread_buffer[] = $row->postParentID;
            }
        }

        return $final_result;
    }

    function getPopularThreads($offset = NULL, $limit = NULL, $range = NULL) {
        $this->db->select('posts.postID, posts.postCreatedAt, users.userName as threadCreatorUserName, posts.postCreatorID, posts.postTitle, count(threads_box.boxThreadID) as threadBookmarkCount, threads_box.*');
        $this->db->from('threads_box');
        $this->db->join('posts', 'posts.postID = threads_box.boxThreadID', 'left');
        $this->db->join('users', 'users.userID = posts.postCreatorID', 'left');
        $this->db->group_by('threads_box.boxThreadID');
        $this->db->order_by('threadBookmarkCount', 'desc');
        $this->db->where('threads_box.boxModel', 'follow');

        if(isset($range))
            $this->db->where('threads_box.boxCreatedAt >', 'DATE_SUB(NOW(),INTERVAL '.$range.')', false);

        if(isset($offset) && isset($limit)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query->result();
    }

    function getPopularThreadsF($offset = NULL, $limit = NULL, $range = NULL) {
        $this->db->select('posts.postID as threadID, posts.postCreatedAt as threadCreatedAt, users.userName as threadCreatorUserName, posts.postCreatorID, posts.postTitle as threadTitle, count(threads_box.boxThreadID) as threadBookmarkCount, threads_box.*');
        $this->db->from('threads_box');
        $this->db->join('posts', 'posts.postID = threads_box.boxThreadID', 'left');
        $this->db->join('users', 'users.userID = posts.postCreatorID', 'left');
        $this->db->join('threads', 'threads.threadID = posts.postID', 'left');
        $this->db->group_by('threads_box.boxThreadID');
        $this->db->order_by('threadBookmarkCount', 'desc');
        $this->db->where('threads_box.boxModel', 'follow');
        $this->db->where('threads.threadStatus', 'visible');

        if(isset($range))
            $this->db->where('threads_box.boxCreatedAt >', 'DATE_SUB(NOW(),INTERVAL '.$range.')', false);

        if(isset($offset) && isset($limit)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query->result();
    }

    function getPopularThreadsG($offset = NULL, $limit = NULL, $range = NULL) {
        $this->db->select('posts.postID as threadID, posts.postCreatedAt as threadCreatedAt, users.userName as threadCreatorUserName, posts.postCreatorID, posts.postTitle as threadTitle, count(distinct threads_box.boxID) as threadBookmarkCount, count(distinct threads_views.viewID) as viewCount, count(distinct threads_box.boxID) * 10 + count(distinct threads_views.viewID) as rank, threads_box.*');
        $this->db->from('threads_box');
        $this->db->join('posts', 'posts.postID = threads_box.boxThreadID', 'left');
        $this->db->join('users', 'users.userID = posts.postCreatorID', 'left');
        $this->db->join('threads', 'threads.threadID = posts.postID', 'left');
        $this->db->join('threads_views', 'posts.postID = threads_views.viewThreadID', 'left');
        $this->db->group_by('threads_box.boxThreadID, threads_views.viewThreadID');
        $this->db->order_by('rank', 'desc');
        $this->db->where('threads_box.boxModel', 'follow');
        $this->db->where('threads.threadStatus', 'visible');
        $this->db->where('threads.threadBoard <>', 8);
        $this->db->where('threads.threadBoard <>', 9);

        if(isset($range)) {
            $this->db->where('threads_box.boxCreatedAt >', 'DATE_SUB(NOW(),INTERVAL '.$range.')', false);
            $this->db->where('threads_views.viewCreatedAt >', 'DATE_SUB(NOW(),INTERVAL '.$range.')', false);
        }

        if(isset($offset) && isset($limit)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query->result();
    }

    function getThreadBookmarks($threadID) {
        $this->db->select('*, count(*) as threadBookmarkCount');
        $this->db->where('boxThreadID', $threadID);
        $this->db->where('boxModel', 'follow');
        $this->db->from('threads_box');

        $query = $this->db->get();
        return $query->row(0)->threadBookmarkCount;
    }

    function getThreadViews($threadID) {
        $this->db->where('threadID', $threadID);
        $query = $this->db->get('threads');

        return $query->row()->threadViews;
    }

    function getUserBookmarks($userID) {
        $this->db->select('*, count(*) as userBookmarkCount');
        $this->db->where('boxOwnerID', $userID);
        $this->db->where('boxModel', 'follow');
        $this->db->from('threads_box');

        $query = $this->db->get();
        return $query->row(0)->userBookmarkCount;
    }

    function getThreadImages($threadID, $offset = NULL, $limit = NULL) {
        $this->db->where('uploadThreadID', $threadID);
        $this->db->from('uploads');

        if(isset($offset) && isset($limit))
        $this->db->limit($limit, $offset);

        $query = $this->db->get();

        return $query->result();
    }

    function getPostImages($postID, $offset = NULL, $limit = NULL) {
        $this->db->where('uploadPostID', $postID);
        $this->db->from('uploads');

        if(isset($offset) && isset($limit))
            $this->db->limit($limit, $offset);

        $query = $this->db->get();

        return $query->result();
    }

    function countVotes($postID) {
        $this->db->where('votePostID', $postID);;
        $query = $this->db->get('posts_votes_buffer');
        return $query->row(0);
    }

    function attachPoll($threadID, $question, $answers, $status) {
        $poll = $this->getThreadPoll($threadID);

        if ($poll) {
            if (empty($question) || empty($answers))
                return;

            // Update question and status
            $this->db->where('pollID', $poll->pollID);
            $this->db->set('pollQuestion', $question);
            $this->db->set('pollStatus', $status);
            $this->db->update('threads_polls');

            // Add new answers
            $answer_array = explode(",", $answers);
            foreach($answer_array as $answer) {
                $this->db->where('answerPollID', $poll->pollID);
                $this->db->where('answerContent', trim($answer));
                $query = $this->db->get('threads_polls_answers');

                if(!$query->row()->answerID) {
                    $this->db->set('answerPollID', $poll->pollID);
                    $this->db->set('answerContent', trim($answer));
                    $this->db->insert('threads_polls_answers');
                }
            }

            // Remove old answers
            $old_answers = $this->getAnswers($poll->pollID);
            foreach($old_answers as $answer) {
                if (!in_array($answer->answerContent, $answer_array)) {
                    $this->db->where('answerID', $answer->answerID);
                    $this->db->delete('threads_polls_answers');
                }
            }

            // Recount
            $this->db->query("update threads_polls set pollAnswerCount = (select sum(answerCount) from threads_polls_answers where answerPollID = " . $poll->pollID . ") where pollID = " . $poll->pollID);
        } else {
            if (empty($status) || $status == "disabled" || empty($question) || empty($answers))
                return;

            $this->db->set('pollThreadID', $threadID);
            $this->db->set('pollQuestion', $question);
            $this->db->set('pollStatus', 'enabled');
            $this->db->insert('threads_polls');
            $pollID = $this->db->insert_id();

            $answer_array = explode(",", $answers);
            foreach($answer_array as $answer) {
                $this->db->set('answerPollID', $pollID);
                $this->db->set('answerContent', trim($answer));
                $this->db->insert('threads_polls_answers');
            }
        }
    }

    function getThreadPoll($threadID) {
        $this->db->where(array('pollThreadID' => $threadID));
        $query = $this->db->get('threads_polls');
        return $query->row();
    }

    function getAnswers($pollID) {
        $this->db->where('answerPollID', $pollID);
        $query = $this->db->get('threads_polls_answers');

        return $query->result();
    }

    function getAnswer($pollID, $userID) {
        $this->db->where(array('votePollID' => $pollID, 'voteUserID' => $userID));
        $this->db->join('threads_polls_answers', 'voteAnswerID = answerID', 'inner');
        $query = $this->db->get('threads_polls_votes');

        return $query->row();
    }

    function answerPoll($pollID, $userID, $answerID) {
        $old_answer = $this->getAnswer($pollID, $userID);

        if ($old_answer)
        {
            $this->db->where('voteID', $old_answer->voteID);
            $this->db->set('voteAnswerID', $answerID);
            $this->db->set('voteIP', $this->input->ip_address());
            $this->db->update('threads_polls_votes');

            if($this->db->affected_rows() > 0) {
                $this->db->query('update threads_polls_answers set answerCount = answerCount - 1 where answerID = '. $old_answer->answerID);
                $this->db->query('update threads_polls_answers set answerCount = answerCount + 1 where answerID = '. $answerID);
            }
        }
        else
        {
            $this->db->set('voteUserID', $userID);
            $this->db->set('votePollID', $pollID);
            $this->db->set('voteAnswerID', $answerID);
            $this->db->set('voteIP', $this->input->ip_address());
            $this->db->insert('threads_polls_votes');

            if($this->db->insert_id()) {
                $this->db->query('update threads_polls_answers set answerCount = answerCount + 1 where answerID = '. $answerID);
                $this->db->query('update threads_polls set pollAnswerCount = pollAnswerCount + 1 where pollID = '. $pollID);
            }
        }
    }




    function attachEvent($threadID, $name, $location, $start, $end, $status) {
        $event = $this->getEvent($threadID);

        // The str_replace is to imply european date (dd/mm/yyyy)

        if ($event) {
                $this->db->where('eventID', $event->eventID);
                $this->db->set('eventName', $name);
                $this->db->set('eventLocation', $location);
                $this->db->set('eventStart', date("Y-m-d",strtotime(str_replace('/', '-', $start))));
                $this->db->set('eventEnd', date("Y-m-d",strtotime(str_replace('/', '-', $end))));
                $this->db->set('eventStatus', $status);
                $this->db->update('threads_events');
        } else {
            if (empty($status) || $status == "disabled" || empty($name) || empty($location))
                return;

                $this->db->set('eventThreadID', $threadID);
                $this->db->set('eventName', $name);
                $this->db->set('eventLocation', $location);
                $this->db->set('eventStart', date("Y-m-d",strtotime(str_replace('/', '-', $start))));
                $this->db->set('eventEnd', date("Y-m-d",strtotime(str_replace('/', '-', $end))));
                $this->db->set('eventStatus', 'enabled');
                $this->db->insert('threads_events');
        }
    }

    function getEvent($threadID) {
        $this->db->where('eventThreadID', $threadID);
        $query = $this->db->get('threads_events');

        return $query->row();
    }

    function getAtendees($eventID, $status) {
        $this->db->where(array("atendeeEventID" => $eventID, "atendeeStatus" => $status));
            $this->db->join("users", "atendeeUserID = userID", "inner");
            $query = $this->db->get("threads_events_attendees");

            return $query->result();
    }

    function getEventStatus($eventID, $userID) {
        $this->db->where(array('atendeeEventID' => $eventID, 'atendeeUserID' => $userID));
        $query = $this->db->get('threads_events_attendees');

        return $query->row();
    }

    function answerEvent($eventID, $userID, $answer) {
        if ($answer != 'attending' && $answer != 'pending' && $answer != 'cancelled' && $answer != 'not attending') return;

        $old_status = $this->getEventStatus($eventID, $userID);

        if ($old_status) {
            $this->db->where(array('atendeeEventID' => $eventID, 'atendeeUserID' => $userID));
            $this->db->set('atendeeStatus', $answer);
            $this->db->update('threads_events_attendees');
        }
        else {
            $this->db->set('atendeeEventID', $eventID);
            $this->db->set('atendeeUserID', $userID);
            $this->db->set('atendeeStatus', $answer);
            $this->db->insert('threads_events_attendees');
        }
    }

    function inviteToEvent($eventID, $inviterID, $receiverID) {
        // send PM
    }


    /**
     * format method creates all necessary HTML for output
     *
     * @param string $raw
     * @return string
     */

    function format($raw) {
            return $this->micro_text_format($raw);
    }

    var $micro_text_needles = array(
            '/(^|[^"])(http:\/\/)([^\s"<>\/]{1,32})([^\s"<>]*?) \(([^\)]+?)\)/',
            '/(^|[^"])(?<!\()(http:\/\/)([^\s"<>\/]{1,32})([^\s"<>]*?)($|[\s<>])/',
            '/(^|[^"])href=\"(?<!\()(http:\/\/)(thecolorless.net\/uploads\/)([^\s"<>]*?)(_medium.|_original.)(?:jpg|png|gif|jpeg)\"($|[\s<>])/',
            '/(^|[^"])href=\"(?<!\()(http:\/\/)(thecolorless.net\/uploads\/)([^\s"<>]*?)\"($|[\s<>])/',
            '/(^|[^&;\w])@([^\s\.\´\`<>!\*\'\(\);:@&=\+\$\,\/\?%#\[\]]{3,})?/',
            '/(^|[^"])(http:\/\/[^\s"<>]+?\.(?:jpg|png|gif|jpeg)(?:\?[^\s"<>]*?)?)($|[\s<>])/i',
            '/([^\w]?)\-\-([^\-]+)\-\-[^\w]?/im'
    );

    var $micro_text_poison  = array(
            '\1<a href="\2\3\4" rel="nofollow">\5</a>',
            '\1<a href="\2\3\4" rel="nofollow">\2\3\4</a>\5',
            '\1href="\2thecolorless.net/i/\4" \8',
            '\1href="\2thecolorless.net/i/\4" \6',
            '\1@<a href="/user/\2" rel="nofollow">\2</a>',
            '\1<img src="\2" alt="\2" />\3',
            '\1<span class="spoiler">\2</span>'
    );

    var $allow_tags = 'p|a|em|strong|del|abbr|acronym|b|blockquote|cite|code|pre|i|q|strike|sup|sub|small|big|br';
    var $allow_tags2 = '<p><a><em><strong><del><abbr><acronym><b><blockquote><cite><code><pre><i><q><strike><sup><sub><small><big><br>';

    function micro_prep_text( $text )
    {
        // convert a href, img src to

        $pre = array(
        '/<a href="([^" <>]+?)">([.+?])<\/a>/',
        '/<img src="([^" <>]+?)"( alt="\1")?[\/ ]*>/',
        '/((?<!#video )(http:\/\/(?:www\.)?(?:youtube\.com|vimeo\.com|nicovideo\.jp)\/[^\s"<>]{1,50}))/');

        $pos = array(
        '\1 (\2)',
        '#image \1',
        '#video \1');

        return preg_replace( $pre, $pos, $text );
    }

    function micro_change_poison( $text )
    {
        return preg_replace( $this->micro_text_needles, $this->micro_text_poison, $text );
    }

    function micro_inline_code( $text )
    {
        if( preg_match_all('/#(video|audio|image) (http:\/\/[^\s"<>\/]{2,40}[^\s"<>]*?)($|\s)/', $text, $match) )
        {
            for( $a=0; $a<count($match[0]); $a++ )
            {
                $str    = $match[0][$a];
                $type   = $match[1][$a];
                $url    = $match[2][$a];
                $domain = $this->get_domain($url);

                switch( $type )
                {
                    case 'image':
                        $text = str_replace( $str, '<img src="'. $url .'" alt="'. $url .'" />'. $match[3][$a], $text );
                        break;

                    case 'video':
                        $text = str_replace( $str, $this->micro_video_html( $url, $domain ), $text );
                        break;

                    case 'audio':
                        break;
                }
            }
        }

        return $text;
    }

    function micro_video_html( $url, $domain )
    {
        if( strpos($domain, "youtube")!==false && preg_match('/v=([0-9a-zA-Z\-\_]{1,20})/', $url, $m) )
        {
                /*return  '<object type="application/x-shockwave-flash" '.
                                'width="400" height="300" '.
                                'data="http://www.youtube.com/v/'. $m[1] .'" class="avvid">'.
                                '<param name="movie" value="http://www.youtube.com/v/'. $m[1] .'" />'.
                                '<param name="wmode" value="transparent" />'.
                            '<embed type="application/x-shockwave-flash" width="400" height="300" src="http://www.youtube.com/v/'. $m[1] .'" allowscriptaccess="always" allowfullscreen="true"></embed>'.
                                '</object>';*/
            return '<iframe title="YouTube video player" class="youtube-player" type="text/html" width="480" height="390" src="http://www.youtube.com/embed/'. $m[1] .'" frameborder="0"></iframe>';
        }

        if( strpos($domain, "vimeo")!==false && preg_match('/([0-9]{1,20})(?:\?|$)/', $url, $m) )
        {
                return  '<object type="application/x-shockwave-flash" '.
                                'width="400" height="300" '.
                                'data="http://vimeo.com/moogaloop.swf?clip_id='. $m[1] .'&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1">'.
                                '<param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id='. $m[1] .'&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" />'.
                                '<param name="wmode" value="transparent" />'.
                                '</object>';
        }

        if( strpos($domain, "nicovideo")!==false && preg_match('/\/watch\/([0-9a-zA-Z\-\_]{1,20})/', $url, $m) )
        {
                return  '<script type="text/javascript" src="http://ext.nicovideo.jp/thumb_watch/'. $m[1] .'"></script>'.
                                '<noscript><a href="'. $url .'" rel="nofollow">'.$url.
                                '</a></noscript>';
        }

        return $url;
    }

    function get_domain( $url )
    {
        $domain = str_replace('http://', '', $url);
        $sect   = explode('/', $domain);
        return $sect[0];
    }

    function micro_allow_tags( $text, $tags )
    {
        list( $find, $repl ) = $this->allow_tag_expression( $tags, 3 );
    $find[] = '/&lt;br \/&gt;/ims';
    $repl[] = '<br />';
    $find[] = '/&lt;(del|abbr|acronym|cite|q).*? title="([^"]*)"?.*?&gt;(.*?)&lt;\/\1&gt;/ims';
    $repl[] = '<\1 title="\2">\3</\1>';
        return preg_replace( $find, $repl, $text );
    }

    function allow_tag_expression( $tags, $recurse=1 )
    {
        $find_str   = '/&lt;('. $tags . ')&gt;(.+?)&lt;\/\1&gt;/ims';
        $repl_str   = '<\1>\2</\1>';

        $find_a     = array();
        $repl_a     = array();

        while( $recurse>1 )
        {
                $find_a[] = $find_str;
                $repl_a[] = $repl_str;
                $recurse--;
        }

        return array( $find_a, $repl_a );
    }

    function micro_text_format($text)
    {
            $this->load->helper('markdown');

            $this->load->helper('purifier');
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Filter.Custom', array(new HTMLPurifier_Filter_Youtube()));
            $purifier = new HTMLPurifier($config);

            // $text = $this->micro_allow_tags($text, $this->allow_tags);
            // $text = htmlspecialchars($text, ENT_NOQUOTES);

        $text = str_replace("\n", "  \n", $text);
            $text = $this->micro_prep_text($text);
            $text = $this->micro_inline_code($text);
            $text = $this->micro_change_poison($text);
            $text = markdown($text);
            $text = $purifier->purify($text);

            return $text;
    }

    /**
     * retrieveThreads retrieves threads
     *
     * Option: Values
     * --------------
     * threadCreatorID
     * threadBoard
     * threadStatus
     * limit
     * offset
     * sortBy
     * sortDirection
     *
     * Returned object (array of):
     * ---------------------------
     * threadID
     * threadItems
     * threadLastPost
     * threadCreatorID
     * threadBoard
     * userName
     * userEmail
     *
     * @param array $options
     * @return array of objects
     */

    function retrieveThreads($options = array()) {
        $options = $this->_default(array('threadStatus'=>'visible', 'sortBy'=>'threads.threadUpdatedAt'), $options);

        $this->db->select('threads.*, firstPost.postTitle as threadTitle, firstPost.postCreatedAt as threadCreatedAt, firstPost.postContent as threadOP, creator.userName as threadCreatorUserName, updater.userName as threadUpdaterUserName');
        $this->db->from('threads force index(updated_at)');

        if(isset($options['threadCreatorID']))
                $this->db->where('threads.threadCreatorID', $options['threadCreatorID']);

        if(isset($options['threadBoard']))
                $this->db->where('threads.threadBoard', $options['threadBoard']);

        if(isset($options['excludeBoards']))
                $this->db->where_not_in('threads.threadBoard', $options['excludeBoards']);

        if(isset($options['threadID']))
                $this->db->where('threads.threadID', $options['threadID']);

        if(isset($options['postTags'])) {
            $this->db->where('boxTagID', $options['postTags']);
            $this->db->join('threads_tags_box', 'boxThreadID = threads.threadID', 'inner');
        }

        $this->db->where('threads.threadStatus <>', 'deleted');
        $this->db->where('threads.threadStatus <>', 'merged');
//      if(isset($options['threadStatus']))
//              $this->db->where('threads.threadStatus', $options['threadStatus']);

        $this->db->join('posts as firstPost', 'threads.threadID = firstPost.postID', 'left');
        //$this->db->join('posts as lastPost', 'threads.threadLastEntry = lastPost.postID', 'left');
        $this->db->join('users as creator', 'threads.threadCreatorID = creator.userID', 'left');
        $this->db->join('users as updater', 'threads.threadLastUserID = updater.userID', 'left');

        if(isset($options['limit']) && isset($options['offset']))
                $this->db->limit($options['limit'], $options['offset']);
        elseif(isset($options['limit']))
                $this->db->limit($options['limit']);

        if(isset($options['sortBy']) && isset($options['sortDirection']))
                $this->db->order_by($options['sortBy'], $options['sortDirection']);

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * updatePost method updates a post
     */

    function updatePost($options = array(), $tags = "") {
        if(!$this->_required(array('postID'), $options))
            return false;

        if(isset($options['postTitle']))
            $this->db->set('postTitle', $options['postTitle']);

        if(isset($options['postContent']))
            $this->db->set('postContent', $options['postContent']);

        if(isset($options['postBoard']))
            $this->db->set('postBoard', $options['postBoard']);

        if(isset($options['postStatus']))
            $this->db->set('postStatus', $options['postStatus']);

        $this->db->set('postUpdatedAt', date("Y-m-d H:i:s"));
            $this->db->set('postLastIP', $_SERVER["REMOTE_ADDR"]);

        $this->db->where('postID', $options['postID']);
        $this->db->update('posts');

        $this->insertTags($options['postID'], (isset($options['postCreatorID']) ? $options['postCreatorID'] : $this->user->id), $tags);

        return true;
    }

    /**
     *
     */

    function sendPostNotification($postID, $postContent) {
        /*
        $threadID = $this->findParentID($postID);
        $thread = $this->retrievePosts(array('postID'=>$threadID));
        $threadTitle = $thread->postTitle;
        $threadCreatorID = $thread->postCreatorID;
        if($this->user->doesWant($threadCreatorID, 'settingOwnThreadNotification') && $threadCreatorID !== $this->session->userdata('userID')) {
            $this->load->library('email');

            $user = $this->user->getUsers(array('userID'=>$threadCreatorID));
            $userEmail = $user->userEmail;
            $userName = $user->userName;

            $this->email->from('notifications@thecolorless.net', 'The Colorless Bot');
            $this->email->to($userEmail);

            $this->email->subject('New reply to your thread "'.$threadTitle.'"');
            $this->email->message('Hey '.$userName.'! There\'s a new reply to your thread "'.$threadTitle.'" (#'.$threadID.'). '.base64_decode($this->session->userdata('userName'))." wrote:\n\n".'"'.$postContent.'"'."\n\nYou can view the thread here:\n".site_url('thread/'.$threadID)."\n\nRegards,\nThe Colorless Bot");

            $this->email->send();
            return true;
        }
        return false;
                */
                return true;
    }

    function followThread($userID, $threadID, $model = "follow") {
        $this->db->query("insert into threads_box (boxOwnerID, boxThreadID, boxModel) values ('{$userID}', '{$threadID}', '{$model}') on duplicate key update boxCreatedAt = CURRENT_TIMESTAMP, boxModel = '{$model}'");
        if($model == "follow") {
            $this->db->where('threadID', $threadID)->set('threadHearts', 'threadHearts + 1', FALSE)->update('threads');
            $this->redis->publish("threads:all", json_encode(array(
                "thread" => $threadID, "data" => array(
                    "hearts_plus" => true
                )
            )));
        } else {
            $this->db->where('threadID', $threadID)->set('threadHearts', 'threadHearts - 1', FALSE)->update('threads');
            $this->redis->publish("threads:all", json_encode(array(
                "thread" => $threadID, "data" => array(
                    "hearts_minus" => true
                )
            )));
        }
        return $this->db->affected_rows();
    }

    function getFollowedThreads($userID, $model = "follow", $sort_by = NULL, $sort_dir = "asc", $offset = NULL, $limit = NULL) {
        $this->db->select('thread.postTitle as threadTitle, thread.postCreatedAt as threadCreatedAt, threads.*, users.userName as threadCreatorUserName, l_users.userName as threadUpdaterUserName');
        $this->db->from('threads_box as box');
        $this->db->where('box.boxOwnerID', $userID);
        $this->db->where('box.boxModel', $model);
        $this->db->where('threads.threadStatus', 'visible');

        $this->db->join('threads', 'threads.threadID = box.boxThreadID', 'left');
        $this->db->join('users', 'users.userID = threads.threadCreatorID', 'left');
        $this->db->join('posts as thread', 'thread.postID = box.boxThreadID', 'left');
        $this->db->join('users as l_users', 'l_users.userID = threads.threadLastUserID', 'left');

        if(isset($offset) && isset($limit))
            $this->db->limit($limit, $offset);

        if(isset($sort_by))
            $this->db->order_by($sort_by, $sort_dir);

        $query = $this->db->get();

        return $query->result();
    }

    function numFollowedThreads($userID, $model = "follow") {
        $this->db->select('count(*) as boxBookmarksCount');
        $this->db->from('threads_box as box');
        $this->db->where('box.boxOwnerID', $userID);
        $this->db->where('box.boxModel', $model);
        $this->db->where('threads.threadStatus', 'visible');

        $this->db->join('threads', 'threads.threadID = box.boxThreadID', 'left');

        $query = $this->db->get();

        return $query->row(0)->boxBookmarksCount;
    }

    function isFollowing($userID, $threadID) {
        $this->db->where('boxOwnerID', $userID);
        $this->db->where('boxModel', 'follow');
        $this->db->where('boxThreadID', $threadID);
        $query = $this->db->get('threads_box');
        if($query->num_rows() > 0) {
                return true;
        }
        return false;
    }

    var $flagReasons = array(
        0 => "Low effort/Unconstructive",
        1 => "Spam",
        2 => "Illegal/Inappropriate materials",
        3 => "Wrong board",
        4 => "Duplicate"
    );

    function isFlagged($id) {
        return ($this->db->where("flagPostID", $id)->get("threads_flags")->num_rows() > 0);
    }

    function flag($userID, $id, $reason) {
        $this->db->insert("threads_flags", array(
            "flagPostID" => $id,
            "flagUserID" => $userID,
            "flagReason" => $reason
        ));
        if($this->db->insert_id()) {
            $this->sendFlagNotifications($id, $reason);
        }
    }

    function unflag($id) {
        $this->db->where("flagPostID", $id)->delete("threads_flags");
    }

    function getLatestFlags($offset = 0) {
        $this->db->select("posts.postID, posts.postParentID, users.userName, posts.postTitle, threads_flags.*, flagger.userName as flaggerName");
        $this->db->join("posts", "posts.postID = threads_flags.flagPostID", "inner");
        $this->db->join("users", "users.userID = posts.postCreatorID", "inner");
        $this->db->join("users as flagger", "flagger.userID = threads_flags.flagUserID", "left");
        $this->db->where("posts.postStatus", "visible");
        $this->db->order_by("threads_flags.flagCreatedAt desc");
        $this->db->limit(50, $offset);

        $query = $this->db->get("threads_flags");
        return $query->result();
    }

    function doUserAction($userID, $variant, $reason) {
        $this->db->set("actionObjectID", $userID);
        $this->db->set("actionUserID", $this->user->id);
        $this->db->set("actionType", "user");
        $this->db->set("actionVariant", $variant);
        $this->db->set("actionReason", $reason);
        $this->db->set("actionIP", $_SERVER["REMOTE_ADDR"]);
        $this->db->insert("actions");
    }

    function doPostAction($postID, $variant, $reason) {
        $this->db->set("actionObjectID", $postID);
        $this->db->set("actionUserID", $this->user->id);
        $this->db->set("actionType", "post");
        $this->db->set("actionVariant", $variant);
        $this->db->set("actionReason", $reason);
        $this->db->set("actionIP", $_SERVER["REMOTE_ADDR"]);
        $this->db->insert("actions");
    }

    function getActions($offset = 0) {
        $is_admin = $this->user->isAdmin();

        $this->db->select("*, modd.username as modname");
        $this->db->join("users as modd", "actionUserID = modd.userID", "inner");
        $this->db->join("users as usu", "actionObjectID = usu.userID", "left");
        $this->db->join("threads", "actionObjectID = threadID", "left");
        $this->db->join("posts", "actionObjectID = postID", "left");

        if(!$is_admin)
            $this->db->where("actionUserID", $this->user->id);

        $this->db->limit(50, $offset);
        $this->db->order_by("actionCreatedAt desc");

        $query = $this->db->get("actions");
        return $query->result();
    }

    /**
     *
     */

    var $boards = array(
        0  => 'Random',
        1  => 'Anime',
        2  => 'Music',
        3  => 'Projects',
        4  => 'Gaming',
        5  => 'Advice',
        6  => 'Art',
        7  => 'Staff',
        8  => 'Modboard',
        9  => 'Spam',
        10 => 'World News',
        11 => 'Philosophy',
        12 => 'Science',
        13 => 'Programming',
        14 => 'Funny',
        15 => 'Film',
        16 => 'Writing',
        17 => 'Food',
        18 => 'Groups'
    );

    function get_boards() {
        return $this->boards;
    }

    function boardsToString($exclude) {
        $str = "";
        if(!empty($exclude)) {
            foreach($exclude as $board) {
                $key = $this->chan->slugBoard($board);
                $str .= $key . " ";
            }
        }
        return $str;
    }

    function slugBoard($board) {
        if(is_numeric($board)) {
            return $this->boards[$board];
        } else {
            foreach($this->boards as $key => $name) {
                if(strtolower($name) == $board) {
                    return $key;
                }
            }
        }
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
     * _default method defaults values in an options array
     *
     * @param array $default
     * @param array $options
     * @return array
     */

    function _default($default, $options) {
        return array_merge($default, $options);
    }

    function sendFlagNotifications($threadID, $reasonID) {
        $this->load->library('postmark');
        $thread = $this->db->where('postID', $threadID)->get('posts')->row();
        $mods = $this->db->where('userRole >=', 2)->get('users')->result();
        $title = (! empty($thread->postTitle) ? "The thread \"" . $thread->postTitle . "\"" : "A post");
        foreach($mods as $m) {
            $msg = <<<EOT
Hello {$m->userName},

{$title} was flagged with the following reason:

>{$this->flagReasons[$reasonID]}

Here is the post: http://thecolorless.net/thread/post/{$threadID}

========================================

This e-mail was intended for {$m->userEmail}.
You are a moderator, therefore you are notified.

--
With utter love from everywhere and nowhere,
The Colorless' Herald
EOT;
            $this->postmark->to($m->userEmail, $m->userName);
            $this->postmark->subject('A post was flagged on the Colorless');
            $this->postmark->message_plain($msg);
            $this->postmark->send();
        }
    }

    function sendMentionNotification($receiverID, $senderID, $parentID, $postID, $content) {
        $this->db->select("userName");
        $this->db->where(array("userID" => $senderID));
        $query = $this->db->get('users');
        $seName = $query->row()->userName;

        $this->db->select("userName, userEmail");
        $this->db->where(array("userID" => $receiverID));
        $query = $this->db->get('users');
        $reName = $query->row()->userName;
        $reEmail = $query->row()->userEmail;

        $this->db->select("postTitle");
        $this->db->where(array("postID" => $parentID));
        $query = $this->db->get('posts');
        $reTitle = strip_tags($query->row()->postTitle);

            if($this->user->forceWant($reName, 'settingReceiveMNs')) {
                $newPMmessage = <<<EOT
Hey $reName,

$seName mentioned you on the thread "$reTitle" on the Colorless.

Here you go: http://thecolorless.net/thread/$parentID/post/$postID?ref=email&type=mention

========================================

This e-mail was intended for $reEmail.
You can manage your notification settings here: http://thecolorless.net/settings?ref=email&type=mention

========================================

E-mail notifications kindly sponsored by J-List: http://moe.jlist.com/click/3341

--
With utter love from everywhere and nowhere,
The Colorless' Herald
EOT;

                $this->load->library('postmark');
                $this->postmark->to($reEmail, $reName);
                $this->postmark->subject('You\'ve been mentioned on the Colorless');
                $this->postmark->message_plain($newPMmessage);
                $this->postmark->send();
                return true;
            } else {
                return false;
            }
    }
}
?>
