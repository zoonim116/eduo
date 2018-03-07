$(document).ready(function () {
    $('.menu-popover').popover({
        html: true,
        content: function() {
            var content = $(this).attr("data-popover-content");
            return $(content).children(".popover-body").html();
        },
    });

    if ($('.medium-text').length > 0) {
        var editor = new MediumEditor('.medium-text', {
            toolbar: {
                buttons: ['bold', 'italic', 'underline', 'anchor', 'h2', 'h3', 'quote', 'table']
            },
            buttonLabels: 'fontawesome',
            extensions: {
                markdown: new MeMarkdown(function (md) {
                    // markDownEl.textContent = md;
                }),
                table: new MediumEditorTable()
            }
        });
        $('.medium-text').mediumInsert({
            editor: editor,
        });
    }

    $('[name="create_text"]').on('submit', function () {
        $('.hidden-medium-text').val($('.medium-text').html());
    });

    $('.text p').selectionSharer();
});