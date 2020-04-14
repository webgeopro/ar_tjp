var type = "IE"; // Variable used to hold the browser name

detectBrowser();

function detectBrowser() {
  if (window.opera && document.readyState) {
    type="OP"; // The surfer is using Opera of some version
  } else if (document.all) {
    type="IE"; // The surfer is using IE 4+
  } else if (document.layers) {
    type="NN"; // The surfer is using NS 4
  } else if (!document.all && document.getElementById) {
    type="MO"; // The surfer is using NS6+ or Firefox
  } else {
    type="IE"; // I assume it will not get here
  }
}

/*****************************************************************************/

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_showHideLayers() { //v6.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; }
}

/*****************************************************************************/

function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}

function preloadImages(images) {
    if (document.images) {
        var i, imageArray = images.split(','), imageObj = new Array();
        for(i=0; i<imageArray.length; i++) {
			imageObj[i] = new Image();
            imageObj[i].src=imageArray[i];
        }
    }
}

var exifVisible = false;
var sidebarVisible = true;

function setLayer(obj,lyr) {
  var newX = findPosX(obj)+5;
  var newY = findPosY(obj)+5;
  var x = new getObj(lyr);
  x.style.top = newY + 'px';
  x.style.left = newX + 'px';
}

function findPosX(obj) {
  var curleft = 0;

  if (!obj) { return curleft; }
  if (obj.offsetParent) {
    while (obj.offsetParent) {
      curleft += obj.offsetLeft
      obj = obj.offsetParent;
    }
  } else if (obj.x) {
    curleft += obj.x;
  }
  return curleft;
}

function findPosY(obj) {
  var curtop = 0;
  var printstring = '';

  if (!obj) { return curtop; }
  if (obj.offsetParent) {
    while (obj.offsetParent) {
      printstring += ' element ' + obj.tagName + ' has ' + obj.offsetTop;
      curtop += obj.offsetTop
      obj = obj.offsetParent;
    }
  } else if (obj.y) {
    curtop += obj.y;
  }
  window.status = printstring;
  return curtop;
}

function getObj(id) {
  if (type=="IE") {
    this.obj = document.all[id];
    this.style = document.all[id].style;
  } else if (type=="MO" || type=="OP") {
    this.obj = document.getElementById(id);
    this.style = document.getElementById(id).style;
  } else if (type=="NN") {
    if (document.layers[id]) {
      this.obj = document.layers[id];
      this.style = document.layers[id];
    }
  }
}

function toggleExif(parentId, id) {
  var parent = document.getElementById(parentId);

  MM_showHideLayers(id,'',(exifVisible) ? 'hide' : 'show');
  setLayer(parent,id);

  exifVisible = !exifVisible;
}

function toggleSidebar(parentId, id) {
  var parent = document.getElementById(parentId);

  MM_showHideLayers(id,'',(sidebarVisible) ? 'hide' : 'show');
//  setLayer(parent,id);
 
  sidebarVisible = !sidebarVisible;
}

/***************floating menu**************/

var cutoutListSrc = [
"20 piece Classic",	"gra",
"50 piece Classic",	"gra",
"100 piece Classic","gra",
"150 piece Classic","gra",
"200 piece Classic","gra",
"250 piece Classic","gra",
"300 piece Classic","ra",
"400 piece Classic","ra",
"500 piece Classic","ra",
"20 piece Elegant",	"gra",
"50 piece Elegant",	"gra",
"100 piece Elegant","gra",
"150 piece Elegant","gra",
"200 piece Elegant","gra",
"250 piece Elegant","gra",
"300 piece Elegant","ra",
"400 piece Elegant","ra",
"500 piece Elegant","ra",
"20 piece Mosaic",	"gra",
"50 piece Mosaic",	"gra",
"100 piece Mosaic",	"gra",
"150 piece Mosaic",	"gra",
"200 piece Mosaic",	"gra",
"250 piece Mosaic",	"gra",
"300 piece Mosaic",	"ra",
"400 piece Mosaic",	"ra",
"500 piece Mosaic",	"ra",
"20 piece Square",	"gra",
"50 piece Square",	"gra",
"100 piece Square",	"gra",
"150 piece Square",	"gra",
"200 piece Square",	"gra",
"250 piece Square",	"gra",
"300 piece Square",	"ra",
"400 piece Square",	"ra",
"500 piece Square",	"ra",
"20 piece Spiral",	"gran",
"50 piece Spiral",	"gran",
"100 piece Spiral",	"gran",
"150 piece Spiral",	"gran",
"200 piece Spiral",	"gran",
"250 piece Spiral",	"gran"];
var cutoutList = new Array();
for(var i = 0; i < cutoutListSrc.length/2; i++){
	cutoutList[i]=new Array(2);
	cutoutList[i][0]=cutoutListSrc[i*2];
	cutoutList[i][1]=cutoutListSrc[i*2+1];
}
var cutoutMenu=parseMenuSrcArray(cutoutList);

