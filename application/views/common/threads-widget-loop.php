<ul class="threads-widget-list">
    <?php foreach($threads as $t) {
        $thumb = $this->chan->thumb($t->postID);
        echo '<li>'.
            anchor('thread/'.$t->postID,
                   '<img class="thread-thumb" width="60" height="55" src="'.
//                   ($thumb ? site_url('timthumb.php?src=uploads/'.$thumb.'&amp;w=60&amp;h=55') : site_url('images/no_thumb.jpg')).
                   ($thumb ? site_url('images/no_thumb.jpg') : site_url('images/no_thumb.jpg')).
                   '" alt="'.
                   $t->postTitle.
                   '"><strong>'.
                   $t->postTitle.
                   '</strong>'.
                   '<span class="thread-creator">'.
                   $t->threadCreatorUserName.
                   '</span>'
                   ).
            '</li>';
    } ?>
</ul>
