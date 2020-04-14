function initHelpBlock(block){
	if (typeof(block)!='object'){ alert("Not Found!"); return false; }
	divs=block.getElementsByTagName('div');
//	alert(divs.length);
	for (var i = 0; i < divs.length; i++) {
		if(divs[i].className=='q'){
			divs[i].onclick=expandQuestion;
			divs[i].aList=new Array();
			var children=divs[i].parentNode.childNodes;
			for (var j = 0; j < children.length; j++){
				if((typeof(children[j])=='object') && (children[j].className=='a')){
					divs[i].aList.push(children[j]);
					children[j].className='ac';
				}
			}
			divs[i].className='qc';
		}
	}
	var anchorName=window.location.hash.substring(1);
	if(anchorName.length>0){ expandQuestion(anchorName); }
}

function expandQuestion(questionId){
    questionElement = (typeof questionId != 'undefined')&&(questionId.length>0) ? document.getElementById(questionId).parentNode : this;
    questionElement.className=(questionElement.className=='q')&&(questionElement == this) ? 'qc' : 'q';
    if (typeof(questionElement.aList)!='object'){ alert("List Not Found!"); return false; }
    for (var i = 0; i < questionElement.aList.length; i++) {
//		alert(typeof questionElement.aList[i]);
        questionElement.aList[i].className=(questionElement.aList[i].className=='a')&&(questionElement == this) ? 'ac' : 'a';
    }
//	alert(questionElement.aList.length);
//	alert("Click!");
}
$(document).ready(function(){
    $('.qc').click(function(event){
        $(this).siblings('.ac').show();
        event.stopImmediatePropagation();
    });
});