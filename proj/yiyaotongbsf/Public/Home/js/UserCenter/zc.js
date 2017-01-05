function onlyNum()
	{
	 if(!(event.keyCode==46)&&!(event.keyCode==8)&&!(event.keyCode==37)&&!(event.keyCode==39))
	  if(!((event.keyCode>=48&&event.keyCode<=57)||(event.keyCode>=96&&event.keyCode<=105)))
	    event.returnValue=false;
	}

function dl()
	{
		var zh=$(".phone-sr input").val();
		var mm=$(".mima-code input").val();
        var result=false;
		if (zh=="") {
			alert("手机号不能为空");
            result=false;
		}else{
			var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/; 
			if(!myreg.test($(".phone-sr input").val())) 
			{ 
			    alert('请输入有效的手机号码！');
                result=false;
			}else{
				if (mm=="") {
					alert("密码不能为空");
                    result=false;
				}else{/*验证用户名和密码合法性*/
                    var bj=0;
                    $.ajax({
                        type: 'POST',
                        url: $("#phone").attr('_href'),
                        data: {"password":mm,"number":zh},
                        dataType:'json',
                        async: false,
                        success: function(data){
                            if(data.code ==1002){
                                result=true;
                            }else{
                               alert('用户名或者密码错误');
                                result=false;
                            }
                        }
                    });
                }
			}
		}
		return result;
	}
/*手机找回密码*/
	function sz()                               
    {
    	var zh=$(".phone-xg input").val();
		var yzm=$(".xg-code input").val();
        var password=$("#password").val();
        var bb =false;
        if (zh=="") {
			alert("手机号不能为空");
            bb =false;
		}else{
			var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
			if(!myreg.test($(".phone-xg input").val()))
			{
			    alert('请输入有效的手机号码！');
                bb =false;
			}else{
				if (yzm=="") {
				    alert("验证码不能为空");
                    bb =false;
				}else{/*验证验证码*/
                    $.ajax({
                        type: "POST",
                        url: $("#checkcode").attr('_href'),
                        data: {"code":yzm,"mobile":zh},
                        dataType: 'json',
                        async:false,
                        success: function (data) {
                            if (data.code == 1002) {
                                bb =true;
                            } else {
                                alert('验证码不正确');
                                bb =false;
                            }
                        }
                    });
				}
			}

		}
        if(bb){
            var ls=0;
            if(password.match(/([a-z])+/)){  ls++; }/*验证字母*/
            if(password.match(/([0-9])+/)){  ls++; }/*验证数字*/
            if(password.length <6 || password.length > 20){
                alert('请长度保持在6-20');
                bb =false;
            }else{
                if(ls !=2){
                    alert("密码由数字和字母组成");
                    bb =false;
                }else{
                    var repassword =$("#repassword").val();
                    if(password!==repassword){
                        alert("两次密码不一致");
                        bb =false;
                    }else{
                        bb =true;
                    }
                }
            }
        }
        return bb;
    }



    function msg()                               
    {
        var password =$(".password input").val();
        var ls=0;
        if(password.match(/([a-z])+/)){  ls++; }/*验证字母*/
        if(password.match(/([0-9])+/)){  ls++; }/*验证数字*/
        if(password ==''){
            alert('请填写密码');
            return false;
        }
        if(password.length <6 || password.length > 20){
            alert('请长度保持在6-20');
            return false;
        }
        if(ls !=2){
            alert("密码由数字和字母组成");
            return false;
        }
        return true;
    }
var wait = 60;

document.getElementById("timeyzm").disabled = false;

function time(o) {
    if (wait == 0) {
        o.removeAttribute("disabled");
        o.value = "获取验证码";
        wait = 60;
    } else {
        o.setAttribute("disabled", true);
        o.value = "重新发送(" + wait + ")";
        wait--;
        setTimeout(function() {
                time(o)
            },
            1000)
    }
}

    function sendyzm(){
        var zh = $(".phone-n input").val();
        if (zh == '' || zh.length != 11) {
            alert("请填写正确的手机号");
            $("#number").focus();
            return false;
        }
        var reg = /^[1][34578]\d{9}$/;
        if (!reg.test(zh)) {
            alert('请输入正确的手机号！');
            $("#number").focus();
            return false;
        }
        $.ajax({
            type: 'POST',
            url: $("#yzm").attr('_href'),
            data: {"mobile":zh},
            dataType: 'json',
            async:false,
            success: function (data) {
                if(data.code == 1002){
                    alert("发送成功");
                    return true;
                }else if(data.code == 1007){
                    alert("用户已经存在");
                    return false;
                }else{
                    alert('发送失败');
                    return false;
                }
            }
        });

    }
/*验证手机号找回密码*/
    function checkmobile(){
        var zh=$(".phone-xg input").val();
        var boot =false;
        var user =false;
        if (zh == '' || zh.length != 11) {
            alert("请填写正确的手机号");
            $("#number").focus();
            boot =false;
        }
        var reg = /^[1][34578]\d{9}$/;
        if (!reg.test(zh)) {
            alert('请输入正确的手机号！');
            $("#number").focus();
            boot =false;
        }
        $.ajax({
            type: 'POST',
            url: $("#number").attr('_href'),
            data: {"mobile":zh},
            dataType: 'json',
            async:false,
            success: function (data) {
                if(data.code == 1006){
                    alert('用户不存在');
                    boot =false;
                }else{
                    user = true;
                }
            }
        });
        if(user){
            /*发送验证码*/
            $.ajax({
                type: 'POST',
                url: $("#code").attr('_href'),
                data: {"mobile": zh},
                dataType: 'json',
                success: function (data) {
                    if(data.code ==1002){
                        alert('发送成功');
                        boot =true;
                    }else {
                        alert('发送失败');
                        boot =false;
                    }
                }
            });
        }
        return boot;
    }

    function zc(){
        var result = false;
        var zh=$(".phone-n input").val();
		var yzm=$(".id-code input").val();
		if (zh=="") {
			alert("手机号不能为空");
            result = false;
		}else{
			var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/; 
			if(!myreg.test($(".phone-n input").val())) 
			{ 
			    alert('请输入有效的手机号码！');
                result = false;
			}else{
				if (yzm=="") {
					alert("验证码不能为空");
                    result = false;
				}else{
                    $.ajax({
                        type: "POST",
                        url: $("#number").attr('_href'),
                        data: {"code":yzm,"mobile":zh},
                        dataType: 'json',
                        async:false,
                        success: function (data) {
                            if (data.code == 1002) {
                                result = true;
                            } else {
                                alert('验证码不正确');
                                result = false;
                            }
                        }
                    });
                }
			} 
			
		}
        return result;
    }


    