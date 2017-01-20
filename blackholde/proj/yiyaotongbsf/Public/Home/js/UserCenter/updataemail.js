var REG_EMAIL = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i; //判断邮箱
var username = $("#new-email"); //电话或者邮箱
// 公用提示
function promptcontent(dom, content) {
	var me = $(dom);

	me.parent().find(".post_error").html(content).css('opacity', '1')
}

// 检查用户名是否为空
function checkUserName() {
	var value = $.trim(username.val());
	if (value === '') {
		username.addClass('redbd');
		username.parent().find(".post_error").html("邮箱不能为空");
		username.parent().find(".post_error").css("opacity", '1');
		return false;
	} else {
		// email
        var mobile=$("#mobile").attr('value');
		if (REG_EMAIL.test(value)) {
            var t=0;
            $.ajax({
                type: 'POST',
                url: $("#new-email").attr('_href'),
                data: {"email":value,"mobile":mobile},
                dataType:'json',
                async: false,
                success: function(data){
                    if(data.code ==1002){
                        t=1;
                    }else{
                        $("#new-email").addClass('redbd');
                        $("#new-email").parent().find(".post_error").html('邮箱已经存在').css('opacity', '1');
                        return false;
                    }
                }
            });
            if(t ==1 ){
                $("#new-email").removeClass('redbd');
                $("#new-email").parent().find(".post_error").html("");
                $("#new-email").parent().find(".post_error").css("opacity", '0');
                return true
            }
        } else {
			username.removeClass('redbd');
			username.parent().find(".post_error").html("邮箱格式不正确");
			username.parent().find(".post_error").css("opacity", '1');
			return false;
		}

	}
}
function sendcode(){
    var m =0;
    var mobile = $("#mobile").attr("value");
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
        var a=0;
        var mobile= $("#mobile").attr('value');
        $.ajax({
            type: 'POST',
            url: $("#telcode").attr('_href'),
            data: {"code":value,"mobile":mobile},
            dataType:'json',
            async: false,
            success: function(data){

                if(data.code ==1002){
                    a=1;
                }else{
                    $("#telcode").addClass('redbd');
                    $("#telcode").parent().find(".post_error").html('验证码不正确').css('opacity', '1');
                    return false;
                }
            }
        });
        if(a ==1 ){
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
	$("#new-email").on("focus", function() {
		var self = $(this);

		promptcontent(self, "请输入您的常用邮箱")
	});
	$("#new-email").on("blur", function() {
		checkUserName()
	});
	/*----------点击进入下一步--------------*/
	$("#form_tel").on("submit", function() {

		var formOk = checkCode() && checkUserName();

		if (formOk) {
			return true
		} else {
			return false
		}
	});
});