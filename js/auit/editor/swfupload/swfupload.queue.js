var SWFUpload;if(typeof(SWFUpload)==="function"){SWFUpload.queue={};SWFUpload.prototype.initSettings=(function(a){return function(b){if(typeof(a)==="function"){a.call(this,b)}this.queueSettings={};this.queueSettings.queue_cancelled_flag=false;this.queueSettings.queue_upload_count=0;this.queueSettings.user_upload_complete_handler=this.settings.upload_complete_handler;this.queueSettings.user_upload_start_handler=this.settings.upload_start_handler;this.settings.upload_complete_handler=SWFUpload.queue.uploadCompleteHandler;this.settings.upload_start_handler=SWFUpload.queue.uploadStartHandler;this.settings.queue_complete_handler=b.queue_complete_handler||null}})(SWFUpload.prototype.initSettings);SWFUpload.prototype.startUpload=function(a){this.queueSettings.queue_cancelled_flag=false;this.callFlash("StartUpload",[a])};SWFUpload.prototype.cancelQueue=function(){this.queueSettings.queue_cancelled_flag=true;this.stopUpload();var a=this.getStats();while(a.files_queued>0){this.cancelUpload();a=this.getStats()}};SWFUpload.queue.uploadStartHandler=function(a){var b;if(typeof(this.queueSettings.user_upload_start_handler)==="function"){b=this.queueSettings.user_upload_start_handler.call(this,a)}b=(b===false)?false:true;this.queueSettings.queue_cancelled_flag=!b;return b};SWFUpload.queue.uploadCompleteHandler=function(b){var c=this.queueSettings.user_upload_complete_handler;var d;if(b.filestatus===SWFUpload.FILE_STATUS.COMPLETE){this.queueSettings.queue_upload_count++}if(typeof(c)==="function"){d=(c.call(this,b)===false)?false:true}else{if(b.filestatus===SWFUpload.FILE_STATUS.QUEUED){d=false}else{d=true}}if(d){var a=this.getStats();if(a.files_queued>0&&this.queueSettings.queue_cancelled_flag===false){this.startUpload()}else{if(this.queueSettings.queue_cancelled_flag===false){this.queueEvent("queue_complete_handler",[this.queueSettings.queue_upload_count]);this.queueSettings.queue_upload_count=0}else{this.queueSettings.queue_cancelled_flag=false;this.queueSettings.queue_upload_count=0}}}}};