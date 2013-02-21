(function($) {
    $.fn.tabify = function(o) {
        return $(this).each(function() {
            var e     = $(this);
            var links = e.find(".tab-links a");
            var tabs  = e.find(".tab-is");

            tabs.hide();
            tabs.eq(0).addClass("active").show();

            links.bind('click', function(ev) {
                ev.preventDefault();
                var e = $(this);
                e.parent().addClass("active").siblings().removeClass("active");
                e.parent().parent().parent().find(".tab-is").removeClass("active").hide();
                e.parent().parent().parent().find(".tab-is").eq($(this).parent().index()).addClass("active").show();
            });
        });
    }
})(jQuery);