/*function findPosition(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	}
	return [curleft,curtop];
}*/
function findPosition( oElement ) {
  if( typeof( oElement.offsetParent ) != 'undefined' ) {
    for( var posX = 0, posY = 0; oElement; oElement = oElement.offsetParent ) {
      posX += oElement.offsetLeft;
      posY += oElement.offsetTop;
    }
    return [ posX, posY ];
  } else {
    return [ oElement.x, oElement.y ];
  }
}

function getScrollOffset() {
  var scrOfX = 0, scrOfY = 0;
  if( typeof( window.pageYOffset ) == 'number' ) {
    //Netscape compliant
    scrOfY = window.pageYOffset;
    scrOfX = window.pageXOffset;
  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
    //DOM compliant
    scrOfY = document.body.scrollTop;
    scrOfX = document.body.scrollLeft;
  } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
    //IE6 standards compliant mode
    scrOfY = document.documentElement.scrollTop;
    scrOfX = document.documentElement.scrollLeft;
  }
  return [ scrOfX, scrOfY ];
}

function getWindowSize() {
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;
  }
  return [myWidth,myHeight];
}

function getElementsByClassName(classname, node){
	if(!node) node = document.getElementsByTagName("body")[0];
	var a = [];
	var re = new RegExp('\\b' + classname + '\\b');
	var els = node.getElementsByTagName("*");
	for(var i=0,j=els.length; i<j; i++)
		if(re.test(els[i].className))a.push(els[i]);
	return a;
}

var mouseOvers=0;
var menuShown=false;

function showCutoutMenu(evt,defaultCutout){
	if(!window.userStatus){ window.userStatus='g'; }
	//window.userStatus='g'; //TO REMOVE
	var forRegisteredURL="/user/reistration";
	mouseOvers++;

	if((!evt.srcElement)&&(!evt.toElement)&&(!evt.target)){evt=window.event}
	if(evt.type == "click"){ 
		menuShown=true;
		targetElement=evt.srcElement ? evt.srcElement : evt.target
	}
	else{ 
		menuShown=false;
		targetElement=evt.toElement ? evt.toElement : evt.target
	}

	var targetWidth=targetElement.offsetWidth, targetHeight=targetElement.offsetHeight;

	if (targetElement.nodeName.toLowerCase()!="a"){
		targetElement=targetElement.parentNode;
		if (targetElement.nodeName.toLowerCase()!="a"){return}
	}

	var targetLink=targetElement.href;
	targetElement=targetElement.parentNode; //damn IE
	var targetPosition=findPosition(targetElement);
	var scrollOffset=getScrollOffset();
//	alert (targetElement.nodeName.toLowerCase()+" "+targetPosition[0]+" "+targetWidth);

	var containerDiv=document.getElementById('cutoutMenuContainer');
	if (!containerDiv){return;}
//	var menuHTML='<a href="javascript:">Mouseovers:'+mouseOvers+"</a>\n";
	var menuHTML='';
	for(i in cutoutList){
		if (cutoutList[i][1].indexOf(userStatus)!=-1) {
			if(targetLink.indexOf("?")==-1){ itemLink=targetLink+"?cutout="+escape(cutoutList[i][0]) }
				else{ itemLink=targetLink+"&cutout="+escape(cutoutList[i][0]) }
//			alert(itemLink);
			menuHTML+='<a href="javascript:"'+((cutoutList[i][0] == defaultCutout) ? ' class="active"' : '')+'onClick=\'location.href="'+itemLink+'"\'>'+cutoutList[i][0]+"</a>\n";
		}
		else if(userStatus=='g'){
			menuHTML+='<a href="javascript:" class="forRegistered" onClick=\'location.href="'+forRegisteredURL+'"\' onMouseOver=\'showBalloonForReg(this);\' onMouseOut=\'hideBalloonForReg();\'>'+cutoutList[i][0]+"</a>\n";
		}

	}
	containerDiv.innerHTML=menuHTML;

	var windowSize=getWindowSize();
	if(targetPosition[0]-scrollOffset[0]+targetWidth+containerDiv.offsetWidth<windowSize[0]-10){
		containerDiv.style.left=(targetPosition[0]+targetWidth-1)+'px';
	}
	else{ containerDiv.style.left=(targetPosition[0]-containerDiv.offsetWidth+1)+'px' }

	if(targetPosition[1]-scrollOffset[1]+containerDiv.offsetHeight<windowSize[1]-10){
		containerDiv.style.top=targetPosition[1]+'px'
	}
	else if(targetPosition[1]-scrollOffset[1]+targetHeight-containerDiv.offsetHeight>0){
		containerDiv.style.top=(targetPosition[1]+targetHeight-containerDiv.offsetHeight)+'px'
	} else {
		containerDiv.style.top=targetPosition[1]+'px'
	}

	containerDiv.style.visibility="visible";

	return true;
}

