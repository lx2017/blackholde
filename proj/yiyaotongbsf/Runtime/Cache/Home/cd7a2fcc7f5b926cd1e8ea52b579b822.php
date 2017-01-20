<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>我的诊所</title>
	<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
	<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/index.css">
	<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/dropload.css">
</head>
<body>
	<div class="clinic">
    <header class = "all_header">
      <a class = "h_back clinicBack" href="javascript:void(0)"><img src="/Public/Home/SaleManager/images/back_icon.png" alt=""></a>
      <span class = "h_title">我的诊所</span>
      <a class="h_add" href="javascript:void(0);"></a>
    </header>
    <div class="weui-panel__bd">
    <?php if(is_array($clinicList)): $i = 0; $__LIST__ = $clinicList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
        <div class="weui-media-box__hd clinic_img" data-clinic-id='<?php echo ($v["id"]); ?>'>
          <img class="weui-media-box__thumb" src="/Public/Home/SaleManager/images/demo.png" alt="">
        </div>
        
        <div class="weui-media-box__bd" data-clinic-id='<?php echo ($v["id"]); ?>'>
        	<div class="cli_w_name">
            <h4 class="weui-media-box__title"><?php echo ($v["clinic_name"]); ?></h4>
           <!--  <?php if($v["clinic_pic"] != ''): ?><div class="cli_del"><img src="<?php echo ($v["clinic_pic"]); ?>" alt=""></div>
            <?php else: ?> -->
            		<div class="cli_del" data-clinic-id='<?php echo ($v["id"]); ?>'><img src="/Public/Home/SaleManager/images/del_btn.png" alt=""></div>
          <!--<?php endif; ?> -->
            
         </div>
          <div class="cli_w_num">
          	<span>诊断量：<span><?php echo ($v["clinic_treatment_volume"]); ?></span></span>
          	<span>评分：<span><?php echo ($v["clinic_score"]); ?></span></span>
          </div>	
          <div class="weui-media-box__desc"><?php echo ($v["clinic_introduction"]); ?>
          </div>
        </div>
      </a><?php endforeach; endif; else: echo "" ;endif; ?>
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
	</div>
	<script src = "/Public/Home/SaleManager/js/index.js"></script>
	<script src = "/Public/Home/SaleManager/js/dropload.min.js"></script>
</body>
<script>
		$(".clinic").on("click",".cli_del",function (e) {
			
			$clinic=$(this);
			$.ajax({
                url: "clinic",
                type: 'get',
                data: {'clinicid':$(this).attr('data-clinic-id')},
                async: false,
                cache: false,
               	dataType:"json",
                success: function (returndata) {
                	//session失效
                	// 我的业务员
					
               	 	if(returndata.code==2028){
               	 		sessioncode=2028;
               	 		nosesstion_url=returndata.data;
	        	       	 	$('.weui-dialog__bd').html(returndata.msg);
	        	     		$('#confirm_dialog').css('display','block');
               	 	}else{
               	 		if(returndata.code==1){
               	 			$clinic.parents(".weui-media-box").remove();
	               	 		$('.weui-dialog__bd').html('删除成功');
		             		$('#confirm_dialog').css('display','block');
               	 		}else{
	               	 		$('.weui-dialog__bd').html('删除失败');
		             		$('#confirm_dialog').css('display','block');
               	 		}
	               	 	
               	 	}
                		
                },
                error: function (returndata) {
                		$('.weui-dialog__bd').html("未知错误");
             		$('#confirm_dialog').css('display','block');
                }
            });
			
		});
		
		
		//下拉刷新
		var LoadEnd = false;
	 // 每页展示10个
		var counter = 0;
		var num = 10;
    		var pageStart = 0;
		var dropload =$('.clinic').dropload({
			scrollArea : window,
			loadDownFn : function(me){
				if(counter==0){
					counter++;
					 pageStart = counter*num;
                     me.resetload();
					return false;
				}
				// 锁定
                me.lock();
		        $.ajax({
		            type: 'GET',
		            url: '',
		            data:{'pageStart':pageStart},
		            dataType: 'json',
		            success: function(data){
		            		var vhtml='';
		            		counter++;
                        pageStart = counter*num+1;
	                	 	LoadEnd=true;
	                	 	if(data.data==null ||data.data=='undefined'){
	                	 		 me.noData();
	                	 		 me.unlock()
	    		                // 代码执行后必须重置
	    		                 me.resetload();
	                	 		 return false;
	                	 	}
	                	 	$(data.data).each(function(i,val) { 
	                	 		vhtml+='<a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">';
	                	 		vhtml+='<div class="weui-media-box__hd clinic_img" data-clinic-id="'+val.id+'">';
	                	 		if(val.clinic_pic!=null&&val.clinic_pic!=''){
	                	 			vhtml+='<img class="weui-media-box__thumb" src="'+val.clinic_pic+'" alt="">';
	                	 		}else{
	                	 			vhtml+='<img class="weui-media-box__thumb" src="/Public/Home/SaleManager/images/demo.png" alt="">';
	                	 		}
	                	 		vhtml+='</div><div class="weui-media-box__bd" data-clinic-id="'+val.id+'">';
	                	 		vhtml+='	<div class="cli_w_name"><h4 class="weui-media-box__title">'+val.clinic_name+'</h4>';
	                	 		vhtml+='<div class="cli_del" data-clinic-id="'+val.id+'"><img src="/Public/Home/SaleManager/images/del_btn.png" alt=""></div>';
	                	 		vhtml+='</div><div class="cli_w_num"><span>预约量：<span></span></span>';
	                	 		vhtml+='<span>评分：<span>'+val.clinic_score+'</span></span> </div>';
	                	 		vhtml+='<div class="weui-media-box__desc"></div>';
	                	 		vhtml+='</div></a>';
						}); 
	                	 	if(vhtml!=''){
	                	 		$('.weui-panel__bd').append(vhtml);
	                	 	}else{
	                	 		// 无数据
	                         me.noData();
	                	 	}
	                		me.unlock()
		                // 代码执行后必须重置
		                me.resetload();
		            },
		            error: function(xhr, type){
		            		me.unlock()
                        // 无数据
                        me.noData();
		                me.resetload();
		            }
		        });
		    }
		});
</script>
</html>