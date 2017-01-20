/**
 * Created by mafengli on 16/6/17.
 */
$(function () {
    $("#addHookFrom").validate({
        rules: {
            username: {
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
                remote: "钩子名称已经存在"
            }
        },
        submitHandler: function (form) {
            $.ajax({
                type: 'post',
                data: $("#addHookFrom").serialize(),
                url: $("#addHookFrom").attr('action'),
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
    $("#editHookFrom").validate({
        submitHandler: function (form) {
            $.ajax({
                type: 'post',
                data: $("#editHookFrom").serialize(),
                url: $("#editHookFrom").attr('action'),
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
})
