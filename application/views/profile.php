<?php $this->load->view('common/header'); ?>

    <div id="user-vcard" class="round-10 clearfix">
        <hgroup>
            <h1>
                <img src="http://www.gravatar.com/avatar/<?php echo md5(strtolower($user->userEmail)); ?>?s=128&amp;d=http%3A%2F%2Fthecolorless.net%2Fimages%2Favatars%2Fblack.png" width="128" height="128" class="avatar" />
                <?php echo $user->userName; ?>
            </h1>
            <h2><?php echo (!empty($user->userBlob->userAlias) ? substr($user->userBlob->userAlias, 0, 25) : '{anonymous}'); ?></h2>
            <h3><abbr class="Well, not really. Who cares?"><?php echo '#'.rand(1, 999999); ?></abbr> <?php echo (!empty($user->userBlob->userCountry) ? $user->userBlob->userCountry : NULL); ?></h3>
        </hgroup>
        <ul id="user-vcard-links">
        <?php if(!empty($social) || !empty($user->userBlob->userHP)) {
                if(!empty($user->userBlob->userHP)) { $in = true; ?>
                    <li class="link-rss"><?php echo anchor($user->userBlob->userHP, 'Homepage', array('rel'=>'nofollow', 'target'=>'_blank', 'title'=>$user->userBlob->userHP, 'class'=>'tooltipped')); ?></li>
        <?php   }
                $social_a = array_slice($social, 0, ($in ? 3 : 4));
                $social_b = array_slice($social, ($in ? 3 : 4));
                foreach($social_a as $s): ?>
            <li class="link-<?php echo $s->slug; ?>"><?php echo anchor($s->address, $s->name, array('rel'=>'nofollow', 'target'=>'_blank', 'title'=>$s->title, 'class'=>'tooltipped')); ?></li>
        <?php   endforeach;
                if(!empty($social_b)): ?>
            <li id="user-vcard-more">
                <a href="javascript:;" id="user-vcard-more-link">More &rarr;</a>
                <ul id="user-vcard-links-more">
                <?php foreach($social_b as $s): ?>
                    <li class="link-<?php echo $s->slug; ?>"><?php echo anchor($s->address, $s->name, array('rel'=>'nofollow', 'target'=>'_blank', 'title'=>$s->title, 'class'=>'tooltipped')); ?></li>
                <?php endforeach; ?>
                </ul>
            </li>
        <?php   endif;
              } ?>
        </ul>
    </div>
<?php
    $userPosts = $this->chan->numUserPosts($user->userID);
    $allPosts = $this->cache->model('chan', 'numPosts', array(), 3600);
    $percent = round(($userPosts / $allPosts) * 100, 2);
