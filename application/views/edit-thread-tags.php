<?php $this->load->view('common/header'); ?>
<form action="/post/tags/<?php echo $post->postID; ?>" method="post" class="form-awesome form-awesome-wider round-10">
  <p><strong>Please note:</strong> The tags must be comma-separated, but have <strong>no spaces</strong>. That means that all spaces that are part of the tag <strong>must be replaced with underscores</strong>. Thank you.
  <ul>
    <li>
        <label for="postTags">Tags:</label>
        <textarea name="postTags" id="postTags"><?php echo $postTags; ?></textarea>
    </li>
    <li>
        <button type="submit"><span>Save tags</span></button>
    </li>
  </ul>
</form>
<?php $this->load->view('common/footer'); ?>