<?php $this->load->view('common/header'); ?>
<h2>Tags</h2>
<?php foreach($tags as $t): ?>
    <li><?php echo anchor('tags/'.$t->tagSlug, '<span>'.$t->tagSlug.'</span> <em>'.$t->tagCount.'</em>'); ?></li>
<?php endforeach; ?>
<?php $this->load->view('common/footer');