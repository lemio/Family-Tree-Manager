// XQTO Date Picker 1.1 
// http://www.xqto.com
// Released: 13 Jan 2008

// date picker calls this function
var activefield; 
var dpmouseover = false;
var dpactive = false;
document.getElementsByTagName("BODY")[0].onclick = bodyonclick;

function bodyonclick(e) {

	//alert('bodyonclick');
	
	if (!e) var e = window.event;
	// e gives access to the event in all browsers
	
	var targ;
	if (!e) var e = window.event;
	if (e.target) targ = e.target; // everyone else
	else if (e.srcElement) targ = e.srcElement; // microsoft
	if (targ.nodeType == 3) // defeat Safari bug
		targ = targ.parentNode;
		
	if(targ.id == 'dpicon') {
		showfdatepicker(targ.parentNode.parentNode);
	} else {
		if(dpactive == true && dpmouseover == false) {
			var datepicker = document.getElementById('datepicker');
			datepicker.style.visibility = 'hidden';
		}
	}
		
	
}

function getfdate(dbdate) {
	//alert('getfdate');
	var datefield = activefield.getElementsByTagName('input');
	datefield[0].value = dbdate;
	var datepicker = document.getElementById('datepicker');
	datepicker.style.visibility = 'hidden';
	dpactive = false;
}

// this fucntion passes current date to date picker
function putfdate(ymd) {
	//alert('putfdate');
	if(thisMovie("fdatepicker").currentdate) { 
		thisMovie("fdatepicker").currentdate(ymd);
	} else {
		setTimeout('putfdate("' + ymd + '")', 3); // it takes a number of milliseconds for the currentdate function to become available in the swf datepicker so keep running the function untill it is completed
	}
}	

function thisMovie(movieName) {
	//alert('thismovie');
	if (navigator.appName.indexOf("Microsoft") != -1) {
		return window[movieName]
	} else {
		return document[movieName]
	}
}

function showfdatepicker(fielddiv) {
	//alert('showfdatepicker');
	activefield = fielddiv;
	
	var icon = fielddiv.getElementsByTagName('img');
	
	var pos = new Array();
	pos = findPos(icon[0]);
	
	var datepicker = document.getElementById('datepicker');
	datepicker.style.visibility = 'visible';
	
	// move it to the right place
	datepicker.style.left = pos[0]+10 + "px";
	datepicker.style.top = pos[1]-6 + "px";
	
	dpactive = true;
	
	var datefield = fielddiv.getElementsByTagName('input');
	putfdate(datefield[0].value);
	
}

function findPos(obj) {
	//alert('findpos');
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
		return [curleft,curtop];
	}
}