<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="black" name="apple-mobile-web-app-status-bar-style">
        <title>个人资料</title>
        <script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
        <link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/index.css">
    </head>
    <body>
        <div class="personData">
            <header class = "all_header">
                <a class = "h_back" href="/Home/Saleman/Saleman/index"><img src="/Public/Home/Saleman/images/back_icon.png" alt=""></a>
                <span class = "h_title">个人资料</span>
                <a class = "h_add" href="javascript:void(0)"></a>
            </header>
            <div class="weui-gallery" id="gallery" style="display: none;">
                <span class="weui-gallery__img" id="galleryImg" style="background-image:url(./images/pic_160.png)"></span>
                <div class="weui-gallery__opr">
                    <a href="javascript:" class="weui-gallery__del">
                        <i class="weui-icon-delete weui-icon_gallery-delete"></i>
                    </a>
                </div>
            </div>
            <form action="" method="post" name="persondata" id="persondata" enctype="multipart/form-data">
                <div class="weui-cells">
                    <a class="weui-cell weui-cell_access" href="javascript:;">
                        <div class="weui-cell__bd">
                            <?php if(!empty($salemanInfo['head_img'])): ?><img class = "person_img head_img" src="<?php echo ($salemanInfo['head_img']); ?>" alt="">
                                <?php else: ?>
                                <img class = "person_img head_img" src="/Public/Home/Saleman/images/people.png" alt=""><?php endif; ?>
                        </div>
                        <div class="weui-cell__ft"><input style = "position: absolute;left:0;z-index: 10;opacity: 0;" name='head_img' pos_show='head_img' class="uploaderInput" type="file" accept="image/*" multiple="">更换头像</div>
                    </a>
                    <div class="weui-cell">
                        <div class="weui-cell__bd">
                            <p>姓名</p>
                        </div>
                        <div class="weui-cell__ft">
                            <?php echo ($salemanInfo['real_name']); ?>
                            <input type="hidden" name="real_name" value="<?php echo ($salemanInfo['real_name']); ?>" />
                        </div>
                    </div>
                    <div class="weui-cell">
                        <div class="weui-cell__bd">
                            <p>手机号</p>
                        </div>
                        <div class="weui-cell__ft">
                            <?php echo ($salemanInfo['phone']); ?>
                            <input type="hidden" name="phone" value="<?php echo ($salemanInfo['phone']); ?>" />
                        </div>
                    </div>
                    <div class="weui-cell">
                        <div class="weui-cell__bd">
                            <p>身份证号</p>
                        </div>
                        <div class="weui-cell__ft">
                            <?php echo ($salemanInfo['card_number']); ?>
                            <input type="hidden" name="card_number" value="<?php echo ($salemanInfo['card_number']); ?>" />
                        </div>
                    </div>
                    <div class="weui-cell weui-cell_select" style = "padding: 0;">
                        <div class="weui-cell__bd">
                            <p style = "padding-left: 15px;">性别</p>
                        </div>
                        <div class="weui-cell__bd">
                            <select class="weui-select" name="sex">
                                <option value="1" <?php if(($salemanInfo['sex']) == "1"): ?>selected="selected"<?php endif; ?>>男</option>
                                <option value="2" <?php if(($salemanInfo['sex']) == "2"): ?>selected="selected"<?php endif; ?>>女</option>
                            </select>
                        </div> 
                    </div>
                    <div class="weui-cell">
                        <div class="weui-cell__bd">
                            <p>年龄</p>
                        </div>
                        <div class="weui-cell__ft">
                            <?php echo ($salemanInfo['age']); ?>
                            <input type="hidden" name="age" value="<?php echo ($salemanInfo['age']); ?>" />
                        </div>
                    </div>
                </div>
                <div class="identify_w">
                    <div class="ident_head">身份证</div>
                    <div class="ident_pic_w  weui-uploader__files" id="uploaderFiles">
                        <li class="ident_z weui-uploader__file">
                        <?php if(!empty($salemanInfo['card_front_img'])): ?><img class = "ident_z card_front_img" src="<?php echo ($salemanInfo['card_front_img']); ?>" style="width: 100%; height: 100%;" />
                            <?php else: ?>
                            <img class = "ident_z card_front_img" src="/Public/Home/Saleman/images/idetify.png" style="width: 100%; height: 100%;" /><?php endif; ?>
                        </li>
                        <li class="ident_z weui-uploader__file">
                        <?php if(!empty($salemanInfo['card_back_img'])): ?><img class = "ident_f card_back_img" src="<?php echo ($salemanInfo['card_back_img']); ?>" style="width: 100%; height: 100%;" />
                            <?php else: ?>
                            <img class = "ident_f card_back_img" src="/Public/Home/Saleman/images/idetify.png" style="width: 100%; height: 100%;" /><?php endif; ?>
                        </li>
                    </div>
                    <div class="ident_info">
                        <span style = "flex:1;padding: 10px;position: relative;">
                            <input style = "position: absolute; z-index: 10; opacity: 0; bottom: 1rem; width: 4rem; left: 3.5rem;" name="card_front_img" pos_show="card_front_img" class="uploaderInput" type="file" accept="image/*" multiple="">
                            <span style = "display: block;">身份证正面</span>
                            <a href="javascript:;" class="weui-btn weui-btn_mini weui-btn_primary" style = "background: #6db0e4;">修改</a>
                        </span>
                        <span style = "flex:1;padding: 10px;position: relative;">
                            <input style = "position: absolute; z-index: 10; opacity: 0; bottom: 1rem; width: 4rem; left: 3.5rem;" name="card_back_img" pos_show="card_back_img" class="uploaderInput" type="file" accept="image/*" multiple="">
                            <span style = "display: block;">身份证反面</span>
                            <a href="javascript:;" class="weui-btn weui-btn_mini weui-btn_primary" style = "background: #6db0e4;">修改</a>
                        </span>
                    </div>
                </div>
                <div class="graduate_w">
                    <div class="graduate_h" style = "padding: 10px;">
                        <span class = "gra_icon">|</span>
                        <span>毕业证书</span>
                    </div>
                    <div class="graduate_img weui-uploader__files" style = "height: 245px;" id = "identGFiles">
                        <li class="weui-uploader__file graduate_tips" style=" width: 100%; height:100%;">
                            <?php if(!empty($salemanInfo['graduate_img'])): ?><img class="graduate_img" src="<?php echo ($salemanInfo['graduate_img']); ?>" alt="" style="width: 100%; height: 100%;"> 
                            <?php else: ?>
                            <img class="graduate_img" src="/Public/Home/Saleman/images/idetify.png" alt="" style="width: 100%; height: 100%;"><?php endif; ?>
                        </li>
                    </div>
                    <div style = "text-align: center;"><a href="javascript:javascript:void(0);" class="weui-btn weui-btn_mini weui-btn_primary" style = "background: #6db0e4;">
                            <input name="graduate_img" pos_show="graduate_img" class="uploaderInput" style = "position: absolute;left:0;z-index: 10;opacity: 0;" id="uploaderInput" type="file" accept="image/*" multiple="">修改</a></div>  

                    <div class="certi_degree_h" style = "padding: 10px;">
                        <span class = "certi_icon">|</span>
                        <span>学位证书</span>
                    </div>
                    <div class="certi_img weui-uploader__files" style = "height: 245px;" id = "certiImg">
                        <li class="weui-uploader__file graduate_tips" style="width: 100%; height: 100%;">
                            <?php if(!empty($salemanInfo['diploma_img'])): ?><img class="diploma_img"  src="<?php echo ($salemanInfo['diploma_img']); ?>" alt="" style="width: 100%; height: 100%;"> 
                            <?php else: ?>
                            <img class="diploma_img"  src="/Public/Home/Saleman/images/idetify.png" alt="" style="width: 100%; height: 100%;"><?php endif; ?>
                        </li>
                    </div>
                    <div style = "text-align: center;"><a href="javascript:javascript:void(0);" class="weui-btn weui-btn_mini weui-btn_primary" style = "background: #6db0e4;">
                            <input name="diploma_img" pos_show="diploma_img" class="uploaderInput"  style = "position: absolute;left:0;z-index: 10;opacity: 0;" id="uploaderInput" type="file" accept="image/*" multiple="">修改</a></div> 
                </div>
                <div class="weui-btn-area">
                    <a class="weui-btn person_data_btn" href="javascript:">确定</a>
                </div>
            </form>
        </div>
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
    </body>
</html>