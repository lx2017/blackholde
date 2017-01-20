<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>修改密码</title>
	<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
	<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/index.css">
</head>
<body>
<header class = "all_header">
			<a class = "h_back" href="javascript:void(0)"><img src="/Public/Home/SaleManager/images/back_icon.png" alt=""></a>
			<span class = "h_title" style="width:500px">首次登录需要修改密码</span>
			<a class = "h_add" href="javascript:void(0)"></a>
		</header>
	<div class="findPassWord_w">
		
		<div class="find_tel_w">
			<span class = "password_icon"></span>
			<div class="weui-cell" style = "margin-left: 25px;">
        <div class="weui-cell__bd">
          <input class="weui-input find_pwd" type="password" name='origin_passwd' id='origin_passwd' placeholder="请输入原密码">
        </div>
       </div>
		
		<div class="find_password_c">
			<span class = "password_icon"></span>
			<div class="weui-cell" style = "margin-left: 25px;">
        <div class="weui-cell__bd">
          <input class="weui-input find_pwd" type="password" name='newpassword'   id='newpassword' placeholder="请输入新密码">
        </div>
       </div>
		</div>
		<div class="find_password_c">
			<span class = "password_icon"></span>
			<div class="weui-cell" style = "margin-left: 25px;">
        <div class="weui-cell__bd">
          <input class="weui-input find_pwd" type="password" name='againpassword'   id='againpassword' placeholder="请再次输入新️密码">
        </div>
       </div>
		</div>

		<div class="login_btn_w">
			<a href="javascript:;" class="weui-btn modify_btn update_passwd">修改密码</a>
		</div>

		<div class="js_dialog" id="find_tips_dialog" style="display: none;">
      <div class="weui-mask"></div>
      <div class="weui-dialog">
        <div class="weui-dialog__bd"></div>
        <div class="weui-dialog__ft">
            <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary find_btns">确 定</a>
        </div>
      </div>
    </div>
    <div class="js_dialog" id="confirm_dialog" style="display: none;">
      <div class="weui-mask"></div>
      <div class="weui-dialog">
        <div class="weui-dialog__bd"></div>
        <div class="weui-dialog__ft">
            <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary">确 定</a>
        </div>
      </div>
    </div>
  <script type="text/javascript">
	var saleManagerStatus; //操作返回状态
	$(document).ready(function() { 
		saleManagerStatus='';
		saleManagerStatus ='<?php echo ($status); ?>';  //success,成功；error,错误
		msg ='<?php echo ($msg); ?>';
		if(saleManagerStatus=='success'||saleManagerStatus=='validatefail'){
			$('.weui-dialog__bd').html(msg);
     		$('#confirm_dialog').css('display','block');
     		//saleManagerStatus='formSubmitTrue';
		}
		if(saleManagerStatus=='error'){ 
			$('.weui-dialog__bd').html(msg);
     		$('#confirm_dialog').css('display','block');
     		//saleManagerStatus='formSubmitTrue';
		}
	}); 
	//关闭弹出框
	$(document).on('click','.weui-dialog__btn_primary',function(){
		$('#confirm_dialog').css('display','none');
		if(saleManagerStatus=='error'){
			history.go(-1);
		}
	}); 
  </script>
	</div>
	<script src = "/Public/Home/SaleManager/js/index.js"></script>
	<script src = "/Public/Home/SaleManager/js/Common.js"></script>
</body>
<script>
var returnStatus;
var returnMsgstatus;

//修改密码按钮触发
$(document).on('click','.update_passwd',function(){
		tel = $("#origin_passwd").val().trim(),
		pwd = $("#newpassword").val().trim();
		againpwd = $("#againpassword").val().trim();

		if (tel.trim().length == 0) {
			$("#find_tips_dialog").show().find(".weui-dialog__bd").html("请输入原始密码");
			return false;
		};
		if (pwd.trim().length == 0) {
			$("#find_tips_dialog").show().find(".weui-dialog__bd").html("请输入密码");
			return false;
		};
		if (againpwd.trim().length == 0) {
			$("#find_tips_dialog").show().find(".weui-dialog__bd").html("请再次输入密码");
			return false;
		};
		if (againpwd.trim()!==pwd.trim()) {
			$("#find_tips_dialog").show().find(".weui-dialog__bd").html("两次密码输入不一致");
			return false;
		};
		$.ajax({
		    url: "/index.php/Home/SaleManager/Login/changePassword",
		    type: 'POST',
		    data: {'originpasswd':tel,'password':pwd},
		    async: false,
		    cache: false,
		   	dataType:"json",
		    success: function (returndata) {
			    	if(returndata.code==2028){
			    		window.location.href=returndata.data;
			    		return false;
			    	}
			    	if(returndata.code==1){
			    		returnStatus=1;
			    		returnMsgstatus=returndata.data;
			    	}
		    		$('.weui-dialog__bd').html(returndata.msg);
		 		$('#confirm_dialog').css('display','block');
		    },
		    error: function (returndata) {
		    		$('.weui-dialog__bd').html(returndata.msg);
		 		$('#confirm_dialog').css('display','block');
		    }
		});
})
$(document).on('click','.weui-dialog__btn_primary',function(){
	$('#confirm_dialog').css('display','none');
	if(returnStatus==1){
		window.location.href=returnMsgstatus;
	}
});
			
</script>
</html>