<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>添加目标诊所</title>
	<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
	<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/index.css">
</head>
<body>
	<div class="addTargetClinic_w">
    <header class = "all_header">
      <a class = "h_back" href="javascript:"><img src="/Public/Home/SaleManager/images/back_icon.png" alt=""></a>
      <span class = "h_title">添加目标诊所</span>
      <a class = "h_add" href="javascript:void(0)"></a>
    </header>
    <form action='' name='addtargetclinic' id='addtargetclinic' method='post'>
		<div class="weui-cells__title">诊所名称</div>
		<div class="weui-cells">
      <div class="weui-cell">
        <div class="weui-cell__bd">
          <input class="weui-input clinicName" type="text"  name='clinic_name' id='clinic_name' value='<?php echo ($v["clinic_name"]); ?>' placeholder="请输入诊所名称">
        </div>
      </div>
    </div>
    <div class="weui-cells__title">诊所联系人(选填)</div>
		<div class="weui-cells">
      <div class="weui-cell">
        <div class="weui-cell__bd">
          <input class="weui-input clinicPeople" type="text" name='manager_name'  id='manager_name' value='<?php echo ($v["manager_name"]); ?>' placeholder="请输入诊所联系人">
        </div>
      </div>
    </div>
    <div class="weui-cells__title">电话(选填)</div>
		<div class="weui-cells">
      <div class="weui-cell">
        <div class="weui-cell__bd">
          <input class="weui-input clinicTel" type="text" name='clinic_phone' id='clinic_phone' value='<?php echo ($v["clinic_phone"]); ?>'  placeholder="请输入电话">
        </div>
      </div>
    </div>
    <div class="weui-cells__title">地址</div>
    <div class="weui-cells">
      <div class="weui-cell">
        <div class="weui-cell__bd">
          <input class="weui-input clinicAdress" type="text" name='clinic_address'  id='clinic_address' value='<?php echo ($v["clinic_address"]); ?>' placeholder="请输入地址">
        </div>
      </div>
    </div>
</form>
    <div class="weui-btn-area">
      <a class="weui-btn add_btn" href="javascript:">确定</a>
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
  </script> <!-- 弹出框 -->
	</div>
  <script src = "/Public/Home/SaleManager/js/index.js"></script>
  <script>
   $(".h_back").click(function(){
     window.alert("a");
     window.location.href="http://www.baidu.com";
   });
  setTimeout(checkLocation, 100);
  function checkLocation() {
      var checkKey = $('.userkey').val();
      if (checkKey) {
          window.BSFWebView.showInfoFromJs(checkKey);
      }
  }

  function setCoordinate(lng, lat) {
      if (lng) {
          alert("经度：" + lng);
          $('.lng').val(lng);
      }
      if (lat) {
          alert("纬度：" + lat);
          $('.lat').val(lat);
      }
  }
  
	$(document).on("click",".add_btn",function(){
		 var telReg = /^0?1[3|4|5|8][0-9]\d{8}$/;
		var clinicName = $(".clinicName").val()
		if (clinicName.trim().length == 0) {
			$("#confirm_dialog").show().find(".weui-dialog__bd").html("请输入诊所名称");
			return false;	
		}
		var clinicName = $(".clinicName").val(),
		manageerName = $('#manager_name').val(),
		clinicAdress = $('#clinic_address').val(),
		clinicPhone = $('#clinic_phone').val();

		if (telReg.test(clinicPhone) === false &&clinicPhone.trim().length > 0) { 
		$("#confirm_dialog").show().find(".weui-dialog__bd").html("手机号输入不合法");
		 return false;  
		};
		if (clinicName.trim().length == 0 || clinicAdress.trim().length == 0 ) {
			$("#confirm_dialog").show().find(".weui-dialog__bd").html("请输入名称或地址");
			return false;	
		}
		$.ajax({
            url: "",
            type: 'POST',
            data:{'clinic_name':$('#clinic_name').val(),'manager_name':$('#manager_name').val(),'clinic_phone':$('#clinic_phone').val(),'clinic_address':$('#clinic_address').val()},
            async: false,
            cache: false,
           /*  contentType: false, */
           /*  processData: false,  */
          	dataType:"json",
            success: function (returndata) {
            	　$('.weui-dialog__bd').html(returndata.msg);
          	  $('#confirm_dialog').css('display','block');;
            },
            error: function (returndata) {
                alert(returndata);
            }
        });
		$(document).on('click','.weui-dialog__btn_primary',function(){

			location.href='/Home/SaleManager/County/targetClinic';
		});
  
	})

	</script>
</body>
</html>