<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>县总主页</title>
	<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
	<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/css/stylesheets/index.css">
</head>
<body>
	<div class="newMedicine">
		<div class="info_nav nav_show">
			<div class="info_head">
				<div class="head_img">
				 <?php if(!empty($myself['head_img'])): ?><img src="<?php echo ($myself["head_img"]); ?>" alt="暂无" style="width:70px;height:70px;border-radius:50%">
					  <?php else: ?> <img src="/Public/Home/SaleManager/images/people.png" alt=""><?php endif; ?>
					
				</div>
				<div class = "info_name"><?php echo ($myself["real_name"]); ?></div>
			</div>
			<div class="infos">
				<div class="weui-grids">
	        		<a href="/Home/Saleman/Saleman/workArgument" class="weui-grid">
	            <div class="weui-grid__icon">
	                <img src="/Public/Home/SaleManager/images/work_icon.png" alt="">
	            </div>
	            <p class="weui-grid__label">工作安排</p>
	        </a>
	        <a href="./targetClinic.html" class="weui-grid">
	            <div class="weui-grid__icon">
	                <img src="/Public/Home/SaleManager/images/goad_icon.png" alt="">
	            </div>
	            <p class="weui-grid__label">目标诊所</p>
	        </a>
	        <a href="./clinic.html" class="weui-grid">
	            <div class="weui-grid__icon">
	                <img src="/Public/Home/SaleManager/images/my_clinic.png" alt="">
	            </div>
	            <p class="weui-grid__label">我的诊所</p>
	        </a>
	    </div>
			</div>
			<div class="info_list">
				<div class="weui-cells">
        <a class="weui-cell weui-cell_access" href="./mySaleMan.html">
            <div class="weui-cell__hd"><img src="/Public/Home/SaleManager/images/my_sale.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
            <div class="weui-cell__bd">
                <p>我的业务员</p>
            </div>
            <div class="weui-cell__ft"></div>
          </a>
          <a class="weui-cell weui-cell_access" href="/Home/Saleman/Saleman/sign.html">
            <div class="weui-cell__hd"><img src="/Public/Home/SaleManager/images/visit_icon.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
            <div class="weui-cell__bd">
                <p>签到拜访</p>
            </div>
            <div class="weui-cell__ft"></div>
          </a>
	        <a class="weui-cell weui-cell_access" href="./approvalRecord.html">
	          <div class="weui-cell__hd"><img src="/Public/Home/SaleManager/images/support_icon.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
	          <div class="weui-cell__bd">
	              <p>审批记录</p>
	          </div>
	          <div class="weui-cell__ft"></div>
	        </a>
	        <a class="weui-cell weui-cell_access" href="/Home/Saleman/Saleman/clinicOrder">
	          <div class="weui-cell__hd"><img src="/Public/Home/SaleManager/images/clinic_order.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
	          <div class="weui-cell__bd">
	              <p>诊所订单</p>
	          </div>
	          <div class="weui-cell__ft"></div>
	        </a>
	        <a class="weui-cell weui-cell_access" href="/Home/SaleManager/County/orderlist">
	          <div class="weui-cell__hd"><img src="/Public/Home/SaleManager/images/clinic_order.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
	          <div class="weui-cell__bd">
	              <p>业务员订单</p>
	          </div>
	          <div class="weui-cell__ft"></div>
	        </a>
	        <a class="weui-cell weui-cell_access" href="/Home/Saleman/Saleman/orderMedicine">
	          <div class="weui-cell__hd"><img src="/Public/Home/SaleManager/images/order_medicine.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
	          <div class="weui-cell__bd">
	              <p>订购药品</p>
	          </div>
	          <div class="weui-cell__ft"></div>
	        </a>
          <a class="weui-cell weui-cell_access" href="./pushHistory.html">
            <div class="weui-cell__hd"><img src="/Public/Home/SaleManager/images/order_medicine.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
            <div class="weui-cell__bd">
                <p>消息推送</p>
            </div>
            <div class="weui-cell__ft"></div>
          </a>
	        <a class="weui-cell weui-cell_access" href="/Home/WorkSummary/SaleSummary/index/saleId/<?php echo ($myself["id"]); ?>">
	          <div class="weui-cell__hd"><img src="/Public/Home/SaleManager/images/work_all.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
	          <div class="weui-cell__bd">
	              <p>工作汇总</p>
	          </div>
	          <div class="weui-cell__ft"></div>
	        </a>
	      </div>
			</div>
		</div>
		<!-- 消息 -->
		<div class="info_nav msgs">
			<header class = "all_header">
		        <a class = "h_back" href="javascript:void(0)"><img src="/Public/Home/SaleManager/images/back_icon.png" alt=""></a>
		        <span class = "h_title">消息</span>
		        <a class = "h_add" href="javascript:void(0)"></a>
	      </header>
			<div class="weui-panel__bd">
        <a href="./infoList/tag/upinfo.html" class="weui-media-box weui-media-box_appmsg">
          <div class="weui-media-box__hd">
            <img class="weui-media-box__thumb" src="/Public/Home/SaleManager/images/upInfo.png" alt="">
          </div>
          <div class="weui-media-box__bd">
          	<div class = "msg_tips">
            	<h4 class="weui-media-box__title">上级通知</h4>
            	<span><?php echo ($supertime); ?></span>
            </div>
            <div class="msg_cont">
            	<p class="weui-media-box__desc"><?php echo ($lastupinfo); ?>给您发了一条消息</p><!-- 发送消息者名称 -->
            	<span class = "info_num"><?php echo ($supercount); ?></span>
            </div>
          </div>
        </a>
      </div>
      <div class="weui-panel__bd">
        <a href="./infoList/tag/downinfo.html" class="weui-media-box weui-media-box_appmsg">
          <div class="weui-media-box__hd">
            <img class="weui-media-box__thumb" src="/Public/Home/SaleManager/images/downInfo.png" alt="">
          </div>
          <div class="weui-media-box__bd">
          	<div class = "msg_tips">
            	<h4 class="weui-media-box__title">下级通知</h4>
            	<span><?php echo ($lowertime); ?></span>
            </div>
            <div class="msg_cont">
            	<p class="weui-media-box__desc"><?php echo ($lastdowninfo); ?>给您发了一条消息</p>
            	<span class = "info_num"><?php echo ($lowercount); ?></span>
            </div>
          </div>
        </a>
      </div>
