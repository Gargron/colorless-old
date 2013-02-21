$(function() {
    var socket      = {},
        chat_wrap   = $("#chat_wrap"),
        chat        = $("#chat_area"),
        chat_form   = $("#chat_form"),
        speech      = $("#chat_input"),
        name_box    = $("#chat_name"),
        colors      = $("#colors"),
        counter     = $("#chat_online_count"),
        json        = JSON.stringify,
        online      = {
            userlist: [],
            count: 0,
            codes: []
        },
        extractCodes= function(user) {
            renderColors(user.color);
            return user.color;
        },
        renderChat  = function(data) {
            var box    = $("<li>"),
                meta   = $("<div>"),
                bubble = $("<div>");
                box.addClass("item").addClass("item_" + data.type);
                meta.addClass("item_meta");
                bubble.addClass("item_bubble");
                if(data.user || data.type !== "status") {
                    if(typeof data.user.color !== "undefined" && data.user.color !== null) {
                        box.addClass("item_color_" + data.user.color);
                    }
                    meta.html((data.user.name ? '<img src="http://gravatar.com/avatar/' + data.user.hash + '?s=40&amp;d=http://thecolorless.net/images/avatars/black.png" width="40" height="40" /><strong class="name tt" title="' + (data.user.rname ? data.user.rname : "G" + data.user.id) + '">' + data.user.name + "</strong>" : "") + (data.type == "action" ? " " + data.verb + " " : ""));
                }
                bubble.html(data.content);
                box.append(meta).append(bubble);
                chat.prepend(box);
        },
        renderColors= function(color) {
            if(typeof color !== "undefined" && color !== null && online.codes.indexOf(color) == -1) {
                colors.append("ul#chat_area li.item_message.item_color_" + color + " .item_bubble { background-color: #" + color + "; } ul#chat_area li.item_message.item_color_" + color + " .item_bubble:after { border-right-color: #" + color + "; } ul#chat_area li.item_action.item_color_" + color + " { color: #" + color + "}");
            }
        },
        secret      = function(type) {
            var header = $("header"),
                drama  = document.getElementById("drama");
            switch(type) {
                case "rainbow":
                    header.stop().animate({
                        "background-position": "0 0"
                    }, 400, function() {
                        header.delay(5000).animate({
                            "background-position": "0 -280px"
                        }, 400);
                    });
                break;
                case "drama":
                    try {
                        drama.play();
                    } catch (SyntaxError) {

                    }
                break;
            }

        };

    if (typeof io === "undefined") {
        socket.connect = function() {}, socket.on = function() {}, socket.send = function() {};
        renderChat({type: "status", content: "Chat is not available, sorry bro"});
    } else {
        socket = io.connect("http://thecolorless.net:90")
    }

    socket.on('connect', function(){
        socket.send(json({type: "meta", client: client_id}));
        socket.send(json({type: "inquiry", get: "users"}));
        renderChat({type: "status", content: "We are online"});
    });

    socket.on('message', function(data){
        try {
            var request = JSON.parse(data);
        } catch (SyntaxError) {
            return false;
        }

        if(request.type == "meta") {
            if(request.users) {
                counter.text(request.users.length);
                online.count = request.users.length;
                online.userlist = request.users;
                online.codes = request.users.map(extractCodes);
            }
            if(request.buffer) {
                for(var i = 0; i < request.buffer.length; i++) {
                    renderChat(request.buffer[i]);
                }
            }
        } else if(request.type == "special") {
            secret(request.make);
        } else {
            renderChat(request);
            if(request.type == "status" && request.users) {
                counter.text(request.users.length);
                online.count = request.users.length;
                online.userlist = request.users;
                online.codes = request.users.map(extractCodes);
            }
        }
    });

    socket.on('disconnect', function(){
        renderChat({type: "status", content: "Oh bugger, we disconnected"});
    });

    speech.keydown(function(e) {
        if(e.keyCode == '13') {
            e.preventDefault();
            var msg = { text: speech.val() };
            speech.val("");
            if (msg.text.length > 0) { socket.send(json(msg)) } else { }
        }
    });

    chat_form.submit(function(e) {
        e.preventDefault();
    });

    name_box.blur(function(e) {
        var name = name_box.val();
        if(name.length > 0) {
            socket.send(json({new_name: name}));
        }
    });

    chat_wrap.css("height", $(window).height() - $("header").outerHeight(false)) && $(window).resize(function() {
        chat_wrap.css("height", $(window).height() - $("header").outerHeight(false));
    });
});