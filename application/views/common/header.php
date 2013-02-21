<?php if($this->user->isBanned()) $this->load->view('banned');

$loggedin = $this->user->isLoggedIn();

$typpe = "general";
	if($loggedin) {
		if($this->uri->segment(1) == "thread") {
			$typpe = "thread";
		} elseif($this->uri->segment(1) == 'user') {
			$typpe = "user";
		}
	}

$cur_con = $this->router->class;;
$cur_met = $this->router->method;

if(!isset($unread)) { $unread = $this->privatum->numUnreadPM($this->user->id); } ?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo (!empty($page_title) ? $page_title . ' - ' : ''); echo (!empty($page_number) ? 'Page ' . $page_number . ' - ' : ''); ?>The Colorless ~ Winter Is Coming</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" href="/min/b=css&amp;f=reset.css,typography.css,style.css,tipTip.css,jquery.tagsinput.css,jquery.autocomplete.css,reveal.css" />
	<meta name="keywords" content="colorless,dollars,durarara,anime,manga,japan,otaku,cosplay" />
	<meta name="google-site-verification" content="UR0fIbiNgcAeq-65d0CQC01THgXWOUKzrXhXIbQ2iYk" />
	<meta name="alexaVerifyID" content="A8YQKgpOq2oRET3lYU58Z4n3HJQ" />
	<link type="text/plain" rel="author" href="http://thecolorless.net/humans.txt" />
	<meta property="og:site_name" content="The Colorless"/>
	<meta property="fb:page_id" content="103554563030409" />
