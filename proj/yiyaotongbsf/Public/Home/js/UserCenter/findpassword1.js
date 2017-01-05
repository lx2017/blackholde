// 公用提示
function promptcontent(dom, content) {
	var me = $(dom);

	me.parent().find(".post_error").html(content).css('opacity', '1')
}


/*-------密码判断-------*/

function username() {

	var input = $("#number");
	var value = $.trim(input.val());
	if (value === "") {
        input.addClass('redbd');
		promptcontent(input, "用户名不能为空");
		return false;
	} else {

         input.removeClass('redbd');
		input.parent().find(".post_error").html("").css('opacity', '0');
		return true;
	}
}

/* 检查重复密码 */
function checkcode() {

	var input = $("#code");
	var value = $.trim(input.val());
	if (value === "") {
          input.addClass('redbd');
		promptcontent(input, "验证码不能为空");
		return false;
	} else {

          input.removeClass('redbd');
		input.parent().find(".post_error").html("").css('opacity', '0');
		return true;
	}
}
$("#get-code2").click(function(){
    var input = $("#number");
    var mobile = $.trim(input.val());
    var reg= /^[1][34578]\d{9}$/;
    if (mobile === "" || mobile.length != 11) {
        input.addClass('redbd');
        promptcontent(input, "请填写正确手机号");
        return false;
    }else if(!reg.test(mobile)){
        input.addClass('redbd');
        promptcontent(input, "请填写正确手机号");
        return false;
    }else{
        $.ajax({
            type: 'POST',
            url: $("#get-code2").attr('_href'),
            data: {"mobile":mobile},
            dataType: 'json',
            success: function (data) {
                if (data.code == 1002) {
                    return true;
                } else {
                    input.addClass('redbd');
                    promptcontent(input, "验证码发送失败");
                    return false;
                }
            }
        });
    }
});
function checkyzm(){
    var input = $("#code");
    var code = $.trim(input.val());
    var mobile =$("#number").val();
    var bj=0;
    $.ajax({
        type: 'POST',
        url: $("#code").attr('_href'),
        data: {"code": code,"mobile":mobile},
        dataType: 'json',
        async:false,
        success: function (data) {
            if (data.code == 1002) {
               bj=1;
            } else {
                input.addClass('redbd');
                promptcontent(input, "验证码不正确");
                return false;
            }
        }
    });
    if(bj==1){
        return true;
    }
}



$(function() {

	/*----------点击进入下一步--------------*/
	$("#form_find").on("submit", function() {
		var formOk = username() && checkcode() && checkyzm();
		if (formOk) {
			return true
		} else {
			return false
		}
	});
});