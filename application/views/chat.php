<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php if($channel_req) { echo $channel_req . " - ";} ?>The Colorless: Chat (Dollars/Durarara!!) ~ Winter Is Coming</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="/css/chat.css" type="text/css" />
	<link rel="stylesheet" href="/css/reveal.css" type="text/css" />
	<link rel="stylesheet" href="/css/radio.css" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
	<script type="text/javascript" src="/js/dragdealer.js"></script>
	<script type="text/javascript" src="/js/radio.js"></script>
    <script type="text/javascript" src="/js/snow.js"></script>
	<script type="text/javascript" src="/min/b=js&amp;f=plugins/json2.js,plugins/jquery.tmpl.min.js,jquery.comet.alt.js,jquery.reveal.js,aspects/chat.js?v=123"></script>
	<script type="text/javascript">
		$(function() {
			Chat.channel = "<?php echo !empty($channel_req) ? $channel_req : 0; ?>";
			Chat.init();
		})
	</script>
	<meta name="keywords" content="anime, manga, japan, durarara, dollars" />
</head>
<body>
	<script id="template-message" type="text/x-jquery-tmpl">
		<div class="message ${color}{{if r}} is_reply{{/if}}{{if color == "gravatar"}} color${colorcode}{{/if}}">
			<div class="user">
				<img src="{{if color == "gravatar"}}http://gravatar.com/avatar/${avatar}?s=64{{else}}/images/avatars/${color}.png{{/if}}" width="64" height="64" class="avatar" alt="${timestamp}" />
				<a class="username" target="_blank" href="/user/${nickname}">${nickname}</a>
			</div>
			<div class="blurb">
				<p>{{html text}}</p>
			</div>
		</div>
	</script>
	<style id="colors" type="text/css">

	</style>
	<div id="wrapper">
		<div id="header">
			<form action="/chat/publish" method="post" id="send-message-area">
				<div id="name-area">
<span style="float:left;margin-right:5px;" id="you-are">You are: <span><?php echo ($this->user->isLoggedIn() ? base64_decode($this->session->userdata('userName')) : false); ?></span> &rarr;</span>
					<ul id="color-select">
						<li id="active-color">Color: <span>Black</span></li>
						<li data-value="black">Black</li>
						<li data-value="purple">Purple</li>
						<li data-value="lime_green">Lime green</li>
						<li data-value="darkblue">Dark Blue</li>
						<li data-value="orange">Orange</li>
						<li data-value="blue">Blue</li>
						<li data-value="red">Red</li>
						<li data-value="magenta">Magenta</li>
						<li data-value="green">Green</li>
						<li data-value="grey">Grey</li>
						<li data-value="mud_green">Mud Green</li>
						<li data-value="gravatar">Gravatar</li>
					</ul>
					<a href="/" id="home-link"><strong>Home</strong></a>
					<a href="#" data-reveal-id="export-log" id="print-log">Export chatlog</a>
                    <a href="javascript:if(browserok){initsnow()}">Let It Snow!</a>
					<?php if($this->user->canBan()) { ?> <a href="javascript:kickUser(prompt('Whom?', ''));">Kick</a><?php } ?>
				</div>
				<label id="characters-left" for="sendie">160</label>
				<input type="text" id="sendie" name="message" maxlength="160" autocomplete="off" />
				<input type="hidden" name="function" value="send" />
				<input type="hidden" name="username" value="<?php echo base64_decode($this->session->userdata('userName')); ?>" />
				<input type="hidden" name="color" value="<?php echo (array_key_exists("color", $_COOKIE) ? $_COOKIE["color"] : false); ?>" />
				<button id="new-msg-post">Post!</button>
			</form>
		</div><!--header-->
		<div id="content">
			<div id="chat-wrap">
				<div id="chat-area">
				    <div class="status">Rules: no racism/nationalism; no sharing of illegal material; no spamming. English only on /main, but check our other channels!</div>
				    <div class="status">Welcome to the Colorless chatrooom! This is a real-time webchat for anime and manga fans and stuff.</div>
				</div><!--chat-area-->
			</div><!--chat-wrap-->
		</div>
		<div id="footer">
		<p>Updates: <span id="status-update">-</span>; Online now: <strong id="online-now">0</strong> <a href="#" id="view-whois-online" data-reveal-id="online-list">See who is online</a></p>
		</div><!--#footer-->
		<div id="online-list" class="reveal-modal">
			[Not loaded yet]
			<a class="close-reveal-modal">&#215;</a>
		</div>
		<div id="export-log" class="reveal-modal">
			<textarea readonly style="width:100%;height:400px"></textarea>
			<a class="close-reveal-modal">&#215;</a>
		</div>
	</div><!--#wrapper-->
<div id="player">
	<audio id="radio" src="http://radio.thecolorless.net:8000/stream.ogg">
		<!-- BEGINS: AUTO-GENERATED FFMP3 CODE -->
		<object width="329" height="21" type="application/x-shockwave-flash">
			<param name="movie" value="ffmp3.swf" />
			<param name="flashvars" value="url=http://radio.thecolorless.net:8000/stream.ogg&lang=en&codec=ogg&volume=100&autoplay=false&traking=true&jsevents=true&title=Colorless%20Radio&welcome=Yes%20hello, this is.." />
			<param name="wmode" value="transparent" />
			<param name="allowscriptaccess" value="always" />
			<param name="scale" value="noscale" />
			<embed src="ffmp3.swf" flashvars="url=http://radio.thecolorless.net:8000/stream.ogg&lang=en&codec=ogg&volume=100&autoplay=false&traking=true&jsevents=true&title=Colorless%20Radio&welcome=Yes%20hello, this is.." width="320" scale="noscale" height="21" wmode="transparent" allowscriptaccess="always" type="application/x-shockwave-flash" />
		</object>
		<!-- ENDS: AUTO-GENERATED FFMP3 CODE -->
	</audio>
	<div class="controls">
		<div class="radiometa">
			<span id="radioListeners"></span>
			<span id="radioTrack"></span>
		</div>
		<button id="play">&#9658;</button>
		<div id="volume">
			<div class="handle">Drag me~</div>
		</div>
	</div>
	<script>
	$(function() {
		Radio.init();
	});
	</script>
</div><!--#player-->
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-7104252-6']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</body>
</html>