<?php if($this->uri->total_segments() == 0) { ?>
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="<?php echo site_url(); ?>"/>
	<link rel="canonical" href="<?php echo site_url(); ?>" />
<?php } elseif($this->uri->segment(1) == "thread" && !$this->uri->segment(4)) { ?>
	<link rel="canonical" href="<?php echo site_url(); ?>thread/<?php echo $this->uri->segment(2); ?>" />
	<meta property="og:title" content="<?php echo htmlentities(strip_tags($thread->postTitle)); ?>"/>
	<meta property="og:type" content="article"/>
	<meta property="og:url" content="<?php echo site_url(); ?>thread/<?php echo $this->uri->segment(2); ?>"/>
<?php } elseif($this->uri->segment(1) == "thread" && $this->uri->segment(4)) { ?>
	<link rel="canonical" href="<?php echo site_url(); ?>thread/<?php echo $this->uri->segment(2); ?>/offset/<?php echo $this->uri->segment(4); ?>" />
	<meta property="og:title" content="<?php echo htmlentities(strip_tags($thread->postTitle)); ?>"/>
	<meta property="og:type" content="article"/>
	<meta property="og:url" content="<?php echo site_url(); ?>thread/<?php echo $this->uri->segment(2); ?>"/>
<?php } elseif($this->uri->segment(1) == "user" || ($this->uri->segment(1) == "profile" && $this->uri->segment(2) == "id")) { ?>
	<link rel="canonical" href="<?php echo site_url(); ?>user/<?php echo $user->userName; ?>" />
<?php } ?>
<?php if($this->uri->segment(1) == "thread") { ?>
	<link rel="alternate" type="application/rss+xml" href="<?php site_url('feed/thread/'.$threadID); ?>" />
<?php } ?>
<?php if($this->uri->segment(1) == "board") { ?>
	<link rel="alternate" type="application/rss+xml" href="<?php site_url('feed/board/'.$this->uri->segment(2)); ?>" />
<?php } ?>
<?php if($this->uri->segment(1) == "user") { ?>
	<link rel="alternate" type="application/rss+xml" href="<?php site_url('feed/user/'.$this->uri->segment(2)); ?>" />
<?php } ?>
</head>
<body>
	<!-- BuySellAds.com Ad Code -->
	<script type="text/javascript">
	(function(){
	  var bsa = document.createElement('script');
		 bsa.type = 'text/javascript';
		 bsa.async = true;
		 bsa.src = '//s3.buysellads.com/ac/bsa.js';
	  (document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(bsa);
	})();
	</script>
	<!-- END BuySellAds.com Ad Code -->
	<div id="loader" class="round-10" style="display:none">Loading</div>
	
	<div id="wrapper">
		<div id="header">
			<div id="members-counter" class="right">
				<?php if($loggedin) { ?>You are <?php echo anchor('user/'.$this->user->name, $this->user->name, array('class'=>'tooltipped-b', 'title'=>'Click here to view your public profile')); ?>, one of our <?php } ?><span><?php echo anchor('members/recent', User::numActiveUsers()); ?></span> members
			</div><!--#members-counter.right-->
			<ul id="top-nav" class="left">
				<li id="frontpage-link"><?php echo anchor('', 'Front page'); ?></li>
<?php if($loggedin && $this->user->doesWant($this->user->id, 'settingShowSpam')) { echo '<li>'. anchor('/board/spam', 'Spam') . '</li>'; } ?>

				<li class="nav-dropdown-container">
					<a href="javascript:;" id="nav-posts-link" class="nav-dropdown-link">Boards</a>
					<ul class="nav-dropdown" id="nav-posts-dropdown">
						<?php if($loggedin): ?>
						<li><?php echo anchor('post/create', 'Create thread'); ?></li>
						<?php endif; ?>
						<?php
							$boards = $this->chan->get_boards();
							foreach($boards as $key => $b):
								echo '<li'.($key == 0 && $loggedin ? ' class="nav-sep-above"' : NULL).'>'.anchor('board/'.strtolower(str_replace(" ", "_", $b)), $b).'</li>';
							endforeach;
						?>
					</ul>
				</li>
				<?php if($loggedin) { ?>
				<li class="nav-dropdown-container<?php echo ($unread > 0 ? ' has-unread' : NULL); ?>">
					<a href="javascript:;" id="nav-mystuff-link" class="nav-dropdown-link">My Stuff<?php echo ($unread > 0 ? ' <span class="totally-unread round-10">'.$unread.'</span>' : NULL); ?></a>
					<ul class="nav-dropdown" id="nav-me-dropdown">
						<li><?php echo anchor('posts/own', 'Your threads'); ?></li>
						<li><?php echo anchor('posts/bookmarks', 'Bookmarks'); ?></li>
						<li class="nav-sep-above<?php echo ($unread > 0 ? ' is-unread' : NULL); ?>"><?php echo anchor('direct/inbox', 'Inbox'.($unread > 0 ? ' <span class="totally-unread round-10">'.$unread.'</span>' : false)); ?></li>
						<li><?php echo anchor('direct/sent', 'Outbox'); ?></li>
						<li><?php echo anchor('direct/compose', 'Compose'); ?></li>
						<li class="nav-sep-above"><?php echo anchor('dashboard/friends', 'Friends'); ?></li>
						<li><?php echo anchor('dashboard', 'Dashboard'); ?></li>
						<li><?php echo anchor('dashboard/mentions', 'Mentions'); ?></li>
						<!--<li><?php echo anchor('profile/invite', 'Invitations'); ?></li>-->
						<li class="nav-sep-above"><?php echo anchor('settings', 'Settings'); ?></li>
						<li><?php echo anchor('login/logout', 'Logout'); ?></li>
					</ul>
				</li>
				<li id="chat-link"><?php echo anchor('chat', 'Chat'); ?></li>
				<?php } else { ?>
				<li id="chat-link"><a href="http://thecolorless.net/chat">Login</a></li>
				<li><a href="http://thecolorless.net/sessions/new">Login</a></li>
				<li><a href="http://thecolorless.net/users/new">Join</a></li>
				<?php } ?>
				<!--<li><?php echo anchor('about', 'About us'); ?></li>-->
				<li id="blog-link"></li>
				<li class="nav-dropdown-container">
					<a href="javascript:;" id="nav-pages-link" class="nav-dropdown-link">About</a>
					<ul class="nav-dropdown" id="nav-pages-dropdown">
					<li><?php echo anchor('about/rules', 'Rules'); ?></li>
						<li><?php echo anchor('about', 'About us'); ?></li>
						<li><?php echo anchor('about/advertising', 'Advertising'); ?></li>
						<li><?php echo anchor('about/faq', 'FAQ'); ?></li>
						<li><?php echo anchor('http://twitter.com/TheColorless', 'Our Twitter', array('rel'=>'nofollow')); ?></li>
					</ul>
				</li>
			</ul><!--#top-nav.left-->
		</div><!--#header-->
		<div id="subhdr" class="clearfix">
			<div id="subhdr-nav-container" class="clearfix">
				<h2><?php echo (isset($page_title) ? $page_title : "Frontpage") . (!empty($page_number) ? ' - ' . 'Page ' . $page_number : ''); ?></h2>
				<ul id="subhdr-subnav" class="clearfix<?php if($cur_con == "thread"): ?> bread<?php endif; ?>">
					<?php if($cur_con == "home"): ?>
				<?php
					foreach($boards as $key => $b):
						/*if($key > 13) {
							echo '<li class="two-rows">'.anchor('#', 'More &rarr;', array('onclick'=>'$(\'#nav-posts-link\').click()')).'</li>';
							break;
						}*/
						echo '<li class="two-rows">'.anchor('board/'.strtolower(str_replace(" ", "_", $b)), $b, array('class'=>'tooltipped board-link')).'</li>';
					endforeach;
				?>
					<?php elseif($cur_con == "admin"): ?>
				<li><?php echo anchor('admin', 'Control Panel'); ?></li>
				<li><?php echo anchor('admin/statistics', 'Statistics'); ?></li>
				<li><?php echo anchor('admin/flags', 'Flags'); ?></li>
				<li><?php echo anchor('admin/actions', 'Action Watcher'); ?></li>
					<?php elseif($cur_con == "direct"): ?>
				<li><?php echo anchor('direct/compose', 'New'); ?></li>
				<li><?php echo anchor('direct/inbox', 'Inbox'.($unread > 0 ? ' ('.$unread.')' : false)); ?></li>
				<li><?php echo anchor('direct/sent', 'Sent'); ?></li>
					<?php elseif($cur_met == "settings" || $cur_met == "invite"): ?>
				<li><?php echo anchor('settings', 'Settings'); ?></li>
				<li><?php echo anchor('profile/invite', 'Invitations'); ?></li>
				<li><?php echo anchor('profile/my', 'View public profile'); ?></li>
					<?php elseif($cur_con == "profile" || $cur_con == "members"): ?>
					<?php if(isset($user) && $loggedin): ?>
						<?php if($this->user->id == $user->userID): ?>
				<li>This is your profile!</li>
				<li><?php echo anchor('settings', 'Edit'); ?></li>
						<?php else: ?>
				<li><?php echo anchor('direct/compose/'.$user->userID, 'Send a PM', array('data-reveal-id'=>'new-pm')); ?></li>
						<?php endif; ?>
					<?php if($this->user->canBan($user->userRole) && !$this->user->isBanned($user->userID)) { echo '<li>'.anchor('profile/ban/'.$user->userID, 'Ban', array('data-reveal-id'=>'ban-et')).'</li>'; }
    elseif($this->user->canBan($user->userRole) && $this->user->isBanned($user->userID)) { echo '<li>'.anchor('profile/unban/'.$user->userID, 'Unban', array('data-reveal-id'=>'ban-et')).'</li>'; } ?>
						<?php if($this->session->userdata("userID") !== $user->userID): ?>
					<?php $relation = $this->user->isFriend($this->user->id, $user->userID); ?>
				<li><a href="javascript:;" data-uid="<?php echo $user->userID; ?>" class="<?php echo ($relation < 3 ? 'add-friend-link' : 'remove-friend-link'); ?>">
						<?php
						switch($relation) {
							case 0:
								echo "Send friend request";
								break;
							case 1:
								echo "Request sent";
								break;
							case 2:
								echo "Accept friend request";
								break;
							case 3:
								echo "Unfriend";
								break;
						}
						?>
					</a></li>
					<?php endif; ?>
					<?php endif; ?>
					<?php elseif($cur_con == "dashboard"): ?>
    			<li><?php echo anchor('dashboard', 'My Dashboard'); ?></li>
  				<li><?php echo anchor('dashboard/mentions', 'Mentions'); ?></li>
  				<li><?php echo anchor('dashboard/friends', 'My friends'); ?></li>
				<li><?php echo anchor('dashboard/pending', 'Sent requests'); ?></li>
				<li><?php echo anchor('dashboard/requests', 'Incoming requests'); ?></li>
					<?php elseif($cur_con == "thread"): ?>
				<li class="first"><?php echo anchor('board/'.strtolower(str_replace(" ", "_", $this->chan->slugBoard($thread->postBoard))), ucwords($this->chan->slugBoard($thread->postBoard))); ?></li>
				<li class="current"><?php echo anchor('thread/'.$threadID, 'Thread #'.$threadID); ?></li>
				<li class="last"><a href="#"><?php echo "Page " . (!empty($page_number) ? $page_number : '1'); ?></a></li>
					<?php elseif($cur_con == "login" || $cur_con == "about"): ?>
				<li><?php echo anchor('about/rules', 'Rules'); ?></li>
				<li><?php echo anchor('about', 'About us'); ?></li>
				<li><?php echo anchor('about/images', 'Images rules/guide'); ?>
				<li><?php echo anchor('about/advertising', 'Advertising'); ?></li>
				<li><?php echo anchor('about/faq', 'FAQ'); ?></li>
					<?php elseif($cur_con == "post"): ?>
					<?php if($loggedin): ?><li><?php echo anchor('posts/own', 'Your threads'); ?></li><?php endif; ?>
				<li><?php echo anchor('about/rules', 'Rules'); ?></li>
				<li><?php echo anchor('post/create', 'New thread'); ?></li>
				<?php endif; ?>
				</ul><!--#subhdr-subnav-->
				<?php if($cur_con == "thread"): ?>
				<ul id="subhdr-subcrumb" class="clearfix">
					<?php if($loggedin): ?>
					<li id="thread-likes-item">
						<?php
						$follows = $this->chan->isFollowing($this->user->id, $threadID);
						$likes = '<span class="icon heart_12"></span><span class="thread-likes" id="thread-likes">' . $this->chan->getThreadBookmarks($threadID) . '</span> &bull; ';
						echo $this->session->userdata('userID') == $thread->postCreatorID ? ($likes.'Hearts') : (!$follows ? anchor('#', $likes.'<span id="thread-likes-text">Heart it</span>', array('data-threadid'=>$threadID, 'class'=>'tooltipped-b', 'title'=>'Click to appreciate this thread')) : anchor('#', $likes.'<span id="thread-likes-text">Unheart it</span>', array('data-threadid'=>$threadID)));
						?>
					</li>
					<?php else: ?>
					<li>
						<?php
						echo '<span class="icon heart_12"></span><span>' . $this->chan->getThreadBookmarks($threadID) . '</span> &bull; Hearts';
						?>
					</li>
					<?php endif; ?>
					<li id="thread-views-item">
						<?php
						echo '<span class="icon book_12"></span><span>' . $this->chan->getThreadViews($threadID) . '</span> &bull; Readers';
						?>
					</li>
					<li><a href="http://twitter.com/home?status=<?php echo urlencode($thread->postTitle . " " . site_url('thread/'.$threadID) . " via @TheColorless"); ?>" rel="nofollow"><span class="icon comment_12"></span>Tweet it!</a></li>
					<?php if($this->user->isLoggedIn()): ?>
						<li id="thread-flag-item">
							<?php if ($this->chan->isFlagged($threadID)) {
								if($this->user->role >= 2) { ?>
								<a href="/thread/unflag/<?php echo $threadID; ?>"><span class="icon x_12"></span><span id="thread-flag-text">Unflag</span></a>
							<?php } } else { ?>
								<a href="javascript:;" title="Flag this thread as innapropriate" class="tooltipped" data-reveal-id="flag-et-<?php echo $threadID; ?>"><span class="icon x_12"></span><span id="thread-flag-text">Flag</span></a>
							<?php } ?>
						</li>
					<?php endif; ?>
				</ul>
				<?php endif; ?>
			</div><!--#subhdr-nav-container-->
			<div id="subhdr-logo">
				<h1><a href="/" rel="home"><strong>The</strong><em>Color<span>less</span></em></a></h1>
			</div><!--#subhdr-logo-->
		</div><!--#subhdr-->
		<div id="container">
			<div id="content">
