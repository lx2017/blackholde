
// 公用提示
function promptcontent(dom, content) {
	var me = $(dom);

	me.parent().find(".post_error").html(content).css('opacity', '1')
}


function sendcode(){
    var m =0;
    var mobile =$("#mobile").text();

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
    var mobile =$("#mobile").text();
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
                    return false
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
	$("#telcode").on("blur", function() {
		checkCode()
	});
	/*----------点击进入下一步--------------*/
	$("#form_tel").on("submit", function() {
		var formOk =checkCode();
		if (formOk) {
			return true
		} else {
			return false
		}
	});
});