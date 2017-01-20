<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="black" name="apple-mobile-web-app-status-bar-style">
        <title>消息推送</title>
        <script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
        <link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/index.css">
    </head>
    <body>
        <div class="pushHistory">
            <header class = "all_header">
                <a class = "h_back" href="javascript:history.back(-1);"><img src="/Public/Home/Saleman/images/back_icon.png" alt=""></a>
                <span class = "h_title">消息推送</span>
                <a class = "h_add" href="javascript:void(0)"></a>
            </header>
            <div class="page">
                <div class="page__bd" style="height: 100%;">
                    <div class="weui-tab">
                        <div class="weui-navbar">
                            <div class="weui-navbar__item weui-bar__item_on">
                                推送消息
                            </div>
                            <div class="weui-navbar__item">
                                推送历史
                            </div>
                        </div>
                        <div class="weui-tab__panel">
                            <div class="push_list" style = "display: block;">
                                <div class="weui-cells__title">请选择推送对象</div>
                                <div class="weui-cells">
                                    <div class="weui-cell weui-cell_select">
                                        <div class="weui-cell__bd">
                                            <select class="weui-select" name="select1">
                                                <option selected="" value="1">全部县总</option>
                                                <option value="2">QQ号</option>
                                                <option value="3">Email</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="weui-cells__title">请选择推送分类</div>
                                <div class="weui-cells">
                                    <div class="weui-cell weui-cell_select">
                                        <div class="weui-cell__bd">
                                            <select class="weui-select" name="select2">
                                                <option selected="" value="1">普通消息</option>
                                                <option value="2">QQ号</option>
                                                <option value="3">Email</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="weui-cells__title">推送标题</div>
                                <div class="weui-cells" style = "padding: 10px 15px;">
                                    <div class="weui-cell">
                                        <div class="weui-cell__bd">
                                            <input class="weui-input" maxlength="20" type="text" placeholder="请输入标题(20字以内)">
                                        </div>
                                    </div>
                                </div>
                                <div class="weui-cells__title">编辑推送内容</div>
                                <div class="weui-cells weui-cells_form">
                                    <div class="weui-cell" style = "padding: 10px 15px;">
                                        <div class="weui-cell__bd">
                                            <textarea class="weui-textarea" placeholder="请输入文本" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                 <div class="certi_img weui-uploader__files" style = "height: 200px;" id = "certiImg">
                        <li class="weui-uploader__file graduate_tips" style="width: 100%; height: 100%;">
                            <?php if(!empty($salemanInfo['diploma_img'])): ?><img class="diploma_img"  src="<?php echo ($salemanInfo['diploma_img']); ?>" alt="" style="width: 100%; height: 100%;"> 
                            <?php else: ?>
                            <img class="diploma_img"  src="/Public/Home/Saleman/images/idetify.png" alt="" style="width: 100%; height: 100%;"><?php endif; ?>
                        </li>
                    </div>
                   <a href="javascript:javascript:void(0);" class="weui-btn weui-btn_mini weui-btn_primary" style = "background: #6db0e4;">
                            <input name="diploma_img" pos_show="diploma_img" class="uploaderInput"  style = "position: absolute;left:0;z-index: 10;opacity: 0;" id="uploaderInput" type="file" accept="image/*" multiple="">选择图片</a>
                                <div class="weui-btn-area">
                                    <a class="weui-btn push_btn" href="javascript:">发 送</a>
                                </div>
                            </div>
                            <div class="push_list">
                                <div class="weui-panel weui-panel_access">
                                    <div class="weui-panel__bd">
                                        <div class="weui-media-box weui-media-box_text">
                                            <div class="weui-media-box__title">
                                                <div>
                                                    <span class = "all_user">所有用户</span>
                                                    <span class = "infos">普通消息</span>
                                                </div>
                                                <div class = "nav_btn">删除</div>
                                            </div>
                                            <p class="weui-media-box__desc">由各种物质组成的巨型球状天体，叫做星球。星球有一定的形状，有自己的运行轨道。</p>
                                            <div class = "push_date_w">
                                                <span>2016-10-31</span>
                                                <span class = "push_state">已完成</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="weui-panel weui-panel_access">
                                    <div class="weui-panel__bd">
                                        <div class="weui-media-box weui-media-box_text">
                                            <div class="weui-media-box__title">
                                                <div>
                                                    <span class = "all_user">所有用户</span>
                                                    <span class = "infos">普通消息</span>
                                                </div>
                                                <div class = "nav_btn">删除</div>
                                            </div>
                                            <p class="weui-media-box__desc">由各种物质组成的巨型球状天体，叫做星球。星球有一定的形状，有自己的运行轨道。</p>
                                            <div class = "push_date_w">
                                                <span>2016-10-31</span>
                                                <span class = "push_state">已完成</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="weui-panel weui-panel_access">
                                    <div class="weui-panel__bd">
                                        <div class="weui-media-box weui-media-box_text">
                                            <div class="weui-media-box__title">
                                                <div>
                                                    <span class = "all_user">所有用户</span>
                                                    <span class = "infos">普通消息</span>
                                                </div>
                                                <div class = "nav_btn">删除</div>
                                            </div>
                                            <p class="weui-media-box__desc">由各种物质组成的巨型球状天体，叫做星球。星球有一定的形状，有自己的运行轨道。</p>
                                            <div class = "push_date_w">
                                                <span>2016-10-31</span>
                                                <span class = "push_state">已完成</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src = "/Public/Home/Saleman/js/index.js"></script>
    </body>
</html>
<script>
            $(function () {
                var tmpl = '<li class="weui-uploader__file" style="background-image:url(#url#)"></li>',
                        $gallery = $("#gallery"), $galleryImg = $("#galleryImg"),
                        $uploaderInput = $(".uploaderInput"),
                        $uploaderFiles = $("#uploaderFiles");
                $uploaderInput.on("change", function (e) {
                    var src, url = window.URL || window.webkitURL || window.mozURL, files = e.target.files;
                    for (var i = 0, len = files.length; i < len; ++i) {
                        var file = files[i];
                        if (url) {
                            src = url.createObjectURL(file);
                        } else {
                            src = e.target.result;
                        }
                        var pos_show = $(this).attr('pos_show');

                        $("." + pos_show).attr('src', src);
                    }
                });
                $(".weui-uploader__file").on("click", function () {
                    
                    var src = $(this).find('img').attr('src');
                    var style = 'background-image:url(' + src + '); background - size: 100 % ; width: 100 % ; height: 100 % ; ';
                    $galleryImg.attr("style", style);
                    $gallery.show(100);
                });
                $gallery.on("click", function () {
                    $gallery.hide(100);
                });

                $('.person_data_btn').on('click', function () {
                    $('#persondata').submit();
                });
            });
        </script>