var myreg = /^1\d{10}$/; //手机格式验证
var chrnum = /^([a-zA-Z0-9]+)$/;

var telphone = $("#number"); //电话
var Icode = $("#getnum"); //邀请码
var yznum = $("#yznum"); //验证码


// 公用提示
function promptcontent(dom, content) {
    var me = $(dom);

    me.parent().find(".post_error").html(content).css('opacity', '1')
}

// 检查手机号是否为空
function tel() {

    if ($.trim(telphone.val()) === '') {
        telphone.addClass('redbd');
        telphone.parent().find(".post_error").html('您的手机号码不能为空').css('opacity', '1');

        return false;
    } else {
        if (!myreg.test(telphone.val())) {
            telphone.parent().find(".post_error").html('您的手机号码不正确').css('opacity', '1');
            telphone.addClass('redbd');
            return false;
        } else {
            var t=0;
            var mobile =$.trim(telphone.val());
            $.ajax({
                type: 'POST',
                url: $("#number").attr('_href'),
                data: {"mobile":mobile},
                dataType:'json',
                async: false,
                success: function(data){
                    if(data.code ==1006){
                        telphone.parent().find(".post_error").html('').css('opacity', '0');
                        telphone.removeClass('redbd');
                        t=1;
                        return true;
                    }else{
                        telphone.parent().find(".post_error").html('用户已注册').css('opacity', '1');
                        telphone.addClass('redbd');
                        return false;
                    }
                }
            });
            if(t ==1 ){
                telphone.parent().find(".post_error").html('').css('opacity', '0');
                telphone.removeClass('redbd');
                return true
            }

        }

    }
}
/*检测用户是否存在*/
function checkuser(){
    var t=0;
    var mobile =$.trim(telphone.val());
    $.ajax({
        type: 'POST',
        url: $("#number").attr('_href'),
        data: {"mobile":mobile},
        dataType:'json',
        async: false,
        success: function(data){
            if(data.code ==1006){
                telphone.parent().find(".post_error").html('').css('opacity', '0');
                telphone.removeClass('redbd');
                t=1;
                return true;
            }else{
                telphone.parent().find(".post_error").html('用户已注册').css('opacity', '1');
                telphone.addClass('redbd');
                return false;
            }
        }
    });
    if(t ==1 ){
        telphone.parent().find(".post_error").html('').css('opacity', '0');
        telphone.removeClass('redbd');
        return true
    }
}
// 检查验证码是否为空
function checkVeryfycode() {
    var mobile =$.trim(telphone.val());
    if ($.trim($("#code-num").val()) === '') {
        $("#code-num").addClass('redbd');
        $("#code-num").parent().find(".post_error").html('您的验证码不能为空').css('opacity', '1');
        return false
    } else {
        var t=0;
        $.ajax({
            type: 'POST',
            url: $("#code-num").attr('_href'),
            data: {"code":$.trim($("#code-num").val()),"mobile":mobile},
            dataType:'json',
            async: false,
            success: function(data){
                if(data.code ==1002){
                    t=1;
                    return true;
                }else{
                    $("#code-num").addClass('redbd');
                    $("#code-num").parent().find(".post_error").html('验证码不正确').css('opacity', '1');
                    return false
                }
            }
        });
        if(t ==1 ){
            $("#code-num").removeClass('redbd');
            $("#code-num").parent().find(".post_error").html("").css('opacity', '0');
            return true;
        }

    }
}



/*-------密码判断-------*/
function checkPassword() {

    var input = $("#userpassword");
    var value = $.trim(input.val());
    if (value === "") {
        input.addClass('redbd');
        promptcontent(input, "密码不能为空");
        return false;
    } else {


        if (value.length < 6 || value.length > 20) {
            input.addClass('redbd');
            promptcontent(input, "6-20位英文字母或数字")
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
        input.parent().find(".post_error").html("").css('opacity', '0');
        input.removeClass('redbd');
        return true;
    }
}


$("#number").on("focus", function() {
    var self = $(this);

    promptcontent(self, "您的常用手机号")
});
$("#number").on("blur", function() {
    var self = $(this);
    tel()
});
$("#code-num").on("focus", function() {
    var self = $(this);

    promptcontent(self, "您的手机验证码")
});
$("#code-num").on("blur", function() {
    checkVeryfycode()
});
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



var countdown2 = 60;

function settime2(val) {
    if (countdown2 == 0) {

        $("#code-num").attr("disabled", 'disabled');
        $("#get-code").removeClass('geted').html("发送验证码");
        countdown2 = 60;
        return false
    } else {
        $("#code-num").removeAttr("disabled");
        values = "" + countdown2 + "后可重新发送";
        $("#get-code").html(values);
        countdown2--;
    }
    setTimeout(function() {
        settime2(val)
    }, 1000)
}
/*发送验证码*/
function checkmobilecode(){
    var t=0;
    var mobile =$.trim(telphone.val());
    $.ajax({
        type: 'POST',
        url: $("#get-code").attr('_href'),
        data: {"mobile":mobile},
        dataType:'json',
        async: false,
        success: function(data){
            if(data.code ==1002){
                telphone.parent().find(".post_error").html('').css('opacity', '0');
                telphone.removeClass('redbd');
                t=1;
                return true;
            }else{
                telphone.parent().find(".post_error").html('发送失败').css('opacity', '1');
                telphone.addClass('redbd');
                return false;
            }
        }
    });
    if(t ==1 ){
        $("#code-num").removeClass('redbd');
        $("#code-num").parent().find(".post_error").html("").css('opacity', '0');
        return true;
    }
}
$(function() {
    // 发送验证码
    $("#get-code").click(function() {
        var me = $(this);
        var isOk = tel();
        var issend = checkmobilecode();

        if (me.hasClass("geted")) {
            return false
        } else {
            if (isOk && issend) {
                $("#code-num").removeAttr("disabled");
                $("#get-code").addClass('geted');
                var av = $("#get-code");
                settime2(av)
            } else {
                tel()
            }

        }

    });
    /*----------点击进入下一步--------------*/
    $("#form_register").on("submit", function() {

        var formOk =  tel() && checkVeryfycode() && checkPassword() && passwordConfirm();
        if (formOk) {
            return true
        } else {
            return false
        }
    });


});