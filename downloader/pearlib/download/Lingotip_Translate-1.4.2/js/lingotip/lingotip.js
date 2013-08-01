/*
JSdigit--||---/9/4/---||||---/6/0/0/---||
 */
// FCKeditor Class
 
var LingotipButton = function( instanceName, width, height, toolbarSet, value )
{
 
	// Properties
	this.InstanceName	= instanceName ;
}

LingotipButton.prototype.Version		= '2.6.4.1' ;
LingotipButton.prototype.VersionBuild	= '23187' ;
 

LingotipButton.prototype.CreateButton = function()
{
	if ( document.getElementById( this.InstanceName + '___Frame' ) )
		return ;

		// We must check the elements firstly using the Id and then the name.
		var idTextarea = document.getElementById( this.InstanceName ) ;
		var textAreaByName = document.getElementsByName( this.InstanceName ) ;
		var i = 0;
		while ( idTextarea || i == 0 )
		{
			if ( idTextarea && idTextarea.tagName.toLowerCase() == 'textarea' )
				break ;
			idTextarea = textAreaByName[i++] ;
		}

		if ( !idTextarea )
		{
			alert( 'Error: The TEXTAREA with id or name set to "' + this.InstanceName + '" was not found' ) ;
			return ;
		}

		//oTextarea.style.display = 'none' ;
 
 		this._InsertHtmlBefore( this._GetConfigHtml(idTextarea.id), idTextarea ) ;
		//this._InsertHtmlBefore( this._GetIFrameHtml(), oTextarea ) ;
	 
}

LingotipButton.prototype._InsertHtmlBefore = function( html, element )
{
	if ( element.insertAdjacentHTML )	// IE
		element.insertAdjacentHTML( 'beforeBegin', html ) ;
	else								// Gecko
	{
 		var oRange = document.createRange() ;
		oRange.setStartBefore( element ) ;
		var oFragment = oRange.createContextualFragment( html );
		element.parentNode.insertBefore( oFragment, element ) ;
	}
}

LingotipButton.prototype._GetConfigHtml = function(id)
{ 
	var checkboxVal = id+'_default';
 if(document.getElementById(checkboxVal))
 {
	if(document.getElementById(checkboxVal).checked == true){
		var html = '';
		//html += '<input style="margin-bottom:5px;" class="form-button" type="button" id="'+'lingotip_'+this.InstanceName+'" name="'+'lingotip_'+this.InstanceName+'" value="Find Translated Text" onclick="translateText('+"'"+id+"'"+')" />' ;
		html += '<button class="scalable disabled" disabled="disabled" id="'+'mage_'+this.InstanceName+'" name="'+'mage_'+this.InstanceName+'" onclick="nonTranslateText('+"'"+id+"'"+')" type="button"  value="Select Translated Text" style="margin-bottom:5px;margin-left:5px;margin-top:5px;"><span>Request a Translation</span></button>';
		html += '<button class="scalable disabled" disabled="disabled" id="'+'lingotip_'+this.InstanceName+'" name="'+'lingotip_'+this.InstanceName+'" onclick="translateText('+"'"+id+"'"+')" type="button"  value="Find Translated Text" style="margin-bottom:5px;margin-left:5px;margin-top:5px;"><span>Find Translated Text</span></button>';
		return html ;
	}
}
	<!--JS Code   ---- ||/7/4/|| ->
 
 	var html = '';
	//html += '<input style="margin-bottom:5px;" class="form-button" type="button" id="'+'lingotip_'+this.InstanceName+'" name="'+'lingotip_'+this.InstanceName+'" value="Find Translated Text" onclick="translateText('+"'"+id+"'"+')" />' ;
	html += '<button class="scalable" id="'+'mage_'+this.InstanceName+'" name="'+'mage_'+this.InstanceName+'" onclick="nonTranslateText('+"'"+id+"'"+')" type="button"  value="Select Translated Text" style="margin-bottom:5px;margin-left:5px;margin-top:5px;"><span>Request a Translation</span></button>';
	html += '<button class="scalable" id="'+'lingotip_'+this.InstanceName+'" name="'+'lingotip_'+this.InstanceName+'" onclick="translateText('+"'"+id+"'"+')" type="button"  value="Find Translated Text" style="margin-bottom:5px;margin-left:5px;margin-top:5px;"><span>Find Translated Text</span></button>';
	return html ;
}
 

 

;(function()
{
	var textareaToEditor = function( textarea )
	{
		var editor = new LingotipButton( textarea.name ) ;

		editor.Width = Math.max( textarea.offsetWidth, LingotipButton.MinWidth ) ;
		editor.Height = Math.max( textarea.offsetHeight, LingotipButton.MinHeight ) ;
 
		return editor ;
	}
 
 	<!--JS Code   ---- ||/1/1/3/0/0/0/0/|| ->
 
	LingotipButton.ReplaceAllTextareas = function()
	{
		var textareas = document.getElementsByTagName( 'textarea' ) ;

		for ( var i = 0 ; i < textareas.length ; i++ )
		{
			var editor = null ;
			var textarea = textareas[i] ;
			var name = textarea.name ;

			// The "name" attribute must exist.
			if ( !name || name.length == 0 )
				continue ;

			if ( typeof arguments[0] == 'string' )
			{
				// The textarea class name could be passed as the function
				// parameter.

				var classRegex = new RegExp( '(?:^| )' + arguments[0] + '(?:$| )' ) ;

				if ( !classRegex.test( textarea.className ) )
					continue ;
			}
			else if ( typeof arguments[0] == 'function' )
			{
				// An assertion function could be passed as the function parameter.
				// It must explicitly return "false" to ignore a specific <textarea>.
				editor = textareaToEditor( textarea ) ;
				if ( arguments[0]( textarea, editor ) === false )
					continue ;
			}

			if ( !editor )
				editor = textareaToEditor( textarea ) ;

			editor.CreateButton() ;
		}
	}
})() ;

