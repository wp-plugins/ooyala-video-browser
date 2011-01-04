function ooyalavideo_insert() {
	if(window.tinyMCE) {
		var postnumber = document.getElementById('post_ID').value;		

		tinyMCE.activeEditor.windowManager.open( {
				url : tinyMCE.activeEditor.documentBaseURI + '../../../wp-content/plugins/ooyala-video-browser/ooyala-video-popup.php?post='+postnumber,
				width : 800,
				height : 600,
				resizable : 'no',
				scrollbars : 'no',
				inline : 'yes'
			}, { /* custom parameter space */ }
		);
		return true;
	} else {
		window.alert('This function is only available in the WYSIWYG editor');
		return true;
	}
}

function ev_insertVideoCode(vid, linktext) {
	var text = (linktext == '') ? ('[ooyala ' + vid + ']') : ('[ooyala ' + vid + ' ' + linktext + ']');
	if(window.tinyMCE) {
		var ed = tinyMCE.activeEditor;
		ed.execCommand('mceInsertContent', false, '<p>' + text + '</p>');
		ed.execCommand('mceCleanup');
	}
	return true;
}

function ev_checkData(formObj) {
	
	if (formObj.vid.value != '') ev_insertCode(formObj);
}

function ev_insertCode(formObj) {
	var vid = formObj.vid.value;
	var linktext = 'nolink';
	ev_insertVideoCode(vid, linktext);
	tinyMCEPopup.close();
}

function disable_enable(objCheckbox, objTextfield) {
	objTextfield.disabled = (objCheckbox.checked) ? true : false;
	objTextfield.value = '';
	objTextfield.style.backgroundColor = (objTextfield.disabled) ? '#ccc' : '#fff';
}

function dailymotion(objSelectBox, objTextfield, objCheckbox) {
	if (objSelectBox.value=='dailymotion' || objSelectBox.value=='garagetv') {
		objCheckbox.checked = true;
		objTextfield.disabled = true;
		objTextfield.style.backgroundColor = '#ccc';
		objTextfield.value = '';
	}
	objCheckbox.disabled = (objSelectBox.value=='dailymotion' || objSelectBox.value=='garagetv') ? true : false;
}

function init() {
	tinyMCEPopup.resizeToInnerSize();
}

//Added By Dave Searle
function getXMLHttp()
{
  var xmlHttp

  try
  {
    //Firefox, Opera 8.0+, Safari
    xmlHttp = new XMLHttpRequest();
  }
  catch(e)
  {
    //Internet Explorer
    try
    {
      xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch(e)
    {
      try
      {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch(e)
      {
        alert("Your browser does not support AJAX!")
        return false;
      }
    }
  }
  return xmlHttp;
}

function SearchRequest(searchTerm,pageId)
{
  var xmlHttp = getXMLHttp();
  xmlHttp.onreadystatechange = function()
  {
    if(xmlHttp.readyState == 4)
    {
      HandleResponse(xmlHttp.responseText);
    }
  }
  var call;
  if (pageId !=null) {
  call = "../ooyala-video-browser/ooyalaajax.php?do=search&key_word="+searchTerm+"\&pageid="+pageId;}else{call = "../ooyala-video-browser/ooyalaajax.php?do=search&key_word="+searchTerm;}
  xmlHttp.open("GET", call, true); 
  xmlHttp.send(null);
}


function MakeRequest(requestType,pageId)
{
  var xmlHttp = getXMLHttp();
  xmlHttp.onreadystatechange = function()
  {
    if(xmlHttp.readyState == 4)
    {
      HandleResponse(xmlHttp.responseText);
    }
  }
  var call;

	  if (pageId !=null) {
  call = "../ooyala-video-browser/ooyalaajax.php?do="+requestType+"\&pageid="+pageId;}else{call = "../ooyala-video-browser/ooyalaajax.php?do="+requestType;}
 
  xmlHttp.open("GET", call, true); 
  xmlHttp.send(null);
}

function HandleResponse(response)
{
  document.getElementById('ResponseDiv').innerHTML = response;
}
//End of AJAX Functions*/

/*gallery functions */

function ChooseVideo(Arg1){
document.getElementById('portal_vid').value = Arg1;
}
