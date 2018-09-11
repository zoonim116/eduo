$(document).ready(function () {

    $('.menu-popover').popover({
        html: true,
        content: function() {
            var content = $(this).attr("data-popover-content");
            return $(content).children(".popover-body").html();
        },
    });

    if($('#text-category').length > 0) {
        $('#text-category').selectize({
            allowEmptyOption: false,
            create: true
        });
    }
    
    function alphabet_filter(character) {
        $('.category-list div[data-character]').hide();
        $.map($('.category-list div[data-character]'), function (val) {
            if($(val).data('character') === character) {
                $(val).show();
            }
        });
    }

    if($('.category-list').length > 0) {
        alphabet_filter('A');
        $('.alphabet-buttons button').on('click', function () {
            alphabet_filter($(this).text());
        });
    }

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

    //Watch for profile
    $(document).on('click', '.profile-subscribe', function (e) {
        e.preventDefault();
        var profile_id = $(this).data('profile-id');
        if(profile_id && profile_id > 0) {
            $.post( "/user/watch", { profile_id: profile_id}).done(function( data ) {
                var response = JSON.parse(data);
                if(response.status == 'success') {
                    $('.profile-subscribe').remove();
                    $('.follow-sidebar').append("<a href=\"#\" data-sub-id=\""+ response.sub_id +"\" class=\"btn float-right btn-outline-warning profile-unsubscribe\"><i class=\"fa fa-eye-slash\" aria-hidden=\"true\"></i> Unfollow</a>");
                }
            });
        }
    });

    // Unwatch for profile
    $(document).on('click', '.profile-unsubscribe', function (e) {
        e.preventDefault();
        var sub_id = $(this).data('sub-id');
        if(sub_id && sub_id > 0) {
            $.post( "/user/unwatch", { sub_id: sub_id}).done(function( data ) {
                var response = JSON.parse(data);
                if(response.status == 'success') {
                    $('.profile-unsubscribe').remove();
                    $('.follow-sidebar').append("<a href=\"#\" data-profile-id=\""+ response.profile_id +"\" class=\"btn float-right btn-outline-warning profile-subscribe\"><i class=\"fa fa-eye\" aria-hidden=\"true\"></i> Follow</a>");
                }
            });
        }
    });

    if (window.content !== undefined) {
        var editorrr = new tui.Editor({
            el: document.querySelector('.medium-text'),
            // initialEditType: 'markdown',
            previewStyle: 'vertical',
            height: '500px',
            usageStatistics: false,
            exts: ['scrollSync', 'table'],
            initialValue: content,
            hooks: {
                addImageBlobHook: function(blob, callback) {
                    var fd = new FormData;
                    fd.append('files', blob);
                    $.ajax({
                        url: '/text/upload',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        success: function (data) {
                            data = JSON.parse(data);
                            callback(data.url, 'alt text');
                        }
                    });
                },
            },
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

    if($('#rating').length > 0) {
        $('#rating').barrating({
            theme: 'bars-1to10'
        });
    }
    $('select[id^="disabled_rating_"]').barrating({
        readonly:true,
        theme: 'bars-1to10'
    });

    if($('#datetimepicker1').length > 0) {
        $('#datetimepicker1').datetimepicker({
            format: 'DD-MM-YYYY HH:mm',
            pickSeconds: false
        });
    }
});