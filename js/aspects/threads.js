var Threads = {
    link: $("#toggle-heart"),
    id: this.link.attr("data-id") || null,
    count: $("#thread-heart-count"),
    reply: $("#newPost"),
    init: function() {
        this.link.bind('click', this.toggle);
    },
    toggle: function() {
        var o;
        Colorless.loader_show();
        $.ajax({
            url: "/thread/love",
            type: "POST",
            data: {id: this.id},
            success: function(r) {
                o = r;
                Colorless.loader_hide();
            },
            dataType: "json",
            async: false
        });
        this.update(o);
    },
    update: function(o) {
        this.count.text(o.count);
        this.link.text(o.type == "hearts.now" ? "Unheart" : (o.type == "hearts.nevermore" ? "Heart again" : ""));
    },
    livefy: function() {
        $.comet.connect("/thread/live?id="+this.id, {onError: Colorless.exception});
        $.comet.bind(this.livefy_update);
        this.reply.bind('submit', this.livefy_post);
    },
    livefy_update: function(d, t) {
        var msg = $("<div/>");
        var atr = $("<address/>");
        var cnt = $("<div/>");
        var met = $("<div/>");
        var tim = Date.parse(d.postCreatedAt);
        msg.addClass("hentry entry entry-live");
        msg.attr("id", "p"+d.postID);
        atr.addClass("entry-author author vcard");
        atr.html('<img alt="'+d.userName+'" src="http://www.gravatar.com/avatar/'+d.userHash+'?s=64&amp;d=http%3A%2F%2Fthecolorless.net%2Fimages%2Favatars%2Fblack.png" width="64" height="64" class="entry-author-avatar" /><a href="/user/'+d.userName+'" class="entry-author-username">'+d.userName+'</a>');
        cnt.addClass("entry-content");
        cnt.html(d.postContent);
        met.addClass("entry-meta");
        met.html('<ul><li style="overflow:hidden"><a href="javascript:;" data-postid="'+d.postID+'" data-model="up" class="vote-link">Thumbs up</a><span class="vote-details">~</span><a href="javascript:;" data-postid="'+d.postID+'" data-model="down" class="vote-link">Thumbs down</a><span class="vote-details">~</span></li><li><abbr class="published tooltipped-b timestamp" data-timestamp="'+tim+'" title="'+d.postCreatedAt+'">'+Date.relativeTime(tim)+'</abbr></li><li>#'+d.postID+'</li></ul>');
        msg.append(atr);
        msg.append(cnt);
        msg.append(met);
        msg.appendTo(".thread-posts:eq(0)");
        $("#p"+d.postID).hide().slideDown(500);
    },
    livefy_post: function(e) {
        e.preventDefault();
        if(this.reply.find("textarea").val().length < 4) return false;
        Colorless.loader_show();
        this.reply.find("button").attr("disabled", "disabled");
        $.post(
            '/post/create',
            this.reply.serialize(),
            function() {
                this.reply.find("textarea").val("");
                this.reply.find("button").removeAttr("disabled");
                Colorless.loader_hide();
            }
        );
    }
};

$(function() {
    Threads.init();
})