<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>我的业务员</title>
	<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
	<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/index.css">
</head>
<body>
	<div class="noClinic">
		<header class = "all_header">
         <a class = "h_back_1 targetBack" style='flex: 1;' href="javascript:void(0); data-role-id='<?php echo ($myself["role_id"]); ?>'"><img src="/Public/Home/SaleManager/images/back_icon.png" alt="" style="width:30px;vertical-align: middle;"></a>
        <span class = "h_title">目标诊所</span>
        <a class = "h_add" href="./addTargetClinic.html">添加目标诊所</a>
    </header>
		<div class="no_img">
			<img src="/Public/Home/SaleManager/images/no_clinic.png" alt="">
		</div>
		<div class="no_tips">
			暂无目标诊所，请添加
		</div>
	</div>




	<!-- 弹框 -->
	<a href="javascript:;" class="weui-btn weui-btn_default" id="showIOSDialog2">iOS Dialog</a>

	<div class="js_dialog" id="iosDialog2" style="display: none;">
      <div class="weui-mask"></div>
      <div class="weui-dialog">
          <div class="weui-dialog__bd">弹窗内容，告知当前状态、信息和解决方法，描述文字尽量控制在三行内</div>
          <div class="weui-dialog__ft">
              <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary">知道了</a>
          </div>
      </div>
  </div>

  <script>
  	var $iosDialog2 = $('#iosDialog2');
  	$iosDialog2.on('click', '.weui-dialog__btn', function(){
        $(this).parents('.js_dialog').hide(200);
    });
  	$('#showIOSDialog2').on('click', function(){
        $iosDialog2.show(200);
    });
  </script>
 <script src = "/Public/Home/SaleManager/js/Common.js"></script>
</body>
</html>