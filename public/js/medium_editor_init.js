document.addEventListener("DOMContentLoaded", function () {
    var markDownEl = document.querySelector(".hidden-medium-text");

    if ($('.medium-text').length > 0) {
        var editor = new MediumEditor('.medium-text', {
            toolbar: {
            buttons: ['bold', 'italic', 'underline', 'anchor', 'h2', 'h3', 'quote', 'table', 'list-extension', 'unorderedlist','orderedlist']
            },
                buttonLabels: 'fontawesome',
                extensions: {
                markdown: new MeMarkdown({
                    events: ["input", "change", 'upd']
                },
                function (md) {
                    markDownEl.value = md;
                }),
                table: new MediumEditorTable(),
                'list-extension': new MediumEditorList(),
                autolist: new AutoList()
            },
                mediumEditorList: {
                    newParagraphTemplate: '<li>...</li>',
                    buttonTemplate: '<b><i class="fa fa-list" aria-hidden="true"></i></b>',
                    addParagraphTemplate: 'Add new item',
                    isEditable: true
                }
        });
        $('.medium-text').mediumInsert({
            editor: editor,
            addons: {
                images: {
                    fileUploadOptions: {
                        url: '/text/upload',
                    }
                }
            }

        });
    }
});