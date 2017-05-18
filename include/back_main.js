/*BackEnd custom javascript*/

// Mobile Nav
(function () {
	// Create mobile element
	var mobile = document.createElement('div');
	mobile.className = 'nav-mobile';
	document.querySelector('#nav').appendChild(mobile);

	// hasClass
	function hasClass(elem, className) {
		return new RegExp(' ' + className + ' ').test(' ' + elem.className + ' ');
	}

	// toggleClass
	function toggleClass(elem, className) {
		var newClass = ' ' + elem.className.replace(/[\t\r\n]/g, ' ') + ' ';
		if (hasClass(elem, className)) {
			while (newClass.indexOf(' ' + className + ' ') >= 0) {
				newClass = newClass.replace(' ' + className + ' ', ' ');
			}
			elem.className = newClass.replace(/^\s+|\s+$/g, '');
		} else {
			elem.className += ' ' + className;
		}
	}

	// Mobile nav function
	var mobileNav = document.querySelector('.nav-mobile');
	var toggle = document.querySelector('.nav-list');
	mobileNav.onclick = function () {
		toggleClass(this, 'nav-mobile-open');
		toggleClass(toggle, 'nav-active');
	};
	
	// removeClass
	function removeClass(elem, className) {
	var newClass = ' ' + elem.className.replace( /[\t\r\n]/g, ' ') + ' ';
	if (hasClass(elem, className)) {
        while (newClass.indexOf(' ' + className + ' ') >= 0 ) {
            newClass = newClass.replace(' ' + className + ' ', ' ');
        }
        elem.className = newClass.replace(/^\s+|\s+$/g, '');
    	}
	}
	
	document.getElementById('main-wrapper').onclick = function() {
		removeClass(mobileNav, 'nav-mobile-open');
		removeClass(toggle, 'nav-active');
	};
})();


// sticky navigation
(function () {
	var sticky = document.getElementById("header-wrapper");
	var stickyimg = document.getElementById("logo");
	var stop = (sticky.offsetTop - -30);

	window.onscroll = function (e) {
		var scrollTop = (window.pageYOffset !== undefined) ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;

		if (scrollTop >= stop) {
			sticky.className = 'fixed';
			stickyimg.className = 'fixed';
		} else { 
			sticky.className = '';
			stickyimg.className = '';
		}

	};
})();

// Change addbtn
	var adddiv = document.getElementById('addroll');
	var popupp = document.getElementById('popup');
	adddiv.setAttribute('class', 'visible');
	popupp.setAttribute('class', 'visible');

	function addin() {
	document.getElementById('img').src='../bilder/back-add2.png';
	}
 	function addout() {
	document.getElementById('img').src='../bilder/back-add1.png';
	}

