<?php $this->load->view('common/header'); ?>
<ul id="moderator-actions">
  <?php foreach($actions as $action) { ?>
    <li>
      <?php echo $action->modname; ?>
      <?php if ($action->actionType == "user" && $action->actionVariant == "do") { ?>
        banned
        <?php echo anchor('user/'.$action->userName, $action->userName); ?>
      <?php } elseif ($action->actionType == "user" && $action->actionVariant == "undo") { ?>
        unbanned
        <?php echo anchor('user/'.$action->userName, $action->userName); ?>
      <?php } elseif ($action->actionType == "post" && $action->actionVariant == "do") { ?>
        deleted
        <?php echo anchor('thread/' . $action->postID, $action->postTitle); ?>
      <?php } elseif ($action->actionType == "post" && $action->actionVariant == "undo") { ?>
        recovered
        <?php echo anchor('thread/' . $action->postID, $action->postTitle); ?>
      <?php } elseif ($action->actionType == "post" && $action->actionVariant == "move") { ?>
        moved
        <?php echo anchor('thread/' . $action->postID, $action->postTitle); ?>
      <?php } elseif ($action->actionType == "post" && $action->actionVariant == "lock") { ?>
        locked
        <?php echo anchor('thread/' . $action->postID, $action->postTitle); ?>
      <?php } elseif ($action->actionType == "post" && $action->actionVariant == "unlock") { ?>
        unlocked
        <?php echo anchor('thread/' . $action->postID, $action->postTitle); ?>
      <?php } elseif ($action->actionType == "post" && $action->actionVariant == "merge") { ?>
        merged another thread into this one
        <?php echo anchor('thread/' . $action->postID, $action->postTitle); ?>
      <?php } elseif ($action->actionType == "post" && $action->actionVariant == "merge_with") { ?>
        merged this thread into antoher
        <?php echo anchor('thread/' . $action->postID, $action->postTitle); ?>
      <?php } elseif ($action->actionType == "post" && $action->actionVariant == "stick") { ?>
        sticked
        <?php echo anchor('thread/' . $action->postID, $action->postTitle); ?>
      <?php } elseif ($action->actionType == "post" && $action->actionVariant == "unstick") { ?>
        unsticked
        <?php echo anchor('thread/' . $action->postID, $action->postTitle); ?>
      <?php } ?>

      <?php
        $ago = timespan(human_to_unix($action->actionCreatedAt));
        $ago = explode(',', $ago);
        echo $ago[0].' ago';
      ?>
    </li>
  <?php } ?>
</ul>
<div>
  <a href="/admin/actions/<?php echo $offset + 50 ?>">Next page...</a>
</div>
<?php $this->load->view('common/footer'); ?>
