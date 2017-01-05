var REG_EMAIL = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i; //判断邮箱
var username = $("#useremail"); //电话或者邮箱
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
		$(".post_error").html("邮箱不能为空");
		$(".post_error").css("opacity", '1');
		return false;
	} else {
        var mobile =$("#mobile").attr('value');
		// email
		if (REG_EMAIL.test(value)) {
            var t=0;
            $.ajax({
                type: 'POST',
                url: $("#useremail").attr('_href'),
                data: {"email":value,"mobile":mobile},
                dataType:'json',
                async: false,
                success: function(data){
                    if(data.code ==1002){
                        t=1;
                    }else{
                        $("#useremail").addClass('redbd');
                        $("#useremail").parent().find(".post_error").html('邮箱已经存在').css('opacity', '1');
                        return false;
                    }
                }
            });
            if(t ==1 ){
                username.addClass('redbd');
                $(".post_error").html("");
                $(".post_error").css("opacity", '0');
                return true
            }

		} else {
			 username.removeClass('redbd');
          $(".post_error").html("邮箱格式不正确");
			$(".post_error").css("opacity", '1');
			return false;
		}

	}
}
$(function() {
	$("#useremail").on("focus", function() {
		var self = $(this);

		promptcontent(self, "请输入您的常用邮箱")
	});
	$("#useremail").on("blur", function() {

		 checkUserName()
	});
		/*----------点击进入下一步--------------*/
	$("#form_find").on("submit", function() {
		var formOk = checkUserName();
		if (formOk) {
			return true
		} else {
			return false
		}
	});
});