?>
    <div class="form-tabbed clearfix" id="user-feat">
        <ul class="form-tab-navigation">
            <li>
                <a href="javascript:;" class="form-tab-navigation-link active">About</a>
            </li>
            <li>
                <a href="javascript:;" class="form-tab-navigation-link">Activity</a>
            </li>
            <?php if($this->user->canBan($user->userRole)): ?>
            <li>
                <a href="javascript:;" class="form-tab-navigation-link">Backstage</a>
            </li>
            <?php endif; ?>
        </ul>
        <div class="form-tab active">
            <table>
                <tr>
                    <td>Age:</td>
                    <!--<?php echo $user->userBlob->userYear ?>-->
                    <td><?php echo ($user->userBlob->userYear != '' ? floor((time() - strtotime($user->userBlob->userYear))/(3600*24*365)) : 'Unknown'); ?></td>
                </tr>
                <tr>
                    <td>Gender:</td>
                    <td><?php echo (!empty($user->userBlob->userGender) && $user->userBlob->userGender !== "unknown" ? ($user->userBlob->userGender == "male" ? "Male" : "Female") : 'Unknown'); ?></td>
                </tr>
                <tr>
                    <td>About me:</td>
                    <td style="max-width: 300px; word-wrap: break-word;"><?php echo strip_tags($user->userBlob->userAbout); ?></td>
                </tr>
            </table>
        </div>
        <div class="form-tab">
            <table>
                <tr>
                    <td>Posts:</td>
                    <td><?php echo $userPosts.' ('.$percent.'%)'; ?></td>
                </tr>
                <tr>
                    <td>Hearted:</td>
                    <td><?php echo $this->chan->getUserBookmarks($user->userID); ?></td>
                </tr>
                <tr>
                    <td>Friends:</td>
                    <td><?php echo $friends_count; ?></td>
                </tr>
                <tr>
                    <td>Last login:</td>
                    <td><?php echo $user->userUpdatedAt; ?></td>
                </tr>
            </table>
        </div>
        <?php if($this->user->canBan($user->userRole)): ?>
        <div class="form-tab">
            <table>
                <tr>
                    <td>Registered:</td>
                    <td><?php echo $user->userCreatedAt; ?></td>
                </tr>
                <tr>
                    <td>ID</td>
                    <td>#<?php echo $user->userID; ?></td>
                </tr>
                <tr>
                    <td>First IP:</td>
                    <td><?php echo (!empty($user->userIP) ? $user->userIP : "Unknown"); ?></td>
                </tr>
                <tr>
                    <td>Last IP:</td>
                    <td><?php echo (!empty($user->userLastIP) ? $user->userLastIP : "Unknown"); ?></td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td><?php echo $user->userStatus == "questionable" ? "Half-blocked" : $user->userStatus == "inactive" ? "Banned" : "Active"; ?></td>
                </tr>
                <tr>
                    <td>Role:</td>
                    <td><?php echo $user->userRole == 3 ? "Administrator" : $user->userStatus == 2 ? "Moderator" : $user->userStatus == 1 ? "Submoderator" : "Member"; ?></td>
                </tr>
                <tr>
                    <td>Same IP:</td>
                    <td>
                        <ul>
                            <?php
                            $res = $this->user->getClones($user->userLastIP);
                            foreach($res as $clone) {
                                echo '<li>'.anchor('user/'.$clone->userName, $clone->userName).'</li>';
                            }
                            ?>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php if (!empty($badges2)) { ?>
    <ul id="top-nav" style="float: right; margin: 6px; list-style: none;">
        <li class="nav-dropdown-container">
			<a href="javascript:;" id="nav-posts-link" class="nav-dropdown-link">Give...</a>
			<ul class="nav-dropdown" id="nav-posts-dropdown">
			<?php foreach($badges2 as $badge) { ?>
                <li><?php echo anchor('/profile/givebadge/'.$user->userID.'/'.$badge->badgeID, $badge->badgeTitle); ?></li>
            <?php } ?>
			</ul>
		</li>
	</ul>
    <?php } ?>

    <?php if (!empty($badges) || !empty($badges2)) { ?>
    <div class="form-awesome form-awesome-wider round-10">
        <ul class="badges"><?php foreach($badges as $badge) { ?>
            <li><img src="<?php echo $badge->badgeImageURL; ?>"  title="<?php echo $badge->badgeContent; ?>" class="tooltipped" /></li>
        <?php } ?></ul>
    </div>
    <?php } ?>
<?php if(!empty($user_threads)): ?>
<h3 class="dash-head">
    <span class="right"><?php echo anchor('posts/'.$user->userName, 'More'); ?></span>
    Their threads
</h3>
<?php $this->load->view('common/threads-loop', array('posts'=>$user_threads)); ?>
<?php endif; ?>
<?php if($this->user->isLoggedIn()): ?>
<div id="new-pm" class="form-awesome form-awesome-wider reveal-modal">
    <?php echo form_open('direct/compose/'.$user->userID, '', array("prevMessage" => 0)); ?>
        <ul>
        <li><label for="pmSubject">Subject:</label>
            <?php echo form_input('pmSubject'); ?>
        </li>
        <li><label for="pmContent">Message:</label>
            <?php echo form_textarea('pmContent'); ?>
        </li>
        <li>
            <button type="submit"><span>Send off</span></button>
        </li>
        </ul>
    <?php echo form_close(); ?>
    <a class="close-reveal-modal">&#215;</a>
</div>
<?php if($this->user->canBan($user->userRole)): ?>
<div id="ban-et" class="form-awesome form-awesome-wider reveal-modal">
<?php if(!$this->user->isBanned($user->userID)): ?>
    <?php echo form_open('profile/ban/'.$user->userID); ?>
        <ul>
            <li>Are you sure you want to ban <?php echo $user->userName; ?>?</li>
            <li><button type="submit"><span>Sure, send'em to hell</span></button></li>
        </ul>
    <?php echo form_close(); ?>
<?php else: ?>
    <?php echo form_open('profile/unban/'.$user->userID); ?>
            <ul>
                <li>You wanna forgive? That's cool, bro.</li>
                <li><button type="submit"><span>Yup, forgive them</span></button></li>
            </ul>
    <?php echo form_close(); ?>
<?php endif; ?>
    <a class="close-reveal-modal">&#215;</a>
</div>
<?php endif; ?>
<?php endif; ?>
<?php $this->load->view('common/footer'); ?>