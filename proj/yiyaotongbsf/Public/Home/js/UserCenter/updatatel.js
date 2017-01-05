var REG_PHONE = /^1[1-9][0-9][0-9]{8}$/; //判断手机号
// 公用提示
function promptcontent(dom, content) {
	var me = $(dom);

	me.parent().find(".post_error").html(content).css('opacity', '1')
}


// 检查用户名是否为空
function checktel() {
	var value = $.trim($("#newtel").val());
	if (value === '') {
		$("#newtel").addClass('redbd');
		$("#newtel").parent().find('.post_error').html("手机号不能为空");
		$("#newtel").parent().find('.post_error').css("opacity", '1');
		return false;
	} else {

		// phone
		if (REG_PHONE.test(value)) {
			$("#newtel").removeClass('redbd');
			$("#newtel").parent().find('.post_error').html("");
			$("#newtel").parent().find('.post_error').css("opacity", '0');
			return true
		} else {
			if (value.length < 11 && value.length > 11) {
				$("#newtel").addClass('redbd');
				$("#newtel").parent().find('.post_error').html("您的手机号码不不正确");
				$("#newtel").parent().find('.post_error').css("opacity", '1');
				return false
			} else {
				$("#newtel").addClass('redbd');
				$("#newtel").parent().find('.post_error').html("您的手机号码不不正确");
				$("#newtel").parent().find('.post_error').css("opacity", '1');
				return false
			}
		}
	}
}
function sendcode(){
    var m =0;
    var mobile =$("#newtel").val();
    $.ajax({
        type: 'POST',
        url: $("#get-code3").attr('_href'),
        data: {"mobile":mobile},
        dataType:'json',
        async: false,
        success: function(data){
            if(data.code ==1002){
                m=1;
                $("#get-code3").addClass('redbd');
                $("#get-code3").parent().find(".post_error").html('验证码已发送').css('opacity', '1');
                return true;
            }else{
                $("#get-code3").addClass('redbd');
                $("#get-code3").parent().find(".post_error").html('验证码发送失败').css('opacity', '1');
                return false
            }
        }
    });
    if(m==1){
        $("#get-code3").removeClass('redbd');
        $("#get-code3").parent().find(".post_error").html("");
        $("#get-code3").parent().find(".post_error").css("opacity", '0');
        return true
    }
}
function checkCode() {
	var value = $.trim($("#telcode").val());
	if (value === '') {
		$("#telcode").addClass('redbd');
		$("#telcode").parent().find(".post_error").html("验证码不能为空");
		$("#telcode").parent().find(".post_error").css("opacity", '1');
		return false;
	} else {
        var t=0;
        $.ajax({
            type: 'POST',
            url: $("#telcode").attr('_href'),
            data: {"code":value,"mobile":mobile},
            dataType:'json',
            async: false,
            success: function(data){
                if(data.code ==1002){
                    t=1;
                    return true;
                }else{
                    $("#telcode").addClass('redbd');
                    $("#telcode").parent().find(".post_error").html('验证码不正确').css('opacity', '1');
                    return false;
                }
            }
        });
        if(t ==1 ){
            $("#telcode").removeClass('redbd');
            $("#telcode").parent().find(".post_error").html("");
            $("#telcode").parent().find(".post_error").css("opacity", '0');
            return true
        }


	}
}
$(function() {
    $("#get-code3").click(function(){
        sendcode()
    });
	$("#newtel").on("blur", function() {
		checktel()
	});
	$("#telcode").on("blur", function() {
		checkCode()
	});
	/*----------点击进入下一步--------------*/
	$("#form_tel").on("submit", function() {
		var formOk = checktel() && checkCode() && sendcode();
		if (formOk) {
			return true
		} else {
			return false
		}
	});
});