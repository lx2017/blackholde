/**
 * Created by mafengli on 16/6/20.
 */
$(function () {
    function bindShow(radio_bind, selectors) {
        $(radio_bind).click(function () {
            $(selectors).toggleClass('hidden');
        })
    }

    //配置的动态
    bindShow('#has_config', '.has_config');
    $('#preview').click(function () {
        var preview_url = '{:U("preview")}';
        console.log($('#form').serialize());
        $.post(preview_url, $('#form').serialize(), function (data) {
            $.thinkbox('<div id="preview_window" class="loading"><textarea></textarea></div>', {
                afterShow: function () {
                    var codemirror_option = {
                        lineNumbers: true,
                        matchBrackets: true,
                        mode: "application/x-httpd-php",
                        indentUnit: 4,
                        gutter: true,
                        fixedGutter: true,
                        indentWithTabs: true,
                        readOnly: true,
                        lineWrapping: true,
                        height: 500,
                        enterMode: "keep",
                        tabMode: "shift",
                        theme: "{:C('CODEMIRROR_THEME')}"
                    };
                    var preview_window = $("#preview_window").removeClass(".loading").find("textarea");
                    var editor = CodeMirror.fromTextArea(preview_window[0], codemirror_option);
                    editor.setValue(data);
                    $(window).resize();
                },

                title: '预览插件主文件',
                unload: true,
                actions: ['close'],
                drag: true
            });
        });
        return false;
    });

    $("#addAddonForm").validate({
        rules: {
            name: {
                remote: {
                    url: $("#name").attr('_target'),     //后台处理程序
                    type: "post",               //数据发送方式
                    dataType: "json",           //接受数据格式
                    data: {                     //要传递的数据
                        name: function () {
                            return $("#name").val();
                        }
                    }
                }
            }
        },
        messages: {
            name: {
                remote: "插件名称已经存在"
            }
        },
        submitHandler: function (form) {
            $.ajax({
                type: 'post',
                data: $("#addAddonForm").serialize(),
                url: $("#addAddonForm").attr('action'),
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        alert(data.info);
                        window.location.href = data.url;
                    } else {
                        alert(data.info);
                    }
                }
            })
        }
    });

//    $('.ajax-post_custom').click(function () {
//        var target, query, form;
//        var target_form = $(this).attr('target-form');
//        $.ajax({
//            type: "POST",
//            url: check_url,
//            dataType: 'json',
//            async: false,
//            data: $('#form').serialize(),
//            success: function (data) {
//                if (data.status) {
//                    if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('url'))) {
//                        form = $('.' + target_form);
//                        if (form.get(0).nodeName == 'FORM') {
//                            target = form.get(0).action;
//                            query = form.serialize();
//                        } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
//                            query = form.serialize();
//                        } else {
//                            query = form.find('input,select,textarea').serialize();
//                        }
//                        $.post(target, query).success(function (data) {
//                            if (data.status == 1) {
//                                if (data.url) {
////                                            updateAlert(data.info + ' 页面即将自动跳转~', 'alert-success');
//                                } else {
////                                            updateAlert(data.info + ' 页面即将自动刷新~');
//                                }
//                                setTimeout(function () {
//                                    if (data.url) {
//                                        location.href = data.url;
//                                    } else {
//                                        location.reload();
//                                    }
//                                }, 1500);
//                            } else {
////                                        updateAlert(data.info);
//                            }
//                        });
//                    }
//                } else {
////                            updateAlert(data.info);
//                }
//            }
//        });
//        return false;
//    })
})