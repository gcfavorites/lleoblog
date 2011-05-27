// TOUCH-EVENTS SINGLE-FINGER SWIPE-SENSING JAVASCRIPT
// Courtesy of PADILICIOUS.COM and MACOSXAUTOMATION.COM

function ipad_init(){ document.body.id='body'; delete(mHelps['Wscroll']); 
	ipadset(document.body,ipadkeydo);
}

function ipadkeydo(){ if(typeof mHelps['nonav'] !== 'undefined') return true; /*&& hotkey[i][1]==0*/
	var s=keycodes[swipeDirection]; for(var i in hotkey) { if(hotkey[i][0]==s) return hotkey[i][2](); }
}

function ipadset(e,fun){ // ������������� �������
	addEvent(e,'touchstart',function(){touchStart(event,e.id)});
	addEvent(e,'touchend',function(){touchEnd(event,fun)});
	addEvent(e,'touchmove',function(){touchMove(event)});
	addEvent(e,'touchcancel',function(){touchCancel(event)});
}

page_onstart.push("ipad_init()");

//================================================

// this script can be used with one or more page elements to perform actions based on them being swiped with a single finger

var triggerElementID = null; // this variable is used to identity the triggering element
var fingerCount = 0;
var startX = 0;
var startY = 0;
var curX = 300;
var curY = 0;
var deltaX = 0;
var deltaY = 0;
var horzDiff = 0;
var vertDiff = 0;
var minLength = 72; // the shortest distance the user may swipe
var swipeLength = 0;
var swipeAngle = null;
var swipeDirection = null;

function touchStart(e,passedName){ if(0 && typeof e.ipaddis == 'undefined') e.preventDefault(); // disable the standard ability to select the touched object
	fingerCount=e.touches.length; // get the total number of fingers touching the screen
	// since we're looking for a swipe (single finger) and not a gesture (multiple fingers),
	// check that only one finger was used
	if(fingerCount==1){
		startX=e.touches[0].pageX; startY=e.touches[0].pageY; // get the coordinates of the touch
		triggerElementID=passedName; // store the triggering element ID
	} else touchCancel(e); // more than one finger touched so cancel
}

function touchMove(e){ if(0 && typeof e.ipaddis == 'undefined') e.preventDefault();
	if(e.touches.length == 1) { curX=e.touches[0].pageX; curY=e.touches[0].pageY; }
	else touchCancel(e);
}

function touchEnd(e,fun){ if(0 && typeof e.ipaddis == 'undefined') e.preventDefault();
	// check to see if more than one finger was used and that there is an ending coordinate
	if(fingerCount == 1 && curX != 0){
		// use the Distance Formula to determine the length of the swipe
		swipeLength = Math.round(Math.sqrt(Math.pow(curX - startX,2) + Math.pow(curY - startY,2)));
		// if the user swiped more than the minimum length, perform the appropriate action
		if( swipeLength >= minLength ) {
			caluculateAngle();
			determineSwipeDirection();
			fun(); // processingRoutine();
			touchCancel(e); // reset the variables
		} else touchCancel(e);
	} else touchCancel(e);
}

function touchCancel(e){
	// reset the variables back to default values
	fingerCount = 0;
	startX = 0;
	startY = 0;
	curX = 0;
	curY = 0;
	deltaX = 0;
	deltaY = 0;
	horzDiff = 0;
	vertDiff = 0;
	swipeLength = 0;
	swipeAngle = null;
	swipeDirection = null;
	triggerElementID = null;
}

function caluculateAngle(){
	var X = startX-curX;
	var Y = curY-startY;
	var Z = Math.round(Math.sqrt(Math.pow(X,2)+Math.pow(Y,2))); //the distance - rounded - in pixels
	var r = Math.atan2(Y,X); //angle in radians (Cartesian system)
	swipeAngle=Math.round(r*180/Math.PI); //angle in degrees
	if(swipeAngle<0) swipeAngle=360-Math.abs(swipeAngle);
}

function determineSwipeDirection(){
	if((swipeAngle <= 45)&&(swipeAngle >= 0)) swipeDirection='right'; // 'left';
	else if((swipeAngle <= 360)&&(swipeAngle >= 315)) swipeDirection='right'; // 'left';
	else if((swipeAngle >= 135)&&(swipeAngle <= 225)) swipeDirection='left'; // 'right';
	else if((swipeAngle > 45)&&(swipeAngle < 135)) swipeDirection='down';
	else swipeDirection='up';
}
