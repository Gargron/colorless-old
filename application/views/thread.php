<?php $this->load->view('common/header'); ?>
<div class="black-box lone">
    <!-- BuySellAds.com Zone Code -->
    <div id="bsap_1262243" class="bsarocks bsap_6185bec2da7bb340a6a5e59e4e0e5995"></div>
    <!-- End BuySellAds.com Zone Code -->
</div>
<div class="thread-posts hfeed">
        <div class="entry-tags tags round-5" style="margin-bottom:10px;">
            Tagged:
<?php $tags = $this->chan->getTagsB($threadID); ?>
<?php if($tags): ?>
            <?php foreach($tags as $t): echo anchor('tags/'.$t->tagSlug, $t->tagSlug, array('rel'=>'tag')); endforeach; ?>
<?php else: ?>
            <strong class="tis-bad">[not tagged at all]</strong>
<?php endif; ?>
<?php if($this->user->isLoggedIn()): ?>
            <?php echo anchor('post/tags/'.$thread->postID, 'Edit tags!', array('class' => 'tis-action')); ?>
<?php endif; ?>
        </div>

    <?php $poll = $this->chan->getThreadPoll($thread->postID); if ($poll && $poll->pollStatus == 'enabled') { ?>
    <?php echo form_open_multipart('post/poll/'.$thread->postID, array('id'=>'new-post-form', 'class'=>'form-awesome form-awesome-wider round-10')); ?>
        <h3><?php echo $poll->pollQuestion; ?></h3>

        <fieldset style="border: none; margin-bottom: 5px">
            <table>
                <?php $answers = $this->chan->getAnswers($poll->pollID);
                    $my_answer = $this->chan->getAnswer($poll->pollID, $this->user->id);
                    foreach($answers as $answer) {
                    $percentage = $poll->pollAnswerCount == 0 ? 0 : ($answer->answerCount / $poll->pollAnswerCount) * 100; ?>
                    <tr>
                        <td style="width: 200px; text-align: right"><label for="answer-<?php echo $answer->answerID ?>"><?php echo $answer->answerContent; ?></label></td>
                        <td style="width: 30px; padding-right: 5px"><?php if($this->user->isLoggedIn()) echo form_radio(array('name' => 'answer', 'value' => $answer->answerID, 'checked' => ($answer->answerID == $my_answer->voteAnswerID), 'id' => 'answer-'.$answer->answerID)); ?></td>
                        <td style="width: 205px"><img src="/images/poll_beg.png" height="10" width="2" /><img title="<?php echo $answer->answerCount; ?> votes" src="/images/poll_mid.png" height="10" width="<?php echo $percentage * 2; ?>" /><img src="/images/poll_end.png" height="10" width="2" /></td>
                        <td style=""><?php echo intval($percentage); ?>%</td>
                    </tr>
                <?php } ?>
            </table>
            <?php if($this->user->isLoggedIn()) { ?><div style="text-align: center;"><button type="submit" id="new-post-submit"><span>Vote!</span></button></div><?php } ?>
        </fieldset>
    <?php echo form_close(); ?>
    <?php } ?>

    <?php /*if($this->user->isLoggedIn()) { ?>
    <div class="my-pagination">
        <?php $opage = $this->chan->getPageItsOn($thread->postID, $myLastPost); if($myLastPost > 0 && $opage > 0) { echo anchor('/thread/'.$thread->postID.'/offset/'.$opage.'#p'.$myLastPost, ($opage/20) + 1, array('title'=>'Go to your last post in this thread', 'class'=>'tooltipped my-pagination-my')); } ?>
        <?php $opage = $this->chan->getPageItsOn($thread->postID, $opLastPost); if($opLastPost > 0 && $opage > 0) { echo anchor('/thread/'.$thread->postID.'/offset/'.$opage.'#p'.$opLastPost, ($opage/20) + 1, array('title'=>'Go to the thread opener\'s last post in this thread', 'class'=>'tooltipped my-pagination-op')); } ?>
    </div>
    <?php }*/ ?>
    <?php echo $this->pagination->create_links(); ?>
    <?php foreach($posts as $e) { ?>
    <div class="hentry entry author-<?php echo $e->userName; ?> author-role-<?php echo $e->userRole; ?>" id="p<?php echo $e->postID; ?>">
        <address class="entry-author author vcard">
            <img alt="<?php echo $e->userName; ?>" src="http://www.gravatar.com/avatar/<?php echo md5(strtolower($e->userEmail)); ?>?s=64&amp;d=http%3A%2F%2Fthecolorless.net%2Fimages%2Favatars%2Fblack.png" width="64" height="64" class="entry-author-avatar" />
            <?php echo anchor('user/'.$e->userName, $e->userName, array('class'=>'entry-author-username fn nickname url tooltipped-b', 'title'=>($e->userRole > 1 ? 'This user is a staff member' : ''))); ?>
        </address>
        <div class="entry-content">
            <?php echo $this->chan->format($e->postContent); ?>
        </div>
        <div class="entry-meta">
            <ul>
                <li><abbr class="published tooltipped-b timestamp" title="<?php echo $e->postCreatedAt; ?>" data-timestamp="<?php echo human_to_unix($e->postCreatedAt); ?>"><?php $ago = timespan(human_to_unix($e->postCreatedAt)); $ago = explode(',', $ago); echo $ago[0]; ?> ago</abbr></li>
                <li><?php echo anchor(uri_string().'#p'.$e->postID, '#'.$e->postID, array('rel'=>'bookmark')); ?></li>
                <?php if(intval($e->postUpdatedAt) > 0): ?>
                    <li>Updated <?php echo $e->postUpdatedAt; ?></li>
                <?php endif; ?>
                    <?php if($e->postID == $threadID) {
                    if($this->user->canEdit($e->postCreatorID)) {
                        echo '<li>' . anchor('post/edit/'.$e->postID, 'Edit thread') . '</li>';
                    }
                    if($this->user->canDelete($e->postCreatorID)) {
                        echo '<li>' . anchor('post/delete/'.$e->postID, 'Delete thread') . '</li>';
                    }
                    if(($this->user->isMod() || $this->user->isAdmin()) && $threadStatus == 'visible') {
                        echo '<li>' . anchor('post/lock/'.$e->postID, 'Lock thread') . '</li>';
                    }
                    if(($this->user->isAdmin()) && $threadStatus == 'closed') {
                        echo '<li>' . anchor('post/unlock/'.$e->postID, 'Unlock thread') . '</li>';
                    }
                    if(($this->user->isMod() || $this->user->isAdmin()) && ($threadStatus == 'visible' || $threadStatus == 'closed')) {
                        echo '<li>' . anchor('post/merge/'.$e->postID, 'Merge') . '</li>';
                    }
                    if(($this->user->isMod() || $this->user->isAdmin()) && ($threadStatus == 'visible' || $threadStatus == 'closed')) {
                        echo '<li>' . anchor('post/move/'.$e->postID, 'Move') . '</li>';
                    }
                } else { ?>
                <?php
                if($this->user->canEdit($e->postCreatorID)) {
                    echo '<li>' . anchor('post/edit/'.$e->postID, 'Edit post') . '</li>';
                }
                if($this->user->canDelete($e->postCreatorID)) {
                    echo '<li>' . anchor('post/delete/'.$e->postID, 'Delete post') . '</li>';
                }
                if($this->user->isLoggedIn() && $e->postCreatorID !== $this->user->id) {
                    if($this->chan->isFlagged($e->postID)) {
                        if($this->user->role >= 2) {
                            ?><li><a href="/thread/unflag/<?php echo $e->postID; ?>">Unflag</a></li><?php
                        }
                    } else {
                        ?><li><a href="javascript:;" data-reveal-id="flag-et-<?php echo $e->postID; ?>">Flag</a></li><?php
                    }
                }
            }
                ?>
            </ul>
        </div>
    </div>
    <?php if($this->user->isLoggedIn()): ?>
        <div id="flag-et-<?php echo $e->postID; ?>" class="form-awesome form-awesome-wider reveal-modal">
            <?php echo form_open('/thread/flag', '', array("flagPostID" => $e->postID)); ?>
                <ul>
                    <li>
                        <label>Why?</label>
                        <?php echo form_dropdown("flagReason", $this->chan->flagReasons); ?>
                    </li>
                    <li>
                        <button type="submit">Flag</button>
                    </li>
                </ul>
            <?php echo form_close(); ?>
        </div>
        <?php endif; ?>
    <?php
    } ?>
    <?php /*if($this->user->isLoggedIn()) { ?>
    <div class="my-pagination">
        <?php $opage = $this->chan->getPageItsOn($thread->postID, $myLastPost); if($myLastPost > 0 && $opage > 0) { echo anchor('/thread/'.$thread->postID.'/offset/'.$opage.'#p'.$myLastPost, ($opage/20) + 1, array('title'=>'Go to your last post in this thread', 'class'=>'tooltipped my-pagination-my')); } ?>
        <?php $opage = $this->chan->getPageItsOn($thread->postID, $opLastPost); if($opLastPost > 0 && $opage > 0) { echo anchor('/thread/'.$thread->postID.'/offset/'.$opage.'#p'.$opLastPost, ($opage/20) + 1, array('title'=>'Go to the thread opener\'s last post in this thread', 'class'=>'tooltipped my-pagination-op')); } ?>
    </div>
    <?php }*/ ?>
    <?php echo $this->pagination->create_links(); ?>
</div><!--.thread-posts-->
<?php if($this->user->isLoggedIn()) { ?>
<div id="flag-et-<?php echo $threadID; ?>" class="form-awesome form-awesome-wider reveal-modal">
    <?php echo form_open('/thread/flag', '', array("flagPostID" => $threadID)); ?>
        <ul>
            <li>
                <label>Why?</label>
                <?php echo form_dropdown("flagReason", $this->chan->flagReasons); ?>
            </li>
            <li>
                <button type="submit">Flag</button>
            </li>
        </ul>
    <?php echo form_close(); ?>
</div>
<?php
    if($this->user->isQuestionable()) {
    } else {
        if($thread->postBoard == 8 && (!$this->user->isMod() && !$this->user->isAdmin())) {
        } elseif ($threadStatus == 'closed') {
        } else {
            $this->load->view('common/new-post');
        }
    }
} ?>
<?php $this->load->view('common/footer'); ?>
