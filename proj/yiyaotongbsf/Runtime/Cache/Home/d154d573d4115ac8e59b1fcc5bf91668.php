<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>目标诊所</title>
	<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
	<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/index.css">
	<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/dropload.css">
</head>
<body>
	<div class="targetClinic_w">
    <header class = "all_header">
      <a class = "h_back_1 targetBack" style='flex: 1;' href="javascript:void(0);" data-role-id='<?php echo ($myself["role_id"]); ?>'><img src="/Public/Home/SaleManager/images/back_icon.png" alt="" style="width:30px;vertical-align: middle;"></a>
      <span class = "h_title">目标诊所</span>
      <a class = "h_add" href="./addTargetClinic.html">添加目标诊所</a>
    </header>
    <div class="weui-panel__bd">
     <?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
     
        <div class="weui-media-box__hd">
        <?php if($v["clinic_pic"] != ''): ?><img class="weui-media-box__thumb" src="<?php echo ($v["clinic_pic"]); ?>" alt="">
        		<?php else: ?><img class="weui-media-box__thumb" src="/Public/Home/SaleManager/images/demo.png" alt=""><?php endif; ?>
          
        </div>
        
        <div class="weui-media-box__bd">
        	<div class="cli_w_name">
            <h4 class="weui-media-box__title"><?php echo ($v["clinic_name"]); ?></h4>
            <span class = "clinic_del" data-clinicid=<?php echo ($v["id"]); ?>>删除</span>
          </div>
          <div class="cli_w_num">
            <span>地址：</span>
          	<span><?php echo ($v["clinic_address"]); ?></span>
          </div>
          <div class="cli_w_num">
            <span>联系人：</span>
            <span><?php echo ($v["manager_name"]); ?></span>
          </div>  
          <div class="weui-media-box__desc">
            <div class="cli_w_num cli_w_tel">
              <div style = "flex: 1;">
                <span>电话：</span>
                <span><?php echo ($v["clinic_phone"]); ?></span>
              </div>
              <!--  <span>成功</span>  -->
            </div>
            <div class="weui-cell__ft">
              <input class="weui-switch" type="checkbox" data-clinic-id='<?php echo ($v["id"]); ?>'>
            </div>
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
    </div>
	</div>
	<script src = "/Public/Home/SaleManager/js/index.js"></script>
	<script src = "/Public/Home/SaleManager/js/dropload.min.js"></script>
	<script src = "/Public/Home/SaleManager/js/Common.js"></script>
	<script>
	
		//删除目标诊所
		$(document).on('click','.clinic_del',function(){
			$id = $(this).attr('data-clinicid');
			//删除事件
				$.ajax({
	                url: "/Home/SaleManager/County/delClinic",
	                type: 'POST',
	                data: {'id':$id},
	                async: false,
	                cache: false,
	               /*  contentType: false, */
	               /*  processData: false,  */
	               dataType:"json",
	                success: function (returndata) {
	              		　$('.weui-dialog__bd').html("删除成功");
	               		 $('#confirm_dialog').css('display','block');
	                },
	                error: function (returndata) {
	                    alert(returndata);
	                }
	            });
			})
			
			//下拉刷新
		var LoadEnd = false;
	 // 每页展示10个
		var counter = 0;
		var num = 10;
    		var pageStart = 0;
		var dropload =$('.targetClinic_w').dropload({
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
	                	 		vhtml+='<div class="weui-media-box__hd">';
	                	 		if(val.clinic_pic!=null &&val.clinic_pic!=''){
	                	 			vhtml+='<img class="weui-media-box__thumb" src="'+val.clinic_pic+'" alt="">';
	                	 		}else{
	                	 			vhtml+='<img class="weui-media-box__thumb" src="/Public/Home/SaleManager/images/demo.png" alt="">';
	                	 		}
	                	 		vhtml+='</div>';
	                	 		vhtml+='<div class="weui-media-box__bd">';
	                	        vhtml+='<div class="cli_w_name">';
	                	        vhtml+='<h4 class="weui-media-box__title">'+val.clinic_name+'</h4>';
	                	        vhtml+='<span class = "clinic_del" data-clinicid='+val.id+'>删除</span></div><div class="cli_w_num"><span>地址：</span>';
	                	        vhtml+='<span>'+val.clinic_address+'</span> </div><div class="cli_w_num"><span>联系人：</span>';
	                	        vhtml+='<span>'+val.manager_name+'</span></div><div class="weui-media-box__desc"><div class="cli_w_num cli_w_tel">';
	                	        vhtml+='<div style = "flex: 1;"><span>电话：</span><span>'+val.clinic_phone+'</span></div></div>';
	                	        vhtml+='<div class="weui-cell__ft">';
	                	        vhtml+=' <input class="weui-switch" type="checkbox" data-clinic-id="'+val.id+'">';
	                	        vhtml+=' </div> </div></div> </a>';                  	     
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
</body>
</html>