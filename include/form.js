// Form hide and show
(function () {
	var form1 = document.getElementById('loginform');
	var form2 = document.getElementById('recoverpwform');

	var recoverpwBtn = document.getElementById('recoverpw_btn');

	recoverpwBtn.onclick = function() {
		form1.setAttribute('class', 'hidden');
		form2.setAttribute('class', 'visible');
		document.getElementById("recoverpassword").focus();
	};

})();