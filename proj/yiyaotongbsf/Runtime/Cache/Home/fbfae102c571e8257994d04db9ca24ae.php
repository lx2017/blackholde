<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>拜访详情</title>
	<script src="/Public/Home/SaleManager/js/zepto.min.js"></script>
	<link href="/Public/Home/SaleManager/css/stylesheets/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/css/stylesheets/index.css">
	<style>
		body{
			height: auto;
		}
		</style>
</head>
<body>
	<div class="clinicCount">
		<header class = "all_header">
			<a class = "h_back" href="javascript:history.back();"><img src="/Public/Home/SaleManager/images/back_icon.png" alt=""></a>
			<span class = "h_title">拜访详情</span>
			<a class = "h_add" href="javascript:void(0)"></a>
		</header>
		<div class="clinicTitle">
			<span><?php echo ($year); ?></span>年拜访总数(<span><?php echo ($salmanVisitNum); ?></span>次)
		</div>
		<div class="clinic_name">
			<span style = "flex: 49%;">月份</span><span style = "flex: 2%; color: #999;">｜</span>
			<span style = "flex: 49%;">拜访次数</span>
		</div>
		<?php if(is_array($salmanVisitMonth)): $k = 0; $__LIST__ = $salmanVisitMonth;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if(($mod) == "1"): ?><div class="clinic_odd">
			<?php else: ?>
				<div class="clinic_even"><?php endif; ?>
				<span><?php echo ($k); ?>月</span>
				<span><a href="<?php echo U('/Home/WorkSummary/SaleSummary/visitdetail',array('sale_id'=>$sale_id,'year'=>$year,'month'=>$k));?>"><?php echo ($vo); ?></a></span>
			</div><?php endforeach; endif; else: echo "" ;endif; ?>
	</div>
</body>
</html>