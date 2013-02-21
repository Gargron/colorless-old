<!--<?php
    $create_thread = (!isset($threadID) && !isset($post) ? true : false);
    $edit_thread   = ($create_thread || isset($threadID) ? false : true);
    $create_post   = (!isset($post) && isset($threadID) ? true : false);
    $edit_post     = ($create_post && !isset($threadID) ? false : true);

    setcookie('user_id', $this->session->userdata('userID'), time() + 3600, '/');
    setcookie('security', md5("I am a salty salt ".$this->session->userdata('userID')), time() + 3600, '/');

?>-->
<h3 class="suprahead round-10"><small class="right">You are welcome to share your thoughts, but stick to <?php echo anchor('about/rules', 'the rules', array('target'=>'_blank')); ?>. </small><?php echo ($create_thread ? 'Creating a new thread' : ($edit_thread ? 'Editing a thread' : ($create_post ? 'Replying to a thread' : 'Editing a post'))); ?></h3>
<?php echo form_open((isset($post->postID) ? 'post/edit/'.$post->postID : 'post/create'), array('id' => 'newPost', 'class'=>'form-awesome round-10 form-awesome-wider')); ?>
    <ul>
        <?php if($create_thread || $edit_thread): ?>
        <li>
            <span class="right">Short and descriptive</span>
            <label for="postTitle">Title:</label>
            <?php $title = array('name'=>'postTitle', 'value'=>set_value('postTitle', (isset($post) ? $post->postTitle : false)), 'id'=>'postTitle', 'maxlength'=>'60');
            echo form_input($title); ?>
        </li>
        <?php endif; ?>
        <li>
            <span class="right">Supporting <a href="http://daringfireball.net/projects/markdown/syntax" rel="nofollow" target="_blank">Markdown</a> and HTML syntax since 2009.</span>
            <label for="postContent">Your post:</label>
            <?php $content = array('name'=>'postContent', 'id'=>'postContent', 'value'=>set_value('postContent', (isset($post) ? $post->postContent : false)));
            echo form_textarea($content); ?>
            <hr />
            <div style="font-size:13px; color:#aaa">
                <p>
                <code>*italic*</code>
                &nbsp;
                <code>**bold**</code>
                &nbsp;
                <code>[Text](http://link.com)</code>
                &nbsp;
                <code>`code`</code>
                &nbsp;
                <code>--spoiler--</code>
                </p>
            </div>
            <hr />
        </li>
        <?php if($create_thread || $edit_thread): ?>
        <li>
            <span class="right">Look what tags are being <a href="/about/tags">used</a></span>
            <label for="postTags">Comma-separated tags: <em>All spaces will be replaced with an underscore</em></label>
            <textarea id="postTags" name="postTags"><?php echo $postTags; ?></textarea>
        </li>
        <?php endif; ?>
        <li class="right">
            <button type="button" id="doPreview"><span>Preview</span></button>
            <button type="submit"><span><?php echo ($create_thread ? 'Create thread' : ($edit_thread ? 'Save thread' : ($create_post ? 'Post reply' : 'Save post'))); ?></span></button>
        </li>
        <?php if($create_thread || $edit_thread): ?>
        <li>
            <div class="dropdown-awesome">
                <label>Select the right board:</label>
                <?php
                    $boards = $this->chan->get_boards();
                ?>
                <?php if(!$this->user->isMod() && !$this->user->isAdmin()) {
                    unset($boards[8]);
                } ?>
                <?php echo form_dropdown('postBoard', $boards, (isset($post) ? $post->postBoard : (isset($boardID) ? $boardID : false))); ?>
            </div>
        </li>
        <?php endif; ?>
    </ul>
    <input type="hidden" name="postUploadIDs" id="postUploadIDs" value="<?php echo set_value('postUploadIDs'); ?>" />

    <input type="hidden" name="eventName" id="eventName" />
    <input type="hidden" name="eventLocation" id="eventLocation" />
    <input type="hidden" name="eventStart" id="eventStart" />
    <input type="hidden" name="eventEnd" id="eventEnd" />
    <input type="hidden" name="eventStatus" id="eventStatus" />

    <input type="hidden" name="pollQuestion" id="pollQuestion" />
    <input type="hidden" name="pollAnswers" id="pollAnswers" />
    <input type="hidden" name="pollStatus" id="pollStatus" />

    <?php echo form_hidden('postParentID', set_value('postParentID', (isset($threadID) ? $threadID : false))); ?>
<?php echo form_close(); ?>
<?php /*if($edit_thread): ?>
<h3 class="suprahead round-10"><small class="right">Choose what image should represent this thread. </small>Thread thumbnail</h3>
<form action="#" class="form-awesome round-10 form-awesome-wider">
    <ul class="form-awesome-images">
        <?php
        if(!empty($reps)) {
            foreach($reps as $a) {
                echo '<li><img src="'.($a->uploadLocation == 'amazon' ? 'http://thecolorless.net/uploads/' : 'http://thecolorless.net/uploads/').$a->uploadFilename.'" width="100" /></li>';
            }
        } else {
            echo '<li><img src="/images/no_thumb.jpg" class="nothing" width="100" /></li>';
        }
        ?>
    </ul>
</form>
<?php endif;*/ ?>
<?php /* <h3 class="suprahead round-10"><small class="right">Upload and manage your images here.</small>Attachments</h3>
<form action="#" class="form-awesome round-10 form-awesome-wider">
    <fieldset>
        <legend>Upload</legend>
        <ul>
            <li>
                <span class="right">Only PNG, JPG or GIF up to 700Kb</span>
                <label for="postUpload">Select an image:</label>
                <input type="file" id="postUpload" name="postUpload" />
            </li>
        </ul>
    </fieldset>
    <fieldset>
        <legend>Manage</legend>
        <ul id="postUploadList" class="form-awesome-images">
        <?php
        if(!empty($attachments)) {
            foreach($attachments as $a) {
                echo '<li><img src="'.($a->uploadLocation == 'amazon' ? 'http://thecolorless.net/uploads/' : 'http://thecolorless.net/uploads/').$a->uploadFilename.'" width="100" /><input type="text" readonly="readonly" value="'.($a->uploadLocation == 'amazon' ? 'http://thecolorless.net/uploads/' : 'http://thecolorless.net/uploads/').$a->uploadFilename.'" /></li>';
            }
        } else {
            echo '<li><img src="/images/no_thumb.jpg" class="nothing" width="100" /><input type="text" readonly="readonly" value="http://" /></li>';
        }
        ?>
        </ul>
    </fieldset>
</form>
*/ ?>

<?php if ($create_thread || $edit_thread) { ?>
<span>
<a href="javascript:;" class="nav-toggle-link<?php if (!$has_poll) { echo ' nav-toggle-link-inactive'; } ?>">
    <h3 class="suprahead round-10"><small class="right">Ask a question and get answers.</small>Polls</h3>
</a>
<form id="pollForm" class="form-awesome round-10 form-awesome-wider nav-toggle-content <?php echo ($has_poll ? ' active' : ''); ?>">
    <ul>
        <li>
            <span class="right">Short and descriptive</span>
            <label for="postTitle">Question:</label>
            <?php $title = array('name' => 'pollQuestion', 'value'=>set_value('pollQuestion', $pollQuestion), 'id'=>'pollQuestion', 'maxlength'=>'40');
            echo form_input($title); ?>
        </li>
        <li>
            <span class="right">Comma-separated list</span>
            <label for="pollAnswers">Answers:</label>
            <input type="text" id="pollAnswersB" name="pollAnswersB" value="<?php echo $pollAnswers; ?>" />
        </li>
    </ul>
</form>
</span>

<?php } ?>

<div id="postPreviewContainer">
    <h3 class="suprahead round-10"><small class="right">Does your formatting work? </small>Preview</h3>
    <div class="thread-posts hfeed">
        <?php
            $userName  = base64_decode($this->session->userdata('userName'));
            $userEmail = $this->session->userdata('userEmail');
        ?>
        <div class="hentry first-entry entry">
            <address class="entry-author author vcard">
                <img alt="<?php echo $userName; ?>" src="http://www.gravatar.com/avatar/<?php echo md5(strtolower($userEmail)); ?>?s=64&amp;d=http%3A%2F%2Fthecolorless.net%2Fimages%2Favatars%2Fblack.png" width="64" height="64" class="entry-author-avatar" />
                <?php echo anchor('user/'.$userName, $userName, array('class'=>'entry-author-username fn nickname url')); ?>
            </address>
            <div class="entry-content" id="postPreview">
                <p>No content yet</p>
            </div>
            <div class="entry-meta">
                <ul>
                    <li><abbr class="published tooltipped-b" title="Not yet posted">In the future</abbr></li>
                    <li><?php echo anchor('#', '#NO-ID', array('rel'=>'bookmark')); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div><!--#postPreviewContainer-->