function showPersistentCutoutMenu(evt,defaultCutout){
	if(!menuShown){
		showCutoutMenu(evt,defaultCutout);
	} else {
		hideCutoutMenu(evt);
	}
}

function hideCutoutMenu(evt){

	menuShown=false;

	if((!evt.srcElement)&&(!evt.toElement)&&(!evt.relatedTarget)){evt=window.event}
	if(evt.type == "click"){ targetElement=evt.srcElement ? evt.srcElement : evt.target }
	else {targetElement=evt.toElement ? evt.toElement : evt.relatedTarget }

	if((targetElement.id=='cutoutMenuContainer')||(targetElement.parentNode.id=='cutoutMenuContainer')){return}

	document.getElementById('cutoutMenuContainer').style.visibility="hidden";

	//alert("out!");
}

function showPreviewButton(evt, previewId){
	if(!previewId){ return true; }

	if((!evt.srcElement)&&(!evt.toElement)&&(!evt.target)){evt=window.event}
	if(evt.type == "click"){ 
		menuShown=true;
		targetElement=evt.srcElement ? evt.srcElement : evt.target
	}
	else{ 
		menuShown=false;
		targetElement=evt.toElement ? evt.toElement : evt.target
	}
	
	var targetWidth=targetElement.offsetWidth, targetHeight=targetElement.offsetHeight;
	var targetPosition=findPosition(targetElement);
	//alert (targetPosition[0]+" "+targetPosition[1]+" "+targetWidth+" "+targetHeight);

	if (targetElement.nodeName.toLowerCase()!="a"){
		targetElement=targetElement.parentNode;
	}
	if (targetElement.nodeName.toLowerCase()!="a"){
		targetElement=targetElement.parentNode;
		if (targetElement.nodeName.toLowerCase()!="a"){return}
	}

	var targetLink=targetElement.href;
	targetElement=targetElement.parentNode; //damn IE
	var scrollOffset=getScrollOffset();

	var containerDiv=document.getElementById('previewButtonContainer');
	if (!containerDiv){return;}                                      // /download/ ... -2/Preview
	var previewButtonHTML='<div id="previewButton"><a href="'+previewId+'" class="highslide" onclick="return hs.expand(this);"></a></div>';
	containerDiv.innerHTML=previewButtonHTML;
	containerDiv.style.left=(targetPosition[0]+1)+'px';
	containerDiv.style.top=(targetPosition[1]+targetHeight-25)+'px'
	containerDiv.style.visibility="visible";
	return true;
}

function hidePreviewButton(evt){

	if((!evt.srcElement)&&(!evt.toElement)&&(!evt.relatedTarget)){evt=window.event}
	if(evt.type == "click"){ targetElement=evt.srcElement ? evt.srcElement : evt.target }
	else {targetElement=evt.toElement ? evt.toElement : evt.relatedTarget }

	evt.cancelBubble = true;
	if (evt.stopPropagation) evt.stopPropagation();

	if (typeof(targetElement)!='object'){ return false; }

	if ((typeof(targetElement.id)=='undefined')||(targetElement.id==null)||(targetElement.id=='')){ targetElement=targetElement.parentNode }
	if ((typeof(targetElement.id)=='undefined')||(targetElement.id==null)||(targetElement.id=='')){ targetElement=targetElement.parentNode }
	if ((typeof(targetElement.id)!='undefined')&&((targetElement.id=='previewButtonContainer')||(targetElement.id=='previewButton'))){return}

	document.getElementById('previewButtonContainer').style.visibility="hidden";

	//alert("out!");
}

function showBalloonForReg(element){
//	document.getElementById('balloonForRegRight').style.visibility="visible";
}

function hideBalloonForReg(){
	document.getElementById('balloonForRegRight').style.visibility="hidden";
	document.getElementById('balloonForRegLeft').style.visibility="hidden";
}
/*
function getMovie(movieName) { 
    if (navigator.appName.indexOf("Microsoft") != -1) { 
        return window[movieName]; 
    } else { 
        return document[movieName]; 
    }
}
*/
function getMovie(movieName) {
    var M$ =  document[movieName];
    return (M$ ? document : window)[movieName]
}


function toggleFullScreen(){
	var puzzleObject=getMovie("puzzleObject")
	puzzleObject.toggleFullScreen();
}

function lzwCompress(input){
    var dict = {};
    var data = (input + "").split("");
    var out=[];
    var currChar;
    var phrase = data[0];
    var code = 256;
    
    for(var i=1;i<data.length;i++){
        currChar = data[i];
        if(dict[phrase + currChar] != null){
            phrase += currChar;
        }else{
            out.push(phrase.length > 1 ? dict[phrase] : phrase.charCodeAt(0));
            dict[phrase + currChar] = code;
            code++;
            phrase=currChar;
        }
    }
    out.push( phrase.length > 1 ? dict[phrase] : phrase.charCodeAt(0));
    for(var i=0;i<out.length;i++){

        out[i] = String.fromCharCode(out[i]);
    }
    return out.join("");
}

