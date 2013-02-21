<?php $this->load->view('common/header'); ?>
<ul class="friends-list">
    <?php
    foreach($members as $auser) {
         $blob = json_decode($auser->userBlob);
         echo '<li class="round-10">'
         . '<ul class="friends-list-submenu">'.($this->session->userdata('userID') ? '<li>'.anchor('direct/compose/'.$auser->userID, 'Send PM').'</li>' : NULL).'<li>'.anchor('posts/'.$auser->userName, 'Their threads').'</li></ul>'
         . anchor('user/'.$auser->userName,
         '<img src="http://www.gravatar.com/avatar/'.md5(strtolower($auser->userEmail)).'?s=64&amp;d=http%3A%2F%2Fthecolorless.net%2Fimages%2Favatars%2Fblack.png" alt="'.$auser->userName.'" class="avatar left" width="48" height="48" /> <strong class="friends-list-name">'.$auser->userName.'</strong>',
           array('title'=>$blob->userAlias, 'class'=>'tooltipped')
         ).'<span class="friends-list-active">Last active '.$auser->userUpdatedAt.'</span>'
         .'</li>';
    } ?>
</ul>
<?php echo $this->pagination->create_links(); ?>
<?php $this->load->view('common/footer'); ?>