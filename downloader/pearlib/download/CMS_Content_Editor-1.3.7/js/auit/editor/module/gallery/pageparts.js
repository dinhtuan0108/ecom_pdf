Ext.namespace("AuIt.Action.ToolBar.PageParts","AuIt.Editor.Gallery","AuIt.Action.PageParts");AuIt.Action.PageParts.ADD={actionGroup:"EDITOR",disabled:true,tooltip:AuIt.locale.PageParts_ADD,scale:"small",iconCls:"elm-partpart-add-16",itemId:"PAGEPARTS_ADD",updateState:function(a){this.setDisabled((!a.opt||!a.opt.isEditable))}};AuIt.Action.PageParts.ADDFROM={itemId:"PAGEPARTS_ADDFROM"};AuIt.Action.PageParts.SET={actionGroup:"EDITOR",itemId:"PAGEPARTS_SET"};AuIt.Action.PageParts.CREATETEMPLATE={itemId:"PAGEPARTS_CREATETEMPLATE"};AuIt.Action.PageParts.SETPAGEPART={disabled:true,tooltip:AuIt.locale.PageParts_SETPAGEPART,scale:"small",iconCls:"elm-partpart-set-16",styledisabled:true,editdisabled:true,itemId:"SETPAGEPARTFROMLIST",updateState:function(b){var a=true;if(b.source=="Gallery.PageParts"){this.styledisabled=(b.selections.length==0)}else{if(b.opt){this.editdisabled=!b.opt.isEditable}}this.setDisabled(this.editdisabled||this.styledisabled)}};AuIt.Action.PageParts.EDIT={tooltip:AuIt.locale.PageParts_EDIT,scale:"small",iconCls:"elm-partpart-edit-16",itemId:"PAGEPARTS_EDIT",disabled:true,updateState:function(a){if(a.source=="Gallery.PageParts"){this.setDisabled(a.selections.length==0)}}};AuIt.Action.PageParts.DEL={tooltip:AuIt.locale.PageParts_DEL,scale:"small",iconCls:"elm-partpart-del-16",itemId:"PAGEPARTS_DEL",disabled:true,updateState:function(a){if(a.source=="Gallery.PageParts"){this.setDisabled(a.selections.length==0)}}};AuIt.Action.ToolBar.PageParts.MAIN=function(a){return new Ext.Toolbar({items:[a.getAction("ADD","PageParts"),a.getAction("EDIT","PageParts"),"-",a.getAction("DEL","PageParts"),"->",a.getAction("SETPAGEPART","PageParts")]})};AuIt.Editor.Gallery.PageParts=Ext.extend(AuIt.Editor.Gallery.Container,{id:"AuIt.Editor.Gallery.PageParts",altKey:"P",title:AuIt.locale.Gallery_PagePartsT,tabTip:"Page Parts",iconCls:"tab-pageparts-16",getToolbar:function(){return AuIt.App.getActionMgr().getToolBar("MAIN","PageParts")},putPageParts:function(a){this.store.loadData(this.getDataSource());AuIt.App.getActionMgr().request(a,{directCall:true,url:AUIT_EDITOR.web_cmd_url,waitMsg:false,params:{action:"save_html_template",data:AUIT_EDITOR.templates},LoadSuccess:function(b,c){}})},onDblClick:function(a){this.setPagePart(a)},getDataSource:function(){return AuIt.Editor.Gallery.PageParts.superclass.getDataSource.call(this,AUIT_EDITOR.templates)},setPagePart:function(b){var d=AuIt.App.getActionMgr().getAction("SET","PageParts");if(d.actionMgr.fireEvent("beforecmd",d)&&d.editor){var c=function(f){var e=AuIt.App.getActionMgr().getAction("CMD_INSERTHTML","Editor");e.value=f;e.executeCmd()};if(b.type!=1){c(b.template)}else{if(d.editor&&d.editor.getActiveBlockPageData()){var a=d.editor.getActiveBlockPageData();AuIt.App.getActionMgr().request({itemId:"TRANSLATE_BLOCK_TO_HTML"},{directCall:true,url:AUIT_EDITOR.web_cmd_url,waitMsg:AuIt.locale.Gallery_PageParts_WAIT,waitWnd:this.el,params:{action:"translate_block_to_html",page_id:a.page_id,factory:a.factory,storeId:a.storeId,data:b.template},LoadSuccess:function(e,f){if(e.data&&e.data.xhtml){c(e.data.xhtml)}}})}}}},onCmd:function(d){switch(d.itemId){case"SETPAGEPARTFROMLIST":var a=this.getSelectedRecord();if(a){this.setPagePart(a)}break;case"PAGEPARTS_ADDFROM":var g=new AuIt.Editor.dlg.pagePartEditor({cfg:d.value});g.show();break;case"PAGEPARTS_ADD":var f=AuIt.App.getActiveEditor();if(!f){return}var d=AuIt.App.getActionMgr().getAction("INTERN_PAGEPART_ADD","Editor");d.data={};d.quest(f.proxy,function(j){var k={itemId:"TRANSLATE_HTML_TO_LOCAL",_data:j.data};AuIt.App.getActionMgr().request(k,{directCall:true,url:AUIT_EDITOR.web_cmd_url,waitMsg:AuIt.locale.Gallery_PageParts_WAIT,waitWnd:this.el,params:{action:"translate_html_to_local",data:k._data},LoadSuccess:function(l,m){if(l.data){var n=new AuIt.Editor.dlg.pagePartEditor({cfg:{template:AuIt.Editor.utils.formatHTMLFragment(l.data.content)}});n.show()}}})},this);break;case"PAGEPARTS_EDIT":var a=this.getSelectedRecord();if(a){var g=new AuIt.Editor.dlg.pagePartEditor({cfg:a});g.show()}break;case"PAGEPARTS_CREATETEMPLATE":var c,e=AUIT_EDITOR.templates,h=[];Ext.applyIf(d.cfg,{menu:"",name:"",image:"",description:"",template:""});var i=null;if(d.cfg.key&&e[d.cfg.key]){i=d.cfg.key}else{for(c in e){if(e.hasOwnProperty(c)){if(e[c].name==d.cfg.name&&e[c].menu==d.cfg.menu){i=c;break}}}}if(!i){i=(new Date()).getTime()}d.cfg.type=0;if(d.cfg.template.indexOf("{{")>=0){d.cfg.type=1}if(Ext.isArray(AUIT_EDITOR.templates)){var b={};for(c in AUIT_EDITOR.templates){if(e.hasOwnProperty(c)){b[c]=AUIT_EDITOR.templates[c]}}AUIT_EDITOR.templates=b}AUIT_EDITOR.templates[i]=d.cfg;this.putPageParts(d);break;case"PAGEPARTS_DEL":var a=this.getSelectedRecord();if(a){Ext.MessageBox.show({title:AuIt.locale.Gallery_PageParts_DT,msg:AuIt.locale.Gallery_PageParts_DM,buttons:Ext.MessageBox.YESNO,fn:function(l){if(l=="yes"){var k=null;var j,n=AUIT_EDITOR.templates;for(j in n){if(n.hasOwnProperty(j)){if(n[j].name==a.name&&n[j].menu==a.menu){k=j;break}}}if(k){n[k]=null;delete n[k]}this.putPageParts(d)}},scope:this,icon:Ext.MessageBox.QUESTION})}break}},onSelectionChange:function(a){AuIt.App.getActionMgr().updateState({source:"Gallery.PageParts",selections:a})}});