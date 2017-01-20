// 公用提示
function promptcontent(dom, content) {
	var me = $(dom);

	me.parent().find(".post_error").html(content).css('opacity', '1')
}


/*-------密码判断-------*/
function proPassword() {

	var input = $("#pro-userpassword");
	var value = $.trim(input.val());
    var mobile=$("#mobile").attr('value');
	if (value === "") {
         input.addClass('redbd');
		promptcontent(input, "原始密码不能为空");
		return false;
	} else {
        var t=0;
        $.ajax({
            type: 'POST',
            url: $("#pro-userpassword").attr('_href'),
            data: {"password":value,"mobile":mobile},
            dataType:'json',
            async: false,
            success: function(data){
                if(data.code ==1002){
                    t=1;
                    return true;
                }else{
                    $("#pro-userpassword").addClass('redbd');
                    $("#pro-userpassword").parent().find(".post_error").html('原始密码不正确').css('opacity', '1');
                    return false;
                }
            }
        });
        if(t ==1 ){
            input.removeClass('redbd');
            input.parent().find(".post_error").html("").css('opacity', '0');
            return true;
        }

	}
}

function checkPassword() {

	var input = $("#new-password");
	var value = $.trim(input.val());
	if (value === "") {
          input.addClass('redbd');
		promptcontent(input, "密码不能为空");
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

	var input = $("#new-password1");

	var value = $.trim(input.val());
	if (input.val() === "") {
		 input.addClass('redbd');
		promptcontent(input, "重复密码不能为空");
		return false;
	} else if (value !== $.trim($("#new-password").val())) {
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
	$("#new-password").on("focus", function() {
		var self = $(this);

		promptcontent(self, "6-20位英文字母或数字")
	});
	$("#new-password").on("blur", function() {
		var self = $(this);

		checkPassword()
	});
	$("#pro-userpassword").on("blur", function() {
		proPassword()
	});

	$("#new-password1").on("focus", function() {
		var self = $(this);

		promptcontent(self, "请再次输入一次")
	});
	$("#new-password1").on("blur", function() {
		passwordConfirm()
	});
	/*----------点击进入下一步--------------*/
	$("#form_find2").on("submit", function() {

		var formOk = proPassword() && checkPassword() && passwordConfirm();

		if (formOk) {
			return true
		} else {
			return false
		}
	});
});