!function(t,e){"use strict";"object"==typeof module?module.exports=e:"function"==typeof define&&define.amd?define(e):t.MediumEditorList=e}(this,function(t){function e(t){return"undefined"!=typeof t&&null!==t}function n(t,e,n){if(void 0!==t.getSelection&&e){var i=t.createRange(),o=t.getSelection();n?i.setStartBefore(e):i.setStartAfter(e),i.collapse(!0),o.removeAllRanges(),o.addRange(i)}}"function"!=typeof Object.assign&&!function(){Object.assign=function(t){"use strict";if(void 0===t||null===t)throw new TypeError("Cannot convert undefined or null to object");for(var e=Object(t),n=1;n<arguments.length;n++){var i=arguments[n];if(void 0!==i&&null!==i)for(var o in i)i.hasOwnProperty(o)&&(e[o]=i[o])}return e}}();var i="medium-editor-list-add-paragraph",o="data-medium-editor-list-id",r="medium-editor-list",s=function(s,l){function u(t){return A=t,b(),s.on(T,"click",d),s.on(P,"click",f),C=Number(t.getAttribute(o)),x}function a(t){var e,n=(new Date).getTime();return t=t?"<li>"+t+"</li>":l.newParagraphTemplate,q.pasteHTML('<ul class="'+r+'" '+o+'="'+n+'">'+t+S()+"</ul><br/>",{cleanAttrs:[]}),e=P.querySelector("ul["+o+'="'+n+'"].'+r),u(e)}function c(){return C}function d(){T.insertAdjacentHTML("beforebegin",l.newParagraphTemplate),y(),n(j,w[w.length-2],!0),m()}function f(){g()?m():E()}function g(){var e=t.selection.getSelectionRange(j),n=t.selection.getSelectedParentElement(e),i=t.util.getClosestTag(n,"li");return i?Number(i.parentElement.getAttribute(o))===C:!1}function m(){v()!==!1&&(h()?L(T,"block"):(p(),L(T,"block")))}function h(){return e(A.querySelector("."+i))}function p(){A.insertAdjacentHTML("beforeend",S()),y(),b()}function b(){T=A.querySelector("."+i)}function E(){T&&(T.style.display="none")}function v(){return l.isEditable===!0}function L(t,e){t.style.display=e}function y(){w=A.querySelectorAll("li")}function S(){return'<li class="'+i+'">'+l.addParagraphTemplate+"</li>"}var T,w,A,C,x=this,j=s.document,q=s.base,P=q.origElements[0]||q.origElements;x.init=u,x.create=a,x.show=m,x.getId=c},l=t.Extension.extend({name:"list-extension",init:function(){var t=this;t.options=Object.assign({},{newParagraphTemplate:"<li>...</li>",buttonTemplate:"<b>List</b>",addParagraphTemplate:"Add new paragraph",isEditable:!0},t.base.options.mediumEditorList||{}),t.button=t.document.createElement("button"),t.button.classList.add("medium-editor-action"),t.button.innerHTML=t.options.buttonTemplate,t.editor=this.base,t.listInstances={},t.on(t.button,"click",t.onClick.bind(t)),function(t){window.setTimeout(function(){t.initExistsingLists()},0)}(t)},getExistsingLists: function () {return  [];
},initExistsingLists:function(){for(var t,e=this.getExistsingLists(),n=0,i=e.length;i>n;n+=1)t=new s(this,this.options).init(e[n]),this.addList(t)},getExistingLists:function(){var t=this.editor.origElements[0]?this.editor.origElements[0].querySelectorAll("ul."+r):this.editor.origElements.querySelectorAll("ul."+r);return e(t)?t:[]},getButton:function(){return this.button},onClick:function(){var t=this.getCurrentList();e(t)?t.show():(t=new s(this,this.options).create(this.getSelectedContextHtml()),this.addList(t))},addList:function(t){this.listInstances[t.getId()]=t},getSelectedContextHtml:function(){var e=this.document.createElement("div"),n=t.selection.getSelectionRange(this.document).cloneContents().cloneNode(!0);return e.appendChild(n),e.innerHTML},getCurrentList:function(){var e,n=t.selection.getSelectionRange(this.document),i=t.selection.getSelectedParentElement(n),r=t.util.getClosestTag(i,"li");return r?(e=r.parentElement.getAttribute(o),this.listInstances[Number(e)]):void 0}});return l}("function"==typeof require?require("medium-editor"):MediumEditor));