Ext.namespace("AuIt.Action.cms");AuIt.Action.cms.RELOAD={tooltip:AuIt.locale.CMS_RELOAD,disabled:false,executeCmd:function(){AuIt.App.getActionMgr().fireEvent("cmd",{itemId:"PAGE_RELOAD",window_id:"CMSPage_"})},scale:"small",iconCls:"cms-reload-16",itemId:"CMS_RELOAD"};AuIt.Action.cms.NEW={text:AuIt.locale.CMS_NEW,disabled:false,executeCmd:function(){this.actionMgr.request(this,{directCall:true,url:AUIT_EDITOR.cms_page_url,waitMsg:true,params:{action:"cms_newpage"},LoadSuccess:function(a,b){if(a.success){AuIt.App.openWindow("CMSPage_"+a.page_id,a.clickurl);AuIt.App.getActionMgr().fireEvent("cmd",{itemId:"PAGE_RELOAD",window_id:"CMSPage_"+a.page_id})}}})},scale:"small",iconCls:"cms-new-16",itemId:"CMS_NEW"};AuIt.Action.cms.DUP={text:AuIt.locale.CMS_DUP,disabled:true,executeCmd:function(){if(this.curpageId){this.actionMgr.request(this,{url:AUIT_EDITOR.cms_page_url,directCall:true,waitMsg:true,params:{action:"cms_duppage",page_id:this.curpageId},LoadSuccess:function(a,b){if(a.success){AuIt.App.openWindow("CMSPage_"+a.page_id,a.clickurl);AuIt.App.getActionMgr().fireEvent("cmd",{itemId:"PAGE_RELOAD",window_id:"CMSPage_"+a.page_id})}}})}},scale:"small",iconCls:"cms-dup-16",itemId:"CMS_DUP",updateState:function(a){if(a.source!="CMSTREE"){return}this.curpageId=0;if(a.node){this.curpageId=a.node.attributes.page_id}this.setDisabled(!a.node)}};AuIt.Action.cms.DEL={text:AuIt.locale.CMS_DEL,disabled:true,executeCmd:function(){if(this.curpageId){Ext.MessageBox.show({title:AuIt.locale.CMS_DEL_DT,msg:AuIt.locale.CMS_DEL_M,buttons:Ext.MessageBox.YESNO,fn:function(a){if(a=="yes"){this.actionMgr.request(this,{url:AUIT_EDITOR.cms_page_url,directCall:true,waitMsg:true,params:{action:"cms_delpage",page_id:this.curpageId},LoadSuccess:function(b,c){if(b.success){AuIt.App.closeWindow("CMSPage_"+b.page_id);AuIt.App.getActionMgr().fireEvent("cmd",{itemId:"PAGE_REMOVE",window_id:"CMSPage_"+b.page_id})}}})}},scope:this,icon:Ext.MessageBox.QUESTION})}},scale:"small",iconCls:"cms-del-16",itemId:"CMS_DEL",updateState:function(a){if(a.source!="CMSTREE"){return}this.curpageId=0;if(a.node){this.curpageId=a.node.attributes.page_id}this.setDisabled(!a.node)}};AuIt.Action.cms.SUB1={text:"CMS",itemId:"SUB1_CMS",disabled:false,iconCls:"page-cms-page-16",initComponent:function(){var a=this.baseAction.actionMgr;this.menu=new Ext.menu.Menu({items:[a.getAction("NEW","cms"),a.getAction("DUP","cms"),"-",a.getAction("DEL","cms")]});this.constructor.superclass.initComponent.call(this)}};Ext.namespace("AuIt.Action.ToolBar.Pages");AuIt.Action.ToolBar.Pages.CMS=function(a){return[a.getAction("RELOAD","cms"),"-",a.getAction("NEW","cms"),a.getAction("DUP","cms"),"->",a.getAction("DEL","cms")]};Ext.namespace("AuIt.Action.ToolBar.Short.CMS");AuIt.Action.ToolBar.Short.CMS=function(a){return[a.getAction("RELOAD","cms")]};Ext.namespace("AuIt.Action.ToolBar.Short2.CMS");AuIt.Action.ToolBar.Short2.CMS=function(a){return[a.getAction("RELOAD","cms"),a.getAction("SUB1","cms")]};