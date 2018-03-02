$(document).ready(function () {
    $('.menu-popover').popover({
        html: true,
        content: function() {
            var content = $(this).attr("data-popover-content");
            return $(content).children(".popover-body").html();
        },
    });

    if ($('.medium-text').length > 0) {
        var editor = new MediumEditor('.medium-text');
    }
});