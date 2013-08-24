Ext.namespace("AuIt.Editor");AuIt.Editor.Selection=function(a){this.editor=a;this.doc=a.getDoc();this.win=a.getWin()};AuIt.Editor.Selection.prototype={_getSel:function(){return this.win.getSelection?this.win.getSelection():this.doc.selection},getBookmark:function(){var a=this._getRng();return a.cloneRange()},moveToBookmark:function(a){this._getSel().removeAllRanges();this._getSel().addRange(a)},checkFirstLine:function(){var a,c=this.editor.getActiveBlock();if(c&&c.iscontentEditable()&&(a=c.getEl())&&a.isContentEditable()){a.checkEmptyHTML();if(this._getSel().anchorOffset===0&&this._getSel().focusOffset===0){var b=a.dom;if(this._getSel().anchorNode==b&&b==this._getSel().focusNode){this.selectNode(a.getFocusBlock(),true)}}}},_getRng:function(){var b=this,c=b._getSel(),d;try{if(c){d=c.rangeCount>0?c.getRangeAt(0):(c.createRange?c.createRange():b.doc.createRange())}}catch(a){}if(!d){d=Ext.isIE?b.doc.body.createTextRange():b.doc.createRange()}return d},getNode:function(){var a=this,c=a._getRng(),b=a._getSel(),f;var d=null;if(!Ext.isIE){if(!c){return null}f=c.commonAncestorContainer;if(!c.collapsed){if(Ext.isWebKit&&b.anchorNode&&b.anchorNode.nodeType==1){return b.anchorNode.childNodes[b.anchorOffset]}if(c.startContainer==c.endContainer){if(c.startOffset-c.endOffset<2){if(c.startContainer.hasChildNodes()){f=c.startContainer.childNodes[c.startOffset]}}}}return AuIt.Editor.utils.typeNode(f)}return c.item?c.item(0):c.parentElement()},isEmpty:function(b){return(""+this._getSel())===""},selectNode:function(c,b){if(!c){return null}this._getSel().removeAllRanges();var a=this._getRng();a.selectNode(c);if(b===true||b===false){a.collapse(b)}this._getSel().addRange(a);this.editor.updateToolbar(false)},getRange:function(){var a=this,b=a._getSel();return b.createRange?b.createRange():b.getRangeAt(0)},findParent:function(a){var c=this.getRange();a=a.toUpperCase();var b;var d=null;d=Ext.fly(this.getNode());d=d.findParent(a);return d?d.dom:null},insertNodeAtSelection:function(f){var m=this.doc;var o=this,a=o._getRng(),p=o._getSel(),j;p.removeAllRanges();a.deleteContents();var b=a.startContainer;var l=a.startOffset;var g=m.createRange();if(b.nodeType==3&&f.nodeType==3){b.insertData(l,f.data);g.setEnd(b,l+f.length);g.setStart(b,l+f.length)}else{var c;var k;if(b.nodeType==3){var d=b;b=d.parentNode;var n=d.nodeValue;var i=n.substr(0,l);var h=n.substr(l);k=m.createTextNode(i);c=m.createTextNode(h);b.insertBefore(c,d);b.insertBefore(f,c);b.insertBefore(k,f);b.removeChild(d)}else{c=b.childNodes[l];b.insertBefore(f,c)}try{g.setEnd(c,0);g.setStart(c,0)}catch(j){alert(j)}}p.addRange(g)}};