function lzwDecompress(input){
    var dict={};
    var data = (input + "").split("");
    var currChar = data[0];
    var oldPhrase = currChar;
    var out = [currChar];
    var code = 256;
    var phrase;
    for(var i=1;i<data.length;i++){
        var currCode = data[i].charCodeAt(0);
        if(currCode < 256){
            phrase = data[i];
        }else{
            phrase = dict[currCode] ? dict[currCode] : (oldPhrase + currChar);
        }
        out.push(phrase);
        currChar = phrase.charAt(0);
        dict[code] = oldPhrase + currChar;
        code ++;
        oldPhrase = phrase;
    }
    return out.join("");
}

// private property
_keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";

// private method for UTF-8 encoding
function _utf8_encode(string) {
	string = string.replace(/\r\n/g,"\n");
	var utftext = "";

	for (var n = 0; n < string.length; n++) {

		var c = string.charCodeAt(n);

		if (c < 128) {
			utftext += String.fromCharCode(c);
		}
		else if((c > 127) && (c < 2048)) {
			utftext += String.fromCharCode((c >> 6) | 192);
			utftext += String.fromCharCode((c & 63) | 128);
		}
		else {
			utftext += String.fromCharCode((c >> 12) | 224);
			utftext += String.fromCharCode(((c >> 6) & 63) | 128);
			utftext += String.fromCharCode((c & 63) | 128);
		}

	}
	return utftext;
}

// private method for UTF-8 decoding
function _utf8_decode(utftext) {
	var string = "";
	var i = 0;
	var c = c1 = c2 = 0;

	while ( i < utftext.length ) {

		c = utftext.charCodeAt(i);

		if (c < 128) {
			string += String.fromCharCode(c);
			i++;
		}
		else if((c > 191) && (c < 224)) {
			c2 = utftext.charCodeAt(i+1);
			string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
			i += 2;
		}
		else {
			c2 = utftext.charCodeAt(i+1);
			c3 = utftext.charCodeAt(i+2);
			string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			i += 3;
		}

	}
	return string;
}

// public method for encoding
function base64Encode(input) {
	var output = "";
	var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
	var i = 0;

	input = _utf8_encode(input);

	while (i < input.length) {
		//alert("i: "+i);

		chr1 = input.charCodeAt(i++);
		chr2 = input.charCodeAt(i++);
		chr3 = input.charCodeAt(i++);

		//alert(chr1+" "+chr2+" "+chr3);

		enc1 = chr1 >> 2;
		enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
		enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
		enc4 = chr3 & 63;

		if (isNaN(chr2)) {
			enc3 = enc4 = 64;
		} else if (isNaN(chr3)) {
			enc4 = 64;
		}

		output = output +
		_keyStr.charAt(enc1) + _keyStr.charAt(enc2) +
		_keyStr.charAt(enc3) + _keyStr.charAt(enc4);

	}

	return output;
}

// public method for decoding
function base64Decode(input) {
	var output = "";
	var chr1, chr2, chr3;
	var enc1, enc2, enc3, enc4;
	var i = 0;

	input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

	while (i < input.length) {

		enc1 = _keyStr.indexOf(input.charAt(i++));
		enc2 = _keyStr.indexOf(input.charAt(i++));
		enc3 = _keyStr.indexOf(input.charAt(i++));
		enc4 = _keyStr.indexOf(input.charAt(i++));

		chr1 = (enc1 << 2) | (enc2 >> 4);
		chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
		chr3 = ((enc3 & 3) << 6) | enc4;

		output = output + String.fromCharCode(chr1);

		if (enc3 != 64) {
			output = output + String.fromCharCode(chr2);
		}
		if (enc4 != 64) {
			output = output + String.fromCharCode(chr3);
		}

	}

	output = _utf8_decode(output);

	return output;

}

function getCookie(key) {
	var cookieValue = null;
	if (key) {
		var cookieSearch = key + "=";
		if (document.cookie) {
			var cookieArray = document.cookie.split(";");
			for (var i = 0; i < cookieArray.length; i++) {
				var cookieString = cookieArray[i];
				// skip past leading spaces
				while (cookieString.charAt(0) == ' ') {
					cookieString = cookieString.substr(1);
				}
				// extract the actual value
				if (cookieString.indexOf(cookieSearch) == 0) {
					cookieValue = cookieString.substr(cookieSearch.length);
				}
			}
		}
	}
	//alert("Got cookie "+key+": "+cookieValue);
	return cookieValue;
}

