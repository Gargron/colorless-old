<?php
$popular = $this->cache->model('chan', 'getPopularThreadsG', array(0, 10, '1 DAY'), 1800);
$pthread = $popular[mt_rand(0, count($popular) - 1)];

?>
    <dd class="thread-item-meta"></dd>
    <dt class="thread-item-title">
    <strong><?php echo anchor('thread/'.$pthread->threadID, strip_tags($pthread->threadTitle)); ?></strong>
    <span>By <?php echo anchor('user/'.$pthread->threadCreatorUserName, $pthread->threadCreatorUserName); ?>, <?php $ago = timespan(human_to_unix($pthread->threadCreatedAt)); $ago = explode(',', $ago); echo $ago[0]; ?> ago</span>
    </dt>
    <dd class="thread-item-last" style="background-image: url('<?php echo $pthumb; ?>')">
        <strong><span class="icon heart_16">Bookmarks: </span><?php echo $pthread->threadBookmarkCount; ?>
        <span class="icon book_16">Views: </span><?php echo $pthread->viewCount; ?></strong>
    </dd>
