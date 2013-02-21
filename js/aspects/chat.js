var Cookie = {
    read: function(k) {
	var keq = k + "=";
	var ca = document.cookie.split(';');
	for(var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while(c.charAt(0)==' ') c = c.substring(1, c.length);
		if(c.indexOf(keq) == 0) return c.substring(keq.length, c.length);
	}
	return null;
    },
    write: function(k, v, t) {
        var e = t ? "; expires=" + new Date(new Date().getTime() + (t * 24 * 60 * 60 * 1000)).toGMTString() : "";
	document.cookie = k + "=" + v + e + "; path=/";
    }
};

var Chat = {
    channel: null,
    nick: Cookie.read("nickname").toLowerCase(),
    online: {
        names: [],
        count: 0,
        codes: []
        },
    last_message: null,
    init: function() {
        this.cosmitology();
        this.actions();
        $.comet.connect("/chat/comet_pull?channel=" + this.channel);
        $.comet.bind(this.receive);
        $("#send-message-area").bind("submit", this.prepare);
	$("#sendie").bind("keydown", this.limit);
	$("#print-log").click(this.print_log);
        $("#color-select li[id!=active-color]").bind("click", this.change_color);
        this.ping("join");
        this.get_online();
        window.onbeforeunload = function() { window.clearInterval(Chat.ping); Chat.ping_leave(); };
        window.setInterval(Chat.ping, 60000);
        return this;
    },
    receive: function(d, t) {
        if(JSON.stringify(Chat.last_message) == JSON.stringify(d)) {
            return false;
        } else {
            Chat.last_message = d;
            //Chat.add_to_log(d);
        }
        var mt = d.type;
        var mn = d.nickname + "";
        var md = d.timestamp;
        var mc = d.text + "";
        var mf = d.color;
        var ma = d.avatar;
        var mh = d.colorcode;
        var mz = d.target + "";

        var r = false, o = null, c = null;
            c = mc.toLowerCase();
            r = c.search(Chat.nick) != -1 ? true : false;
          d.r = r;

        switch(mt) {
            case "message":
                if(mf == "gravatar") {
                    var known_colors = Chat.online.codes,
                        is_known     = false;

                    for(var i = 0; i < known_colors.length; i++) {
                        if(known_colors[i] == mh) {
                            is_known = true;
                            break;
                        }
                    }
                    if(is_known) {

                    } else {
                        Chat.online.codes.push(mh);
                        $("#colors").append(".color" + mh + " .blurb { background-color: #" + mh + "} .color" + mh + " .blurb:after { border-color: transparent #" + mh + " transparent transparent }");
                    }
                }
                $("#template-message").tmpl(d).prependTo("#chat-area");
                Animation.pop();
            break;
            case "join":
                o = '&mdash; &mdash; <a class="inline-username" target="_blank" href="/user/' + mn + '">' + mn + '</a> is now online';
                $("#status-update").html(o);
            break;
            case "leave":
                o = '&mdash; &mdash; <a class="inline-username" target="_blank" href="/user/' + mn + '">' + mn + '</a> is now offline';
                $("#status-update").html(o);
            break;
            case "kick":
		u1 = mz.toLowerCase();
		u2 = Chat.nick.toLowerCase();
                if(u1 == u2) {
		    $("#sendie").val("").attr("disabled", "disabled");
		    $("#send-message-area").unbind("submit").bind("submit", function(e) { e.preventDefault(); return false; });
		    alert("Sorry, you've been kicked");
		}
                o = '&mdash; &mdash; <a class="inline-username" target="_blank" href="/user/' + mn + '">' + mn + '</a> kicked ' + mz;
                $("#status-update").html(o);
            break;
        }
        return this;
    },
    prepare: function(e) {
        e.preventDefault();
        var form  = $(this);
        var field = form.find("#sendie");

        var c = field.val();
            c = $.trim(c);

        var maxlen     = field.attr("maxlength");
        var length     = c.length;
        if (length <= maxlen + 1 && length !== 0 && length > 1 && c !== "" && c != null) {
                Chat.publish(c);
                field.val("");
        }
        return true;
    },
    publish: function(m) {
        $.post("/chat/publish/"+this.channel, {message: m.replace(/\\/g, "\\\\")});
        return this;
    },
    ping: function(t) {
        console.log("ping");
        $.ajax({
            type: "GET",
            async : (t == "leave" ? false : true),
	    url:  "/chat/hit/" + (t == "join" ? "online" : (t == "leave" ? "offline" : "c")) + "/" + Chat.channel,
            success: function() {
                if(t !== "join" && t !== "leave") {
                    Chat.get_online();
                }
            }
        });
    },
    ping_leave: function() {
        this.ping("leave");
    },
    get_online: function() {
        $.ajax({
            type: "GET",
            url: "/chat/get_online",
            dataType: "json",
            success: function(d) {
                Chat.online.count = d.count;
                Chat.online.names = d.users;
                $("#online-now").text(d.count);
                $("#online-list").html("");
                $.each(d.users, function(i) {
                    var cl = '<a href="/chat/channel/'+this.chatChannel+'">/'+this.chatChannel+'/</a>';
                    if(this.chatChannel == 0) cl = '<a href="/chat">/main/</a>';
                    var o = '<a href="/user/'+ this.chatUser + '" class="inline-username">' + this.chatUser + "</a> ("+cl+") &bull; ";
                    $("#online-list").append(o);
                });
            }
        });
    },
    change_color: function(e) {
        e.preventDefault();
        var link = $(this);
        var color = link.attr("data-value");
        Cookie.write("color", color, 365);
        link.siblings("#active-color").find("span").text(link.text());
        $("#home-link").css("margin-left", $("#color-select").width() +5);
        return true;
    },
    cosmitology: function() {
        $("#color-select").css("left", $("#you-are").width() + 5);
        $("#chat-wrap").css("height", $(window).height() - 230);
        $(window).resize(function() {
                $("#chat-wrap").css("height", $(window).height() - 230);
        });
    },
    actions: function() {
        $(".avatar").live('click', function() {
                var u_n = $(this).siblings(".username").text();
                var o_v = $("#sendie").val();
                $("#sendie").val(o_v + "@"+u_n+" ").focus();
        });
        $("#print-log").click(this.print_log);
    },
    keep_clean: function() {
        $("#chat-area .message:gt(99)").remove();
    },
    print_log: function() {
        var log = $("#chat-area .message"),
	    exp = $("#export-log textarea");
	exp.html("");
        for(var i = 0; i < log.length; i++) {
            var item = $(log[i]),
	        time = new Date($(item).find(".avatar").attr("alt")*1000);
            exp.append("[" + time.getDate() + "." + time.getMonth() + "." + time.getFullYear() + " " + time.getHours() + ":" + time.getMinutes() + ":" + time.getSeconds() + "] " + item.find(".username").text() + ": " + $.trim(item.find(".blurb").text()) + "\n");
        }
    },
    limit: function(e) {
	var field = $("#sendie"),
	    counter = $("#characters-left");
	if (field.val().length > field.attr("maxlength")) {
	    return false;
	} else {
	    if (e.keyCode == '13') {
		counter.text(field.attr("maxlength"));
	    } else {
		counter.text(field.attr("maxlength") - field.val().length);
	    }
	    return true;
	}
    },
    disconnect: function() {
	$.comet.disconnect();
    }
};

var Animation = {
    pop: function() {
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
}

function supports_html5_storage() {
  try {
    return 'localStorage' in window && window['localStorage'] !== null;
  } catch (e) {
    return false;
  }
}

function kickUser(nick) {
  if (nick) {
    $.post("/chat/kick/"+Chat.channel, {target: nick});
  }
}