function setCookie(key, val, time) {
	if (key) {
		var date = new Date();
		if (val != null) {
			// expires in one day
			date.setTime(date.getTime() + (time === undefined ? 30*24*60*60*1000 : time*1000));
			document.cookie = key + "=" + val + ";" + (time===0 ? "" : " expires=" + date.toGMTString() + ";") + " path=/";
		}
		else {
			// expires yesterday
			date.setTime(date.getTime() - (24*60*60*1000));
			document.cookie = key + "=; expires=" + date.toGMTString() + "; path=/";
		}
	}
	//alert("Set cookie "+key+" to "+val);
	//alert("Checking cookie "+key+": "+getCookie(key));
}

function setPackedCookie(key, val) {
	if (key) {
		if (val != null) {
			alert("Source ("+val.length+" chars): "+val);
			packedCookie = lzwCompress(val);
			alert("Compressed: "+packedCookie);
			packedCookie = base64Encode(packedCookie);
			alert("Encoded: "+packedCookie);
			setCookie(key, packedCookie);
		}
		else {
			setCookie(key, val);
		}
	}
}

function getPackedCookie(key) {
	if (key) {
		packedCookie=getCookie(key);
		alert("Cookie ("+packedCookie.length+" chars): "+packedCookie);
		if (packedCookie != null) {
			packedCookie = base64Decode(packedCookie);
			alert("Decoded: "+packedCookie);
			packedCookie = lzwDecompress(packedCookie);
			alert("Decompressed: "+packedCookie);
		}
		return packedCookie;
	}
}

function loadPuzzle(){
	var savedPuzzle=getCookie("savedPuzzle").split(":&");
	if (savedPuzzle[0]){
		//alert ("Found saved puzzle: #"+savedPuzzle[0]+": "+savedPuzzle[1]);
		if((typeof(puzzleId) != "undefined") && (savedPuzzle[0] == puzzleId)){
			getMovie("puzzleObject").loadState();
		} else {
			document.getElementById("savedPuzzleNameBlock").innerHTML="- loading...";
			window.name="loadOnStart";
			window.location="/itemId="+savedPuzzle[0];
		}
	}
}

var timeout;
var autoSaveInterval;
var tmpImg;
function savePuzzle(silent){
	var result = getMovie("puzzleObject").saveState();
	if (result & !silent){
		var savedPuzzleCookie=getCookie("savedPuzzle");
		if(savedPuzzleCookie){ var savedPuzzle=savedPuzzleCookie.split(":&") }
		if((typeof(puzzleId) != "undefined") && (typeof(savedPuzzle) != "undefined") && (savedPuzzle[0] == puzzleId)){
			//alert(getCookie("savedPuzzle"));
			document.getElementById("autoSaveBlock").style.display="none";
			document.getElementById("saveStatusBlock").style.display="block";
			document.getElementById("saveStatusBlock").innerHTML="- puzzle saved";
			timeout=setTimeout("document.getElementById('autoSaveBlock').style.display='block'; document.getElementById('saveStatusBlock').style.display='none';",600);
		} else {
			document.getElementById("autoSaveBlock").style.display="none";
			document.getElementById("saveStatusBlock").style.display="block";
			document.getElementById("saveStatusBlock").innerHTML="- failed (<a href='http://www.google.com/support/accounts/bin/answer.py?answer=61416' target='_blank' rel='nofollow'>cookies disabled?</a>)";
			timeout=setTimeout("document.getElementById('autoSaveBlock').style.display='block'; document.getElementById('saveStatusBlock').style.display='none';",5000);
		}
	}
	updateLoadBlock();
}
function updateAutoSave(checkbox){
	if(checkbox.checked){
		autoSaveInterval=setInterval("savePuzzle(false)",60000);
	} else {
		clearInterval(autoSaveInterval);
	}
}
function updateLoadBlockThumb(){
	var clipTo=35;
	//alert(tmpImg.width + 'x' + tmpImg.height);
	var savedPuzzleThumb=document.getElementById("savedPuzzleThumb");
	var coef=tmpImg.width/tmpImg.height;
	if (coef>1){
		savedPuzzleThumb.style.width=Math.round(clipTo*coef)+"px";
		savedPuzzleThumb.style.height=clipTo+"px";
		savedPuzzleThumb.style.top="0";
		savedPuzzleThumb.style.left="-"+Math.round(clipTo*(coef-1)/2)+"px";
	} else {
		savedPuzzleThumb.style.width=clipTo+"px";
		savedPuzzleThumb.style.height=Math.round(clipTo/coef)+"px";
		savedPuzzleThumb.style.top="-"+Math.round((clipTo/coef-clipTo)/2)+"px";
		savedPuzzleThumb.style.left="0";
	}
	//savedPuzzleThumb.style.clip="rect(0px,10px,0px,10px)";
	savedPuzzleThumb.src=tmpImg.src;
}
function updateLoadBlock(){
	//alert("Updating");
	if(document.getElementById("loadBlock") == null){ return false }
	var savedPuzzle = getCookie("savedPuzzle");
	if (savedPuzzle){
		savedPuzzle = savedPuzzle.split(":&");

		document.getElementById("loadBlock").savedPuzzleId=savedPuzzle[0];
		document.getElementById("savedPuzzleNameBlock").innerHTML=savedPuzzle[1];
		if (savedPuzzle[0]) {
            tmpImg = new Image();
            tmpImg.onload = updateLoadBlockThumb;
            tmpImg.src = 'http://216.55.178.207/getThumbnail?g2_itemId='+savedPuzzle[0];
            //tmpImg.src = 'http://thejigsaw/items/thumbnail/02/02/0000060202.jpg';
        } else if (savedPuzzle[2]){
			tmpImg = new Image();
			tmpImg.onload = updateLoadBlockThumb;
            tmpImg.src = 'http://216.55.178.207/getThumbnail?g2_itemId='+savedPuzzle[2];
		}
		document.getElementById("loadBlock").style.display="block";
	} else {
		document.getElementById("loadBlock").style.display="none";
	}
}
function checkLoadOnStart(){
	var savedPuzzle=getCookie("savedPuzzle").split(":&");
	if(savedPuzzle[0] && (savedPuzzle[0] == puzzleId) && window.name=="loadOnStart"){
		//alert("need to load!");
		window.name="mainWindow";
		return true;
	} else {
		window.name="mainWindow";
		return false;
	}
}

