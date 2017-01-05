var REG_EMAIL = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i; //判断邮箱
var REG_tel = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/; //手机验证
// 检查用户名是否为空
function checkUserEmail() {
	var value = $.trim($("#i_email").val());
	if (value === '') {

		$("#dom_error_wrap").html('请输入您的邮箱!').css("display", 'block');
		return false;
	} else {
		// email
		if (REG_EMAIL.test(value)) {
			$("#dom_error_wrap").html('').css("display", 'none');
			return true
		} else {
			$("#dom_error_wrap").html('您的邮箱格式不正确!').css("display", 'block');
			return false;
		}

	}
}
// 检查手机
function checkmobile(iscode) {
	var mobile = $.trim($("#i_mobile").val());
	var mbCode = $.trim($("#i_smsCode").val());

	var dom = '请正确输入11位手机号码' + '</br>' + '验证码只能为4个数字';

	if (!REG_tel.test(mobile)) {

		$("#dom_error_wrap").html(dom).css("display", 'block');
		return false
	} else {
		if (mbCode == "" || mbCode.length != 4) {
			$("#dom_error_wrap").html('请收入手机收到的6位验证码').css("display", 'block');
			return false
		} else {
            $.ajax({
                type: 'POST',
                url:$("#btn_modify_mobile").attr('_href'),
                data: {"mobile":mobile,"code":mbCode},
                dataType:'json',
                async: false,
                success: function (data) {
                    if(data.code==1005){
                        alert('验证码不正确');
                        return false;
                    }
                }
            });
			$("#dom_error_wrap").html('').css("display", 'none');
			return true
		}

	}
}
//60秒倒计时

var countdown = 60;

function settime(val) {
	if (countdown == 0) {

		$("#btn_get_sms").attr("disabled", 'disabled');
		$("#btn_get_sms").removeClass('btned').html("发送验证码");
		countdown = 60;
		return false
	} else {
		$("#btn_get_sms").removeAttr("disabled");
		values = "" + countdown + "秒后重发";
		$("#btn_get_sms").html(values);
		countdown--;
	}
	setTimeout(function() {
		settime(val)
	}, 1000)
}
var inputTxt = function(txt, txtname) {
		if (txt == "" || txt.length < 2) {
			$("#dom_error_wrap").html(txtname).css("display", 'block')
		} else {
			$("#dom_error_wrap").html('').css("display", 'none')
		}
	};
	/*-------密码判断-------*/
function proPassword() {

	var input = $("#i_password");
	var value = $.trim(input.val());
    var  id =$("#userid").val();
	if (value === "") {
		$("#dom_error_wrap").html('请收入您的初始密码').css("display", 'block');
		return false;
	} else {
        var t=0;
        $.ajax({
            type: 'POST',
            url:$("#dom_error_wrap").attr('_href'),
            data: {"password":value,"id":id},
            dataType:'json',
            async: false,
            success: function (data) {
                if(data.code ==1007){
                  t=1;
                }else{
                    $("#dom_error_wrap").html('原始密码不正确').css("display", 'block');
                    return false;
                }
            }
        });
        if(t == 1){
            return true;
        }
	}
}

function checkPassword() {

	var input = $("#i_passwordnew");
	var value = $.trim(input.val());
	if (value === "") {
		$("#dom_error_wrap").html('请输入您的新密码').css("display", 'block');
		return false;

	} else {


		if (value.length < 6 || value.length > 20) {
			$("#dom_error_wrap").html('密码至少为6位数字').css("display", 'block');
			return false
		} else {
			$("#dom_error_wrap").html('').css("display", 'none');
			return true;
		}
	}
}
/* 检查重复密码 */
function passwordConfirm() {

	var input = $("#i_repassword");

	var value = $.trim(input.val());
	if (input.val() === "") {

		$("#dom_error_wrap").html('重复密码不能为空').css("display", 'block');
		return false;
	} else if (value !== $.trim($("#i_passwordnew").val())) {

		$("#dom_error_wrap").html('两次密码输入不一致!').css("display", 'block');
		return false;
	} else {
		$("#dom_error_wrap").html('').css("display", 'none');
		return true;
	}
}

