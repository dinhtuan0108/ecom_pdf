Ext.ns("AuIt");AuIt.UploadMgr=function(){var c,b;function a(n){alert(n)}function g(){Ext.MessageBox.show({title:AuIt.locale.UploadMgr_title,msg:AuIt.locale.UploadMgr_msg,progressText:AuIt.locale.UploadMgr_progressText,width:300,progress:true,closable:false})}function i(n,p){try{var q=b.getNodeId();c.setPostParams({form_key:FORM_KEY,node:q,action:"upload-files"});if(q&&n>0){g();this.startUpload()}}catch(o){a(o)}}function j(n){}function k(n,p,o){}function d(o){try{Ext.MessageBox.updateText(""+o.name+" ...")}catch(n){}return true}function e(o,r,q){try{var p=Math.ceil((r/q)*100);Ext.MessageBox.updateProgress(p,p+"% "+AuIt.locale.UploadMgr_completed)}catch(n){a(n)}}function h(o,n){}function m(o,q,p){try{Ext.MessageBox.hide();b.uploadFinish();a(p)}catch(n){a(n)}}function f(n){if(this.getStats().files_queued===0){Ext.MessageBox.hide();b.uploadFinish()}else{this.startUpload()}}function l(n){}SWFUpload.onload=function(){var n={flash_url:AUIT_EDITOR.js_url+"auit/editor/swfupload/swfupload.swf",flash9_url:AUIT_EDITOR.js_url+"auit/editor/swfupload/swfupload_fp9.swf",upload_url:AUIT_EDITOR.upload_url,file_size_limit:"100 MB",file_post_name:AUIT_EDITOR.file_field,file_types:AUIT_EDITOR.filters.images.files,file_types_description:AUIT_EDITOR.filters.images.label,post_params:{form_key:FORM_KEY,node:"",action:"upload-files"},debug:false,button_placeholder_id:"AUIT_UPLOAD_PLACEHOLDER",button_width:16,button_height:16,button_window_mode:SWFUpload.WINDOW_MODE.TRANSPARENT,button_cursor:SWFUpload.CURSOR.HAND,file_dialog_complete_handler:i,file_queued_handler:j,file_queue_error_handler:k,upload_start_handler:d,upload_progress_handler:e,upload_error_handler:m,upload_success_handler:h,upload_complete_handler:f,queue_complete_handler:l};c=new SWFUpload(n)};return{getSwf:function(){return c},removeAction:function(n){var o=Ext.get("AUIT_UPLOAD_WRAP");var p=Ext.get(c.movieName);o.appendChild(p);b=null},addAction:function(n){b=n;n.each(function(p){var o=p.el.child("em");o.setStyle({position:"relative",display:"block"});var q=Ext.get(c.movieName);q.setStyle({position:"absolute",top:0,left:0,width:p.getWidth(),height:p.getHeight()});o.appendChild(q);return false})}}}();