function deleteSavedPuzzle(){
	if (confirm('Delete saved puzzle?')){
		if(getMovie("puzzleObject")){ getMovie("puzzleObject").clearState() }
		setCookie("savedPuzzle", null);
		updateLoadBlock();
	}
}


// �������� ������� mousewheel
function handleMouseWheel(event){
        var delta = 0;
        if (!event) event = window.event; // ������� IE.
        // ��������� ��������������� delta
        if (event.wheelDelta) { 
                // IE, Opera, safari, chrome - ��������� ������ ����� 120
                delta = event.wheelDelta/120;
        } else if (event.detail) { 
                // Mozilla, ��������� ������ ����� 3
                delta = -event.detail/3;
        }
        // �������������� ������� ��������� mousewheel
        if (delta && typeof handleFunction == 'function' && event.ctrlKey) {
                handleFunction(delta);
                // ������� ������� ������� - ������� ����������� (�������� ����).
                if (event.preventDefault)
                        event.preventDefault();
                event.returnValue = false; // ��� IE
				return false;
        }
}

function initMouseWheel()
{
    var puzzleObject = getMovie("puzzleObject");
    if (!puzzleObject){ return false; }
    // ������������� ��������������� �������
    puzzleObject.onmouseover = function(){
       handleFunction = passMouseWheel;
    };
    // �������� ��������������� �������
    puzzleObject.onmouseout = function(){
       handleFunction = null;
    }
     
    function passMouseWheel(delta) {
		//alert("Passing "+delta);
		getMovie("puzzleObject").zoomBy(delta/Math.abs(delta)*10);
    }

	// ������������� ������� mousewheel
	if (window.addEventListener) // mozilla, safari, chrome
		window.addEventListener('DOMMouseScroll', handleMouseWheel, false);
	// IE, Opera.
	window.onmousewheel = document.onmousewheel = handleMouseWheel;

	if(window.addEventListener){
		window.addEventListener('DOMMouseScroll', handleMouseWheel, false);  
		window.addEventListener('mousewheel', handleMouseWheel, false);
	} else if(window.attachEvent){
		window.attachEvent('onmousewheel', handleMouseWheel);
	}

	return true;
}

function parseMenuSrcArray(menuSrcArray){
	var re = /\d+\spiece\s([^\s][\w\s]+)$/ig, menuArray=new Array();

	for(var i=0; i<menuSrcArray.length; i++){
		if (re.test(menuSrcArray[i][0])){
			var cutoutName = RegExp.$1;
			if (typeof menuArray[cutoutName] == 'undefined'){
				menuArray[cutoutName]=new Array();
			}
			menuArray[cutoutName].push(new Array(menuSrcArray[i][0], menuSrcArray[i][1]));
		}
		re.lastIndex = 0;
	}

	//alert(menuArray['Elegant'][1][0]+" "+menuArray['Elegant'][1][1]);
	return menuArray;
}

