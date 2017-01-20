<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>登录</title>
	<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
	<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/index.css">
</head>
<body>
<form action='' method='POST' name='login' id='login'>
	<div class="login_w">
		<div class="login_icon_w">
			<img src="/Public/Home/SaleManager/images/logo_icon.png" alt="">
			<!-- <span></span> -->
		</div>
		<div class="login_c">
			<div class="weui-cells">
				<span class = "tel_c"></span>
        <div class="weui-cell">
          <div class="weui-cell__bd">
            <input class="weui-input login_tel" type='text' name='phone'  id='phone' pattern="[0-9]*" value='<?php echo ($data["phone"]); ?>' placeholder="请输入手机号">
          </div>
        </div>
      </div>
      <div class="weui-cells">
				<span class = "pas_c"></span>
        <div class="weui-cell">
          <div class="weui-cell__bd">
            <input class="weui-input login_pwd" type="password" name='password'  id='password' placeholder="密码为6-12位数字或者字母">
          </div>
        </div>
        <span class = "pas_show"></span>
      </div>

			<div class="login_btn_w">
				<a href="javascript:;" class="weui-btn login_btn">登 陆</a>
				<div class="forget_pas">
					<a href="/Home/SaleManager/Login/findPassword.html">忘记密码?</a>
				</div>
			</div>
		</div>
		<div class="js_dialog" id="login_dialog" style = "display: none">
      <div class="weui-mask"></div>
      <div class="weui-dialog">
        <div class="weui-dialog__bd"></div>
        <div class="weui-dialog__ft">
          <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary login_dia_btns">确 定</a>
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
</form>
	<script src = "/Public/Home/SaleManager/js/index.js"></script>
</body>
<script>
	/* $(document).on('click','.login_btn',function(){
		$('#login').submit();
	}) */
</script>
</html>