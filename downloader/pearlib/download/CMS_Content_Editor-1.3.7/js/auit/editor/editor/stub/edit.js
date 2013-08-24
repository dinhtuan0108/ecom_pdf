Ext.namespace("AuIt.Editor");AuIt.Editor.Panel=Ext.extend(AuIt.Panel.Iframe,{proxy:null,getActionMgr:function(){return AuIt.App.getActionMgr()},initComponent:function(){this.blockFrameCoord={};this.blocks=[];AuIt.Editor.Panel.superclass.initComponent.call(this);var a=this.getActionMgr();a.on("beforecmd",this.onbeforeCmd,this);a.on("beforerequest",this.onbeforeRequest,this);a.on("cmd",this.onCmd,this);a.on("aftercmd",this.onafterCmd,this);a.on("watchtask",this.watchTask,this);a.on("queryState",this.onqueryState,this);a.on("itemchanged",this.onItemChanged,this)},destroy:function(){this.destroyBlocks();var a=this.getActionMgr();a.un("beforecmd",this.onbeforeCmd,this);a.un("beforerequest",this.onbeforeRequest,this);a.un("cmd",this.onCmd,this);a.un("aftercmd",this.onafterCmd,this);a.un("watchtask",this.watchTask,this);a.un("itemchanged",this.onItemChanged,this);a.un("queryState",this.onqueryState,this);AuIt.Editor.Panel.superclass.destroy.call(this)},getActiveBlockPageData:function(){if(this.activeProperty){return this.activeProperty.pagedata}return null},getBlockProperty:function(a){var b=null;Ext.each(this.blocks,function(c){if(c.property){if(c.property.pagedata.blockIds[a]){b=c.property;return false}}},this);return b},getBlockData:function(a){var b=null;Ext.each(this.blocks,function(c){if(c.property){if(c.property.pagedata.blockIds[a]){b=c.property.pagedata.blockIds[a];return false}}},this);return b},getBlock:function(a){var b=null;Ext.each(this.blocks,function(c){if(c.property){if(c.property.pagedata.blockIds[a]){b=c;return false}}},this);return b},onItemChanged:function(a){if(!a.blockData){return}if(!this.getActionMgr().isActivePanel(this)){return}if(a.opt&&a.opt.modified){this.modified=true}var d=null,b=a.blockData;var c=this.getBlock(b.DomID);if(c){d=c.property}if(!d&&b.isInline){c={};c.typ="INLINE";b.pagedata.blockIds={};b.pagedata.blockIds[b.DomID]={};d=c.property=new AuIt.Editor.Property.InlineBlock({editor:this.editor,pagedata:b.pagedata,mainPageData:this.main_property?this.main_property.pagedata:null});this.blocks.push(c)}if(d==this.activeProperty){return}this.activeProperty=d;if(this.activeProperty){this.activeProperty.validPropertys()}this._setPropertyPanel(this.activeProperty)},onbeforeCmd:function(a){if(this.getActionMgr().isActivePanel(this)){a.editor=this}},onbeforeRequest:function(a){switch(a.itemId){case"SAVE":if(!this.getActionMgr().isActivePanel(this)){return}Ext.each(this.blocks,function(b){if(b.property){b.property.addSaveData(a)}},this);break;case"SAVEALL":Ext.each(this.blocks,function(b){if(b.property){b.property.addSaveData(a)}},this);break}},onCmd:function(b){switch(b.itemId){case"CMD_IFRAMEMSG":switch(b.data.itemId){case"EDITFRAME_INIT":if(this.iframeUID==b.iframeUID){this.proxy={origin:b.event.origin,source:b.event.source,iframeUID:this.iframeUID,title:b.data.title};this.pageData=b.data.AUIT_PAGEDATA;b.data={itemId:"EDITSTATES",lstate:AuIt.App.l,states:b.actionMgr.getEditStates(),altKeyMap:AuIt.App.getShortKeyMap()};b.actionMgr.postMessage(b,this.proxy);this.initEditor()}break}break;case"MAIN_TAB_CHANGE":if(b.newTab==this){this._setPropertyPanel(this.main_property)}else{if(b.oldTab==this){if(this.proxy){var a=this.getActionMgr().getAction("INTERN_MAIN_TAB_CHANGE","Editor");a.postMessage(this.proxy);this._setPropertyPanel(null)}}}break}},onafterCmd:function(b,a){if(b.itemId=="STATIC_BLOCKS_DUP"){if(!this.getActionMgr().isActivePanel(this)){return}var c=this.getActionMgr().getAction("INTERN_AFTERCMD","Editor");c.data={cmd:b.cmd,modul:b.modul,response:a};c.executeCmd(this.proxy)}else{if(b.itemId=="SAVE"||b.itemId=="SAVEALL"){if(b.itemId=="SAVE"&&!this.getActionMgr().isActivePanel(this)){return}if(a.success){if(a.reload){this.destroyBlocks()}else{this.ignoreChecked=true;Ext.each(this.blocks,function(d){if(d.property){d.property.commitChanges(b)}},this);this.ignoreChecked=false}var c=this.getActionMgr().getAction("INTERN_AFTERCMD","Editor");c.data={cmd:b.cmd,modul:b.modul,response:a};c.executeCmd(this.proxy);this.modified=false}}}},watchTask:function(a){},onqueryState:function(b){var a;switch(b.itemId){case"SAVEALL":if(this.isDirty()){b.cancmd++}break;case"SAVE":if(this.getActionMgr().isActivePanel(this)){if(this.isDirty()){b.cancmd++}}break}},initEditor:function(){this.initBlocks();this.setTitle(Ext.util.Format.ellipsis(""+this.proxy.title,15));this.tabTip=this.proxy.title;if(this.getActionMgr().isActivePanel(this)){this._setPropertyPanel(this.main_property);this.getActionMgr().fireEvent("cmd",{actionGroup:"EDITOR2",itemId:"CURRENT_TAB_CHANGED",newTab:this})}},destroyBlocks:function(){AuIt.App.unsetPropertyPanel(this.activeProperty);var a=this.blocks;this.blocks=[];Ext.each(a,function(b){if(b.property){b.property.destroy()}},this);this.activeProperty=null;this.activeBlock=null;this.modified=false},initBlocks:function(){this.destroyBlocks();this.blocks=[];this.activeProperty=null;var b=this.pageData;var c=null;if(b.MAIN){var a=b.MAIN.factory;this.window_id=b.MAIN.window_id;if(AuIt.Editor.Property[a]){var d={};d.typ="MAIN";d.property=new AuIt.Editor.Property[a]({editor:this,pagedata:b.MAIN});this.main_property=d.property;this.blocks.push(d)}}if(b.SUB){Ext.each(b.SUB,function(e){var f=e.factory;if(AuIt.Editor.Property[f]&&e.blockIds){var g={};g.typ="SUB";g.frames=[];g.property=new AuIt.Editor.Property[f]({editor:this,pagedata:e});this.blocks.push(g)}},this)}},_setPropertyPanel:function(a,b){if(this.getActionMgr().isActivePanel(this)){if(!a){a=this.main_property}this.activeProperty=a;AuIt.App.setPropertyPanel(a,b)}},isDirty:function(){if(!this.modified){Ext.each(this.blocks,function(a){if(a.property){if(a.property.isDirty()){this.modified=true}}},this)}return this.modified},setLastFocus:function(){this.getActionMgr().postMessage({itemId:"SETLASTFOCUS",data:{value:true}},this.proxy)},setModified:function(a){if(a=="local_only"){this.modified=true;this.getActionMgr().updateState()}else{if(1||!this.modified){this.getActionMgr().postMessage({itemId:"SETMODIFIED",data:{value:true}},this.proxy)}}}});Ext.onReady(function(){var b=AuIt.Editor.utils;var a=new AuIt.Editor.Frame();a.m=AuIt["lo"+b.m];AuIt.App=a});