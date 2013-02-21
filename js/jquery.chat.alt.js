var chat_title = document.title;
var intID;
var last_message = last_message || null;

$(document).ready(
	function()
	{
		channel = channel;
		sdfsdf = sdfsdf;
		jQuery.comet.connect('/chat/comet_pull?channel='+channel);
		jQuery.comet.bind(update);
		joinChat();
		$("#chat-wrap").css("height", $(window).height() - 230);
		$(window).resize(function() {
			$("#chat-wrap").css("height", $(window).height() - 230);
		});
		$("#sendie").keydown(function( event )
		{
			var key = event.which;
			if (key >= 33) {
				var maxLength = $(this).attr("maxlength");
				var length = this.value.length;
				if (length >= maxLength) {
					event.preventDefault();
				}
			}
		});
		$('#sendie').bind('textchange', function (event, previousText) {
                    $('#characters-left').html( 160 - parseInt($(this).val().length) );
                });
		$("#send-message-area").submit(function(e) {
			e.preventDefault();
			var msgcontent = $("#sendie").val();
			msgcontent     = $.trim(msgcontent);
			var maxlen     = $("#sendie").attr("maxlength");
			var length     = msgcontent.length;
			if (length <= maxlen + 1 && length !== 0 && length > 1 && msgcontent !== "" && msgcontent != null) {
				postMessage(msgcontent);
				$("#sendie").val("");
			} else {
				$("#sendie").val(msgcontent.substring(0, maxlen));
			}
		});
		$(".avatar").live('click', function() {
			var u_n = $(this).siblings(".username").text();
			var o_v = $("#sendie").val();
			$("#sendie").val(o_v + "@"+u_n+" ").focus();
		});
		$("#color-select").css("left", $("#you-are").width() + 5);
		$("#color-select li[id!=active-color]").click(function() {
			var color = $(this).attr("data-value");
			createCookie("color", color, 365);
			$("#active-color span").text($(this).text());
			$("#home-link").css("margin-left", $("#color-select").width() +5);
		});
		$("#print-log").click(print_log);
		$("#view-whois-online").toggle(function(e) {
			e.preventDefault();
			$("#whois-online").fadeIn();
		}, function(e) {
			e.preventDefault();
			$("#whois-online").fadeOut();
		});
		$("#close-whois-online").click(function(e) {
			e.preventDefault();
			$("#view-whois-online").click();
		});
		window.onbeforeunload = leaveChat;

		var title = $('title'),
		    originalTitleText = title.html(),
		    messageCountFrom = false;

		//Guess right now, simplest way
		//If window in focus,
		var getMessageCount = function() {
		    if(false === messageCountFrom) {
			return '*';
		    }
		    //Tried caching $(topic-messsages) but seems i need to call it live eachtime
		    var count = $('#chat-area').children().length - 1;
		    return count - messageCountFrom;
		};

		var updateTitle = function(){
		    title.html('('+getMessageCount()+') '+ originalTitleText);
		};

		//No good dom/id hooks right now, but you get the idea...
		$(window).blur(function() {
		    messageCountFrom = $('#chat-area').children().length - 1;
		});

		$(window).focus(function() {
		    messageCountFrom = false;
		    updateTitle();
		});

		updateTitle();
		setInterval(updateTitle, 3000);
    }
);

function print_log() {
	logg = window.open("", "loggWindow", "width=440,height=500,scrollbars=no");
	var loggy = "";
	$(".message").each(function(i) {
		var taimu = new Date($(this).find(".avatar").attr("alt") * 1000);
		// hours part from the timestamp
		var hours = taimu.getHours();
		// minutes part from the timestamp
		var minutes = taimu.getMinutes();
		// seconds part from the timestamp
		var seconds = taimu.getSeconds();

		var formattedTime = hours + ':' + minutes + ':' + seconds;
		loggy = "["+formattedTime+"] "+$(this).find(".username").text() + ": " + $(this).find(".blurb").text() + "\n" + loggy;
	});
	var style = "@import url('reset.css'); ::-webkit-scrollbar{ width:10px; height:10px; border-left:0}::-webkit-scrollbar-button:start:decrement{ display:block; height:5px}::-webkit-scrollbar-button:end:increment{ display:block; height:5px}::-webkit-scrollbar-button:vertical:increment{ background-color:transparent}::-webkit-scrollbar-track:enabled{ background-color:rgba(0,0,0,0.1); -webkit-border-radius:5px}::-webkit-scrollbar-thumb:vertical{ height:50px; background-color:rgba(0,0,0,.2); -webkit-border-radius:5px}::-webkit-scrollbar-thumb:horizontal{ width:50px; background-color:rgba(0,0,0,.2); -webkit-border-radius:5px} body{ background:#111112; padding:10px}textarea{ width:380px; height:420px; padding:10px; border:1px solid #fff; -webkit-border-radius:10px; outline:0; -moz-border-radius:10px; border-radius:10px; background:#ededed; text-shadow:#fff 0px 1px 1px; font-family:Helvetica,Arial,sans-serif; line-height:18px; font-size:13px}";
	var doc1 = "<!DOCTYPE html><html><head><title>Chatlog</title><style type=\"text/css\">"+style+"</style></head><body><textarea readonly=\"readonly\" onclick=\"this.select()\" id=\"exported-textarea\">";
	var doc2 = "</textarea></body></html>";
	logg.document.open();
	logg.document.write(doc1+loggy+doc2);
	logg.document.close();
	return false;
}

function update( data, type )
{
	if(last_message === null || JSON.stringify(last_message) !== JSON.stringify(data)) {
		pressMessage(data);
		last_message = data;
	}
}

