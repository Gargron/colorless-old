$(function() {
	$.ajax({
	  type: 'GET',
	  url: 'http://thecolorless.net:91/socket.io/socket.io.js',
	  timeout: 2000,
	  cache: true,
	  dataType: "script",
	  success: function(data, status, jqXHR) {
  		live = true;

      var count = 0,
          time  = 0,
          timercont = $("#activity-speed");

      function timer() {
          time  =+ 1;
      };

      function getSpeed() {
          //console.log("(" + ((count/time)) + " act/sec)");
      };

      var timerID = setInterval(timer, 1000),
          alertID = setInterval(getSpeed, 2000);

  		console.log("Loaded. Live is:" + live);

  		if(typeof threadID !== "undefined") {
  			var thread = io.connect('http://thecolorless.net:91/threads-specific');
  	  		thread.on('connect', function() {
  	    		thread.on('message', function (rr) {
  	    			var r = $.parseJSON(rr);
  	                if(Number(r.thread) !== threadID) {
  	                	return false;
  	                }
  				    var msg = $("<div/>");
  				    var atr = $("<address/>");
  				    var cnt = $("<div/>");
  				    var met = $("<div/>");
  				    msg.addClass("hentry entry entry-live author-role-" + r.data.user.role);
  				    msg.attr("id", "p"+r.data.id);
  				    atr.addClass("entry-author author vcard");
  				    atr.html('<img alt="'+r.data.user.name+'" src="http://www.gravatar.com/avatar/'+r.data.user.hash+'?s=64&amp;d=http%3A%2F%2Fthecolorless.net%2Fimages%2Favatars%2Fblack.png" width="64" height="64" class="entry-author-avatar" /><a href="/user/'+r.data.user.name+'" class="entry-author-username">'+r.data.user.name+'</a>');
  				    cnt.addClass("entry-content");
  				    cnt.html(r.data.content);
  				    met.addClass("entry-meta");
  				    met.html('<ul><li><abbr class="published tooltipped-b timestamp" data-timestamp="'+r.data.created_at+'">'+Date.relativeTime(r.data.created_at)+'</abbr></li><li>#'+r.data.id+'</li></ul>');
  				    msg.append(atr);
  				    msg.append(cnt);
  				    msg.append(met);
  				    msg.appendTo(".thread-posts:eq(0)");
  				    $("#p"+r.data.id).hide().slideDown(200);
  	    		});
  	  		});
    		}
    		var stream = io.connect('http://thecolorless.net:91/threads-all');
    		stream.on('connect', function() {
    			var cont = $("#activity-stream"),
    			    tscont = $(".threads-list");
    			cont.prepend("<div>Connected, waiting for activity...</div>");
    			stream.on('message', function (rr) {
            count =+ 1;
    				var r = $.parseJSON(rr),
    				    tcont = $("#thread" + r.thread);
    				if(r.new) {
    					var mess = "New thread '" + r.data.title + "' created";
    					tscont.prepend('<dl class="thread-item" id="thread'+r.thread+'"><dd class="thread-item-meta">P1<br/>&#9829; 0</dd><dt class="thread-item-title"><strong><a href="/thread/'+r.thread+'">'+r.data.title+'</a></strong><span>In '+r.data.board+' by '+r.data.last_user_name+', '+Date.relativeTime(r.data.last_updated_at)+'</span></dt><dd class="thread-item-last"><a href="/thread/'+r.thread+'/gotopost/'+r.thread+'"><span class="thread-item-last-user-id">'+r.data.last_user_name+'</span><br /><span class="thread-item-last-updated-at">'+Date.relativeTime(r.data.last_updated_at)+'</span></a></dd></dl>');
    				} else {
    					if(r.data.last_id) {
    						var mess = "New reply to thread " + r.data.title;
    						var old_number = tcont.find(".thread-item-posts");
    						var old_last_user_id = tcont.find(".thread-item-last-user-id"),
    						    old_last_updated_at = tcont.find(".thread-item-last-updated-at"),
    						    old_link = tcont.find(".thread-item-last > a").attr("href");

    						//var new_link = old_link.replace(/([\d]+\/gotopost\/)([\d]+)/ig, "$1" + r.data.last_id);

    						old_last_user_id.text(r.data.last_user_name);
    						old_last_updated_at.text(Date.relativeTime(r.data.last_updated_at));
    						//tcont.find(".thread-item-last > a").attr("href", new_link);

    						old_number.text((old_number.text()*1) + 1);
    						tcont.detach();
    						tscont.prepend(tcont);
    					}
    					if(r.data.hearts_plus) {
    						var mess = r.data.title + " got a +heart";
     						var old_number = tcont.find(".thread-item-hearts");
    						old_number.text((old_number.text()*1) + 1);
    					}
    					if(r.data.hearts_minus) {
    						var mess = r.data.title + " lost a heart";
     						var old_number = tcont.find(".thread-item-hearts");
    						old_number.text((old_number.text()*1) - 1);
    					}
    				}
            tcont.css({backgroundColor:"#666"}).animate({backgroundColor: $.Color("rgba(0,0,0,0)")}, 500);
    				cont.prepend("<div>" + mess + "</div>");
    			});
    		});
  	}
	});
});