function showPopupMenu(evt, menuArray, rootItem, selectedItem){
	if(!window.userStatus){ window.userStatus='g'; }
	//window.userStatus='g'; //TO REMOVE
	var forRegisteredURL="/info/registration";
	mouseOvers++;

	if((!evt.srcElement)&&(!evt.toElement)&&(!evt.target)){evt=window.event}
	if(evt.type == "click"){ 
		menuShown=true;
		targetElement=evt.srcElement ? evt.srcElement : evt.target
	}
	else{ 
		menuShown=false;
		targetElement=evt.toElement ? evt.toElement : evt.target
	}
	
	if(targetElement.id=='newLabel'){ targetElement=targetElement.parentNode }
	var targetWidth=targetElement.offsetWidth, targetHeight=targetElement.offsetHeight;
	var targetPosition=findPosition(targetElement);

	if (targetElement.nodeName.toLowerCase()!="a"){
		targetElement=targetElement.parentNode;
	}
	
	var containerDiv=!rootItem ? document.getElementById('popupMenuContainer') : document.getElementById('popupMenuContainer2');

	var targetLink=targetElement.href;
	targetElement=targetElement.parentNode; //damn IE
	if (!rootItem){ targetPosition=findPosition(targetElement) }
	//if(window.userStatus=='a'){alert("Position: "+targetPosition[0]+" "+targetPosition[1])}
	if (rootItem){ targetLink=targetElement.parentNode.targetLink } // to fix (by wrapping links with divs?)
	containerDiv.targetLink=targetLink;
	//if (rootItem){ alert(rootItem); }
	//alert(targetElement.nodeName.toLowerCase());
	//alert(targetLink);

	var scrollOffset=getScrollOffset();
//	alert (targetElement.nodeName.toLowerCase()+" "+targetPosition[0]+" "+targetWidth);

//	var menuHTML='<a href="javascript:">Mouseovers:'+mouseOvers+"</a>\n";
	var menuHTML='';
	var itemArray = !rootItem ? menuArray : eval("menuArray['"+rootItem+"']");

	if (!rootItem){
		menuHTML+='<div style="font-weight:bold;">More cuts:</div>\n';
	}

	for(i in itemArray){
		if (!rootItem){
			var newCutout = (typeof(menuArray[i][0][0]) != 'undefined')&&(typeof(menuArray[i][0][1]) != 'undefined')&&(itemArray[i][0][1].indexOf('n')!=-1);
			menuHTML+='<span><a href="javascript:"'+(typeof(menuArray[i][0][0]) != 'undefined' ? ' class="submenu"' : '')+' onMouseOver="showPopupMenu(event,cutoutMenu,\''+i+'\',\''+selectedItem+'\');return true;" onMouseOut="hidePopupMenu(event);return true;">'+i+(newCutout ? '<img id="newLabel" src="/images/new_cutout.png">' : '')+'</a></span>\n';

		} else {
			//alert(itemArray[i][0]);

			if (itemArray[i][1].indexOf(userStatus)!=-1) {
				if(targetLink.indexOf("?")==-1){ itemLink=targetLink+"?cutout="+escape(itemArray[i][0]) }
					else{ itemLink=targetLink+"&cutout="+escape(itemArray[i][0]) }
//				alert(itemLink);
				menuHTML+='<span><a href="'+itemLink+'"'+((itemArray[i][0] == selectedItem) ? ' class="active"' : '')+'>'+itemArray[i][0]+"</a></span>\n";
			}
			else if(userStatus=='g'){
				menuHTML+='<span><a href="'+forRegisteredURL+'" class="forRegistered">'+itemArray[i][0]+"</a></span>\n";
			}

		}
	}
	containerDiv.innerHTML=menuHTML;

	var windowSize=getWindowSize();
	if(targetPosition[0]-scrollOffset[0]+targetWidth+containerDiv.offsetWidth<windowSize[0]-10){
		containerDiv.style.left=(targetPosition[0]+targetWidth-1)+'px';
	}
	else{ containerDiv.style.left=(targetPosition[0]-containerDiv.offsetWidth+1)+'px' }

	if(targetPosition[1]-scrollOffset[1]+containerDiv.offsetHeight<windowSize[1]-10){
		containerDiv.style.top=targetPosition[1]+'px'
	}
	else if(targetPosition[1]-scrollOffset[1]+targetHeight-containerDiv.offsetHeight>0){
		containerDiv.style.top=(targetPosition[1]+targetHeight-containerDiv.offsetHeight)+'px'
	} else {
		containerDiv.style.top=targetPosition[1]+'px'
	}

	containerDiv.style.visibility="visible";

	return true;
}

function showPersistentPopupMenu(evt,menuArray,rootItem,selectedItem){
	if(!menuShown){
		showPopupMenu(evt,menuArray,rootItem,selectedItem);
	} else {
		hidePopupMenu(evt);
	}
}