function stripslashes( str )
{
	return (str+'').replace(/\\(.?)/g, function (s, n1) {
		switch (n1) {
			case '\\':
				return '\\';
			case '0':
				return '\u0000';
			case '':
				return '';
			default:
				return n1;
		}
	});
}

function pressMessage( msg )
{
	var type      = msg.type;
	var nickname  = stripslashes(msg.nickname);
	var timestamp = msg.timestamp;
	var content   = stripslashes(msg.text);
	var color     = msg.color;
	var avatar    = msg.avatar;
	var colorcode = msg.colorcode;
	switch(type) {
		case "message":
			var reply = "";
			var o_u = readCookie("nickname").toLowerCase();
			var t_c = content.toLowerCase();
			if(t_c.search(o_u) !== -1) {
				reply = " is_reply";
			}
			if (msg.color == "gravatar") {
				var message = '<style type="text/css">.color'+colorcode+' .blurb:after { border-color: transparent #'+colorcode+' transparent transparent }</style><div class="message color'+colorcode+reply+'"><div class="user"><img src="http://www.gravatar.com/avatar/'+avatar+'?s=64&d=http%3A%2F%2Fthecolorless.net%2Fimages%2Favatars%2Fblack.png" width="64" height="64" class="avatar" alt="' + timestamp + '" /><a class="username" target="_blank" href="/user/' + nickname + '">'+nickname+'</a></div><div class="blurb" style="background-color: #'+colorcode+'; after { border-color: transparent #'+colorcode+' transparent transparent }"><p>'+content+'</p></div></div>';
			} else {
				var message = '<div class="message '+color+reply+'"><div class="user"><img src="/images/avatars/'+color+'.png" width="64" height="64" class="avatar" alt="' + timestamp + '" /><a class="username" target="_blank" href="/user/' + nickname + '">'+nickname+'</a></div><div class="blurb"><p>'+content+'</p></div></div>';
			}
			$("#chat-area").prepend(message);
			damnedEffect();
		break;
		case "join":
			var message = '&mdash; &mdash; <a class="inline-username" target="_blank" href="/user/' + nickname + '">' + nickname + '</a> is now online';
			$("#status-update").html(message);
			pingChat();
		break;
		case "leave":
			var message = '&mdash; &mdash; <a class="inline-username" target="_blank" href="/user/' + nickname + '">' + nickname + '</a> is now offline';
			$("#status-update").html(message);
			pingChat();
		break;
		case "kick":
			if(msg.target == readCookie("nickname") && !sdfsdf) {
				window.location.href = "/chat/kicked/"+nickname;
				kick = true;
			}
			if(/chuc[kl][<\. _]{0,}n[o0]rri[s5]/i.test(msg.target) && nickname !== "Gargron") {
				alert("OH SNAP! "+nickname+" attempted to kick Chuck Norris! I mean, CHUCK GODDAMN NORRIS!!1 Everyone knows it's impossible! So it failed! Failed hard and dirty!");
				return true;
			}
			var message = '&mdash; &mdash; <a class="inline-username" target="_blank" href="/user/' + nickname + '">' + nickname + '</a> kicked ' + msg.target;
			$("#status-update").html(message);
		break;
	}
}

function blinkWindow(new_title) {
	if(document.title == new_title) {
		document.title = chat_title;
	} else {
		document.title = new_title;
	}
}

function damnedEffect()
{
	var FirstP = $(".blurb:first");
	var FirstM = FirstP.parent();
	var oldW   = FirstP.width()+"px";
	var oldH   = FirstP.height()+"px";
	FirstM.css("background-image", "url()");
	FirstP.css({"width":"0px", "height":"0px","font-size":"0px","opacity":"0","border-width":"0px","text-indent":"-9999px"});
	FirstP.animate({"width":oldW,"height":oldH,"fontSize":"12px","opacity":1,"borderWidth":"3px","textIndent":0}, 200, function() {FirstM.css("background-image", "url(/images/tri-bg.png)");});
	var s_t = $("#chat-wrap").scrollTop();
	if(s_t !== 0) {
		$("#chat-wrap").scrollTop(s_t + FirstM.height() + 20);
	}
}

function pushMessage( message )
{
	$.ajax({
		type:   "POST",
		url:    "/process.php?channel="+channel,
		data: {
			"function": "send",
			"message": message
		},
		dataType: "json"
	});
}

function postMessage ( message ) {
	$.post("/chat/publish/"+channel, {message: message.replace(/\\/g, "\\\\")});
}

function pingChat()
{
	$.ajax({
		type:   "GET",
		url:    "/chat/hit/c/"+channel,
		success: function() {
			console.log("ping successful");
			$.ajax({
				type:   "GET",
				url:    "/chat/get_online",
				dataType: "json",
				success: function(data) {
					$("#online-now").text(data.count);
					var userstring = "";
					$.each(data.users, function(i) {
						var channelLink = '<a href="/chat/channel/'+this.chatChannel+'">/'+this.chatChannel+'/</a>';
						if(this.chatChannel == 0)
							channelLink = '<a href="/chat">/main/</a>';

						userstring = '<a href="/user/'+ this.chatUser + '" class="inline-username">' + this.chatUser + "</a> ("+channelLink+"), " + userstring;
					});
					$("#whois-online p").html(userstring);
				}
			});
		}
	});
}

window.setInterval( pingChat, 30000 );

function joinChat()
{
	$.ajax({
		type:   "GET",
		url:    "/chat/hit/online/"+channel
	});
}

function leaveChat()
{
	$.ajax({
		type:   "GET",
		url:    "/chat/hit/offline/"+channel
	});
}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}


function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}
