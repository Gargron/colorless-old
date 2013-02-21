<div class="threads-list">
<?php foreach($posts as $post) { ?>
    <dl class="thread-item <?php if ($post->threadRole != "stickyboard" || $this->uri->segment(1) == "board") { echo $post->threadRole; } ?>" id="thread<?php echo $post->threadID; ?>">
	   <dd class="thread-item-meta">
       P<span class="thread-item-posts"><?php echo $post->threadItems; ?></span><br />&#9829; <span class="thread-item-hearts"><?php echo $post->threadHearts; ?></span>
       </dd>
	   <dt class="thread-item-title">
	       <strong>
            <?php echo ($post->threadRole == "sticky" || ($post->threadRole == "stickyboard" && $this->uri->segment(1) == "board")) ? '<span class="icon star_16"></span>' : NULL; ?>
            <?php echo anchor('thread/'.$post->threadID, strip_tags($post->threadTitle)); ?>
           </strong>
	       <span>
            In <?php echo anchor('board/'.strtolower(str_replace(' ', '_', $this->chan->slugBoard($post->threadBoard))), $this->chan->slugBoard($post->threadBoard)); ?> by <?php echo anchor('user/'.$post->threadCreatorUserName, $post->threadCreatorUserName); ?>,
            <?php $ago = timespan(human_to_unix($post->threadCreatedAt)); $ago = explode(',', $ago); echo $ago[0]; ?>
            ago
           </span>
	   </dt>
	   <dd class="thread-item-last">
            <?php $ago = timespan(human_to_unix($post->threadUpdatedAt)); $ago = explode(',', $ago);
            echo anchor('thread/gotopost/'.$post->threadID.'/'.$post->threadLastEntry.'#p'.$post->threadLastEntry, '<span class="thread-item-last-user-id">'.$post->threadUpdaterUserName.'</span><br /><span class="thread-item-last-updated-at">'.$ago[0].' ago</span>'); ?>
        </dd>
    </dl>
<?php } ?>
</div>
