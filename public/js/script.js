$(document).ready(function () {

    $('.menu-popover').popover({
        html: true,
        content: function() {
            var content = $(this).attr("data-popover-content");
            return $(content).children(".popover-body").html();
        },
    });

    $('.medium-text').focusout(function () {
        document.querySelector('.medium-text').dispatchEvent(new Event('upd'));
    });

    //Watch for repository
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

    //Unwatch for repository
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

    //Watch for text

    $(document).on('click', '.text-subscribe', function (e) {
        e.preventDefault();
        var text_id = $(this).data('text-id');
        if(text_id && text_id > 0) {
            $.post( "/text/watch", { text_id: text_id}).done(function( data ) {
                var response = JSON.parse(data);
                if(response.status == 'success') {
                    $('.text-subscribe').remove();
                    $('.title').append("<a href=\"#\" data-sub-id=\""+ response.sub_id +"\" class=\"btn float-right btn-outline-warning text-unsubscribe\"><i class=\"fa fa-eye-slash\" aria-hidden=\"true\"></i></a>");
                }
            });
        }
    });

    // Unwatch for text
    $(document).on('click', '.text-unsubscribe', function (e) {
        e.preventDefault();
        var sub_id = $(this).data('sub-id');
        if(sub_id && sub_id > 0) {
            $.post( "/text/unwatch", { sub_id: sub_id}).done(function( data ) {
                var response = JSON.parse(data);
                if(response.status == 'success') {
                    $('.text-unsubscribe').remove();
                    $('.title').append("<a href=\"#\" data-text-id=\""+ response.text_id +"\" class=\"btn float-right btn-outline-warning text-subscribe\"><i class=\"fa fa-eye\" aria-hidden=\"true\"></i></a>");
                }
            });
        }
    });

    if (window.content !== undefined) {
        var editorrr = new tui.Editor({
            el: document.querySelector('.medium-text'),
            initialEditType: 'wysiwyg',
            previewStyle: 'vertical',
            height: '500px',
            hideModeSwitch: true,
            usageStatistics: false,
            exts: ['scrollSync', 'table'],
            initialValue: content,
            events: {
                change: function() {
                    $('.hidden-medium-text').val(editorrr.getMarkdown());
                }
            }
        });
    }

    $('.delete-my-account').on('click', function () {
        var res = confirm('Are you sure you want to delete you account?');
        if(!res) {
            return false;
        }
    })
});