$(function() {

	/*-----------保存用户名----------*/
	$("#btn_save_name").click(function() {
			var name = $.trim($("#i_nickname").val());
			inputTxt(name, '昵称最少2个字符');
            $("#form_nic").submit();
		});
		/*-----------保存性别----------*/
	$("#sex-list li").click(function() {

		var me = $(this);
		var sex = $.trim(me.find("span").text());
		$("#sex-list li").find("i").removeClass("selected");
		me.find("i").addClass("selected");
		$("#sexed").val(sex)

	});
	$("#btn_save_sex").click(function() {
			var name = $.trim($("#sexed").val());
            $("#mysex").val(name);
            $("#form_sex").submit();
		});
		/*-----------保存性别----------*/
		/*-----------我的个性签名----------*/
	$("#btn_nn").click(function() {
			var name = $.trim($("#w-txt2").val());
			inputTxt(name, '签名最少2个字符');
            $("#form_mysig").submit();

		});
		/*-----------邮箱验证----------*/
	$("#btn_save_email").click(function() {
			var email = $.trim($("#i_email").val());
			var isSave = checkUserEmail();
			if (isSave) {
				return true
			} else {
				return false
			}
		});
		/*-----------修改手机----------*/
	$("#btn_modify_mobile").click(function() {
			var mobile = $.trim($("#i_mobile").val());
			var ismobile = checkmobile();
			if (ismobile) {
                $("#form_tel").submit();
				return true;
			} else {
				return false;
			}
		});
		// 发送验证码
	$("#btn_get_sms").click(function() {
			var me = $(this);
			var mobile = $.trim($("#i_mobile").val());
			if (me.hasClass("btned")) {
				return false
			} else {
				if (!REG_tel.test(mobile)) {
					alert("请输入有效的手机号码")
				} else {
                    $.ajax({
                        type: 'POST',
                        url:$("#i_mobile").attr('_href'),
                        data: {"mobile":mobile},
                        dataType:'json',
                        async: false,
                        success: function (data) {
                            if(data.code==1007){
                                alert('该用户已经存在');
                                return false;
                            }
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url:$("#sendyzm").attr('_href'),
                        data: {"mobile":mobile},
                        dataType:'json',
                        async: false,
                        success: function (data) {
                            if(data.code==1005){
                                alert('发送失败');
                                return false;
                            }
                        }
                    });
					$("#btn_get_sms").removeAttr("disabled");
					$("#btn_get_sms").addClass('btned');
					var DomAttr = $("#btn_get_sms");
					settime(DomAttr)
				}

			}

		});
		// 发送验证码
		/*-----------修改密码----------*/
	$("#form_password").on("submit", function() {
			var formOk = proPassword() && checkPassword() && passwordConfirm();
			if (formOk) {
				return true
			} else {
				return false
			}

		});
		/*-----------保存省市区----------*/
	$("#form_addr").on("submit", function() {
		var provinceId = $("#provinceId option:selected").text(); //获取省份值
		var cityId = $("#cityId option:selected").text(); //获取城市值
		var districtId = $("#districtId option:selected").text(); //获取地区值
		var address = $.trim($("#address-info").val()); //获取详情值


		if (provinceId.indexOf('请选择') != "-1") {

			$("#dom_error_wrap").html('请选择省份').css("display", 'block');
			return false
		} else if (cityId.indexOf('请选择') != "-1") {
			$("#dom_error_wrap").html('请选择城市').css("display", 'block');
			return false
		} else if (districtId.indexOf('请选择') != "-1") {
			$("#dom_error_wrap").html('请选择地区').css("display", 'block');
			return false
		}
		else if (address =="" || address.length < 2) {
			$("#dom_error_wrap").html('详细地址最少两个字符').css("display", 'block');
			return false
		}
		else{
			return true
		}
	});
	/*-----------保存省市区----------*/
});