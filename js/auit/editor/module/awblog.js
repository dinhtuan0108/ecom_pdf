Ext.namespace("AuIt.Action.awblog");AuIt.Action.awblog.NEW={text:AuIt.locale.awblog_NEWM,p:1,tooltip:AuIt.locale.awblog_NEWT,disabled:false,executeCmd:function(){this.actionMgr.request(this,{directCall:true,url:AUIT_EDITOR.cms_page_url,waitMsg:true,params:{action:"awblog_newpost"},LoadSuccess:function(a,b){if(a.success){AuIt.App.openWindow("CMSPage_"+a.page_id,a.clickurl);AuIt.App.getActionMgr().fireEvent("cmd",{itemId:"PAGE_RELOAD",window_id:"CMSPage_"+a.page_id})}}})},scale:"small",iconCls:"cms-new-16",itemId:"AWBLOG_NEW"};AuIt.Action.Editor.BLOCK_BLOG={scale:"large",tooltip:"<b>AW Blog</b><br/>",p:1,rowspan:2,text:"&#160;&#160;AW Blog&#160;&#160;",itemId:"BLOCK_AWBLOG",disabled:false,iconAlign:"bottom",iconCls:"edit-blocks-32",initComponent:function(){var a=this.baseAction.actionMgr;this.menu=new Ext.menu.Menu({items:[a.getAction("NEW","awblog")]});this.constructor.superclass.initComponent.call(this)}};