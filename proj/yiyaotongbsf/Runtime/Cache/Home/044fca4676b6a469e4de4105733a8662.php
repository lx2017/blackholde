<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
	content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta content="yes" name="apple-mobile-web-app-capable">
<meta content="black" name="apple-mobile-web-app-status-bar-style">
<title>诊所订单</title>
<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css"
	rel="stylesheet">
<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/index.css">
<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/dropload.css">
</head>
<body>
<div class='dorp_down_fororder'>
	<div class="clinicOrder">
		<header class="all_header">
			<a class = "h_back_1 orderlistfor" style='flex: 1;' href="javascript:void(0); data-role-id='<?php echo ($myself["role_id"]); ?>'"><img src="/Public/Home/SaleManager/images/back_icon.png" alt="" style="width:30px;vertical-align: middle;"></a>
				<span class="h_title"><?php echo ($title); ?></span> <a class="h_add"
				href="javascript:void(0)"></a>
		</header>
		<div class="weui-tab">
			<div class="weui-navbar">
				<div class="weui-navbar__item weui-bar__item_on">全部</div>
				<div class="weui-navbar__item">未完成</div>
				<div class="weui-navbar__item">已完成</div>
				<div class="weui-navbar__item">退货</div>
			</div>
			<div class="weui-tab__panel">
				<div class="order_list all" style="display: block;">
					<?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="order ">
						<div class="weui_title">
							<div class="order_id">
								<span>订单号：</span> <span><?php echo ($v["oid"]); ?></span>
							</div>
							<div class="order_pre_status"><?php echo ($v["status"]); ?></div>
						</div>
						<div class="order_info">
							<a
								href="/Home/SaleManager/County/orderInfo/oid/<?php echo ($v["osid"]); ?>/status/<?php echo ($v["status"]); ?>.html"
								class="weui-media-box weui-media-box_appmsg">
								<div class="weui-media-box__hd">
									<img class="weui-media-box__thumb"
										src="/Public/Home/SaleManager/images/demo.png" alt="">
								</div>
								<div class="weui-media-box__bd">
									<h4 class="weui-media-box__title"><?php echo ($v["real_name"]); ?>
										<?php echo ($v["role_name"]); ?></h4>
									<p class="order_date_clinic"><?php echo ($v["add_date"]); ?></p>
									<p class="weui-media-box__desc">
										<span>总价：</span><span><?php echo ($v["total_price"]); ?></span>元
									</p>
								</div>
							</a>
						</div>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>

				</div>
				<div class="order_list wait">
					
				</div>
				<div class="order_list complete">
				</div>
				<div class="order_list exit">
				
				</div>
			</div>
		</div>
		<div class="weui-loadmore" style='display:none'>
  			<i class="weui-loading"></i>
  			<span class="weui-loadmore__tips">正在加载</span>
		</div>
	</div>
