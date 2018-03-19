/*
 * selection-sharer: Medium like popover menu to share on Twitter or by email any text selected on the page
 *
 * -- Requires jQuery --
 * -- AMD compatible  --
 *
 * Author: Xavier Damman (@xdamman)
 * GIT: https://github.com/xdamman/share-selection
 * MIT License
 */

(function($) {

    var SelectionSharer = function(options) {

        var self = this;

        options = options || {};
        if(typeof options == 'string')
            options = { elements: options };

        this.sel = null;
        this.textSelection='';
        this.htmlSelection='';
        this.startPosition = 0;
        this.endPosition = 0;

        this.appId = $('meta[property="fb:app_id"]').attr("content") || $('meta[property="fb:app_id"]').attr("value");
        this.url2share = $('meta[property="og:url"]').attr("content") || $('meta[property="og:url"]').attr("value") || window.location.href;

        this.getSelectionText = function(sel) {
            var html = "", text = "";
            sel = sel || window.getSelection();
            self.startPosition = sel.anchorOffset;
            self.endPosition = sel.focusOffset;
            if (sel.rangeCount) {
                var container = document.createElement("div");
                for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                    container.appendChild(sel.getRangeAt(i).cloneContents());
                }
                text = container.textContent;
                html = container.innerHTML;
            }
            self.textSelection = text;
            self.htmlSelection = html || text;
            return text;
        };

        this.selectionDirection = function(selection) {
            var sel = selection || window.getSelection();
            var range = document.createRange();
            if(!sel.anchorNode) return 0;
            range.setStart(sel.anchorNode, sel.anchorOffset);
            range.setEnd(sel.focusNode, sel.focusOffset);
            var direction = (range.collapsed) ? "backward" : "forward";
            range.detach();
            return direction;
        };

        this.showPopunder = function() {
            self.popunder = self.popunder || document.getElementById('selectionSharerPopunder');

            var sel = window.getSelection();
            var selection = self.getSelectionText(sel);

            if(sel.isCollapsed || selection.length < 10 || !selection.match(/ /))
                return self.hidePopunder();

            if(self.popunder.classList.contains("fixed")) {
                self.popunder.style.bottom = 0;
                return self.popunder.style.bottom;
            }

            var range = sel.getRangeAt(0);
            var node = range.endContainer.parentNode; // The <p> where the selection ends

            // If the popunder is currently displayed
            if(self.popunder.classList.contains('show')) {
                // If the popunder is already at the right place, we do nothing
                if(Math.ceil(self.popunder.getBoundingClientRect().top) == Math.ceil(node.getBoundingClientRect().bottom))
                    return;

                // Otherwise, we first hide it and the we try again
                return self.hidePopunder(self.showPopunder);
            }
            // debugger;
            if(node.nextElementSibling) {
                // We need to push down all the following siblings
                self.pushSiblings(node);
            }
            else {
                // We need to append a new element to push all the content below
                if(!self.placeholder) {
                    self.placeholder = document.createElement('div');
                    self.placeholder.className = 'selectionSharerPlaceholder';
                    self.placeholder.appendChild($(self.popunder).first().get(0));
                }

                // If we add a div between two <p> that have a 1em margin, the space between them
                // will become 2x 1em. So we give the placeholder a negative margin to avoid that
                var margin = window.getComputedStyle(node).marginBottom;
                self.placeholder.style.height = margin;
                self.placeholder.style.marginBottom = (-2 * parseInt(margin,10))+'px';
                // self.placeholder.insertBefore(node.parentNode, document.querySelector('.selectionShareable p:last-child'));
                node.parentNode.insertBefore(self.placeholder, document.querySelector('.selectionShareable p:last-child'));
            }

            // scroll offset
            var offsetTop = window.pageYOffset + node.getBoundingClientRect().bottom;
            self.popunder.style.top = Math.ceil(offsetTop)+'px';

            setTimeout(function() {
                if(self.placeholder) self.placeholder.classList.add('show');
                self.popunder.classList.add('show');
            },0);

        };

        this.pushSiblings = function(el) {
            while(el=el.nextElementSibling) { el.classList.add('selectionSharer'); el.classList.add('moveDown'); }
        };

        this.hidePopunder = function(cb) {
            cb = cb || function() {};

            if(self.popunder == "fixed") {
                self.popunder.style.bottom = '-50px';
                return cb();
            }

            self.popunder.classList.remove('show');
            if(self.placeholder) self.placeholder.classList.remove('show');
            // We need to push back up all the siblings
            var els = document.getElementsByClassName('moveDown');
            while(el=els[0]) {
                el.classList.remove('moveDown');
            }

            // CSS3 transition takes 0.6s
            setTimeout(function() {
                if(self.placeholder) document.body.insertBefore(self.placeholder);
                cb();
            }, 600);

        };

        this.show = function(e) {
            setTimeout(function() {
                var sel = window.getSelection();
                var selection = self.getSelectionText(sel);
                if(!sel.isCollapsed && selection && selection.length>10 && selection.match(/ /)) {
                    var range = sel.getRangeAt(0);
                    var topOffset = range.getBoundingClientRect().top - 5;
                    var top = topOffset + self.getPosition().y - self.$popover.height();
                    var left = 0;
                    if(e) {
                        left = e.pageX;
                    }
                    else {
                        var obj = sel.anchorNode.parentNode;
                        left += obj.offsetWidth / 2;
                        do {
                            left += obj.offsetLeft;
                        }
                        while(obj = obj.offsetParent);
                    }
                    switch(self.selectionDirection(sel)) {
                        case 'forward':
                            left -= self.$popover.width();
                            break;
                        case 'backward':
                            left += self.$popover.width();
                            break;
                        default:
                            return;
                    }
                    self.$popover.removeClass("anim").css("top", top+10).css("left", left).show();
                    setTimeout(function() {
                        self.$popover.addClass("anim").css("top", top);
                    }, 0);
                }
            }, 10);
        };

        this.hide = function(e) {
            self.$popover.hide();
        };

        this.smart_truncate = function(str, n){
            if (!str || !str.length) return str;
            var toLong = str.length>n,
                s_ = toLong ? str.substr(0,n-1) : str;
            s_ = toLong ? s_.substr(0,s_.lastIndexOf(' ')) : s_;
            return  toLong ? s_ +'...' : s_;
        };


        this.highlight = function (e) {
            e.preventDefault();
            var text = self.textSelection.replace(/<p[^>]*>/ig,'\n').replace(/<\/p>|  /ig,'').trim();
            var text_id = $('[name="text_id"]').val();
            $.post( "/text/highlight", { data: text, id: text_id}).done(function( data ) {
                var response = JSON.parse(data);
                if(response.status == 'success') {
                    window.location.reload();
                }
            });
            return false;
        };
        
        this.comment = function (e) {
            e.preventDefault();
            var text = self.textSelection.replace(/<p[^>]*>/ig,'\n').replace(/<\/p>|  /ig,'').trim();
            $('#commentModal').modal('show');
            // console.log(text);
            // $.post( "/text/highlight", { data: text} );
            return false;
        };

        this.getSelectedText = function (e) {
            return self.textSelection.replace(/<p[^>]*>/ig,'\n').replace(/<\/p>|  /ig,'').trim();
        }

        this.render = function() {
            var popoverHTML =  '<div class="selectionSharer" id="selectionSharerPopover" style="position:absolute;">'
                + '  <div id="selectionSharerPopover-inner">'
                + '    <ul>'
                + '      <li><a class="action highlight" href="#" title="Highlight this selection" ><i class="fa fa-floppy-o" aria-hidden="true"></i></a></li>'
                + '      <li><a class="action comment" href="#" title="Comment this selection" ><i class="fa fa-comment-o" aria-hidden="true"></i></a></li>'
                + '    </ul>'
                + '  </div>'
                + '  <div class="selectionSharerPopover-clip"><span class="selectionSharerPopover-arrow"></span></div>'
                + '</div>';

            var popunderHTML = '<div id="selectionSharerPopunder" class="selectionSharer">'
                + '  <div id="selectionSharerPopunder-inner">'
                + '    <label>Choose action</label>'
                + '    <ul>'
                + '      <li><a class="action highlight" href="#" title="Highlight this selection" ><i class="fa fa-floppy-o" aria-hidden="true"></i></a></li>'
                + '      <li><a class="action comment" href="#" title="Comment this selection" ><i class="fa fa-comment-o" aria-hidden="true"></i></a></li>'
                + '    </ul>'
                + '  </div>'
                + '</div>';
            self.$popover = $(popoverHTML);
            self.$popover.find('a.highlight').on('click', function(e) { self.highlight(e); });
            self.$popover.find('a.comment').on('click', function(e) { self.comment(e); });
            $('body').append(self.$popover);

            self.$popunder = $(popunderHTML);
            self.$popunder.find('a.highlight').on('click', function(e) { self.highlight(e); });
            self.$popunder.find('a.comment').on('click', function(e) { self.comment(e); });
            $('body').append(self.$popunder);

        };

        this.setElements = function(elements) {
            if(typeof elements == 'string') elements = $(elements);
            self.$elements = elements instanceof $ ? elements : $(elements);
            self.$elements.on({
                mouseup: function (e) {
                    self.show(e);
                },
                mousedown: function(e) {
                    self.hide(e);
                },
                touchstart: function(e) {
                    self.isMobile = true;
                }
            }).addClass("selectionShareable");

            document.onselectionchange = self.selectionChanged;
        };

        this.selectionChanged = function(e) {
            if(!self.isMobile) return;

            if(self.lastSelectionChanged) {
                clearTimeout(self.lastSelectionChanged);
            }
            self.lastSelectionChanged = setTimeout(function() {
                self.showPopunder(e);
            }, 300);
        };

        this.getPosition = function() {
            var supportPageOffset = window.pageXOffset !== undefined;
            var isCSS1Compat = ((document.compatMode || "") === "CSS1Compat");

            var x = supportPageOffset ? window.pageXOffset : isCSS1Compat ? document.documentElement.scrollLeft : document.body.scrollLeft;
            var y = supportPageOffset ? window.pageYOffset : isCSS1Compat ? document.documentElement.scrollTop : document.body.scrollTop;
            return {x: x, y: y};
        };

        this.render();

        if(options.elements) {
            this.setElements(options.elements);
        }
    };

    // jQuery plugin
    // Usage: $( "p" ).selectionSharer();
    $.fn.selectionSharer = function() {
        var sharer = new SelectionSharer();
        sharer.setElements(this);
        return this;
    };

    // For AMD / requirejs
    // Usage: require(["selection-sharer!"]);
    //     or require(["selection-sharer"], function(selectionSharer) { var sharer = new SelectionSharer('p'); });
    if(typeof define == 'function') {
        define(function() {
            SelectionSharer.load = function (name, req, onLoad, config) {
                var sharer = new SelectionSharer();
                sharer.setElements('p');
                onLoad();
            };
            return SelectionSharer;
        });
    } else if (typeof module === 'object' && module.exports) {
        module.exports = SelectionSharer;
    } else {
        // Registering SelectionSharer as a global
        // Usage: var sharer = new SelectionSharer('p');
        window.SelectionSharer = SelectionSharer;
    }

})(jQuery);