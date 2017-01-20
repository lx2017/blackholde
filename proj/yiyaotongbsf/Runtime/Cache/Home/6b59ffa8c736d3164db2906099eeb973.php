<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>添加诊所帐号</title>
	<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
	<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/index.css">
</head>
<body>
	<div class="clinicInfos">
  	<header class = "all_header">
      <a class = "h_back" href="javascript:void(0)"><img src="/Public/Home/SaleManager/images/back_icon.png" alt=""></a>
      <span class = "h_title" >添加诊所帐号</span>
      <a class = "h_add" href="javascript:void(0)"></a>
    </header>
    <form action='./addClinicAccount.html' name='form1' method='POST' id='addsale'>
    <input type='hidden' id='clinId' name='clinId' value='<?php echo ($clinId); ?>'>
		<div class="weui-cells">
      <div class="weui-cell">
          <div class="weui-cell__hd"><label class="weui-label">诊所负责人手机号</label></div>
          <div class="weui-cell__bd">
            <input class="weui-input" type="number" name='manager_mobile' pattern="[0-9]*" id='manager_mobile' placeholder="请输入手机号">
          </div>
      </div>
      <div class="weui-cell">
          <div class="weui-cell__hd"><label class="weui-label">诊所负责人</label></div>
          <div class="weui-cell__bd">
              <input class="weui-input" type="text" name='manager_name'  id='manager_name'  placeholder="请输入诊所负责人">
          </div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">诊所名称</label></div>
        <div class="weui-cell__bd">
          <input class="weui-input" type="text" name='clinic_name' id='clinic_name' value='<?php echo ($clinname); ?>' placeholder="请输入诊所名称">
        </div>
      </div>
    </div>
    </form>
    <div class="weui-btn-area" style="background-color:#6db0e4;border-radius:5px">
	    <a class="weui-btn add_clinic_btn" href="javascript:">确定</a>
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
</body>
<script src = "/Public/Home/SaleManager/js/Common.js"></script>
<script>
	/* $(document).on('click','.change_btn',function(){
		$('#addsale').submit();
	}) */
	var sessioncode;
	var nosesstion_url;
	//返回操作
	$(document).on('click','.h_back',function(){
		location.href=document.referrer;
	})
	//删除事件
		$(document).on("click",".add_clinic_btn",function(){
			 var telReg = /^0?1[3|4|5|8][0-9]\d{8}$/;
			var manageerName = $('#manager_name').val(),
	          clinicName = $('#clinic_name').val(),
	          managerMobile = $('#manager_mobile').val();
	      if (telReg.test(managerMobile) === false) { 
	        $("#confirm_dialog").show().find(".weui-dialog__bd").html("手机号输入不合法");
	         return false;  
	      };
	      if (manageerName.trim().length == 0 || clinicName.trim().length == 0) {
	        $("#confirm_dialog").show().find(".weui-dialog__bd").html("请输入负责人或名称");
	        return false;
	      };
			$.ajax({
                url: "",
                type: 'POST',
                data: {'clinId':$("#clinId").val(),'clinic_name':$("#clinic_name").val(),'manager_name':$("#manager_name").val(),'manager_mobile':$("#manager_mobile").val()},
                async: false,
                cache: false,
               	dataType:"json",
                success: function (returndata) {
                	//session失效
               	 	if(returndata.code==2028){
               	 		sessioncode=2028;
               	 		nosesstion_url=returndata.data;
	        	       	 	$('.weui-dialog__bd').html(returndata.msg);
	        	     		$('#confirm_dialog').css('display','block');
               	 	}else{
	               	 	$('.weui-dialog__bd').html(returndata.msg);
	             		$('#confirm_dialog').css('display','block');
               	 	}
                },
                error: function (returndata) {
                		$('.weui-dialog__bd').html(returndata.msg);
             		$('#confirm_dialog').css('display','block');
                }
            });
			$(document).on('click','.weui-dialog__btn_primary',function(){
				if(sessioncode==2028){
					window.location.href=nosesstion_url;
				}else{
					location.href=document.referrer;
				}
			})
		})
</script>
</html>