var Posts = {
    links_vote: $(".vote-link"),
    init: function() {
        this.links_vote.bind('click', this.toggle_vote);
    },
    toggle_vote: function() {
        var link  = $(this),
            id    = link.attr("data-id"),
            model = link.attr("data-model"),

            o;
        Colorless.loader_show();
        $.ajax({
            url: "/post/vote",
            type: "POST",
            data: {id: id, model: model},
            success: function(r) {
                o = r;
                Colorless.loader_hide();
            },
            dataType: "json",
            async: false
        });
        this.update_vote(link, o);
    },
    update_vote: function(link, o) {
        link.text(o.count);
        link.unbind('click', this.toggle_vote);
    }
};