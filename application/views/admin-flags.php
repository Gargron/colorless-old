<?php $this->load->view('common/header'); ?>
<ul id="flagged-threads">
  <?php foreach($threads as $thread) { ?>
    <li>
      <strong>
        <?php echo anchor('/thread/post/'.$thread->postID, (!empty($thread->postTitle) ? $thread->postTitle : "Post " . $thread->postID)); ?>
      </strong><br />
      By <?php echo $thread->userName; ?>
      - flagged by <?php echo $thread->flaggerName; ?><br />
      <em>Reason: <?php echo $this->chan->flagReasons[$thread->flagReason]; ?></em><br />
      Actions:
      <?php echo anchor('/thread/unflag/'.$thread->postID, "Unflag"); ?> | 
      <?php echo anchor('/direct/compose/'.$thread->userName, "Send the owner a PM"); ?>
    </li>
  <?php } ?>
</ul>
<div>
  <a href="/admin/flags/<?php echo $offset + 50 ?>">Next page...</a>
</div>
<?php $this->load->view('common/footer'); ?>
