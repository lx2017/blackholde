var REG_PHONE = /^1[1-9][0-9][0-9]{8}$/; //判断手机号

var loginPassword = $("#userpassword");
var REG_EMAIL = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i; //判断邮箱
var username = $("#number"); //电话或者邮箱
// 检查用户名是否为空
function checkUserName() {
	var value = username.val();
	if (value === '') {
		username.addClass('redbd');
		$("#error-wrap").html("手机号不能为空");
		$("#error-wrap").css("opacity", '1');
		return false;
	} else {
		// phone email
		if ( !REG_PHONE.test(value)) {

            if (value.length < 11 && value.length > 11) {
                username.addClass('redbd');
                $("#error-wrap").html("您的手机号码不正确");
                $("#error-wrap").css("opacity", '1');
                return false;
            } else {
                username.addClass('redbd');
                $("#error-wrap").html("您的手机号码不正确");
                $("#error-wrap").css("opacity", '1');
                return false;
            }
		}else{
            username.removeClass('redbd');
            $("#error-wrap").html("");
            $("#error-wrap").css("opacity", '0');
            return true;
        }
	}
}
function checkisuser(){
    var password = loginPassword.val();
    var value = $("#number").val();
    var t =0;
    $.ajax({
        type: 'POST',
        url:$("#number").attr('_href'),
        data: {"password":password,"number":value},
        dataType:'json',
        async: false,
        success: function (data) {
            if(data.code== 1006){
                loginPassword.addClass('redbd');
                $("#error-wrap").html("用户或密码错误");
                $("#error-wrap").css("opacity", '1');
                return false
            }else{
                t=1;
            }
        }
    });
    if(t==1){
        loginPassword.removeClass('redbd');
        $("#error-wrap").html("");
        $("#error-wrap").css("opacity", '0');
        return true
    }
}
function checkPassword() {
	if (loginPassword.val() === '') {
		loginPassword.addClass('redbd');
		$("#error-wrap").html("您的密码不能为空");
		$("#error-wrap").css("opacity", '1');
		return false
	} else {
		loginPassword.removeClass('redbd');
		$("#error-wrap").html("");
		$("#error-wrap").css("opacity", '0');
		return true
	}
}
// 检查验证码是否为空
function checkVeryfycode() {
	if ($("#captcha").val() === '') {
		$("#captcha").addClass('redbd');
		$("#error-wrap").html("验证码不能为空");
		$("#error-wrap").css("opacity", '1');
		return false

	} else {
        loginPassword.removeClass('redbd');
		$("#captcha").html("");
		$("#error-wrap").css("opacity", '0');
		return true
	}
}
//input显示隐藏
$.fn.foucsText = function(c) {
	var a = this;
	var b = (c == null) ? $(a).val() : c;
	a.val(b);
	a.focus(function() {
		if (a.val() == b) {
			a.val("")
		}
	});
	a.blur(function() {
		if (a.val() == "") {
			a.val(b)
		}
	});
	return a
};
/*判断是否为ie*/
function checkIE() {
	var _IE = (function() {
		var v = 3,
			div = document.createElement('div'),
			all = div.getElementsByTagName('i');
		while (
			div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
			all[0]
		);
		return v > 4 ? v : false;
	}());


	if (_IE && _IE > 6) {

		$("#number").foucsText('请输入您的手机号码');
		$("#userpassword").foucsText('请输入您的密码');
		
		$("#captcha").foucsText('验证码');
		
	}

}
$(function() {
	$("#number").on("blur", function() {

		 checkUserName()
	});
		$("#userpassword").on("blur", function() {

		checkPassword();


	});
			/*$("#captcha").on("blur", function() {

		checkPassword()
	});*/
	/*-------解决placeholder兼容-------*/
		checkIE();
	// 点击登录按钮验证更新
	function refreshAuth() {
		var timestamp = Date.parse(new Date());
		$("#auth-image").attr("src", "http://www.daishusale.com/user/vcode?timestamp=" + timestamp);
	}
	$(".resh-agin").click(function() {
			refreshAuth();
		});
		// 无验证码时验证
	$("#form_login").on("submit", function() {


		var formOk = checkUserName() && checkPassword() && checkisuser();
		if (!formOk) {
			return false
		} else {
			return true
		}
	});
	// 有验证码适验证
	// $("#form_login").on("submit", function() {


	// 	var formOk = checkUserName() && checkPassword() && checkVeryfycode()

	// 	if (!formOk) {
	// 		return false
	// 	} else {
	// 		return true
	// 	}
	// });

});