</div>

	<script src="/Public/Home/SaleManager/js/index.js"></script>
	<script src = "/Public/Home/SaleManager/js/Common.js"></script>
	<script src = "/Public/Home/SaleManager/js/dropload.min.js"></script>
	<script>
	var status;
	var allcounter=1;
	var waitcounter=1;
	var completecounter=1;
	var exitcounter=1;
	var allpagestart=1;
		$(".clinicOrder").on("click",".weui-navbar__item",function () {
	        $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
	       	status = ($(this).index()-1);
	       	switch(parseInt($(this).index())){	
	 			case 0:
	 				completecounter=1;
	 				break;
	 			case 1:
	 				waitcounter=1;
	 				break;
	 			case 2:
	 				exitcounter=1;
	 				break;
	 			default:
	 				allcounter=1;
	 				break;
 			}
	        $(".order_list").hide().eq($(this).index()).show();
	         if($(this).index()==0){
    	   			return false;
      		 } 
	        $('.weui-loadmore').css('display','block');
	        $.ajax({
                type: 'POST',
                url: '',
                data:{'pageStart':0,'status':status},
                dataType: 'json',
                success: function(data){
                	 	$('.weui-loadmore').css('display','none');
                		var vhtml='';
                    pageStart = 0;
                	 	if(data.data==null ||data.data=='undefined'){
                	 		 return false;
                	 	}
                	 	$(data.data).each(function(i,val) { 
                	 		vhtml+='<div class="order "><div class="weui_title"><div class="order_id"><span>订单号：</span> <span>'+val.oid+'</span>';
                	 		vhtml+='</div><div class="order_pre_status">'+val.status+'</div>';
                	 		vhtml+='</div><div class="order_info"><a href="/Home/SaleManager/County/orderInfo/oid/'+val.osid+'/status/'+val.status+'.html"';
						vhtml+='class="weui-media-box weui-media-box_appmsg"><div class="weui-media-box__hd">';
						if(val.head_img!=null &&val.head_img!=''){
            	 				vhtml+='<img class="weui-media-box__thumb" src="'+val.head_img+'" alt="">';
	            	 		}else{
	            	 			vhtml+='<img class="weui-media-box__thumb" src="/Public/Home/SaleManager/images/demo.png" alt="">';
	            	 		}		
						vhtml+='</div><div class="weui-media-box__bd"><h4 class="weui-media-box__title"><span>'+val.real_name+'</span><span>'+val.role_name+'</span></h4>';	  
	            	 		vhtml+='<p class="order_date_clinic">'+val.add_date+'</p>'; 	
	            	 		vhtml+='<p class="weui-media-box__desc"><span>总价：</span><span>'+val.total_price+'</span>元</p>';  		
	            	 		vhtml+='	</div> </div></a> </div> </div>';  	
    				}); 

                	 	if(vhtml!=''){
               
                	 		switch(parseInt(status)){	
                	 			case 0:
                	 				$('.wait').empty().append(vhtml);
                	 				break;
                	 			case 1:
                	 				$('.complete').empty().append(vhtml);
                	 				break;
                	 			case 2:
                	 				$('.exit').empty().append(vhtml);
                	 				break;
                	 			default:break;
                	 		}
                	 	}else{
                	 	//	locks=true;
                	 	}
                	
                },
                error: function(xhr, type){
                //	newlocks=true;
                	 $('.weui-loadmore').css('display','none');
                		
                		console.log('error');
                }
            });
		});
		//下拉刷新
        var LoadEnd = false;
        // 每页展示10个
        var counter = 0;
        var num = 10;
        	var pageStart = 0;
        var dropload =$('.dorp_down_fororder').dropload({
        	scrollArea : window,
        	loadDownFn : function(me){
    	       	switch(parseInt(status)){	
    	 			case 0:
    	 				 pageStart = waitcounter*num;	
    	 				counter=1;
    	 				break;
    	 			case 1:
    	 				pageStart = completecounter*num;		
    	 				counter=1;
    	 				break;
    	 			case 2:
    	 				pageStart = exitcounter*num;
    	 				counter=1;
    	 				break;
    	 			default:
    	 				pageStart = allcounter*num;
    	 				break;
     			}
        		if(counter==0){
        			counter++;
        			 pageStart = counter*num;
        			 me.unlock();
                 me.resetload();
        			//return false;
        		}
        		// 锁定
                me.lock();
                $.ajax({
                    type: 'GET',
                    url: '',
                    data:{'pageStart':pageStart,'status':status},
                    dataType: 'json',
                    success: function(data){
                    		var vhtml='';
                    	 	LoadEnd=true;
                    	 	if(data.data==null ||data.data=='undefined'){
                    	 		 me.unlock();
                    	 		me.noData();
        		                // 代码执行后必须重置
        		                 me.resetload();
                    	 		 return false;
                    	 	}else{
                    	 		switch(parseInt(status)){	
                	 			case 0:
                	 				waitcounter++;
                	 				break;
                	 			case 1:
                	 				completecounter++;
                	 				break;
                	 			case 2:
                	 				exitcounter++;
                	 				break;
                	 			default:
                	 				allcounter++;
                	 				break;
                 			}
                    	 	}
                    	 	$(data.data).each(function(i,val) { 
                    	 		vhtml+='<div class="order "><div class="weui_title"><div class="order_id"><span>订单号：</span> <span>'+val.oid+'</span>';
                    	 		vhtml+='</div><div class="order_pre_status">'+val.status+'</div>';
                    	 		vhtml+='</div><div class="order_info"><a href="/Home/SaleManager/County/orderInfo/oid/'+val.osid+'/status/'+val.status+'.html"';
    						vhtml+='class="weui-media-box weui-media-box_appmsg"><div class="weui-media-box__hd">';
    						if(val.head_img!=null &&val.head_img!=''){
                	 				vhtml+='<img class="weui-media-box__thumb" src="'+val.head_img+'" alt="">';
    	            	 		}else{
    	            	 			vhtml+='<img class="weui-media-box__thumb" src="/Public/Home/SaleManager/images/demo.png" alt="">';
    	            	 		}		
    						vhtml+='</div><div class="weui-media-box__bd"><h4 class="weui-media-box__title"><span>'+val.real_name+'</span><span>'+val.role_name+'</span></h4>';	  
    	            	 		vhtml+='<p class="order_date_clinic">'+val.add_date+'</p>'; 	
    	            	 		vhtml+='<p class="weui-media-box__desc"><span>总价：</span><span>'+val.total_price+'</span>元</p>';  		
    	            	 		vhtml+='	</div> </div></a> </div> </div>';  	
        				}); 
                    	 	if(vhtml!=''){
                    	 		switch(parseInt(status)){
                    	 			case 0:
                    	 				$('.wait').append(vhtml);
                    	 				break;
                    	 			case 1:
                    	 				$('.complete').append(vhtml);
                    	 				break;
                    	 			case 2:
                    	 				$('.exit').append(vhtml);
                    	 				break;
                    	 				default:
                    	 				$('.all').append(vhtml);
                    	 				break;
                    	 		}
                    	 	}else{
                    	 		// 无数据
                    	 		me.unlock();
                             me.noData();
                    	 	}
                    		me.unlock();
                        // 代码执行后必须重置
                        me.resetload();
                    },
                    error: function(xhr, type){
                    		me.unlock();
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