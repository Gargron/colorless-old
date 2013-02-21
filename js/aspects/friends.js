var Friends = {
    link: $("#toggle-friend"),
    id: this.link.attr("data-id"),
    init: function() {
        this.link.bind('click', this.toggle)
    },
    toggle: function() {
        var o;
        Colorless.loader_show();
        $.ajax({
            url: "/profile/relate",
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
        this.link.text(o.type == "friends.now" ? "Unfriend" : (o.type == "friends.sent" ? "Request sent" : (o.type == "friends.nevermore" ? "Friend again" : (o.type == "friends.not" ? "Friend" : ""))));
    }
};

$(function() {
    Friends.init();
});