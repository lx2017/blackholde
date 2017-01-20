<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>全部医生</title>
	<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
	<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/css/stylesheets/index.css">
</head>
<body>
	<div class="allDoctor">
    <header class = "all_header">
      <a class = "h_back" href="javascript:history.back();"><img src="/Public/Home/SaleManager/images/back_icon.png" alt=""></a>
      <span class = "h_title">全部医生</span>
      <a class = "h_add" href="javascript:void(0)"></a>
    </header>
		<div class="weui-cells">
			<?php if(!$doctors): ?><div class="weui-panel__bd">
					<a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
						<div style="margin:auto">
							无
						</div>
					</a>
				</div>
			<?php else: ?>

				<?php if(is_array($doctors)): $i = 0; $__LIST__ = $doctors;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$doctor): $mod = ($i % 2 );++$i;?><div class="weui-panel__bd">
				<a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
				  <div class="weui-media-box__hd">
					<img class="weui-media-box__thumb" src="<?php echo ($doctor["image"]); ?>" alt="">
				  </div>
				  <div class="weui-media-box__bd">
					<div class="clinic_doc">
						<div class="doc_name">
							<h4 class="weui-media-box__title"><?php echo ($doctor["name"]); ?></h4>
							<span class="doc_adress"><?php echo ($doctor["clinic_name"]); ?></span>
						</div>
						<div class="clinic_score">
							<span class = "clinic_s"><?php echo ($doctor["score"]); ?></span><span>分</span>
						</div>
					</div>
					<div class="doc_cont">
						<p class="weui-media-box__desc">擅长：<?php if($doctor['good']): echo ($doctor["good"]); else: ?>暂无<?php endif; ?></p>
					</div>
				  </div>
				</a>
			  </div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
    </div>
	</div>
</body>
</html>