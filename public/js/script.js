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

    $(document).on('click', '.repo-subscribe', function (e) {
        e.preventDefault();
        var repo_id = $(this).data('repo-id');
        if(repo_id && repo_id > 0) {
            $.post( "/repository/watch", { repo_id: repo_id}).done(function( data ) {
                var response = JSON.parse(data);
                if(response.status == 'success') {
                    $('.repo-subscribe').remove();
                    $('.repository-header-title').append("<a href=\"#\" data-sub-id=\""+ response.sub_id +"\" class=\"btn float-right btn-outline-warning repo-unsubscribe\"><i class=\"fa fa-eye-slash\" aria-hidden=\"true\"></i></a>");
                }
            });
        }
    });

    $(document).on('click', '.repo-unsubscribe', function (e) {
        e.preventDefault();
        var sub_id = $(this).data('sub-id');
        if(sub_id && sub_id > 0) {
            $.post( "/repository/unwatch", { sub_id: sub_id}).done(function( data ) {
                var response = JSON.parse(data);
                if(response.status == 'success') {
                    $('.repo-unsubscribe').remove();
                    $('.repository-header-title').append("<a href=\"#\" data-repo-id=\""+ response.repo_id +"\" class=\"btn float-right btn-outline-warning repo-subscribe\"><i class=\"fa fa-eye\" aria-hidden=\"true\"></i></a>");
                }
            });
        }
    });


});