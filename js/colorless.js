var Colorless = {
    loader: $("#loader"),
    loader_is: false,
    loader_show: function() {
        if(!this.loader_is) {
            this.loader_is = true;
            this.loader.fadeIn(500);
        }
    },
    loader_hide: function() {
        if(this.loader_is) {
            this.loader_is = false;
            this.loader.fadeOut(500);
        }
    },
    timefy: function() {
        $(".timestamp").each(function(i, e) {
            var stamp = $(e).attr("data-timestamp");
            $(e).text(Date.relativeTime(stamp));
        });
    },
    init: function() {
        setInterval(this.timefy, 10000);
    },
    exception: function() {
        alert("Some kind of error on the page occurred, so whatever you just wanted to do didn't work.");
    }
};

$(function() {
    Colorless.init();
});