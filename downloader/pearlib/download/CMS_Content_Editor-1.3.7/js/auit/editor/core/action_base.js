Ext.namespace("AuIt.Action");Ext.namespace("AuIt.Action.iframe");AuIt.Action.iframe.CMD_IFRAMEMSG={itemId:"CMD_IFRAMEMSG"};AuIt.Action.Base=function(a){a.tabIndex=-1;Ext.apply(this,a);if(this.handler&&!a.handler){a.handler=this.handler;a.scope=this}if(this.tooltip&&this["p"]&&AuIt.App.l!=1){a.tooltip=this.tooltip+="<br/>"+AuIt.locale.p}AuIt.Action.Base.superclass.constructor.call(this,a)};Ext.extend(AuIt.Action.Base,Ext.Action,{isPressed:function(){if(this.items&&this.items[0]){return this.items[0].pressed}return false},updateState:function(a){if(this["x"]()){this.setDisabled(true)}},addComponent:function(a){AuIt.Action.Base.superclass.addComponent.call(this,a);if(!a.baseAction){a.baseAction=this}},handler:function(){var a=this.isAction?this:this.baseAction;if(a&&a.executeCmd){a.executeCmd()}},beforeCmd:function(){},afterCmd:function(){},x:function(a){if(a||!this["p"]){return a}return AuIt.App.l!=1},executeCmd:function(){var a=this;if(a.actionMgr.fireEvent("beforecmd",a)){a.beforeCmd();a.actionMgr.fireEvent("cmd",this);a.afterCmd();a.actionMgr.fireEvent("aftercmd",a,{});a.actionMgr.updateState()}},postMessage:function(a){this.actionMgr.postMessage(this,a)},answer:function(){this.actionMgr.answer(this)}});AuIt.ActionMgr=function(a){Ext.apply(this,a);this.pendingCMDS={};this.pendingIDS=1;this.actions={};this.addEvents("queryState","beforecmd","beforerequest","cmd","aftercmd","watchtask","itemchanged");this.currentPanel=null;AuIt.ActionMgr.superclass.constructor.call(this);this.startWatchTask();window.addEventListener("message",this.receiveMessage.createDelegate(this),false)};Ext.extend(AuIt.ActionMgr,Ext.util.Observable,{quest:function(e,b,f,c){if(!b){b=AuIt.App.getActiveEditor().proxy}if(!b){return}var a=this.pendingIDS++;e.POSTID=a;var d={pID:a,action:e,callback:f,scope:c};this.pendingCMDS[a]=d;this.postMessage(e,b)},answer:function(a){this.postMessage(a)},receiveMessage:function(d){if(this.isProxy){var e=Ext.decode(d.data);if(e.itemId=="SETMODIFIED"){this.editor.setModified()}else{if(e.itemId=="SETLASTFOCUS"){this.editor.setLastFocus()}else{if(e.itemId){var c=this.getAction(e.cmd,e.modul);c.POSTID=e.POSTID;c.data=e.data;c.iframeUID=e.iframeUID;c.event=d;c.executeCmd()}}}}else{var e=Ext.decode(d.data);if(e.itemId){if(e.itemId=="SHOWMESSAGE"){this.showMessage(e.data.localmsg)}else{if(e.itemId=="CMD_IFRAMEMSG"&&e.data.itemId=="EDITFRAME_UPDATESTATES"){this.updateState(e.data);if(e.data.KeyCode){AuIt.App.handleKeyCode(e.data.KeyCode)}if(e.data.triggerAttribute){var a=AuIt.App.showKeyPanel(e.data.triggerAttribute.panel);if(a&&a.triggerAtribute){a.triggerAtribute(e.data.triggerAttribute.attr)}}}else{var c=this.getAction(e.cmd,e.modul);c.data=e.data;c.iframeUID=e.iframeUID;c.event=d;if(e.POSTID){var b=this.pendingCMDS[e.POSTID];if(b){this.pendingCMDS[e.POSTID]=null;e.POSTID=0;if(b.callback){b.callback.call(b.scope,c,b)}}}else{c.executeCmd()}}}}}},postMessage:function(d,c){var b={cmd:d.cmd||"",modul:d.modul||"",itemId:d.itemId,sender:document.domain,iframeUID:c?c.iframeUID:this.isProxy?this.editor.iframeUID:null,POSTID:d.POSTID?d.POSTID:0,data:d.data};b=Ext.encode(b);if(this.isProxy){parent.parent.postMessage(b,this.proxy_origin)}else{c.source.postMessage(b,c.origin)}},showMessage:function(c){if(this.isProxy){var b={itemId:"SHOWMESSAGE",data:{localmsg:c}};this.postMessage(b)}else{AuIt.Editor.Dialog.Message(c)}},getAction:function(c,a){a=a||this.modul;var b=a+"_"+c;if(!this.actions[b]){baseClass=AuIt.Action[a][c].baseClass||AuIt.Action.Base;this.actions[b]=new baseClass(AuIt.Action[a][c]);this.actions[b].actionMgr=this;this.actions[b].cmd=c;this.actions[b].modul=a}return this.actions[b]},getToolBar:function(b,a){a=a||this.modul;return AuIt.Action.ToolBar[a][b](this)},getEditStates:function(){var d={useQueryState:1};var c={};for(var b in this.actions){if(this.actions[b].updateState){var e=this.actions[b];if(e.actionGroup=="EDITOR"){var b={itemId:e.itemId,enableToggle:e.enableToggle,value:e.value,actionGroup:e.actionGroup};c[e.itemId]=b}}}return c},updateState:function(c){if(this.isProxy){this.editor.sendEditStates(c)}else{c=c||{};for(var b in this.actions){if(this.actions[b].updateState){this.actions[b].updateState(c)}}this.fireEvent("itemchanged",c)}},setMainPanel:function(a){var b=this.currentPanel;this.currentPanel=a;this.fireEvent("cmd",{actionGroup:"EDITOR2",itemId:"MAIN_TAB_CHANGE",oldTab:b,newTab:a});this.updateState()},isActivePanel:function(a){return this.currentPanel==a},getMainPanel:function(){return this.currentPanel},watchInfo:{Timer:null},startWatchTask:function(){if(!this.watchInfo.Timer){this.watchInfo.Timer=setTimeout(this.watchTask.createDelegate(this),150)}},stopWatchTask:function(a){if(this.watchInfo.Timer){clearTimeout(this.watchInfo.Timer);this.watchInfo.Timer=null}},watchTask:function(){this.fireEvent("watchtask",this);this.watchInfo.Timer=null;this.startWatchTask()},showWaitBox:function(a){Ext.MessageBox.show({msg:a===true?AuIt.locale.txt1:a,progressText:AuIt.locale.txt1,width:300,waitConfig:{interval:200},wait:true});Ext.MessageBox.getDialog().toFront()},hideWaitBox:function(){Ext.MessageBox.hide()},request:function(b,a){if(this.fireEvent("beforecmd",b)){if(!a.directCall){this.fireEvent("beforerequest",b)}a.params=a.params||{};if(a.params.data){a.params.data=Ext.encode(a.params.data)}a.myMask=false;if(a.waitMsg){a.myMask=true;this.showWaitBox(a.waitMsg)}if(!a.directCall){this.stopWatchTask()}a.action=b;a.success=this.LoadSuccess;a.failure=this.LoadFailed;a.scope=this;a.params.form_key=FORM_KEY;Ext.Ajax.request(a)}},showResonse:function(a){if(a.errors||a.messages){var c,e,f="";var d=Ext.MessageBox.INFO;if(a.messages){var b={error:{icon:Ext.MessageBox.ERROR},warning:{icon:Ext.MessageBox.ERROR},notice:{icon:Ext.MessageBox.ERROR},success:{icon:Ext.MessageBox.ERROR}};for(e in b){if(a.messages[e]){for(c=0;c<a.messages[e].length;c++){if(!d){d=b[e].icon}f+=a.messages[e][c]+"<br/>"}}}}if(f){Ext.Msg.show({title:a.title||AuIt.locale.Message,msg:f,buttons:Ext.MessageBox.OK,icon:d,width:300})}}},LoadSuccess:function(a,b){if(b.myMask){b.myMask=false;this.hideWaitBox()}try{if(!b.directCall){this.startWatchTask()}a=Ext.decode(a.responseText);this.showResonse(a);if(!b.LoadSuccess||b.LoadSuccess.call(b.action,a,b)!==false){this.fireEvent("aftercmd",b.action,a);if(!b.directCall){this.updateState()}}}catch(c){}},LoadFailed:function(a,b){if(b.myMask){b.myMask=false;this.hideWaitBox()}this.startWatchTask();alert("Failed:"+a.responseText)}});