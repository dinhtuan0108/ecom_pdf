Ext.namespace("AuIt.HTML5DragDrop");AuIt.HTML5DragDrop=function(a){a=a||{};Ext.apply(this,a);this.actions={};this.addEvents("dragstart","drag","dragenter","dragover","dragleave","drop","dragend");AuIt.HTML5DragDrop.superclass.constructor.call(this);this.init()};Ext.extend(AuIt.HTML5DragDrop,Ext.util.Observable,{init:function(){var a=Ext.getBody();a.on("dragstart",this.onDragStart,this);a.on("drag",this.onDrag,this);a.on("dragenter",this.onDragEnter,this);a.on("dragover",this.onDragOver,this);a.on("dragleave",this.onDragLeave,this);a.on("drop",this.onDrop,this);a.on("dragend",this.onDragEnd,this)},onDragStart:function(a){var b={event:a,type:a.type,target:a.target,handler:this,allowed:false};this.fireEvent("dragstart",b);if(b.allowed){return true}a.stopEvent();return true},onDrag:function(){},onDragEnter:function(){},onDragOver:function(){},onDragLeave:function(){},onDrop:function(){},onDragEnd:function(){}});function dragenter(a){a.stopPropagation();a.preventDefault()}function dragover(a){a.stopPropagation();a.preventDefault()}function drop(c){c.stopPropagation();c.preventDefault();var b=c.dataTransfer;var a=b.files;handleFiles(a)}function handleFiles(e){var g=document.getElementById("preview");for(var d=0;d<e.length;d++){var c=e[d];var f=/image.*/;if(!c.type.match(f)){continue}var b=document.createElement("img");b.classList.add("obj");b.file=c;g.appendChild(b);var a=new FileReader();a.onload=(function(h){return function(i){h.src=i.target.result}})(b);a.readAsDataURL(c)}};