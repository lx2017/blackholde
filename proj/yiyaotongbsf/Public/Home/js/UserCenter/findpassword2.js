
// 公用提示
function promptcontent(dom, content) {
	var me = $(dom);

	me.parent().find(".post_error").html(content).css('opacity', '1')
}


/*-------密码判断-------*/
function checkPassword() {

	var input = $("#userpassword");
	var value = $.trim(input.val());
	if (value === "") {
        input.addClass('redbd');
		promptcontent(input, "新密码不能为空");
		return false;
	} else {


		if (value.length < 6 || value.length > 20) {
             input.addClass('redbd');
			promptcontent(input, "6-20位英文字母或数字");
			return false
		} else {
              input.removeClass('redbd');
			input.parent().find(".post_error").html("").css('opacity', '0');
			return true;
		}
	}
}
/* 检查重复密码 */
function passwordConfirm() {

	var input = $("#userpassword2");

	var value = $.trim(input.val());
	if (input.val() === "") {
		 input.addClass('redbd');
		promptcontent(input, "重复密码不能为空");
		return false;
	} else if (value !== $.trim($("#userpassword").val())) {
		 input.addClass('redbd');
		promptcontent(input, "两次密码输入不一致!");
		return false;
	} else {
		  input.removeClass('redbd');
		input.parent().find(".post_error").html("").css('opacity', '0');
		return true;
	}
}

$(function() {
	$("#userpassword").on("focus", function() {
		var self = $(this);

		promptcontent(self, "6-20位英文字母或数字")
	});
	$("#userpassword").on("blur", function() {
		var self = $(this);

		checkPassword()
	});

	$("#userpassword2").on("focus", function() {
		var self = $(this);

		promptcontent(self, "请再次输入一次")
	});
	$("#userpassword2").on("blur", function() {
		passwordConfirm()
	});
	/*----------点击进入下一步--------------*/
	$("#form_find2").on("submit", function() {

		var formOk =checkPassword() && passwordConfirm();

		if (formOk) {
			return true
		} else {
			return false
		}
	});
});