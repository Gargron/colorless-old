<?php $this->load->view('common/header'); ?>
<h3 class="dash-head">Popular
    <span style="float: right">
        <a id="next_popular" href="#">next</a>
    </span>
</h3>
<dl class="thread-item" id="featured-popular">
<?php $this->load->view('common/popular-widget.php'); ?>
</dl>
<h3 class="dash-head">Friends' activity</h3>
<ul class="dashboard">
  <?php if(!empty($timeline)): $last = 0; $users = array(); foreach($timeline as $e) { ?>
  <li class="dashboard-item">
      <span class="dashboard-avatars">
        <?php $a_i = 0; foreach(array_slice($userlist[$e->postParentID], 1) as $u) { $a_i++; if($a_i==7) { break; } ?>
        <img src="http://gravatar.com/avatar/<?php echo md5(strtolower($u['userEmail'])); ?>?s=16&amp;d=http%3A%2F%2Fthecolorless.net%2Fimages%2Favatars%2Fblack.png" width="16" height="16" class="avatar" />
        <?php } ?>
      </span>
      <?php $u1 = array_values($userlist[$e->postParentID]); $u1 = $u1[0]; ?>
      <img src="http://gravatar.com/avatar/<?php echo md5(strtolower($u1['userEmail'])); ?>?s=16&amp;d=http%3A%2F%2Fthecolorless.net%2Fimages%2Favatars%2Fblack.png" width="16" height="16" class="avatar left" />
      <span class="dashboard-namelist">
        <?php echo format_namelist($userlist[$e->postParentID], 3, "userName", "user/"); ?>
      </span>
      <span class="dashboard-action">
        <?php echo $e->postParentID != $e->postID ? 'replied to' : 'created'; ?>
      </span>
      <span class="dashboard-subject">
        <?php echo anchor('dashboard/gotopost/'.$e->postParentID.'/'.$e->postID.'#p'.$e->postID, $e->threadTitle); ?>
      </span>
      <span class="dashboard-time">
        <?php $ago = timespan(human_to_unix($e->postCreatedAt)); $ago = explode(',', $ago); echo $ago[0]; ?> ago
      </span>
  </li>
  <?php } else: ?>
  <li>No messages (yet). Try befriending some people!</li>
  <?php endif; ?>
</ul>
<?php echo $this->pagination->create_links(); ?>
<?php $this->load->view('common/footer'); ?>