function hidePopupMenu(evt){

	menuShown=false;

	if((!evt.srcElement)&&(!evt.toElement)&&(!evt.relatedTarget)){evt=window.event}
	if(evt.type == "click"){ targetElement=evt.srcElement ? evt.srcElement : evt.target }
	else {targetElement=evt.toElement ? evt.toElement : evt.relatedTarget }

	evt.cancelBubble = true;
	if (evt.stopPropagation) evt.stopPropagation();

	if (typeof(targetElement)!='object'){ return false; }

	if ((typeof(targetElement.id)=='undefined')||(targetElement.id==null)||(targetElement.id=='')){ targetElement=targetElement.parentNode }
	if ((typeof(targetElement.id)=='undefined')||(targetElement.id==null)||(targetElement.id=='')){ targetElement=targetElement.parentNode }
	if((typeof(targetElement.id)!='undefined')&&((targetElement.id=='popupMenuContainer')||(targetElement.id=='popupMenuContainer2')||(targetElement.id=='newLabel'))){return}

//	if(window.userStatus=='a'){alert("Target: "+targetElement.nodeName.toLowerCase()+" "+targetElement.id)}

//	if((targetElement.id=='popupMenuContainer')||(targetElement.parentNode.id=='popupMenuContainer')||(targetElement.parentNode.parentNode.id=='popupMenuContainer')){return}
//	if((targetElement.id=='popupMenuContainer2')||(targetElement.parentNode.id=='popupMenuContainer2')||(targetElement.parentNode.parentNode.id=='popupMenuContainer2')){return}

	document.getElementById('popupMenuContainer').style.visibility="hidden";
	document.getElementById('popupMenuContainer2').style.visibility="hidden";

	//alert("out!");
}

var feedbackButtonY, feedbackButtonHideInterval;
function hideFeedbackButton(){
	var clientWidth=getWindowSize()[0];
	if ((clientWidth>0)&&(clientWidth<=1024)){
        feedbackButtonY = 50;
        preloadImages('http://thejigsawpuzzles.com/themes/icemodified/images/feedback-hidden.gif');
        feedbackButtonHideInterval = setInterval(function() {
            feedbackButtonY += 3;
            document.getElementById("feedbackButton").style.top = feedbackButtonY + "%";
            if (feedbackButtonY >= 95){
                clearInterval(feedbackButtonHideInterval);
                document.getElementById("feedbackButton").style.top = "";
                document.getElementById("feedbackButton").className += " hidden";
            }
        }, 20);
	}
}

function getClientOS(){
	var userAgent=navigator.userAgent.toLowerCase();
	if (userAgent.indexOf('android')!=-1) {
		return('Android');
	} else if ((userAgent.indexOf('ipad')!=-1)||(userAgent.indexOf('iphone')!=-1)||(userAgent.indexOf('ipod')!=-1)) {
		return('iOS');
	} else if(userAgent.indexOf('mac') != -1){
		return('Mac OS X');
	} else if (userAgent.indexOf('win')!=-1) {
		if ((userAgent.indexOf('windows nt 5.1')!=-1) || (userAgent.indexOf('windows xp')!=-1)){ return('Windows XP'); }
		else if ((userAgent.indexOf("windows nt 7.0")!=-1) || (userAgent.indexOf("windows nt 6.1")!=-1)){ return('Windows 7'); }
		else if ((userAgent.indexOf("windows nt 6.0")!=-1)){ return('Windows Vista'); }
		else if (userAgent.indexOf("windows me")!=-1){ return('Windows ME'); }
		else if ((userAgent.indexOf("windows nt 4.0")!=-1) || (userAgent.indexOf("winnt4.0")!=-1) || (userAgent.indexOf("winnt")!=-1)){ return('Windows NT'); }
		else if ((userAgent.indexOf("windows nt 5.2")!=-1)){ return('Windows 2003 Server'); }
		else if ((userAgent.indexOf("windows nt 5.0")!=-1) || (userAgent.indexOf("windows 2000")!=-1)){ return('Windows 2000'); }
		else if ((userAgent.indexOf("windows 98")!=-1) || (userAgent.indexOf("win98")!=-1)){ return('Windows 98'); }
		else if ((userAgent.indexOf("windows 95")!=-1) || (userAgent.indexOf("win95")!=-1) || (userAgent.indexOf("windows_95")!=-1)){ return('Windows 95'); }
	}
}

function explorePuzzle(){
	if (typeof(document.getElementById("cse-search-box"))!='object'){
		return false;
	}
	var csForm=document.getElementById("cse-search-box");
	if (typeof(csForm["q"])=='undefined'){
		return false;
	}
	csForm["q"].value=window.puzzleName;
	if (typeof(csForm["oq"])!='undefined'){ csForm["oq"].value=window.puzzleName; }
	if (typeof(csForm["gsc.q"])!='undefined'){ csForm["gsc.q"].value=window.puzzleName; }
	csForm["q"].focus();
	csForm.submit();
}

function explorePuzzleDelayed(){
	setTimeout(explorePuzzle,500);
}

function openMorePuzzles(){
	var re= /^(https?:\/\/)?([\w\.-_]+\.)?thejigsawpuzzles.com/i ;
	if(document.referrer && (!re.test(document.referrer))){ window.location.href='http://thejigsawpuzzles.com' }
	else {
		var currentURL=window.location.href;
		window.setTimeout(function(){ if(window.location.href==currentURL){ window.location.href='http://thejigsawpuzzles.com' } }, 1000);
		window.history.go(-1);
	}
}
