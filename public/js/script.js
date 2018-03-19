$(document).ready(function () {
    $('.menu-popover').popover({
        html: true,
        content: function() {
            var content = $(this).attr("data-popover-content");
            return $(content).children(".popover-body").html();
        },
    });

    $('[name="create_text"]').on('submit', function () {
        $('.medium-text').find('.medium-insert-buttons').remove();
        $('.hidden-medium-text').val($('.medium-text').html());
    });
});