<!--       <div class="weui-panel__bd">
        <a href="./infoList.html" class="weui-media-box weui-media-box_appmsg">
          <div class="weui-media-box__hd">
            <img class="weui-media-box__thumb" src="/Public/Home/SaleManager/images/xitongInfo.png" alt="">
          </div>
          <div class="weui-media-box__bd">
          	<div class = "msg_tips">
            	<h4 class="weui-media-box__title">系统消息</h4>
            	<span>昨天</span>
            </div>
            <div class="msg_cont">
            	<p class="weui-media-box__desc">系统升级到了1.12</p>
            	<span class = "info_num">1</span>
            </div>
          </div>
        </a>
      </div> -->
		</div>
		<!-- 个人 -->
		<div class="info_nav set_cont">
			<header class = "all_header">
		        <a class = "h_back" href="javascript:void(0)"><img src="/Public/Home/SaleManager/images/back_icon.png" alt=""></a>
		        <span class = "h_title">个人</span>
		        <a class = "h_add" href="javascript:void(0)"></a>
     	 </header>
			<div class="weui-cells">
        <a class="weui-cell weui-cell_access" href="./personData/userId/<?php echo ($myself["id"]); ?>/x/nosecret/f/page.html">
          <div class="weui-cell__bd">
              <p>个人资料</p>
          </div>
          <div class="weui-cell__ft">
          </div>
        </a>
        <a class="weui-cell weui-cell_access" href="/Home/SaleManager/Login/changePassword">
          <div class="weui-cell__bd">
              <p>修改密码</p>
          </div>
          <div class="weui-cell__ft">
          </div>
        </a>
      <!--   <a class="weui-cell weui-cell_access" href="javascript:void(0);">
          <div class="weui-cell__bd">
              <p>系统消息</p>
          </div>
          <div class="weui-cell__ft">
          </div>
        </a> -->
        <a class="weui-cell weui-cell_access" href="javascript:void(0);">
          <div class="weui-cell__bd">
              <p>新消息通知</p>
          </div>
          <div class="weui-cell__ft">
          </div>
        </a>
      </div>

      <div class="weui-cells">
       <!--  <a class="weui-cell weui-cell_access" href="javascript:void(0);">
          <div class="weui-cell__bd">
              <p>功能介绍</p>
          </div>
          <div class="weui-cell__ft">
          </div>
        </a>
        <a class="weui-cell weui-cell_access" href="javascript:void(0);">
          <div class="weui-cell__bd">
              <p>检查更新</p>
          </div>
          <div class="weui-cell__ft" style = "padding: 5px 20px 0 0;">v1.2.3</div>
        </a> -->
        <a class="weui-cell weui-cell_access" href="javascript:void(0);">
          <div class="weui-cell__bd">
              <p>意见反馈</p>
          </div>
          <div class="weui-cell__ft">
          </div>
        </a>
       <!--  <a class="weui-cell weui-cell_access" href="javascript:void(0);">
          <div class="weui-cell__bd">
              <p>去评分</p>
          </div>
          <div class="weui-cell__ft">
          </div>
        </a> -->
      </div>
		</div>
		  <div class="weui-btn-area loginout" style='display:none'>
                    <a class="weui-btn quit_btn" href="javascript:">退出登录</a>
                </div>
		<div class="weui-tabbar">
	    <a href="javascript:;" class="weui-tabbar__item weui-bar__item_on">
        <span class = "work_active_icon common_icon"></span>
	      <!-- <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon"> -->
	      <p class="weui-tabbar__label">工作</p>
	    </a>
	    <a href="javascript:;" class="weui-tabbar__item">
        <span class = "info_icon common_icon"></span>
	      <!-- <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon"> -->
	      <p class="weui-tabbar__label" style = "position: relative;">消息<span class = "new_info"><?php echo ($total); ?></span></p>
	    </a>
	    <a href="javascript:;" class="weui-tabbar__item">
        <span class = "person_icon common_icon"></span>
	      <!-- <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon"> -->
	      <p class="weui-tabbar__label">个人</p>
	    </a>
	  </div>
	</div>
	<script src = "/Public/Home/SaleManager/js/index.js"></script>
</body>
</html>