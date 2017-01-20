<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="black" name="apple-mobile-web-app-status-bar-style">
        <title>申请支持</title>
        <script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
        <link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/index.css">
    </head>
    <body>
        <div class="appliceSupport">
            <header class = "all_header">
                <a class = "h_back" href="javascript:history.back(-1)"><img src="/Public/Home/Saleman/images/back_icon.png" alt=""></a>
                <span class = "h_title">申请支持</span>
                <a class = "h_add" href="javascript:void(0)"></a>
            </header>
            <div class="weui-cells__title">选择申请的类别</div>
            <div class="weui-cell weui-cell_select" style = "padding: 0 15px">
                <div class="weui-cell__bd">
                    <select class="weui-select" name="type">
                        <?php if(is_array($applyCateList)): $i = 0; $__LIST__ = $applyCateList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$applyCateVo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($applyCateVo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>
            <div class="weui-cells__title">申请内容</div>
            <div class="weui-cells weui-cells_form">
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <textarea class="weui-textarea appli_ipt" placeholder="请输入文本" rows="5" name="apply_content"></textarea>
                    </div>
                </div>
            </div>
            <div class="weui-btn-area">
                <a class="weui-btn weui-btn_plain-disabled appli_confirm" href="javascript:" pos_title="确定添加次申请？">确定</a>
            </div>
        </div>



    <div class="js_dialog" id="iosDialog1" style="display: none;">
    <div class="weui-mask"></div>
    <div class="weui-dialog">
        <div class="weui-dialog__hd"><strong class="weui-dialog__title">提示</strong></div>
        <div class="weui-dialog__bd confir_content"></div>
        <div class="weui-dialog__ft">
            <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default cancel-btn">取消</a>
            <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary ok-btn">确定</a>
        </div>
    </div>
</div>

<div class="js_dialog" id="iosDialog2" style="display: none;">
    <div class="weui-mask"></div>
    <div class="weui-dialog">
        <div class="weui-dialog__bd sure-info"></div>
        <div class="weui-dialog__ft">
            <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary sure-btn" pos_url="location.href">知道了</a>
        </div>
    </div>
</div>
<script>
    $('.cancel-btn').on('click', function () {
        $('#iosDialog1').hide(200);
    });
    $('.sure-btn').on('click', function () {
        $('#iosDialog2').hide(200);

        var pos_url = $(this).attr('pos_url');

        if (pos_url != "") {
            location.href = location.href;
        }


    });
</script>

    <script src = "/Public/Home/Saleman/js/index.js"></script>
    <script>
        $('.ok-btn').on('click', function () {
            var type = $('select[name=type]').val();
            var apply_content = $("textarea[name=apply_content]").val();
            $.ajax({
                url: "/Home/Saleman/Saleman/appliceSupport",
                type: 'post',
                data: {type: type, apply_content: apply_content},
                cache: false,
                dataType: "json",
                success: function (result) {
                    $("#iosDialog1").hide(200);
                    $('#iosDialog2').find('.sure-info').html(result['msg']);
                    $('#iosDialog2').show(200);
                }
            });
        });

    </script>
</body>
</html>